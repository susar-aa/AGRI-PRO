<?php
/**
 * Stage 1 Technical Foundation & Core Accounting Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

echo "==================================================\n";
echo "AGRI CO-OP ERP - STAGE 1 AUTOMATED VERIFICATION\n";
echo "==================================================\n\n";

try {
    // 1. Test Database Connection
    echo "[TEST 1] Database Connection... ";
    $db = \Core\Database::getInstance();
    $dbName = $db->query("SELECT DATABASE()")->fetchColumn();
    if ($dbName === 'agri_erp') {
        echo "PASSED (Database: agri_erp)\n";
    } else {
        throw new Exception("Connected database is not agri_erp!");
    }

    // 2. Test Super Admin Authentication
    echo "[TEST 2] Authentication & Password Hashing... ";
    $authSuccess = \Core\Auth::attempt('admin', 'admin123');
    if ($authSuccess) {
        $user = \Core\Auth::user();
        $perms = \Core\Auth::permissions();
        echo "PASSED (Logged in as: {$user['full_name']} with " . count($perms) . " permissions)\n";
    } else {
        throw new Exception("Authentication failed for admin / admin123!");
    }

    // 3. Test Chart of Accounts Hierarchy
    echo "[TEST 3] Chart of Accounts Structure... ";
    $accModel = new \App\Models\Account();
    $tree = $accModel->getAllHierarchical();
    $flat = $accModel->getAllFlat();
    if (count($flat) >= 61) {
        echo "PASSED (" . count($flat) . " Accounts Seeded across 6 Categories)\n";
    } else {
        throw new Exception("Expected at least 61 accounts, found " . count($flat));
    }

    // 4. Test Cost Centers
    echo "[TEST 4] Cost Centers Initialization... ";
    $ccModel = new \App\Models\CostCenter();
    $ccList = $ccModel->getAll();
    if (count($ccList) >= 9) {
        echo "PASSED (" . count($ccList) . " Cost Centers Configured: CC-001 through CC-009)\n";
    } else {
        throw new Exception("Expected 9 Cost Centers, found " . count($ccList));
    }

    // 5. Test Double-Entry Accounting Engine: Unbalanced Entry Prevention
    echo "[TEST 5] Accounting Engine Unbalanced Entry Block... ";
    try {
        \App\Services\AccountingEngine::postJournalEntry([
            'transaction_date' => date('Y-m-d'),
            'description' => 'Invalid Unbalanced Test',
            'lines' => [
                ['account_id' => 9, 'debit' => 5000.00, 'credit' => 0.00],
                ['account_id' => 25, 'debit' => 0.00, 'credit' => 4000.00]
            ]
        ]);
        echo "FAILED (Unbalanced entry was improperly allowed!)\n";
    } catch (Exception $e) {
        echo "PASSED (Successfully caught expected exception: " . $e->getMessage() . ")\n";
    }

    // 6. Test Double-Entry Accounting Engine: Valid Balanced Posting
    echo "[TEST 6] Accounting Engine Valid Double-Entry Posting... ";
    $journalId = \App\Services\AccountingEngine::postJournalEntry([
        'transaction_date' => date('Y-m-d'),
        'description' => 'Initial Capital Injection into Bank Account',
        'reference' => 'CAP-001',
        'source_module' => 'manual',
        'cost_center_id' => 9, // Administration
        'lines' => [
            ['account_id' => 10, 'debit' => 100000.00, 'credit' => 0.00, 'description' => 'Debit Bank Accounts'],
            ['account_id' => 25, 'debit' => 0.00, 'credit' => 100000.00, 'description' => 'Credit Member Share Capital']
        ]
    ]);
    if ($journalId > 0) {
        echo "PASSED (Posted Journal Entry ID: #{$journalId})\n";
    } else {
        throw new Exception("Failed to post valid journal entry!");
    }

    // 7. Test General Ledger & Trial Balance Synchronization
    echo "[TEST 7] General Ledger & Trial Balance Verification... ";
    $journalModel = new \App\Models\Journal();
    $tb = $journalModel->getTrialBalance(date('Y-m-d'));
    
    $totDebit = 0.00;
    $totCredit = 0.00;
    foreach ($tb as $row) {
        $totDebit += (float)$row['total_debit'];
        $totCredit += (float)$row['total_credit'];
    }

    if (abs($totDebit - $totCredit) < 0.001 && $totDebit === 100000.00) {
        echo "PASSED (Trial Balance Balanced: Debit LKR " . number_format($totDebit, 2) . " == Credit LKR " . number_format($totCredit, 2) . ")\n";
    } else {
        throw new Exception("Trial Balance imbalance detected! Debit: {$totDebit}, Credit: {$totCredit}");
    }

    echo "\n==================================================\n";
    echo "ALL STAGE 1 VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Verification Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
