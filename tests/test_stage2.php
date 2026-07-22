<?php
/**
 * Stage 2 Central Double-Entry Accounting Engine Verification Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

use App\Services\AccountingEngine;
use App\Models\Journal;
use Core\Database;

echo "==================================================\n";
echo "AGRI CO-OP ERP - STAGE 2 AUTOMATED VERIFICATION\n";
echo "==================================================\n\n";

try {
    $db = Database::getInstance();
    $journalModel = new Journal();

    // Ensure we are logged in as admin to pass permission/auth checks
    \Core\Auth::attempt('admin', 'admin123');
    echo "[SETUP] Logged in as administrator.\n\n";

    // ----------------------------------------------------
    // TEST 1: Create a Draft Journal Voucher
    // ----------------------------------------------------
    echo "[TEST 1] Create Draft Journal... ";
    $draftId = AccountingEngine::createJournalEntry([
        'transaction_date' => date('Y-m-d'),
        'description' => 'Draft Office Rent Expense',
        'reference' => 'REF-RENT-01',
        'source_module' => 'manual',
        'cost_center_id' => 9, // Admin
        'status' => 'draft',
        'lines' => [
            ['account_id' => 58, 'debit' => 20000.00, 'credit' => 0.00, 'description' => 'Rent Expense Debit'],
            ['account_id' => 10, 'debit' => 0.00, 'credit' => 20000.00, 'description' => 'Rent Expense Cash Credit']
        ]
    ]);

    $draftJe = $journalModel->getById($draftId);
    if ($draftJe && $draftJe['status'] === 'draft') {
        echo "PASSED (Journal ID: #{$draftId}, Status: draft)\n";
    } else {
        throw new Exception("Failed to create draft journal or status mismatch.");
    }

    // ----------------------------------------------------
    // TEST 2: Draft Entry should NOT affect General Ledger or Trial Balance
    // ----------------------------------------------------
    echo "[TEST 2] Verify Draft has NO financial impact... ";
    // Retrieve general ledger entries for cash (ID 10)
    $ledger = $journalModel->getGeneralLedger(10);
    $hasDraft = false;
    foreach ($ledger as $le) {
        if ($le['journal_entry_id'] === $draftId) {
            $hasDraft = true;
            break;
        }
    }
    if (!$hasDraft) {
        echo "PASSED (No entries found in General Ledger for Draft Journal)\n";
    } else {
        throw new Exception("Draft journal improperly posted to General Ledger!");
    }

    // ----------------------------------------------------
    // TEST 3: Submit for Approval
    // ----------------------------------------------------
    echo "[TEST 3] Submit Draft for Approval... ";
    if (AccountingEngine::submitForApproval($draftId)) {
        $submittedJe = $journalModel->getById($draftId);
        if ($submittedJe['status'] === 'pending_approval') {
            echo "PASSED (Status updated to pending_approval)\n";
        } else {
            throw new Exception("Status not updated to pending_approval.");
        }
    } else {
        throw new Exception("Submit for approval failed.");
    }

    // ----------------------------------------------------
    // TEST 4: Approve Journal
    // ----------------------------------------------------
    echo "[TEST 4] Approve Pending Journal... ";
    if (AccountingEngine::approveJournalEntry($draftId)) {
        $approvedJe = $journalModel->getById($draftId);
        if ($approvedJe['status'] === 'approved' && !empty($approvedJe['approved_by'])) {
            echo "PASSED (Status updated to approved by Admin)\n";
        } else {
            throw new Exception("Status not updated to approved.");
        }
    } else {
        throw new Exception("Approval failed.");
    }

    // ----------------------------------------------------
    // TEST 5: Post Approved Journal
    // ----------------------------------------------------
    echo "[TEST 5] Post Approved Journal to Ledger... ";
    $postedId = AccountingEngine::postJournalEntry($draftId);
    $postedJe = $journalModel->getById($draftId);
    if ($postedJe['status'] === 'posted' && !empty($postedJe['posted_by']) && !empty($postedJe['posting_date'])) {
        echo "PASSED (Status updated to posted)\n";
    } else {
        throw new Exception("Posting failed.");
    }

    // ----------------------------------------------------
    // TEST 6: Verify Posted Entry affects Ledger & Trial Balance
    // ----------------------------------------------------
    echo "[TEST 6] Verify Ledger & Trial Balance Impact... ";
    $ledger2 = $journalModel->getGeneralLedger(10);
    $hasPosted = false;
    foreach ($ledger2 as $le) {
        if ($le['journal_entry_id'] === $postedId) {
            $hasPosted = true;
            break;
        }
    }
    if ($hasPosted) {
        echo "PASSED (Posted Journal appears in General Ledger)\n";
    } else {
        throw new Exception("Posted journal does not appear in General Ledger!");
    }

    // ----------------------------------------------------
    // TEST 7: Journal Reversal
    // ----------------------------------------------------
    echo "[TEST 7] Reverse Posted Journal... ";
    $reversalId = AccountingEngine::reverseJournalEntry($postedId, 'Incorrect rent amount');
    $reversalJe = $journalModel->getById($reversalId);
    $originalJe = $journalModel->getById($postedId);

    if ($originalJe['status'] === 'reversed' && $reversalJe['status'] === 'posted' && $reversalJe['reversal_of_journal_id'] === $postedId) {
        echo "PASSED (Original status: reversed, Reversal entry: #{$reversalId} posted)\n";
    } else {
        throw new Exception("Reversal link or status update failed.");
    }

    // ----------------------------------------------------
    // TEST 8: Cancel a Draft Journal
    // ----------------------------------------------------
    echo "[TEST 8] Cancel Draft Journal... ";
    $cancelId = AccountingEngine::createJournalEntry([
        'transaction_date' => date('Y-m-d'),
        'description' => 'Office Supplies Draft',
        'lines' => [
            ['account_id' => 58, 'debit' => 1000.00, 'credit' => 0.00],
            ['account_id' => 10, 'debit' => 0.00, 'credit' => 1000.00]
        ]
    ]);
    if (AccountingEngine::cancelJournalEntry($cancelId)) {
        $cancelledJe = $journalModel->getById($cancelId);
        if ($cancelledJe['status'] === 'cancelled') {
            echo "PASSED (Journal ID: #{$cancelId} cancelled successfully)\n";
        } else {
            throw new Exception("Status not set to cancelled.");
        }
    } else {
        throw new Exception("Cancel failed.");
    }

    echo "\n==================================================\n";
    echo "ALL STAGE 2 VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Verification Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
