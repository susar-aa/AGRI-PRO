<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\InvoiceModel;
use App\Models\ProductModel;
use App\Models\ServiceModel;
use App\Models\Party;
use App\Models\Expense; // for cash/bank account queries
use App\Services\InvoiceEngine;
use App\Services\InventoryEngine;

class InvoiceController extends Controller {
    private InvoiceModel $invoiceModel;
    private ProductModel $productModel;
    private ServiceModel $serviceModel;
    private Party $partyModel;
    private Expense $expenseModel;

    public function __construct() {
        $this->invoiceModel = new InvoiceModel();
        $this->productModel = new ProductModel();
        $this->serviceModel = new ServiceModel();
        $this->partyModel = new Party();
        $this->expenseModel = new Expense();
    }

    public function index(): void {
        Auth::requirePermission('invoices.view');

        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'status' => $_GET['status'] ?? '',
            'customer_id' => $_GET['customer_id'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $invoices = $this->invoiceModel->getAll($filters, $limit, $offset);
        $totalItems = $this->invoiceModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        // Fetch active customers
        $db = \Core\Database::getInstance();
        $customers = $db->query("SELECT id, party_code, name FROM parties WHERE party_type IN ('CUSTOMER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();

        $this->render('invoices/index', [
            'pageTitle' => 'Central Invoices Directory',
            'activeNav' => 'invoices',
            'invoices' => $invoices,
            'filters' => $filters,
            'customers' => $customers,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function view(): void {
        Auth::requirePermission('invoices.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $invoice = $this->invoiceModel->getById($id);

        if (!$invoice) {
            Session::setFlash('error', 'Invoice record not found.');
            Helper::redirect('modules/invoices');
        }

        // Calculate COGS and Profitability for Posted Invoice
        $totalCogs = 0.00;
        if ($invoice['status'] === 'POSTED') {
            $db = \Core\Database::getInstance();
            $stmt = $db->prepare("SELECT SUM(total_cost) FROM stock_ledger WHERE source_module = 'SALES_INVOICE' AND source_transaction_id = :invoice_id AND quantity_out > 0");
            $stmt->execute(['invoice_id' => $id]);
            $totalCogs = (float)$stmt->fetchColumn();
        }

        $grossProfit = $invoice['total'] - $totalCogs;

        $this->render('invoices/view', [
            'pageTitle' => 'Invoice: ' . $invoice['invoice_number'],
            'activeNav' => 'invoices',
            'invoice' => $invoice,
            'totalCogs' => $totalCogs,
            'grossProfit' => $grossProfit
        ]);
    }

    public function create(): void {
        Auth::requirePermission('invoices.create');

        $db = \Core\Database::getInstance();

        // 1. Fetch active customers
        $customers = $db->query("SELECT id, party_code, name FROM parties WHERE party_type IN ('CUSTOMER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();
        
        // 1.5 Fetch active members
        $members = $db->query("SELECT id, membership_no, full_name, party_id FROM members WHERE status = 'ACTIVE' ORDER BY full_name ASC")->fetchAll();

        // 2. Fetch active warehouses (or just resolve the single warehouse system-wide)
        $warehouses = $db->query("SELECT id, code, name FROM inventory_locations WHERE is_active = 1 OR 1=1 ORDER BY name ASC")->fetchAll();
        // Single warehouse auto-resolved
        $defaultWarehouseId = null;
        if (!empty($warehouses)) {
            foreach ($warehouses as $wh) {
                if ($wh['code'] === 'LOC-MAIN') {
                    $defaultWarehouseId = $wh['id'];
                    break;
                }
            }
            if (!$defaultWarehouseId) {
                $defaultWarehouseId = $warehouses[0]['id'];
            }
        }

        // 3. Fetch active cash & bank accounts
        $cashAccounts = $this->expenseModel->getCashAccounts();
        $bankAccounts = $this->expenseModel->getBankAccounts();

        // 4. Fetch marketplace available products
        $products = $db->query("
            SELECT p.*, pc.name AS category_name, u.code AS unit_code
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            LEFT JOIN units_of_measure u ON p.sales_unit_id = u.id
            WHERE p.is_marketplace = 1 AND p.is_active = 1
            ORDER BY p.name_en ASC
        ")->fetchAll();

        // Fetch stock limits per warehouse
        foreach ($products as &$p) {
            $p['stocks'] = [];
            foreach ($warehouses as $wh) {
                $p['stocks'][$wh['id']] = InventoryEngine::getStockOnHand((int)$p['id'], (int)$wh['id']);
            }
        }

        // 5. Fetch active services registry catalog
        $services = $db->query("
            SELECT s.id, s.service_code, s.service_name, 
                   s.unit, s.default_price, s.id AS service_id, s.description
            FROM services s
            WHERE s.is_active = 1
            ORDER BY s.service_name ASC
        ")->fetchAll();

        // 6. Fetch active/eligible machinery rentals
        $rentals = $db->query("
            SELECT mr.*, m.machinery_name, m.machinery_code, p.name AS customer_name
            FROM machinery_rentals mr
            JOIN machinery m ON mr.machinery_id = m.id
            JOIN parties p ON mr.customer_id = p.id
            WHERE mr.status = 'ACTIVE' AND mr.invoice_id IS NULL
            ORDER BY mr.id DESC
        ")->fetchAll();

        // 7. Fetch all machinery assets from directory
        $machineryAssets = $db->query("
            SELECT * FROM machinery WHERE status = 'AVAILABLE' OR 1=1 ORDER BY machinery_name ASC
        ")->fetchAll();

        $prefilled = [
            'customer_id' => $_GET['customer_id'] ?? null,
            'service_id' => $_GET['service_id'] ?? null,
            'reference' => $_GET['reference'] ?? '',
            'service_job_id' => $_GET['service_job_id'] ?? null,
            'machinery_rental_id' => $_GET['machinery_rental_id'] ?? null
        ];

        $this->render('invoices/create', [
            'pageTitle' => 'Compose Invoice',
            'activeNav' => 'invoices',
            'customers' => $customers,
            'members' => $members,
            'warehouses' => $warehouses,
            'defaultWarehouseId' => $defaultWarehouseId,
            'cashAccounts' => $cashAccounts,
            'bankAccounts' => $bankAccounts,
            'products' => $products,
            'services' => $services,
            'rentals' => $rentals,
            'machineryAssets' => $machineryAssets,
            'prefilled' => $prefilled
        ]);
    }

    public function store(): void {
        Auth::requirePermission('invoices.create');
        $this->validateCsrf();

        $db = \Core\Database::getInstance();

        // Get single warehouse automatically if none provided
        $warehouseId = !empty($_POST['warehouse_id']) ? (int)$_POST['warehouse_id'] : null;
        if (!$warehouseId) {
            $warehouseId = (int)$db->query("SELECT id FROM inventory_locations WHERE code = 'LOC-MAIN' OR is_active = 1 LIMIT 1")->fetchColumn();
        }

        // Resolve Walk-in Customer ID
        $customerIdInput = $_POST['customer_id'] ?? '';
        $customerId = 0;
        
        $walkinCustomer = $db->query("SELECT id FROM parties WHERE party_code = 'PTY-WALKIN'")->fetch();
        $walkinId = $walkinCustomer ? (int)$walkinCustomer['id'] : 0;

        // Check customer selection
        if (empty($customerIdInput)) {
            $customerId = $walkinId;
        } elseif (strpos($customerIdInput, 'M_') === 0) {
            // It's a member
            $memberId = (int)substr($customerIdInput, 2);
            $member = $db->query("SELECT * FROM members WHERE id = " . $memberId)->fetch();
            if (!$member) {
                throw new \Exception("Selected member not found.");
            }
            if (!empty($member['party_id'])) {
                $customerId = (int)$member['party_id'];
            } else {
                // Auto-create a Party for this member
                $partyCode = 'CUST-' . strtoupper(substr(uniqid(), -6));
                $stmt = $db->prepare("
                    INSERT INTO parties (party_code, party_type, name, phone, address, status, credit_limit, opening_balance)
                    VALUES (:code, 'CUSTOMER', :name, :phone, :address, 'active', 0.00, 0.00)
                ");
                $stmt->execute([
                    'code' => $partyCode,
                    'name' => $member['full_name'],
                    'phone' => $member['phone'],
                    'address' => $member['address']
                ]);
                $customerId = (int)$db->lastInsertId();
                // Link party back to member
                $db->prepare("UPDATE members SET party_id = :pid WHERE id = :mid")->execute(['pid' => $customerId, 'mid' => $memberId]);
            }
        } else {
            $customerId = (int)$customerIdInput;
        }

        $paymentType = $_POST['payment_type'] ?? 'CASH';

        // Walk-in credit restriction validation
        if ($customerId == $walkinId && $paymentType === 'CREDIT') {
            throw new \Exception("Walk-in Customer is NOT allowed to make purchases on Credit. Please select a registered Customer.");
        }

        // Auto-select cash drawer for cash payment types
        $cashAccountId = !empty($_POST['cash_account_id']) ? (int)$_POST['cash_account_id'] : null;
        if ($paymentType === 'CASH' && !$cashAccountId) {
            $cashAccountId = (int)$db->query("SELECT id FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
        }

        // Compile lines from POST data
        $items = [];
        if (!empty($_POST['items'])) {
            foreach ($_POST['items'] as $it) {
                if ((float)($it['quantity'] ?? 0) > 0) {
                    $items[] = [
                        'item_type' => $it['item_type'] ?? 'PRODUCT',
                        'product_id' => !empty($it['product_id']) ? (int)$it['product_id'] : null,
                        'service_id' => !empty($it['service_id']) ? (int)$it['service_id'] : null,
                        'description' => trim($it['description'] ?? ''),
                        'quantity' => (float)$it['quantity'],
                        'unit_price' => (float)($it['unit_price'] ?? 0),
                        'discount' => (float)($it['discount'] ?? 0)
                    ];
                }
            }
        }

        $data = [
            'id' => !empty($_POST['id']) ? (int)$_POST['id'] : null,
            'customer_id' => $customerId,
            'invoice_date' => $_POST['invoice_date'] ?? date('Y-m-d'),
            'reference' => trim($_POST['reference'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'payment_type' => $paymentType,
            'warehouse_id' => $warehouseId,
            'cash_account_id' => $cashAccountId,
            'bank_account_id' => !empty($_POST['bank_account_id']) ? (int)$_POST['bank_account_id'] : null,
            'discount' => (float)($_POST['discount'] ?? 0),
            'items' => $items
        ];

        try {
            $invoiceId = InvoiceEngine::saveInvoice($data);

            // Link to service job if preselected (Stage 6C)
            $jobId = !empty($_POST['service_job_id']) ? (int)$_POST['service_job_id'] : 0;
            if ($jobId > 0) {
                $db->prepare("UPDATE service_jobs SET invoice_id = :invoice_id WHERE id = :job_id")
                   ->execute(['invoice_id' => $invoiceId, 'job_id' => $jobId]);
            }

            // Link to machinery rental if preselected (Stage 6D)
            $rentalId = !empty($_POST['machinery_rental_id']) ? (int)$_POST['machinery_rental_id'] : 0;
            if ($rentalId > 0) {
                $db->prepare("UPDATE machinery_rentals SET invoice_id = :invoice_id WHERE id = :rental_id")
                   ->execute(['invoice_id' => $invoiceId, 'rental_id' => $rentalId]);
            }

            if (!empty($_POST['action']) && $_POST['action'] === 'post') {
                Auth::requirePermission('invoices.post');
                $chequeInfo = [
                    'cheque_number' => $_POST['cheque_number'] ?? '',
                    'bank_name' => $_POST['cheque_bank'] ?? '',
                    'cheque_date' => $_POST['cheque_date'] ?? date('Y-m-d')
                ];
                InvoiceEngine::postInvoice($invoiceId, $chequeInfo);
                Session::setFlash('success', 'Invoice generated and posted successfully.');
            } else {
                Session::setFlash('success', 'Invoice recorded as Draft.');
            }

            Helper::redirect('modules/invoices/view?id=' . $invoiceId);

        } catch (\Exception $e) {
            Session::setFlash('error', 'Action failed: ' . $e->getMessage());
            Helper::redirect('modules/invoices/create');
        }
    }

    public function post(): void {
        Auth::requirePermission('invoices.post');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $chequeInfo = [
            'cheque_number' => $_POST['cheque_number'] ?? '',
            'bank_name' => $_POST['cheque_bank'] ?? '',
            'cheque_date' => $_POST['cheque_date'] ?? date('Y-m-d')
        ];

        try {
            InvoiceEngine::postInvoice($id, $chequeInfo);
            Session::setFlash('success', 'Invoice successfully posted. Financial and stock ledgers updated.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Posting failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/invoices/view?id=' . $id);
    }

    public function cancel(): void {
        Auth::requirePermission('invoices.cancel');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $reason = trim($_POST['reversal_reason'] ?? 'Invoice cancelled');

        try {
            InvoiceEngine::cancelInvoice($id, $reason);
            Session::setFlash('success', 'Invoice successfully cancelled and reversed.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Cancellation failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/invoices/view?id=' . $id);
    }
}
