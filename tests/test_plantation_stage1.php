<?php
/**
 * Plantation Operation Stage 1 Automated Verification Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

use Core\Database;

echo "==================================================\n";
echo "AGRI CO-OP ERP - PLANTATION STAGE 1 VERIFICATION\n";
echo "==================================================\n\n";

try {
    $db = Database::getInstance();

    // 1. Setup Session & Authentication Simulation
    \Core\Auth::attempt('admin', 'admin123');
    echo "[SETUP] Authenticated as administrator.\n";

    // Clean old test data
    $db->exec("DELETE FROM plantation_project_crops");
    $db->exec("DELETE FROM plantation_projects");
    $db->exec("DELETE FROM expenses WHERE project_id IS NOT NULL");
    echo "[SETUP] Cleaned old test data.\n";

    // 2. Test Project Creation
    echo "[TEST 1] Creating Plantation Project... ";
    $projectName = "2026 Yatagama Plantation";
    $location = "Yatagama Sector B";
    $startDate = date('Y-m-d');
    
    $stmt = $db->prepare("
        INSERT INTO plantation_projects (project_name, location, start_date, status) 
        VALUES (:name, :location, :start_date, 'ACTIVE')
    ");
    $stmt->execute([
        'name' => $projectName,
        'location' => $location,
        'start_date' => $startDate
    ]);
    $projectId = (int)$db->lastInsertId();
    if ($projectId <= 0) {
        throw new Exception("Failed to insert plantation project.");
    }
    echo "PASSED (ID: $projectId)\n";

    // 3. Test Crop Association
    echo "[TEST 2] Mapping crops to project... ";
    // Let's find some existing products in DB or insert a test one if needed
    $products = $db->query("SELECT id, sales_unit_id FROM products LIMIT 2")->fetchAll();
    if (count($products) < 2) {
        $db->exec("
            INSERT INTO products (sku, product_code, name_en, category_id, product_type, base_unit_id, purchase_unit_id, sales_unit_id, created_by) 
            VALUES ('TEST-SKU-999', 'PROD-999', 'Test Product 999', 1, 'RAW_MATERIAL', 1, 1, 1, 1)
        ");
        $products = $db->query("SELECT id, sales_unit_id FROM products LIMIT 2")->fetchAll();
    }

    $cropStmt = $db->prepare("
        INSERT INTO plantation_project_crops (project_id, product_id, planned_quantity, unit) 
        VALUES (:project_id, :product_id, :qty, :unit)
    ");
    foreach ($products as $index => $prod) {
        $cropStmt->execute([
            'project_id' => $projectId,
            'product_id' => $prod['id'],
            'qty' => ($index + 1) * 100.0,
            'unit' => 'KG'
        ]);
    }
    
    $cropsCount = (int)$db->query("SELECT COUNT(*) FROM plantation_project_crops WHERE project_id = $projectId")->fetchColumn();
    if ($cropsCount !== 2) {
        throw new Exception("Expected 2 crops mapped, found $cropsCount.");
    }
    echo "PASSED\n";

    // 4. Test Expenses Association
    echo "[TEST 3] Linking expenses to project... ";
    // Insert a posted expense
    // Find active cost center, bank/cash accounts, expense category, etc.
    $catId = $db->query("SELECT id FROM expense_categories LIMIT 1")->fetchColumn();
    $cashId = $db->query("SELECT id FROM cash_accounts LIMIT 1")->fetchColumn();
    $accId = $db->query("SELECT id FROM accounts WHERE category = 'Expense' LIMIT 1")->fetchColumn();
    
    if (!$catId || !$cashId || !$accId) {
        throw new Exception("Required expense mappings not found in DB.");
    }

    $expNumber = 'EXP-' . time();
    $db->prepare("
        INSERT INTO expenses (expense_number, expense_date, payee, expense_category_id, description, amount, payment_method, cash_account_id, expense_account_id, cost_center_id, project_id, status, created_by) 
        VALUES (:exp_num, :date, 'Test Payee', :cat_id, 'Fertilizer for Yatagama', 1500.50, 'Cash', :cash_id, :exp_acc_id, 4, :proj_id, 'posted', 1)
    ")->execute([
        'exp_num' => $expNumber,
        'date' => date('Y-m-d'),
        'cat_id' => $catId,
        'cash_id' => $cashId,
        'exp_acc_id' => $accId,
        'proj_id' => $projectId
    ]);

    $expTotal = (float)$db->query("SELECT COALESCE(SUM(amount), 0.00) FROM expenses WHERE project_id = $projectId AND status = 'posted'")->fetchColumn();
    if ($expTotal !== 1500.50) {
        throw new Exception("Expected expense total of 1500.50, found $expTotal.");
    }
    echo "PASSED\n";

    // 5. Test Controller Dispatch Renders Correctly
    echo "[TEST 4] Dispatching OperationsController::plantation... ";
    $opsController = new \App\Controllers\OperationsController();
    ob_start();
    $opsController->plantation();
    $plantationOutput = ob_get_clean();
    if (strpos($plantationOutput, 'Plantation Operations Overview') === false) {
        throw new Exception("Plantation Overview view did not render successfully.");
    }
    echo "PASSED\n";

    echo "[TEST 5] Dispatching OperationsController::viewPlantationProject... ";
    $_GET['id'] = (string)$projectId;
    ob_start();
    $opsController->viewPlantationProject();
    $dashboardOutput = ob_get_clean();
    if (strpos($dashboardOutput, 'Yatagama Plantation Dashboard') === false) {
        throw new Exception("Plantation Dashboard view did not render successfully.");
    }
    echo "PASSED\n";

    echo "\n==================================================\n";
    echo "ALL PLANTATION STAGE 1 VERIFICATION TESTS PASSED!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Verification Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
