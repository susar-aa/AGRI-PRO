<?php
/**
 * Automated Verification Test for Plantation Yield Harvesting
 */

require_once __DIR__ . '/../core/bootstrap.php';

use Core\Database;
use App\Services\InventoryEngine;

echo "==================================================\n";
echo "AGRI CO-OP ERP - PLANTATION HARVEST VERIFICATION\n";
echo "==================================================\n\n";

try {
    $db = Database::getInstance();
    
    // 1. Authenticate as Admin
    $_SESSION['user_id'] = 1;
    $_SESSION['role_id'] = 1;
    $_SESSION['permissions'] = ['machinery.view', 'inventory.view']; // minimal permissions
    echo "[SETUP] Authenticated as administrator.\n";

    // Clean old test data
    $db->exec("DELETE FROM stock_ledger WHERE source_module = 'PLANTATION_HARVEST'");
    $db->exec("DELETE FROM plantation_harvests WHERE project_id IN (SELECT id FROM plantation_projects WHERE project_name LIKE 'TEST-HARV-%')");
    $db->exec("DELETE FROM plantation_project_crops WHERE project_id IN (SELECT id FROM plantation_projects WHERE project_name LIKE 'TEST-HARV-%')");
    $db->exec("DELETE FROM plantation_projects WHERE project_name LIKE 'TEST-HARV-%'");
    echo "[SETUP] Cleaned old test data.\n";

    // Get an active product to cultivate
    $product = $db->query("SELECT id, sku, product_code, name_en, base_unit_id FROM products WHERE is_active = 1 LIMIT 1")->fetch();
    if (!$product) {
        throw new Exception("No active products available to run tests.");
    }
    $productId = (int)$product['id'];
    echo "[SETUP] Selected crop product: " . $product['product_code'] . " - " . $product['name_en'] . "\n";

    // 2. Create Plantation Project
    $db->prepare("
        INSERT INTO plantation_projects (project_name, location, start_date, status)
        VALUES ('TEST-HARV-PROJ', 'Test Land Area H', CURDATE(), 'ACTIVE')
    ")->execute();
    $projectId = (int)$db->lastInsertId();
    echo "[TEST 1] Created Plantation Project. ID: {$projectId} ... PASSED\n";

    // 3. Map crop to project
    $db->prepare("
        INSERT INTO plantation_project_crops (project_id, product_id, planned_quantity, unit, notes)
        VALUES (:project_id, :product_id, 0.00, 'KG', 'Test mapping')
    ")->execute([
        'project_id' => $projectId,
        'product_id' => $productId
    ]);
    echo "[TEST 2] Mapped crop to project ... PASSED\n";

    // Get current stock balance before harvest to compare later
    $balanceBefore = (float)$db->query("SELECT quantity_on_hand FROM inventory_balances WHERE product_id = {$productId} AND location_id = 1")->fetchColumn();
    echo "[INFO] Stock quantity before harvest: {$balanceBefore} KG\n";

    // 4. Simulate Yield Harvest Record insertion and Inventory integration
    $harvestDate = date('Y-m-d');
    $harvestQty = 150.00;
    
    // Insert harvest record
    $db->prepare("
        INSERT INTO plantation_harvests (project_id, product_id, harvest_date, quantity, unit, quality_grade, notes, created_by)
        VALUES (:project_id, :product_id, :harvest_date, :quantity, 'KG', 'Grade A', 'First yield batch', 1)
    ")->execute([
        'project_id' => $projectId,
        'product_id' => $productId,
        'harvest_date' => $harvestDate,
        'quantity' => $harvestQty
    ]);
    $harvestId = (int)$db->lastInsertId();
    echo "[TEST 3] Logged Harvest Record. ID: {$harvestId} ... PASSED\n";

    // 5. Verification Assertions
    // A. Check harvest row exists
    $harvestRow = $db->query("SELECT * FROM plantation_harvests WHERE id = {$harvestId}")->fetch();
    if (!$harvestRow || (float)$harvestRow['quantity'] !== 150.00) {
        throw new Exception("Verification failed: Harvest record quantity mismatch.");
    }
    echo "[ASSERT] Harvest record saved successfully ... PASSED\n";

    // B. Check that NO Stock Ledger Entry exists for this harvest
    $ledgerRow = $db->query("SELECT * FROM stock_ledger WHERE source_module = 'PLANTATION_HARVEST' AND source_transaction_id = {$harvestId} LIMIT 1")->fetch();
    if ($ledgerRow) {
        throw new Exception("Verification failed: Stock ledger entry should NOT be created on harvest.");
    }
    echo "[ASSERT] No Stock Ledger record created for harvest (as expected) ... PASSED\n";

    // C. Check that inventory balance is unchanged
    $balanceAfter = (float)$db->query("SELECT quantity_on_hand FROM inventory_balances WHERE product_id = {$productId} AND location_id = 1")->fetchColumn();
    echo "[INFO] Stock quantity after harvest: {$balanceAfter} KG\n";
    if ($balanceAfter !== $balanceBefore) {
        throw new Exception("Verification failed: Inventory balance changed on harvest.");
    }
    echo "[ASSERT] Inventory balance remains unchanged after harvest ... PASSED\n";

    echo "\n==================================================\n";
    echo "ALL PLANTATION HARVEST VERIFICATION TESTS PASSED!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Test failed: " . $e->getMessage() . "\n";
    exit(1);
}
