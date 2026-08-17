<?php
// Variables available: $project, $crops, $products
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
        <h4 class="fw-bold mb-1 text-dark">Plantation Crops Register</h4>
        <p class="text-muted small mb-0">Project: <strong class="text-dark"><?= htmlspecialchars($project['project_name']); ?></strong> &bull; Location: <?= htmlspecialchars($project['location']); ?></p>
    </div>
    <div>
        <button class="btn btn-success rounded-pill px-4 shadow-sm" style="background-color: #1b4332; border-color: #1b4332;" data-bs-toggle="modal" data-bs-target="#addCropModal">
            <i class="bi bi-plus-lg me-1"></i> Add Crop to Project
        </button>
    </div>
</div>

<!-- Crops Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-seedling text-success me-2"></i>Crops &amp; Plants Cultivating</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 150px;">Crop Code</th>
                        <th>Crop / Product Name</th>
                        <th style="width: 120px;">Unit</th>
                        <th>Cultivation Notes</th>
                        <th style="width: 150px;" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($crops)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-info-circle fs-3 d-block mb-1 text-secondary opacity-50"></i>
                                No crops listed under this project. Click "+ Add Crop to Project" to begin.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($crops as $c): ?>
                            <tr>
                                <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($c['product_code']); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($c['product_name']); ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($c['unit']); ?></span></td>
                                <td class="text-muted small"><?= htmlspecialchars($c['notes'] ?: '-'); ?></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <!-- Edit trigger button -->
                                        <button type="button" class="btn btn-sm btn-outline-secondary border-0 rounded-circle" data-bs-toggle="modal" data-bs-target="#editCropModal"
                                                data-id="<?= $c['id']; ?>"
                                                data-name="<?= htmlspecialchars($c['product_name']); ?>"
                                                data-unit="<?= htmlspecialchars($c['unit']); ?>"
                                                data-notes="<?= htmlspecialchars($c['notes']); ?>">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        
                                        <!-- Remove form -->
                                        <form action="<?= \Core\Helper::baseUrl('operations/plantation/crops/delete'); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this crop from the project?')">
                                            <?= \Core\CSRF::getFormField(); ?>
                                            <input type="hidden" name="id" value="<?= $c['id']; ?>">
                                            <input type="hidden" name="project_id" value="<?= $project['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: ADD CROP TO PROJECT -->
<div class="modal fade" id="addCropModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-success text-white py-3 border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Add Crop to Project</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('operations/plantation/crops/add'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="project_id" value="<?= $project['id']; ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="product_id" class="form-label fw-semibold small">Select Crop/Product <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="product_id" name="product_id" required onchange="handleProductChange(this)">
                            <option value="">-- Select Product --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id']; ?>" data-unit="<?= htmlspecialchars($p['unit_code'] ?: 'Units'); ?>"><?= htmlspecialchars($p['product_code']); ?> - <?= htmlspecialchars($p['name_en']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="crop_unit" class="form-label fw-semibold small">Unit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="crop_unit" name="unit" value="Units" required readonly>
                    </div>

                    <div class="mb-3">
                        <label for="crop_notes" class="form-label fw-semibold small">Cultivation Notes (Optional)</label>
                        <input type="text" class="form-control form-control-sm" id="crop_notes" name="notes" placeholder="e.g. Bed row B, green house">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Add Crop</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDIT CROP -->
<div class="modal fade" id="editCropModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-secondary text-white py-3 border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Crop Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('operations/plantation/crops/update'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="project_id" value="<?= $project['id']; ?>">
                <input type="hidden" name="id" id="edit_crop_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small d-block">Crop / Product</label>
                        <strong id="edit_crop_name" class="text-dark fs-6"></strong>
                    </div>

                    <div class="mb-3">
                        <label for="edit_crop_unit" class="form-label fw-semibold small">Unit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="edit_crop_unit" name="unit" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_crop_notes" class="form-label fw-semibold small">Cultivation Notes (Optional)</label>
                        <input type="text" class="form-control form-control-sm" id="edit_crop_notes" name="notes">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #6c757d; border-color: #6c757d;">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleProductChange(select) {
    const selectedOption = select.options[select.selectedIndex];
    const unit = selectedOption.getAttribute('data-unit') || 'Units';
    document.getElementById('crop_unit').value = unit;
}

document.addEventListener('DOMContentLoaded', () => {
    const editModal = document.getElementById('editCropModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', (event) => {
            const btn = event.relatedTarget;
            document.getElementById('edit_crop_id').value = btn.getAttribute('data-id');
            document.getElementById('edit_crop_name').innerText = btn.getAttribute('data-name');
            document.getElementById('edit_crop_unit').value = btn.getAttribute('data-unit');
            document.getElementById('edit_crop_notes').value = btn.getAttribute('data-notes') || '';
        });
    }
});
</script>
