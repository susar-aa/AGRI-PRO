<?php if ($flashError = \Core\Session::getFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('grn'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to GRNs
        </a>
        <h4 class="fw-bold mb-1 text-dark">New Goods Receipt Note (GRN)</h4>
        <p class="text-muted small mb-0">Receive multiple items at once into a specific warehouse.</p>
    </div>
</div>

<form action="<?= \Core\Helper::baseUrl('grn/store'); ?>" method="POST" id="grnForm">
    <?= \Core\CSRF::getFormField(); ?>

    <!-- HEADER INFO -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-square me-2"></i> Document Header</h6>
            <div class="row g-4">
                <div class="col-md-4">
                    <label for="location_id" class="form-label fw-semibold">Receiving Warehouse <span class="text-danger">*</span></label>
                    <select class="form-select" id="location_id" name="location_id" required>
                        <option value="">-- Select Location --</option>
                        <?php foreach ($warehouses as $wh): ?>
                            <option value="<?= $wh['id']; ?>"><?= htmlspecialchars($wh['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="supplier_id" class="form-label fw-semibold">Supplier (Optional)</label>
                    <select class="form-select" id="supplier_id" name="supplier_id">
                        <option value="">-- No Supplier --</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= $sup['id']; ?>"><?= htmlspecialchars($sup['party_code']); ?> - <?= htmlspecialchars($sup['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="reference_number" class="form-label fw-semibold">GRN / Invoice Reference <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="reference_number" name="reference_number" placeholder="e.g. INV-2023-001" required>
                </div>
            </div>
        </div>
    </div>

    <!-- LINE ITEMS -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-list-check me-2"></i> Received Items</h6>
            
            <div class="table-responsive">
                <table class="table align-middle" id="itemsTable">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th style="min-width: 300px;">Product <span class="text-danger">*</span></th>
                            <th style="width: 150px;">Unit Cost (LKR) <span class="text-danger">*</span></th>
                            <th style="width: 150px;">Qty Received <span class="text-danger">*</span></th>
                            <th style="width: 150px;">Total (LKR)</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows dynamically added via JS -->
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn btn-sm btn-outline-success rounded-pill mt-2 px-3" onclick="addRow()">
                <i class="bi bi-plus-lg me-1"></i> Add Product
            </button>
        </div>
    </div>

    <!-- SUBMIT -->
    <div class="d-flex justify-content-end gap-2 mb-5">
        <a href="<?= \Core\Helper::baseUrl('grn'); ?>" class="btn btn-light rounded-pill px-4 border">Cancel</a>
        <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
            <i class="bi bi-box-arrow-in-down me-1"></i> Save GRN
        </button>
    </div>

</form>

<!-- HIDDEN PRODUCT TEMPLATE DATA -->
<select id="productTemplate" style="display:none;">
    <option value="">-- Choose a Product --</option>
    <?php foreach ($products as $p): ?>
        <option value="<?= $p['id']; ?>" data-cost="<?= $p['default_purchase_price']; ?>">
            <?= htmlspecialchars($p['product_code']); ?> - <?= htmlspecialchars($p['name_en']); ?>
        </option>
    <?php endforeach; ?>
</select>

<script>
function addRow() {
    const tbody = document.querySelector('#itemsTable tbody');
    const tr = document.createElement('tr');
    
    // Copy options from hidden template
    const optionsHTML = document.getElementById('productTemplate').innerHTML;
    
    tr.innerHTML = `
        <td>
            <select class="form-select product-select" name="products[]" required onchange="handleProductChange(this)">
                ${optionsHTML}
            </select>
        </td>
        <td>
            <input type="number" step="0.01" min="0" class="form-control text-success fw-bold unit-cost" name="unit_costs[]" required oninput="calculateRow(this)">
        </td>
        <td>
            <input type="number" step="0.01" min="0.01" class="form-control fw-bold qty" name="quantities[]" required oninput="calculateRow(this)">
        </td>
        <td>
            <input type="text" class="form-control-plaintext text-end row-total fw-bold" readonly value="0.00">
        </td>
        <td class="text-end">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" title="Remove item">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
}

function removeRow(btn) {
    const tbody = document.querySelector('#itemsTable tbody');
    if (tbody.children.length > 1) {
        btn.closest('tr').remove();
    } else {
        alert('You must have at least one product line.');
    }
}

function handleProductChange(select) {
    const selectedOption = select.options[select.selectedIndex];
    const row = select.closest('tr');
    const costInput = row.querySelector('.unit-cost');
    
    if (selectedOption && selectedOption.value !== "") {
        costInput.value = selectedOption.getAttribute('data-cost');
    } else {
        costInput.value = '';
    }
    calculateRow(select);
}

function calculateRow(element) {
    const row = element.closest('tr');
    const cost = parseFloat(row.querySelector('.unit-cost').value) || 0;
    const qty = parseFloat(row.querySelector('.qty').value) || 0;
    const total = cost * qty;
    row.querySelector('.row-total').value = total.toFixed(2);
}

document.addEventListener('DOMContentLoaded', () => {
    // Add default first row
    addRow();

    // Prevent submission without rows
    document.getElementById('grnForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('#itemsTable tbody tr');
        if (rows.length === 0) {
            e.preventDefault();
            alert('Please add at least one product.');
        }
    });
});
</script>
