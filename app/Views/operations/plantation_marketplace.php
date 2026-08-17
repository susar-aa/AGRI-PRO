<?php
// Variables available: $project, $harvests, $transfers, $stats
?>

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

<div class="mb-3">
    <a href="<?= \Core\Helper::baseUrl('operations/plantation/view?id=' . $project['id']); ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Back to Project Dashboard
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Marketplace Transfers Log</h4>
        <p class="text-muted small mb-0">Project: <strong class="text-dark"><?= htmlspecialchars($project['project_name']); ?></strong> &bull; Location: <?= htmlspecialchars($project['location']); ?></p>
    </div>
    <div>
        <?php 
        $hasEligibleHarvests = false;
        foreach ($harvests as $h) {
            if ($h['remaining_quantity'] > 0) {
                $hasEligibleHarvests = true;
                break;
            }
        }
        ?>
        <?php if ($hasEligibleHarvests): ?>
            <button class="btn btn-success rounded-pill px-4 shadow-sm" style="background-color: #1b4332; border-color: #1b4332;" data-bs-toggle="modal" data-bs-target="#transferHarvestModal">
                <i class="bi bi-plus-lg me-1"></i> Transfer Harvest to Marketplace
            </button>
        <?php else: ?>
            <button class="btn btn-secondary rounded-pill px-4 shadow-sm" disabled title="No remaining harvest quantity available to transfer.">
                <i class="bi bi-plus-lg me-1"></i> Transfer Harvest to Marketplace
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 bg-secondary-subtle text-secondary me-3">
                    <i class="bi bi-gift fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Total Harvested</h6>
                    <h4 class="fw-bold mb-0 text-dark font-monospace"><?= number_format($stats['total_harvested'], 2); ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 bg-success-subtle text-success me-3">
                    <i class="bi bi-shop fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Transferred to Marketplace</h6>
                    <h4 class="fw-bold mb-0 text-success font-monospace"><?= number_format($stats['total_transferred'], 2); ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 bg-warning-subtle text-warning me-3">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Remaining Harvest</h6>
                    <h4 class="fw-bold mb-0 text-dark font-monospace"><?= number_format($stats['remaining_harvest'], 2); ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 bg-info-subtle text-info me-3">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Marketplace Quantity</h6>
                    <h4 class="fw-bold mb-0 text-dark font-monospace"><?= number_format($stats['marketplace_qty'], 2); ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Yield Harvests Transfer Status -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-seedling text-success me-2"></i>Yield Harvests Transfer Status</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Last Harvest Date</th>
                        <th>Product / Crop</th>
                        <th class="text-end">Total Harvested</th>
                        <th class="text-end">Transferred Qty</th>
                        <th class="text-end" style="width: 200px;">Remaining Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($harvests)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No harvest records registered. Go to Yield Harvesting to log harvests first.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($harvests as $h): ?>
                            <tr>
                                <td><?= date('m/d/Y', strtotime($h['harvest_date'])); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($h['product_name']); ?></div>
                                    <small class="text-muted font-monospace"><?= htmlspecialchars($h['product_code']); ?></small>
                                </td>
                                <td class="text-end font-monospace"><?= number_format($h['quantity'], 2); ?> <?= htmlspecialchars($h['unit']); ?></td>
                                <td class="text-end font-monospace text-success"><?= number_format($h['transferred_quantity'], 2); ?> <?= htmlspecialchars($h['unit']); ?></td>
                                <td class="text-end fw-bold font-monospace <?= $h['remaining_quantity'] > 0 ? 'text-dark' : 'text-muted opacity-50'; ?>">
                                    <?= number_format($h['remaining_quantity'], 2); ?> <?= htmlspecialchars($h['unit']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Transfer Logs History -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-success me-2"></i>Marketplace Transfer Logs History</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 150px;">Transfer Date</th>
                        <th>Product / Crop</th>
                        <th>Harvest Origin Date</th>
                        <th class="text-end">Quantity Transferred</th>
                        <th class="text-end">Cost Price</th>
                        <th class="text-end">Selling Price</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transfers)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-arrow-left-right fs-3 d-block mb-1 text-secondary opacity-50"></i>
                                No marketplace transfer history logged yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transfers as $t): ?>
                            <tr>
                                <td><?= date('m/d/Y', strtotime($t['transfer_date'])); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($t['product_name']); ?></div>
                                    <small class="text-muted font-monospace"><?= htmlspecialchars($t['product_code']); ?></small>
                                </td>
                                <td><?= date('m/d/Y', strtotime($t['harvest_date'])); ?></td>
                                <td class="text-end fw-bold font-monospace text-dark"><?= number_format($t['quantity'], 2); ?></td>
                                <td class="text-end font-monospace">LKR <?= number_format($t['cost_price_per_unit'], 2); ?></td>
                                <td class="text-end font-monospace fw-bold text-success">LKR <?= number_format($t['selling_price_per_unit'], 2); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-success">Completed</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: TRANSFER HARVEST -->
