<?php
namespace App\Services;

use Core\Database;
use Core\Auth;
use App\Models\SaleModel;
use App\Models\ProductModel;
use App\Models\Party;
use App\Services\InventoryEngine;
use App\Services\AccountingEngine;
use App\Services\ChequeDepositEngine;
use App\Services\AuditService;
use Exception;

class SalesEngine {

    /**
     * Save a sale voucher as DRAFT (create or update).
     */
    public static function saveSale(array $data): int {
        $db = Database::getInstance();
        $saleModel = new SaleModel();

        if (empty($data['customer_id'])) {
            throw new Exception("Customer is required.");
        }
        if (empty($data['warehouse_id'])) {
            throw new Exception("Warehouse is required.");
        }
        if (empty($data['items']) || !is_array($data['items'])) {
            throw new Exception("At least one product item is required.");
        }

        $saleDate = $data['sale_date'] ?? date('Y-m-d');
        $saleType = $data['sale_type'] ?? 'CASH';
        $paymentMethod = $data['payment_method'] ?? 'CASH';

        if ($saleType === 'CREDIT') {
            $paymentMethod = 'CREDIT';
        }

        // Calculate totals
        $subtotal = 0.00;
        $itemsToInsert = [];
        $prodModel = new ProductModel();

        foreach ($data['items'] as $item) {
            $prodId = (int)$item['product_id'];
            $qty = (float)$item['quantity'];
            if ($qty <= 0) {
                throw new Exception("Quantity must be greater than zero.");
            }

            $prod = $prodModel->getById($prodId);
            if (!$prod || !$prod['is_active']) {
                throw new Exception("Product is invalid or inactive.");
            }

            $price = round((float)($item['unit_price'] ?? $prod['default_selling_price']), 2);
            $disc = round((float)($item['discount'] ?? 0), 2);
            $lineTotal = round(($qty * $price) - $disc, 2);

            if ($lineTotal < 0) {
                throw new Exception("Line discount cannot exceed line subtotal.");
            }

            $subtotal += $qty * $price;

            $itemsToInsert[] = [
                'product_id' => $prodId,
                'quantity' => $qty,
                'unit_price' => $price,
                'discount' => $disc,
                'total' => $lineTotal
            ];
        }

        $discount = round((float)($data['discount'] ?? 0), 2);
        $total = round($subtotal - $discount, 2);
        if ($total < 0) {
            throw new Exception("Overall discount cannot exceed subtotal.");
        }

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            $saleId = !empty($data['id']) ? (int)$data['id'] : 0;
            $createdBy = Auth::id() ?? 1;

            if ($saleId > 0) {
                // Update
                $sale = $saleModel->getById($saleId);
                if (!$sale || $sale['status'] !== 'DRAFT') {
                    throw new Exception("Only draft sales can be modified.");
                }

                $stmt = $db->prepare("
                    UPDATE sales 
                    SET sale_date = :sale_date,
                        customer_id = :customer_id,
                        sale_type = :sale_type,
                        payment_method = :payment_method,
                        warehouse_id = :warehouse_id,
                        cash_account_id = :cash_account_id,
                        bank_account_id = :bank_account_id,
                        subtotal = :subtotal,
                        discount = :discount,
                        total = :total,
                        notes = :notes,
                        updated_at = NOW()
                    WHERE id = :id
                ");
                $stmt->execute([
                    'sale_date' => $saleDate,
                    'customer_id' => (int)$data['customer_id'],
                    'sale_type' => $saleType,
                    'payment_method' => $paymentMethod,
                    'warehouse_id' => (int)$data['warehouse_id'],
                    'cash_account_id' => !empty($data['cash_account_id']) ? (int)$data['cash_account_id'] : null,
                    'bank_account_id' => !empty($data['bank_account_id']) ? (int)$data['bank_account_id'] : null,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                    'notes' => $data['notes'] ?? null,
                    'id' => $saleId
                ]);

                // Delete old items
                $db->exec("DELETE FROM sale_items WHERE sale_id = " . $saleId);
            } else {
                // Create
                $saleNumber = $saleModel->generateSaleNumber();

                $stmt = $db->prepare("
                    INSERT INTO sales 
                    (sale_number, sale_date, customer_id, sale_type, payment_method, warehouse_id, cash_account_id, bank_account_id, subtotal, discount, total, notes, status, created_by)
                    VALUES 
                    (:sale_number, :sale_date, :customer_id, :sale_type, :payment_method, :warehouse_id, :cash_account_id, :bank_account_id, :subtotal, :discount, :total, :notes, 'DRAFT', :created_by)
                ");
                $stmt->execute([
                    'sale_number' => $saleNumber,
                    'sale_date' => $saleDate,
                    'customer_id' => (int)$data['customer_id'],
                    'sale_type' => $saleType,
                    'payment_method' => $paymentMethod,
                    'warehouse_id' => (int)$data['warehouse_id'],
                    'cash_account_id' => !empty($data['cash_account_id']) ? (int)$data['cash_account_id'] : null,
                    'bank_account_id' => !empty($data['bank_account_id']) ? (int)$data['bank_account_id'] : null,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                    'notes' => $data['notes'] ?? null,
                    'created_by' => $createdBy
                ]);
                $saleId = (int)$db->lastInsertId();
            }

            // Insert items
            $itemStmt = $db->prepare("
                INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, discount, total)
                VALUES (:sale_id, :product_id, :quantity, :unit_price, :discount, :total)
            ");
            foreach ($itemsToInsert as $item) {
                $item['sale_id'] = $saleId;
                $itemStmt->execute($mItem = [
                    'sale_id' => $item['sale_id'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'],
                    'total' => $item['total']
                ]);
            }

            if (!$inTransaction) {
                Database::commit();
            }

            return $saleId;

        } catch (Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Post a draft sale.
     * Reduces inventory stock, calculates AVCO COGS, and logs double-entry journal entries.
     */
    public static function postSale(int $id, array $chequeInfo = []): bool {
        $db = Database::getInstance();
        $saleModel = new SaleModel();

        $sale = $saleModel->getById($id);
        if (!$sale) {
            throw new Exception("Sale voucher not found.");
        }
        if ($sale['status'] !== 'DRAFT') {
            throw new Exception("Only draft sales can be posted.");
        }

        // Validate active Customer & Warehouse
        $custStatus = $db->query("SELECT status FROM parties WHERE id = " . (int)$sale['customer_id'])->fetchColumn();
        if ($custStatus !== 'active') {
            throw new Exception("Selected customer is inactive or invalid.");
        }

        $whActive = (int)$db->query("SELECT COUNT(*) FROM inventory_locations WHERE id = " . (int)$sale['warehouse_id'])->fetchColumn();
        if ($whActive === 0) {
            throw new Exception("Selected warehouse/location is invalid.");
        }

        // Fetch settings configurations for dynamic Chart of Accounts mapping
        $cfgRevenueCode = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'mkt_sales_revenue_acc'")->fetchColumn() ?: '4300';
        $cfgCogsCode = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'mkt_cogs_acc'")->fetchColumn() ?: '5100';
        $cfgInvCode = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'mkt_inventory_acc'")->fetchColumn() ?: '1150';
        $cfgArCode = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'mkt_ar_acc'")->fetchColumn() ?: '1140';
        $cfgCashCode = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'mkt_cash_acc'")->fetchColumn() ?: '1110';
        $cfgBankCode = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'mkt_bank_acc'")->fetchColumn() ?: '1120';
        $cfgChequeCode = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'mkt_cheques_acc'")->fetchColumn() ?: '1115';

        $revAccountId = (int)$db->query("SELECT id FROM accounts WHERE account_code = '{$cfgRevenueCode}'")->fetchColumn();
        $cogsAccountId = (int)$db->query("SELECT id FROM accounts WHERE account_code = '{$cfgCogsCode}'")->fetchColumn();
        $invAccountId = (int)$db->query("SELECT id FROM accounts WHERE account_code = '{$cfgInvCode}'")->fetchColumn();
        $arAccountId = (int)$db->query("SELECT id FROM accounts WHERE account_code = '{$cfgArCode}'")->fetchColumn();
        $chequeAccountId = (int)$db->query("SELECT id FROM accounts WHERE account_code = '{$cfgChequeCode}'")->fetchColumn();

        if (!$revAccountId || !$cogsAccountId || !$invAccountId || !$arAccountId || !$chequeAccountId) {
            throw new Exception("One or more required accounts from configurations are missing in Chart of Accounts.");
        }

        // Determine asset debit account
        $debitAccountId = null;
        $cashAccId = null;
        $bankAccId = null;
        $chequeId = null;

        if ($sale['sale_type'] === 'CREDIT' || $sale['payment_method'] === 'CREDIT') {
            $debitAccountId = $arAccountId;
        } elseif ($sale['payment_method'] === 'CASH') {
            $cashAccId = !empty($sale['cash_account_id']) ? (int)$sale['cash_account_id'] : null;
            if (!$cashAccId) {
                // Get first active cash account
                $cashAccId = (int)$db->query("SELECT id FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
            }
            if (!$cashAccId) {
                throw new Exception("No active Cash Drawer accounts found.");
            }
            $debitAccountId = (int)$db->query("SELECT account_id FROM cash_accounts WHERE id = " . $cashAccId)->fetchColumn();
        } elseif ($sale['payment_method'] === 'BANK') {
            $bankAccId = !empty($sale['bank_account_id']) ? (int)$sale['bank_account_id'] : null;
            if (!$bankAccId) {
                $bankAccId = (int)$db->query("SELECT id FROM bank_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
            }
            if (!$bankAccId) {
                throw new Exception("No active Bank Accounts found.");
            }
            $debitAccountId = (int)$db->query("SELECT account_id FROM bank_accounts WHERE id = " . $bankAccId)->fetchColumn();
        } else {
            // CHEQUE
            $debitAccountId = $chequeAccountId;
        }

        if (!$debitAccountId) {
            throw new Exception("Could not resolve asset debit account mapping.");
        }

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // 1. Process inventory Stock OUT and calculate COGS (using AVCO costing method)
            $totalCogs = 0.00;
            foreach ($sale['items'] as $item) {
                $itemCogs = InventoryEngine::recordStockOut(
                    (int)$item['product_id'],
                    (int)$sale['warehouse_id'],
                    (float)$item['quantity'],
                    'SALE',
                    'MARKETPLACE',
                    (int)$sale['id'],
                    $sale['sale_number']
                );
                $totalCogs += $itemCogs;
            }

            // 2. If Payment Method is CHEQUE, automatically register the cheque
            if ($sale['payment_method'] === 'CHEQUE') {
                $chNum = trim($chequeInfo['cheque_number'] ?? 'CHQ-' . $sale['sale_number']);
                $chBank = trim($chequeInfo['bank_name'] ?? 'Customer Bank');
                $chDate = $chequeInfo['cheque_date'] ?? date('Y-m-d');

                $chequeId = ChequeDepositEngine::recordCheque([
                    'cheque_number' => $chNum,
                    'party_id' => $sale['customer_id'],
                    'bank_name' => $chBank,
                    'cheque_date' => $chDate,
                    'amount' => $sale['total'],
                    'received_issued_date' => $sale['sale_date'],
                    'reference_number' => $sale['sale_number'],
                    'notes' => 'Generated automatically from Marketplace Sale ' . $sale['sale_number']
                ]);
            }

            // 3. Post double-entry journal entry
            $costCenterId = (int)$db->query("SELECT id FROM cost_centers LIMIT 1")->fetchColumn();
            $journalLines = [
                // Line 1: Debit asset account (Cash/Bank/Receivable/Cheques)
                [
                    'account_id' => $debitAccountId,
                    'debit' => (float)$sale['total'],
                    'credit' => 0.00,
                    'description' => "Marketplace Sale " . $sale['sale_number']
                ],
                // Line 2: Credit Marketplace Revenue
                [
                    'account_id' => $revAccountId,
                    'debit' => 0.00,
                    'credit' => (float)$sale['total'],
                    'description' => "Marketplace Sale " . $sale['sale_number']
                ]
            ];

            // If COGS occurs, write COGS lines
            if ($totalCogs > 0) {
                $journalLines[] = [
                    'account_id' => $cogsAccountId,
                    'debit' => $totalCogs,
                    'credit' => 0.00,
                    'description' => "COGS for Marketplace Sale " . $sale['sale_number']
                ];
                $journalLines[] = [
                    'account_id' => $invAccountId,
                    'debit' => 0.00,
                    'credit' => $totalCogs,
                    'description' => "Inventory Stock reduction for Sale " . $sale['sale_number']
                ];
            }

            $journalData = [
                'transaction_date' => $sale['sale_date'],
                'description' => "Marketplace Sale Invoice (" . $sale['sale_number'] . ")",
                'reference' => $sale['sale_number'],
                'source_module' => 'marketplace',
                'source_transaction_id' => $sale['id'],
                'cost_center_id' => $costCenterId,
                'status' => 'approved',
                'lines' => $journalLines
            ];

            $journalId = AccountingEngine::postJournalEntry($journalData);

            // 4. Adjust cash drawer or bank balances if CASH or BANK
            if ($sale['payment_method'] === 'CASH') {
                $db->prepare("UPDATE cash_accounts SET current_balance = current_balance + :amt WHERE id = :id")
                   ->execute(['amt' => (float)$sale['total'], 'id' => $cashAccId]);
            } elseif ($sale['payment_method'] === 'BANK') {
                $db->prepare("UPDATE bank_accounts SET current_balance = current_balance + :amt WHERE id = :id")
                   ->execute(['amt' => (float)$sale['total'], 'id' => $bankAccId]);
            }

            // 5. Update sale status, link journal, cash drawer, bank, cheque ID
            $db->prepare("
                UPDATE sales 
                SET status = 'POSTED',
                    journal_entry_id = :je_id,
                    cash_account_id = :cash_id,
                    bank_account_id = :bank_id,
                    cheque_id = :cheque_id,
                    updated_at = NOW()
                WHERE id = :id
            ")->execute([
                'id' => $sale['id'],
                'je_id' => $journalId,
                'cash_id' => $cashAccId,
                'bank_id' => $bankAccId,
                'cheque_id' => $chequeId
            ]);

            AuditService::log('post_marketplace_sale', 'marketplace', $sale['id'], null, [
                'sale_number' => $sale['sale_number'],
                'total' => $sale['total'],
                'journal_entry_id' => $journalId
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
     * Cancel a posted sale.
     * Reverts stock movements, reverses journal entry, and returns balances.
     */
    public static function cancelSale(int $id, string $reason): bool {
        $db = Database::getInstance();
        $saleModel = new SaleModel();

        $sale = $saleModel->getById($id);
        if (!$sale) {
            throw new Exception("Sale voucher not found.");
        }

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            if ($sale['status'] === 'DRAFT') {
                // Draft can just be set to CANCELLED without reversals
                $db->prepare("UPDATE sales SET status = 'CANCELLED', reversal_reason = :reason, updated_at = NOW() WHERE id = :id")
                   ->execute(['id' => $sale['id'], 'reason' => $reason]);
            } else {
                // POSTED sale cancellation requires full reversal
                if ($sale['status'] !== 'POSTED' || empty($sale['journal_entry_id'])) {
                    throw new Exception("Only posted sales can be reversed.");
                }

                // 1. Reverse Stock movements (restoring shelf stock)
                InventoryEngine::reverseStockMovement('MARKETPLACE', (int)$sale['id']);

                // 2. Reverse accounting journal entry
                $reversalJournalId = AccountingEngine::reverseJournalEntry(
                    (int)$sale['journal_entry_id'],
                    "Reversal of Marketplace Sale " . $sale['sale_number'] . ": " . $reason
                );

                // 3. Revert cash or bank balances if CASH or BANK
                if ($sale['payment_method'] === 'CASH' && $sale['cash_account_id']) {
                    $db->prepare("UPDATE cash_accounts SET current_balance = current_balance - :amt WHERE id = :id")
                       ->execute(['amt' => (float)$sale['total'], 'id' => (int)$sale['cash_account_id']]);
                } elseif ($sale['payment_method'] === 'BANK' && $sale['bank_account_id']) {
                    $db->prepare("UPDATE bank_accounts SET current_balance = current_balance - :amt WHERE id = :id")
                       ->execute(['amt' => (float)$sale['total'], 'id' => (int)$sale['bank_account_id']]);
                }

                // 4. Cancel linked cheque if payment method was CHEQUE
                if ($sale['payment_method'] === 'CHEQUE' && $sale['cheque_id']) {
                    $db->prepare("UPDATE cheques SET status = 'CANCELLED', updated_at = NOW() WHERE id = :id")
                       ->execute(['id' => (int)$sale['cheque_id']]);
                }

                // 5. Update sale status
                $db->prepare("
                    UPDATE sales 
                    SET status = 'CANCELLED', 
                        reversal_journal_entry_id = :rev_je_id,
                        reversal_reason = :reason,
                        updated_at = NOW()
                    WHERE id = :id
                ")->execute([
                    'id' => $sale['id'],
                    'rev_je_id' => $reversalJournalId,
                    'reason' => $reason
                ]);
            }

            AuditService::log('cancel_marketplace_sale', 'marketplace', $sale['id'], null, [
                'sale_number' => $sale['sale_number'],
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
