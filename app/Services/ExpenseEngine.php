<?php
namespace App\Services;

use Core\Database;
use Core\Auth;
use Exception;
use PDO;

class ExpenseEngine {

    /**
     * Create an expense voucher.
     */
    public static function createExpense(array $data): int {
        $db = Database::getInstance();

        // 1. Validation
        if (empty($data['payee'])) {
            throw new Exception("Payee/recipient is required.");
        }
        $amount = round((float)($data['amount'] ?? 0), 2);
        if ($amount <= 0) {
            throw new Exception("Expense amount must be greater than zero.");
        }
        if (empty($data['expense_category_id'])) {
            throw new Exception("Expense category is required.");
        }
        if (empty($data['cost_center_id'])) {
            throw new Exception("Cost center is required.");
        }
        if (empty($data['payment_method'])) {
            throw new Exception("Payment method is required.");
        }

        $expenseDate = $data['expense_date'] ?? date('Y-m-d');
        $categoryId = (int)$data['expense_category_id'];
        $costCenterId = (int)$data['cost_center_id'];
        $paymentMethod = $data['payment_method'];

        // 2. Resolve Accounts
        // Resolve Debit Account (Expense Account)
        $catStmt = $db->prepare("SELECT linked_account_id FROM expense_categories WHERE id = :id LIMIT 1");
        $catStmt->execute(['id' => $categoryId]);
        $linkedAcc = (int)$catStmt->fetchColumn();
        if (!$linkedAcc) {
            throw new Exception("Selected expense category is invalid or missing linked account.");
        }
        $expenseAccountId = !empty($data['expense_account_id']) ? (int)$data['expense_account_id'] : $linkedAcc;

        // Resolve Credit Account (Cash drawer, bank account, or accounts payable)
        $cashAccountId = null;
        $bankAccountId = null;
        $apAccountId = null;
        $creditAccountId = null;

        if ($paymentMethod === 'Cash') {
            $cashAccountId = !empty($data['cash_account_id']) ? (int)$data['cash_account_id'] : null;
            if (!$cashAccountId) {
                throw new Exception("Cash account is required for cash payments.");
            }
            $cashStmt = $db->prepare("SELECT account_id FROM cash_accounts WHERE id = :id LIMIT 1");
            $cashStmt->execute(['id' => $cashAccountId]);
            $creditAccountId = (int)$cashStmt->fetchColumn();
            if (!$creditAccountId) {
                throw new Exception("Invalid cash account.");
            }
        } elseif ($paymentMethod === 'Credit') {
            // Accounts Payable (normally account code '2110', which is ID 20 from seeders)
            $apStmt = $db->prepare("SELECT id FROM accounts WHERE account_code = '2110' LIMIT 1");
            $apStmt->execute();
            $creditAccountId = (int)$apStmt->fetchColumn();
            if (!$creditAccountId) {
                // Fallback to accounts payable if id is different
                $creditAccountId = 20; 
            }
            $apAccountId = $creditAccountId;
        } else {
            // Bank transfer, cheque, card, online
            $bankAccountId = !empty($data['bank_account_id']) ? (int)$data['bank_account_id'] : null;
            if (!$bankAccountId) {
                throw new Exception("Bank account is required for bank/electronic payments.");
            }
            $bankStmt = $db->prepare("SELECT account_id FROM bank_accounts WHERE id = :id LIMIT 1");
            $bankStmt->execute(['id' => $bankAccountId]);
            $creditAccountId = (int)$bankStmt->fetchColumn();
            if (!$creditAccountId) {
                throw new Exception("Invalid bank account.");
            }
        }

        // Duplicate prevention check
        if (!empty($data['source_module']) && !empty($data['source_transaction_id'])) {
            if (self::checkDuplicate($data['source_module'], $data['source_type'] ?? 'GENERAL_EXPENSE', (int)$data['source_transaction_id'])) {
                throw new Exception("Duplicate Prevention: A posted expense already exists for this source transaction.");
            }
        }

        $createdBy = $data['created_by'] ?? Auth::id() ?? 1;
        $expenseNumber = self::generateExpenseNumber();
        $status = $data['status'] ?? 'draft';

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            $stmt = $db->prepare("
                INSERT INTO expenses 
                (expense_number, expense_date, reference_number, payee, supplier_id, expense_category_id, description, amount, 
                 payment_method, cash_account_id, bank_account_id, accounts_payable_account_id, expense_account_id, cost_center_id, 
                 project_id, batch_id, service_job_id, machinery_id, machinery_rental_id, source_module, source_type, source_transaction_id, notes, status, created_by)
                VALUES 
                (:expense_number, :expense_date, :reference_number, :payee, :supplier_id, :expense_category_id, :description, :amount, 
                 :payment_method, :cash_account_id, :bank_account_id, :accounts_payable_account_id, :expense_account_id, :cost_center_id, 
                 :project_id, :batch_id, :service_job_id, :machinery_id, :machinery_rental_id, :source_module, :source_type, :source_transaction_id, :notes, :status, :created_by)
            ");

            $stmt->execute([
                'expense_number' => $expenseNumber,
                'expense_date' => $expenseDate,
                'reference_number' => $data['reference_number'] ?? null,
                'payee' => $data['payee'],
                'supplier_id' => !empty($data['supplier_id']) ? (int)$data['supplier_id'] : null,
                'expense_category_id' => $categoryId,
                'description' => $data['description'] ?? 'Operational Expense',
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'cash_account_id' => $cashAccountId,
                'bank_account_id' => $bankAccountId,
                'accounts_payable_account_id' => $apAccountId,
                'expense_account_id' => $expenseAccountId,
                'cost_center_id' => $costCenterId,
                'project_id' => !empty($data['project_id']) ? (int)$data['project_id'] : null,
                'batch_id' => !empty($data['batch_id']) ? (int)$data['batch_id'] : null,
                'service_job_id' => !empty($data['service_job_id']) ? (int)$data['service_job_id'] : null,
                'machinery_id' => !empty($data['machinery_id']) ? (int)$data['machinery_id'] : null,
                'machinery_rental_id' => !empty($data['machinery_rental_id']) ? (int)$data['machinery_rental_id'] : null,
                'source_module' => $data['source_module'] ?? 'GENERAL',
                'source_type' => $data['source_type'] ?? 'GENERAL_EXPENSE',
                'source_transaction_id' => !empty($data['source_transaction_id']) ? (int)$data['source_transaction_id'] : null,
                'notes' => $data['notes'] ?? null,
                'status' => $status,
                'created_by' => $createdBy
            ]);

            $expenseId = (int)$db->lastInsertId();

            // Save attachment details if passed in array
            if (!empty($data['attachment_file'])) {
                self::saveAttachment($expenseId, $data['attachment_file'], $createdBy);
            }

            AuditService::log('create_expense', 'finance', $expenseId, null, [
                'expense_number' => $expenseNumber,
                'status' => $status
            ]);

            if (!$inTransaction) {
                Database::commit();
            }

            return $expenseId;

        } catch (Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Submit an expense draft for approval.
     */
    public static function submitForApproval(int $id): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, status FROM expenses WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $exp = $stmt->fetch();

        if (!$exp) {
            throw new Exception("Expense voucher not found.");
        }
        if ($exp['status'] !== 'draft') {
            throw new Exception("Only draft expenses can be submitted.");
        }

        $update = $db->prepare("UPDATE expenses SET status = 'pending_approval' WHERE id = :id");
        $success = $update->execute(['id' => $id]);
        if ($success) {
            AuditService::log('submit_expense', 'finance', $id, null, ['new_status' => 'pending_approval']);
        }
        return $success;
    }

    /**
     * Approve a pending expense.
     */
    public static function approveExpense(int $id): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, status FROM expenses WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $exp = $stmt->fetch();

        if (!$exp) {
            throw new Exception("Expense voucher not found.");
        }
        if ($exp['status'] !== 'pending_approval') {
            throw new Exception("Only pending approval expenses can be approved.");
        }

        $userId = Auth::id() ?? 1;
        $update = $db->prepare("
            UPDATE expenses 
            SET status = 'approved', approved_by = :approved_by, approved_at = NOW() 
            WHERE id = :id
        ");
        $success = $update->execute([
            'id' => $id,
            'approved_by' => $userId
        ]);
        if ($success) {
            AuditService::log('approve_expense', 'finance', $id, null, ['new_status' => 'approved']);
        }
        return $success;
    }

    /**
     * Post an approved expense to double-entry ledger.
     */
    public static function postExpense(int $id): bool {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM expenses WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $exp = $stmt->fetch();

        if (!$exp) {
            throw new Exception("Expense voucher not found.");
        }

        if ($exp['status'] === 'posted') {
            return true;
        }

        if (!in_array($exp['status'], ['draft', 'approved'])) {
            throw new Exception("Only draft or approved expenses can be posted.");
        }

        // Get debit account (Expense Account)
        $expenseAccountId = (int)$exp['expense_account_id'];

        // Get credit account (Cash, Bank, or AP)
        $creditAccountId = null;
        if ($exp['payment_method'] === 'Cash') {
            $cashStmt = $db->prepare("SELECT account_id FROM cash_accounts WHERE id = :id LIMIT 1");
            $cashStmt->execute(['id' => (int)$exp['cash_account_id']]);
            $creditAccountId = (int)$cashStmt->fetchColumn();
        } elseif ($exp['payment_method'] === 'Credit') {
            $apStmt = $db->prepare("SELECT id FROM accounts WHERE account_code = '2110' LIMIT 1");
            $apStmt->execute();
            $creditAccountId = (int)$apStmt->fetchColumn() ?: 20;
        } else {
            $bankStmt = $db->prepare("SELECT account_id FROM bank_accounts WHERE id = :id LIMIT 1");
            $bankStmt->execute(['id' => (int)$exp['bank_account_id']]);
            $creditAccountId = (int)$bankStmt->fetchColumn();
        }

        if (!$creditAccountId) {
            throw new Exception("Could not resolve payment method account.");
        }

        $userId = Auth::id() ?? 1;

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // Build Journal Voucher payload
            $journalData = [
                'transaction_date' => $exp['expense_date'],
                'description' => "Expense: " . $exp['description'] . " (" . $exp['payee'] . ")",
                'reference' => $exp['expense_number'],
                'source_module' => strtolower($exp['source_module']),
                'source_transaction_id' => $exp['id'],
                'cost_center_id' => $exp['cost_center_id'],
                'project_id' => $exp['project_id'],
                'batch_id' => $exp['batch_id'],
                'status' => 'approved', // will post it directly
                'lines' => [
                    [
                        'account_id' => $expenseAccountId,
                        'debit' => round((float)$exp['amount'], 2),
                        'credit' => 0.00,
                        'description' => $exp['description']
                    ],
                    [
                        'account_id' => $creditAccountId,
                        'debit' => 0.00,
                        'credit' => round((float)$exp['amount'], 2),
                        'description' => "Payment via " . $exp['payment_method']
                    ]
                ]
            ];

            // Post double-entry journal entry
            $journalEntryId = AccountingEngine::postJournalEntry($journalData);

            // Update cash or bank account current balance if applicable
            if ($exp['payment_method'] === 'Cash') {
                $updBal = $db->prepare("UPDATE cash_accounts SET current_balance = current_balance - :amount WHERE id = :id");
                $updBal->execute(['amount' => $exp['amount'], 'id' => $exp['cash_account_id']]);
            } elseif ($exp['payment_method'] !== 'Credit') {
                $updBal = $db->prepare("UPDATE bank_accounts SET current_balance = current_balance - :amount WHERE id = :id");
                $updBal->execute(['amount' => $exp['amount'], 'id' => $exp['bank_account_id']]);
            }

            // Update expense record status
            $updExp = $db->prepare("
                UPDATE expenses 
                SET status = 'posted', journal_entry_id = :je_id, posted_by = :posted_by, posted_at = NOW() 
                WHERE id = :id
            ");
            $updExp->execute([
                'id' => $id,
                'je_id' => $journalEntryId,
                'posted_by' => $userId
            ]);

            AuditService::log('post_expense', 'finance', $id, null, [
                'expense_number' => $exp['expense_number'],
                'journal_entry_id' => $journalEntryId
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
     * Reverse a posted expense.
     */
    public static function reverseExpense(int $id, string $reason): bool {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM expenses WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $exp = $stmt->fetch();

        if (!$exp) {
            throw new Exception("Expense voucher not found.");
        }
        if ($exp['status'] !== 'posted' || empty($exp['journal_entry_id'])) {
            throw new Exception("Only posted expenses can be reversed.");
        }

        $userId = Auth::id() ?? 1;

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // Reverse the accounting journal entry
            $reversalJournalId = AccountingEngine::reverseJournalEntry((int)$exp['journal_entry_id'], "Reversal of Expense " . $exp['expense_number'] . ": " . $reason);

            // Revert Cash/Bank balances
            if ($exp['payment_method'] === 'Cash') {
                $updBal = $db->prepare("UPDATE cash_accounts SET current_balance = current_balance + :amount WHERE id = :id");
                $updBal->execute(['amount' => $exp['amount'], 'id' => $exp['cash_account_id']]);
            } elseif ($exp['payment_method'] !== 'Credit') {
                $updBal = $db->prepare("UPDATE bank_accounts SET current_balance = current_balance + :amount WHERE id = :id");
                $updBal->execute(['amount' => $exp['amount'], 'id' => $exp['bank_account_id']]);
            }

            // Update expense record status
            $updExp = $db->prepare("
                UPDATE expenses 
                SET status = 'reversed', reversal_journal_entry_id = :rev_je_id, reversed_by = :reversed_by, reversed_at = NOW(), reversal_reason = :reason 
                WHERE id = :id
            ");
            $updExp->execute([
                'id' => $id,
                'rev_je_id' => $reversalJournalId,
                'reversed_by' => $userId,
                'reason' => $reason
            ]);

            AuditService::log('reverse_expense', 'finance', $id, null, [
                'expense_number' => $exp['expense_number'],
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

    /**
     * Cancel an unposted expense.
     */
    public static function cancelExpense(int $id): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT status FROM expenses WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $status = $stmt->fetchColumn();

        if (!$status) {
            throw new Exception("Expense voucher not found.");
        }
        if (in_array($status, ['posted', 'reversed', 'cancelled'])) {
            throw new Exception("Cannot cancel an expense that is already {$status}.");
        }

        $update = $db->prepare("UPDATE expenses SET status = 'cancelled' WHERE id = :id");
        $success = $update->execute(['id' => $id]);
        if ($success) {
            AuditService::log('cancel_expense', 'finance', $id, null, ['new_status' => 'cancelled']);
        }
        return $success;
    }

    /**
     * Checks if a posted expense already exists for this source transaction.
     */
    public static function checkDuplicate(string $module, string $type, int $txId): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM expenses 
            WHERE source_module = :module AND source_type = :type AND source_transaction_id = :tx_id AND status = 'posted'
        ");
        $stmt->execute([
            'module' => $module,
            'type' => $type,
            'tx_id' => $txId
        ]);
        return (int)$stmt->fetchColumn() > 0;
    }

    private static function saveAttachment(int $expenseId, array $fileInfo, int $userId): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO expense_attachments 
            (expense_id, original_name, stored_name, file_path, mime_type, file_size, uploaded_by)
            VALUES (:expense_id, :original_name, :stored_name, :file_path, :mime_type, :file_size, :uploaded_by)
        ");
        $stmt->execute([
            'expense_id' => $expenseId,
            'original_name' => $fileInfo['original_name'],
            'stored_name' => $fileInfo['stored_name'],
            'file_path' => $fileInfo['file_path'],
            'mime_type' => $fileInfo['mime_type'],
            'file_size' => (int)$fileInfo['file_size'],
            'uploaded_by' => $userId
        ]);

        // Also update the attachment filepath in expenses main record
        $upd = $db->prepare("UPDATE expenses SET attachment = :file_path WHERE id = :id");
        $upd->execute([
            'file_path' => $fileInfo['file_path'],
            'id' => $expenseId
        ]);
    }

    private static function generateExpenseNumber(): string {
        $db = Database::getInstance();
        $prefix = 'EXP-' . date('Y') . '-';
        $stmt = $db->prepare("SELECT expense_number FROM expenses WHERE expense_number LIKE :prefix ORDER BY id DESC LIMIT 1");
        $stmt->execute(['prefix' => $prefix . '%']);
        $lastNum = $stmt->fetchColumn();

        if ($lastNum) {
            $seq = (int)substr($lastNum, -6);
            $newSeq = str_pad($seq + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '000001';
        }

        return $prefix . $newSeq;
    }
}
