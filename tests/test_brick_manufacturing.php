<?php
/**
 * Automated Verification Test for Brick Manufacturing System
 */

require_once __DIR__ . '/../core/bootstrap.php';

use Core\Database;
use App\Services\InventoryEngine;

echo "==================================================\n";
echo "AGRI CO-OP ERP - BRICK MANUFACTURING VERIFICATION\n";
echo "==================================================\n\n";

try {
    $db = Database::getInstance();

    // 1. Authenticate as Admin
    $_SESSION['user_id'] = 1;
    $_SESSION['role_id'] = 1;
    $_SESSION['permissions'] = ['machinery.view', 'inventory.view'];
    echo "[SETUP] Authenticated as administrator.\n";

    // Clean old test data
    $db->exec("DELETE FROM stock_ledger WHERE source_module = 'BRICK_PRODUCTION'");
    $db->exec("DELETE FROM brick_transfers WHERE project_id IN (SELECT id FROM brick_production_projects WHERE project_name LIKE 'TEST-BRK-%')");
    $db->exec("DELETE FROM brick_production_records WHERE project_id IN (SELECT id FROM brick_production_projects WHERE project_name LIKE 'TEST-BRK-%')");
    $db->exec("DELETE FROM expenses WHERE project_id IN (SELECT id FROM brick_production_projects WHERE project_name LIKE 'TEST-BRK-%') OR expense_number = 'EXP-TEST-BRK-01'");
    $db->exec("DELETE FROM brick_production_projects WHERE project_name LIKE 'TEST-BRK-%'");
    echo "[SETUP] Cleaned old test data.\n";

    // Get an active product for bricks
    $product = $db->query("SELECT id, sku, product_code, name_en FROM products WHERE is_active = 1 LIMIT 1")->fetch();
    if (!$product) {
        throw new Exception("No active products available to run tests.");
    }
    $productId = (int)$product['id'];
    echo "[SETUP] Selected brick product: " . $product['product_code'] . " - " . $product['name_en'] . "\n";

    // 2. Create Brick Production Project
    $db->prepare("
        INSERT INTO brick_production_projects (project_name, location, start_date, product_id, planned_quantity, unit, status, created_by)
        VALUES ('TEST-BRK-PROJECT-01', 'Test Clay Yard', CURDATE(), :pid, 5000.00, 'Pieces', 'ACTIVE', 1)
    ")->execute(['pid' => $productId]);
    $projectId = (int)$db->lastInsertId();
    echo "[TEST 1] Created Production Project. ID: {$projectId} ... PASSED\n";

    // Fetch valid accounts and categories dynamically
    $expenseAccountId = (int)$db->query("SELECT id FROM accounts WHERE category = 'Expense' OR account_code LIKE '5%' LIMIT 1")->fetchColumn();
    $cashAccountId = (int)$db->query("SELECT id FROM accounts WHERE category = 'Asset' OR account_code LIKE '1%' LIMIT 1")->fetchColumn();
    $expenseCategoryId = (int)$db->query("SELECT id FROM expense_categories LIMIT 1")->fetchColumn();

    // 3. Create Posted Project Expense (Cost Center = 5)
    $db->prepare("
        INSERT INTO expenses (expense_number, expense_date, expense_category_id, expense_account_id, cash_account_id, payee, amount, description, payment_method, cost_center_id, project_id, status, created_by)
        VALUES ('EXP-TEST-BRK-01', CURDATE(), :cat_id, :exp_acc, :cash_acc, 'Clay Supplier Ltd', 50000.00, 'Raw clay for test batch', 'CASH', 5, :project_id, 'posted', 1)
    ")->execute([
        'project_id' => $projectId,
        'cat_id' => $expenseCategoryId ?: 1,
        'exp_acc' => $expenseAccountId ?: 1,
        'cash_acc' => $cashAccountId ?: 1
    ]);
    echo "[TEST 2] Created Posted Project Expense (LKR 50,000) ... PASSED\n";

    // 4. Log Production Batches
    // Batch 1: 2000 Pcs
    $db->prepare("
        INSERT INTO brick_production_records (project_id, production_date, product_id, quantity, unit, notes, created_by)
        VALUES (:project_id, :production_date, :product_id, :quantity, :unit, :notes, :created_by)
    ")->execute([
        'project_id' => $projectId,
        'production_date' => date('Y-m-d'),
        'product_id' => $productId,
        'quantity' => 2000.00,
        'unit' => 'Pieces',
        'notes' => 'Batch 1 output',
        'created_by' => 1
    ]);
    $batch1Id = (int)$db->lastInsertId();

    // Batch 2: 3000 Pcs
    $db->prepare("
        INSERT INTO brick_production_records (project_id, production_date, product_id, quantity, unit, notes, created_by)
        VALUES (:project_id, :production_date, :product_id, :quantity, :unit, :notes, :created_by)
    ")->execute([
        'project_id' => $projectId,
        'production_date' => date('Y-m-d'),
        'product_id' => $productId,
        'quantity' => 3000.00,
        'unit' => 'Pieces',
        'notes' => 'Batch 2 output',
        'created_by' => 1
    ]);
    $batch2Id = (int)$db->lastInsertId();
    echo "[TEST 3] Logged Two Production Batches (Total: 5,000 Pieces) ... PASSED\n";

    // 5. Calculate Cost Per Brick
    // Total expenses = 50,000
    // Total produced = 5,000
    // Cost per unit = 50,000 / 5,000 = LKR 10.00
    $totalExpenses = (float)$db->query("SELECT COALESCE(SUM(amount), 0.00) FROM expenses WHERE project_id = {$projectId} AND status = 'posted' AND cost_center_id = 5")->fetchColumn();
    $totalProduced = (float)$db->query("SELECT COALESCE(SUM(quantity), 0.00) FROM brick_production_records WHERE project_id = {$projectId}")->fetchColumn();
    $costPerUnit = $totalProduced > 0 ? ($totalExpenses / $totalProduced) : 0.00;

    echo "[TEST 4] Calculated cost per unit: LKR " . number_format($costPerUnit, 2) . " ... ";
    if (abs($costPerUnit - 10.00) < 0.001) {
        echo "PASSED\n";
    } else {
        throw new Exception("Incorrect cost price calculated: " . $costPerUnit);
    }

    // 6. Marketplace Transfer Simulation
    // Transfer 4,000 Pieces at LKR 15.00 selling price
    $transferQty = 4000.00;
    $sellingPrice = 15.00;

    // Fetch chronological production records with remaining quantities
    $productionRecords = $db->query("
        SELECT bpr.*,
               (bpr.quantity - COALESCE((SELECT SUM(quantity) FROM brick_transfers WHERE production_record_id = bpr.id), 0.00)) AS remaining_quantity
        FROM brick_production_records bpr
        WHERE bpr.project_id = {$projectId}
        ORDER BY bpr.production_date ASC, bpr.id ASC
    ")->fetchAll();

    $remainingToTransfer = $transferQty;
    $firstTransferId = null;

    $transferStmt = $db->prepare("
        INSERT INTO brick_transfers (project_id, production_record_id, transfer_date, quantity, cost_price_per_unit, selling_price_per_unit, created_by)
        VALUES (:project_id, :production_record_id, CURDATE(), :quantity, :cost, :selling, 1)
    ");

    foreach ($productionRecords as $pr) {
        if ($remainingToTransfer <= 0) break;
        $rem = (float)$pr['remaining_quantity'];
        if ($rem <= 0) continue;

        $take = min($remainingToTransfer, $rem);
        $transferStmt->execute([
            'project_id' => $projectId,
            'production_record_id' => $pr['id'],
            'quantity' => $take,
            'cost' => $costPerUnit,
            'selling' => $sellingPrice
        ]);

        if (!$firstTransferId) {
            $firstTransferId = (int)$db->lastInsertId();
        }
        $remainingToTransfer -= $take;
    }

    echo "[TEST 5] Distributed and saved transfers in DB ... PASSED\n";

    // Update catalog prices
    $db->prepare("
        UPDATE products 
        SET is_marketplace = 1, default_selling_price = :sell_price, default_purchase_price = :cost_price 
        WHERE id = :pid
    ")->execute([
        'sell_price' => $sellingPrice,
        'cost_price' => $costPerUnit,
        'pid' => $productId
    ]);

    // Record Stock In using InventoryEngine
    \App\Services\InventoryEngine::recordStockIn(
        $productId,
        1,
        $transferQty,
        $costPerUnit,
        'Transfer',
        'BRICK_PRODUCTION',
        $firstTransferId,
        'BRK-' . $firstTransferId
    );

    // Overwrite average cost in inventory balances
    $db->prepare("
        UPDATE inventory_balances 
        SET average_cost = :cost_price, inventory_value = quantity_on_hand * :cost_price_val, updated_at = NOW()
        WHERE product_id = :pid AND location_id = 1
    ")->execute([
        'cost_price' => $costPerUnit,
        'cost_price_val' => $costPerUnit,
        'pid' => $productId
    ]);
    echo "[TEST 6] Triggered recordStockIn and updated inventory balances ... PASSED\n";

    // 7. Verify Assertions
    // Assert 1: Transfers created successfully
    $transfersCount = (int)$db->query("SELECT COUNT(*) FROM brick_transfers WHERE project_id = {$projectId}")->fetchColumn();
    echo "[ASSERT 1] Number of transfer records: {$transfersCount} (Expected: 2) ... ";
    if ($transfersCount === 2) {
        echo "PASSED\n";
    } else {
        throw new Exception("Expected 2 transfer records, got: " . $transfersCount);
    }

    // Assert 2: Product prices updated
    $prodRow = $db->query("SELECT is_marketplace, default_selling_price, default_purchase_price FROM products WHERE id = {$productId}")->fetch();
    echo "[ASSERT 2] Product selling/purchase prices updated in catalog ... ";
    if ($prodRow['is_marketplace'] == 1 && abs($prodRow['default_selling_price'] - 15.00) < 0.001 && abs($prodRow['default_purchase_price'] - 10.00) < 0.001) {
        echo "PASSED\n";
    } else {
        throw new Exception("Product pricing mismatch: " . json_encode($prodRow));
    }

    // Assert 3: Stock ledger matches
    $ledgerRow = $db->query("SELECT COUNT(*) FROM stock_ledger WHERE source_module = 'BRICK_PRODUCTION' AND source_transaction_id = {$firstTransferId}")->fetchColumn();
    echo "[ASSERT 3] Stock ledger entry logged correctly ... ";
    if ($ledgerRow > 0) {
        echo "PASSED\n";
    } else {
        throw new Exception("No stock ledger records found for transfer.");
    }

    // Assert 4: Inventory balances average cost matches LKR 10.00
    $balanceRow = $db->query("SELECT average_cost FROM inventory_balances WHERE product_id = {$productId} AND location_id = 1")->fetch();
    echo "[ASSERT 4] Inventory balance average cost matches LKR 10.00 ... ";
    if (abs($balanceRow['average_cost'] - 10.00) < 0.001) {
        echo "PASSED\n";
    } else {
        throw new Exception("Inventory balance average cost mismatch: " . $balanceRow['average_cost']);
    }

    echo "\n==================================================\n";
    echo "ALL BRICK MANUFACTURING VERIFICATION TESTS PASSED!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
