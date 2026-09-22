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
        
        // 1.5 Fetch active members and directors
        $members = $db->query("SELECT id, member_no, full_name, member_type, party_id FROM coop_members WHERE status = 'ACTIVE' AND member_type IN ('MEMBER', 'DIRECTOR') ORDER BY full_name ASC")->fetchAll();

        // 1.6 Fetch active staff (users)
        $staff = $db->query("SELECT id, username, full_name, party_id FROM users WHERE status = 'active' ORDER BY full_name ASC")->fetchAll();

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
            'staff' => $staff,
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

    
    public function edit(): void {
        Auth::requirePermission('invoices.edit');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $invoice = $this->invoiceModel->getById($id);

        if (!$invoice || ($invoice['status'] !== 'DRAFT' && $invoice['status'] !== 'POSTED')) {
            Session::setFlash('error', 'Invoice cannot be edited (Not found or cancelled).');
            Helper::redirect('modules/invoices');
        }

        $db = \Core\Database::getInstance();
        $customers = $db->query("SELECT id, party_code, name, phone, address FROM parties WHERE party_type IN ('CUSTOMER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();
        $members = $db->query("SELECT id, member_number, full_name, phone, address, party_id FROM coop_members WHERE status = 'ACTIVE' ORDER BY full_name ASC")->fetchAll();
        $staff = $db->query("SELECT id, username, full_name, role_id, party_id FROM users WHERE status = 'active' ORDER BY full_name ASC")->fetchAll();
        $warehouses = $db->query("SELECT id, code, name FROM inventory_locations WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        $defaultWarehouseId = $warehouses[0]['id'] ?? 1;
        $cashAccounts = $db->query("SELECT id, name FROM cash_accounts WHERE status = 'active' ORDER BY name ASC")->fetchAll();
        $bankAccounts = $db->query("SELECT id, account_name, bank_name FROM bank_accounts WHERE status = 'active' ORDER BY account_name ASC")->fetchAll();
        $products = $db->query("SELECT p.id, p.sku, p.name_en, p.price, p.sales_unit_id, u.code AS unit_code FROM products p LEFT JOIN units_of_measure u ON p.sales_unit_id = u.id WHERE p.status = 'active' ORDER BY p.name_en ASC")->fetchAll();

        foreach ($products as &$p) {
            $p['stocks'] = [];
            foreach ($warehouses as $wh) {
                $p['stocks'][$wh['id']] = \App\Services\InventoryEngine::getStockOnHand((int)$p['id'], (int)$wh['id']);
            }
        }

        $services = $db->query("SELECT s.id, s.service_code, s.service_name, s.unit, s.default_price, s.id AS service_id, s.description FROM services s WHERE s.is_active = 1 ORDER BY s.service_name ASC")->fetchAll();
        $rentals = $db->query("SELECT mr.*, m.machinery_name, m.machinery_code, pt.name AS customer_name FROM machinery_rentals mr JOIN machinery m ON mr.machinery_id = m.id JOIN parties pt ON mr.customer_id = pt.id WHERE mr.status = 'ACTIVE' AND (mr.invoice_id IS NULL OR mr.invoice_id = {$id}) ORDER BY mr.id DESC")->fetchAll();
        $machineryAssets = $db->query("SELECT * FROM machinery WHERE status = 'AVAILABLE' OR 1=1 ORDER BY machinery_name ASC")->fetchAll();

        // Convert the invoice_items back into the format expected by the JS
        $editItems = [];
        foreach ($invoice['items'] as $itm) {
            $editItems[] = [
                'type' => $itm['item_type'],
                'product_id' => $itm['product_id'] ?? '',
                'product_name' => $itm['product_name'] ?? '',
                'service_id' => $itm['service_id'] ?? '',
                'service_name' => $itm['service_name'] ?? '',
                'description' => $itm['description'] ?? '',
                'unit' => $itm['product_unit'] ?? $itm['service_unit'] ?? 'Qty',
                'qty' => (float)$itm['quantity'],
                'price' => (float)$itm['unit_price'],
                'total' => (float)$itm['total'],
                'service_job_id' => $itm['service_job_id'] ?? '',
                'machinery_rental_id' => $itm['machinery_rental_id'] ?? ''
            ];
        }

        $this->render('invoices/edit', [
            'pageTitle' => 'Edit Invoice ' . $invoice['invoice_number'],
            'activeNav' => 'invoices',
            'customers' => $customers,
            'members' => $members,
            'staff' => $staff,
            'warehouses' => $warehouses,
            'defaultWarehouseId' => $defaultWarehouseId,
            'cashAccounts' => $cashAccounts,
            'bankAccounts' => $bankAccounts,
            'products' => $products,
            'services' => $services,
            'rentals' => $rentals,
            'machineryAssets' => $machineryAssets,
            'invoice' => $invoice,
            'editItems' => $editItems
        ]);
    }

    
    public function update(): void {
        Auth::requirePermission('invoices.edit');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            Session::setFlash('error', 'Invoice ID is required for update.');
            Helper::redirect('modules/invoices');
        }

        $db = \Core\Database::getInstance();
        
        $invoice = $this->invoiceModel->getById($id);
        if (!$invoice || ($invoice['status'] !== 'DRAFT' && $invoice['status'] !== 'POSTED')) {
            Session::setFlash('error', 'Cannot update this invoice. It may be cancelled.');
            Helper::redirect('modules/invoices');
        }

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

        if (empty($customerIdInput)) {
            $customerId = $walkinId;
        } elseif (strpos($customerIdInput, 'M_') === 0) {
            $memberId = (int)substr($customerIdInput, 2);
            $member = $db->query("SELECT * FROM coop_members WHERE id = " . $memberId . " AND member_type IN ('MEMBER', 'DIRECTOR')")->fetch();
            if (!$member) throw new \Exception("Selected member/director not found.");
            
            if (!empty($member['party_id'])) {
                $customerId = (int)$member['party_id'];
            } else {
                $partyCode = 'CUST-' . strtoupper(substr(uniqid(), -6));
                $stmt = $db->prepare("INSERT INTO parties (party_code, party_type, name, phone, address, status, credit_limit, opening_balance) VALUES (:code, 'CUSTOMER', :name, :phone, :address, 'active', 0.00, 0.00)");
                $stmt->execute(['code' => $partyCode, 'name' => $member['full_name'], 'phone' => $member['phone'], 'address' => $member['address']]);
                $customerId = (int)$db->lastInsertId();
                $db->prepare("UPDATE coop_members SET party_id = :pid WHERE id = :mid")->execute(['pid' => $customerId, 'mid' => $memberId]);
            }
        } elseif (strpos($customerIdInput, 'U_') === 0) {
            $userId = (int)substr($customerIdInput, 2);
            $staff = $db->query("SELECT * FROM users WHERE id = " . $userId)->fetch();
            if (!$staff) throw new \Exception("Selected staff not found.");
            
            if (!empty($staff['party_id'])) {
                $customerId = (int)$staff['party_id'];
            } else {
                $partyCode = 'STF-' . strtoupper(substr(uniqid(), -6));
                $stmt = $db->prepare("INSERT INTO parties (party_code, party_type, name, phone, status, credit_limit, opening_balance) VALUES (:code, 'CUSTOMER', :name, :phone, 'active', 0.00, 0.00)");
                $stmt->execute(['code' => $partyCode, 'name' => $staff['full_name'], 'phone' => $staff['phone'] ?? '']);
                $customerId = (int)$db->lastInsertId();
                $db->prepare("UPDATE users SET party_id = :pid WHERE id = :uid")->execute(['pid' => $customerId, 'uid' => $userId]);
            }
        } else {
            $customerId = (int)$customerIdInput;
        }

        $invoiceDate = $_POST['invoice_date'] ?? date('Y-m-d');
        $dueDate = $_POST['due_date'] ?? $invoiceDate;
        $reference = trim($_POST['reference'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $paymentType = $_POST['payment_type'] ?? 'CASH';
        
        $cashAccountId = !empty($_POST['cash_account_id']) ? (int)$_POST['cash_account_id'] : null;
        $bankAccountId = !empty($_POST['bank_account_id']) ? (int)$_POST['bank_account_id'] : null;
        $chequeNo = trim($_POST['cheque_number'] ?? '');
        $chequeBank = trim($_POST['cheque_bank'] ?? '');
        $chequeDate = $_POST['cheque_date'] ?? null;

        $itemTypes = $_POST['item_type'] ?? [];
        $productIds = $_POST['product_id'] ?? [];
        $serviceIds = $_POST['service_id'] ?? [];
        $descriptions = $_POST['item_description'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        $unitPrices = $_POST['unit_price'] ?? [];
        $totals = $_POST['line_total'] ?? [];
        $serviceJobIds = $_POST['service_job_id'] ?? [];
        $machineryRentalIds = $_POST['machinery_rental_id'] ?? [];

        $subtotal = 0.00;
        $taxAmount = 0.00;

        if (empty($itemTypes)) {
            Session::setFlash('error', 'Please add at least one item to the invoice.');
            Helper::redirect('modules/invoices/edit?id=' . $id);
        }

        try {
            $db->beginTransaction();

                        // If POSTED, we must reverse the old ledger and balances before updating items
            if ($invoice['status'] === 'POSTED') {
                if ($invoice['payment_type'] === 'CASH' && $invoice['cash_account_id']) {
                    $db->exec("UPDATE cash_accounts SET current_balance = current_balance - {$invoice['total']} WHERE id = {$invoice['cash_account_id']}");
                } elseif ($invoice['payment_type'] === 'BANK' && $invoice['bank_account_id']) {
                    $db->exec("UPDATE bank_accounts SET current_balance = current_balance - {$invoice['total']} WHERE id = {$invoice['bank_account_id']}");
                }
                
                $journal = $db->query("SELECT id FROM journal_entries WHERE source_module = 'invoices' AND source_transaction_id = {$id}")->fetch();
                if ($journal) {
                    $db->exec("DELETE FROM journal_lines WHERE journal_entry_id = {$journal['id']}");
                    $db->exec("DELETE FROM journal_entries WHERE id = {$journal['id']}");
                }
                
                $db->exec("DELETE FROM stock_ledger WHERE source_module = 'SALES_INVOICE' AND source_transaction_id = {$id}");
                
                if ($invoice['cheque_id']) {
                    $db->exec("DELETE FROM cheques WHERE id = {$invoice['cheque_id']}");
                }

                // Temporarily set to DRAFT so postInvoice() can run later
                $db->exec("UPDATE invoices SET status = 'DRAFT' WHERE id = {$id}");
            }

            // 1. Delete existing items
            $db->prepare("DELETE FROM invoice_items WHERE invoice_id = :id")->execute(['id' => $id]);

            // 2. Insert new items
            $insertItemStmt = $db->prepare("
                INSERT INTO invoice_items 
                (invoice_id, item_type, product_id, service_id, description, quantity, unit_price, total, tax_amount, service_job_id, machinery_rental_id)
                VALUES 
                (:inv_id, :type, :prod_id, :srv_id, :desc, :qty, :price, :tot, 0.00, :job_id, :rental_id)
            ");

            for ($i = 0; $i < count($itemTypes); $i++) {
                $type = $itemTypes[$i];
                $pid = !empty($productIds[$i]) ? (int)$productIds[$i] : null;
                $sid = !empty($serviceIds[$i]) ? (int)$serviceIds[$i] : null;
                $desc = $descriptions[$i] ?? '';
                $qty = (float)($quantities[$i] ?? 0);
                $price = (float)($unitPrices[$i] ?? 0);
                $tot = (float)($totals[$i] ?? 0);
                $sjid = !empty($serviceJobIds[$i]) ? (int)$serviceJobIds[$i] : null;
                $mrid = !empty($machineryRentalIds[$i]) ? (int)$machineryRentalIds[$i] : null;

                if ($tot <= 0) continue;

                $insertItemStmt->execute([
                    'inv_id' => $id,
                    'type' => $type,
                    'prod_id' => $pid,
                    'srv_id' => $sid,
                    'desc' => $desc,
                    'qty' => $qty,
                    'price' => $price,
                    'tot' => $tot,
                    'job_id' => $sjid,
                    'rental_id' => $mrid
                ]);

                $subtotal += $tot;
            }

            $discount = (float)($_POST['discount'] ?? 0);
            $netTotal = $subtotal - $discount;
            if ($netTotal < 0) $netTotal = 0;

            // 3. Update invoice header
            $stmt = $db->prepare("
                UPDATE invoices SET 
                    customer_id = :customer_id, 
                    warehouse_id = :warehouse_id,
                    invoice_date = :invoice_date, 
                    reference = :reference,
                    notes = :notes, 
                    payment_type = :payment_type, 
                    cash_account_id = :cash_id, 
                    bank_account_id = :bank_id,
                    subtotal = :subtotal, 
                    discount = :discount, 
                    tax_amount = :tax_amount, 
                    total = :total, 
                    updated_at = NOW()
                WHERE id = :id
            ");

            $stmt->execute([
                'customer_id' => $customerId,
                'warehouse_id' => $warehouseId,
                'invoice_date' => $invoiceDate,
                'reference' => $reference,
                'notes' => $notes,
                'payment_type' => $paymentType,
                'cash_id' => $cashAccountId,
                'bank_id' => $bankAccountId,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax_amount' => $taxAmount,
                'total' => $netTotal,
                'id' => $id
            ]);

                        // Save cheque details to session if CHEQUE payment type
            if ($paymentType === 'CHEQUE') {
                $_SESSION['temp_cheque_' . $id] = [
                    'cheque_number' => $chequeNo,
                    'bank_name' => $chequeBank,
                    'cheque_date' => $chequeDate
                ];
            } else {
                unset($_SESSION['temp_cheque_' . $id]);
            }

            $db->commit();
            
            if ($invoice['status'] === 'POSTED') {
                $chequeInfo = [
                    'cheque_number' => $chequeNo,
                    'bank_name' => $chequeBank,
                    'cheque_date' => $chequeDate
                ];
                \App\Services\InvoiceEngine::postInvoice($id, $chequeInfo);
                Session::setFlash('success', 'Invoice updated and re-posted successfully.');
            } else {
                Session::setFlash('success', 'Invoice updated successfully.');
            }
            
            Helper::redirect('modules/invoices/view?id=' . $id);

        } catch (\Exception $e) {
            $db->rollBack();
            Session::setFlash('error', 'Error updating invoice: ' . $e->getMessage());
            Helper::redirect('modules/invoices/edit?id=' . $id);
        }
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
            // It's a member or director
            $memberId = (int)substr($customerIdInput, 2);
            $member = $db->query("SELECT * FROM coop_members WHERE id = " . $memberId . " AND member_type IN ('MEMBER', 'DIRECTOR')")->fetch();
            if (!$member) {
                throw new \Exception("Selected member/director not found.");
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
                $db->prepare("UPDATE coop_members SET party_id = :pid WHERE id = :mid")->execute(['pid' => $customerId, 'mid' => $memberId]);
            }
        } elseif (strpos($customerIdInput, 'U_') === 0) {
            // It's a staff user
            $userId = (int)substr($customerIdInput, 2);
            $user = $db->query("SELECT * FROM users WHERE id = " . $userId)->fetch();
            if (!$user) {
                throw new \Exception("Selected staff member not found.");
            }
            if (!empty($user['party_id'])) {
                $customerId = (int)$user['party_id'];
            } else {
                // Auto-create a Party for this user
                $partyCode = 'CUST-' . strtoupper(substr(uniqid(), -6));
                $stmt = $db->prepare("
                    INSERT INTO parties (party_code, party_type, name, phone, address, status, credit_limit, opening_balance)
                    VALUES (:code, 'CUSTOMER', :name, :phone, :address, 'active', 0.00, 0.00)
                ");
                $stmt->execute([
                    'code' => $partyCode,
                    'name' => $user['full_name'],
                    'phone' => $user['phone'] ?? '',
                    'address' => ''
                ]);
                $customerId = (int)$db->lastInsertId();
                // Link party back to user
                $db->prepare("UPDATE users SET party_id = :pid WHERE id = :uid")->execute(['pid' => $customerId, 'uid' => $userId]);
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

            // Automatically post the invoice unconditionally
            Auth::requirePermission('invoices.post');
            $chequeInfo = [
                'cheque_number' => $_POST['cheque_number'] ?? '',
                'bank_name' => $_POST['cheque_bank'] ?? '',
                'cheque_date' => $_POST['cheque_date'] ?? date('Y-m-d')
            ];
            InvoiceEngine::postInvoice($invoiceId, $chequeInfo);
            Session::setFlash('success', 'Invoice generated and posted successfully.');

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

    public function recordCancelled(): void {
        Auth::requirePermission('invoices.create');
        $this->validateCsrf();

        $invoiceNumber = trim($_POST['invoice_number'] ?? '');
        $reason = trim($_POST['reason'] ?? 'Physically voided');

        if (empty($invoiceNumber)) {
            Session::setFlash('error', 'Invoice number is required.');
            Helper::redirect('modules/invoices');
        }

        $db = \Core\Database::getInstance();
        
        // Check if invoice number already exists
        $exists = $db->query("SELECT id FROM invoices WHERE invoice_number = " . $db->quote($invoiceNumber))->fetchColumn();
        if ($exists) {
            Session::setFlash('error', "Invoice number {$invoiceNumber} already exists in the system.");
            Helper::redirect('modules/invoices');
        }

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("
                INSERT INTO invoices (
                    invoice_number, customer_id, invoice_date, status, payment_type, 
                    subtotal, discount, tax_amount, total, created_by, reversal_reason
                ) VALUES (
                    :invoice_number, :customer_id, :invoice_date, 'CANCELLED', 'CASH',
                    0.00, 0.00, 0.00, 0.00, :created_by, :reversal_reason
                )
            ");
            
            // Assign to a default walk-in customer if available, or just first customer, or null
            // Table might require customer_id. Let's find first customer.
            $customerId = (int)$db->query("SELECT id FROM parties WHERE party_type IN ('CUSTOMER','BOTH') LIMIT 1")->fetchColumn();
            if (!$customerId) $customerId = 1;

            $stmt->execute([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customerId,
                'invoice_date' => date('Y-m-d'),
                'created_by' => Auth::id(),
                'reversal_reason' => $reason
            ]);

            $db->commit();
            Session::setFlash('success', "Invoice {$invoiceNumber} successfully recorded as Cancelled.");
        } catch (\Exception $e) {
            $db->rollBack();
            Session::setFlash('error', 'Failed to record cancelled invoice: ' . $e->getMessage());
        }

        Helper::redirect('modules/invoices');
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

    public function delete(): void {
        Auth::requirePermission('invoices.cancel');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            \Core\Helper::redirect('modules/invoices');
        }

        try {
            $db = \Core\Database::getInstance();
            $invoice = $db->query("SELECT * FROM invoices WHERE id = $id")->fetch();
            if ($invoice) {
                // Delete items
                $db->exec("DELETE FROM invoice_items WHERE invoice_id = $id");
                
                // Revert balances and journals if POSTED
                if ($invoice['status'] === 'POSTED') {
                    if ($invoice['payment_type'] === 'CASH' && $invoice['cash_account_id']) {
                        $db->exec("UPDATE cash_accounts SET current_balance = current_balance - {$invoice['total']} WHERE id = {$invoice['cash_account_id']}");
                    } elseif ($invoice['payment_type'] === 'BANK' && $invoice['bank_account_id']) {
                        $db->exec("UPDATE bank_accounts SET current_balance = current_balance - {$invoice['total']} WHERE id = {$invoice['bank_account_id']}");
                    }
                    
                    $journal = $db->query("SELECT id FROM journal_entries WHERE source_module = 'invoices' AND source_transaction_id = $id")->fetch();
                    if ($journal) {
                        $db->exec("DELETE FROM journal_lines WHERE journal_entry_id = {$journal['id']}");
                        $db->exec("DELETE FROM journal_entries WHERE id = {$journal['id']}");
                    }
                }
                
                $db->exec("DELETE FROM invoices WHERE id = $id");
                
                // Reset auto-increment
                $count = $db->query("SELECT COUNT(*) FROM invoices")->fetchColumn();
                if ($count == 0) {
                    $db->exec("ALTER TABLE invoices AUTO_INCREMENT = 1");
                }
                
                \Core\Session::setFlash('success', 'Invoice permanently deleted.');
            }
        } catch (\Exception $e) {
            \Core\Session::setFlash('error', 'Delete failed: ' . $e->getMessage());
        }

        \Core\Helper::redirect('modules/invoices');
    }
}
