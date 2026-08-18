<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use Core\Database;
use App\Models\BankAccountModel;
use App\Models\CashAccountModel;
use App\Services\AccountingEngine;

class BankController extends Controller {
    private BankAccountModel $bankModel;
    private CashAccountModel $cashModel;

    public function __construct() {
        $this->bankModel = new BankAccountModel();
        $this->cashModel = new CashAccountModel();
    }

    public function index(): void {
        Auth::requirePermission('dashboard.view');

        $bankAccounts = $this->bankModel->getAll();
        $cashAccounts = $this->cashModel->getActiveAccounts();
        $db = Database::getInstance();

        // Chart of Accounts Bank category Accounts
        $bankAccountsGL = $db->query("SELECT id, account_code, account_name FROM accounts WHERE category = 'Asset' AND account_code LIKE '1120%' AND allow_manual_posting = 1")->fetchAll();

        // Calculate Cash In Hand Balance
        $cashInHandAccount = $db->query("SELECT id FROM accounts WHERE account_code = '1110'")->fetchColumn();
        
        $cashReceived = (float)$db->query("
            SELECT SUM(debit) 
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            WHERE jl.account_id = " . (int)$cashInHandAccount . " AND je.status = 'posted'
        ")->fetchColumn();

        $cashPayments = (float)$db->query("
            SELECT SUM(credit) 
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            WHERE jl.account_id = " . (int)$cashInHandAccount . " AND je.status = 'posted'
        ")->fetchColumn();

        $cashBalance = $cashReceived - $cashPayments;

        // Fetch recent bank transactions from General Ledger
        $recentTransactions = $db->query("
            SELECT jl.*, je.journal_number, je.transaction_date, je.description AS entry_desc, ba.bank_name, ba.account_number
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            JOIN bank_accounts ba ON jl.account_id = ba.account_id
            WHERE je.status = 'posted'
            ORDER BY je.transaction_date DESC, je.id DESC LIMIT 10
        ")->fetchAll();

        $this->render('bank/index', [
            'pageTitle' => 'Cash & Bank Accounts',
            'activeNav' => 'bank_accounts',
            'bankAccounts' => $bankAccounts,
            'cashAccounts' => $cashAccounts,
            'bankAccountsGL' => $bankAccountsGL,
            'recentTransactions' => $recentTransactions,
            'cashBalance' => $cashBalance
        ]);
    }

    public function store(): void {
        $this->validateCsrf();
        
        $data = [
            'id' => !empty($_POST['id']) ? (int)$_POST['id'] : null,
            'bank_name' => trim($_POST['bank_name'] ?? ''),
            'branch' => trim($_POST['branch'] ?? ''),
            'account_number' => trim($_POST['account_number'] ?? ''),
            'account_name' => trim($_POST['account_name'] ?? ''),
            'swift_code' => trim($_POST['swift_code'] ?? ''),
            'account_id' => !empty($_POST['account_id']) ? (int)$_POST['account_id'] : 0,
            'opening_balance' => (float)($_POST['opening_balance'] ?? 0),
            'status' => $_POST['status'] ?? 'active'
        ];

        try {
            $this->bankModel->save($data);
            Session::setFlash('success', 'Bank Account settings saved successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to save Bank Account: ' . $e->getMessage());
        }

        Helper::redirect('modules/bank-accounts');
    }

    public function transaction(): void {
        $this->validateCsrf();
        $type = $_POST['type'] ?? '';
        $amount = (float)($_POST['amount'] ?? 0);
        $date = $_POST['date'] ?? date('Y-m-d');
        $desc = trim($_POST['description'] ?? 'Bank transaction');
        $bankAccountId = !empty($_POST['bank_account_id']) ? (int)$_POST['bank_account_id'] : 0;

        if ($amount <= 0 || !$bankAccountId) {
            Session::setFlash('error', 'Invalid amount or Bank account.');
            Helper::redirect('modules/bank-accounts');
        }

        $db = Database::getInstance();
        $bankAccount = $this->bankModel->getById($bankAccountId);
        if (!$bankAccount) {
            Session::setFlash('error', 'Bank account not found.');
            Helper::redirect('modules/bank-accounts');
        }

        $bankGL = (int)$bankAccount['account_id'];
        $costCenterId = (int)$db->query("SELECT id FROM cost_centers LIMIT 1")->fetchColumn();

        try {
            $db->beginTransaction();
            $journalLines = [];

            if ($type === 'deposit') {
                // Dr Bank, Cr Cash
                $cashAccountId = !empty($_POST['cash_account_id']) ? (int)$_POST['cash_account_id'] : 1;
                $cashDrawer = $db->query("SELECT * FROM cash_accounts WHERE id = $cashAccountId")->fetch();
                $cashGL = (int)$cashDrawer['account_id'];

                $journalLines[] = [
                    'account_id' => $bankGL,
                    'debit' => $amount,
                    'credit' => 0.00,
                    'description' => $desc
                ];
                $journalLines[] = [
                    'account_id' => $cashGL,
                    'debit' => 0.00,
                    'credit' => $amount,
                    'description' => $desc
                ];

                $db->prepare("UPDATE bank_accounts SET current_balance = current_balance + :amount WHERE id = :id")
                   ->execute(['amount' => $amount, 'id' => $bankAccountId]);
                $db->prepare("UPDATE cash_accounts SET current_balance = current_balance - :amount WHERE id = :id")
                   ->execute(['amount' => $amount, 'id' => $cashAccountId]);

            } elseif ($type === 'withdrawal') {
                // Dr Cash, Cr Bank
                $cashAccountId = !empty($_POST['cash_account_id']) ? (int)$_POST['cash_account_id'] : 1;
                $cashDrawer = $db->query("SELECT * FROM cash_accounts WHERE id = $cashAccountId")->fetch();
                $cashGL = (int)$cashDrawer['account_id'];

                $journalLines[] = [
                    'account_id' => $cashGL,
                    'debit' => $amount,
                    'credit' => 0.00,
                    'description' => $desc
                ];
                $journalLines[] = [
                    'account_id' => $bankGL,
                    'debit' => 0.00,
                    'credit' => $amount,
                    'description' => $desc
                ];

                $db->prepare("UPDATE cash_accounts SET current_balance = current_balance + :amount WHERE id = :id")
                   ->execute(['amount' => $amount, 'id' => $cashAccountId]);
                $db->prepare("UPDATE bank_accounts SET current_balance = current_balance - :amount WHERE id = :id")
                   ->execute(['amount' => $amount, 'id' => $bankAccountId]);

            } elseif ($type === 'transfer') {
                // Dr Target Bank, Cr Source Bank
                $targetBankId = !empty($_POST['target_bank_account_id']) ? (int)$_POST['target_bank_account_id'] : 0;
                $targetBank = $this->bankModel->getById($targetBankId);
                $targetGL = (int)$targetBank['account_id'];

                $journalLines[] = [
                    'account_id' => $targetGL,
                    'debit' => $amount,
                    'credit' => 0.00,
                    'description' => $desc
                ];
                $journalLines[] = [
                    'account_id' => $bankGL,
                    'debit' => 0.00,
                    'credit' => $amount,
                    'description' => $desc
                ];

                $db->prepare("UPDATE bank_accounts SET current_balance = current_balance - :amount WHERE id = :id")
                   ->execute(['amount' => $amount, 'id' => $bankAccountId]);
                $db->prepare("UPDATE bank_accounts SET current_balance = current_balance + :amount WHERE id = :id")
                   ->execute(['amount' => $amount, 'id' => $targetBankId]);
            }

            $journalData = [
                'transaction_date' => $date,
                'description' => $desc,
                'reference' => 'BANK-TX',
                'source_module' => 'finance',
                'cost_center_id' => $costCenterId,
                'status' => 'approved',
                'lines' => $journalLines
            ];

            AccountingEngine::postJournalEntry($journalData);
            $db->commit();
            Session::setFlash('success', 'Bank transaction posted successfully.');
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            Session::setFlash('error', 'Transaction failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/bank-accounts');
    }

    public function reconcile(): void {
        Auth::requirePermission('dashboard.view');

        $bankAccounts = $this->bankModel->getActiveAccounts();
        $db = Database::getInstance();

        $selectedBankId = !empty($_GET['bank_account_id']) ? (int)$_GET['bank_account_id'] : null;
        $statementDate = $_GET['statement_date'] ?? date('Y-m-d');
        $endingBalance = (float)($_GET['ending_balance'] ?? 0);

        $transactions = [];
        $bookBalance = 0.00;
        $reconciledAmount = 0.00;

        if ($selectedBankId) {
            $bankAccount = $this->bankModel->getById($selectedBankId);
            $bankGL = (int)$bankAccount['account_id'];

            // Fetch book balance
            $bookBalance = (float)$bankAccount['current_balance'];

            // Fetch unreconciled transactions
            $transactions = $db->query("
                SELECT jl.id AS line_id, jl.debit, jl.credit, jl.reconciled, je.journal_number, je.transaction_date, je.description
                FROM journal_lines jl
                JOIN journal_entries je ON jl.journal_entry_id = je.id
                WHERE jl.account_id = $bankGL AND je.status = 'posted'
                ORDER BY je.transaction_date ASC
            ")->fetchAll();

            foreach ($transactions as $tx) {
                if ($tx['reconciled']) {
                    $reconciledAmount += (float)$tx['debit'] - (float)$tx['credit'];
                }
            }
        }

        $this->render('bank/reconcile', [
            'pageTitle' => 'Bank Reconciliation Portal',
            'activeNav' => 'bank_accounts',
            'bankAccounts' => $bankAccounts,
            'selectedBankId' => $selectedBankId,
            'statementDate' => $statementDate,
            'endingBalance' => $endingBalance,
            'bookBalance' => $bookBalance,
            'reconciledAmount' => $reconciledAmount,
            'transactions' => $transactions
        ]);
    }

    public function postReconcile(): void {
        $this->validateCsrf();
        $bankAccountId = (int)$_POST['bank_account_id'];
        $reconciledLineIds = $_POST['reconcile_lines'] ?? [];

        $db = Database::getInstance();
        $bankAccount = $this->bankModel->getById($bankAccountId);
        if (!$bankAccount) {
            Session::setFlash('error', 'Bank account not found.');
            Helper::redirect('modules/bank-accounts');
        }

        try {
            $db->beginTransaction();
            
            // Mark selected lines as reconciled, others as unreconciled
            $bankGL = (int)$bankAccount['account_id'];
            $db->prepare("UPDATE journal_lines SET reconciled = 0 WHERE account_id = :bank_gl")
               ->execute(['bank_gl' => $bankGL]);

            if (!empty($reconciledLineIds)) {
                $stmt = $db->prepare("UPDATE journal_lines SET reconciled = 1 WHERE id = :id");
                foreach ($reconciledLineIds as $lineId) {
                    $stmt->execute(['id' => (int)$lineId]);
                }
            }

            // Save reconciliation header
            $stmt = $db->prepare("
                INSERT INTO bank_reconciliations (bank_account_id, statement_date, ending_balance, book_balance, difference, created_by)
                VALUES (:bank_account_id, :statement_date, :ending_balance, :book_balance, :difference, :created_by)
            ");
            $stmt->execute([
                'bank_account_id' => $bankAccountId,
                'statement_date' => $_POST['statement_date'],
                'ending_balance' => (float)$_POST['ending_balance'],
                'book_balance' => (float)$_POST['book_balance'],
                'difference' => (float)$_POST['difference'],
                'created_by' => Auth::id() ?? 1
            ]);

            $db->commit();
            Session::setFlash('success', 'Bank Reconciliation saved and cleared successfully.');
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            Session::setFlash('error', 'Reconciliation failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/bank-accounts');
    }
}
