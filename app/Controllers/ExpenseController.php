<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\Expense;
use App\Models\CostCenter;
use App\Models\Account;
use App\Services\ExpenseEngine;

class ExpenseController extends Controller {
    private Expense $expenseModel;
    private CostCenter $costCenterModel;
    private Account $accountModel;

    public function __construct() {
        $this->expenseModel = new Expense();
        $this->costCenterModel = new CostCenter();
        $this->accountModel = new Account();
    }

    public function index(): void {
        Auth::requirePermission('expenses.view');

        $filters = [
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'expense_number' => trim($_GET['expense_number'] ?? ''),
            'category_id' => $_GET['category_id'] ?? '',
            'payee' => trim($_GET['payee'] ?? ''),
            'payment_method' => $_GET['payment_method'] ?? '',
            'cost_center_id' => $_GET['cost_center_id'] ?? '',
            'source_module' => $_GET['source_module'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $expenses = $this->expenseModel->getAll($filters, $limit, $offset);
        $totalItems = $this->expenseModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        $categories = $this->expenseModel->getAllCategories();
        $costCenters = $this->costCenterModel->getActive();

        $this->render('expenses/index', [
            'pageTitle' => 'Central Expense Ledger',
            'activeNav' => 'expenses',
            'expenses' => $expenses,
            'categories' => $categories,
            'costCenters' => $costCenters,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function view(): void {
        Auth::requirePermission('expenses.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $expense = $this->expenseModel->getById($id);

        if (!$expense) {
            Session::setFlash('error', 'Expense voucher not found.');
            Helper::redirect('expenses');
        }

        // Fetch audit logs
        $db = \Core\Database::getInstance();
        $stmt = $db->prepare("
            SELECT al.*, u.full_name 
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE al.module = 'finance' AND al.record_id = :id
            ORDER BY al.id DESC
        ");
        $stmt->execute(['id' => $id]);
        $auditLogs = $stmt->fetchAll();

        $this->render('expenses/view', [
            'pageTitle' => 'Expense Voucher #' . $expense['expense_number'],
            'activeNav' => 'expenses',
            'expense' => $expense,
            'auditLogs' => $auditLogs
        ]);
    }

    public function create(): void {
        Auth::requirePermission('expenses.create');

        $categories = $this->expenseModel->getAllCategories();
        $costCenters = $this->costCenterModel->getActive();
        $cashAccounts = $this->expenseModel->getCashAccounts();
        $bankAccounts = $this->expenseModel->getBankAccounts();
        
        // Fetch suppliers for credit expense selection
        $db = \Core\Database::getInstance();
        $stmt = $db->prepare("SELECT id, party_code AS supplier_code, name AS name_en FROM parties WHERE party_type IN ('SUPPLIER', 'BOTH') AND status = 'active' ORDER BY name ASC");
        $stmt->execute();
        $suppliers = $stmt->fetchAll();

        // Fetch active service jobs (Stage 6C)
        $serviceJobs = $db->query("SELECT id, job_number, description FROM service_jobs WHERE status IN ('OPEN', 'IN_PROGRESS') ORDER BY job_number DESC")->fetchAll();

        // Fetch active machinery and active/draft rentals (Stage 6D)
        $machinery = $db->query("SELECT id, machinery_code, machinery_name FROM machinery WHERE status != 'INACTIVE' ORDER BY machinery_name ASC")->fetchAll();
        $machineryRentals = $db->query("SELECT id, rental_number FROM machinery_rentals WHERE status IN ('DRAFT', 'ACTIVE') ORDER BY rental_number DESC")->fetchAll();

        // Allow prefilled params from external modules (plantation, service, construction, etc.)
        $prefilled = [
            'source_module' => $_GET['source_module'] ?? 'GENERAL',
            'source_type' => $_GET['source_type'] ?? 'GENERAL_EXPENSE',
            'source_transaction_id' => $_GET['source_transaction_id'] ?? null,
            'cost_center_id' => $_GET['cost_center_id'] ?? null,
            'project_id' => $_GET['project_id'] ?? null,
            'batch_id' => $_GET['batch_id'] ?? null,
            'service_job_id' => $_GET['service_job_id'] ?? null,
            'machinery_id' => $_GET['machinery_id'] ?? null,
            'machinery_rental_id' => $_GET['machinery_rental_id'] ?? null,
            'reference' => $_GET['reference'] ?? ''
        ];

        $this->render('expenses/create', [
            'pageTitle' => 'Record Operational Expense',
            'activeNav' => 'expenses',
            'categories' => $categories,
            'costCenters' => $costCenters,
            'cashAccounts' => $cashAccounts,
            'bankAccounts' => $bankAccounts,
            'suppliers' => $suppliers,
            'serviceJobs' => $serviceJobs,
            'machinery' => $machinery,
            'machineryRentals' => $machineryRentals,
            'prefilled' => $prefilled
        ]);
    }

    public function store(): void {
        $action = $_POST['action'] ?? 'draft';

        if ($action === 'post') {
            Auth::requirePermission('expenses.post');
        } else {
            Auth::requirePermission('expenses.create');
        }

        $this->validateCsrf();

        // 1. Prepare Data
        $expenseData = [
            'expense_date' => trim($_POST['expense_date'] ?? date('Y-m-d')),
            'payee' => trim($_POST['payee'] ?? ''),
            'supplier_id' => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
            'expense_category_id' => !empty($_POST['expense_category_id']) ? (int)$_POST['expense_category_id'] : 0,
            'description' => trim($_POST['description'] ?? ''),
            'amount' => (float)($_POST['amount'] ?? 0),
            'payment_method' => $_POST['payment_method'] ?? '',
            'cash_account_id' => !empty($_POST['cash_account_id']) ? (int)$_POST['cash_account_id'] : null,
            'bank_account_id' => !empty($_POST['bank_account_id']) ? (int)$_POST['bank_account_id'] : null,
            'cost_center_id' => !empty($_POST['cost_center_id']) ? (int)$_POST['cost_center_id'] : null,
            'reference_number' => trim($_POST['reference_number'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'source_module' => trim($_POST['source_module'] ?? 'GENERAL'),
            'source_type' => trim($_POST['source_type'] ?? 'GENERAL_EXPENSE'),
            'source_transaction_id' => !empty($_POST['source_transaction_id']) ? (int)$_POST['source_transaction_id'] : null,
            'project_id' => !empty($_POST['project_id']) ? (int)$_POST['project_id'] : null,
            'batch_id' => !empty($_POST['batch_id']) ? (int)$_POST['batch_id'] : null,
            'service_job_id' => !empty($_POST['service_job_id']) ? (int)$_POST['service_job_id'] : null,
            'machinery_id' => !empty($_POST['machinery_id']) ? (int)$_POST['machinery_id'] : null,
            'machinery_rental_id' => !empty($_POST['machinery_rental_id']) ? (int)$_POST['machinery_rental_id'] : null,
            'status' => 'draft' // Always create as draft; postExpense will change to posted
        ];

        // 2. Handle File Attachment Upload
        if (isset($_FILES['attachment_file']) && $_FILES['attachment_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['attachment_file'];
            $fileSize = $file['size'];
            $fileType = $file['type'];
            $origName = basename($file['name']);
            
            // Validate File Size (Max 5MB)
            if ($fileSize > 5 * 1024 * 1024) {
                Session::setFlash('error', 'File size exceeds maximum limit of 5MB.');
                Helper::redirect('expenses/create');
            }

            // Validate File Type (MIMEs allowed)
            $allowedTypes = [
                'application/pdf',
                'image/jpeg',
                'image/jpg',
                'image/png'
            ];
            if (!in_array($fileType, $allowedTypes)) {
                Session::setFlash('error', 'Invalid file type. Only PDF, JPG, JPEG, and PNG are allowed.');
                Helper::redirect('expenses/create');
            }

            // Generate Secure Stored Name
            $ext = pathinfo($origName, PATHINFO_EXTENSION);
            $storedName = 'exp_' . md5(uniqid((string)time(), true)) . '.' . $ext;
            $uploadDir = 'c:/xampp/htdocs/AGRI PRO/public/uploads/expenses/';
            $targetPath = $uploadDir . $storedName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $expenseData['attachment_file'] = [
                    'original_name' => $origName,
                    'stored_name' => $storedName,
                    'file_path' => 'uploads/expenses/' . $storedName,
                    'mime_type' => $fileType,
                    'file_size' => $fileSize
                ];
            } else {
                Session::setFlash('error', 'Failed to move uploaded file.');
                Helper::redirect('expenses/create');
            }
        }

        try {
            if ($action === 'post') {
                $expenseId = ExpenseEngine::createExpense($expenseData);
                ExpenseEngine::postExpense($expenseId);
                Session::setFlash('success', 'Expense successfully posted and recorded in accounting.');
            } else {
                $expenseId = ExpenseEngine::createExpense($expenseData);
                Session::setFlash('success', "Expense successfully saved as draft.");
            }

            if (!empty($_POST['redirect_to'])) {
                Helper::redirect($_POST['redirect_to']);
            } else {
                Helper::redirect('expenses/view?id=' . $expenseId);
            }

        } catch (\Exception $e) {
            Session::setFlash('error', 'Expense recording failed: ' . $e->getMessage());
            if (!empty($_POST['redirect_to'])) {
                Helper::redirect($_POST['redirect_to']);
            } else {
                Helper::redirect('expenses/create');
            }
        }
    }

    public function submit(): void {
        Auth::requirePermission('expenses.submit');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            if (ExpenseEngine::submitForApproval($id)) {
                Session::setFlash('success', 'Expense voucher submitted for approval.');
            } else {
                Session::setFlash('error', 'Failed to submit expense.');
            }
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        Helper::redirect('expenses/view?id=' . $id);
    }

    public function approve(): void {
        Auth::requirePermission('expenses.approve');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            if (ExpenseEngine::approveExpense($id)) {
                Session::setFlash('success', 'Expense voucher approved.');
            } else {
                Session::setFlash('error', 'Failed to approve expense.');
            }
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        Helper::redirect('expenses/view?id=' . $id);
    }

    public function post(): void {
        Auth::requirePermission('expenses.post');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            if (ExpenseEngine::postExpense($id)) {
                Session::setFlash('success', 'Expense posted successfully. Accounting entries generated.');
            } else {
                Session::setFlash('error', 'Failed to post expense.');
            }
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        Helper::redirect('expenses/view?id=' . $id);
    }

    public function cancel(): void {
        Auth::requirePermission('expenses.cancel');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            if (ExpenseEngine::cancelExpense($id)) {
                Session::setFlash('success', 'Expense has been cancelled.');
            } else {
                Session::setFlash('error', 'Failed to cancel expense.');
            }
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        Helper::redirect('expenses/view?id=' . $id);
    }

    public function reverse(): void {
        Auth::requirePermission('expenses.reverse');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $reason = trim($_POST['reversal_reason'] ?? '');

        if (empty($reason)) {
            Session::setFlash('error', 'Reversal reason is required.');
            Helper::redirect('expenses/view?id=' . $id);
        }

        try {
            if (ExpenseEngine::reverseExpense($id, $reason)) {
                Session::setFlash('success', 'Expense reversed successfully. Reversal ledger entry generated.');
            } else {
                Session::setFlash('error', 'Failed to reverse expense.');
            }
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        Helper::redirect('expenses/view?id=' . $id);
    }

    public function reports(): void {
        Auth::requirePermission('reports.view');

        $filters = [
            'date_from' => $_GET['date_from'] ?? date('Y-01-01'),
            'date_to' => $_GET['date_to'] ?? date('Y-m-d'),
            'cost_center_id' => $_GET['cost_center_id'] ?? ''
        ];

        $reportData = $this->expenseModel->getExpenseReports($filters);
        $costCenters = $this->costCenterModel->getActive();

        $this->render('expenses/reports', [
            'pageTitle' => 'Expense Analytics & Reports',
            'activeNav' => 'expenses',
            'costCenters' => $costCenters,
            'filters' => $filters,
            'report' => $reportData
        ]);
    }
}
