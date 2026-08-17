<?php
/**
 * Stage 6B Central Invoicing Automated Verification Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

use App\Models\Party;
use App\Models\PartyLedger;
use App\Models\ProductModel;
use App\Models\ServiceModel;
use App\Models\InvoiceModel;
use App\Services\InventoryEngine;
use App\Services\InvoiceEngine;
use App\Services\PaymentEngine;
use Core\Database;

echo "==================================================\n";
echo "AGRI CO-OP ERP - STAGE 6B AUTOMATED VERIFICATION\n";
echo "==================================================\n\n";

try {
    $db = Database::getInstance();
    $partyModel = new Party();
    $ledgerModel = new PartyLedger();
    $prodModel = new ProductModel();
    $srvModel = new ServiceModel();
    $invModel = new InvoiceModel();

    // Setup: Log in as admin
    \Core\Auth::attempt('admin', 'admin123');
    echo "[SETUP] Logged in as administrator.\n";

    // Clean old test data
    $db->exec("DELETE FROM invoice_items");
    $db->exec("DELETE FROM invoices");
    $db->exec("DELETE FROM payment_receipts");
    $db->exec("DELETE FROM parties WHERE party_code IN ('CUST-MKT6B')");
    $db->exec("DELETE FROM journal_entries WHERE source_module IN ('invoices', 'parties')");
    $db->exec("DELETE FROM cheques");
    $db->exec("DELETE FROM stock_ledger WHERE source_module = 'SALES_INVOICE'");
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

    // Setup customer
    $custId = $partyModel->create([
        'party_code' => 'CUST-MKT6B',
        'party_type' => 'CUSTOMER',
        'name' => 'Invoicing Test Customer',
        'created_by' => 1
    ]);

    // Service setup
    $service = $db->query("SELECT * FROM services WHERE service_code = 'SRV-PLOW'")->fetch();
    if (!$service) {
        throw new Exception("Test setup error: Service SRV-PLOW is missing.");
    }

    // --- TEST 1 ---
    echo "[TEST 1] Create Product-only Invoice (10 FERT-001 @ LKR 150.00)... ";
    $inv1 = InvoiceEngine::saveInvoice([
        'customer_id' => $custId,
        'warehouse_id' => 1,
        'invoice_date' => date('Y-m-d'),
        'payment_type' => 'CASH',
        'cash_account_id' => $cashAccount['id'],
        'items' => [
            [
                'item_type' => 'PRODUCT',
                'product_id' => 1,
                'quantity' => 10.00,
                'unit_price' => 150.00,
                'discount' => 0.00
            ]
        ]
    ]);
    InvoiceEngine::postInvoice($inv1);

    // Verify stock decreased to 90
    $stock1 = InventoryEngine::getStockOnHand(1, 1);
    if ($stock1 !== 90.00) {
        throw new Exception("Stock did not decrease to 90. Found: " . $stock1);
    }
    // Verify journal posted
    $je1 = $db->query("SELECT COUNT(*) FROM journal_entries WHERE source_module = 'invoices' AND source_transaction_id = {$inv1}")->fetchColumn();
    if ($je1 != 1) {
        throw new Exception("Journal entry not posted for Product-only invoice.");
    }
    echo "PASSED\n";

    // --- TEST 2 ---
    echo "[TEST 2] Create Service-only Invoice (2 Paddy Field Plowing @ LKR 12,500.00)... ";
    $inv2 = InvoiceEngine::saveInvoice([
        'customer_id' => $custId,
        'invoice_date' => date('Y-m-d'),
        'payment_type' => 'CASH',
        'cash_account_id' => $cashAccount['id'],
        'items' => [
            [
                'item_type' => 'SERVICE',
                'service_id' => $service['id'],
                'quantity' => 2.00,
                'unit_price' => 12500.00
            ]
        ]
    ]);
    InvoiceEngine::postInvoice($inv2);

    // Verify stock did not change (remains 90)
    $stock2 = InventoryEngine::getStockOnHand(1, 1);
    if ($stock2 !== 90.00) {
        throw new Exception("Stock changed unexpectedly for service invoice. Found: " . $stock2);
    }
    echo "PASSED\n";

    // --- TEST 3 ---
    echo "[TEST 3] Create Mixed Invoice (10 FERT-001 @ LKR 150.00 + 1 Paddy Field Plowing @ LKR 12,500.00)... ";
    $inv3 = InvoiceEngine::saveInvoice([
        'customer_id' => $custId,
        'warehouse_id' => 1,
        'invoice_date' => date('Y-m-d'),
        'payment_type' => 'CASH',
        'cash_account_id' => $cashAccount['id'],
        'items' => [
            [
                'item_type' => 'PRODUCT',
                'product_id' => 1,
                'quantity' => 10.00,
                'unit_price' => 150.00
            ],
            [
                'item_type' => 'SERVICE',
                'service_id' => $service['id'],
                'quantity' => 1.00,
                'unit_price' => 12500.00
            ]
        ]
    ]);
    InvoiceEngine::postInvoice($inv3);

    // Stock should be 80 now (decreased by 10 products, no decrease for services)
    $stock3 = InventoryEngine::getStockOnHand(1, 1);
    if ($stock3 !== 80.00) {
        throw new Exception("Mixed invoice stock deduction failed. Stock: " . $stock3);
    }
    echo "PASSED\n";

    // --- TEST 4 ---
    echo "[TEST 4] Create Credit Invoice (LKR 5,000.00)... ";
    $inv4 = InvoiceEngine::saveInvoice([
        'customer_id' => $custId,
        'invoice_date' => date('Y-m-d'),
        'payment_type' => 'CREDIT',
        'items' => [
            [
                'item_type' => 'SERVICE',
                'service_id' => $service['id'],
                'quantity' => 1.00,
                'unit_price' => 5000.00
            ]
        ]
    ]);
    InvoiceEngine::postInvoice($inv4);

    // Customer balance should be LKR 5,000
    $custBal = $ledgerModel->calculateBalance($custId, 'CUSTOMER');
    if ($custBal !== 5000.00) {
        throw new Exception("Customer outstanding balance is incorrect. Expected: 5000.00, Found: " . $custBal);
    }
    echo "PASSED\n";

    // --- TEST 5 ---
    echo "[TEST 5] Receive payment of LKR 3,000.00 using Customer Receipt... ";
    $receiptId = PaymentEngine::recordPayment([
        'party_id' => $custId,
        'payment_type' => 'RECEIPT',
        'payment_method' => 'Cash',
        'cash_account_id' => $cashAccount['id'],
        'amount' => 3000.00,
        'payment_date' => date('Y-m-d')
    ]);
    PaymentEngine::postPayment($receiptId);

    // Balance should decrease to LKR 2,000
    $custBal5 = $ledgerModel->calculateBalance($custId, 'CUSTOMER');
    if ($custBal5 !== 2000.00) {
        throw new Exception("Customer outstanding balance did not decrease. Expected: 2000.00, Found: " . $custBal5);
    }
    echo "PASSED\n";

    // --- TEST 6 ---
    echo "[TEST 6] Cancel Draft Invoice... ";
    $inv6 = InvoiceEngine::saveInvoice([
        'customer_id' => $custId,
        'invoice_date' => date('Y-m-d'),
        'payment_type' => 'CREDIT',
        'items' => [
            [
                'item_type' => 'SERVICE',
                'service_id' => $service['id'],
                'quantity' => 1.00,
                'unit_price' => 500.00
            ]
        ]
    ]);
    InvoiceEngine::cancelInvoice($inv6, 'Testing draft cancel');
    $cancelInv = $invModel->getById($inv6);
    if ($cancelInv['status'] !== 'CANCELLED') {
        throw new Exception("Draft status was not updated to CANCELLED.");
    }
    echo "PASSED\n";

    // --- TEST 7 ---
    echo "[TEST 7] Reverse Posted Invoice... ";
    // Let's reverse the Credit invoice $inv4 (Total: 5000.00)
    // Currently customer outstanding is 2000.00 (from 5000 credit, minus 3000 receipt)
    // Reversing the 5000 credit invoice should reverse the 5000 debit on customer, 
    // making customer ledger outstanding balance -3000.00 (an overpayment/advance)!
    InvoiceEngine::cancelInvoice($inv4, 'Reversal test');

    $custBal7 = $ledgerModel->calculateBalance($custId, 'CUSTOMER');
    if ($custBal7 !== -3000.00) {
        throw new Exception("Reversal did not update customer outstanding. Expected: -3000.00, Found: " . $custBal7);
    }
    echo "PASSED\n";

    // --- TEST 8 ---
    echo "[TEST 8] Verify Trial Balance is in equilibrium... ";
    $tbBalances = $db->query("
        SELECT SUM(debit) AS total_debit, SUM(credit) AS total_credit 
        FROM journal_lines jl
        JOIN journal_entries je ON jl.journal_entry_id = je.id
        WHERE je.status = 'posted'
    ")->fetch();

    $diff = abs((float)$tbBalances['total_debit'] - (float)$tbBalances['total_credit']);
    if ($diff < 0.001) {
        echo "PASSED (Debits: " . $tbBalances['total_debit'] . " / Credits: " . $tbBalances['total_credit'] . ")\n";
    } else {
        throw new Exception("Trial Balance out of balance by: " . $diff);
    }

    // Cleanup
    $db->exec("DELETE FROM invoice_items");
    $db->exec("DELETE FROM invoices");
    $db->exec("DELETE FROM payment_receipts");
    $db->exec("DELETE FROM parties WHERE party_code IN ('CUST-MKT6B')");
    $db->exec("DELETE FROM journal_entries WHERE source_module IN ('invoices', 'parties')");
    $db->exec("DELETE FROM cheques");
    $db->exec("DELETE FROM stock_ledger WHERE source_module = 'SALES_INVOICE'");
    $db->exec("DELETE FROM inventory_balances WHERE product_id = 1");

    echo "\n==================================================\n";
    echo "ALL STAGE 6B VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Verification Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
