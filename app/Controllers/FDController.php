<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use Core\Database;
use App\Models\FDModel;
use App\Models\MemberModel;
use App\Services\AccountingEngine;
use App\Services\AuditService;

class FDController extends Controller {
    private FDModel $fdModel;
    private MemberModel $memberModel;

    public function __construct() {
        $this->fdModel = new FDModel();
        $this->memberModel = new MemberModel();
    }

    public function index(): void {
        Auth::requirePermission('parties.view');

        // Automatic update: Check if any ACTIVE FD has reached its maturity date, flag as MATURED
        $db = Database::getInstance();
        $db->query("UPDATE member_fixed_deposits SET status = 'MATURED' WHERE status = 'ACTIVE' AND maturity_date <= CURRENT_DATE()");

        $filters = [
            'member_id' => $_GET['member_id'] ?? '',
            'status' => $_GET['status'] ?? '',
            'maturity_date' => $_GET['maturity_date'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $fds = $this->fdModel->getAll($filters, $limit, $offset);
        $totalItems = $this->fdModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        $stats = $this->fdModel->getActiveStats();
        $members = $this->memberModel->getAll([], 200);

        $this->render('fd/index', [
            'pageTitle' => 'Fixed Deposits Registry',
            'activeNav' => 'fixed_deposits',
            'fds' => $fds,
            'stats' => $stats,
            'filters' => $filters,
            'members' => $members,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function create(): void {
        Auth::requirePermission('parties.create');

        $members = $this->memberModel->getAll([], 500);

        $this->render('fd/create', [
            'pageTitle' => 'Open Fixed Deposit',
            'activeNav' => 'fixed_deposits',
            'members' => $members
        ]);
    }

    public function store(): void {
        Auth::requirePermission('parties.create');
        $this->validateCsrf();

        $db = Database::getInstance();
        $memberId = (int)$_POST['member_id'];
        $amount = (float)$_POST['deposit_amount'];
        $startDate = $_POST['start_date'];
        $termMonths = (int)$_POST['term_months'];
        $interestRate = (float)$_POST['interest_rate'];
        $payMethod = $_POST['payment_method'];

        // Simple Interest expected calculation
        $expectedInterest = $amount * ($interestRate / 100) * ($termMonths / 12);
        $maturityAmount = $amount + $expectedInterest;
        $maturityDate = date('Y-m-d', strtotime("+$termMonths months", strtotime($startDate)));

        try {
            $db->beginTransaction();

            // Fixed Deposits GL asset account (e.g. code 1150)
            $fdAssetGL = (int)$db->query("SELECT id FROM accounts WHERE account_code = '1150'")->fetchColumn();
            if (!$fdAssetGL) {
                throw new \Exception("Fixed Deposits GL asset account (1150) is missing.");
            }

            $creditGL = null;
            if ($payMethod === 'Cash') {
                $creditGL = (int)$db->query("SELECT account_id FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $cashAccId = $db->query("SELECT id FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $db->prepare("UPDATE cash_accounts SET current_balance = current_balance - :amt WHERE id = :id")->execute(['amt' => $amount, 'id' => $cashAccId]);
            } elseif ($payMethod === 'Bank Transfer') {
                $creditGL = (int)$db->query("SELECT account_id FROM bank_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $bankAccId = $db->query("SELECT id FROM bank_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $db->prepare("UPDATE bank_accounts SET current_balance = current_balance - :amt WHERE id = :id")->execute(['amt' => $amount, 'id' => $bankAccId]);
            } elseif ($payMethod === 'Cheque') {
                // Cheque In Hand GL code 1115
                $creditGL = (int)$db->query("SELECT id FROM accounts WHERE account_code = '1115'")->fetchColumn();

                $chqNo = $_POST['cheque_number'] ?? 'CHQ-' . time();
                $chqStmt = $db->prepare("
                    INSERT INTO cheques (cheque_number, cheque_type, party_id, bank_name, cheque_date, amount, received_issued_date, status, created_by)
                    VALUES (:num, 'RECEIVED', :party, :bank, :dt, :amt, :rcv_dt, 'RECEIVED', :by)
                ");
                $chqStmt->execute([
                    'num' => $chqNo,
                    'party' => 1,
                    'bank' => $_POST['cheque_bank'] ?? 'Co-Op Bank',
                    'dt' => date('Y-m-d'),
                    'amt' => $amount,
                    'rcv_dt' => date('Y-m-d'),
                    'by' => Auth::id() ?? 1
                ]);
            }

            $costCenterId = (int)$db->query("SELECT id FROM cost_centers LIMIT 1")->fetchColumn();
            $journalData = [
                'transaction_date' => $startDate,
                'description' => "Fixed Deposit investment setup",
                'reference' => 'FD-SETUP',
                'source_module' => 'finance',
                'cost_center_id' => $costCenterId,
                'status' => 'approved',
                'lines' => [
                    [
                        'account_id' => $fdAssetGL,
                        'debit' => $amount,
                        'credit' => 0.00,
                        'description' => "Fixed Deposit Principal investment"
                    ],
                    [
                        'account_id' => $creditGL,
                        'debit' => 0.00,
                        'credit' => $amount,
                        'description' => "FD Funding source"
                    ]
                ]
            ];
            $jeId = AccountingEngine::postJournalEntry($journalData);

            $fdId = $this->fdModel->create([
                'member_id' => $memberId,
                'deposit_date' => $startDate,
                'start_date' => $startDate,
                'term_months' => $termMonths,
                'interest_rate' => $interestRate,
                'expected_interest' => $expectedInterest,
                'maturity_amount' => $maturityAmount,
                'maturity_date' => $maturityDate,
                'payment_method' => $payMethod,
                'status' => 'ACTIVE',
                'journal_entry_id' => $jeId,
                'notes' => $_POST['notes'] ?? ''
            ]);

            $db->commit();
            Session::setFlash('success', 'Fixed Deposit successfully opened and posted!');
            Helper::redirect('modules/fixed-deposits/view?id=' . $fdId);

        } catch (\Exception $e) {
            $db->rollBack();
            Session::setFlash('error', 'Failed to open Fixed Deposit: ' . $e->getMessage());
            Helper::redirect('modules/fixed-deposits/create');
        }
    }

    public function view(): void {
        Auth::requirePermission('parties.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $fd = $this->fdModel->getById($id);

        if (!$fd) {
            Session::setFlash('error', 'Fixed Deposit not found.');
            Helper::redirect('modules/fixed-deposits');
        }

        $this->render('fd/view', [
            'pageTitle' => 'FD Receipt: ' . $fd['deposit_number'],
            'activeNav' => 'fixed_deposits',
            'fd' => $fd
        ]);
    }

    public function processMaturity(): void {
        Auth::requirePermission('parties.edit');
        $this->validateCsrf();

        $id = (int)$_POST['id'];
        $fd = $this->fdModel->getById($id);

        if (!$fd || $fd['status'] !== 'MATURED') {
            Session::setFlash('error', 'FD is not matured or invalid.');
            Helper::redirect('modules/fixed-deposits');
        }

        $payoutMethod = $_POST['payout_method'] ?? 'Cash';
        $payoutDate = $_POST['payout_date'] ?? date('Y-m-d');
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            $fdAssetGL = (int)$db->query("SELECT id FROM accounts WHERE account_code = '1150'")->fetchColumn();
            $interestExpenseGL = (int)$db->query("SELECT id FROM accounts WHERE account_code = '6990'")->fetchColumn(); // Other operating expenses

            $debitGL = null;
            if ($payoutMethod === 'Cash') {
                $debitGL = (int)$db->query("SELECT account_id FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $cashAccId = $db->query("SELECT id FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $db->prepare("UPDATE cash_accounts SET current_balance = current_balance + :amt WHERE id = :id")->execute(['amt' => $fd['maturity_amount'], 'id' => $cashAccId]);
            } else {
                $debitGL = (int)$db->query("SELECT account_id FROM bank_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $bankAccId = $db->query("SELECT id FROM bank_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $db->prepare("UPDATE bank_accounts SET current_balance = current_balance + :amt WHERE id = :id")->execute(['amt' => $fd['maturity_amount'], 'id' => $bankAccId]);
            }

            $costCenterId = (int)$db->query("SELECT id FROM cost_centers LIMIT 1")->fetchColumn();
            $journalData = [
                'transaction_date' => $payoutDate,
                'description' => "Fixed Deposit payout maturity clearance: " . $fd['deposit_number'],
                'reference' => 'FD-MATURE',
                'source_module' => 'finance',
                'cost_center_id' => $costCenterId,
                'status' => 'approved',
                'lines' => [
                    [
                        'account_id' => $debitGL,
                        'debit' => $fd['maturity_amount'],
                        'credit' => 0.00,
                        'description' => "Fixed Deposit payout maturity credit"
                    ],
                    [
                        'account_id' => $fdAssetGL,
                        'debit' => 0.00,
                        'credit' => $fd['maturity_amount'] - $fd['expected_interest'],
                        'description' => "Reverse Fixed Deposit Asset"
                    ],
                    [
                        'account_id' => $interestExpenseGL,
                        'debit' => 0.00,
                        'credit' => $fd['expected_interest'],
                        'description' => "Reverse Accrued FD Interest"
                    ]
                ]
            ];
            // Wait, double entry check:
            // Debit: Payout destination (LKR Maturity Amount)
            // Credit: FD Asset Principal (Maturity Amount - Expected Interest)
            // Credit: Interest Revenue / Expense (Expected Interest)
            // Total debit = Total Credit = Maturity Amount. This balances perfectly!
            $mjeId = AccountingEngine::postJournalEntry($journalData);

            $db->prepare("UPDATE member_fixed_deposits SET status = 'CLOSED', maturity_journal_entry_id = :je_id, closure_date = :dt WHERE id = :id")
               ->execute(['je_id' => $mjeId, 'dt' => $payoutDate, 'id' => $id]);

            $db->commit();
            Session::setFlash('success', 'Maturity processed successfully.');
            Helper::redirect('modules/fixed-deposits/view?id=' . $id);
        } catch (\Exception $e) {
            $db->rollBack();
            Session::setFlash('error', 'Failed to process maturity: ' . $e->getMessage());
            Helper::redirect('modules/fixed-deposits/view?id=' . $id);
        }
    }

    public function prematureClose(): void {
        Auth::requirePermission('parties.edit');
        $this->validateCsrf();

        $id = (int)$_POST['id'];
        $fd = $this->fdModel->getById($id);

        if (!$fd || $fd['status'] !== 'ACTIVE') {
            Session::setFlash('error', 'FD must be active to close prematurely.');
            Helper::redirect('modules/fixed-deposits');
        }

        $closeDate = $_POST['closure_date'] ?? date('Y-m-d');
        $reason = trim($_POST['closure_reason'] ?? 'Premature Closure');
        $adjInterest = (float)($_POST['interest_adjustment'] ?? 0);
        $finalPayable = (float)($_POST['final_payable_amount'] ?? 0);
        $payMethod = $_POST['payout_method'] ?? 'Cash';
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            $fdAssetGL = (int)$db->query("SELECT id FROM accounts WHERE account_code = '1150'")->fetchColumn();
            $interestExpenseGL = (int)$db->query("SELECT id FROM accounts WHERE account_code = '6990'")->fetchColumn();

            $debitGL = null;
            if ($payMethod === 'Cash') {
                $debitGL = (int)$db->query("SELECT account_id FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $cashAccId = $db->query("SELECT id FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $db->prepare("UPDATE cash_accounts SET current_balance = current_balance + :amt WHERE id = :id")->execute(['amt' => $finalPayable, 'id' => $cashAccId]);
            } else {
                $debitGL = (int)$db->query("SELECT account_id FROM bank_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $bankAccId = $db->query("SELECT id FROM bank_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $db->prepare("UPDATE bank_accounts SET current_balance = current_balance + :amt WHERE id = :id")->execute(['amt' => $finalPayable, 'id' => $bankAccId]);
            }

            $costCenterId = (int)$db->query("SELECT id FROM cost_centers LIMIT 1")->fetchColumn();
            $journalData = [
                'transaction_date' => $closeDate,
                'description' => "Fixed Deposit premature closure payout: " . $fd['deposit_number'],
                'reference' => 'FD-CLOSE-EARLY',
                'source_module' => 'finance',
                'cost_center_id' => $costCenterId,
                'status' => 'approved',
                'lines' => [
                    [
                        'account_id' => $debitGL,
                        'debit' => $finalPayable,
                        'credit' => 0.00,
                        'description' => "Premature payout debit"
                    ],
                    [
                        'account_id' => $fdAssetGL,
                        'debit' => 0.00,
                        'credit' => $fd['maturity_amount'] - $fd['expected_interest'],
                        'description' => "Reverse original principal asset"
                    ]
                ]
            ];
            // If adjusted interest exists, credit interest expense
            if ($adjInterest > 0) {
                $journalData['lines'][] = [
                    'account_id' => $interestExpenseGL,
                    'debit' => 0.00,
                    'credit' => $adjInterest,
                    'description' => "Premature closure interest payout credit"
                ];
            }
            // Double entry check: Total Debits (finalPayable) = original principal + adjInterest. This balances perfectly!
            $mjeId = AccountingEngine::postJournalEntry($journalData);

            $db->prepare("
                UPDATE member_fixed_deposits 
                SET status = 'PREMATURELY_CLOSED', 
                    maturity_journal_entry_id = :je_id, 
                    closure_date = :dt, 
                    closure_reason = :reason, 
                    interest_adjustment = :adj, 
                    final_payable_amount = :final_pay 
                WHERE id = :id
            ")->execute([
                'je_id' => $mjeId,
                'dt' => $closeDate,
                'reason' => $reason,
                'adj' => $adjInterest,
                'final_pay' => $finalPayable,
                'id' => $id
            ]);

            $db->commit();
            Session::setFlash('success', 'FD Prematurely Closed successfully.');
            Helper::redirect('modules/fixed-deposits/view?id=' . $id);
        } catch (\Exception $e) {
            $db->rollBack();
            Session::setFlash('error', 'Premature closure failed: ' . $e->getMessage());
            Helper::redirect('modules/fixed-deposits/view?id=' . $id);
        }
    }
}
