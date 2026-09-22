<?php
$file = 'c:\xampp\htdocs\AGRI PRO\app\Views\invoices\edit.php';
$content = file_get_contents($file);

// Replace prefilled reference with invoice reference
$content = str_replace(
    'value="<?= htmlspecialchars($prefilled[\'reference\'] ?? \'\'); ?>" placeholder="e.g. PO-1234"',
    'value="<?= htmlspecialchars($invoice[\'reference\'] ?? \'\'); ?>" placeholder="e.g. PO-1234"',
    $content
);

// Replace default dates
$content = str_replace(
    'value="<?= date(\'Y-m-d\'); ?>" required',
    'value="<?= htmlspecialchars($invoice[\'invoice_date\'] ?? date(\'Y-m-d\')); ?>" required',
    $content
);

$content = preg_replace(
    '/name="due_date" value="<\?= date\(\'Y-m-d\'\); \?>"/',
    'name="due_date" value="<?= htmlspecialchars($invoice[\'due_date\'] ?? date(\'Y-m-d\')); ?>" ',
    $content
);

// Update customer dropdown to select existing
$content = str_replace(
    '<?php foreach ($members as $m): ?>',
    '<?php foreach ($members as $m): ?>
    <?php $mval = "M_" . $m[\'id\']; $sel = ($invoice[\'customer_id\'] == $m[\'party_id\']) ? \'selected\' : \'\'; ?>
    <option value="<?= $mval ?>" <?= $sel ?>>',
    $content
);
$content = str_replace(
    '<option value="M_<?= $m[\'id\']; ?>">',
    '',
    $content
);

$content = str_replace(
    '<?php foreach ($customers as $c): ?>',
    '<?php foreach ($customers as $c): ?>
    <?php $cval = $c[\'id\']; $sel = ($invoice[\'customer_id\'] == $c[\'id\']) ? \'selected\' : \'\'; ?>
    <option value="<?= $cval ?>" <?= $sel ?>>',
    $content
);
$content = str_replace(
    '<option value="<?= $c[\'id\']; ?>" <?= ($prefilled[\'customer_id\'] == $c[\'id\']) ? \'selected\' : \'\'; ?>>',
    '',
    $content
);

$content = str_replace(
    '<?php foreach ($staff as $s): ?>',
    '<?php foreach ($staff as $s): ?>
    <?php $sval = "U_" . $s[\'id\']; $sel = ($invoice[\'customer_id\'] == $s[\'party_id\']) ? \'selected\' : \'\'; ?>
    <option value="<?= $sval ?>" <?= $sel ?>>',
    $content
);
$content = str_replace(
    '<option value="U_<?= $s[\'id\']; ?>">',
    '',
    $content
);

// Discount & Notes
$content = str_replace(
    'name="discount" value="0.00"',
    'name="discount" value="<?= number_format($invoice[\'discount\'] ?? 0, 2, \'.\', \'\'); ?>"',
    $content
);
$content = str_replace(
    'name="notes" rows="2" placeholder="Enter any additional notes or terms here..."></textarea>',
    'name="notes" rows="2" placeholder="Enter any additional notes or terms here..."><?= htmlspecialchars($invoice[\'notes\'] ?? \'\'); ?></textarea>',
    $content
);

// Change Payment Type Tab Active states based on invoice payment type
// The tabs are controlled by javascript later, but let's pre-select the radio buttons (which we don't have, they are just tabs that set a hidden input maybe? No, the tabs are labels for radio buttons.
$content = preg_replace(
    '/<input type="radio" class="btn-check" name="payment_type" id="pay_cash" value="CASH" autocomplete="off" checked>/',
    '<input type="radio" class="btn-check" name="payment_type" id="pay_cash" value="CASH" autocomplete="off" <?= ($invoice[\'payment_type\'] === \'CASH\') ? \'checked\' : \'\' ?>>',
    $content
);
$content = preg_replace(
    '/<input type="radio" class="btn-check" name="payment_type" id="pay_bank" value="BANK" autocomplete="off">/',
    '<input type="radio" class="btn-check" name="payment_type" id="pay_bank" value="BANK" autocomplete="off" <?= ($invoice[\'payment_type\'] === \'BANK\') ? \'checked\' : \'\' ?>>',
    $content
);
$content = preg_replace(
    '/<input type="radio" class="btn-check" name="payment_type" id="pay_cheque" value="CHEQUE" autocomplete="off">/',
    '<input type="radio" class="btn-check" name="payment_type" id="pay_cheque" value="CHEQUE" autocomplete="off" <?= ($invoice[\'payment_type\'] === \'CHEQUE\') ? \'checked\' : \'\' ?>>',
    $content
);
$content = preg_replace(
    '/<input type="radio" class="btn-check" name="payment_type" id="pay_credit" value="CREDIT" autocomplete="off">/',
    '<input type="radio" class="btn-check" name="payment_type" id="pay_credit" value="CREDIT" autocomplete="off" <?= ($invoice[\'payment_type\'] === \'CREDIT\') ? \'checked\' : \'\' ?>>',
    $content
);


// Add JS at bottom to load existing items
$jsInject = <<<JS
<script>
document.addEventListener('DOMContentLoaded', () => {
    const editItems = <?= json_encode(\$editItems ?? []); ?>;
    
    // Disable form submission initially while loading items
    const form = document.getElementById('invoiceForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;

    // Small delay to let JS functions load
    setTimeout(() => {
        editItems.forEach(item => {
            if (item.type === 'PRODUCT') {
                const prod = availableProducts.find(p => p.id == item.product_id);
                if (prod) {
                    addProductRow(prod);
                    const rows = document.querySelectorAll('#invoiceItemsTable tbody tr');
                    const lastRow = rows[rows.length - 1];
                    lastRow.querySelector('.qty-input').value = item.qty;
                    lastRow.querySelector('.price-input').value = item.price;
                }
            } else if (item.type === 'SERVICE') {
                addServiceRow({
                    id: item.service_id,
                    service_name: item.service_name,
                    unit: item.unit,
                    default_price: item.price,
                    description: item.description
                });
                const rows = document.querySelectorAll('#invoiceItemsTable tbody tr');
                const lastRow = rows[rows.length - 1];
                lastRow.querySelector('.qty-input').value = item.qty;
                lastRow.querySelector('.price-input').value = item.price;
                if(item.description) {
                   lastRow.querySelector('input[name="item_description[]"]').value = item.description;
                }
            } else if (item.type === 'RENTAL') {
                 // For rental, we might just add a basic row since we only have some details
                 // Or we can construct a fake machine object
                 addMachineRowFromDirectory({
                    id: item.machinery_rental_id,
                    machinery_code: 'RENTAL',
                    machinery_name: item.description || 'Machinery Rental',
                    rental_unit: item.unit || 'Qty',
                    default_rental_rate: item.price
                 }, {
                    parentElement: {
                        querySelector: function(selector) {
                            if(selector === '.modal-machine-qty-input') return {value: item.qty};
                            if(selector === '.modal-machine-price-input') return {value: item.price};
                            return null;
                        }
                    }
                 });
            } else if (item.type === 'MEMBER_FEE' || item.type === 'SHARE_CAPITAL') {
                 addOtherFeeRow(item.type, item.price);
                 const rows = document.querySelectorAll('#invoiceItemsTable tbody tr');
                 const lastRow = rows[rows.length - 1];
                 lastRow.querySelector('.qty-input').value = item.qty;
                 lastRow.querySelector('.price-input').value = item.price;
            }
        });
        
        calcTotals();
        submitBtn.disabled = false;
        
        // Trigger customer change to set available payment methods
        handleCustomerChange();
        togglePaymentFields();
    }, 500);
});
</script>
JS;

$content = str_replace('</body>', $jsInject . "\n</body>", $content);

file_put_contents($file, $content);
echo "Edit view updated.\n";