<div class="modal fade" id="transferHarvestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-success text-white py-3 border-0" style="background-color: #1b4332;">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-left-right me-2"></i> Transfer Yield to Marketplace</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('operations/plantation/marketplace/transfer'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="project_id" value="<?= $project['id']; ?>">
                <input type="hidden" id="cost_price" name="cost_price" value="<?= round($stats['cost_per_unit'], 4); ?>">
                
                <div class="modal-body p-4">
                    <!-- Project Cost Summary Card -->
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <h6 class="fw-bold mb-2 small text-dark"><i class="bi bi-calculator me-1"></i>Project Costing Information</h6>
                        <div class="row g-2 text-muted small">
                            <div class="col-6">Total Posted Expenses:</div>
                            <div class="col-6 text-end fw-bold text-dark font-monospace">LKR <?= number_format($stats['total_expenses'], 2); ?></div>
                            <div class="col-6">Total Project Harvest:</div>
                            <div class="col-6 text-end fw-bold text-dark font-monospace"><?= number_format($stats['total_harvested'], 2); ?> Units</div>
                            <hr class="my-1 col-12">
                            <div class="col-6 fw-semibold text-dark">Calculated Unit Cost:</div>
                            <div class="col-6 text-end fw-bold text-success font-monospace">LKR <?= number_format($stats['cost_per_unit'], 2); ?> / Unit</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="harvest_id" class="form-label fw-semibold small">Select Crop / Product <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="harvest_id" name="harvest_id" required onchange="handleHarvestChange(this)">
                            <option value="">-- Select Crop / Product --</option>
                            <?php foreach ($harvests as $h): ?>
                                <?php if ($h['remaining_quantity'] > 0): ?>
                                    <option value="<?= $h['product_id']; ?>" data-rem="<?= $h['remaining_quantity']; ?>" data-unit="<?= htmlspecialchars($h['unit']); ?>" data-sell="<?= round($h['default_selling_price'], 2); ?>">
                                        <?= htmlspecialchars($h['product_code']); ?> - <?= htmlspecialchars($h['product_name']); ?> (Available Total: <?= number_format($h['remaining_quantity'], 2); ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="transfer_date" class="form-label fw-semibold small">Transfer Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="transfer_date" name="transfer_date" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-6">
                            <label for="quantity" class="form-label fw-semibold small">Quantity to Transfer <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control form-control-sm font-monospace" id="quantity" name="quantity" placeholder="0.00" required>
                            <div class="form-text small text-muted" id="avail_hint">Max: 0.00</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="cost_price_display" class="form-label fw-semibold small">Cost Price Per Unit (LKR)</label>
                            <input type="text" class="form-control form-control-sm bg-light font-monospace" id="cost_price_display" value="<?= number_format($stats['cost_per_unit'], 2); ?>" readonly>
                        </div>
                        <div class="col-6">
                            <label for="selling_price" class="form-label fw-semibold small">Selling Price Per Unit (LKR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.00" class="form-control form-control-sm font-monospace" id="selling_price" name="selling_price" value="<?= round($stats['cost_per_unit'], 2); ?>" required oninput="calculateMargin()">
                        </div>
                    </div>

                    <!-- Dynamic Margin Calculator Block -->
                    <div class="p-3 rounded-3 border" style="background-color: #f4f9f4;">
                        <h6 class="fw-bold mb-2 small text-dark"><i class="bi bi-graph-up-arrow me-1"></i>Expected Sales Margin</h6>
                        <div class="row g-1 small">
                            <div class="col-7 text-muted">Expected Profit Per Unit:</div>
                            <div class="col-5 text-end fw-bold text-dark font-monospace" id="profit_display">LKR 0.00</div>
                            <div class="col-7 text-muted">Expected Margin %:</div>
                            <div class="col-5 text-end fw-bold text-success font-monospace" id="margin_display">0.00%</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Transfer Yield</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleHarvestChange(select) {
    const selectedOption = select.options[select.selectedIndex];
    const rem = selectedOption.getAttribute('data-rem') || '0.00';
    const unit = selectedOption.getAttribute('data-unit') || '';
    const sell = selectedOption.getAttribute('data-sell') || '0.00';
    
    const qtyInput = document.getElementById('quantity');
    const sellInput = document.getElementById('selling_price');
    if (selectedOption.value !== "") {
        qtyInput.max = rem;
        document.getElementById('avail_hint').innerText = "Max: " + rem + " " + unit;
        sellInput.value = sell;
    } else {
        qtyInput.removeAttribute('max');
        document.getElementById('avail_hint').innerText = "Max: 0.00";
        sellInput.value = "0.00";
    }
    calculateMargin();
}

function calculateMargin() {
    const cost = parseFloat(document.getElementById('cost_price').value) || 0.00;
    const sell = parseFloat(document.getElementById('selling_price').value) || 0.00;
    
    const profit = sell - cost;
    const margin = sell > 0 ? (profit / sell * 100) : 0.00;
    
    document.getElementById('profit_display').innerText = "LKR " + profit.toFixed(2);
    
    const marginDisplay = document.getElementById('margin_display');
    marginDisplay.innerText = margin.toFixed(2) + "%";
    
    if (margin < 0) {
        marginDisplay.className = "col-5 text-end fw-bold text-danger font-monospace";
    } else {
        marginDisplay.className = "col-5 text-end fw-bold text-success font-monospace";
    }
}

// Initial run
document.addEventListener("DOMContentLoaded", function() {
    calculateMargin();
});
</script>
