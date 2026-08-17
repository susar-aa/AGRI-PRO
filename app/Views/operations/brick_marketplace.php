<?php
// Variables available: $project, $totalExpenses, $totalProduced, $costPerUnit, $transfers, $productionRecords, $remainingQuantity, $totalTransferred, $currentSellingPrice
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <div class="mb-1">
            <a href="<?= \Core\Helper::baseUrl('operations/brick-manufacturing/view?id=' . $project['id']); ?>" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
        </div>
        <h4 class="fw-bold mb-1 text-dark">Transfer Finished Production to Marketplace</h4>
        <p class="text-muted small mb-0"><i class="bi bi-bricks text-danger me-1"></i>Project: <?= htmlspecialchars($project['project_name']); ?></p>
    </div>
    <div>
        <?php if ($project['status'] === 'ACTIVE' && $remainingQuantity > 0): ?>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#transferModal">
                <i class="bi bi-send me-1"></i> + Transfer to Marketplace
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Cost Calculation Display Row -->
<div class="row g-3 mb-4">
    <!-- Total Production Expenses -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-danger border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.65rem;">Total Posted Expenses</span>
            <h4 class="fw-bold text-danger mb-0 font-monospace">LKR <?= number_format($totalExpenses, 2); ?></h4>
            <small class="text-muted">Expenses from posted journals</small>
        </div>
    </div>
    <!-- Total Quantity Produced -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-warning border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.65rem;">Total Produced</span>
            <h4 class="fw-bold text-dark mb-0 font-monospace"><?= number_format($totalProduced); ?> <?= htmlspecialchars($project['unit']); ?></h4>
            <small class="text-muted">Total of logged production batches</small>
        </div>
    </div>
    <!-- Calculated Cost Per Unit -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-success border-4 h-100" style="background-color: #f0fdf4 !important;">
            <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.65rem;">Calculated Cost Price</span>
            <h4 class="fw-bold text-success mb-0 font-monospace">LKR <?= number_format($costPerUnit, 2); ?></h4>
            <small class="text-muted">Expenses ÷ Total Produced</small>
        </div>
    </div>
</div>

<!-- Stock Quantities Status Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border p-3 rounded-4 bg-white text-center">
            <span class="text-muted small d-block mb-1">Total Produced</span>
            <h3 class="fw-bold text-dark mb-0 font-monospace"><?= number_format($totalProduced); ?></h3>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border p-3 rounded-4 bg-white text-center">
            <span class="text-muted small d-block mb-1 text-success">Transferred to Marketplace</span>
            <h3 class="fw-bold text-success mb-0 font-monospace"><?= number_format($totalTransferred); ?></h3>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border p-3 rounded-4 bg-white text-center">
            <span class="text-muted small d-block mb-1 text-warning">Remaining in Production Yard</span>
            <h3 class="fw-bold text-warning mb-0 font-monospace"><?= number_format($remainingQuantity); ?></h3>
        </div>
    </div>
</div>

<!-- Transfers Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Marketplace Transfer Logs</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Transfer Date</th>
                        <th>Brick Product / Type</th>
                        <th class="text-end">Quantity</th>
                        <th>Unit</th>
                        <th class="text-end">Cost Price / Unit</th>
                        <th class="text-end">Selling Price / Unit</th>
                        <th>Transferred By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transfers)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No finished bricks transferred to marketplace yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transfers as $t): ?>
                            <tr>
                                <td class="font-monospace fw-bold"><?= date('Y-m-d', strtotime($t['transfer_date'])); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($project['product_name']); ?></div>
                                    <small class="text-muted font-monospace"><?= htmlspecialchars($project['product_code']); ?></small>
                                </td>
                                <td class="text-end font-monospace fw-bold text-primary"><?= number_format($t['quantity'], 2); ?></td>
                                <td><?= htmlspecialchars($project['unit']); ?></td>
                                <td class="text-end font-monospace">LKR <?= number_format($t['cost_price_per_unit'], 2); ?></td>
                                <td class="text-end font-monospace text-success fw-bold">LKR <?= number_format($t['selling_price_per_unit'], 2); ?></td>
                                <td><?= htmlspecialchars($t['creator_name'] ?: 'System'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Transfer Production -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold" id="transferModalLabel"><i class="bi bi-send me-2"></i>Transfer to Marketplace</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('operations/brick-manufacturing/marketplace/transfer'); ?>" method="POST" id="transferForm">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="project_id" value="<?= $project['id']; ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="transfer_date" class="form-label small fw-semibold">Transfer Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm rounded-3" id="transfer_date" name="transfer_date" value="<?= date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Brick Type / Product</label>
                        <input type="text" class="form-control form-control-sm rounded-3 bg-light" value="<?= htmlspecialchars($project['product_code']); ?> - <?= htmlspecialchars($project['product_name']); ?>" readonly>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-8">
                            <label for="quantity" class="form-label small fw-semibold">Quantity to Transfer <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" max="<?= $remainingQuantity; ?>" class="form-control form-control-sm rounded-3 font-monospace" id="quantity" name="quantity" placeholder="e.g. 500" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Available Max</label>
                            <input type="text" class="form-control form-control-sm rounded-3 bg-light font-monospace" value="<?= number_format($remainingQuantity, 2, '.', ''); ?>" readonly>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Calculated Cost / Unit</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light font-monospace" style="font-size: 0.75rem;">LKR</span>
                                <input type="text" class="form-control rounded-end-3 bg-light font-monospace" value="<?= number_format($costPerUnit, 2, '.', ''); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="selling_price" class="form-label small fw-semibold">Selling Price / Unit <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light font-monospace" style="font-size: 0.75rem;">LKR</span>
                                <input type="number" step="0.01" min="0.01" class="form-control rounded-end-3 font-monospace" id="selling_price" name="selling_price" value="<?= number_format($currentSellingPrice ?: $costPerUnit * 1.5, 2, '.', ''); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4">Complete Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>
