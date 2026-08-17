<?php if ($flashSuccess = \Core\Session::getFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($flashError = \Core\Session::getFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Marketplace Products Configuration</h4>
        <p class="text-muted small mb-0">Select products from the general master list, mark them active for marketplace retail, and specify source origins.</p>
    </div>
    <div>
        <button type="button" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </button>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('modules/marketplace/products'); ?>" method="GET" class="row g-3 small">
            <div class="col-md-9">
                <input type="text" class="form-control form-control-sm" name="search" value="<?= htmlspecialchars($filters['search']); ?>" placeholder="Search by SKU, Product code, or Name...">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 w-100" style="background-color: #1b4332; border-color: #1b4332;">Search</button>
                <a href="<?= \Core\Helper::baseUrl('modules/marketplace/products'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Available Stock</th>
                        <th>Unit</th>
                        <th class="text-end">Cost Price</th>
                        <th class="text-end">Selling Price</th>

                        <th class="text-center">Marketplace Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($p['name_en']); ?></div>
                                </td>
                                <td class="font-monospace text-secondary fw-semibold"><?= htmlspecialchars($p['sku']); ?></td>
                                <td>
                                    <?php
                                    $stockBreakdown = [];
                                    $totalStock = 0.00;
                                    foreach ($p['stock'] as $whName => $qty) {
                                        $totalStock += $qty;
                                        if ($qty > 0) {
                                            $stockBreakdown[] = htmlspecialchars($whName) . ": " . number_format($qty, 2);
                                        }
                                    }
                                    ?>
                                    <strong class="text-dark"><?= number_format($totalStock, 2); ?></strong>
                                    <?php if (!empty($stockBreakdown)): ?>
                                        <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= implode(', ', $stockBreakdown); ?>"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($p['unit_code']); ?></td>
                                <td class="text-end font-monospace">LKR <?= number_format($p['default_purchase_price'], 2); ?></td>
                                <td class="text-end font-monospace fw-bold text-success">LKR <?= number_format($p['default_selling_price'], 2); ?></td>

                                <td class="text-center">
                                    <form action="<?= \Core\Helper::baseUrl('modules/marketplace/products/toggle'); ?>" method="POST" class="d-inline">
                                        <?= \Core\CSRF::getFormField(); ?>
                                        <input type="hidden" name="id" value="<?= $p['id']; ?>">
                                        <?php if ($p['is_marketplace']): ?>
                                            <input type="hidden" name="status" value="0">
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-0.5">Active</button>
                                        <?php else: ?>
                                            <input type="hidden" name="status" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-0.5">Inactive</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editPricesModal" 
                                            data-id="<?= $p['id']; ?>" 
                                            data-name="<?= htmlspecialchars($p['name_en']); ?>"
                                            data-cost="<?= $p['default_purchase_price']; ?>" 
                                            data-sell="<?= $p['default_selling_price']; ?>">
                                        <i class="bi bi-pencil-square"></i> Configure
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Configure prices & source -->
<div class="modal fade" id="editPricesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #1b4332;">
                <h5 class="modal-title fw-bold"><i class="bi bi-gear-wide-connected me-2"></i> Configure Marketplace Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/marketplace/products/update'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" id="editProdId" value="">
                <div class="modal-body p-4">
                    <p class="mb-3 text-muted">Configure costings and source properties for: <strong id="editProdName" class="text-dark"></strong></p>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="default_purchase_price" class="form-label fw-semibold small">Default Cost Price (LKR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="default_purchase_price" name="default_purchase_price" required>
                        </div>
                        <div class="col-6">
                            <label for="default_selling_price" class="form-label fw-semibold small">Default Selling Price (LKR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="default_selling_price" name="default_selling_price" required>
                        </div>
                    </div>


                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add New Product -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #1b4332;">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Add New Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/marketplace/products/store'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label for="product_code" class="form-label fw-semibold small">Product Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="product_code" name="product_code" placeholder="e.g. PROD-FERT-002" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name_en" class="form-label fw-semibold small">Product Name (EN) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="name_en" name="name_en" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label for="base_unit_id" class="form-label fw-semibold small">Unit <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="base_unit_id" name="base_unit_id" required>
                                <option value="">-- Select Unit --</option>
                                <?php foreach ($units as $u): ?>
                                    <option value="<?= $u['id']; ?>"><?= htmlspecialchars($u['code']); ?> - <?= htmlspecialchars($u['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="default_purchase_price" class="form-label fw-semibold small">Default Cost (LKR)</label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="default_purchase_price" name="default_purchase_price" value="0.00">
                        </div>
                        <div class="col-6">
                            <label for="default_selling_price" class="form-label fw-semibold small">Default Selling Price (LKR)</label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="default_selling_price" name="default_selling_price" value="0.00">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="product_type" class="form-label fw-semibold small">Product Type</label>
                            <select class="form-select form-select-sm" id="product_type" name="product_type">
                                <option value="TRADING">Trading Product</option>
                                <option value="RAW_MATERIAL">Raw Material</option>
                                <option value="WIP">Work-in-Progress</option>
                                <option value="FINISHED_GOODS">Finished Goods</option>
                                <option value="AGRICULTURAL_PRODUCT">Agricultural Product</option>
                                <option value="MANUFACTURED_PRODUCT">Manufactured Product</option>
                                <option value="PACKAGING">Packaging</option>
                                <option value="CONSUMABLE">Consumable</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="source_module_new" class="form-label fw-semibold small">Source Module</label>
                            <select class="form-select form-select-sm" id="source_module_new" name="source_module">
                                <option value="PURCHASE">Purchase</option>
                                <option value="PLANTATION">Plantation</option>
                                <option value="BRICK_PRODUCTION">Brick Production</option>
                                <option value="FRUIT_PACKING">Fruit Packing</option>
                                <option value="GRINDING_MILL">Grinding Mill</option>
                                <option value="OTHER">Other</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tooltips activation
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const editPricesModal = document.getElementById('editPricesModal');
    if (editPricesModal) {
        editPricesModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('editProdId').value = button.getAttribute('data-id');
            document.getElementById('editProdName').textContent = button.getAttribute('data-name');
            document.getElementById('default_purchase_price').value = button.getAttribute('data-cost');
            document.getElementById('default_selling_price').value = button.getAttribute('data-sell');
        });
    }
});
</script>
