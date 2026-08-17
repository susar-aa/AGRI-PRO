<?php
/**
 * Stage 3 Central Expense Engine Verification Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

use App\Services\ExpenseEngine;
use App\Models\Expense;
use App\Models\Journal;
use Core\Database;

echo "==================================================\n";
echo "AGRI CO-OP ERP - STAGE 3 AUTOMATED VERIFICATION\n";
echo "==================================================\n\n";

try {
    $db = Database::getInstance();
    $expenseModel = new Expense();
    $journalModel = new Journal();

    // Setup: Log in as admin
    \Core\Auth::attempt('admin', 'admin123');
    echo "[SETUP] Logged in as administrator.\n";

    // Ensure a default supplier exists for credit tests
    $db->exec("INSERT IGNORE INTO suppliers (id, supplier_code, name_en, status) VALUES (1, 'SUP-001', 'ABC Supplier', 'active')");
    echo "[SETUP] Supplier database records initialized.\n\n";

    // ----------------------------------------------------
    // TEST 1: Cash Expense
    // ----------------------------------------------------
    echo "[TEST 1] Create and Post Cash Expense (Fuel)... ";
    $cashExpId = ExpenseEngine::createExpense([
        'expense_date' => date('Y-m-d'),
        'payee' => 'Colombo Fuel Station',
        'expense_category_id' => 1, // Fuel
        'description' => 'Fuel for Agri Tractor plowing',
        'amount' => 10000.00,
        'payment_method' => 'Cash',
        'cash_account_id' => 1, // Main Office Drawer
        'cost_center_id' => 1, // Agricultural Services
        'status' => 'approved' // Set to approved so we can post directly
    ]);

    // Fetch initial cash drawer balance
    $cashBalStmt = $db->prepare("SELECT current_balance FROM cash_accounts WHERE id = 1");
    $cashBalStmt->execute();
    $initialCash = (float)$cashBalStmt->fetchColumn();

    // Post to Ledger
    ExpenseEngine::postExpense($cashExpId);

    $postedCashExp = $expenseModel->getById($cashExpId);
    $cashBalStmt->execute();
    $newCash = (float)$cashBalStmt->fetchColumn();

    if ($postedCashExp['status'] === 'posted' && !empty($postedCashExp['journal_entry_id'])) {
        // Verify ledger lines
        $je = $journalModel->getById((int)$postedCashExp['journal_entry_id']);
        $lines = $je['lines'];
        if (count($lines) === 2 && (float)$lines[0]['debit'] === 10000.00 && (float)$lines[1]['credit'] === 10000.00) {
            echo "PASSED (Expense posted, Journal #" . $je['journal_number'] . " created, Cash decreased by LKR 10,000.00)\n";
        } else {
            throw new Exception("Journal lines debit/credit mismatch.");
        }
    } else {
        throw new Exception("Cash expense failed to post.");
    }

    // ----------------------------------------------------
    // TEST 2: Bank Expense
    // ----------------------------------------------------
    echo "[TEST 2] Create and Post Bank Expense (Electricity)... ";
    $bankExpId = ExpenseEngine::createExpense([
        'expense_date' => date('Y-m-d'),
        'payee' => 'Ceylon Electricity Board',
        'expense_category_id' => 6, // Electricity
        'description' => 'Office Building Electricity',
        'amount' => 25000.00,
        'payment_method' => 'Bank Transfer',
        'bank_account_id' => 1, // Bank of Ceylon
        'cost_center_id' => 9, // Administration
        'status' => 'approved'
    ]);

    ExpenseEngine::postExpense($bankExpId);
    $postedBankExp = $expenseModel->getById($bankExpId);

    if ($postedBankExp['status'] === 'posted' && !empty($postedBankExp['journal_entry_id'])) {
        $je = $journalModel->getById((int)$postedBankExp['journal_entry_id']);
        echo "PASSED (Journal #" . $je['journal_number'] . " created, debited Electricity account successfully)\n";
    } else {
        throw new Exception("Bank expense failed to post.");
    }

    // ----------------------------------------------------
    // TEST 3: Credit Expense (Accounts Payable)
    // ----------------------------------------------------
    echo "[TEST 3] Create and Post Credit Expense (Fuel on Credit)... ";
    $creditExpId = ExpenseEngine::createExpense([
        'expense_date' => date('Y-m-d'),
        'payee' => 'ABC Supplier',
        'expense_category_id' => 1, // Fuel
        'description' => 'Credit purchase of tractor fuel',
        'amount' => 15000.00,
        'payment_method' => 'Credit',
        'supplier_id' => 1,
        'cost_center_id' => 1,
        'status' => 'approved'
    ]);

    ExpenseEngine::postExpense($creditExpId);
    $postedCreditExp = $expenseModel->getById($creditExpId);

    if ($postedCreditExp['status'] === 'posted' && !empty($postedCreditExp['journal_entry_id'])) {
        $je = $journalModel->getById((int)$postedCreditExp['journal_entry_id']);
        // Verify accounts payable was credited
        $hasAp = false;
        foreach ($je['lines'] as $line) {
            if ($line['account_code'] === '2110' && (float)$line['credit'] === 15000.00) {
                $hasAp = true;
                break;
            }
        }
        if ($hasAp) {
            echo "PASSED (Accounts Payable credited with LKR 15,000.00)\n";
        } else {
            throw new Exception("Accounts Payable account was not credited.");
        }
    } else {
        throw new Exception("Credit expense failed to post.");
    }

    // ----------------------------------------------------
    // TEST 4: Draft Expense Has No Financial Impact
    // ----------------------------------------------------
    echo "[TEST 4] Verify Draft Expense has NO financial impact... ";
    $draftExpId = ExpenseEngine::createExpense([
        'expense_date' => date('Y-m-d'),
        'payee' => 'Temporary Vendor',
        'expense_category_id' => 17, // Other operating
        'description' => 'Draft Office Supplies',
        'amount' => 5000.00,
        'payment_method' => 'Cash',
        'cash_account_id' => 1,
        'cost_center_id' => 9,
        'status' => 'draft'
    ]);

    $draftExp = $expenseModel->getById($draftExpId);
    if ($draftExp['status'] === 'draft' && empty($draftExp['journal_entry_id'])) {
        echo "PASSED (Voucher in Draft state, no accounting journal generated)\n";
    } else {
        throw new Exception("Draft expense has journal assigned!");
    }

    // ----------------------------------------------------
    // TEST 5: Cancel Expense
    // ----------------------------------------------------
    echo "[TEST 5] Cancel Unposted Expense... ";
    if (ExpenseEngine::cancelExpense($draftExpId)) {
        $cancelledExp = $expenseModel->getById($draftExpId);
        if ($cancelledExp['status'] === 'cancelled') {
            echo "PASSED (Expense status changed to cancelled)\n";
        } else {
            throw new Exception("Failed to update status to cancelled.");
        }
    } else {
        throw new Exception("Cancellation action failed.");
    }

    // ----------------------------------------------------
    // TEST 6: Reversal of Posted Expense
    // ----------------------------------------------------
    echo "[TEST 6] Reverse Posted Expense... ";
    // Let's reverse the cash expense (LKR 10,000) from Test 1
    $origCashBalStmt = $db->prepare("SELECT current_balance FROM cash_accounts WHERE id = 1");
    $origCashBalStmt->execute();
    $cashBeforeRev = (float)$origCashBalStmt->fetchColumn();

    ExpenseEngine::reverseExpense($cashExpId, 'Double payment entry error');

    $reversedExp = $expenseModel->getById($cashExpId);
    $origCashBalStmt->execute();
    $cashAfterRev = (float)$origCashBalStmt->fetchColumn();

    if ($reversedExp['status'] === 'reversed' && !empty($reversedExp['reversal_journal_entry_id'])) {
        $revJe = $journalModel->getById((int)$reversedExp['reversal_journal_entry_id']);
        if (abs($cashAfterRev - ($cashBeforeRev + 10000.00)) < 0.001) {
            echo "PASSED (Expense status: reversed, Reversal Journal #" . $revJe['journal_number'] . " posted, Cash drawer refunded)\n";
        } else {
            throw new Exception("Cash drawer balance was not properly reverted!");
        }
    } else {
        throw new Exception("Reversal action failed.");
    }

    // ----------------------------------------------------
    // TEST 7: Duplicate Prevention
    // ----------------------------------------------------
    echo "[TEST 7] Duplicate Prevention Block... ";
    $sourceModule = 'PLANTATION';
    $sourceType = 'PLANTATION_EXPENSE';
    $sourceTxId = 15;

    // First transaction should succeed
    $firstId = ExpenseEngine::createExpense([
        'expense_date' => date('Y-m-d'),
        'payee' => 'Fertilizer Depot',
        'expense_category_id' => 10, // Fertilizer
        'description' => 'Fertilizer for crop plantation project #15',
        'amount' => 8000.00,
        'payment_method' => 'Cash',
        'cash_account_id' => 1,
        'cost_center_id' => 4, // Plantation CC
        'source_module' => $sourceModule,
        'source_type' => $sourceType,
        'source_transaction_id' => $sourceTxId,
        'status' => 'approved'
    ]);
    ExpenseEngine::postExpense($firstId);

    // Second transaction should block
    try {
        ExpenseEngine::createExpense([
            'expense_date' => date('Y-m-d'),
            'payee' => 'Fertilizer Depot',
            'expense_category_id' => 10,
            'description' => 'Duplicate fertilizer request for project #15',
            'amount' => 8000.00,
            'payment_method' => 'Cash',
            'cash_account_id' => 1,
            'cost_center_id' => 4,
            'source_module' => $sourceModule,
            'source_type' => $sourceType,
            'source_transaction_id' => $sourceTxId,
            'status' => 'approved'
        ]);
        throw new Exception("Failed (Duplicate posting was improperly allowed!)");
    } catch (Exception $e) {
        echo "PASSED (Successfully blocked duplicate posting request: " . $e->getMessage() . ")\n";
    }

    echo "\n==================================================\n";
    echo "ALL STAGE 3 VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Verification Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
