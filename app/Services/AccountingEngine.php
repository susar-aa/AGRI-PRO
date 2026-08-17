<?php
namespace App\Services;

use Core\Database;
use Core\Auth;
use Exception;
use PDO;

class AccountingEngine {

    /**
     * Create a double-entry journal entry header and lines.
     * 
     * @param array $entry
     * @return int Journal Entry ID
     */
    public static function createJournalEntry(array $entry): int {
        $db = Database::getInstance();

        if (empty($entry['lines']) || count($entry['lines']) < 2) {
            throw new Exception("Journal entry must contain at least two line items (Debit & Credit).");
        }

        $totalDebit = 0.00;
        $totalCredit = 0.00;

        foreach ($entry['lines'] as $line) {
            $debit = round((float)($line['debit'] ?? 0), 2);
            $credit = round((float)($line['credit'] ?? 0), 2);

            if ($debit < 0 || $credit < 0) {
                throw new Exception("Negative amounts are not allowed in journal lines.");
            }

            if ($debit == 0 && $credit == 0) {
                throw new Exception("Each line item must have a non-zero debit or credit amount.");
            }

            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        $totalDebit = round($totalDebit, 2);
        $totalCredit = round($totalCredit, 2);

        // ENFORCE CORE DOUBLE-ENTRY EQUALITY RULE
        if (abs($totalDebit - $totalCredit) > 0.001) {
            throw new Exception("Unbalanced Journal Entry! Total Debit (LKR {$totalDebit}) must equal Total Credit (LKR {$totalCredit}).");
        }

        $createdBy = $entry['created_by'] ?? Auth::id() ?? 1;
        $transactionDate = $entry['transaction_date'] ?? date('Y-m-d');
        $journalNumber = self::generateJournalNumber();
        $status = $entry['status'] ?? 'draft';

        // Check active status of accounts and manual posting flag
        foreach ($entry['lines'] as $line) {
            $accountId = (int)$line['account_id'];
            $accCheck = $db->prepare("SELECT id, is_active, allow_manual_posting FROM accounts WHERE id = :id LIMIT 1");
            $accCheck->execute(['id' => $accountId]);
            $acc = $accCheck->fetch();
            if (!$acc || !$acc['is_active']) {
                throw new Exception("Account ID {$accountId} is invalid or inactive.");
            }
            if (($entry['source_module'] ?? 'manual') === 'manual' && !$acc['allow_manual_posting']) {
                throw new Exception("Manual posting is not allowed on Account ID {$accountId}.");
            }
        }

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // Insert Journal Entry Header
            $stmt = $db->prepare("
                INSERT INTO journal_entries 
                (journal_number, transaction_date, description, reference, source_module, source_transaction_id, cost_center_id, project_id, batch_id, status, total_debit, total_credit, created_by)
                VALUES 
                (:journal_number, :transaction_date, :description, :reference, :source_module, :source_transaction_id, :cost_center_id, :project_id, :batch_id, :status, :total_debit, :total_credit, :created_by)
            ");

            $stmt->execute([
                'journal_number' => $journalNumber,
                'transaction_date' => $transactionDate,
                'description' => $entry['description'] ?? 'General Journal Voucher',
                'reference' => $entry['reference'] ?? null,
                'source_module' => $entry['source_module'] ?? 'manual',
                'source_transaction_id' => $entry['source_transaction_id'] ?? null,
                'cost_center_id' => $entry['cost_center_id'] ?? null,
                'project_id' => $entry['project_id'] ?? null,
                'batch_id' => $entry['batch_id'] ?? null,
                'status' => $status,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'created_by' => $createdBy
            ]);

            $journalEntryId = (int)$db->lastInsertId();

            // Insert Lines
            $lineStmt = $db->prepare("
                INSERT INTO journal_lines (journal_entry_id, account_id, debit, credit, description)
                VALUES (:journal_entry_id, :account_id, :debit, :credit, :description)
            ");

            foreach ($entry['lines'] as $line) {
                $lineStmt->execute([
                    'journal_entry_id' => $journalEntryId,
                    'account_id' => (int)$line['account_id'],
                    'debit' => round((float)($line['debit'] ?? 0), 2),
                    'credit' => round((float)($line['credit'] ?? 0), 2),
                    'description' => $line['description'] ?? $entry['description'] ?? null
                ]);
            }

            AuditService::log('create_journal', 'accounting', $journalEntryId, null, [
                'journal_number' => $journalNumber,
                'status' => $status
            ]);

            if (!$inTransaction) {
                Database::commit();
            }

            return $journalEntryId;

        } catch (Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Submit a draft journal entry for approval.
     */
    public static function submitForApproval(int $journalId): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, status FROM journal_entries WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $journalId]);
        $je = $stmt->fetch();

        if (!$je) {
            throw new Exception("Journal entry not found.");
        }
        if ($je['status'] !== 'draft') {
            throw new Exception("Only draft journals can be submitted for approval.");
        }

        $update = $db->prepare("UPDATE journal_entries SET status = 'pending_approval' WHERE id = :id");
        $success = $update->execute(['id' => $journalId]);

        if ($success) {
            AuditService::log('submit_journal', 'accounting', $journalId, null, ['old_status' => 'draft', 'new_status' => 'pending_approval']);
        }
        return $success;
    }

    /**
     * Approve a pending journal entry.
     */
    public static function approveJournalEntry(int $journalId): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, status FROM journal_entries WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $journalId]);
        $je = $stmt->fetch();

        if (!$je) {
            throw new Exception("Journal entry not found.");
        }
        if ($je['status'] !== 'pending_approval') {
            throw new Exception("Only pending journals can be approved.");
        }

        $userId = Auth::id() ?? 1;
        $update = $db->prepare("
            UPDATE journal_entries 
            SET status = 'approved', approved_by = :approved_by, approved_at = NOW() 
            WHERE id = :id
        ");
        $success = $update->execute([
            'id' => $journalId,
            'approved_by' => $userId
        ]);

        if ($success) {
            AuditService::log('approve_journal', 'accounting', $journalId, null, ['old_status' => 'pending_approval', 'new_status' => 'approved']);
        }
        return $success;
    }

    /**
     * Post an approved or direct journal entry to the general ledger.
     * Backwards compatible with the previous Stage 1 array input.
     * 
     * @param int|array $entryOrId Journal ID or entry parameters array
     * @return int Journal Entry ID
     */
    public static function postJournalEntry(int|array $entryOrId): int {
        $db = Database::getInstance();

        if (is_array($entryOrId)) {
            // Stage 1 backwards compatibility: create & post directly
            $entryOrId['status'] = 'approved';
            $journalId = self::createJournalEntry($entryOrId);
        } else {
            $journalId = (int)$entryOrId;
        }

        $stmt = $db->prepare("SELECT * FROM journal_entries WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $journalId]);
        $je = $stmt->fetch();

        if (!$je) {
            throw new Exception("Journal entry not found.");
        }

        if ($je['status'] === 'posted') {
            return $journalId; // Already posted
        }

        // If the journal is not approved or draft, we check transition
        if (in_array($je['status'], ['reversed', 'cancelled'])) {
            throw new Exception("Reversed or Cancelled journals cannot be posted.");
        }

        // Fetch lines
        $lineStmt = $db->prepare("SELECT * FROM journal_lines WHERE journal_entry_id = :je_id");
        $lineStmt->execute(['je_id' => $journalId]);
        $lines = $lineStmt->fetchAll();

        if (empty($lines) || count($lines) < 2) {
            throw new Exception("Journal entry does not have enough line items.");
        }

        $userId = Auth::id() ?? 1;

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // Write ledger entries
            $ledgerStmt = $db->prepare("
                INSERT INTO ledger_entries (journal_entry_id, journal_line_id, account_id, transaction_date, cost_center_id, project_id, batch_id, debit, credit, running_balance)
                VALUES (:journal_entry_id, :journal_line_id, :account_id, :transaction_date, :cost_center_id, :project_id, :batch_id, :debit, :credit, :running_balance)
            ");

            foreach ($lines as $line) {
                $accountId = (int)$line['account_id'];
                $debit = round((float)$line['debit'], 2);
                $credit = round((float)$line['credit'], 2);

                // Fetch normal balance of account
                $accStmt = $db->prepare("SELECT normal_balance FROM accounts WHERE id = :id LIMIT 1");
                $accStmt->execute(['id' => $accountId]);
                $normalBalance = $accStmt->fetchColumn();

                // Calculate running balance for this account
                $balStmt = $db->prepare("
                    SELECT le.running_balance 
                    FROM ledger_entries le
                    JOIN journal_entries je ON le.journal_entry_id = je.id
                    WHERE le.account_id = :account_id AND je.status = 'posted'
                    ORDER BY le.id DESC LIMIT 1
                ");
                $balStmt->execute(['account_id' => $accountId]);
                $prevBalance = (float)($balStmt->fetchColumn() ?: 0.00);

                if ($normalBalance === 'debit') {
                    $runningBalance = $prevBalance + $debit - $credit;
                } else {
                    $runningBalance = $prevBalance + $credit - $debit;
                }

                $ledgerStmt->execute([
                    'journal_entry_id' => $journalId,
                    'journal_line_id' => (int)$line['id'],
                    'account_id' => $accountId,
                    'transaction_date' => $je['transaction_date'],
                    'cost_center_id' => $je['cost_center_id'],
                    'project_id' => $je['project_id'],
                    'batch_id' => $je['batch_id'],
                    'debit' => $debit,
                    'credit' => $credit,
                    'running_balance' => round($runningBalance, 2)
                ]);
            }

            // Update header status to posted
            $update = $db->prepare("
                UPDATE journal_entries 
                SET status = 'posted', posted_by = :posted_by, posting_date = :posting_date, posted_at = NOW() 
                WHERE id = :id
            ");
            $update->execute([
                'id' => $journalId,
                'posted_by' => $userId,
                'posting_date' => $je['transaction_date']
            ]);

            AuditService::log('post_journal', 'accounting', $journalId, null, [
                'journal_number' => $je['journal_number'],
                'status' => 'posted'
            ]);

            if (!$inTransaction) {
                Database::commit();
            }

            return $journalId;

        } catch (Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Create a reversal entry for a posted journal.
     */
    public static function reverseJournalEntry(int $journalId, string $reason): int {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM journal_entries WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $journalId]);
        $je = $stmt->fetch();

        if (!$je) {
            throw new Exception("Journal entry not found.");
        }
        if ($je['status'] !== 'posted') {
            throw new Exception("Only posted journals can be reversed.");
        }

        // Fetch lines
        $lineStmt = $db->prepare("SELECT * FROM journal_lines WHERE journal_entry_id = :je_id");
        $lineStmt->execute(['je_id' => $journalId]);
        $lines = $lineStmt->fetchAll();

        $userId = Auth::id() ?? 1;
        $reversalNumber = self::generateJournalNumber();

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // Create Reversal Header
            $revHeaderStmt = $db->prepare("
                INSERT INTO journal_entries 
                (journal_number, transaction_date, posting_date, description, reference, source_module, source_transaction_id, cost_center_id, project_id, batch_id, status, total_debit, total_credit, created_by, posted_by, posted_at, reversal_of_journal_id, reversal_reason)
                VALUES 
                (:journal_number, :transaction_date, :posting_date, :description, :reference, :source_module, :source_transaction_id, :cost_center_id, :project_id, :batch_id, 'posted', :total_debit, :total_credit, :created_by, :posted_by, NOW(), :reversal_of_journal_id, :reversal_reason)
            ");

            $revHeaderStmt->execute([
                'journal_number' => $reversalNumber,
                'transaction_date' => date('Y-m-d'),
                'posting_date' => date('Y-m-d'),
                'description' => "Reversal of " . $je['journal_number'] . " - " . $reason,
                'reference' => $je['journal_number'],
                'source_module' => $je['source_module'],
                'source_transaction_id' => $je['source_transaction_id'],
                'cost_center_id' => $je['cost_center_id'],
                'project_id' => $je['project_id'],
                'batch_id' => $je['batch_id'],
                'total_debit' => $je['total_credit'], // Same totals
                'total_credit' => $je['total_debit'],
                'created_by' => $userId,
                'posted_by' => $userId,
                'reversal_of_journal_id' => $journalId,
                'reversal_reason' => $reason
            ]);

            $reversalId = (int)$db->lastInsertId();

            // Create Reversal Lines and General Ledger Vouchers (Swapped Debit / Credit)
            $lineStmtInsert = $db->prepare("
                INSERT INTO journal_lines (journal_entry_id, account_id, debit, credit, description)
                VALUES (:journal_entry_id, :account_id, :debit, :credit, :description)
            ");

            $ledgerStmt = $db->prepare("
                INSERT INTO ledger_entries (journal_entry_id, journal_line_id, account_id, transaction_date, cost_center_id, project_id, batch_id, debit, credit, running_balance)
                VALUES (:journal_entry_id, :journal_line_id, :account_id, :transaction_date, :cost_center_id, :project_id, :batch_id, :debit, :credit, :running_balance)
            ");

            foreach ($lines as $line) {
                $accountId = (int)$line['account_id'];
                // Swap debit & credit
                $debit = round((float)$line['credit'], 2);
                $credit = round((float)$line['debit'], 2);
                $lineDesc = "Reversal: " . ($line['description'] ?: $je['description']);

                $lineStmtInsert->execute([
                    'journal_entry_id' => $reversalId,
                    'account_id' => $accountId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'description' => $lineDesc
                ]);

                $lineIdInsert = (int)$db->lastInsertId();

                // Running balance calculation
                $accStmt = $db->prepare("SELECT normal_balance FROM accounts WHERE id = :id LIMIT 1");
                $accStmt->execute(['id' => $accountId]);
                $normalBalance = $accStmt->fetchColumn();

                $balStmt = $db->prepare("
                    SELECT le.running_balance 
                    FROM ledger_entries le
                    JOIN journal_entries je ON le.journal_entry_id = je.id
                    WHERE le.account_id = :account_id AND je.status = 'posted'
                    ORDER BY le.id DESC LIMIT 1
                ");
                $balStmt->execute(['account_id' => $accountId]);
                $prevBalance = (float)($balStmt->fetchColumn() ?: 0.00);

                if ($normalBalance === 'debit') {
                    $runningBalance = $prevBalance + $debit - $credit;
                } else {
                    $runningBalance = $prevBalance + $credit - $debit;
                }

                $ledgerStmt->execute([
                    'journal_entry_id' => $reversalId,
                    'journal_line_id' => $lineIdInsert,
                    'account_id' => $accountId,
                    'transaction_date' => date('Y-m-d'),
                    'cost_center_id' => $je['cost_center_id'],
                    'project_id' => $je['project_id'],
                    'batch_id' => $je['batch_id'],
                    'debit' => $debit,
                    'credit' => $credit,
                    'running_balance' => round($runningBalance, 2)
                ]);
            }

            // Update original journal entry to 'cancelled' status
            $updateOrig = $db->prepare("
                UPDATE journal_entries 
                SET status = 'cancelled', reversal_reason = :reason 
                WHERE id = :id
            ");
            $updateOrig->execute([
                'id' => $journalId,
                'reason' => $reason
            ]);

            AuditService::log('reverse_journal', 'accounting', $journalId, null, [
                'original_number' => $je['journal_number'],
                'reversal_number' => $reversalNumber,
                'reason' => $reason
            ]);

            if (!$inTransaction) {
                Database::commit();
            }

            return $reversalId;

        } catch (Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Cancel an unposted journal entry.
     */
    public static function cancelJournalEntry(int $journalId): bool {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT id, status FROM journal_entries WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $journalId]);
        $je = $stmt->fetch();

        if (!$je) {
            throw new Exception("Journal entry not found.");
        }
        if (in_array($je['status'], ['posted', 'reversed', 'cancelled'])) {
            throw new Exception("Cannot cancel a journal entry that is already {$je['status']}.");
        }

        $update = $db->prepare("UPDATE journal_entries SET status = 'cancelled' WHERE id = :id");
        $success = $update->execute(['id' => $journalId]);

        if ($success) {
            AuditService::log('cancel_journal', 'accounting', $journalId, null, ['old_status' => $je['status'], 'new_status' => 'cancelled']);
        }
        return $success;
    }

    private static function generateJournalNumber(): string {
        $db = Database::getInstance();
        $prefix = 'JV-' . date('Ym') . '-';
        $stmt = $db->prepare("SELECT journal_number FROM journal_entries WHERE journal_number LIKE :prefix ORDER BY id DESC LIMIT 1");
        $stmt->execute(['prefix' => $prefix . '%']);
        $lastNum = $stmt->fetchColumn();

        if ($lastNum) {
            $seq = (int)substr($lastNum, -4);
            $newSeq = str_pad($seq + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '0001';
        }

        return $prefix . $newSeq;
    }
}
