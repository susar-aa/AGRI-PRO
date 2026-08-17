<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\ProductModel;
use App\Models\SaleModel;
use App\Models\Party;
use App\Models\Expense; // for cash/bank accounts lists
use App\Services\SalesEngine;
use App\Services\InventoryEngine;

class MarketplaceController extends Controller {
    private ProductModel $productModel;
    private SaleModel $saleModel;
    private Party $partyModel;
    private Expense $expenseModel;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->saleModel = new SaleModel();
        $this->partyModel = new Party();
        $this->expenseModel = new Expense();
    }

    public function dashboard(): void {
        Auth::requirePermission('marketplace.view');

        $db = \Core\Database::getInstance();

        // 1. Today's sales count
        $todaySalesCount = (int)$db->query("SELECT COUNT(*) FROM sales WHERE sale_date = CURDATE() AND status = 'POSTED'")->fetchColumn();

        // 2. Today's revenue
        $todayRevenue = (float)$db->query("SELECT COALESCE(SUM(total), 0.00) FROM sales WHERE sale_date = CURDATE() AND status = 'POSTED'")->fetchColumn();

        // 3. Current marketplace stock value (sum of hand qty * avg cost for products with is_marketplace = 1)
        $mktStockVal = (float)$db->query("
            SELECT COALESCE(SUM(ib.inventory_value), 0.00)
            FROM inventory_balances ib
            JOIN products p ON ib.product_id = p.id
            WHERE p.is_marketplace = 1
        ")->fetchColumn();

        // 4. Recent sales
        $recentSales = $this->saleModel->getAll([], 5, 0);

        $this->render('marketplace/dashboard', [
            'pageTitle' => 'Marketplace Dashboard',
            'activeNav' => 'marketplace',
            'todaySalesCount' => $todaySalesCount,
            'todayRevenue' => $todayRevenue,
            'currentStockVal' => $mktStockVal,
            'recentSales' => $recentSales
        ]);
    }

    public function products(): void {
        Auth::requirePermission('marketplace.products');

        $filters = [
            'search' => trim($_GET['search'] ?? '')
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $products = $this->productModel->getAll($filters, $limit, $offset);
        $totalItems = $this->productModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        // Fetch warehouses/locations list to show available stock per warehouse
        $db = \Core\Database::getInstance();
        $warehouses = $db->query("SELECT id, name FROM inventory_locations WHERE is_active = 1 OR 1=1 ORDER BY name ASC")->fetchAll();
        $categories = $db->query("SELECT id, name FROM product_categories ORDER BY name ASC")->fetchAll();
        $units = $db->query("SELECT id, code, name FROM units_of_measure ORDER BY name ASC")->fetchAll();

        foreach ($products as &$p) {
            $p['stock'] = [];
            foreach ($warehouses as $w) {
                $p['stock'][$w['name']] = InventoryEngine::getStockOnHand((int)$p['id'], (int)$w['id']);
            }
        }

        $this->render('marketplace/products', [
            'pageTitle' => 'Marketplace Products Management',
            'activeNav' => 'marketplace_products',
            'products' => $products,
            'filters' => $filters,
            'categories' => $categories,
            'units' => $units,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function storeProduct(): void {
        Auth::requirePermission('marketplace.products');
        $this->validateCsrf();

        $productCode = trim($_POST['product_code'] ?? '');
        $data = [
            'sku' => $productCode,
            'product_code' => $productCode,
            'name_en' => trim($_POST['name_en'] ?? ''),
            'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'product_type' => $_POST['product_type'] ?? 'TRADING',
            'base_unit_id' => !empty($_POST['base_unit_id']) ? (int)$_POST['base_unit_id'] : 0,
            'default_purchase_price' => (float)($_POST['default_purchase_price'] ?? 0),
            'default_selling_price' => (float)($_POST['default_selling_price'] ?? 0),
            'is_marketplace' => !empty($_POST['is_marketplace']) ? 1 : 0,
            'source_module' => $_POST['source_module'] ?? 'PURCHASE',
            'created_by' => 1
        ];

        if (empty($data['product_code']) || empty($data['name_en']) || !$data['base_unit_id']) {
            \Core\Session::setFlash('error', 'Product code, name, and unit are required.');
            Helper::redirect('modules/marketplace/products');
        }

        try {
            $this->productModel->create($data);
            Session::setFlash('success', 'Product created successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to create product: ' . $e->getMessage());
        }

        Helper::redirect('modules/marketplace/products');
    }

    public function toggleProduct(): void {
        Auth::requirePermission('marketplace.products');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $status = !empty($_POST['status']) ? true : false;

        try {
            $this->productModel->toggleMarketplace($id, $status);
            Session::setFlash('success', 'Product marketplace status updated successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Update failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/marketplace/products');
    }

    public function updateProductPrices(): void {
        Auth::requirePermission('marketplace.products');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $purchasePrice = (float)($_POST['default_purchase_price'] ?? 0);
        $sellingPrice = (float)($_POST['default_selling_price'] ?? 0);
        $sourceModule = $_POST['source_module'] ?? 'PURCHASE';
        $sourceTransactionId = !empty($_POST['source_transaction_id']) ? (int)$_POST['source_transaction_id'] : null;

        try {
            $this->productModel->updatePrices($id, $purchasePrice, $sellingPrice);
            $this->productModel->updateSourceInfo($id, $sourceModule, $sourceTransactionId);
            Session::setFlash('success', 'Marketplace product settings updated successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Update failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/marketplace/products');
    }

    public function salesIndex(): void {
        Auth::requirePermission('marketplace.sales.view');

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

        $sales = $this->saleModel->getAll($filters, $limit, $offset);
        $totalItems = $this->saleModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        // Fetch active customers
        $db = \Core\Database::getInstance();
        $customers = $db->query("SELECT id, party_code, name FROM parties WHERE party_type IN ('CUSTOMER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();

        $this->render('marketplace/sales_index', [
            'pageTitle' => 'Marketplace Sales Registry',
            'activeNav' => 'marketplace_sales',
            'sales' => $sales,
            'filters' => $filters,
            'customers' => $customers,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function salesView(): void {
        Auth::requirePermission('marketplace.sales.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $sale = $this->saleModel->getById($id);

        if (!$sale) {
            Session::setFlash('error', 'Sale invoice not found.');
            Helper::redirect('modules/marketplace/sales');
        }

        // Calculate COGS and Gross Profit for posted sales
        $totalCogs = 0.00;
        if ($sale['status'] === 'POSTED') {
            $db = \Core\Database::getInstance();
            // Fetch total cogs recorded in Stock Ledger for this sale
            $stmt = $db->prepare("SELECT SUM(total_cost) FROM stock_ledger WHERE source_module = 'MARKETPLACE' AND source_transaction_id = :sale_id AND quantity_out > 0");
            $stmt->execute(['sale_id' => $id]);
            $totalCogs = (float)$stmt->fetchColumn();
        }

        $grossProfit = $sale['total'] - $totalCogs;

        $this->render('marketplace/sales_view', [
            'pageTitle' => 'Sale Invoice #' . $sale['sale_number'],
            'activeNav' => 'marketplace_sales',
            'sale' => $sale,
            'totalCogs' => $totalCogs,
            'grossProfit' => $grossProfit
        ]);
    }

    public function salesCreate(): void {
        Auth::requirePermission('marketplace.sales.create');

        $db = \Core\Database::getInstance();

        // 1. Fetch active customers
        $customers = $db->query("SELECT id, party_code, name FROM parties WHERE party_type IN ('CUSTOMER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();

        // 2. Fetch warehouses
        $warehouses = $db->query("SELECT id, code, name FROM inventory_locations WHERE is_active = 1 OR 1=1 ORDER BY name ASC")->fetchAll();

        // 3. Fetch cash & bank accounts
        $cashAccounts = $this->expenseModel->getCashAccounts();
        $bankAccounts = $this->expenseModel->getBankAccounts();

        // 4. Fetch products marked as available in Marketplace
        $products = $db->query("
            SELECT p.*, pc.name AS category_name, u.code AS unit_code
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            LEFT JOIN units_of_measure u ON p.sales_unit_id = u.id
            WHERE p.is_marketplace = 1 AND p.is_active = 1
            ORDER BY p.name_en ASC
        ")->fetchAll();

        // Add available stock info for each product
        foreach ($products as &$p) {
            $p['stocks'] = [];
            foreach ($warehouses as $wh) {
                $p['stocks'][$wh['id']] = InventoryEngine::getStockOnHand((int)$p['id'], (int)$wh['id']);
            }
        }

        $this->render('marketplace/sales_create', [
            'pageTitle' => 'New Marketplace Sale Invoice',
            'activeNav' => 'marketplace_sales',
            'customers' => $customers,
            'warehouses' => $warehouses,
            'cashAccounts' => $cashAccounts,
            'bankAccounts' => $bankAccounts,
            'products' => $products
        ]);
    }

    public function salesStore(): void {
        Auth::requirePermission('marketplace.sales.create');
        $this->validateCsrf();

        // Compile items list from post arrays
        $items = [];
        if (!empty($_POST['items'])) {
            foreach ($_POST['items'] as $it) {
                if (!empty($it['product_id']) && (float)($it['quantity'] ?? 0) > 0) {
                    $items[] = [
                        'product_id' => (int)$it['product_id'],
                        'quantity' => (float)$it['quantity'],
                        'unit_price' => (float)($it['unit_price'] ?? 0),
                        'discount' => (float)($it['discount'] ?? 0)
                    ];
                }
            }
        }

        $data = [
            'id' => !empty($_POST['id']) ? (int)$_POST['id'] : null,
            'customer_id' => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0,
            'sale_date' => $_POST['sale_date'] ?? date('Y-m-d'),
            'sale_type' => $_POST['sale_type'] ?? 'CASH',
            'payment_method' => $_POST['payment_method'] ?? 'CASH',
            'warehouse_id' => !empty($_POST['warehouse_id']) ? (int)$_POST['warehouse_id'] : 0,
            'cash_account_id' => !empty($_POST['cash_account_id']) ? (int)$_POST['cash_account_id'] : null,
            'bank_account_id' => !empty($_POST['bank_account_id']) ? (int)$_POST['bank_account_id'] : null,
            'discount' => (float)($_POST['discount'] ?? 0),
            'notes' => trim($_POST['notes'] ?? ''),
            'items' => $items
        ];

        try {
            $saleId = SalesEngine::saveSale($data);

            if (!empty($_POST['action']) && $_POST['action'] === 'post') {
                Auth::requirePermission('marketplace.sales.post');
                $chequeInfo = [
                    'cheque_number' => $_POST['cheque_number'] ?? '',
                    'bank_name' => $_POST['cheque_bank'] ?? '',
                    'cheque_date' => $_POST['cheque_date'] ?? date('Y-m-d')
                ];
                SalesEngine::postSale($saleId, $chequeInfo);
                Session::setFlash('success', 'Sale Invoice created and posted successfully.');
            } else {
                Session::setFlash('success', 'Sale Invoice recorded as Draft.');
            }

            Helper::redirect('modules/marketplace/sales/view?id=' . $saleId);

        } catch (\Exception $e) {
            Session::setFlash('error', 'Action failed: ' . $e->getMessage());
            Helper::redirect('modules/marketplace/sales/create');
        }
    }

    public function salesPost(): void {
        Auth::requirePermission('marketplace.sales.post');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $chequeInfo = [
            'cheque_number' => $_POST['cheque_number'] ?? '',
            'bank_name' => $_POST['cheque_bank'] ?? '',
            'cheque_date' => $_POST['cheque_date'] ?? date('Y-m-d')
        ];

        try {
            SalesEngine::postSale($id, $chequeInfo);
            Session::setFlash('success', 'Sale Invoice posted successfully. Stock reduced and ledger posted.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Posting failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/marketplace/sales/view?id=' . $id);
    }

    public function salesCancel(): void {
        Auth::requirePermission('marketplace.sales.cancel');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $reason = trim($_POST['reversal_reason'] ?? 'Invoice Cancelled');

        try {
            SalesEngine::cancelSale($id, $reason);
            Session::setFlash('success', 'Sale Invoice cancelled. Inventory stock replenished and ledger reversed.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Cancellation failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/marketplace/sales/view?id=' . $id);
    }
}
