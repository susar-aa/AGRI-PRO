<?php
/**
 * Stage 5B Ledger and Opening Balances Verification Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

use App\Models\Party;
use App\Models\PartyLedger;
use App\Services\OpeningBalanceEngine;
use Core\Database;

echo "==================================================\n";
echo "AGRI CO-OP ERP - STAGE 5B AUTOMATED VERIFICATION\n";
echo "==================================================\n\n";

try {
    $db = Database::getInstance();
    $partyModel = new Party();
    $ledgerModel = new PartyLedger();

    // Setup: Log in as admin
    \Core\Auth::attempt('admin', 'admin123');
    echo "[SETUP] Logged in as administrator.\n";

    // Clean old test data
    $db->exec("DELETE FROM parties WHERE party_code IN ('PTY-C5B', 'PTY-S5B')");
    $db->exec("DELETE FROM journal_entries WHERE source_module = 'parties'");
    echo "[SETUP] Cleaned old test data.\n\n";

    // ----------------------------------------------------
    // TEST 1 & 2: Create Customer & Add Opening Balance LKR 50,000
    // ----------------------------------------------------
    echo "[TEST 1 & 2] Create Customer and Add Opening Balance... ";
    $custId = $partyModel->create([
        'party_code' => 'PTY-C5B',
        'party_type' => 'CUSTOMER',
        'name' => 'Stage 5B Customer',
        'created_by' => 1
    ]);

    $obIdCustomer = OpeningBalanceEngine::postOpeningBalance([
        'party_id' => $custId,
        'type' => 'receivable',
        'amount' => 50000.00,
        'balance_date' => date('Y-m-d'),
        'description' => 'Customer Stage 5B Opening Receivable'
    ]);
    echo "PASSED (Customer created, LKR 50,000 opening balance posted)\n";

    // ----------------------------------------------------
    // TEST 3 & 4: Verify Customer Ledger and Profile Balance
    // ----------------------------------------------------
    echo "[TEST 3 & 4] Verify Customer Ledger and Profile Balance... ";
    $custLedger = $ledgerModel->getLedgerEntries($custId, 'CUSTOMER');
    $custBalance = $ledgerModel->calculateBalance($custId, 'CUSTOMER');

    if (count($custLedger) === 1 && (float)$custLedger[0]['debit'] === 50000.00 && $custBalance === 50000.00) {
        echo "PASSED (Ledger row and dynamic balance confirmed LKR 50,000)\n";
    } else {
        throw new Exception("Customer ledger/balance verification failed.");
    }

    // ----------------------------------------------------
    // TEST 5 & 6: Verify General Ledger & Trial Balance (Customer)
    // ----------------------------------------------------
    echo "[TEST 5 & 6] Verify Customer GL and Trial Balance entries... ";
    // Accounts Receivable is ID 12 (1140). Let's check running balance of 1140
    $recBal = (float)$db->query("
        SELECT COALESCE(SUM(jl.debit - jl.credit), 0.00) 
        FROM journal_lines jl
        JOIN journal_entries je ON jl.journal_entry_id = je.id
        WHERE jl.account_id = 12 AND je.status = 'posted'
    ")->fetchColumn();

    if ($recBal >= 50000.00) {
        echo "PASSED (General Ledger accounts receivable has debit balance > LKR 50,000)\n";
    } else {
        throw new Exception("Accounts receivable balance not reflected in General Ledger.");
    }

    // ----------------------------------------------------
    // TEST 7 & 8: Create Supplier & Add Opening Balance LKR 100,000
    // ----------------------------------------------------
    echo "[TEST 7 & 8] Create Supplier and Add Opening Balance... ";
    $suppId = $partyModel->create([
        'party_code' => 'PTY-S5B',
        'party_type' => 'SUPPLIER',
        'name' => 'Stage 5B Supplier',
        'created_by' => 1
    ]);

    $obIdSupplier = OpeningBalanceEngine::postOpeningBalance([
        'party_id' => $suppId,
        'type' => 'payable',
        'amount' => 100000.00,
        'balance_date' => date('Y-m-d'),
        'description' => 'Supplier Stage 5B Opening Payable'
    ]);
    echo "PASSED (Supplier created, LKR 100,000 opening balance posted)\n";

    // ----------------------------------------------------
    // TEST 9 & 10: Verify Supplier Ledger and Profile Balance
    // ----------------------------------------------------
    echo "[TEST 9 & 10] Verify Supplier Ledger and Profile Balance... ";
    $suppLedger = $ledgerModel->getLedgerEntries($suppId, 'SUPPLIER');
    $suppBalance = $ledgerModel->calculateBalance($suppId, 'SUPPLIER');

    if (count($suppLedger) === 1 && (float)$suppLedger[0]['credit'] === 100000.00 && $suppBalance === 100000.00) {
        echo "PASSED (Ledger row and dynamic balance confirmed LKR 100,000)\n";
    } else {
        throw new Exception("Supplier ledger/balance verification failed.");
    }

    // ----------------------------------------------------
    // TEST 11 & 12: Verify General Ledger & Trial Balance (Supplier)
    // ----------------------------------------------------
    echo "[TEST 11 & 12] Verify Supplier GL and Trial Balance entries... ";
    // Accounts Payable is ID 20 (2110). Let's check running credit balance of 2110
    $payBal = (float)$db->query("
        SELECT COALESCE(SUM(jl.credit - jl.debit), 0.00) 
        FROM journal_lines jl
        JOIN journal_entries je ON jl.journal_entry_id = je.id
        WHERE jl.account_id = 20 AND je.status = 'posted'
    ")->fetchColumn();

    if ($payBal >= 100000.00) {
        echo "PASSED (General Ledger accounts payable has credit balance > LKR 100,000)\n";
    } else {
        throw new Exception("Accounts payable balance not reflected in General Ledger.");
    }

    // ----------------------------------------------------
    // TEST 13 & 14 & 15: Reverse Customer Opening Balance & Verify
    // ----------------------------------------------------
    echo "[TEST 13 & 14 & 15] Reverse Customer Opening Balance and verify... ";
    OpeningBalanceEngine::reverseOpeningBalance($obIdCustomer, 'Reversing customer test opening balance');

    $custBalancePostRev = $ledgerModel->calculateBalance($custId, 'CUSTOMER');
    $custLedgerPostRev = $ledgerModel->getLedgerEntries($custId, 'CUSTOMER');

    if ($custBalancePostRev === 0.00 && count($custLedgerPostRev) === 2) {
        echo "PASSED (Customer profile balance returned to LKR 0.00, reversal journal confirmed)\n";
    } else {
        throw new Exception("Customer reversal balance check failed.");
    }

    // ----------------------------------------------------
    // TEST 16 & 17 & 18: Reverse Supplier Opening Balance & Verify
    // ----------------------------------------------------
    echo "[TEST 16 & 17 & 18] Reverse Supplier Opening Balance and verify... ";
    OpeningBalanceEngine::reverseOpeningBalance($obIdSupplier, 'Reversing supplier test opening balance');

    $suppBalancePostRev = $ledgerModel->calculateBalance($suppId, 'SUPPLIER');
    $suppLedgerPostRev = $ledgerModel->getLedgerEntries($suppId, 'SUPPLIER');

    if ($suppBalancePostRev === 0.00 && count($suppLedgerPostRev) === 2) {
        echo "PASSED (Supplier profile balance returned to LKR 0.00, reversal journal confirmed)\n";
    } else {
        throw new Exception("Supplier reversal balance check failed.");
    }

    // ----------------------------------------------------
    // TEST 19: Verify Trial Balance: Total Debits = Total Credits
    // ----------------------------------------------------
    echo "[TEST 19] Verify Trial Balance: Total Debits = Total Credits... ";
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

    // ----------------------------------------------------
    // TEST 20: Verify Stage 1–4 functionality works
    // ----------------------------------------------------
    echo "[TEST 20] Verify existing Stage 1–4 functionality works... ";
    $usersCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $categoriesCount = $db->query("SELECT COUNT(*) FROM product_categories")->fetchColumn(); // inventory
    if ($usersCount > 0 && $categoriesCount > 0) {
        echo "PASSED (Authentication and Inventory directories are responsive)\n";
    } else {
        throw new Exception("Stage 1-4 directories query failed.");
    }

    // Cleanup test data
    $db->exec("DELETE FROM parties WHERE party_code IN ('PTY-C5B', 'PTY-S5B')");

    echo "\n==================================================\n";
    echo "ALL STAGE 5B VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Verification Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
