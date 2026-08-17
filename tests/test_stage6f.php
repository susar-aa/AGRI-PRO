<?php
/**
 * Stage 6F Automated Navigation & Dashboards Verification Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

use Core\Database;

echo "==================================================\n";
echo "AGRI CO-OP ERP - STAGE 6F AUTOMATED VERIFICATION\n";
echo "==================================================\n\n";

try {
    // 1. Setup Session & Authentication Simulation
    \Core\Auth::attempt('admin', 'admin123');
    echo "[SETUP] Authenticated as administrator.\n";

    // 2. Verify sidebar contains core and business headers and elements
    echo "[TEST 1] Testing sidebar layout content... ";
    ob_start();
    $activeNav = 'ops_plantation';
    include __DIR__ . '/../app/Views/layouts/sidebar.php';
    $sidebarOutput = ob_get_clean();

    if (strpos($sidebarOutput, 'CORE OPERATIONS') === false) {
        throw new Exception("Sidebar does not contain 'CORE OPERATIONS' section header.");
    }
    if (strpos($sidebarOutput, 'BUSINESS OPERATIONS') === false) {
        throw new Exception("Sidebar does not contain 'BUSINESS OPERATIONS' section header.");
    }
    if (strpos($sidebarOutput, 'active') === false) {
        throw new Exception("Sidebar does not handle active navigation highlighting correctly.");
    }
    echo "PASSED\n";

    // 3. Test Plantation Overview Controller Dispatch
    echo "[TEST 2] Testing Plantation Overview rendering... ";
    $opsController = new \App\Controllers\OperationsController();
    ob_start();
    $opsController->plantation();
    $plantationOutput = ob_get_clean();
    if (strpos($plantationOutput, 'Plantation Operations Control Panel') === false) {
        throw new Exception("Plantation Overview view did not render correct control title.");
    }
    echo "PASSED\n";

    // 4. Test Machinery Renting Overview Controller Dispatch
    echo "[TEST 3] Testing Machinery Renting Overview rendering... ";
    ob_start();
    $opsController->machinery();
    $machineryOutput = ob_get_clean();
    if (strpos($machineryOutput, 'Machinery Renting Operations Control Panel') === false) {
        throw new Exception("Machinery Overview view did not render correct control title.");
    }
    echo "PASSED\n";

    // 5. Test Fruit Packing Overview Controller Dispatch
    echo "[TEST 4] Testing Fruit Packing Overview rendering... ";
    ob_start();
    $opsController->fruitPacking();
    $fruitPackingOutput = ob_get_clean();
    if (strpos($fruitPackingOutput, 'Fruit Packing Operations Control Panel') === false) {
        throw new Exception("Fruit Packing Overview view did not render correct control title.");
    }
    echo "PASSED\n";

    // 6. Test Brick Manufacturing Overview Controller Dispatch
    echo "[TEST 5] Testing Brick Manufacturing Overview rendering... ";
    ob_start();
    $opsController->brickManufacturing();
    $brickOutput = ob_get_clean();
    if (strpos($brickOutput, 'Brick Manufacturing Operations Control Panel') === false) {
        throw new Exception("Brick Manufacturing view did not render correct control title.");
    }
    echo "PASSED\n";

    // 7. Test Construction Projects Overview Controller Dispatch
    echo "[TEST 6] Testing Construction Projects Overview rendering... ";
    ob_start();
    $opsController->construction();
    $constructionOutput = ob_get_clean();
    if (strpos($constructionOutput, 'Construction Projects Control Panel') === false) {
        throw new Exception("Construction Projects view did not render correct control title.");
    }
    echo "PASSED\n";

    // 8. Test Grinding Mill Overview Controller Dispatch
    echo "[TEST 7] Testing Grinding Mill Overview rendering... ";
    ob_start();
    $opsController->grindingMill();
    $grindingOutput = ob_get_clean();
    if (strpos($grindingOutput, 'Grinding Mill Operations Control Panel') === false) {
        throw new Exception("Grinding Mill view did not render correct control title.");
    }
    echo "PASSED\n";

    echo "\n==================================================\n";
    echo "ALL STAGE 6F VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Verification Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
