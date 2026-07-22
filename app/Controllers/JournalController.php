<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\Journal;
use App\Models\Account;
use App\Models\CostCenter;
use App\Services\AccountingEngine;

class JournalController extends Controller {
    private Journal $journalModel;
    private Account $accountModel;
    private CostCenter $costCenterModel;

    public function __construct() {
        $this->journalModel = new Journal();
        $this->accountModel = new Account();
        $this->costCenterModel = new CostCenter();
    }

    public function index(): void {
        Auth::requirePermission('journal.view');

        $entries = $this->journalModel->getAll(100, 0);
        $accounts = $this->accountModel->getAllFlat();
        $costCenters = $this->costCenterModel->getActive();

        $this->render('accounting/journal_entries', [
            'pageTitle' => 'Double-Entry Journal Vouchers',
            'activeNav' => 'journal_entries',
            'entries' => $entries,
            'accounts' => $accounts,
            'costCenters' => $costCenters
        ]);
    }

    public function view(): void {
        Auth::requirePermission('journal.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $entry = $this->journalModel->getById($id);

        if (!$entry) {
            Session::setFlash('error', 'Journal voucher not found.');
            Helper::redirect('accounting/journal-entries');
        }

        // Fetch audit logs for this journal
        $db = \Core\Database::getInstance();
        $stmt = $db->prepare("
            SELECT al.*, u.full_name 
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE al.module = 'accounting' AND al.record_id = :id
            ORDER BY al.id DESC
        ");
        $stmt->execute(['id' => $id]);
        $auditLogs = $stmt->fetchAll();

        // If this journal is reversed or is a reversal, fetch linked journal
        $linkedJournal = null;
        if (!empty($entry['reversal_of_journal_id'])) {
            $linkedJournal = $this->journalModel->getById((int)$entry['reversal_of_journal_id']);
        } else {
            $stmt = $db->prepare("SELECT id, journal_number FROM journal_entries WHERE reversal_of_journal_id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $linkedJournal = $stmt->fetch();
        }

        $this->render('accounting/journal_view', [
            'pageTitle' => 'Journal Voucher #' . $entry['journal_number'],
            'activeNav' => 'journal_entries',
            'entry' => $entry,
            'auditLogs' => $auditLogs,
            'linkedJournal' => $linkedJournal
        ]);
    }

    public function store(): void {
        $action = $_POST['action'] ?? 'draft';

        if ($action === 'post') {
            Auth::requirePermission('journal.post');
        } else {
            Auth::requirePermission('journal.create');
        }

        $this->validateCsrf();

        $date = trim($_POST['transaction_date'] ?? date('Y-m-d'));
        $description = trim($_POST['description'] ?? '');
        $reference = trim($_POST['reference'] ?? '');
        $costCenterId = !empty($_POST['cost_center_id']) ? (int)$_POST['cost_center_id'] : null;

        $accounts = $_POST['account_id'] ?? [];
        $debits = $_POST['debit'] ?? [];
        $credits = $_POST['credit'] ?? [];
        $lineDescs = $_POST['line_description'] ?? [];

        if (empty($description) || empty($accounts) || count($accounts) < 2) {
            Session::setFlash('error', 'Please fill description and at least two line items.');
            Helper::redirect('accounting/journal-entries');
        }

        $lines = [];
        for ($i = 0; $i < count($accounts); $i++) {
            $accId = (int)($accounts[$i] ?? 0);
            $dr = (float)($debits[$i] ?? 0);
            $cr = (float)($credits[$i] ?? 0);

            if ($accId > 0 && ($dr > 0 || $cr > 0)) {
                $lines[] = [
                    'account_id' => $accId,
                    'debit' => $dr,
                    'credit' => $cr,
                    'description' => trim($lineDescs[$i] ?? $description)
                ];
            }
        }

        try {
            $status = 'draft';
            if ($action === 'submit') {
                $status = 'pending_approval';
            } elseif ($action === 'post') {
                $status = 'approved'; // Will be immediately posted
            }

            $journalData = [
                'transaction_date' => $date,
                'description' => $description,
                'reference' => $reference,
                'source_module' => 'manual',
                'cost_center_id' => $costCenterId,
                'status' => $status,
                'lines' => $lines
            ];

            if ($action === 'post') {
                $journalId = AccountingEngine::postJournalEntry($journalData);
                Session::setFlash('success', "Journal Entry successfully posted! (Number: #{$journalId})");
            } else {
                $journalId = AccountingEngine::createJournalEntry($journalData);
                $msg = $action === 'submit' ? 'submitted for approval' : 'saved as draft';
                Session::setFlash('success', "Journal Entry successfully {$msg}! (ID: #{$journalId})");
            }

            Helper::redirect('accounting/journal-entries/view?id=' . $journalId);

        } catch (\Exception $e) {
            Session::setFlash('error', "Journal Error: " . $e->getMessage());
            Helper::redirect('accounting/journal-entries');
        }
    }

    public function submit(): void {
        Auth::requirePermission('journal.submit');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            if (AccountingEngine::submitForApproval($id)) {
                Session::setFlash('success', 'Journal submitted for approval.');
            } else {
                Session::setFlash('error', 'Failed to submit journal.');
            }
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        Helper::redirect('accounting/journal-entries/view?id=' . $id);
    }

    public function approve(): void {
        Auth::requirePermission('journal.approve');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            if (AccountingEngine::approveJournalEntry($id)) {
                Session::setFlash('success', 'Journal approved successfully.');
            } else {
                Session::setFlash('error', 'Failed to approve journal.');
            }
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        Helper::redirect('accounting/journal-entries/view?id=' . $id);
    }

    public function post(): void {
        Auth::requirePermission('journal.post');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            AccountingEngine::postJournalEntry($id);
            Session::setFlash('success', 'Journal posted to General Ledger.');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        Helper::redirect('accounting/journal-entries/view?id=' . $id);
    }

    public function cancel(): void {
        Auth::requirePermission('journal.cancel');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            if (AccountingEngine::cancelJournalEntry($id)) {
                Session::setFlash('success', 'Journal has been cancelled.');
            } else {
                Session::setFlash('error', 'Failed to cancel journal.');
            }
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        Helper::redirect('accounting/journal-entries/view?id=' . $id);
    }

    public function reverse(): void {
        Auth::requirePermission('journal.reverse');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $reason = trim($_POST['reversal_reason'] ?? '');

        if (empty($reason)) {
            Session::setFlash('error', 'Reversal reason is required.');
            Helper::redirect('accounting/journal-entries/view?id=' . $id);
        }

        try {
            $reversalId = AccountingEngine::reverseJournalEntry($id, $reason);
            Session::setFlash('success', 'Journal reversed successfully. Reversal Entry created.');
            Helper::redirect('accounting/journal-entries/view?id=' . $reversalId);
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Helper::redirect('accounting/journal-entries/view?id=' . $id);
        }
    }

    public function generalLedger(): void {
        Auth::requirePermission('ledger.view');

        $accountId = !empty($_GET['account_id']) ? (int)$_GET['account_id'] : null;
        $fromDate = $_GET['from_date'] ?? date('Y-01-01');
        $toDate = $_GET['to_date'] ?? date('Y-m-d');
        $costCenterId = !empty($_GET['cost_center_id']) ? (int)$_GET['cost_center_id'] : null;

        $ledgerEntries = $this->journalModel->getGeneralLedger($accountId, $fromDate, $toDate, $costCenterId);
        $accounts = $this->accountModel->getAllFlat();
        $costCenters = $this->costCenterModel->getActive();

        $this->render('accounting/general_ledger', [
            'pageTitle' => 'General Ledger Report',
            'activeNav' => 'general_ledger',
            'ledgerEntries' => $ledgerEntries,
            'accounts' => $accounts,
            'costCenters' => $costCenters,
            'selectedAccountId' => $accountId,
            'selectedCostCenterId' => $costCenterId,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);
    }

    public function trialBalance(): void {
        Auth::requirePermission('trial_balance.view');

        $fromDate = $_GET['from_date'] ?? '';
        $toDate = $_GET['to_date'] ?? date('Y-m-d');
        $costCenterId = !empty($_GET['cost_center_id']) ? (int)$_GET['cost_center_id'] : null;

        $trialBalance = $this->journalModel->getTrialBalance($toDate, $fromDate, $costCenterId);
        $costCenters = $this->costCenterModel->getActive();

        $this->render('accounting/trial_balance', [
            'pageTitle' => 'Trial Balance Statement',
            'activeNav' => 'trial_balance',
            'trialBalance' => $trialBalance,
            'costCenters' => $costCenters,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'selectedCostCenterId' => $costCenterId
        ]);
    }
}
