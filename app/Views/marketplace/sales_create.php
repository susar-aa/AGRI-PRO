<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('modules/marketplace/sales'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Logs
        </a>
        <h4 class="fw-bold mb-1 text-dark">Compose Marketplace Sale Invoice</h4>
        <p class="text-muted small mb-0">Record a new sales invoice. Toggles between Cash and Credit sales, with live stock verification.</p>
    </div>
</div>

<form action="<?= \Core\Helper::baseUrl('modules/marketplace/sales/store'); ?>" method="POST" id="salesForm">
    <?= \Core\CSRF::getFormField(); ?>

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-12 col-lg-9">
            <!-- Invoice Details -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="customer_id" class="form-label fw-semibold">Select Customer <span class="text-danger">*</span></label>
                            <select class="form-select" id="customer_id" name="customer_id" required>
                                <option value="">-- Select Customer --</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['party_code']); ?> - <?= htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="warehouse_id" class="form-label fw-semibold">Dispatch Warehouse <span class="text-danger">*</span></label>
                            <select class="form-select" id="warehouse_id" name="warehouse_id" required onchange="updateProductStockLabels()">
                                <option value="">-- Select Warehouse --</option>
                                <?php foreach ($warehouses as $wh): ?>
                                    <option value="<?= $wh['id']; ?>" <?= ($wh['code'] === 'LOC-MAIN') ? 'selected' : ''; ?>><?= htmlspecialchars($wh['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="sale_date" class="form-label fw-semibold">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items Table -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-ol text-success me-2"></i> Invoice Line Items</h6>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="addRow()"><i class="bi bi-plus-lg me-1"></i> Add Line</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Product <span class="text-danger">*</span></th>
                                    <th style="width: 100px;">Available</th>
                                    <th style="width: 100px;">Quantity <span class="text-danger">*</span></th>
                                    <th style="width: 130px;">Unit Price (LKR) <span class="text-danger">*</span></th>
                                    <th style="width: 110px;">Discount (LKR)</th>
                                    <th style="width: 130px;" class="text-end">Total Amount</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Row template inserts here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3">
                    <label for="notes" class="form-label fw-semibold">Invoice Notes / Description</label>
                    <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="e.g. Terms, transport notes, etc."></textarea>
                </div>
            </div>
        </div>

        <!-- Sidebar Payment & Actions Column -->
        <div class="col-12 col-lg-3">
            <!-- Payment Block -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wallet2 text-success me-2"></i> Payment Method</h6>
                </div>
                <div class="card-body p-3 pt-0 small">
                    <div class="mb-3">
                        <label for="sale_type" class="form-label fw-semibold">Sale Type <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="sale_type" name="sale_type" required onchange="toggleSaleType()">
                            <option value="CASH">Cash Drawer Sale</option>
                            <option value="CREDIT">On Credit Sale</option>
                        </select>
                    </div>

                    <div id="cashPaymentMethodBlock">
                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-semibold">Settlement Mode <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="payment_method" name="payment_method" onchange="togglePaymentFields()">
                                <option value="CASH">Cash Drawer</option>
                                <option value="BANK">Bank Deposit</option>
                                <option value="CHEQUE">Received Cheque</option>
                            </select>
                        </div>

                        <!-- Cash Drawer selection -->
                        <div class="mb-3" id="cashDrawerSection">
                            <label for="cash_account_id" class="form-label fw-semibold">Cash Drawer <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="cash_account_id" name="cash_account_id">
                                <?php foreach ($cashAccounts as $ca): ?>
                                    <option value="<?= $ca['id']; ?>"><?= htmlspecialchars($ca['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Bank Account selection -->
                        <div class="mb-3" id="bankAccountSection" style="display: none;">
                            <label for="bank_account_id" class="form-label fw-semibold">Bank Account <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="bank_account_id" name="bank_account_id">
                                <?php foreach ($bankAccounts as $ba): ?>
                                    <option value="<?= $ba['id']; ?>"><?= htmlspecialchars($ba['bank_name']); ?> - <?= htmlspecialchars($ba['account_number']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Cheque Details panel -->
                        <div class="card bg-light border p-2 mb-3 rounded-3" id="chequeDetailsSection" style="display: none;">
                            <h6 class="fw-bold mb-2 small">Cheque Specifications</h6>
                            <div class="mb-2">
                                <label for="cheque_number" class="form-label fw-semibold small mb-1">Cheque Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="cheque_number" name="cheque_number" placeholder="e.g. 012356">
                            </div>
                            <div class="mb-2">
                                <label for="cheque_bank" class="form-label fw-semibold small mb-1">Bank Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="cheque_bank" name="cheque_bank" placeholder="e.g. Sampath Bank">
                            </div>
                            <div class="mb-2">
                                <label for="cheque_date" class="form-label fw-semibold small mb-1">Cheque Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-sm" id="cheque_date" name="cheque_date" value="<?= date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summaries and Invoice Actions -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3 small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-semibold text-dark font-monospace" id="summarySubtotal">LKR 0.00</span>
                    </div>
                    <div class="mb-3">
                        <label for="discount" class="form-label fw-semibold text-muted">Overall Discount (LKR):</label>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end font-monospace" id="discount" name="discount" value="0.00" oninput="calculateInvoiceTotal()">
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold text-dark fs-6">Invoice Total:</span>
                        <span class="fw-bold text-success fs-5 font-monospace" id="summaryTotal">LKR 0.00</span>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="action" value="post" class="btn btn-success rounded-pill" style="background-color: #1b4332; border-color: #1b4332;" onclick="return validateStockBeforeSubmit(event)">Post & Dispatch</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Pass products configuration array to JS -->
<script>
const marketplaceProducts = <?= json_encode($products); ?>;
let rowCount = 0;

function addRow() {
    rowCount++;
    const tbody = document.querySelector('#itemsTable tbody');
    const tr = document.createElement('tr');
    tr.id = `row_${rowCount}`;

    let productOptions = '<option value="">-- Select Product --</option>';
    marketplaceProducts.forEach(p => {
        productOptions += `<option value="${p.id}" data-price="${p.default_selling_price}" data-unit="${p.unit_code}">${htmlspecialchars(p.name_en)} (${p.sku})</option>`;
    });

    tr.innerHTML = `
        <td>
            <select class="form-select form-select-sm product-selector" name="items[${rowCount}][product_id]" required onchange="handleProductSelect(${rowCount})">
                ${productOptions}
            </select>
        </td>
        <td class="font-monospace fw-semibold text-muted text-center" id="available_${rowCount}">0.00</td>
        <td>
            <input type="number" step="0.01" min="0.01" class="form-control form-control-sm font-monospace qty-input" name="items[${rowCount}][quantity]" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
        </td>
        <td>
            <input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace price-input" name="items[${rowCount}][unit_price]" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
        </td>
        <td>
            <input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace disc-input" name="items[${rowCount}][discount]" value="0.00" oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
        </td>
        <td class="text-end fw-bold font-monospace text-dark row-total" id="rowtotal_${rowCount}">0.00</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="removeRow(${rowCount})"><i class="bi bi-trash"></i></button>
        </td>
    `;
    tbody.appendChild(tr);
    updateProductStockLabels();
}

function removeRow(id) {
    const row = document.getElementById(`row_${id}`);
    if (row) {
        row.remove();
        calculateInvoiceTotal();
    }
}

function handleProductSelect(id) {
    const row = document.getElementById(`row_${id}`);
    const selector = row.querySelector('.product-selector');
    const selectedOption = selector.options[selector.selectedIndex];
    const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
    
    row.querySelector('.price-input').value = price.toFixed(2);
    row.querySelector('.qty-input').value = '1.00';
    row.querySelector('.disc-input').value = '0.00';

    updateProductStockLabels();
    calculateRowTotal(id);
    calculateInvoiceTotal();
}

function updateProductStockLabels() {
    const whId = document.getElementById('warehouse_id').value;
    const selectors = document.querySelectorAll('.product-selector');

    selectors.forEach(sel => {
        const option = sel.options[sel.selectedIndex];
        const prodId = sel.value;
        const rowId = sel.name.match(/\[(\d+)\]/)[1];
        const stockLabel = document.getElementById(`available_${rowId}`);

        if (!prodId || !whId) {
            stockLabel.textContent = '0.00';
            return;
        }

        const product = marketplaceProducts.find(p => p.id == prodId);
        if (product && product.stocks && product.stocks[whId] !== undefined) {
            stockLabel.textContent = parseFloat(product.stocks[whId]).toFixed(2);
        } else {
            stockLabel.textContent = '0.00';
        }
    });
}

function calculateRowTotal(id) {
    const row = document.getElementById(`row_${id}`);
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const disc = parseFloat(row.querySelector('.disc-input').value) || 0;

    const total = Math.max(0, (qty * price) - disc);
    document.getElementById(`rowtotal_${id}`).textContent = total.toFixed(2);
}

function calculateInvoiceTotal() {
    let subtotal = 0.00;
    const rowTotals = document.querySelectorAll('.row-total');
    
    rowTotals.forEach(el => {
        subtotal += parseFloat(el.textContent) || 0;
    });

    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const total = Math.max(0, subtotal - discount);

    document.getElementById('summarySubtotal').textContent = 'LKR ' + subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summaryTotal').textContent = 'LKR ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function toggleSaleType() {
    const saleType = document.getElementById('sale_type').value;
    const block = document.getElementById('cashPaymentMethodBlock');

    if (saleType === 'CREDIT') {
        block.style.display = 'none';
        document.getElementById('payment_method').required = false;
        document.getElementById('cash_account_id').required = false;
        document.getElementById('bank_account_id').required = false;
        document.getElementById('cheque_number').required = false;
        document.getElementById('cheque_bank').required = false;
    } else {
        block.style.display = 'block';
        togglePaymentFields();
    }
}

function togglePaymentFields() {
    const method = document.getElementById('payment_method').value;
    const cashSect = document.getElementById('cashDrawerSection');
    const bankSect = document.getElementById('bankAccountSection');
    const chequeSect = document.getElementById('chequeDetailsSection');

    document.getElementById('cash_account_id').required = false;
    document.getElementById('bank_account_id').required = false;
    document.getElementById('cheque_number').required = false;
    document.getElementById('cheque_bank').required = false;

    cashSect.style.display = 'none';
    bankSect.style.display = 'none';
    chequeSect.style.display = 'none';

    if (method === 'CASH') {
        cashSect.style.display = 'block';
        document.getElementById('cash_account_id').required = true;
    } else if (method === 'BANK') {
        bankSect.style.display = 'block';
        document.getElementById('bank_account_id').required = true;
    } else if (method === 'CHEQUE') {
        chequeSect.style.display = 'block';
        document.getElementById('cheque_number').required = true;
        document.getElementById('cheque_bank').required = true;
    }
}

function validateStockBeforeSubmit(event) {
    const rows = document.querySelectorAll('#itemsTable tbody tr');
    let valid = true;

    rows.forEach(row => {
        const prodName = row.querySelector('.product-selector option:checked').text;
        const available = parseFloat(row.querySelector('[id^="available_"]').textContent) || 0;
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;

        if (qty > available) {
            alert(`Stock allocation error: Quantity entered for ${prodName} (${qty}) exceeds stock available on hand (${available}).`);
            valid = false;
        }
    });

    if (!valid) {
        event.preventDefault();
        return false;
    }
    return true;
}

function htmlspecialchars(str) {
    if (typeof str !== 'string') return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

document.addEventListener('DOMContentLoaded', () => {
    // Add default first row
    addRow();
    toggleSaleType();
});
</script>
