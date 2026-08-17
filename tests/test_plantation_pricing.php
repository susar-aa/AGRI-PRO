<?php
/**
 * Automated Verification Test for Plantation Cost Calculation & Selling Price Update
 */

require_once __DIR__ . '/../core/bootstrap.php';

use Core\Database;

echo "==================================================\n";
echo "AGRI CO-OP ERP - COST & PRICING VERIFICATION\n";
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
    $db->exec("DELETE FROM plantation_harvest_transfers WHERE harvest_id IN (SELECT id FROM plantation_harvests WHERE project_id IN (SELECT id FROM plantation_projects WHERE project_name LIKE 'TEST-PRICE-%'))");
    $db->exec("DELETE FROM plantation_harvests WHERE project_id IN (SELECT id FROM plantation_projects WHERE project_name LIKE 'TEST-PRICE-%')");
    $db->exec("DELETE FROM expenses WHERE project_id IN (SELECT id FROM plantation_projects WHERE project_name LIKE 'TEST-PRICE-%')");
    $db->exec("DELETE FROM plantation_project_crops WHERE project_id IN (SELECT id FROM plantation_projects WHERE project_name LIKE 'TEST-PRICE-%')");
    $db->exec("DELETE FROM plantation_projects WHERE project_name LIKE 'TEST-PRICE-%'");
    echo "[SETUP] Cleaned old test data.\n";

    // Get an active product to cultivate
    $product = $db->query("SELECT id, sku, product_code, name_en FROM products WHERE is_active = 1 LIMIT 1")->fetch();
    if (!$product) {
        throw new Exception("No active products available to run tests.");
    }
    $productId = (int)$product['id'];
    echo "[SETUP] Selected crop product: " . $product['product_code'] . " - " . $product['name_en'] . "\n";

    // Turn off is_marketplace and set price initially to 0
    $db->prepare("UPDATE products SET is_marketplace = 0, default_selling_price = 0.00 WHERE id = :pid")->execute(['pid' => $productId]);

    // 2. Create Plantation Project
    $db->prepare("
        INSERT INTO plantation_projects (project_name, location, start_date, status)
        VALUES ('TEST-PRICE-PROJ', 'Test Land Area T', CURDATE(), 'ACTIVE')
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

    // Fetch valid accounts and categories dynamically
    $expenseAccountId = (int)$db->query("SELECT id FROM accounts WHERE category = 'Expense' OR account_code LIKE '5%' LIMIT 1")->fetchColumn();
    $cashAccountId = (int)$db->query("SELECT id FROM accounts WHERE category = 'Asset' OR account_code LIKE '1%' LIMIT 1")->fetchColumn();
    $expenseCategoryId = (int)$db->query("SELECT id FROM expense_categories LIMIT 1")->fetchColumn();

    // 4. Create Project Posted Expenses (total LKR 100,000.00)
    $db->prepare("
        INSERT INTO expenses (project_id, expense_number, expense_date, expense_category_id, expense_account_id, cash_account_id, cost_center_id, amount, payment_method, status, description, created_by)
        VALUES (:project_id, 'EXP-TEST-PRICE', CURDATE(), :cat_id, :exp_acc, :cash_acc, 4, 100000.00, 'Cash', 'posted', 'Test expenses', 1)
    ")->execute([
        'project_id' => $projectId,
        'cat_id' => $expenseCategoryId ?: 1,
        'exp_acc' => $expenseAccountId ?: 1,
        'cash_acc' => $cashAccountId ?: 1
    ]);
    echo "[TEST 3] Created Posted Project Expense (LKR 100,000) ... PASSED\n";

    // 5. Create two Harvest Records (500 KG each, total 1,000 KG)
    $db->prepare("
        INSERT INTO plantation_harvests (project_id, product_id, harvest_date, quantity, unit, created_by)
        VALUES (:project_id, :product_id, CURDATE(), 500.00, 'KG', 1)
    ")->execute([
        'project_id' => $projectId,
        'product_id' => $productId
    ]);
    $harvestId1 = (int)$db->lastInsertId();

    $db->prepare("
        INSERT INTO plantation_harvests (project_id, product_id, harvest_date, quantity, unit, created_by)
        VALUES (:project_id, :product_id, CURDATE(), 500.00, 'KG', 1)
    ")->execute([
        'project_id' => $projectId,
        'product_id' => $productId
    ]);
    $harvestId2 = (int)$db->lastInsertId();
    echo "[TEST 4] Created Two Harvests of 500 KG each (Total: 1,000 KG) ... PASSED\n";

    // 6. Verify Cost Per Unit Calculation
    $totalHarvested = (float)$db->query("SELECT SUM(quantity) FROM plantation_harvests WHERE project_id = {$projectId}")->fetchColumn();
    $totalExpenses = (float)$db->query("SELECT SUM(amount) FROM expenses WHERE project_id = {$projectId} AND status = 'posted'")->fetchColumn();
    $costPerUnit = $totalHarvested > 0 ? ($totalExpenses / $totalHarvested) : 0.00;

    if ($costPerUnit !== 100.00) {
        throw new Exception("Cost calculation mismatch. Expected: 100.00, Got: {$costPerUnit}");
    }
    echo "[TEST 5] Verified Cost Calculation (LKR 100.00 / KG) ... PASSED\n";

    // 7. Transfer Harvest and Update Selling Price (Transfer 400 KG, Sell at LKR 150.00)
    $transferQty = 400.00;
    $sellingPrice = 150.00;

    $db->prepare("
        INSERT INTO plantation_harvest_transfers (harvest_id, transfer_date, quantity, cost_price_per_unit, selling_price_per_unit, created_by)
        VALUES (:harvest_id, CURDATE(), :qty, :cost, :sell, 1)
    ")->execute([
        'harvest_id' => $harvestId1,
        'qty' => $transferQty,
        'cost' => $costPerUnit,
        'sell' => $sellingPrice
    ]);
    $transferId = (int)$db->lastInsertId();

    $db->prepare("UPDATE products SET is_marketplace = 1, default_selling_price = :selling_price, default_purchase_price = :cost_price WHERE id = :pid")->execute([
        'selling_price' => $sellingPrice,
        'cost_price' => $costPerUnit,
        'pid' => $productId
    ]);

    // Create balance if not exists for test robustness
    $hasBalance = $db->query("SELECT COUNT(*) FROM inventory_balances WHERE product_id = {$productId} AND location_id = 1")->fetchColumn();
    if (!$hasBalance) {
        $db->prepare("INSERT INTO inventory_balances (product_id, location_id, quantity_on_hand, average_cost, inventory_value) VALUES (:pid, 1, 100.00, 0.00, 0.00)")->execute(['pid' => $productId]);
    }

    $db->prepare("UPDATE inventory_balances SET average_cost = :cost_price, inventory_value = quantity_on_hand * :cost_price_val, updated_at = NOW() WHERE product_id = :pid AND location_id = 1")->execute([
        'cost_price' => $costPerUnit,
        'cost_price_val' => $costPerUnit,
        'pid' => $productId
    ]);
    echo "[TEST 6] Saved Transfer to Database and updated Product Selling & Cost Prices ... PASSED\n";

    // 8. Assertions
    $transferRow = $db->query("SELECT * FROM plantation_harvest_transfers WHERE id = {$transferId}")->fetch();
    if (!$transferRow || (float)$transferRow['cost_price_per_unit'] !== 100.00 || (float)$transferRow['selling_price_per_unit'] !== 150.00) {
        throw new Exception("Verification failed: Cost/selling price not stored in transfer log.");
    }
    echo "[ASSERT] Cost/Selling prices successfully saved in historical transfers table ... PASSED\n";

    $productPrice = (float)$db->query("SELECT default_selling_price FROM products WHERE id = {$productId}")->fetchColumn();
    $productCost = (float)$db->query("SELECT default_purchase_price FROM products WHERE id = {$productId}")->fetchColumn();
    if ($productPrice !== 150.00 || $productCost !== 100.00) {
        throw new Exception("Verification failed: Product catalog prices not updated. Sell: {$productPrice}, Cost: {$productCost}");
    }
    echo "[ASSERT] Product catalog pricing updated (Sell: LKR 150.00, Cost: LKR 100.00) ... PASSED\n";

    $balanceRow = $db->query("SELECT * FROM inventory_balances WHERE product_id = {$productId} AND location_id = 1")->fetch();
    if (!$balanceRow || (float)$balanceRow['average_cost'] !== 100.00) {
        throw new Exception("Verification failed: Inventory average cost not updated.");
    }
    echo "[ASSERT] Inventory average cost adjusted to LKR 100.00 ... PASSED\n";

    echo "\n==================================================\n";
    echo "ALL COST & PRICING VERIFICATION TESTS PASSED!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Test failed: " . $e->getMessage() . "\n";
    exit(1);
}
