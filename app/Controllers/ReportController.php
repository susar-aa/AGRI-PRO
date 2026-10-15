<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Helper;
use Core\Session;
use Core\Database;
use App\Models\ReportModel;
use App\Models\Journal;
use App\Models\Account;
use App\Models\CostCenter;
use App\Models\Party;

class ReportController extends Controller {

    private ReportModel $reportModel;
    private Journal $journalModel;
    private Account $accountModel;
    private CostCenter $costCenterModel;
    private Party $partyModel;

    public function __construct() {
        $this->reportModel = new ReportModel();
        $this->journalModel = new Journal();
        $this->accountModel = new Account();
        $this->costCenterModel = new CostCenter();
        $this->partyModel = new Party();
    }

    /**
     * Reports Hub / Master Index
     */
    public function index(): void {
        Auth::requirePermission('parties.view');

        $stats = $this->reportModel->getSummaryStats();

        $this->render('reports/index', [
            'pageTitle' => 'Central Reports & Analytics Hub',
            'activeNav' => 'reports_hub',
            'stats' => $stats
        ]);
    }

    /**
     * Profit & Loss Statement (Income Statement)
     */
    public function profitLoss(): void {
        Auth::requirePermission('parties.view');

        $fromDate = $_GET['from_date'] ?? date('Y-01-01');
        $toDate = $_GET['to_date'] ?? date('Y-m-d');
        $costCenterId = !empty($_GET['cost_center_id']) ? (int)$_GET['cost_center_id'] : null;

        $pnl = $this->reportModel->getProfitAndLoss($fromDate, $toDate, $costCenterId);
        $costCenters = $this->costCenterModel->getActive();

        $this->render('reports/profit_loss', [
            'pageTitle' => 'Profit & Loss Statement (Income Statement)',
            'activeNav' => 'reports_pnl',
            'pnl' => $pnl,
            'costCenters' => $costCenters,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'selectedCostCenterId' => $costCenterId
        ]);
    }

    /**
     * Balance Sheet Statement (Financial Position)
     */
    public function balanceSheet(): void {
        Auth::requirePermission('parties.view');

        $asOfDate = $_GET['as_of_date'] ?? date('Y-m-d');
        $balanceSheet = $this->reportModel->getBalanceSheet($asOfDate);

        $this->render('reports/balance_sheet', [
            'pageTitle' => 'Balance Sheet (Statement of Financial Position)',
            'activeNav' => 'reports_balance_sheet',
            'balanceSheet' => $balanceSheet,
            'asOfDate' => $asOfDate
        ]);
    }

    /**
     * Trial Balance Statement
     */
    public function trialBalance(): void {
        Auth::requirePermission('parties.view');

        $fromDate = $_GET['from_date'] ?? '';
        $toDate = $_GET['to_date'] ?? date('Y-m-d');
        $costCenterId = !empty($_GET['cost_center_id']) ? (int)$_GET['cost_center_id'] : null;
        $showZero = !empty($_GET['show_zero']);

        $trialBalance = $this->journalModel->getTrialBalance($toDate, $fromDate, $costCenterId, $showZero);
        $costCenters = $this->costCenterModel->getActive();

        $this->render('reports/trial_balance', [
            'pageTitle' => 'Trial Balance Statement',
            'activeNav' => 'reports_trial_balance',
            'trialBalance' => $trialBalance,
            'costCenters' => $costCenters,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'selectedCostCenterId' => $costCenterId,
            'showZero' => $showZero
        ]);
    }

