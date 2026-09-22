<?php
$file = 'c:\xampp\htdocs\AGRI PRO\app\Controllers\InvoiceController.php';
$content = file_get_contents($file);

// 1. Remove tax_amount from invoice_items insert
$content = str_replace(
    'quantity, unit_price, total, tax_amount, service_job_id, machinery_rental_id)',
    'quantity, unit_price, total)',
    $content
);
$content = str_replace(
    ':tot, 0.00, :job_id, :rental_id)',
    ':tot)',
    $content
);

// 2. Remove tax_amount from invoices update
$content = str_replace(
    "discount = :discount, \n                    tax_amount = :tax_amount, \n                    total = :total,",
    "discount = :discount, \n                    total = :total,",
    $content
);
$content = str_replace(
    "'tax_amount' => \$taxAmount,\n                'total' => \$netTotal,",
    "'total' => \$netTotal,",
    $content
);

// 3. Update recordCancelled to auto-generate the next invoice number
// In recordCancelled(), we get invoice_number from $_POST['invoice_number'].
// But wait, $invoiceNumber is generated in InvoiceEngine if we don't supply it? No, InvoiceEngine has generateInvoiceNumber().
// But InvoiceController has access to the InvoiceModel or we can just query it.
$recordCancelReplacement = <<<PHP
    public function recordCancelled(): void {
        Auth::requirePermission('invoices.post');
        \$this->validateCsrf();

        // Auto-generate invoice number
        \$db = \Core\Database::getInstance();
        \$latest = \$db->query("SELECT invoice_number FROM invoices ORDER BY id DESC LIMIT 1")->fetchColumn();
        if (\$latest && preg_match('/INV-(\d+)/', \$latest, \$matches)) {
            \$nextNum = (int)\$matches[1] + 1;
            \$invoiceNumber = 'INV-' . str_pad((string)\$nextNum, 3, '0', STR_PAD_LEFT);
        } else {
            \$invoiceNumber = 'INV-001';
        }

        \$reason = trim(\$_POST['reason'] ?? 'Manual Bill Cancelled');
PHP;

$content = preg_replace(
    '/public function recordCancelled\(\): void \{[\s\S]*?\$reason = trim\(\$_POST\[\'reason\'\] \?\? \'\'\);/',
    $recordCancelReplacement,
    $content
);

// Remove reversal_reason from INSERT INTO since it's not in the db schema (Wait, is it in the schema? Let's check.)
// The schema output for `invoices` does not have `reversal_reason`.
$content = str_replace(
    "subtotal, discount, tax_amount, total, created_by, reversal_reason",
    "subtotal, discount, total, created_by",
    $content
);
$content = str_replace(
    "0.00, 0.00, 0.00, 0.00, :created_by, :reversal_reason",
    "0.00, 0.00, 0.00, :created_by",
    $content
);
$content = str_replace(
    "'created_by' => Auth::id(),\n                'reversal_reason' => \$reason",
    "'created_by' => Auth::id()\n                // Reason goes to notes instead since reversal_reason doesn't exist",
    $content
);

// Also we should put $reason into notes since reversal_reason column doesn't exist.
$content = str_replace(
    "invoice_date, status, payment_type, \n                    subtotal, discount, total, created_by",
    "invoice_date, status, payment_type, notes, \n                    subtotal, discount, total, created_by",
    $content
);
$content = str_replace(
    ":invoice_number, :customer_id, :invoice_date, 'CANCELLED', 'CASH',\n                    0.00, 0.00, 0.00, :created_by",
    ":invoice_number, :customer_id, :invoice_date, 'CANCELLED', 'CASH', :notes,\n                    0.00, 0.00, 0.00, :created_by",
    $content
);
$content = str_replace(
    "'created_by' => Auth::id()\n                // Reason goes to notes instead since reversal_reason doesn't exist",
    "'created_by' => Auth::id(),\n                'notes' => \$reason",
    $content
);

file_put_contents($file, $content);
echo "InvoiceController tax_amount and recordCancelled updated.\n";
