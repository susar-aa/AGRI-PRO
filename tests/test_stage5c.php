<?php
/**
 * Stage 5C Receipts and Payments Verification Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

use App\Models\Party;
use App\Models\PartyLedger;
use App\Models\ReceiptPaymentModel;
use App\Services\OpeningBalanceEngine;
use App\Services\PaymentEngine;
use Core\Database;

echo "==================================================\n";
echo "AGRI CO-OP ERP - STAGE 5C AUTOMATED VERIFICATION\n";
echo "==================================================\n\n";

try {
    $db = Database::getInstance();
    $partyModel = new Party();
    $ledgerModel = new PartyLedger();
    $pmModel = new ReceiptPaymentModel();

    // 1. Setup: Log in as admin
    \Core\Auth::attempt('admin', 'admin123');
    echo "[SETUP] Logged in as administrator.\n";

    // Clean old test data
    $db->exec("DELETE FROM parties WHERE party_code IN ('PTY-C5C', 'PTY-S5C')");
    $db->exec("DELETE FROM journal_entries WHERE source_module = 'parties'");
    $db->exec("DELETE FROM payment_receipts");
    echo "[SETUP] Cleaned old test data.\n\n";

    // Prepare cash account drawer and bank account drawer for testing
    // Cash Drawer ID 1, Bank Account ID 1
    $cashDrawer = $db->query("SELECT * FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetch();
    $bankDrawer = $db->query("SELECT * FROM bank_accounts WHERE status = 'active' LIMIT 1")->fetch();

    if (!$cashDrawer || !$bankDrawer) {
        throw new Exception("Test requirements failed: Active cash/bank accounts are missing.");
    }

    $cashInitBal = (float)$cashDrawer['current_balance'];
    $bankInitBal = (float)$bankDrawer['current_balance'];

    // ----------------------------------------------------
    // CUSTOMER TESTING FLOW
    // ----------------------------------------------------
    echo "--- CUSTOMER RECEIPTS TESTING ---\n";
    
    // 1. Customer initial balance = LKR 50,000 (via Opening Balance)
    $custId = $partyModel->create([
        'party_code' => 'PTY-C5C',
        'party_type' => 'CUSTOMER',
        'name' => 'Stage 5C Customer',
        'created_by' => 1
    ]);
    OpeningBalanceEngine::postOpeningBalance([
        'party_id' => $custId,
        'type' => 'receivable',
        'amount' => 50000.00,
        'balance_date' => date('Y-m-d'),
        'description' => 'Customer Stage 5C Opening Receivable'
    ]);
    $initCustBal = $ledgerModel->calculateBalance($custId, 'CUSTOMER');
    if ($initCustBal !== 50000.00) {
        throw new Exception("Initial customer balance setup failed.");
    }
    echo "1. Initial customer balance established: LKR " . number_format($initCustBal, 2) . "\n";

    // 2. Create customer receipt = LKR 20,000 as DRAFT
    $recId = PaymentEngine::recordPayment([
        'party_id' => $custId,
        'payment_type' => 'RECEIPT',
        'payment_method' => 'Cash',
        'cash_account_id' => $cashDrawer['id'],
        'amount' => 20000.00,
        'payment_date' => date('Y-m-d'),
        'notes' => 'Test Receipt 20k'
    ]);
    echo "2. Customer receipt created (ID: $recId, Amount: LKR 20,000, Method: Cash)\n";

    // 3 & 4. Verify balance remains LKR 50,000 while receipt is in DRAFT
    $custBalDraft = $ledgerModel->calculateBalance($custId, 'CUSTOMER');
    if ($custBalDraft !== 50000.00) {
        throw new Exception("DRAFT receipt affected customer balance!");
    }
    echo "3 & 4. Confirmed: Customer balance remains LKR " . number_format($custBalDraft, 2) . " while receipt is DRAFT.\n";

    // 5 & 6. Post customer receipt and verify balance = LKR 30,000
    PaymentEngine::postPayment($recId);
    $custBalPost = $ledgerModel->calculateBalance($custId, 'CUSTOMER');
    if ($custBalPost !== 30000.00) {
        throw new Exception("Customer balance after posting receipt is incorrect: " . $custBalPost);
    }
    echo "5 & 6. Confirmed: Customer balance is LKR " . number_format($custBalPost, 2) . " after posting.\n";

    // 7. Verify accounting entry (Dr Cash account / Cr Accounts Receivable)
    $postedRec = $pmModel->getById($recId);
    $jeId = (int)$postedRec['journal_entry_id'];
    $jeLines = $db->query("SELECT * FROM journal_lines WHERE journal_entry_id = $jeId ORDER BY debit DESC")->fetchAll();
    
    if (count($jeLines) !== 2) {
        throw new Exception("Journal entry lines count is invalid.");
    }
    // Debit line: Asset cash account (ID resolves to $cashDrawer['account_id'])
    // Credit line: Accounts Receivable (ID 12)
    if ((int)$jeLines[0]['account_id'] !== (int)$cashDrawer['account_id'] || (float)$jeLines[0]['debit'] !== 20000.00) {
        throw new Exception("Journal Debit entry is incorrect.");
    }
    if ((int)$jeLines[1]['account_id'] !== 12 || (float)$jeLines[1]['credit'] !== 20000.00) {
        throw new Exception("Journal Credit entry is incorrect.");
    }
    echo "7. Confirmed: Double-entry journal checks out perfectly. Dr Cash account (ID: " . $cashDrawer['account_id'] . ") / Cr Accounts Receivable (ID: 12) for LKR 20,000.\n";

    // 8. Verify Cash/Bank balance (the cash account balance must increase by LKR 20,000)
    $cashBalPost = (float)$db->query("SELECT current_balance FROM cash_accounts WHERE id = " . $cashDrawer['id'])->fetchColumn();
    if (abs($cashBalPost - ($cashInitBal + 20000.00)) > 0.001) {
        throw new Exception("Cash drawer account balance did not update correctly: " . $cashBalPost);
    }
    echo "8. Confirmed: Cash drawer balance updated. Old: " . $cashInitBal . " / New: " . $cashBalPost . " (+LKR 20,000)\n";

    // 9 & 10. Reverse receipt and verify customer balance returns to LKR 50,000
    PaymentEngine::reversePayment($recId, 'Reversal test customer receipt');
    $custBalPostRev = $ledgerModel->calculateBalance($custId, 'CUSTOMER');
    if ($custBalPostRev !== 50000.00) {
        throw new Exception("Customer balance did not restore after reversal: " . $custBalPostRev);
    }
    $cashBalPostRev = (float)$db->query("SELECT current_balance FROM cash_accounts WHERE id = " . $cashDrawer['id'])->fetchColumn();
    if (abs($cashBalPostRev - $cashInitBal) > 0.001) {
        throw new Exception("Cash drawer balance did not restore after reversal: " . $cashBalPostRev);
    }
    echo "9 & 10. Confirmed: Customer balance restored to LKR " . number_format($custBalPostRev, 2) . " and Cash drawer balance returned to " . $cashBalPostRev . " after reversal.\n\n";

    // ----------------------------------------------------
    // SUPPLIER TESTING FLOW
    // ----------------------------------------------------
    echo "--- SUPPLIER PAYMENTS TESTING ---\n";

    // 11. Supplier initial balance = LKR 100,000 (via Opening Balance)
    $suppId = $partyModel->create([
        'party_code' => 'PTY-S5C',
        'party_type' => 'SUPPLIER',
        'name' => 'Stage 5C Supplier',
        'created_by' => 1
    ]);
    OpeningBalanceEngine::postOpeningBalance([
        'party_id' => $suppId,
        'type' => 'payable',
        'amount' => 100000.00,
        'balance_date' => date('Y-m-d'),
        'description' => 'Supplier Stage 5C Opening Payable'
    ]);
    $initSuppBal = $ledgerModel->calculateBalance($suppId, 'SUPPLIER');
    if ($initSuppBal !== 100000.00) {
        throw new Exception("Initial supplier balance setup failed.");
    }
    echo "11. Initial supplier balance established: LKR " . number_format($initSuppBal, 2) . "\n";

    // 12. Create supplier payment = LKR 40,000 as DRAFT
    $payId = PaymentEngine::recordPayment([
        'party_id' => $suppId,
        'payment_type' => 'PAYMENT',
        'payment_method' => 'Bank Transfer',
        'bank_account_id' => $bankDrawer['id'],
        'amount' => 40000.00,
        'payment_date' => date('Y-m-d'),
        'notes' => 'Test Payment 40k'
    ]);
    echo "12. Supplier payment created (ID: $payId, Amount: LKR 40,000, Method: Bank)\n";

    // 13 & 14. Verify balance remains LKR 100,000 while payment is in DRAFT
    $suppBalDraft = $ledgerModel->calculateBalance($suppId, 'SUPPLIER');
    if ($suppBalDraft !== 100000.00) {
        throw new Exception("DRAFT payment affected supplier balance!");
    }
    echo "13 & 14. Confirmed: Supplier balance remains LKR " . number_format($suppBalDraft, 2) . " while payment is DRAFT.\n";

    // 15 & 16. Post supplier payment and verify balance = LKR 60,000
    PaymentEngine::postPayment($payId);
    $suppBalPost = $ledgerModel->calculateBalance($suppId, 'SUPPLIER');
    if ($suppBalPost !== 60000.00) {
        throw new Exception("Supplier balance after posting payment is incorrect: " . $suppBalPost);
    }
    echo "15 & 16. Confirmed: Supplier balance is LKR " . number_format($suppBalPost, 2) . " after posting.\n";

    // 17. Verify accounting entry (Dr Accounts Payable / Cr Bank account)
    $postedPay = $pmModel->getById($payId);
    $jeIdPay = (int)$postedPay['journal_entry_id'];
    $jeLinesPay = $db->query("SELECT * FROM journal_lines WHERE journal_entry_id = $jeIdPay ORDER BY debit DESC")->fetchAll();
    
    if (count($jeLinesPay) !== 2) {
        throw new Exception("Journal entry lines count is invalid.");
    }
    // Debit line: Accounts Payable (ID 20)
    // Credit line: Asset bank account (ID resolves to $bankDrawer['account_id'])
    if ((int)$jeLinesPay[0]['account_id'] !== 20 || (float)$jeLinesPay[0]['debit'] !== 40000.00) {
        throw new Exception("Journal Debit entry is incorrect.");
    }
    if ((int)$jeLinesPay[1]['account_id'] !== (int)$bankDrawer['account_id'] || (float)$jeLinesPay[1]['credit'] !== 40000.00) {
        throw new Exception("Journal Credit entry is incorrect.");
    }
    echo "17. Confirmed: Double-entry journal checks out perfectly. Dr Accounts Payable (ID: 20) / Cr Bank account (ID: " . $bankDrawer['account_id'] . ") for LKR 40,000.\n";

    // 18. Verify Bank balance (the bank account balance must decrease by LKR 40,000)
    $bankBalPost = (float)$db->query("SELECT current_balance FROM bank_accounts WHERE id = " . $bankDrawer['id'])->fetchColumn();
    if (abs($bankBalPost - ($bankInitBal - 40000.00)) > 0.001) {
        throw new Exception("Bank account balance did not update correctly: " . $bankBalPost);
    }
    echo "18. Confirmed: Bank account balance updated. Old: " . $bankInitBal . " / New: " . $bankBalPost . " (-LKR 40,000)\n";

    // 19 & 20. Reverse payment and verify supplier balance returns to LKR 100,000
    PaymentEngine::reversePayment($payId, 'Reversal test supplier payment');
    $suppBalPostRev = $ledgerModel->calculateBalance($suppId, 'SUPPLIER');
    if ($suppBalPostRev !== 100000.00) {
        throw new Exception("Supplier balance did not restore after reversal: " . $suppBalPostRev);
    }
    $bankBalPostRev = (float)$db->query("SELECT current_balance FROM bank_accounts WHERE id = " . $bankDrawer['id'])->fetchColumn();
    if (abs($bankBalPostRev - $bankInitBal) > 0.001) {
        throw new Exception("Bank account balance did not restore after reversal: " . $bankBalPostRev);
    }
    echo "19 & 20. Confirmed: Supplier balance restored to LKR " . number_format($suppBalPostRev, 2) . " and Bank balance returned to " . $bankBalPostRev . " after reversal.\n\n";

    // 21. Verify Trial Balance: Total Debits = Total Credits
    $tbBalances = $db->query("
        SELECT SUM(debit) AS total_debit, SUM(credit) AS total_credit 
        FROM journal_lines jl
        JOIN journal_entries je ON jl.journal_entry_id = je.id
        WHERE je.status = 'posted'
    ")->fetch();

    $diff = abs((float)$tbBalances['total_debit'] - (float)$tbBalances['total_credit']);
    if ($diff < 0.001) {
        echo "21. Confirmed: Trial Balance is in perfect equilibrium. Debits: " . $tbBalances['total_debit'] . " / Credits: " . $tbBalances['total_credit'] . "\n";
    } else {
        throw new Exception("Trial Balance imbalance detected! Difference: " . $diff);
    }

    // 22. Verify Stage 1–4 remains functional
    $usersCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $categoriesCount = $db->query("SELECT COUNT(*) FROM product_categories")->fetchColumn();
    if ($usersCount > 0 && $categoriesCount > 0) {
        echo "22. Confirmed: Stage 1–4 directories are responsive.\n";
    } else {
        throw new Exception("Stage 1-4 directories query failed.");
    }

    // Cleanup test data
    $db->exec("DELETE FROM parties WHERE party_code IN ('PTY-C5C', 'PTY-S5C')");
    $db->exec("DELETE FROM journal_entries WHERE source_module = 'parties'");
    $db->exec("DELETE FROM payment_receipts");

    echo "\n==================================================\n";
    echo "ALL STAGE 5C VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Verification Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