    /**
     * Cash & Bank Book Detailed Ledger
     */
    public function cashBankBook(): void {
        Auth::requirePermission('parties.view');

        $fromDate = $_GET['from_date'] ?? date('Y-m-01');
        $toDate = $_GET['to_date'] ?? date('Y-m-d');
        $accountId = !empty($_GET['account_id']) ? (int)$_GET['account_id'] : null;

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $entries = $this->reportModel->getCashBankBook($fromDate, $toDate, $accountId, $limit, $offset);
        $totalItems = $this->reportModel->getCashBankBookCount($fromDate, $toDate, $accountId);
        $totalPages = ceil($totalItems / $limit);

        // Fetch Cash and Bank Accounts list for dropdown filter
        $db = Database::getInstance();
        $accounts = $db->query("
            SELECT id, account_code, account_name 
            FROM accounts 
            WHERE account_code IN ('1110', '1120', '1115', '1130') AND deleted_at IS NULL
            ORDER BY account_code ASC
        ")->fetchAll();

        $this->render('reports/cash_bank_book', [
            'pageTitle' => 'Cash & Bank Book Ledger',
            'activeNav' => 'reports_cash_bank',
            'entries' => $entries,
            'accounts' => $accounts,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'selectedAccountId' => $accountId,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    /**
     * Sales Summary & Breakdown Report
     */
    public function salesSummary(): void {
        Auth::requirePermission('parties.view');

        $filters = [
            'from_date' => $_GET['from_date'] ?? date('Y-m-01'),
            'to_date' => $_GET['to_date'] ?? date('Y-m-d'),
            'customer_id' => $_GET['customer_id'] ?? '',
            'payment_type' => $_GET['payment_type'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $sales = $this->reportModel->getSalesSummary($filters, $limit, $offset);
        $totalItems = $this->reportModel->getSalesSummaryCount($filters);
        $totalPages = ceil($totalItems / $limit);
        $aggregates = $this->reportModel->getSalesSummaryAggregates($filters);

        // Customers list for dropdown filter
        $customers = $this->partyModel->getAll(['party_type' => 'CUSTOMER', 'status' => 'active'], 200);

        $this->render('reports/sales', [
            'pageTitle' => 'Sales & Revenue Summary Report',
            'activeNav' => 'reports_sales',
            'sales' => $sales,
            'aggregates' => $aggregates,
            'customers' => $customers,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    /**
     * Accounts Receivable (AR) Aging & Customer Outstanding
     */
    public function arAging(): void {
        Auth::requirePermission('parties.view');

        $filters = [
            'search' => trim($_GET['search'] ?? '')
        ];

        $agingRecords = $this->reportModel->getArAgingReport($filters);

        $totals = [
            'total_receivable' => array_sum(array_column($agingRecords, 'total_receivable')),
            'current_0_30' => array_sum(array_column($agingRecords, 'current_0_30')),
            'days_31_60' => array_sum(array_column($agingRecords, 'days_31_60')),
            'days_61_90' => array_sum(array_column($agingRecords, 'days_61_90')),
            'days_over_90' => array_sum(array_column($agingRecords, 'days_over_90'))
        ];

        $this->render('reports/ar_aging', [
            'pageTitle' => 'Accounts Receivable (AR) Aging Report',
            'activeNav' => 'reports_ar_aging',
            'records' => $agingRecords,
            'totals' => $totals,
            'filters' => $filters
        ]);
    }

    /**
     * Purchases & GRN Summary Report
     */
    public function purchasesSummary(): void {
        Auth::requirePermission('parties.view');

        $filters = [
            'from_date' => $_GET['from_date'] ?? date('Y-m-01'),
            'to_date' => $_GET['to_date'] ?? date('Y-m-d'),
            'supplier_id' => $_GET['supplier_id'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $purchases = $this->reportModel->getPurchasesSummary($filters, $limit, $offset);
        $totalItems = $this->reportModel->getPurchasesSummaryCount($filters);
        $totalPages = ceil($totalItems / $limit);

        // Fetch Suppliers for dropdown
        $suppliers = $this->partyModel->getAll(['party_type' => 'SUPPLIER', 'status' => 'active'], 200);

        $this->render('reports/purchases', [
            'pageTitle' => 'Purchases & Goods Receipt (GRN) Report',
            'activeNav' => 'reports_purchases',
            'purchases' => $purchases,
            'suppliers' => $suppliers,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    /**
     * Accounts Payable (AP) Aging & Supplier Balances
     */
    public function apAging(): void {
        Auth::requirePermission('parties.view');

        $filters = [
            'search' => trim($_GET['search'] ?? '')
        ];

        $agingRecords = $this->reportModel->getApAgingReport($filters);

        $totals = [
            'total_payable' => array_sum(array_column($agingRecords, 'total_payable')),
            'current_0_30' => array_sum(array_column($agingRecords, 'current_0_30')),
            'days_31_60' => array_sum(array_column($agingRecords, 'days_31_60')),
            'days_61_90' => array_sum(array_column($agingRecords, 'days_61_90')),
            'days_over_90' => array_sum(array_column($agingRecords, 'days_over_90'))
        ];

        $this->render('reports/ap_aging', [
            'pageTitle' => 'Accounts Payable (AP) Aging Report',
            'activeNav' => 'reports_ap_aging',
            'records' => $agingRecords,
            'totals' => $totals,
            'filters' => $filters
        ]);
    }

    /**
     * Expense Analysis & Category Distribution
     */
    public function expensesAnalysis(): void {
        Auth::requirePermission('parties.view');

        $fromDate = $_GET['from_date'] ?? date('Y-01-01');
        $toDate = $_GET['to_date'] ?? date('Y-m-d');
        $costCenterId = !empty($_GET['cost_center_id']) ? (int)$_GET['cost_center_id'] : null;

        $expenseData = $this->reportModel->getExpenseAnalysis($fromDate, $toDate, $costCenterId);
        $costCenters = $this->costCenterModel->getActive();

        $this->render('reports/expenses', [
            'pageTitle' => 'Operating Expense Analysis & Distribution',
            'activeNav' => 'reports_expenses',
            'expenseData' => $expenseData,
            'costCenters' => $costCenters,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'selectedCostCenterId' => $costCenterId
        ]);
    }

    /**
     * Stock Valuation & Inventory Summary
     */
    public function inventoryValuation(): void {
        Auth::requirePermission('parties.view');

        $locationId = !empty($_GET['location_id']) ? (int)$_GET['location_id'] : null;
        $categoryId = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;

        $items = $this->reportModel->getInventoryValuation($locationId, $categoryId);

        $db = Database::getInstance();
        $warehouses = $db->query("SELECT id, name FROM inventory_locations WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        $categories = $db->query("SELECT id, name FROM product_categories ORDER BY name ASC")->fetchAll();

        $totalValuation = array_sum(array_column($items, 'inventory_value'));
        $totalQuantity = array_sum(array_column($items, 'quantity_on_hand'));

        $this->render('reports/inventory_valuation', [
            'pageTitle' => 'Stock Valuation & Inventory Summary',
            'activeNav' => 'reports_inventory_valuation',
            'items' => $items,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'selectedLocationId' => $locationId,
            'selectedCategoryId' => $categoryId,
            'totalValuation' => $totalValuation,
            'totalQuantity' => $totalQuantity
        ]);
    }

    /**
     * Stock Movement Audit Trail / Stock Ledger
     */
    public function stockMovement(): void {
        Auth::requirePermission('parties.view');

        $filters = [
            'from_date' => $_GET['from_date'] ?? date('Y-m-01'),
            'to_date' => $_GET['to_date'] ?? date('Y-m-d'),
            'product_id' => $_GET['product_id'] ?? '',
            'location_id' => $_GET['location_id'] ?? '',
            'movement_type' => $_GET['movement_type'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $movements = $this->reportModel->getStockMovementReport($filters, $limit, $offset);
        $totalItems = $this->reportModel->getStockMovementReportCount($filters);
        $totalPages = ceil($totalItems / $limit);

        $db = Database::getInstance();
        $products = $db->query("SELECT id, product_code, name_en FROM products WHERE is_active = 1 ORDER BY name_en ASC")->fetchAll();
        $warehouses = $db->query("SELECT id, name FROM inventory_locations WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

        $this->render('reports/stock_movement', [
            'pageTitle' => 'Stock Movement Audit Trail (Stock Ledger)',
            'activeNav' => 'reports_stock_movement',
            'movements' => $movements,
            'products' => $products,
            'warehouses' => $warehouses,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    /**
     * Fixed Deposits Portfolio Report
     */
    public function fixedDepositsReport(): void {
        Auth::requirePermission('parties.view');

        $filters = [
            'status' => $_GET['status'] ?? '',
            'member_id' => $_GET['member_id'] ?? ''
        ];

        $fds = $this->reportModel->getFixedDepositsReport($filters);

        $totals = [
            'total_principal' => array_sum(array_column($fds, 'principal_amount')),
            'total_expected_interest' => array_sum(array_column($fds, 'expected_interest')),
            'total_maturity_amount' => array_sum(array_column($fds, 'maturity_amount')),
            'active_count' => count(array_filter($fds, fn($item) => $item['status'] === 'ACTIVE')),
            'matured_count' => count(array_filter($fds, fn($item) => $item['status'] === 'MATURED'))
        ];

        $db = Database::getInstance();
        $members = $db->query("SELECT id, member_no, full_name FROM coop_members WHERE status = 'ACTIVE' ORDER BY full_name ASC")->fetchAll();

        $this->render('reports/fixed_deposits', [
            'pageTitle' => 'Fixed Deposits Portfolio Report',
            'activeNav' => 'reports_fixed_deposits',
            'fds' => $fds,
            'totals' => $totals,
            'members' => $members,
            'filters' => $filters
        ]);
    }

    /**
     * Machinery & Equipment Utilization Report
     */
    public function machineryUtilization(): void {
        Auth::requirePermission('parties.view');

        $filters = [
            'status' => $_GET['status'] ?? ''
        ];

        $records = $this->reportModel->getMachineryUtilizationReport($filters);

        $totals = [
            'total_machinery' => count($records),
            'total_rentals' => array_sum(array_column($records, 'total_rentals')),
            'total_revenue' => array_sum(array_column($records, 'total_revenue'))
        ];

        $this->render('reports/machinery_utilization', [
            'pageTitle' => 'Machinery & Equipment Rental Utilization',
            'activeNav' => 'reports_machinery',
            'records' => $records,
            'totals' => $totals,
            'filters' => $filters
        ]);
    }

    /**
     * Cost Centers & Divisional Performance Report
     */
    public function costCentersPerformance(): void {
        Auth::requirePermission('parties.view');

        $fromDate = $_GET['from_date'] ?? date('Y-01-01');
        $toDate = $_GET['to_date'] ?? date('Y-m-d');

        $divisions = $this->reportModel->getCostCentersPerformance($fromDate, $toDate);

        $totals = [
            'total_revenue' => array_sum(array_column($divisions, 'total_revenue')),
            'total_expense' => array_sum(array_column($divisions, 'total_expense')),
            'net_margin' => array_sum(array_column($divisions, 'net_margin'))
        ];

        $this->render('reports/cost_centers', [
            'pageTitle' => 'Operational Cost Centers & Divisions Performance',
            'activeNav' => 'reports_cost_centers',
            'divisions' => $divisions,
            'totals' => $totals,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);
    }

    /**
     * Universal Excel Exporter (UTF-8 BOM support for Sinhala & special characters)
     */
    public function exportGeneric(): void {
        Auth::requirePermission('parties.view');

        $reportType = $_GET['type'] ?? 'report';
        $filename = 'AgriPro_' . ucfirst($reportType) . '_' . date('Ymd_His') . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $companyConfig = file_exists(__DIR__ . '/../../config/company.php') ? require __DIR__ . '/../../config/company.php' : [];
        $companyName = $companyConfig['company_name'] ?? 'Agri Co-Op Cooperative Society Limited';

        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr style='background-color:#1e3d2f;color:#ffffff;'><th colspan='6' style='font-size:16px;text-align:center;'>{$companyName}</th></tr>";
        echo "<tr style='background-color:#f4f6f8;'><th colspan='6' style='text-align:center;'>Report: " . strtoupper($reportType) . " | Generated: " . date('Y-m-d H:i:s') . "</th></tr>";
        echo "</table><br>";

        // Fetch data based on report type
        if ($reportType === 'profit_loss') {
            $pnl = $this->reportModel->getProfitAndLoss($_GET['from_date'] ?? null, $_GET['to_date'] ?? null);
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr style='background-color:#e8f4ec;'><th>Code</th><th>Account Name</th><th>Category</th><th>Amount (LKR)</th></tr>";
            foreach ($pnl['revenues'] as $r) {
                echo "<tr><td>{$r['account_code']}</td><td>{$r['account_name']}</td><td>Revenue</td><td style='text-align:right;'>" . number_format($r['net_amount'], 2) . "</td></tr>";
            }
            echo "<tr style='background-color:#d4edda;font-weight:bold;'><td colspan='3'>Total Revenue</td><td style='text-align:right;'>" . number_format($pnl['total_revenue'], 2) . "</td></tr>";
            foreach ($pnl['cogs'] as $c) {
                echo "<tr><td>{$c['account_code']}</td><td>{$c['account_name']}</td><td>COGS</td><td style='text-align:right;'>" . number_format($c['net_amount'], 2) . "</td></tr>";
            }
            echo "<tr style='background-color:#d1ecf1;font-weight:bold;'><td colspan='3'>Gross Profit</td><td style='text-align:right;'>" . number_format($pnl['gross_profit'], 2) . "</td></tr>";
            foreach ($pnl['expenses'] as $e) {
                echo "<tr><td>{$e['account_code']}</td><td>{$e['account_name']}</td><td>Expense</td><td style='text-align:right;'>" . number_format($e['net_amount'], 2) . "</td></tr>";
            }
            echo "<tr style='background-color:#f8d7da;font-weight:bold;'><td colspan='3'>Total Expenses</td><td style='text-align:right;'>" . number_format($pnl['total_expenses'], 2) . "</td></tr>";
            echo "<tr style='background-color:#fff3cd;font-weight:bold;font-size:14px;'><td colspan='3'>NET OPERATING SURPLUS / (DEFICIT)</td><td style='text-align:right;'>" . number_format($pnl['net_profit'], 2) . "</td></tr>";
            echo "</table>";
        } elseif ($reportType === 'balance_sheet') {
            $bs = $this->reportModel->getBalanceSheet($_GET['as_of_date'] ?? null);
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr style='background-color:#e8f4ec;'><th>Code</th><th>Account Name</th><th>Category</th><th>Balance (LKR)</th></tr>";
            echo "<tr style='background-color:#cfe2ff;font-weight:bold;'><td colspan='4'>ASSETS</td></tr>";
            foreach ($bs['assets'] as $a) {
                echo "<tr><td>{$a['account_code']}</td><td>{$a['account_name']}</td><td>Asset</td><td style='text-align:right;'>" . number_format($a['net_balance'], 2) . "</td></tr>";
            }
            echo "<tr style='background-color:#b6d4fe;font-weight:bold;'><td colspan='3'>Total Assets</td><td style='text-align:right;'>" . number_format($bs['total_assets'], 2) . "</td></tr>";
            echo "<tr style='background-color:#f8d7da;font-weight:bold;'><td colspan='4'>LIABILITIES</td></tr>";
            foreach ($bs['liabilities'] as $l) {
                echo "<tr><td>{$l['account_code']}</td><td>{$l['account_name']}</td><td>Liability</td><td style='text-align:right;'>" . number_format($l['net_balance'], 2) . "</td></tr>";
            }
            echo "<tr style='background-color:#f1aeb5;font-weight:bold;'><td colspan='3'>Total Liabilities</td><td style='text-align:right;'>" . number_format($bs['total_liabilities'], 2) . "</td></tr>";
            echo "<tr style='background-color:#d1e7dd;font-weight:bold;'><td colspan='4'>EQUITY & RESERVES</td></tr>";
            foreach ($bs['equity'] as $eq) {
                echo "<tr><td>{$eq['account_code']}</td><td>{$eq['account_name']}</td><td>Equity</td><td style='text-align:right;'>" . number_format($eq['net_balance'], 2) . "</td></tr>";
            }
            echo "<tr><td colspan='3'>Current Year Surplus / (Deficit)</td><td style='text-align:right;'>" . number_format($bs['current_earnings'], 2) . "</td></tr>";
            echo "<tr style='background-color:#a3cfbb;font-weight:bold;'><td colspan='3'>Total Equity</td><td style='text-align:right;'>" . number_format($bs['total_equity'], 2) . "</td></tr>";
            echo "<tr style='background-color:#fff3cd;font-weight:bold;'><td colspan='3'>TOTAL LIABILITIES & EQUITY</td><td style='text-align:right;'>" . number_format($bs['total_liabilities_and_equity'], 2) . "</td></tr>";
            echo "</table>";
        } else {
            echo "<p>Export generated successfully for " . htmlspecialchars($reportType) . ".</p>";
        }
        exit;
    }

    /**
     * Existing Directory Report Methods
     */
    public function directory(): void {
        Auth::requirePermission('parties.view');

        $filters = [
            'type' => strtolower(trim($_GET['type'] ?? 'all')),
            'search' => trim($_GET['search'] ?? ''),
            'status' => strtolower(trim($_GET['status'] ?? 'all'))
        ];

        $records = $this->reportModel->getDirectoryReport($filters);
        $counts = $this->reportModel->getDirectoryCounts($filters);

        $this->render('reports/directory', [
            'pageTitle' => 'Central Directory Report',
            'activeNav' => 'reports_directory',
            'records' => $records,
            'counts' => $counts,
            'filters' => $filters
        ]);
    }

    public function exportDirectory(): void {
        Auth::requirePermission('parties.view');

        $filters = [
            'type' => strtolower(trim($_GET['type'] ?? 'all')),
            'search' => trim($_GET['search'] ?? ''),
            'status' => strtolower(trim($_GET['status'] ?? 'all'))
        ];
        $format = strtolower(trim($_GET['format'] ?? 'excel'));

        $records = $this->reportModel->getDirectoryReport($filters);
        
        $companyConfig = file_exists(__DIR__ . '/../../config/company.php')
            ? require __DIR__ . '/../../config/company.php'
            : [];

        $currentUser = Auth::user();
        $generatedBy = $currentUser['full_name'] ?? Session::get('full_name') ?? 'User';
        $generatedAt = date('Y-m-d H:i:s');

        if ($format === 'pdf') {
            $this->renderAuthView('reports/directory_pdf', [
                'pageTitle' => 'Directory Report Print - Agri Co-Op ERP',
                'records' => $records,
                'filters' => $filters,
                'company' => $companyConfig,
                'generatedBy' => $generatedBy,
                'generatedAt' => $generatedAt
            ]);
            exit;
        }

        $filename = 'Directory_Report_' . ($filters['type'] ?: 'All') . '_' . date('Ymd_His') . '.xls';
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        echo "\xEF\xBB\xBF";

        $companyName = $companyConfig['company_name'] ?? 'Agri Co-Op Cooperative Society Limited';
        echo "<table border='1' cellpadding='4' cellspacing='0'>";
        echo "<tr style='background-color:#1e3d2f;color:#ffffff;'><th colspan='7' style='text-align:center;'>{$companyName} - Directory Report</th></tr>";
        echo "<tr style='background-color:#e8f4ec;'><th>Type</th><th>Code/Reg #</th><th>Name</th><th>Phone</th><th>NIC</th><th>Address</th><th>Status</th></tr>";
        foreach ($records as $r) {
            echo "<tr><td>{$r['entity_type']}</td><td>{$r['code']}</td><td>{$r['name']}</td><td>{$r['phone']}</td><td>{$r['nic']}</td><td>{$r['address']}</td><td>" . strtoupper($r['status']) . "</td></tr>";
        }
        echo "</table>";
        exit;
    }
}
