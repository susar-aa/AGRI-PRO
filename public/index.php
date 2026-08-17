<?php
/**
 * Agri Co-Op ERP Front Controller Entry Point
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

use Core\Router;
use Core\Auth;
use Core\Helper;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\AccountController;
use App\Controllers\JournalController;
use App\Controllers\CostCenterController;
use App\Controllers\SettingsController;
use App\Controllers\CustomerActivityController;
use App\Controllers\ModuleController;
use App\Controllers\ExpenseController;
use App\Controllers\PartyController;
use App\Controllers\ReceiptPaymentController;
use App\Controllers\ChequeController;
use App\Controllers\DepositController;
use App\Controllers\MarketplaceController;
use App\Controllers\InvoiceController;
use App\Controllers\ServiceController;
use App\Controllers\ServiceJobController;
use App\Controllers\MachineryController;
use App\Controllers\MachineryRentalController;
use App\Controllers\OperationsController;
use App\Controllers\CashController;
use App\Controllers\BankController;
use App\Controllers\MemberController;
use App\Controllers\DirectorController;
use App\Controllers\FDController;

$router = new Router();

// Auth Routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Main Dashboard
$router->get('/', function() {
    if (!Auth::check()) {
        Helper::redirect('login');
    } else {
        Helper::redirect('dashboard');
    }
});
$router->get('/dashboard', [DashboardController::class, 'index']);

// Accounting Routes
$router->get('/accounting/coa', [AccountController::class, 'index']);
$router->post('/accounting/coa/store', [AccountController::class, 'store']);
$router->get('/accounting/journal-entries', [JournalController::class, 'index']);
$router->post('/accounting/journal-entries/store', [JournalController::class, 'store']);
$router->get('/accounting/journal-entries/view', [JournalController::class, 'view']);
$router->post('/accounting/journal-entries/submit', [JournalController::class, 'submit']);
$router->post('/accounting/journal-entries/approve', [JournalController::class, 'approve']);
$router->post('/accounting/journal-entries/post', [JournalController::class, 'post']);
$router->post('/accounting/journal-entries/reverse', [JournalController::class, 'reverse']);
$router->post('/accounting/journal-entries/cancel', [JournalController::class, 'cancel']);
$router->get('/accounting/general-ledger', [JournalController::class, 'generalLedger']);
$router->get('/accounting/trial-balance', [JournalController::class, 'trialBalance']);

// Organization / Admin
$router->get('/cost-centers', [CostCenterController::class, 'index']);
$router->post('/cost-centers/store', [CostCenterController::class, 'store']);
$router->get('/admin/company', [SettingsController::class, 'company']);
$router->get('/system/customer-activities', [CustomerActivityController::class, 'index']);
$router->post('/system/customer-activities/store', [CustomerActivityController::class, 'store']);

// Central Expense Engine Routes
$router->get('/expenses', [ExpenseController::class, 'index']);
$router->get('/expenses/view', [ExpenseController::class, 'view']);
$router->get('/expenses/create', [ExpenseController::class, 'create']);
$router->post('/expenses/store', [ExpenseController::class, 'store']);
$router->post('/expenses/submit', [ExpenseController::class, 'submit']);
$router->post('/expenses/approve', [ExpenseController::class, 'approve']);
$router->post('/expenses/post', [ExpenseController::class, 'post']);
$router->post('/expenses/reverse', [ExpenseController::class, 'reverse']);
$router->post('/expenses/cancel', [ExpenseController::class, 'cancel']);
$router->get('/expenses/reports', [ExpenseController::class, 'reports']);

// Central Party Management Routes (Stage 5A)
$router->get('/parties', [PartyController::class, 'index']);
$router->get('/parties/customers', [PartyController::class, 'customers']);
$router->get('/parties/suppliers', [PartyController::class, 'suppliers']);
$router->get('/parties/staff', [PartyController::class, 'staff']);
$router->get('/parties/view', [PartyController::class, 'view']);
$router->get('/parties/create', [PartyController::class, 'create']);
$router->post('/parties/store', [PartyController::class, 'store']);
$router->get('/parties/edit', [PartyController::class, 'edit']);
$router->post('/parties/update', [PartyController::class, 'update']);
$router->post('/parties/deactivate', [PartyController::class, 'deactivate']);

// Party Opening Balance Routes (Stage 5B)
$router->get('/parties/opening-balance', [PartyController::class, 'openingBalance']);
$router->post('/parties/opening-balance/store', [PartyController::class, 'storeOpeningBalance']);
$router->post('/parties/opening-balance/reverse', [PartyController::class, 'reverseOpeningBalance']);

// Receipts and Supplier Payments Routes (Stage 5C)
$router->get('/receipts', [ReceiptPaymentController::class, 'receiptsIndex']);
$router->get('/receipts/create', [ReceiptPaymentController::class, 'createReceipt']);
$router->post('/receipts/store', [ReceiptPaymentController::class, 'store']);
$router->get('/receipts/view', [ReceiptPaymentController::class, 'view']);
$router->post('/receipts/post', [ReceiptPaymentController::class, 'post']);
$router->post('/receipts/reverse', [ReceiptPaymentController::class, 'reverse']);

$router->get('/supplier-payments', [ReceiptPaymentController::class, 'paymentsIndex']);
$router->get('/supplier-payments/create', [ReceiptPaymentController::class, 'createPayment']);
$router->post('/supplier-payments/store', [ReceiptPaymentController::class, 'store']);
$router->get('/supplier-payments/view', [ReceiptPaymentController::class, 'view']);
$router->post('/supplier-payments/post', [ReceiptPaymentController::class, 'post']);
$router->post('/supplier-payments/reverse', [ReceiptPaymentController::class, 'reverse']);

// Cheques Routing (Stage 5E)
$router->get('/cheques', [ChequeController::class, 'index']);
$router->post('/cheques/clear', [ChequeController::class, 'clear']);
$router->post('/cheques/bounce', [ChequeController::class, 'bounce']);
$router->post('/cheques/cancel', [ChequeController::class, 'cancel']);

// Deposits Routing (Stage 5E)
$router->get('/deposits', [DepositController::class, 'index']);
$router->get('/deposits/create', [DepositController::class, 'create']);
$router->post('/deposits/store', [DepositController::class, 'store']);
$router->get('/deposits/view', [DepositController::class, 'view']);
$router->post('/deposits/post', [DepositController::class, 'post']);
$router->post('/deposits/cancel', [DepositController::class, 'cancel']);

// Marketplace Routing (Stage 6A)
$router->get('/modules/marketplace', [MarketplaceController::class, 'dashboard']);
$router->get('/modules/marketplace/products', [MarketplaceController::class, 'products']);
$router->post('/modules/marketplace/products/toggle', [MarketplaceController::class, 'toggleProduct']);
$router->post('/modules/marketplace/products/store', [MarketplaceController::class, 'storeProduct']);
$router->post('/modules/marketplace/products/update', [MarketplaceController::class, 'updateProductPrices']);

// Invoices Routing (Stage 6B)
$router->get('/modules/invoices', [InvoiceController::class, 'index']);
$router->get('/modules/invoices/create', [InvoiceController::class, 'create']);
$router->post('/modules/invoices/store', [InvoiceController::class, 'store']);
$router->get('/modules/invoices/view', [InvoiceController::class, 'view']);
$router->post('/modules/invoices/post', [InvoiceController::class, 'post']);
$router->post('/modules/invoices/cancel', [InvoiceController::class, 'cancel']);

// Services Routing (Stage 6B)
$router->get('/modules/services', [ServiceController::class, 'index']);
$router->post('/modules/services/store', [ServiceController::class, 'store']);
$router->post('/modules/services/deactivate', [ServiceController::class, 'deactivate']);

// Service Jobs Routing (Stage 6C)
$router->get('/modules/service-jobs', [ServiceJobController::class, 'index']);
$router->get('/modules/service-jobs/create', [ServiceJobController::class, 'create']);
$router->post('/modules/service-jobs/store', [ServiceJobController::class, 'store']);
$router->get('/modules/service-jobs/view', [ServiceJobController::class, 'view']);
$router->post('/modules/service-jobs/complete', [ServiceJobController::class, 'complete']);
$router->post('/modules/service-jobs/cancel', [ServiceJobController::class, 'cancel']);

// Machinery Routing (Stage 6D)
$router->get('/modules/machinery', [MachineryController::class, 'index']);
$router->post('/modules/machinery/store', [MachineryController::class, 'store']);
$router->post('/modules/machinery/maintenance', [MachineryController::class, 'markMaintenance']);
$router->post('/modules/machinery/deactivate', [MachineryController::class, 'deactivate']);

// Machinery Rentals Routing (Stage 6D)
$router->get('/modules/machinery-rentals', [MachineryRentalController::class, 'index']);
$router->get('/modules/machinery-rentals/view', [MachineryRentalController::class, 'view']);
$router->post('/modules/machinery-rentals/complete', [MachineryRentalController::class, 'complete']);
$router->post('/modules/machinery-rentals/cancel', [MachineryRentalController::class, 'cancel']);

// Business Operations Routing (Stage 6F)
$router->get('/operations/plantation', [OperationsController::class, 'plantation']);
$router->post('/operations/plantation/store', [OperationsController::class, 'storePlantationProject']);
$router->get('/operations/plantation/view', [OperationsController::class, 'viewPlantationProject']);
$router->get('/operations/plantation/expenses', [OperationsController::class, 'plantationExpenses']);
$router->get('/operations/plantation/crops', [OperationsController::class, 'plantationCrops']);
$router->post('/operations/plantation/crops/add', [OperationsController::class, 'addPlantationCrop']);
$router->post('/operations/plantation/crops/delete', [OperationsController::class, 'deletePlantationCrop']);
$router->post('/operations/plantation/crops/update', [OperationsController::class, 'updatePlantationCrop']);
$router->post('/operations/plantation/update-status', [OperationsController::class, 'updatePlantationProjectStatus']);
$router->get('/operations/plantation/harvesting', [OperationsController::class, 'plantationHarvesting']);
$router->post('/operations/plantation/harvesting/store', [OperationsController::class, 'storePlantationHarvest']);
$router->get('/operations/plantation/marketplace', [OperationsController::class, 'plantationMarketplace']);
$router->post('/operations/plantation/marketplace/transfer', [OperationsController::class, 'transferPlantationHarvest']);
$router->get('/operations/machinery', [OperationsController::class, 'machinery']);
$router->get('/operations/fruit-packing', [OperationsController::class, 'fruitPacking']);
$router->get('/operations/brick-manufacturing', [OperationsController::class, 'brickManufacturing']);
$router->post('/operations/brick-manufacturing/store', [OperationsController::class, 'storeBrickProject']);
$router->get('/operations/brick-manufacturing/view', [OperationsController::class, 'viewBrickProject']);
$router->post('/operations/brick-manufacturing/update-status', [OperationsController::class, 'updateBrickProjectStatus']);
$router->get('/operations/brick-manufacturing/expenses', [OperationsController::class, 'brickExpenses']);
$router->get('/operations/brick-manufacturing/production', [OperationsController::class, 'brickProduction']);
$router->post('/operations/brick-manufacturing/production/store', [OperationsController::class, 'storeBrickProduction']);
$router->get('/operations/brick-manufacturing/marketplace', [OperationsController::class, 'brickMarketplace']);
$router->post('/operations/brick-manufacturing/marketplace/transfer', [OperationsController::class, 'transferBrickProduction']);
$router->get('/operations/construction', [OperationsController::class, 'construction']);
$router->get('/operations/grinding-mill', [OperationsController::class, 'grindingMill']);

// Cash Book Routing
$router->get('/modules/cash-accounts', [CashController::class, 'index']);
$router->post('/modules/cash-accounts/transaction', [CashController::class, 'transaction']);

// Bank Accounts Routing
$router->get('/modules/bank-accounts', [BankController::class, 'index']);
$router->post('/modules/bank-accounts/store', [BankController::class, 'store']);
$router->post('/modules/bank-accounts/transaction', [BankController::class, 'transaction']);

// Society Member & Directory Routing
$router->get('/modules/members/directory', [MemberController::class, 'directory']);
$router->get('/modules/members/register', [MemberController::class, 'registerForm']);
$router->post('/modules/members/store', [MemberController::class, 'store']);
$router->get('/modules/members/view', [MemberController::class, 'view']);
$router->post('/modules/members/link-customer', [MemberController::class, 'linkCustomer']);

// Society Director Routing
$router->get('/modules/directors/directory', [DirectorController::class, 'directory']);
$router->get('/modules/directors/register', [DirectorController::class, 'registerForm']);
$router->post('/modules/directors/store', [DirectorController::class, 'store']);
$router->get('/modules/directors/view', [DirectorController::class, 'view']);
$router->post('/modules/directors/link-customer', [DirectorController::class, 'linkCustomer']);

// Complete Fixed Deposits Routing
$router->get('/modules/fixed-deposits', [FDController::class, 'index']);
$router->get('/modules/fixed-deposits/create', [FDController::class, 'create']);
$router->post('/modules/fixed-deposits/store', [FDController::class, 'store']);
$router->get('/modules/fixed-deposits/view', [FDController::class, 'view']);
$router->post('/modules/fixed-deposits/mature', [FDController::class, 'processMaturity']);
$router->post('/modules/fixed-deposits/premature-close', [FDController::class, 'prematureClose']);

// Cash & Banking Dashboard Overview
$router->get('/modules/cash-bank-overview', function() {
    $db = \Core\Database::getInstance();
    
    // Get Cash in Hand Balance
    $cashInHandAccount = $db->query("SELECT id FROM accounts WHERE account_code = '1110'")->fetchColumn();
    $cashDebits = (float)$db->query("SELECT SUM(debit) FROM journal_lines jl JOIN journal_entries je ON jl.journal_entry_id = je.id WHERE jl.account_id = " . (int)$cashInHandAccount . " AND je.status = 'posted'")->fetchColumn();
    $cashCredits = (float)$db->query("SELECT SUM(credit) FROM journal_lines jl JOIN journal_entries je ON jl.journal_entry_id = je.id WHERE jl.account_id = " . (int)$cashInHandAccount . " AND je.status = 'posted'")->fetchColumn();
    $cashBalance = $cashDebits - $cashCredits;

    // Get Total Bank Balance
    $bankBalance = (float)$db->query("SELECT SUM(current_balance) FROM bank_accounts WHERE status = 'active'")->fetchColumn();

    // Get Cheque in Hand (Undeposited Cheques 1115)
    $undepAccountId = (int)$db->query("SELECT id FROM accounts WHERE account_code = '1115'")->fetchColumn();
    $chequeDebits = (float)$db->query("SELECT SUM(debit) FROM journal_lines jl JOIN journal_entries je ON jl.journal_entry_id = je.id WHERE jl.account_id = " . (int)$undepAccountId . " AND je.status = 'posted'")->fetchColumn();
    $chequeCredits = (float)$db->query("SELECT SUM(credit) FROM journal_lines jl JOIN journal_entries je ON jl.journal_entry_id = je.id WHERE jl.account_id = " . (int)$undepAccountId . " AND je.status = 'posted'")->fetchColumn();
    $chequeInHand = $chequeDebits - $chequeCredits;

    // Outstanding Cheques (status RECEIVED or DEPOSITED)
    $outstandingCheques = $db->query("
        SELECT c.*, p.name AS customer_name 
        FROM cheques c
        JOIN parties p ON c.party_id = p.id
        WHERE c.status IN ('RECEIVED', 'DEPOSITED')
        ORDER BY c.cheque_date DESC LIMIT 5
    ")->fetchAll();

    // Recent banking transactions
    $recentTransactions = $db->query("
        SELECT je.* 
        FROM journal_entries je
        WHERE je.source_module = 'finance' AND je.status = 'posted'
        ORDER BY je.transaction_date DESC, je.id DESC LIMIT 5
    ")->fetchAll();

    $controller = new \App\Controllers\ModuleController();
    $controller->renderView('dashboard/cash_bank_overview', [
        'pageTitle' => 'Cash & Banking Overview',
        'activeNav' => 'cash_bank_overview',
        'cashBalance' => $cashBalance,
        'bankBalance' => $bankBalance,
        'chequeInHand' => $chequeInHand,
        'outstandingCheques' => $outstandingCheques,
        'recentTransactions' => $recentTransactions
    ]);
});

// Operational Module Placeholders
$placeholders = [
    'products' => ['Products Directory', 'bi-box-seam'],
    'purchases' => ['Purchase Orders', 'bi-cart-check'],
    'grn' => ['Goods Received Note (GRN)', 'bi-file-earmark-arrow-down'],
    'stock-overview' => ['Stock Overview', 'bi-boxes'],
    'stock-ledger' => ['Stock Ledger', 'bi-list-columns'],
    'agri-services' => ['Agricultural Services & Plowing', 'bi-tractor'],
    'plantation' => ['Plantation Projects', 'bi-flower2'],
    'brick-manufacturing' => ['Brick Manufacturing', 'bi-bricks'],
    'fruit-packing' => ['Fruit Packing & Processing', 'bi-basket'],
    'grinding-mill' => ['Grinding Mill', 'bi-gear-wide-connected'],
    'construction' => ['Construction Contracts', 'bi-building'],
    'financial-reports' => ['Financial Reports', 'bi-file-earmark-bar-graph'],
    'business-reports' => ['Business Reports', 'bi-pie-chart'],
    'users' => ['Users & Role Management', 'bi-person-gear'],
    'audit-logs' => ['System Audit Logs', 'bi-shield-check']
];

foreach ($placeholders as $slug => [$name, $icon]) {
    $router->get("/modules/{$slug}", function() use ($name, $slug, $icon) {
        $controller = new ModuleController();
        $controller->placeholder($name, $name, $icon);
    });
}

// Dispatch Request
$router->dispatch();
