<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\Party;
use App\Models\ReceiptPaymentModel;
use App\Models\ChequeModel;
use App\Models\Expense; // for cash/bank accounts helpers
use App\Services\PaymentEngine;
use App\Services\ChequeDepositEngine;

class ReceiptPaymentController extends Controller {
    private ReceiptPaymentModel $pmModel;
    private Party $partyModel;
    private Expense $expenseModel; // reuse cash/bank helper queries

    public function __construct() {
        $this->pmModel = new ReceiptPaymentModel();
        $this->partyModel = new Party();
        $this->expenseModel = new Expense();
    }

    public function receiptsIndex(): void {
        Auth::requirePermission('receipts.view');

        $filters = [
            'payment_type' => 'RECEIPT',
            'search' => trim($_GET['search'] ?? ''),
            'payment_method' => $_GET['payment_method'] ?? '',
            'status' => $_GET['status'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $receipts = $this->pmModel->getAll($filters, $limit, $offset);
        $totalItems = $this->pmModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        $this->render('receipts/index', [
            'pageTitle' => 'Customer Receipts Ledger',
            'activeNav' => 'receipts',
            'receipts' => $receipts,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function paymentsIndex(): void {
        Auth::requirePermission('supplier_payments.view');

        $filters = [
            'payment_type' => 'PAYMENT',
            'search' => trim($_GET['search'] ?? ''),
            'payment_method' => $_GET['payment_method'] ?? '',
            'status' => $_GET['status'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $payments = $this->pmModel->getAll($filters, $limit, $offset);
        $totalItems = $this->pmModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        $this->render('payments/index', [
            'pageTitle' => 'Supplier Payments Ledger',
            'activeNav' => 'payments',
            'payments' => $payments,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function view(): void {
        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $pr = $this->pmModel->getById($id);

        if (!$pr) {
            Session::setFlash('error', 'Payment/receipt record not found.');
            Helper::redirect('dashboard');
        }

        if ($pr['payment_type'] === 'RECEIPT') {
            Auth::requirePermission('receipts.view');
        } else {
            Auth::requirePermission('supplier_payments.view');
        }

        // Fetch audit logs
        $db = \Core\Database::getInstance();
        $stmt = $db->prepare("
            SELECT al.*, u.full_name 
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE al.module = 'parties' AND al.record_id = :party_id
            ORDER BY al.id DESC
        ");
        $stmt->execute(['party_id' => $pr['party_id']]);
        $auditLogs = $stmt->fetchAll();

        // Fetch cheque details if linked
        $cheque = null;
        if (!empty($pr['cheque_id'])) {
            $chModel = new ChequeModel();
            $cheque = $chModel->getById((int)$pr['cheque_id']);
        }

        $this->render(($pr['payment_type'] === 'RECEIPT') ? 'receipts/view' : 'payments/view', [
            'pageTitle' => ($pr['payment_type'] === 'RECEIPT' ? 'Receipt Voucher #' : 'Payment Voucher #') . $pr['payment_number'],
            'activeNav' => ($pr['payment_type'] === 'RECEIPT' ? 'receipts' : 'payments'),
            'pr' => $pr,
            'cheque' => $cheque,
            'auditLogs' => $auditLogs
        ]);
    }

    public function createReceipt(): void {
        Auth::requirePermission('receipts.create');

        $partyId = !empty($_GET['party_id']) ? (int)$_GET['party_id'] : 0;
        $party = $this->partyModel->getById($partyId);

        $cashAccounts = $this->expenseModel->getCashAccounts();
        $bankAccounts = $this->expenseModel->getBankAccounts();

        // Fetch active customers, members and directors
        $db = \Core\Database::getInstance();
        $customers = $db->query("SELECT id, party_code, name, party_type FROM parties WHERE party_type IN ('CUSTOMER', 'BOTH', 'MEMBER', 'DIRECTOR') AND status = 'active' ORDER BY name ASC")->fetchAll();

        // Fetch undeposited cheques (Stage 5E)
        $chModel = new ChequeModel();
        // Fetch undeposited cheques (Stage 5E)
        $chModel = new ChequeModel();
        $undepositedCheques = $chModel->getUndepositedCheques();

        // Fetch income accounts for Receipts (A/R, Share Capital, Registration Fees, Other Income)
        $incomeAccounts = $db->query("SELECT id, account_name, account_code FROM accounts WHERE id IN (12, 25, 37, 62) ORDER BY account_code ASC")->fetchAll();

        $this->render('receipts/create', [
            'pageTitle' => 'Record Customer Collection Receipt',
            'activeNav' => 'receipts',
            'selectedParty' => $party,
            'cashAccounts' => $cashAccounts,
            'bankAccounts' => $bankAccounts,
            'customers' => $customers,
            'cheques' => $undepositedCheques,
            'incomeAccounts' => $incomeAccounts
        ]);
    }

    public function createPayment(): void {
        Auth::requirePermission('supplier_payments.create');

        $partyId = !empty($_GET['party_id']) ? (int)$_GET['party_id'] : 0;
        $party = $this->partyModel->getById($partyId);

        $cashAccounts = $this->expenseModel->getCashAccounts();
        $bankAccounts = $this->expenseModel->getBankAccounts();

        // Fetch active suppliers
        $db = \Core\Database::getInstance();
        $suppliers = $db->query("SELECT id, party_code, name FROM parties WHERE party_type IN ('SUPPLIER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();

        $this->render('payments/create', [
            'pageTitle' => 'Make Supplier Payment Voucher',
            'activeNav' => 'payments',
            'selectedParty' => $party,
            'cashAccounts' => $cashAccounts,
            'bankAccounts' => $bankAccounts,
            'suppliers' => $suppliers
        ]);
    }

    public function store(): void {
        $type = $_POST['payment_type'] ?? '';
        $action = $_POST['action'] ?? 'draft';

        if ($type === 'RECEIPT') {
            if ($action === 'post') {
                Auth::requirePermission('receipts.post');
            } else {
                Auth::requirePermission('receipts.create');
            }
        } else {
            if ($action === 'post') {
                Auth::requirePermission('supplier_payments.post');
            } else {
                Auth::requirePermission('supplier_payments.create');
            }
        }

        $this->validateCsrf();

        $method = $_POST['payment_method'] ?? '';
        $chequeId = null;

        if ($type === 'RECEIPT' && $method === 'Cheque') {
            $linkType = $_POST['cheque_link_type'] ?? 'new';
            if ($linkType === 'existing') {
                $chequeId = !empty($_POST['cheque_id']) ? (int)$_POST['cheque_id'] : null;
                if (!$chequeId) {
                    Session::setFlash('error', 'Please select an existing received cheque.');
                    Helper::redirect('receipts/create');
                }
            } else {
                // Record new cheque
                $chequeData = [
                    'cheque_number' => trim($_POST['cheque_number_input'] ?? ''),
                    'party_id' => !empty($_POST['party_id']) ? (int)$_POST['party_id'] : 0,
                    'bank_name' => trim($_POST['cheque_bank_name'] ?? ''),
                    'cheque_date' => $_POST['cheque_date'] ?? date('Y-m-d'),
                    'amount' => (float)($_POST['amount'] ?? 0),
                    'received_issued_date' => $_POST['payment_date'] ?? date('Y-m-d'),
                    'reference_number' => trim($_POST['reference_number'] ?? ''),
                    'notes' => trim($_POST['notes'] ?? '')
                ];

                try {
                    $chequeId = ChequeDepositEngine::recordCheque($chequeData);
                } catch (\Exception $e) {
                    Session::setFlash('error', 'Failed to register cheque: ' . $e->getMessage());
                    Helper::redirect('receipts/create');
                }
            }
        }

        $data = [
            'party_id' => !empty($_POST['party_id']) ? (int)$_POST['party_id'] : 0,
            'payment_type' => $type,
            'payment_date' => $_POST['payment_date'] ?? date('Y-m-d'),
            'payment_method' => $method,
            'cash_account_id' => !empty($_POST['cash_account_id']) ? (int)$_POST['cash_account_id'] : null,
            'bank_account_id' => !empty($_POST['bank_account_id']) ? (int)$_POST['bank_account_id'] : null,
            'cheque_id' => $chequeId,
            'amount' => (float)($_POST['amount'] ?? 0),
            'reference_number' => trim($_POST['reference_number'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'income_account_id' => !empty($_POST['income_account_id']) ? (int)$_POST['income_account_id'] : null,
            'created_by' => Auth::id() ?? 1
        ];

        try {
            $id = PaymentEngine::recordPayment($data);

            if ($action === 'post') {
                PaymentEngine::postPayment($id);
                Session::setFlash('success', 'Voucher successfully saved and posted to accounting ledger.');
            } else {
                Session::setFlash('success', 'Voucher successfully recorded as Draft.');
            }

            Helper::redirect('parties/view?id=' . $data['party_id']);

        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to save voucher: ' . $e->getMessage());
            $redirectUrl = ($type === 'RECEIPT') ? 'receipts/create' : 'supplier-payments/create';
            if ($data['party_id'] > 0) $redirectUrl .= '?party_id=' . $data['party_id'];
            Helper::redirect($redirectUrl);
        }
    }

    public function post(): void {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $pr = $this->pmModel->getById($id);

        if (!$pr) {
            Session::setFlash('error', 'Record not found.');
            Helper::redirect('dashboard');
        }

        if ($pr['payment_type'] === 'RECEIPT') {
            Auth::requirePermission('receipts.post');
        } else {
            Auth::requirePermission('supplier_payments.post');
        }

        $this->validateCsrf();

        try {
            PaymentEngine::postPayment($id);
            Session::setFlash('success', 'Voucher successfully posted to accounting ledger.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Posting failed: ' . $e->getMessage());
        }

        Helper::redirect('parties/view?id=' . $pr['party_id']);
    }

    public function reverse(): void {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $reason = trim($_POST['reversal_reason'] ?? '');

        if (empty($reason)) {
            Session::setFlash('error', 'Reversal reason is required.');
            Helper::redirect('dashboard');
        }

        $pr = $this->pmModel->getById($id);
        if (!$pr) {
            Session::setFlash('error', 'Record not found.');
            Helper::redirect('dashboard');
        }

        if ($pr['payment_type'] === 'RECEIPT') {
            Auth::requirePermission('receipts.reverse');
        } else {
            Auth::requirePermission('supplier_payments.reverse');
        }

        $this->validateCsrf();

        try {
            PaymentEngine::reversePayment($id, $reason);
            Session::setFlash('success', 'Voucher successfully reversed. Offset entries generated.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Reversal failed: ' . $e->getMessage());
        }

        Helper::redirect('parties/view?id=' . $pr['party_id']);
    }
}
