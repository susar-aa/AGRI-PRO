<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/core/Helper.php';
require __DIR__ . '/core/Database.php';
require __DIR__ . '/core/Session.php';
require __DIR__ . '/core/Auth.php';
require __DIR__ . '/core/Controller.php';
require __DIR__ . '/app/Models/ReportModel.php';
require __DIR__ . '/app/Models/Journal.php';
require __DIR__ . '/app/Models/Account.php';
require __DIR__ . '/app/Models/CostCenter.php';
require __DIR__ . '/app/Models/Party.php';
require __DIR__ . '/app/Controllers/ReportController.php';

// Mock session and auth
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role_id'] = 1;
$_SESSION['role_code'] = 'ADMIN';
$_SESSION['full_name'] = 'System Administrator';

$_GET['month'] = '2026-09';
$controller = new \App\Controllers\ReportController();

echo "Testing month resolution and data retrieval...\n";
ob_start();
try {
    $journal = new \App\Models\Journal();
    $tb = $journal->getTrialBalance('2026-09-30', '2026-09-01', null, true);
    echo "Trial balance rows for 2026-09: " . count($tb) . "\n";
    echo "SUCCESS: Journal and Model operational.\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
ob_end_flush();
