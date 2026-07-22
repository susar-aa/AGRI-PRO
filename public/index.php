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
use App\Controllers\ModuleController;

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

// Operational Module Placeholders
$placeholders = [
    'marketplace' => ['Marketplace & Trading', 'bi-shop'],
    'products' => ['Products Directory', 'bi-box-seam'],
    'sales' => ['Sales Orders & Billing', 'bi-receipt'],
    'customers' => ['Customer Directory', 'bi-people'],
    'suppliers' => ['Supplier Directory', 'bi-truck'],
    'purchases' => ['Purchase Orders', 'bi-cart-check'],
    'grn' => ['Goods Received Note (GRN)', 'bi-file-earmark-arrow-down'],
    'stock-overview' => ['Stock Overview', 'bi-boxes'],
    'stock-ledger' => ['Stock Ledger', 'bi-list-columns'],
    'agri-services' => ['Agricultural Services & Plowing', 'bi-tractor'],
    'machinery-rental' => ['Machinery Rental', 'bi-tools'],
    'plantation' => ['Plantation Projects', 'bi-flower2'],
    'brick-manufacturing' => ['Brick Manufacturing', 'bi-bricks'],
    'fruit-packing' => ['Fruit Packing & Processing', 'bi-basket'],
    'grinding-mill' => ['Grinding Mill', 'bi-gear-wide-connected'],
    'construction' => ['Construction Contracts', 'bi-building'],
    'expenses' => ['Expense Management Engine', 'bi-wallet2'],
    'cash-accounts' => ['Cash Accounts', 'bi-cash-stack'],
    'bank-accounts' => ['Bank Accounts', 'bi-bank'],
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
