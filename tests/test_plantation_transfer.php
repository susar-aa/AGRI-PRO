<?php
/**
 * Automated Verification Test for Plantation Harvest Transfer to Marketplace
 */

require_once __DIR__ . '/../core/bootstrap.php';

use Core\Database;

echo "==================================================\n";
echo "AGRI CO-OP ERP - HARVEST TRANSFER VERIFICATION\n";
echo "==================================================\n\n";

try {
    $db = Database::getInstance();

    // 1. Authenticate as Admin
    $_SESSION['user_id'] = 1;
    $_SESSION['role_id'] = 1;
    $_SESSION['permissions'] = ['machinery.view', 'inventory.view'];
    echo "[SETUP] Authenticated as administrator.\n";

    // Clean old test data
    $db->exec("DELETE FROM stock_ledger WHERE source_type = 'MARKETPLACE_TRANSFER'");
    $db->exec("DELETE FROM plantation_harvest_transfers WHERE harvest_id IN (SELECT id FROM plantation_harvests WHERE project_id IN (SELECT id FROM plantation_projects WHERE project_name LIKE 'TEST-TRF-%'))");
    $db->exec("DELETE FROM plantation_harvests WHERE project_id IN (SELECT id FROM plantation_projects WHERE project_name LIKE 'TEST-TRF-%')");
    $db->exec("DELETE FROM plantation_project_crops WHERE project_id IN (SELECT id FROM plantation_projects WHERE project_name LIKE 'TEST-TRF-%')");
    $db->exec("DELETE FROM plantation_projects WHERE project_name LIKE 'TEST-TRF-%'");
    echo "[SETUP] Cleaned old test data.\n";

    // Get an active product to cultivate
    $product = $db->query("SELECT id, sku, product_code, name_en FROM products WHERE is_active = 1 LIMIT 1")->fetch();
    if (!$product) {
        throw new Exception("No active products available to run tests.");
    }
    $productId = (int)$product['id'];
    echo "[SETUP] Selected crop product: " . $product['product_code'] . " - " . $product['name_en'] . "\n";

    // Turn off is_marketplace initially to test toggling
    $db->prepare("UPDATE products SET is_marketplace = 0 WHERE id = :pid")->execute(['pid' => $productId]);

    // 2. Create Plantation Project
    $db->prepare("
        INSERT INTO plantation_projects (project_name, location, start_date, status)
        VALUES ('TEST-TRF-PROJ', 'Test Land Area T', CURDATE(), 'ACTIVE')
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

    // 4. Create Harvest Record
    $db->prepare("
        INSERT INTO plantation_harvests (project_id, product_id, harvest_date, quantity, unit, created_by)
        VALUES (:project_id, :product_id, CURDATE(), 200.00, 'KG', 1)
    ")->execute([
        'project_id' => $projectId,
        'product_id' => $productId
    ]);
    $harvestId = (int)$db->lastInsertId();
    echo "[TEST 3] Created Harvest Record. ID: {$harvestId} ... PASSED\n";

    // Get initial balance before transfer
    $balanceBefore = (float)$db->query("SELECT quantity_on_hand FROM inventory_balances WHERE product_id = {$productId} AND location_id = 1")->fetchColumn() ?: 0.00;

    // 5. Transfer Harvest to Marketplace
    $transferQty = 75.00;
    
    // Insert transfer record
    $db->prepare("
        INSERT INTO plantation_harvest_transfers (harvest_id, transfer_date, quantity, created_by)
        VALUES (:harvest_id, CURDATE(), :qty, 1)
    ")->execute([
        'harvest_id' => $harvestId,
        'qty' => $transferQty
    ]);
    $transferId = (int)$db->lastInsertId();
    echo "[TEST 4] Inserted Transfer Record. ID: {$transferId} ... PASSED\n";

    // Make product available in Marketplace
    $db->prepare("UPDATE products SET is_marketplace = 1 WHERE id = :pid")->execute(['pid' => $productId]);
    echo "[TEST 5] Enabled is_marketplace on product ... PASSED\n";

    // Call recordStockIn to log actual transfer and increase stock balance
    \App\Services\InventoryEngine::recordStockIn(
        $productId,
        1,
        $transferQty,
        0.00,
        'Transfer',
        'PLANTATION_HARVEST',
        $transferId,
        'TRF-' . $transferId
    );
    echo "[TEST 6] Triggered recordStockIn for Transfer ... PASSED\n";

    // 6. Verification Assertions
    // A. Check transfer row exists
    $transferRow = $db->query("SELECT * FROM plantation_harvest_transfers WHERE id = {$transferId}")->fetch();
    if (!$transferRow || (float)$transferRow['quantity'] !== 75.00) {
        throw new Exception("Verification failed: Transfer record quantity mismatch.");
    }
    echo "[ASSERT] Transfer record saved successfully ... PASSED\n";

    // B. Check stock ledger entry exists for the transfer
    $ledgerRow = $db->query("SELECT * FROM stock_ledger WHERE source_module = 'PLANTATION_HARVEST' AND source_transaction_id = {$transferId} LIMIT 1")->fetch();
    if (!$ledgerRow || (float)$ledgerRow['quantity_in'] !== 75.00) {
        throw new Exception("Verification failed: Stock ledger transfer entry not found or quantity mismatch.");
    }
    echo "[ASSERT] Stock Ledger Transfer record logged correctly ... PASSED\n";

    // C. Check balance updated
    $balanceAfter = (float)$db->query("SELECT quantity_on_hand FROM inventory_balances WHERE product_id = {$productId} AND location_id = 1")->fetchColumn();
    if ($balanceAfter !== ($balanceBefore + $transferQty)) {
         throw new Exception("Verification failed: Stock balance did not increase after transfer.");
    }
    echo "[ASSERT] Stock balance increased by transfer quantity ... PASSED\n";

    // B. Check is_marketplace updated
    $productStatus = (int)$db->query("SELECT is_marketplace FROM products WHERE id = {$productId}")->fetchColumn();
    if ($productStatus !== 1) {
        throw new Exception("Verification failed: is_marketplace was not set to 1.");
    }
    echo "[ASSERT] Product is enabled for Marketplace sales ... PASSED\n";

    // All assertions completed successfully

    echo "\n==================================================\n";
    echo "ALL HARVEST TRANSFER VERIFICATION TESTS PASSED!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Test failed: " . $e->getMessage() . "\n";
    exit(1);
}
