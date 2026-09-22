<?php
$file = 'c:\xampp\htdocs\AGRI PRO\app\Controllers\InvoiceController.php';
$content = file_get_contents($file);

// 1. Update edit() condition
$content = str_replace(
    "if (!\$invoice || \$invoice['status'] !== 'DRAFT') {",
    "if (!\$invoice || (\$invoice['status'] !== 'DRAFT' && \$invoice['status'] !== 'POSTED')) {",
    $content
);
$content = str_replace(
    "Session::setFlash('error', 'Invoice cannot be edited (Not found or already posted).');",
    "Session::setFlash('error', 'Invoice cannot be edited (Not found or cancelled).');",
    $content
);

// 2. Update update() condition
$content = str_replace(
    "if (!\$invoice || \$invoice['status'] !== 'DRAFT') {",
    "if (!\$invoice || (\$invoice['status'] !== 'DRAFT' && \$invoice['status'] !== 'POSTED')) {",
    $content
);
$content = str_replace(
    "Session::setFlash('error', 'Cannot update this invoice. It may have already been posted.');",
    "Session::setFlash('error', 'Cannot update this invoice. It may be cancelled.');",
    $content
);

// 3. Inject ledger reversal before items deletion in update()
$reversalLogic = <<<PHP
            // If POSTED, we must reverse the old ledger and balances before updating items
            if (\$invoice['status'] === 'POSTED') {
                if (\$invoice['payment_type'] === 'CASH' && \$invoice['cash_account_id']) {
                    \$db->exec("UPDATE cash_accounts SET current_balance = current_balance - {\$invoice['total']} WHERE id = {\$invoice['cash_account_id']}");
                } elseif (\$invoice['payment_type'] === 'BANK' && \$invoice['bank_account_id']) {
                    \$db->exec("UPDATE bank_accounts SET current_balance = current_balance - {\$invoice['total']} WHERE id = {\$invoice['bank_account_id']}");
                }
                
                \$journal = \$db->query("SELECT id FROM journal_entries WHERE source_module = 'invoices' AND source_transaction_id = {\$id}")->fetch();
                if (\$journal) {
                    \$db->exec("DELETE FROM journal_lines WHERE journal_entry_id = {\$journal['id']}");
                    \$db->exec("DELETE FROM journal_entries WHERE id = {\$journal['id']}");
                }
                
                \$db->exec("DELETE FROM stock_ledger WHERE source_module = 'SALES_INVOICE' AND source_transaction_id = {\$id}");
                
                if (\$invoice['cheque_id']) {
                    \$db->exec("DELETE FROM cheques WHERE id = {\$invoice['cheque_id']}");
                }

                // Temporarily set to DRAFT so postInvoice() can run later
                \$db->exec("UPDATE invoices SET status = 'DRAFT' WHERE id = {\$id}");
            }

            // 1. Delete existing items
PHP;

$content = str_replace("// 1. Delete existing items", $reversalLogic, $content);


// 4. Inject re-posting logic at the end of update()
$repostLogic = <<<PHP
            // Save cheque details to session if CHEQUE payment type
            if (\$paymentType === 'CHEQUE') {
                \$_SESSION['temp_cheque_' . \$id] = [
                    'cheque_number' => \$chequeNo,
                    'bank_name' => \$chequeBank,
                    'cheque_date' => \$chequeDate
                ];
            } else {
                unset(\$_SESSION['temp_cheque_' . \$id]);
            }

            \$db->commit();
            
            if (\$invoice['status'] === 'POSTED') {
                \$chequeInfo = [
                    'cheque_number' => \$chequeNo,
                    'bank_name' => \$chequeBank,
                    'cheque_date' => \$chequeDate
                ];
                \App\Services\InvoiceEngine::postInvoice(\$id, \$chequeInfo);
                Session::setFlash('success', 'Invoice updated and re-posted successfully.');
            } else {
                Session::setFlash('success', 'Invoice updated successfully.');
            }
            
            Helper::redirect('modules/invoices/view?id=' . \$id);
PHP;

$content = preg_replace('/\/\/ Save cheque details to session if CHEQUE payment type.*?Helper::redirect\(\'modules\/invoices\/view\?id=\' \. \$id\);/s', $repostLogic, $content);

file_put_contents($file, $content);
echo "InvoiceController updated for POSTED edits.\n";
