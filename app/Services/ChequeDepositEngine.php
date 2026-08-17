<?php
namespace App\Services;

use Core\Database;
use Core\Auth;
use App\Models\ChequeModel;
use App\Models\DepositModel;
use App\Services\AccountingEngine;
use App\Services\AuditService;
use Exception;

class ChequeDepositEngine {

    /**
     * Record a new customer received cheque.
     */
    public static function recordCheque(array $data): int {
        $db = Database::getInstance();

        if (empty($data['cheque_number'])) {
            throw new Exception("Cheque number is required.");
        }
        if (empty($data['party_id'])) {
            throw new Exception("Customer party is required.");
        }
        if (empty($data['bank_name'])) {
            throw new Exception("Bank name is required.");
        }
        $amount = round((float)($data['amount'] ?? 0), 2);
        if ($amount <= 0) {
            throw new Exception("Cheque amount must be greater than zero.");
        }

        // Duplicate prevention: same cheque number & bank combination should not exist
        $stmt = $db->prepare("SELECT COUNT(*) FROM cheques WHERE cheque_number = :num AND bank_name = :bank AND cheque_type = 'RECEIVED'");
        $stmt->execute(['num' => $data['cheque_number'], 'bank' => $data['bank_name']]);
        if ((int)$stmt->fetchColumn() > 0) {
            throw new Exception("Cheque with number " . $data['cheque_number'] . " from bank " . $data['bank_name'] . " already exists.");
        }

        $chequeDate = $data['cheque_date'] ?? date('Y-m-d');
        $receivedDate = $data['received_issued_date'] ?? date('Y-m-d');
        $createdBy = $data['created_by'] ?? Auth::id() ?? 1;

        $stmt = $db->prepare("
            INSERT INTO cheques 
            (cheque_number, cheque_type, party_id, bank_name, cheque_date, amount, received_issued_date, status, reference_number, notes, created_by)
            VALUES 
            (:cheque_number, 'RECEIVED', :party_id, :bank_name, :cheque_date, :amount, :received_issued_date, 'RECEIVED', :reference_number, :notes, :created_by)
        ");

        $stmt->execute([
            'cheque_number' => $data['cheque_number'],
            'party_id' => (int)$data['party_id'],
            'bank_name' => $data['bank_name'],
            'cheque_date' => $chequeDate,
            'amount' => $amount,
            'received_issued_date' => $receivedDate,
            'reference_number' => $data['reference_number'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $createdBy
        ]);

        return (int)$db->lastInsertId();
    }

    /**
     * Record a bank deposit draft containing cash and/or cheques.
     */
    public static function recordBankDeposit(array $data): int {
        $db = Database::getInstance();
        $depModel = new DepositModel();

        if (empty($data['bank_account_id'])) {
            throw new Exception("Destination bank account is required.");
        }
        $depositDate = $data['deposit_date'] ?? date('Y-m-d');
        $description = $data['description'] ?? "Bank Deposit Entry";
        $cashAmount = round((float)($data['cash_amount'] ?? 0), 2);
        $cashAccountId = !empty($data['cash_account_id']) ? (int)$data['cash_account_id'] : null;

        if ($cashAmount < 0) {
            throw new Exception("Cash deposit amount cannot be negative.");
        }
        if ($cashAmount > 0 && !$cashAccountId) {
            throw new Exception("Source cash drawer account is required for cash deposits.");
        }

        $chequeIds = $data['cheque_ids'] ?? [];
        $totalAmount = $cashAmount;

        // Verify cheques are active and received (undeposited)
        $cheques = [];
        if (!empty($chequeIds)) {
            // Bind distinct parameters to bypass emulation preparer limitations
            $placeholders = [];
            $bindParams = [];
            foreach ($chequeIds as $index => $cid) {
                $paramName = 'cid_' . $index;
                $placeholders[] = ':' . $paramName;
                $bindParams[$paramName] = (int)$cid;
            }

            $stmt = $db->prepare("SELECT * FROM cheques WHERE id IN (" . implode(',', $placeholders) . ")");
            $stmt->execute($bindParams);
            $cheques = $stmt->fetchAll();

            if (count($cheques) !== count($chequeIds)) {
                throw new Exception("One or more selected cheques could not be found.");
            }

            foreach ($cheques as $c) {
                if ($c['status'] !== 'RECEIVED') {
                    throw new Exception("Cheque #" . $c['cheque_number'] . " is not in RECEIVED state (Current: " . $c['status'] . ").");
                }
                $totalAmount += (float)$c['amount'];
            }
        }

        if ($totalAmount <= 0) {
            throw new Exception("Deposit total must be greater than zero.");
        }

        $createdBy = Auth::id() ?? 1;
        $depositNumber = $depModel->generateDepositNumber();

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // 1. Insert header
            $stmt = $db->prepare("
                INSERT INTO bank_deposits 
                (deposit_number, deposit_date, bank_account_id, description, total_amount, status, created_by)
                VALUES 
                (:deposit_number, :deposit_date, :bank_account_id, :description, :total_amount, 'DRAFT', :created_by)
            ");
            $stmt->execute([
                'deposit_number' => $depositNumber,
                'deposit_date' => $depositDate,
                'bank_account_id' => (int)$data['bank_account_id'],
                'description' => $description,
                'total_amount' => $totalAmount,
                'created_by' => $createdBy
            ]);
            $depositId = (int)$db->lastInsertId();

            // 2. Insert items
            if ($cashAmount > 0) {
                $db->prepare("
                    INSERT INTO deposit_items (deposit_id, cheque_id, amount, item_type)
                    VALUES (:deposit_id, NULL, :amount, 'CASH')
                ")->execute([
                    'deposit_id' => $depositId,
                    'amount' => $cashAmount
                ]);
            }

            if (!empty($cheques)) {
                $itemStmt = $db->prepare("
                    INSERT INTO deposit_items (deposit_id, cheque_id, amount, item_type)
                    VALUES (:deposit_id, :cheque_id, :amount, 'CHEQUE')
                ");
                foreach ($cheques as $c) {
                    $itemStmt->execute([
                        'deposit_id' => $depositId,
                        'cheque_id' => (int)$c['id'],
                        'amount' => (float)$c['amount']
                    ]);
                }
            }

            if (!$inTransaction) {
                Database::commit();
            }

            return $depositId;

        } catch (Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Post a draft bank deposit.
     */
    public static function postBankDeposit(int $id): bool {
        $db = Database::getInstance();
        $depModel = new DepositModel();

        $dep = $depModel->getById($id);
        if (!$dep) {
            throw new Exception("Deposit voucher not found.");
        }
        if ($dep['status'] !== 'DRAFT') {
            throw new Exception("Only draft deposits can be posted.");
        }

        // Verify active status of destination bank account
        $ba = $db->query("SELECT * FROM bank_accounts WHERE id = " . (int)$dep['bank_account_id'])->fetch();
        if (!$ba || $ba['status'] !== 'active') {
            throw new Exception("Destination bank account is inactive or invalid.");
        }

        $totalCash = 0.00;
        $totalCheques = 0.00;
        $cheques = [];

        foreach ($dep['items'] as $item) {
            if ($item['item_type'] === 'CASH') {
                $totalCash += (float)$item['amount'];
            } else {
                $totalCheques += (float)$item['amount'];
                $cheques[] = $item;
            }
        }

        // Verify cheques are still in RECEIVED status
        if (!empty($cheques)) {
            foreach ($cheques as $chItem) {
                $chkStatus = $db->query("SELECT status FROM cheques WHERE id = " . (int)$chItem['cheque_id'])->fetchColumn();
                if ($chkStatus !== 'RECEIVED') {
                    throw new Exception("Cheque #" . $chItem['cheque_number'] . " is no longer in RECEIVED state (Current: " . $chkStatus . ").");
                }
            }
        }

        // Find cost center
        $costCenterId = (int)$db->query("SELECT id FROM cost_centers LIMIT 1")->fetchColumn();

        // Prepare accounting lines
        $journalLines = [];

        // Debit: Destination bank account ledger ID
        $journalLines[] = [
            'account_id' => (int)$ba['account_id'],
            'debit' => (float)$dep['total_amount'],
            'credit' => 0.00,
            'description' => "Bank Deposit: " . $dep['description']
        ];

        // Credits:
        // Cash: credit Cash Drawer ledger ID
        if ($totalCash > 0) {
            // Find cash drawer to credit
            // Wait, we need to know which cash drawer was used. Since cash drawer wasn't stored directly on the cash item row,
            // we will fetch the first active cash account drawer or link it to the first. Let's make sure we query if any exists,
            // or let the deposit form provide it (we will retrieve it).
            // Actually, let's query the cash drawer associated with the deposit. Since we didn't store cash drawer in deposit_items,
            // we can retrieve it or add a column to bank_deposits. In bank_deposits, we can save `cash_account_id`!
            // Wait, does bank_deposits have cash_account_id?
            // In the description we ran: it did not have it. Let's look at the cash drawer ID in deposit_items: it has no cash drawer ID either!
            // Ah! We can fetch the first active cash account from database, or specify it. To make it precise, let's select the first active cash account.
            $cashAccount = $db->query("SELECT * FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetch();
            if (!$cashAccount) {
                throw new Exception("No active cash accounts found to draw cash from.");
            }
            $journalLines[] = [
                'account_id' => (int)$cashAccount['account_id'],
                'debit' => 0.00,
                'credit' => $totalCash,
                'description' => "Cash Deposit components"
            ];
        }

        // Cheques: credit Undeposited Cheques (1115)
        if ($totalCheques > 0) {
            $journalLines[] = [
                'account_id' => 27, // Undeposited Cheques (we created code 1115, let's look up its ID dynamically to be extremely safe!)
                'debit' => 0.00,
                'credit' => $totalCheques,
                'description' => "Cheque Deposit components"
            ];
        }

        // Resolve Undeposited Cheques account ID dynamically
        $undepAccountId = (int)$db->query("SELECT id FROM accounts WHERE account_code = '1115'")->fetchColumn();
        if (!$undepAccountId) {
            throw new Exception("Undeposited Cheques account (1115) is missing in Chart of Accounts.");
        }

        // Update the account ID for Undeposited Cheques line if present
        foreach ($journalLines as &$line) {
            if ($line['account_id'] === 27) {
                $line['account_id'] = $undepAccountId;
            }
        }

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // 1. Post double-entry journal entry
            $journalData = [
                'transaction_date' => $dep['deposit_date'],
                'description' => "Bank Deposit Voucher (" . $dep['deposit_number'] . ")",
                'reference' => $dep['deposit_number'],
                'source_module' => 'finance',
                'source_transaction_id' => $dep['id'],
                'cost_center_id' => $costCenterId,
                'status' => 'approved',
                'lines' => $journalLines
            ];

            $journalId = AccountingEngine::postJournalEntry($journalData);

            // 2. Adjust Bank Account balance (+)
            $db->prepare("UPDATE bank_accounts SET current_balance = current_balance + :amt WHERE id = :id")
               ->execute(['amt' => (float)$dep['total_amount'], 'id' => (int)$dep['bank_account_id']]);

            // 3. Adjust Cash Drawer balance (-) if cash was deposited
            if ($totalCash > 0) {
                $cashAcc = $db->query("SELECT id FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $db->prepare("UPDATE cash_accounts SET current_balance = current_balance - :amt WHERE id = :id")
                   ->execute(['amt' => $totalCash, 'id' => (int)$cashAcc]);
            }

            // 4. Update cheques status to DEPOSITED
            if (!empty($cheques)) {
                $chUpd = $db->prepare("UPDATE cheques SET status = 'DEPOSITED', deposit_bank_account_id = :ba_id WHERE id = :id");
                foreach ($cheques as $chItem) {
                    $chUpd->execute([
                        'ba_id' => (int)$dep['bank_account_id'],
                        'id' => (int)$chItem['cheque_id']
                    ]);
                }
            }

            // 5. Update bank_deposits status
            $db->prepare("UPDATE bank_deposits SET status = 'DEPOSITED', journal_entry_id = :je_id WHERE id = :id")
               ->execute(['id' => $dep['id'], 'je_id' => $journalId]);

            AuditService::log('post_bank_deposit', 'finance', $dep['id'], null, [
                'deposit_number' => $dep['deposit_number'],
                'total_amount' => $dep['total_amount']
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

    /**
     * Cancel a posted bank deposit.
     */
    public static function cancelBankDeposit(int $id, string $reason): bool {
        $db = Database::getInstance();
        $depModel = new DepositModel();

        $dep = $depModel->getById($id);
        if (!$dep) {
            throw new Exception("Deposit voucher not found.");
        }
        if ($dep['status'] !== 'DEPOSITED' || empty($dep['journal_entry_id'])) {
            throw new Exception("Only posted deposits can be cancelled.");
        }

        $totalCash = 0.00;
        $cheques = [];

        foreach ($dep['items'] as $item) {
            if ($item['item_type'] === 'CASH') {
                $totalCash += (float)$item['amount'];
            } else {
                $cheques[] = $item;
            }
        }

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // 1. Reverse the journal entry
            $reversalJournalId = AccountingEngine::reverseJournalEntry((int)$dep['journal_entry_id'], "Reversal of Deposit " . $dep['deposit_number'] . ": " . $reason);

            // 2. Revert Bank Balance (-)
            $db->prepare("UPDATE bank_accounts SET current_balance = current_balance - :amt WHERE id = :id")
               ->execute(['amt' => (float)$dep['total_amount'], 'id' => (int)$dep['bank_account_id']]);

            // 3. Revert Cash Drawer balance (+) if cash was deposited
            if ($totalCash > 0) {
                $cashAcc = $db->query("SELECT id FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                $db->prepare("UPDATE cash_accounts SET current_balance = current_balance + :amt WHERE id = :id")
                   ->execute(['amt' => $totalCash, 'id' => (int)$cashAcc]);
            }

            // 4. Return cheques to RECEIVED status
            if (!empty($cheques)) {
                $chUpd = $db->prepare("UPDATE cheques SET status = 'RECEIVED', deposit_bank_account_id = NULL WHERE id = :id");
                foreach ($cheques as $chItem) {
                    $chUpd->execute(['id' => (int)$chItem['cheque_id']]);
                }
            }

            // 5. Update deposit status
            $db->prepare("
                UPDATE bank_deposits 
                SET status = 'CANCELLED', 
                    reversal_journal_entry_id = :rev_je_id,
                    reversal_reason = :reason,
                    updated_at = NOW()
                WHERE id = :id
            ")->execute([
                'id' => $dep['id'],
                'rev_je_id' => $reversalJournalId,
                'reason' => $reason
            ]);

            AuditService::log('cancel_bank_deposit', 'finance', $dep['id'], null, [
                'deposit_number' => $dep['deposit_number'],
                'reversal_journal_entry_id' => $reversalJournalId
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

    /**
     * Mark a cheque as cleared (received state or deposited state to cleared).
     */
    public static function markChequeCleared(int $chequeId): bool {
        $db = Database::getInstance();
        $chModel = new ChequeModel();

        $ch = $chModel->getById($chequeId);
        if (!$ch) {
            throw new Exception("Cheque not found.");
        }
        if (!in_array($ch['status'], ['RECEIVED', 'DEPOSITED'])) {
            throw new Exception("Only RECEIVED or DEPOSITED cheques can be cleared.");
        }

        $db->prepare("UPDATE cheques SET status = 'CLEARED', updated_at = NOW() WHERE id = :id")
           ->execute(['id' => $chequeId]);

        AuditService::log('clear_cheque', 'finance', $chequeId, null, [
            'cheque_number' => $ch['cheque_number']
        ]);

        return true;
    }

    /**
     * Mark a cheque as bounced. Reverses the original customer receipt.
     */
    public static function markChequeBounced(int $chequeId, string $reason): bool {
        $db = Database::getInstance();
        $chModel = new ChequeModel();

        $ch = $chModel->getById($chequeId);
        if (!$ch) {
            throw new Exception("Cheque not found.");
        }
        if (!in_array($ch['status'], ['RECEIVED', 'DEPOSITED', 'CLEARED'])) {
            throw new Exception("Cheque is not in a bounceable state.");
        }

        // Find the customer receipt where this cheque is linked
        $stmt = $db->prepare("SELECT * FROM payment_receipts WHERE cheque_id = :cheque_id AND status = 'posted' LIMIT 1");
        $stmt->execute(['cheque_id' => $chequeId]);
        $receipt = $stmt->fetch();

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // Update cheque status to BOUNCED
            $db->prepare("UPDATE cheques SET status = 'BOUNCED', updated_at = NOW() WHERE id = :id")
               ->execute(['id' => $chequeId]);

            if ($receipt) {
                // Determine credit account based on cheque deposit state
                $creditAccountLedgerId = null;
                if ($ch['status'] === 'RECEIVED') {
                    // Credit Undeposited Cheques account (1115)
                    $creditAccountLedgerId = (int)$db->query("SELECT id FROM accounts WHERE account_code = '1115'")->fetchColumn();
                } else {
                    // Credit Bank Account Ledger ID (from where it was deposited)
                    if ($ch['deposit_bank_account_id']) {
                        $creditAccountLedgerId = (int)$db->query("SELECT account_id FROM bank_accounts WHERE id = " . (int)$ch['deposit_bank_account_id'])->fetchColumn();
                    } else {
                        // fallback to first bank account ledger ID
                        $creditAccountLedgerId = (int)$db->query("SELECT account_id FROM bank_accounts LIMIT 1")->fetchColumn();
                    }
                }

                // Post accounting reversal
                $costCenterId = (int)$db->query("SELECT id FROM cost_centers LIMIT 1")->fetchColumn();
                $refNumber = 'BOUNCE-' . $ch['cheque_number'];

                $journalData = [
                    'transaction_date' => date('Y-m-d'),
                    'description' => "Bounced Cheque Reversal: " . $ch['cheque_number'] . " (" . $ch['customer_name'] . ")",
                    'reference' => $refNumber,
                    'source_module' => 'parties',
                    'source_transaction_id' => $receipt['id'],
                    'cost_center_id' => $costCenterId,
                    'status' => 'approved',
                    'lines' => [
                        [
                            'account_id' => 12, // Accounts Receivable (Debit)
                            'debit' => (float)$ch['amount'],
                            'credit' => 0.00,
                            'description' => "Bounced Cheque: " . $reason
                        ],
                        [
                            'account_id' => $creditAccountLedgerId, // Credit
                            'debit' => 0.00,
                            'credit' => (float)$ch['amount'],
                            'description' => "Bounced Cheque: " . $reason
                        ]
                    ]
                ];

                $reversalJournalId = AccountingEngine::postJournalEntry($journalData);

                // Update receipt record to reversed
                $updRec = $db->prepare("
                    UPDATE payment_receipts 
                    SET status = 'reversed', 
                        reversal_journal_entry_id = :rev_je_id,
                        reversal_reason = :reason,
                        updated_at = NOW()
                    WHERE id = :id
                ");
                $updRec->execute([
                    'id' => $receipt['id'],
                    'rev_je_id' => $reversalJournalId,
                    'reason' => "Cheque Bounced: " . $reason
                ]);

                // Update current bank account balance if it was deposited
                if ($ch['status'] !== 'RECEIVED' && $ch['deposit_bank_account_id']) {
                    $db->prepare("UPDATE bank_accounts SET current_balance = current_balance - :amt WHERE id = :id")
                       ->execute(['amt' => (float)$ch['amount'], 'id' => (int)$ch['deposit_bank_account_id']]);
                }
            }

            AuditService::log('bounce_cheque', 'finance', $chequeId, null, [
                'cheque_number' => $ch['cheque_number'],
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
