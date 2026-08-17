<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use Core\Database;
use App\Models\CashAccountModel;
use App\Models\BankAccountModel;
use App\Services\AccountingEngine;
use App\Services\AuditService;

class CashController extends Controller {
    private CashAccountModel $cashModel;

    public function __construct() {
        $this->cashModel = new CashAccountModel();
    }

    public function index(): void {
        Auth::requirePermission('dashboard.view');

        $db = Database::getInstance();
        $cashAccounts = $this->cashModel->getAll();

        // Cash received total (debits to cash accounts)
        $cashInHandAccount = $db->query("SELECT id FROM accounts WHERE account_code = '1110'")->fetchColumn();
        
        $cashReceived = (float)$db->query("
            SELECT SUM(debit) 
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            WHERE jl.account_id = " . (int)$cashInHandAccount . " AND je.status = 'posted'
        ")->fetchColumn();

        // Cash payments total (credits to cash accounts)
        $cashPayments = (float)$db->query("
            SELECT SUM(credit) 
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            WHERE jl.account_id = " . (int)$cashInHandAccount . " AND je.status = 'posted'
        ")->fetchColumn();

        // Cash In Hand Balance (Formula: Cash In Hand Debits - Cash In Hand Credits)
        $cashBalance = $cashReceived - $cashPayments;

        // Recent cash transactions (lines involving Cash account)
        $recentTransactions = $db->query("
            SELECT jl.*, je.journal_number, je.transaction_date, je.description AS entry_desc
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            WHERE jl.account_id = " . (int)$cashInHandAccount . " AND je.status = 'posted'
            ORDER BY je.transaction_date DESC, je.id DESC LIMIT 10
        ")->fetchAll();

        $this->render('cash/index', [
            'pageTitle' => 'Cash Book & Management',
            'activeNav' => 'cash_accounts',
            'cashAccounts' => $cashAccounts,
            'cashBalance' => $cashBalance,
            'cashReceived' => $cashReceived,
            'cashPayments' => $cashPayments,
            'recentTransactions' => $recentTransactions
        ]);
    }

    public function transaction(): void {
        $this->validateCsrf();
        $type = $_POST['type'] ?? '';
        $amount = (float)($_POST['amount'] ?? 0);
        $date = $_POST['date'] ?? date('Y-m-d');
        $desc = trim($_POST['description'] ?? 'Cash transaction');
        $cashDrawerId = !empty($_POST['cash_account_id']) ? (int)$_POST['cash_account_id'] : 1;

        if ($amount <= 0) {
            Session::setFlash('error', 'Amount must be greater than zero.');
            Helper::redirect('modules/cash-accounts');
        }

        $db = Database::getInstance();
        $cashDrawer = $db->query("SELECT * FROM cash_accounts WHERE id = $cashDrawerId")->fetch();
        if (!$cashDrawer) {
            Session::setFlash('error', 'Invalid Cash Drawer.');
            Helper::redirect('modules/cash-accounts');
        }

        $cashAccountGL = (int)$cashDrawer['account_id'];
        $costCenterId = (int)$db->query("SELECT id FROM cost_centers LIMIT 1")->fetchColumn();

        try {
            $db->beginTransaction();
            $journalLines = [];

            if ($type === 'receipt') {
                // Receipt: Dr Cash (Asset +), Cr Revenue (Other Income 4990)
                $otherIncomeGL = (int)$db->query("SELECT id FROM accounts WHERE account_code = '4990'")->fetchColumn();
                $journalLines[] = [
                    'account_id' => $cashAccountGL,
                    'debit' => $amount,
                    'credit' => 0.00,
                    'description' => $desc
                ];
                $journalLines[] = [
                    'account_id' => $otherIncomeGL,
                    'debit' => 0.00,
                    'credit' => $amount,
                    'description' => $desc
                ];
                
                // Update cash account running balance
                $db->prepare("UPDATE cash_accounts SET current_balance = current_balance + :amount WHERE id = :id")
                   ->execute(['amount' => $amount, 'id' => $cashDrawerId]);

            } elseif ($type === 'payment') {
                // Payment: Dr Expense (Other Operating Expenses 6100), Cr Cash (Asset -)
                $otherExpenseGL = (int)$db->query("SELECT id FROM accounts WHERE account_code = '6100'")->fetchColumn();
                $journalLines[] = [
                    'account_id' => $otherExpenseGL,
                    'debit' => $amount,
                    'credit' => 0.00,
                    'description' => $desc
                ];
                $journalLines[] = [
                    'account_id' => $cashAccountGL,
                    'debit' => 0.00,
                    'credit' => $amount,
                    'description' => $desc
                ];

                // Update cash account running balance
                $db->prepare("UPDATE cash_accounts SET current_balance = current_balance - :amount WHERE id = :id")
                   ->execute(['amount' => $amount, 'id' => $cashDrawerId]);
            }

            $journalData = [
                'transaction_date' => $date,
                'description' => $desc,
                'reference' => 'CASH-TX',
                'source_module' => 'finance',
                'cost_center_id' => $costCenterId,
                'status' => 'approved',
                'lines' => $journalLines
            ];

            AccountingEngine::postJournalEntry($journalData);
            $db->commit();
            Session::setFlash('success', 'Cash transaction posted successfully.');
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            Session::setFlash('error', 'Transaction failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/cash-accounts');
    }
}
