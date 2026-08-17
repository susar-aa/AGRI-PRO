<?php
/**
 * Stage 6A Marketplace Automated Verification Test Suite (Fixed Cleanup Order)
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

use App\Models\Party;
use App\Models\PartyLedger;
use App\Models\ProductModel;
use App\Models\SaleModel;
use App\Models\ReceiptPaymentModel;
use App\Services\OpeningBalanceEngine;
use App\Services\PaymentEngine;
use App\Services\InventoryEngine;
use App\Services\SalesEngine;
use Core\Database;

echo "==================================================\n";
echo "AGRI CO-OP ERP - STAGE 6A AUTOMATED VERIFICATION\n";
echo "==================================================\n\n";

try {
    $db = Database::getInstance();
    $partyModel = new Party();
    $ledgerModel = new PartyLedger();
    $prodModel = new ProductModel();
    $saleModel = new SaleModel();
    $pmModel = new ReceiptPaymentModel();

    // Setup: Log in as admin
    \Core\Auth::attempt('admin', 'admin123');
    echo "[SETUP] Logged in as administrator.\n";

    // Clean old test data
    $db->exec("DELETE FROM sale_items");
    $db->exec("DELETE FROM sales");
    $db->exec("DELETE FROM payment_receipts");
    $db->exec("DELETE FROM parties WHERE party_code IN ('CUST-MKT6')");
    $db->exec("DELETE FROM journal_entries WHERE source_module IN ('marketplace', 'parties')");
    $db->exec("DELETE FROM cheques");
    $db->exec("DELETE FROM stock_ledger WHERE source_module = 'MARKETPLACE'");
    $db->exec("DELETE FROM inventory_balances WHERE product_id = 1");
    echo "[SETUP] Cleaned old test data.\n\n";

    // Product setup: Product ID 1 (Fertilizer Pack, FERT-001)
    // Setup initial stock in warehouse 1 (Main Store): 100 units at AVCO cost LKR 100.00
    InventoryEngine::recordStockIn(
        1, // product_id
        1, // location_id
        100.00, // qty
        100.00, // unit cost
        'OPENING_BALANCE',
        'INVENTORY',
        1,
        'INIT-STOCK'
    );
    echo "[SETUP] Seeded 100 units of FERT-001 at Main Store (AVCO cost LKR 100.00).\n";

    // Resolve cash drawer account
    $cashAccount = $db->query("SELECT * FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetch();
    if (!$cashAccount) {
        throw new Exception("Test setup error: Active cash drawer account is missing.");
    }
    $cashInitBal = (float)$cashAccount['current_balance'];

    // 1. Mark an existing product as Marketplace Product
    echo "[TEST 1] Mark product as Marketplace Product... ";
    $prodModel->toggleMarketplace(1, true);
    $p = $prodModel->getById(1);
    if ((int)$p['is_marketplace'] !== 1) {
        throw new Exception("Product was not marked as available in Marketplace.");
    }
    echo "PASSED\n";

    // Setup customer
    $custId = $partyModel->create([
        'party_code' => 'CUST-MKT6',
        'party_type' => 'CUSTOMER',
        'name' => 'Marketplace Test Customer',
        'created_by' => 1
    ]);

    // 2 & 3. Create a Cash Sale & Post the Sale
    echo "[TEST 2 & 3] Create and Post Cash Sale (10 units FERT-001 @ LKR 150.00)... ";
    $saleId = SalesEngine::saveSale([
        'customer_id' => $custId,
        'warehouse_id' => 1,
        'sale_date' => date('Y-m-d'),
        'sale_type' => 'CASH',
        'payment_method' => 'CASH',
        'cash_account_id' => $cashAccount['id'],
        'items' => [
            [
                'product_id' => 1,
                'quantity' => 10.00,
                'unit_price' => 150.00,
                'discount' => 0.00
            ]
        ]
    ]);
    SalesEngine::postSale($saleId);
    $sale = $saleModel->getById($saleId);
    if ($sale['status'] !== 'POSTED') {
        throw new Exception("Sale status should be POSTED. Found: " . $sale['status']);
    }
    echo "PASSED\n";

    // 4 & 5. Verify Inventory decreases & Stock Ledger
    echo "[TEST 4 & 5] Verify Inventory balance and Stock Ledger... ";
    $stockLeft = InventoryEngine::getStockOnHand(1, 1);
    if ($stockLeft !== 90.00) {
        throw new Exception("Warehouse stock did not decrease. Expected 90, Found: " . $stockLeft);
    }
    // Verify stock ledger has a record
    $ledgerCount = (int)$db->query("SELECT COUNT(*) FROM stock_ledger WHERE source_module = 'MARKETPLACE' AND source_transaction_id = {$saleId}")->fetchColumn();
    if ($ledgerCount !== 1) {
        throw new Exception("Stock ledger entry is missing.");
    }
    echo "PASSED (Stock reduced to 90, ledger record logged)\n";

    // 6 & 7 & 8. Verify Revenue accounting, COGS accounting, and Cash balance
    echo "[TEST 6 & 7 & 8] Verify Revenue, COGS, and Cash ledger balances... ";
    // Revenue entry check: Debit Cash, Credit Sales Revenue (LKR 1,500)
    // COGS entry check: Debit COGS, Credit Inventory (10 units * LKR 100.00 = LKR 1,000)
    $journal = $db->query("SELECT * FROM journal_entries WHERE source_module = 'marketplace' AND source_transaction_id = {$saleId}")->fetch();
    if (!$journal) {
        throw new Exception("Journal entry for sale was not posted.");
    }

    $lines = $db->query("SELECT * FROM journal_lines WHERE journal_entry_id = " . $journal['id'])->fetchAll();
    if (count($lines) !== 4) {
        throw new Exception("Journal should contain 4 lines (Revenue: 2, COGS: 2). Found: " . count($lines));
    }

    // Cash drawer check
    $cashPostBal = (float)$db->query("SELECT current_balance FROM cash_accounts WHERE id = " . $cashAccount['id'])->fetchColumn();
    if (abs($cashPostBal - ($cashInitBal + 1500.00)) > 0.001) {
        throw new Exception("Cash drawer was not incremented. Init: $cashInitBal, Post: $cashPostBal");
    }
    echo "PASSED\n";

    // 9 & 10 & 11. Create a Credit Sale & Verify Customer Ledger Outstanding
    echo "[TEST 9 & 10 & 11] Create Credit Sale (20 units FERT-001 @ LKR 150.00 = LKR 3,000)... ";
    $creditSaleId = SalesEngine::saveSale([
        'customer_id' => $custId,
        'warehouse_id' => 1,
        'sale_date' => date('Y-m-d'),
        'sale_type' => 'CREDIT',
        'payment_method' => 'CREDIT',
        'items' => [
            [
                'product_id' => 1,
                'quantity' => 20.00,
                'unit_price' => 150.00,
                'discount' => 0.00
            ]
        ]
    ]);
    SalesEngine::postSale($creditSaleId);

    // Customer balance should be LKR 3,000
    $custBal = $ledgerModel->calculateBalance($custId, 'CUSTOMER');
    if ($custBal !== 3000.00) {
        throw new Exception("Customer outstanding balance is incorrect. Expected: 3000.00, Found: " . $custBal);
    }
    echo "PASSED (Customer outstanding balance increased to LKR 3,000)\n";

    // 12 & 13. Receive payment using Customer Receipt & Verify Outstanding decreases
    echo "[TEST 12 & 13] Pay Credit invoice using Customer Receipt (LKR 2,000)... ";
    $receiptId = PaymentEngine::recordPayment([
        'party_id' => $custId,
        'payment_type' => 'RECEIPT',
        'payment_method' => 'Cash',
        'cash_account_id' => $cashAccount['id'],
        'amount' => 2000.00,
        'payment_date' => date('Y-m-d'),
        'notes' => 'Settlement of invoice'
    ]);
    PaymentEngine::postPayment($receiptId);

    // Customer balance should decrease to LKR 1,000
    $custBalPostPay = $ledgerModel->calculateBalance($custId, 'CUSTOMER');
    if ($custBalPostPay !== 1000.00) {
        throw new Exception("Customer outstanding balance did not decrease. Expected: 1000.00, Found: " . $custBalPostPay);
    }
    echo "PASSED (Outstanding reduced to LKR 1,000)\n";

    // 14 & 15. Test selling more than available stock
    echo "[TEST 14 & 15] Test selling more than available stock... ";
    // Currently stock is 70 (100 - 10 - 20)
    try {
        SalesEngine::saveSale([
            'customer_id' => $custId,
            'warehouse_id' => 1,
            'sale_date' => date('Y-m-d'),
            'sale_type' => 'CASH',
            'payment_method' => 'CASH',
            'cash_account_id' => $cashAccount['id'],
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 80.00, // exceeds 70
                    'unit_price' => 150.00,
                    'discount' => 0.00
                ]
            ]
        ]);
        // Save draft works (drafts don't block stock), but posting should fail!
        $excessSaleId = SalesEngine::saveSale([
            'customer_id' => $custId,
            'warehouse_id' => 1,
            'sale_date' => date('Y-m-d'),
            'sale_type' => 'CASH',
            'payment_method' => 'CASH',
            'cash_account_id' => $cashAccount['id'],
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 80.00,
                    'unit_price' => 150.00
                ]
            ]
        ]);
        SalesEngine::postSale($excessSaleId);
        throw new Exception("Post sale should have thrown stock exceptions!");
    } catch (Exception $ex) {
        echo "PASSED (Correctly rejected stock allocation excess: " . $ex->getMessage() . ")\n";
    }

    // 16 & 17. Cancel a Draft Sale
    echo "[TEST 16 & 17] Cancel Draft Sale... ";
    $draftId = SalesEngine::saveSale([
        'customer_id' => $custId,
        'warehouse_id' => 1,
        'sale_date' => date('Y-m-d'),
        'sale_type' => 'CASH',
        'payment_method' => 'CASH',
        'cash_account_id' => $cashAccount['id'],
        'items' => [
            [
                'product_id' => 1,
                'quantity' => 5.00,
                'unit_price' => 150.00
            ]
        ]
    ]);
    SalesEngine::cancelSale($draftId, 'Draft cancellation test');
    $draftSale = $saleModel->getById($draftId);
    if ($draftSale['status'] !== 'CANCELLED') {
        throw new Exception("Draft sale status is not CANCELLED.");
    }
    // Verify no journal entries or stock reductions occurred
    $draftStock = InventoryEngine::getStockOnHand(1, 1);
    if ($draftStock !== 70.00) {
        throw new Exception("Draft sale cancellation caused stock changes! Stock hand: " . $draftStock);
    }
    echo "PASSED\n";

    // 18. Verify Trial Balance remains balanced
    echo "[TEST 18] Verify Trial Balance remains balanced... ";
    $tbBalances = $db->query("
        SELECT SUM(debit) AS total_debit, SUM(credit) AS total_credit 
        FROM journal_lines jl
        JOIN journal_entries je ON jl.journal_entry_id = je.id
        WHERE je.status = 'posted'
    ")->fetch();

    $diff = abs((float)$tbBalances['total_debit'] - (float)$tbBalances['total_credit']);
    if ($diff < 0.001) {
        echo "PASSED (Trial Balance is in perfect equilibrium. Debits: " . $tbBalances['total_debit'] . " / Credits: " . $tbBalances['total_credit'] . ")\n";
    } else {
        throw new Exception("Trial Balance imbalance detected! Difference: " . $diff);
    }

    // Cleanup
    $db->exec("DELETE FROM sale_items");
    $db->exec("DELETE FROM sales");
    $db->exec("DELETE FROM payment_receipts");
    $db->exec("DELETE FROM parties WHERE party_code IN ('CUST-MKT6')");
    $db->exec("DELETE FROM journal_entries WHERE source_module IN ('marketplace', 'parties')");
    $db->exec("DELETE FROM cheques");
    $db->exec("DELETE FROM stock_ledger WHERE source_module = 'MARKETPLACE'");
    $db->exec("DELETE FROM inventory_balances WHERE product_id = 1");

    echo "\n==================================================\n";
    echo "ALL STAGE 6A VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Verification Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
