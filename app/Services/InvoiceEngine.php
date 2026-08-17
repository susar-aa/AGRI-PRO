<?php
namespace App\Services;

use Core\Database;
use Core\Auth;
use App\Models\InvoiceModel;
use App\Models\ProductModel;
use App\Models\ServiceModel;
use App\Models\Party;
use App\Services\InventoryEngine;
use App\Services\AccountingEngine;
use App\Services\ChequeDepositEngine;
use App\Services\AuditService;
use Exception;

class InvoiceEngine {

    /**
     * Save an invoice voucher as DRAFT.
     */
    public static function saveInvoice(array $data): int {
        $db = Database::getInstance();
        $invModel = new InvoiceModel();

        if (empty($data['customer_id'])) {
            throw new Exception("Customer is required.");
        }
        if (empty($data['items']) || !is_array($data['items'])) {
            throw new Exception("At least one product or service line item is required.");
        }

        $invoiceDate = $data['invoice_date'] ?? date('Y-m-d');
        $paymentType = $data['payment_type'] ?? 'CASH';

        // Calculate totals
        $subtotal = 0.00;
        $itemsToInsert = [];
        $prodModel = new ProductModel();
        $srvModel = new ServiceModel();

        $hasProducts = false;

        foreach ($data['items'] as $item) {
            $type = $item['item_type'] ?? 'PRODUCT';
            $qty = (float)$item['quantity'];
            if ($qty <= 0) {
                throw new Exception("Quantity must be greater than zero.");
            }

            $price = 0.00;
            $prodId = null;
            $srvId = null;

            if ($type === 'PRODUCT') {
                $prodId = (int)$item['product_id'];
                $prod = $prodModel->getById($prodId);
                if (!$prod || !$prod['is_active']) {
                    throw new Exception("Selected product is invalid or inactive.");
                }
                $price = round((float)($item['unit_price'] ?? $prod['default_selling_price']), 2);
                $hasProducts = true;
            } else {
                $srvId = (int)$item['service_id'];
                $srv = $srvModel->getById($srvId);
                if (!$srv || !$srv['is_active']) {
                    throw new Exception("Selected service is invalid or inactive.");
                }
                $price = round((float)($item['unit_price'] ?? $srv['default_price']), 2);
            }

            $disc = round((float)($item['discount'] ?? 0), 2);
            $lineTotal = round(($qty * $price) - $disc, 2);

            if ($lineTotal < 0) {
                throw new Exception("Line discount cannot exceed line subtotal.");
            }

            $subtotal += $qty * $price;

            $itemsToInsert[] = [
                'item_type' => $type,
                'product_id' => $prodId,
                'service_id' => $srvId,
                'description' => trim($item['description'] ?? ''),
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

        // If products exist, warehouse is required
        $warehouseId = !empty($data['warehouse_id']) ? (int)$data['warehouse_id'] : null;
        if ($hasProducts && !$warehouseId) {
            throw new Exception("Warehouse is required because products are included in this invoice.");
        }

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            $invoiceId = !empty($data['id']) ? (int)$data['id'] : 0;
            $createdBy = Auth::id() ?? 1;

            if ($invoiceId > 0) {
                // Update
                $inv = $invModel->getById($invoiceId);
                if (!$inv || $inv['status'] !== 'DRAFT') {
                    throw new Exception("Only draft invoices can be modified.");
                }

                $stmt = $db->prepare("
                    UPDATE invoices 
                    SET invoice_date = :invoice_date,
                        customer_id = :customer_id,
                        reference = :reference,
                        notes = :notes,
                        payment_type = :payment_type,
                        warehouse_id = :warehouse_id,
                        cash_account_id = :cash_account_id,
                        bank_account_id = :bank_account_id,
                        subtotal = :subtotal,
                        discount = :discount,
                        total = :total,
                        updated_at = NOW()
                    WHERE id = :id
                ");
                $stmt->execute([
                    'invoice_date' => $invoiceDate,
                    'customer_id' => (int)$data['customer_id'],
                    'reference' => $data['reference'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'payment_type' => $paymentType,
                    'warehouse_id' => $warehouseId,
                    'cash_account_id' => !empty($data['cash_account_id']) ? (int)$data['cash_account_id'] : null,
                    'bank_account_id' => !empty($data['bank_account_id']) ? (int)$data['bank_account_id'] : null,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                    'id' => $invoiceId
                ]);

                // Delete old items
                $db->exec("DELETE FROM invoice_items WHERE invoice_id = " . $invoiceId);
            } else {
                // Create new
                $invoiceNumber = $invModel->generateInvoiceNumber();

                $stmt = $db->prepare("
                    INSERT INTO invoices 
                    (invoice_number, invoice_date, customer_id, reference, notes, payment_type, warehouse_id, cash_account_id, bank_account_id, subtotal, discount, total, status, created_by)
                    VALUES 
                    (:invoice_number, :invoice_date, :customer_id, :reference, :notes, :payment_type, :warehouse_id, :cash_account_id, :bank_account_id, :subtotal, :discount, :total, 'DRAFT', :created_by)
                ");
                $stmt->execute([
                    'invoice_number' => $invoiceNumber,
                    'invoice_date' => $invoiceDate,
                    'customer_id' => (int)$data['customer_id'],
                    'reference' => $data['reference'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'payment_type' => $paymentType,
                    'warehouse_id' => $warehouseId,
                    'cash_account_id' => !empty($data['cash_account_id']) ? (int)$data['cash_account_id'] : null,
                    'bank_account_id' => !empty($data['bank_account_id']) ? (int)$data['bank_account_id'] : null,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                    'created_by' => $createdBy
                ]);
                $invoiceId = (int)$db->lastInsertId();
            }

            // Insert items
            $itemStmt = $db->prepare("
                INSERT INTO invoice_items (invoice_id, item_type, product_id, service_id, description, quantity, unit_price, discount, total)
                VALUES (:invoice_id, :item_type, :product_id, :service_id, :description, :quantity, :unit_price, :discount, :total)
            ");
            foreach ($itemsToInsert as $item) {
                $item['invoice_id'] = $invoiceId;
                $itemStmt->execute($item);
            }

            if (!$inTransaction) {
                Database::commit();
            }

            return $invoiceId;

        } catch (Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Post a draft invoice.
     */
    public static function postInvoice(int $id, array $chequeInfo = []): bool {
        $db = Database::getInstance();
        $invModel = new InvoiceModel();

        $invoice = $invModel->getById($id);
        if (!$invoice) {
            throw new Exception("Invoice not found.");
        }
        if ($invoice['status'] !== 'DRAFT') {
            throw new Exception("Only draft invoices can be posted.");
        }

        // Validate active Customer
        $custStatus = $db->query("SELECT status FROM parties WHERE id = " . (int)$invoice['customer_id'])->fetchColumn();
        if ($custStatus !== 'active') {
            throw new Exception("Selected customer is inactive or invalid.");
        }

        // Resolve config accounts
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

        if ($invoice['payment_type'] === 'CREDIT') {
            $debitAccountId = $arAccountId;
        } elseif ($invoice['payment_type'] === 'CASH') {
            $cashAccId = !empty($invoice['cash_account_id']) ? (int)$invoice['cash_account_id'] : null;
            if (!$cashAccId) {
                $cashAccId = (int)$db->query("SELECT id FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
            }
            if (!$cashAccId) {
                throw new Exception("No active Cash Drawer accounts found.");
            }
            $debitAccountId = (int)$db->query("SELECT account_id FROM cash_accounts WHERE id = " . $cashAccId)->fetchColumn();
        } elseif ($invoice['payment_type'] === 'BANK') {
            $bankAccId = !empty($invoice['bank_account_id']) ? (int)$invoice['bank_account_id'] : null;
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
            // 1. Process inventory Stock OUT for PRODUCTS & calculate COGS
            $totalCogs = 0.00;
            $hasProducts = false;
            $hasServices = false;

            // Product and Service allocations
            $productRevenueSum = 0.00;
            $serviceRevenueAllocations = []; // Array of [account_id => total_amount]

            foreach ($invoice['items'] as $item) {
                if ($item['item_type'] === 'PRODUCT') {
                    $itemCogs = InventoryEngine::recordStockOut(
                        (int)$item['product_id'],
                        (int)$invoice['warehouse_id'],
                        (float)$item['quantity'],
                        'SALE',
                        'SALES_INVOICE',
                        (int)$invoice['id'],
                        $invoice['invoice_number']
                    );
                    $totalCogs += $itemCogs;
                    $productRevenueSum += (float)$item['total'];
                    $hasProducts = true;
                } else {
                    // Service
                    $srv = $db->query("SELECT revenue_account_id FROM services WHERE id = " . (int)$item['service_id'])->fetch();
                    if (!$srv) {
                        throw new Exception("Service item mapping error.");
                    }
                    $srvAcc = (int)$srv['revenue_account_id'];
                    if (!isset($serviceRevenueAllocations[$srvAcc])) {
                        $serviceRevenueAllocations[$srvAcc] = 0.00;
                    }
                    $serviceRevenueAllocations[$srvAcc] += (float)$item['total'];
                    $hasServices = true;
                }
            }

            // Allocate invoice level discount proportionally
            // Net Total = Subtotal - Discount
            // Factor = Net Total / Subtotal (if Subtotal > 0)
            $subtotal = (float)$invoice['subtotal'];
            $discount = (float)$invoice['discount'];
            $factor = 1.0000;
            if ($subtotal > 0) {
                $factor = ($subtotal - $discount) / $subtotal;
            }

            // 2. Prepare accounting lines
            $journalLines = [];

            // Debit Line: Cash/Bank/Receivable/Cheques
            $journalLines[] = [
                'account_id' => $debitAccountId,
                'debit' => (float)$invoice['total'],
                'credit' => 0.00,
                'description' => "Invoice " . $invoice['invoice_number']
            ];

            // Credits:
            // Product Revenue Credits
            if ($productRevenueSum > 0) {
                $journalLines[] = [
                    'account_id' => $revAccountId,
                    'debit' => 0.00,
                    'credit' => round($productRevenueSum * $factor, 2),
                    'description' => "Product Revenue: Invoice " . $invoice['invoice_number']
                ];
            }

            // Service Revenue Credits
            foreach ($serviceRevenueAllocations as $srvAcc => $amt) {
                $journalLines[] = [
                    'account_id' => $srvAcc,
                    'debit' => 0.00,
                    'credit' => round($amt * $factor, 2),
                    'description' => "Service Revenue: Invoice " . $invoice['invoice_number']
                ];
            }

            // If COGS occurs, write COGS lines
            if ($totalCogs > 0) {
                $journalLines[] = [
                    'account_id' => $cogsAccountId,
                    'debit' => $totalCogs,
                    'credit' => 0.00,
                    'description' => "COGS for Invoice " . $invoice['invoice_number']
                ];
                $journalLines[] = [
                    'account_id' => $invAccountId,
                    'debit' => 0.00,
                    'credit' => $totalCogs,
                    'description' => "Inventory Stock reduction for Invoice " . $invoice['invoice_number']
                ];
            }

            // Double check journal balancing
            $totDeb = 0.00;
            $totCrd = 0.00;
            foreach ($journalLines as &$line) {
                $totDeb += $line['debit'];
                $totCrd += $line['credit'];
            }
            if (abs($totDeb - $totCrd) > 0.001) {
                // If there's rounding difference (e.g. 0.01), adjust on the largest revenue credit line
                $diff = $totDeb - $totCrd;
                foreach ($journalLines as &$line) {
                    if ($line['credit'] > 0) {
                        $line['credit'] += $diff;
                        break;
                    }
                }
            }

            // 3. Post double-entry journal entry
            $costCenterId = (int)$db->query("SELECT id FROM cost_centers LIMIT 1")->fetchColumn();
            $journalData = [
                'transaction_date' => $invoice['invoice_date'],
                'description' => "Central Invoice (" . $invoice['invoice_number'] . ")",
                'reference' => $invoice['invoice_number'],
                'source_module' => 'invoices',
                'source_transaction_id' => $invoice['id'],
                'cost_center_id' => $costCenterId,
                'status' => 'approved',
                'lines' => $journalLines
            ];

            $journalId = AccountingEngine::postJournalEntry($journalData);

            // 4. Adjust cash drawer or bank balances
            if ($invoice['payment_type'] === 'CASH') {
                $db->prepare("UPDATE cash_accounts SET current_balance = current_balance + :amt WHERE id = :id")
                   ->execute(['amt' => (float)$invoice['total'], 'id' => $cashAccId]);
            } elseif ($invoice['payment_type'] === 'BANK') {
                $db->prepare("UPDATE bank_accounts SET current_balance = current_balance + :amt WHERE id = :id")
                   ->execute(['amt' => (float)$invoice['total'], 'id' => $bankAccId]);
            }

            // 5. Create cheque if payment type is CHEQUE
            if ($invoice['payment_type'] === 'CHEQUE') {
                $chNum = trim($chequeInfo['cheque_number'] ?? 'CHQ-' . $invoice['invoice_number']);
                $chBank = trim($chequeInfo['bank_name'] ?? 'Customer Bank');
                $chDate = $chequeInfo['cheque_date'] ?? date('Y-m-d');

                $chequeId = ChequeDepositEngine::recordCheque([
                    'cheque_number' => $chNum,
                    'party_id' => $invoice['customer_id'],
                    'bank_name' => $chBank,
                    'cheque_date' => $chDate,
                    'amount' => $invoice['total'],
                    'received_issued_date' => $invoice['invoice_date'],
                    'reference_number' => $invoice['invoice_number'],
                    'notes' => 'Generated automatically from Central Invoice ' . $invoice['invoice_number']
                ]);
            }

            // 6. Update Invoice Record status, journal_entry_id, cash/bank accounts, cheque references
            $db->prepare("
                UPDATE invoices 
                SET status = 'POSTED',
                    journal_entry_id = :je_id,
                    cash_account_id = :cash_id,
                    bank_account_id = :bank_id,
                    cheque_id = :cheque_id,
                    updated_at = NOW()
                WHERE id = :id
            ")->execute([
                'id' => $invoice['id'],
                'je_id' => $journalId,
                'cash_id' => $cashAccId,
                'bank_id' => $bankAccId,
                'cheque_id' => $chequeId
            ]);

            AuditService::log('post_central_invoice', 'finance', $invoice['id'], null, [
                'invoice_number' => $invoice['invoice_number'],
                'total' => $invoice['total'],
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
     * Cancel/Reverse a posted central invoice.
     */
    public static function cancelInvoice(int $id, string $reason): bool {
        $db = Database::getInstance();
        $invModel = new InvoiceModel();

        $invoice = $invModel->getById($id);
        if (!$invoice) {
            throw new Exception("Invoice not found.");
        }

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            if ($invoice['status'] === 'DRAFT') {
                $db->prepare("UPDATE invoices SET status = 'CANCELLED', reversal_reason = :reason, updated_at = NOW() WHERE id = :id")
                   ->execute(['id' => $invoice['id'], 'reason' => $reason]);
            } else {
                if ($invoice['status'] !== 'POSTED' || empty($invoice['journal_entry_id'])) {
                    throw new Exception("Only posted invoices can be cancelled.");
                }

                // 1. Reverse Stock movements for PRODUCT lines (restores stock)
                InventoryEngine::reverseStockMovement('SALES_INVOICE', (int)$invoice['id']);

                // 2. Reverse accounting journal entry
                $reversalJournalId = AccountingEngine::reverseJournalEntry(
                    (int)$invoice['journal_entry_id'],
                    "Reversal of Invoice " . $invoice['invoice_number'] . ": " . $reason
                );

                // 3. Revert cash or bank balances
                if ($invoice['payment_type'] === 'CASH' && $invoice['cash_account_id']) {
                    $db->prepare("UPDATE cash_accounts SET current_balance = current_balance - :amt WHERE id = :id")
                       ->execute(['amt' => (float)$invoice['total'], 'id' => (int)$invoice['cash_account_id']]);
                } elseif ($invoice['payment_type'] === 'BANK' && $invoice['bank_account_id']) {
                    $db->prepare("UPDATE bank_accounts SET current_balance = current_balance - :amt WHERE id = :id")
                       ->execute(['amt' => (float)$invoice['total'], 'id' => (int)$invoice['bank_account_id']]);
                }

                // 4. Cancel linked cheque if payment type was CHEQUE
                if ($invoice['payment_type'] === 'CHEQUE' && $invoice['cheque_id']) {
                    $db->prepare("UPDATE cheques SET status = 'CANCELLED', updated_at = NOW() WHERE id = :id")
                       ->execute(['id' => (int)$invoice['cheque_id']]);
                }

                // 5. Update invoice status
                $db->prepare("
                    UPDATE invoices 
                    SET status = 'CANCELLED', 
                        reversal_journal_entry_id = :rev_je_id,
                        reversal_reason = :reason,
                        updated_at = NOW()
                    WHERE id = :id
                ")->execute([
                    'id' => $invoice['id'],
                    'rev_je_id' => $reversalJournalId,
                    'reason' => $reason
                ]);
            }

            AuditService::log('cancel_central_invoice', 'finance', $invoice['id'], null, [
                'invoice_number' => $invoice['invoice_number'],
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
