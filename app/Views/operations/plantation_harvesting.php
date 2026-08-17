<?php
// Variables available: $project, $crops, $harvests, $stats, $cropHarvestTotals
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
        <h4 class="fw-bold mb-1 text-dark">Yield Harvesting Log</h4>
        <p class="text-muted small mb-0">Project: <strong class="text-dark"><?= htmlspecialchars($project['project_name']); ?></strong> &bull; Location: <?= htmlspecialchars($project['location']); ?></p>
    </div>
    <div>
        <?php if (!empty($crops)): ?>
            <button class="btn btn-success rounded-pill px-4 shadow-sm" style="background-color: #1b4332; border-color: #1b4332;" data-bs-toggle="modal" data-bs-target="#recordHarvestModal">
                <i class="bi bi-plus-lg me-1"></i> Record Harvest
            </button>
        <?php else: ?>
            <button class="btn btn-secondary rounded-pill px-4 shadow-sm" disabled title="No crops registered to this project yet.">
                <i class="bi bi-plus-lg me-1"></i> Record Harvest
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 bg-success-subtle text-success me-3">
                    <i class="bi bi-gift fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Total Yield Harvested</h6>
                    <h4 class="fw-bold mb-0 text-dark font-monospace"><?= number_format($stats['total_quantity'], 2); ?> <span class="fs-6 text-muted font-sans fw-normal">Units</span></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 bg-info-subtle text-info me-3">
                    <i class="bi bi-calendar2-check fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Harvest Events</h6>
                    <h4 class="fw-bold mb-0 text-dark"><?= number_format($stats['stats_count'] ?? $stats['harvests_count']); ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 bg-warning-subtle text-warning me-3">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Last Harvest Date</h6>
                    <h4 class="fw-bold mb-0 text-dark"><?= $stats['last_harvest_date'] !== '-' ? date('m/d/Y', strtotime($stats['last_harvest_date'])) : '-'; ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cultivated Crops & Total Harvest Progress -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-seedling text-success me-2"></i>Crop Cultivation &amp; Harvest Summary</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Crop Code</th>
                        <th>Crop / Product Name</th>
                        <th>Unit</th>
                        <th class="text-end" style="width: 200px;">Total Yield Harvested</th>
                        <th class="text-center" style="width: 150px;">Harvest Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($crops)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No crops currently registered to this project. Go to Crop Register to add crops.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($crops as $c): ?>
                            <?php $pid = (int)$c['product_id']; ?>
                            <tr>
                                <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($c['product_code']); ?></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($c['product_name']); ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($c['unit']); ?></span></td>
                                <td class="text-end fw-bold font-monospace text-success"><?= number_format($cropHarvestTotals[$pid] ?? 0.00, 2); ?></td>
                                <td class="text-center">
                                    <?php if (($cropHarvestTotals[$pid] ?? 0) > 0): ?>
                                        <span class="badge bg-success">Harvesting</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary text-white">Pending Harvest</span>
                                    <?php ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Harvest Logs History -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-success me-2"></i>Harvest Records History Ledger</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 120px;">Harvest Date</th>
                        <th>Crop Code</th>
                        <th>Crop / Product Name</th>
                        <th class="text-end">Quantity</th>
                        <th>Unit</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($harvests)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-3 d-block mb-1 text-secondary opacity-50"></i>
                                No harvest records logged yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($harvests as $h): ?>
                            <tr>
                                <td><?= date('m/d/Y', strtotime($h['harvest_date'])); ?></td>
                                <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($h['product_code']); ?></td>
                                <td><?= htmlspecialchars($h['product_name']); ?></td>
                                <td class="text-end fw-bold font-monospace text-dark"><?= number_format($h['quantity'], 2); ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($h['unit']); ?></span></td>
                                <td class="text-muted small"><?= htmlspecialchars($h['notes'] ?: '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: RECORD HARVEST -->
<div class="modal fade" id="recordHarvestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-success text-white py-3 border-0" style="background-color: #1b4332;">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Record Yield Harvest</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('operations/plantation/harvesting/store'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="project_id" value="<?= $project['id']; ?>">
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="product_id" class="form-label fw-semibold small">Select Cultivated Crop <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="product_id" name="product_id" required onchange="handleCropChange(this)">
                            <option value="">-- Select Crop --</option>
                            <?php foreach ($crops as $c): ?>
                                <option value="<?= $c['product_id']; ?>" data-unit="<?= htmlspecialchars($c['unit']); ?>"><?= htmlspecialchars($c['product_code']); ?> - <?= htmlspecialchars($c['product_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="harvest_date" class="form-label fw-semibold small">Harvest Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="harvest_date" name="harvest_date" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-6">
                            <label for="quantity" class="form-label fw-semibold small">Harvested Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control form-control-sm" id="quantity" name="quantity" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label for="harvest_unit" class="form-label fw-semibold small">Unit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm bg-light" id="harvest_unit" name="unit" value="Units" required readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold small">Harvest Notes / Remarks (Optional)</label>
                        <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2" placeholder="e.g. Weather conditions, moisture content..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Record Harvest</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleCropChange(select) {
    const selectedOption = select.options[select.selectedIndex];
    const unit = selectedOption.getAttribute('data-unit') || 'Units';
    document.getElementById('harvest_unit').value = unit;
}
</script>
