<?php
// Variables available: $project, $productionRecords, $totalProduced
$planned = (float)$project['planned_quantity'];
$remaining = max(0.00, $planned - $totalProduced);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <div class="mb-1">
            <a href="<?= \Core\Helper::baseUrl('operations/brick-manufacturing/view?id=' . $project['id']); ?>" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
        </div>
        <h4 class="fw-bold mb-1 text-dark">Brick Production Batches Log</h4>
        <p class="text-muted small mb-0"><i class="bi bi-bricks text-danger me-1"></i>Project: <?= htmlspecialchars($project['project_name']); ?></p>
    </div>
    <div>
        <?php if ($project['status'] === 'ACTIVE'): ?>
            <button class="btn btn-warning rounded-pill px-4 text-dark fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#productionModal">
                <i class="bi bi-plus-lg me-1"></i> Record Production Batch
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Production Progress Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-primary border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.65rem;">Planned Target</span>
            <h3 class="fw-bold text-dark mb-0 font-monospace"><?= number_format($planned); ?> <?= htmlspecialchars($project['unit']); ?></h3>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-success border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.65rem;">Total Produced</span>
            <h3 class="fw-bold text-success mb-0 font-monospace"><?= number_format($totalProduced); ?> <?= htmlspecialchars($project['unit']); ?></h3>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-warning border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.65rem;">Remaining Planned</span>
            <h3 class="fw-bold text-warning mb-0 font-monospace"><?= number_format($remaining); ?> <?= htmlspecialchars($project['unit']); ?></h3>
        </div>
    </div>
</div>

<!-- Production Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-list-task text-warning me-2"></i>Recorded Production Batches</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Brick Product / Type</th>
                        <th class="text-end">Quantity Produced</th>
                        <th>Unit</th>
                        <th>Logged By</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productionRecords)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No production batches logged for this project yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productionRecords as $r): ?>
                            <tr>
                                <td class="font-monospace fw-bold"><?= date('Y-m-d', strtotime($r['production_date'])); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($project['product_name']); ?></div>
                                    <small class="text-muted font-monospace"><?= htmlspecialchars($project['product_code']); ?></small>
                                </td>
                                <td class="text-end font-monospace fw-bold text-success"><?= number_format($r['quantity'], 2); ?></td>
                                <td><?= htmlspecialchars($r['unit']); ?></td>
                                <td><?= htmlspecialchars($r['creator_name'] ?: 'System'); ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($r['notes']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Record Production -->
<div class="modal fade" id="productionModal" tabindex="-1" aria-labelledby="productionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold" id="productionModalLabel"><i class="bi bi-hammer me-2"></i>Log Production Batch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('operations/brick-manufacturing/production/store'); ?>" method="POST" id="productionForm">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="project_id" value="<?= $project['id']; ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="production_date" class="form-label small fw-semibold">Production Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm rounded-3" id="production_date" name="production_date" value="<?= date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Brick Product / Type</label>
                        <input type="text" class="form-control form-control-sm rounded-3 bg-light" value="<?= htmlspecialchars($project['product_code']); ?> - <?= htmlspecialchars($project['product_name']); ?>" readonly>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-8">
                            <label for="quantity" class="form-label small fw-semibold">Quantity Produced <span class="text-danger">*</span></label>
                            <input type="number" step="1" min="1" class="form-control form-control-sm rounded-3 font-monospace" id="quantity" name="quantity" placeholder="e.g. 1000" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Unit</label>
                            <input type="text" class="form-control form-control-sm rounded-3 bg-light" value="<?= htmlspecialchars($project['unit']); ?>" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label small fw-semibold">Notes</label>
                        <textarea class="form-control form-control-sm rounded-3" id="notes" name="notes" rows="2" placeholder="Casting notes, kiln details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-warning rounded-pill px-4 text-dark fw-bold">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('productionForm').addEventListener('submit', function(e) {
    const qty = parseFloat(document.getElementById('quantity').value) || 0;
    const planned = <?= $planned; ?>;
    const remaining = <?= $remaining; ?>;
    if (planned > 0 && qty > remaining) {
        if (!confirm("The entered quantity (" + qty + ") exceeds the remaining planned quantity (" + remaining + "). Are you sure you want to log an overproduction?")) {
            e.preventDefault();
        }
    }
});
</script>
