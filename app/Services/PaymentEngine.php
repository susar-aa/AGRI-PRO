<?php
namespace App\Services;

use Core\Database;
use Core\Auth;
use App\Models\Party;
use App\Models\ReceiptPaymentModel;
use App\Services\AccountingEngine;
use App\Services\AuditService;
use Exception;

class PaymentEngine {

    /**
     * Record a draft payment or receipt.
     */
    public static function recordPayment(array $data): int {
        $db = Database::getInstance();
        $partyModel = new Party();
        $pmModel = new ReceiptPaymentModel();

        // 1. Validations
        if (empty($data['party_id'])) {
            throw new Exception("Business partner / party is required.");
        }
        $amount = round((float)($data['amount'] ?? 0), 2);
        if ($amount <= 0) {
            throw new Exception("Amount must be greater than zero.");
        }
        if (empty($data['payment_type']) || !in_array($data['payment_type'], ['RECEIPT', 'PAYMENT'])) {
            throw new Exception("Invalid payment type.");
        }
        if (empty($data['payment_method']) || !in_array($data['payment_method'], ['Cash', 'Bank Transfer', 'Cheque'])) {
            throw new Exception("Invalid payment method. Use 'Cash', 'Bank Transfer' or 'Cheque'.");
        }

        $partyId = (int)$data['party_id'];
        $party = $partyModel->getById($partyId);
        if (!$party || $party['status'] !== 'active') {
            throw new Exception("Selected party contact is invalid or inactive.");
        }

        $type = $data['payment_type'];
        $method = $data['payment_method'];

        if ($type === 'RECEIPT') {
            if (!in_array($party['party_type'], ['CUSTOMER', 'BOTH'])) {
                throw new Exception("Receipts can only be recorded for customer profiles.");
            }
        } else {
            if (!in_array($party['party_type'], ['SUPPLIER', 'BOTH'])) {
                throw new Exception("Payments can only be recorded for supplier profiles.");
            }
            if ($method === 'Cheque') {
                throw new Exception("Issued Cheques are not supported in this stage.");
            }
        }

        $cashAccountId = null;
        $bankAccountId = null;
        $chequeId = null;

        if ($method === 'Cash') {
            $cashAccountId = !empty($data['cash_account_id']) ? (int)$data['cash_account_id'] : null;
            if (!$cashAccountId) {
                throw new Exception("Cash account drawer is required for cash payments.");
            }
            $ca = $db->query("SELECT status FROM cash_accounts WHERE id = " . $cashAccountId)->fetchColumn();
            if ($ca !== 'active') {
                throw new Exception("Selected cash drawer account is inactive.");
            }
        } elseif ($method === 'Bank Transfer') {
            $bankAccountId = !empty($data['bank_account_id']) ? (int)$data['bank_account_id'] : null;
            if (!$bankAccountId) {
                throw new Exception("Bank account is required for bank transfer payments.");
            }
            $ba = $db->query("SELECT status FROM bank_accounts WHERE id = " . $bankAccountId)->fetchColumn();
            if ($ba !== 'active') {
                throw new Exception("Selected bank account is inactive.");
            }
        } else {
            // Cheque
            $chequeId = !empty($data['cheque_id']) ? (int)$data['cheque_id'] : null;
            if (!$chequeId) {
                throw new Exception("Linked customer cheque ID is required.");
            }
            $ch = $db->query("SELECT status FROM cheques WHERE id = " . $chequeId)->fetchColumn();
            if ($ch !== 'RECEIVED') {
                throw new Exception("Selected cheque must be in RECEIVED status.");
            }
        }

        $paymentDate = $data['payment_date'] ?? date('Y-m-d');
        $paymentNumber = $pmModel->generatePaymentNumber($type);
        $createdBy = $data['created_by'] ?? Auth::id() ?? 1;

        $stmt = $db->prepare("
            INSERT INTO payment_receipts 
            (payment_number, payment_type, payment_date, party_id, payment_method, cash_account_id, bank_account_id, cheque_id, amount, reference_number, notes, status, created_by)
            VALUES 
            (:payment_number, :payment_type, :payment_date, :party_id, :payment_method, :cash_account_id, :bank_account_id, :cheque_id, :amount, :reference_number, :notes, 'draft', :created_by)
        ");

        $stmt->execute([
            'payment_number' => $paymentNumber,
            'payment_type' => $type,
            'payment_date' => $paymentDate,
            'party_id' => $partyId,
            'payment_method' => $method,
            'cash_account_id' => $cashAccountId,
            'bank_account_id' => $bankAccountId,
            'cheque_id' => $chequeId,
            'amount' => $amount,
            'reference_number' => $data['reference_number'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $createdBy
        ]);

        $id = (int)$db->lastInsertId();
        AuditService::log('create_payment_receipt', 'parties', $partyId, null, [
            'payment_number' => $paymentNumber,
            'type' => $type,
            'amount' => $amount
        ]);

        return $id;
    }

    /**
     * Post a draft payment/receipt to double-entry ledger.
     */
    public static function postPayment(int $id): bool {
        $db = Database::getInstance();
        $pmModel = new ReceiptPaymentModel();

        $pr = $pmModel->getById($id);
        if (!$pr) {
            throw new Exception("Payment/receipt voucher not found.");
        }
        if ($pr['status'] !== 'draft') {
            throw new Exception("Only draft entries can be posted.");
        }

        $amount = (float)$pr['amount'];
        $method = $pr['payment_method'];
        $type = $pr['payment_type'];

        // Resolve debit/credit accounts
        $debitAccountId = null;
        $creditAccountId = null;

        if ($method === 'Cheque') {
            // Resolve Undeposited Cheques account ID (1115)
            $assetAccountId = (int)$db->query("SELECT id FROM accounts WHERE account_code = '1115'")->fetchColumn();
            if (!$assetAccountId) {
                throw new Exception("Undeposited Cheques account (1115) is missing in Chart of Accounts.");
            }
        } else {
            // Fetch Cash or Bank asset account code
            $assetAccountId = null;
            if ($method === 'Cash') {
                $assetAccountId = (int)$db->query("SELECT account_id FROM cash_accounts WHERE id = " . (int)$pr['cash_account_id'])->fetchColumn();
            } else {
                $assetAccountId = (int)$db->query("SELECT account_id FROM bank_accounts WHERE id = " . (int)$pr['bank_account_id'])->fetchColumn();
            }
        }

        if (!$assetAccountId) {
            throw new Exception("Could not resolve asset account mapping.");
        }

        // Accounts Receivable: ID 12 (1140)
        // Accounts Payable: ID 20 (2110)
        if ($type === 'RECEIPT') {
            // Receipt: Dr Cash/Bank/Cheques, Cr Accounts Receivable
            $debitAccountId = $assetAccountId;
            $creditAccountId = 12; // AR
        } else {
            // Payment: Dr Accounts Payable, Cr Cash/Bank
            $debitAccountId = 20; // AP
            $creditAccountId = $assetAccountId;
        }

        // Get default cost center
        $costCenterId = (int)$db->query("SELECT id FROM cost_centers LIMIT 1")->fetchColumn();

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // 1. Post double-entry journal entry
            $journalData = [
                'transaction_date' => $pr['payment_date'],
                'description' => ($type === 'RECEIPT' ? 'Receipt: ' : 'Payment: ') . ($pr['notes'] ?: "Payment Voucher") . " (" . $pr['party_name'] . ")",
                'reference' => $pr['payment_number'],
                'source_module' => 'parties',
                'source_transaction_id' => $pr['id'],
                'cost_center_id' => $costCenterId,
                'status' => 'approved',
                'lines' => [
                    [
                        'account_id' => $debitAccountId,
                        'debit' => $amount,
                        'credit' => 0.00,
                        'description' => $pr['notes'] ?: ($type === 'RECEIPT' ? 'Customer Receipt' : 'Supplier Payment')
                    ],
                    [
                        'account_id' => $creditAccountId,
                        'debit' => 0.00,
                        'credit' => $amount,
                        'description' => $pr['notes'] ?: ($type === 'RECEIPT' ? 'Customer Receipt' : 'Supplier Payment')
                    ]
                ]
            ];

            $journalId = AccountingEngine::postJournalEntry($journalData);

            // 2. Adjust Cash draw or Bank account current balance (exclude Cheque received)
            if ($method === 'Cash') {
                $change = ($type === 'RECEIPT') ? $amount : -$amount;
                $upd = $db->prepare("UPDATE cash_accounts SET current_balance = current_balance + :change WHERE id = :id");
                $upd->execute(['change' => $change, 'id' => (int)$pr['cash_account_id']]);
            } elseif ($method === 'Bank Transfer') {
                $change = ($type === 'RECEIPT') ? $amount : -$amount;
                $upd = $db->prepare("UPDATE bank_accounts SET current_balance = current_balance + :change WHERE id = :id");
                $upd->execute(['change' => $change, 'id' => (int)$pr['bank_account_id']]);
            }

            // 3. Update payment_receipts status to posted
            $updStatus = $db->prepare("
                UPDATE payment_receipts 
                SET status = 'posted', journal_entry_id = :je_id 
                WHERE id = :id
            ");
            $updStatus->execute([
                'id' => $pr['id'],
                'je_id' => $journalId
            ]);

            AuditService::log('post_payment_receipt', 'parties', (int)$pr['party_id'], null, [
                'payment_number' => $pr['payment_number'],
                'journal_entry_id' => $journalId,
                'amount' => $amount
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
     * Reverse a posted payment or receipt.
     */
    public static function reversePayment(int $id, string $reason): bool {
        $db = Database::getInstance();
        $pmModel = new ReceiptPaymentModel();

        $pr = $pmModel->getById($id);
        if (!$pr) {
            throw new Exception("Payment/receipt voucher not found.");
        }
        if ($pr['status'] !== 'posted' || empty($pr['journal_entry_id'])) {
            throw new Exception("Only posted records can be reversed.");
        }

        $amount = (float)$pr['amount'];
        $method = $pr['payment_method'];
        $type = $pr['payment_type'];

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // 1. Reverse the accounting journal entry
            $reversalJournalId = AccountingEngine::reverseJournalEntry((int)$pr['journal_entry_id'], "Reversal of " . $pr['payment_number'] . ": " . $reason);

            // 2. Adjust Cash draw or Bank account current balance back
            if ($method === 'Cash') {
                $change = ($type === 'RECEIPT') ? -$amount : $amount;
                $upd = $db->prepare("UPDATE cash_accounts SET current_balance = current_balance + :change WHERE id = :id");
                $upd->execute(['change' => $change, 'id' => (int)$pr['cash_account_id']]);
            } elseif ($method === 'Bank Transfer') {
                $change = ($type === 'RECEIPT') ? -$amount : $amount;
                $upd = $db->prepare("UPDATE bank_accounts SET current_balance = current_balance + :change WHERE id = :id");
                $upd->execute(['change' => $change, 'id' => (int)$pr['bank_account_id']]);
            }

            // 3. Update payment_receipts status to reversed
            $updStatus = $db->prepare("
                UPDATE payment_receipts 
                SET status = 'reversed', 
                    reversal_journal_entry_id = :rev_je_id,
                    reversal_reason = :reason,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $updStatus->execute([
                'id' => $pr['id'],
                'rev_je_id' => $reversalJournalId,
                'reason' => $reason
            ]);

            // 4. Cancel linked cheque if payment method is Cheque
            if ($method === 'Cheque' && !empty($pr['cheque_id'])) {
                $db->prepare("UPDATE cheques SET status = 'CANCELLED', updated_at = NOW() WHERE id = :id")
                   ->execute(['id' => (int)$pr['cheque_id']]);
            }

            AuditService::log('reverse_payment_receipt', 'parties', (int)$pr['party_id'], null, [
                'payment_number' => $pr['payment_number'],
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
