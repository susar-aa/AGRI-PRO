<?php
namespace App\Services;

use Core\Database;
use Core\Auth;
use App\Models\Party;
use App\Services\AccountingEngine;
use App\Services\AuditService;
use Exception;

class OpeningBalanceEngine {

    /**
     * Create and post an opening balance voucher.
     */
    public static function postOpeningBalance(array $data): int {
        $db = Database::getInstance();
        $partyModel = new Party();

        // 1. Validation
        if (empty($data['party_id'])) {
            throw new Exception("Business party contact is required.");
        }
        $amount = round((float)($data['amount'] ?? 0), 2);
        if ($amount <= 0) {
            throw new Exception("Opening balance amount must be greater than zero.");
        }
        if (empty($data['type'])) {
            throw new Exception("Opening balance type is required.");
        }

        $partyId = (int)$data['party_id'];
        $party = $partyModel->getById($partyId);
        if (!$party) {
            throw new Exception("Party not found.");
        }

        $type = $data['type'];
        $isCustomer = ($party['party_type'] === 'CUSTOMER' || $party['party_type'] === 'BOTH');
        $isSupplier = ($party['party_type'] === 'SUPPLIER' || $party['party_type'] === 'BOTH');

        if ($type === 'receivable' && !$isCustomer) {
            throw new Exception("Receivable balance type is only valid for customer profiles.");
        }
        if ($type === 'payable' && !$isSupplier) {
            throw new Exception("Payable balance type is only valid for supplier profiles.");
        }

        // Check if an active/posted opening balance already exists to prevent duplicates
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM party_opening_balances WHERE party_id = :party_id AND status = 'posted'");
        $checkStmt->execute(['party_id' => $partyId]);
        if ((int)$checkStmt->fetchColumn() > 0) {
            throw new Exception("An active opening balance is already posted for this business partner.");
        }

        $balanceDate = $data['balance_date'] ?? date('Y-m-d');
        $description = $data['description'] ?? "Opening Balance Entry";
        $createdBy = $data['created_by'] ?? Auth::id() ?? 1;

        // Resolve Accounting Codes
        // Accounts Receivable: ID 12 (1140)
        // Accounts Payable: ID 20 (2110)
        // Equity: ID 3 (3000)
        $debitAccountId = null;
        $creditAccountId = null;

        if ($type === 'receivable') {
            $debitAccountId = 12; // Accounts Receivable
            $creditAccountId = 3;  // Equity
        } elseif ($type === 'payable') {
            $debitAccountId = 3;   // Equity
            $creditAccountId = 20; // Accounts Payable
        } else {
            throw new Exception("Opening balance type not supported in this stage.");
        }

        // Fetch a default cost center
        $costCenterId = (int)$db->query("SELECT id FROM cost_centers LIMIT 1")->fetchColumn();
        if (!$costCenterId) {
            throw new Exception("No cost centers exist in the system.");
        }

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // 1. Insert draft record
            $stmt = $db->prepare("
                INSERT INTO party_opening_balances 
                (party_id, type, amount, balance_date, description, status, created_by)
                VALUES 
                (:party_id, :type, :amount, :balance_date, :description, 'draft', :created_by)
            ");
            $stmt->execute([
                'party_id' => $partyId,
                'type' => $type,
                'amount' => $amount,
                'balance_date' => $balanceDate,
                'description' => $description,
                'created_by' => $createdBy
            ]);
            $obId = (int)$db->lastInsertId();

            // 2. Prepare Double-Entry Journal Entry
            $refNumber = 'OPB-' . str_pad((string)$obId, 5, '0', STR_PAD_LEFT);
            $journalData = [
                'transaction_date' => $balanceDate,
                'description' => $description . " (" . $party['name'] . ")",
                'reference' => $refNumber,
                'source_module' => 'parties',
                'source_transaction_id' => $obId,
                'cost_center_id' => $costCenterId,
                'status' => 'approved', // post it immediately
                'lines' => [
                    [
                        'account_id' => $debitAccountId,
                        'debit' => $amount,
                        'credit' => 0.00,
                        'description' => $description
                    ],
                    [
                        'account_id' => $creditAccountId,
                        'debit' => 0.00,
                        'credit' => $amount,
                        'description' => $description
                    ]
                ]
            ];

            // Post double-entry journal entry
            $journalId = AccountingEngine::postJournalEntry($journalData);

            // 3. Update opening balance record
            $upd = $db->prepare("
                UPDATE party_opening_balances 
                SET status = 'posted', journal_entry_id = :je_id 
                WHERE id = :id
            ");
            $upd->execute([
                'id' => $obId,
                'je_id' => $journalId
            ]);

            AuditService::log('post_opening_balance', 'parties', $partyId, null, [
                'opening_balance_id' => $obId,
                'journal_entry_id' => $journalId,
                'amount' => $amount
            ]);

            if (!$inTransaction) {
                Database::commit();
            }

            return $obId;

        } catch (Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Reverse a posted opening balance.
     */
    public static function reverseOpeningBalance(int $id, string $reason): bool {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM party_opening_balances WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $ob = $stmt->fetch();

        if (!$ob) {
            throw new Exception("Opening balance record not found.");
        }
        if ($ob['status'] !== 'posted') {
            throw new Exception("Only posted opening balances can be reversed.");
        }

        $userId = Auth::id() ?? 1;

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // Reverse the accounting journal entry
            $reversalJournalId = AccountingEngine::reverseJournalEntry((int)$ob['journal_entry_id'], "Reversal of Opening Balance (ID: " . $ob['id'] . "): " . $reason);

            // Update opening balance status
            $upd = $db->prepare("
                UPDATE party_opening_balances 
                SET status = 'reversed', 
                    reversal_journal_entry_id = :rev_je_id,
                    reversal_reason = :reason,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $upd->execute([
                'id' => $id,
                'rev_je_id' => $reversalJournalId,
                'reason' => $reason
            ]);

            AuditService::log('reverse_opening_balance', 'parties', (int)$ob['party_id'], null, [
                'opening_balance_id' => $id,
                'reversal_journal_entry_id' => $reversalJournalId,
                'reason' => $reason
            ]);

            if (!$inTransaction) {
                Database::commit();
            }
            return true;

        } catch (Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            throw $e;
        }
    }
}
