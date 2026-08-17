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
        <h4 class="fw-bold mb-1 text-dark">Machinery Assets Directory</h4>
        <p class="text-muted small mb-0">Manage mechanical equipment, tractors, power tools, water pumps, and generators rented to co-op farmers.</p>
    </div>
    <div>
        <?php if (\Core\Auth::hasPermission('machinery.create')): ?>
            <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#machineryModal" onclick="openCreateModal()" style="background-color: #1b4332; border-color: #1b4332;">
                <i class="bi bi-plus-lg me-1"></i> Add Machinery Asset
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('modules/machinery'); ?>" method="GET" class="row g-3 small">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Search Asset</label>
                <input type="text" class="form-control form-control-sm" name="search" value="<?= htmlspecialchars($filters['search']); ?>" placeholder="Code, asset name, serial #...">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Category</label>
                <select class="form-select form-select-sm" name="category">
                    <option value="">-- All Categories --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat; ?>" <?= ($filters['category'] === $cat) ? 'selected' : ''; ?>><?= htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Operational Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">-- All Statuses --</option>
                    <option value="AVAILABLE" <?= ($filters['status'] === 'AVAILABLE') ? 'selected' : ''; ?>>Available</option>
                    <option value="RENTED" <?= ($filters['status'] === 'RENTED') ? 'selected' : ''; ?>>Rented Out</option>
                    <option value="MAINTENANCE" <?= ($filters['status'] === 'MAINTENANCE') ? 'selected' : ''; ?>>In Maintenance</option>
                    <option value="INACTIVE" <?= ($filters['status'] === 'INACTIVE') ? 'selected' : ''; ?>>Deactivated / Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 w-100" style="background-color: #1b4332; border-color: #1b4332;">Filter</button>
                <a href="<?= \Core\Helper::baseUrl('modules/machinery'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Grid Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Machinery Code</th>
                        <th>Name</th>
                        <th class="text-end">Default Rate</th>
                        <th>Rental Unit</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($machineryList)): ?>
                        <?php foreach ($machineryList as $mac): ?>
                            <tr>
                                <td class="fw-bold font-monospace text-dark"><?= htmlspecialchars($mac['machinery_code']); ?></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($mac['machinery_name']); ?></div>
                                </td>
                                <td class="text-end font-monospace fw-bold text-dark">LKR <?= number_format($mac['default_rental_rate'], 2); ?></td>
                                <td>per <?= htmlspecialchars($mac['rental_unit']); ?></td>
                                <td class="text-center">
                                    <?php
                                    $isActive = (bool)$mac['is_active'];
                                    $badge = $isActive ? 'bg-success' : 'bg-dark';
                                    $label = $isActive ? 'ACTIVE' : 'INACTIVE';
                                    ?>
                                    <span class="badge <?= $badge ?> px-3 py-1"><?= $label; ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group gap-1">
                                        
                                        <?php if (\Core\Auth::hasPermission('machinery.edit')): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2" onclick='openEditModal(<?= json_encode($mac, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>Edit</button>
                                        <?php endif; ?>

                                        <?php if ($isActive && \Core\Auth::hasPermission('machinery.deactivate')): ?>
                                            <form action="<?= \Core\Helper::baseUrl('modules/machinery/deactivate'); ?>" method="POST" class="d-inline" onsubmit="return confirm('Deactivate this machinery asset?');">
                                                <?= \Core\CSRF::getFormField(); ?>
                                                <input type="hidden" name="id" value="<?= $mac['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-dark rounded-pill px-2">Deactivate</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No machinery assets registered.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="machineryModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark" id="modalTitle">Register Machinery Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/machinery/store'); ?>" method="POST" id="machineryForm">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" id="machinery_id" name="id" value="">
                
                <div class="modal-body py-3 small">
                    <div class="mb-3">
                        <label for="form_code" class="form-label fw-semibold">Machinery Code <small class="text-muted">(Optional)</small></label>
                        <input type="text" class="form-control" id="form_code" name="machinery_code" placeholder="e.g. MAC-004">
                    </div>

                    <div class="mb-3">
                        <label for="form_name" class="form-label fw-semibold">Machinery Asset Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="form_name" name="machinery_name" placeholder="e.g. Caterpillar Diesel Generator 10kVA" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="form_rate" class="form-label fw-semibold">Default Rental Rate (LKR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.00" class="form-control" id="form_rate" name="default_rental_rate" value="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <label for="form_unit" class="form-label fw-semibold">Rental Unit <span class="text-danger">*</span></label>
                            <select class="form-select" id="form_unit" name="rental_unit" required>
                                <option value="Hour">Hour</option>
                                <option value="Day">Day</option>
                                <option value="Job">Job</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Save Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Register Machinery Asset';
    document.getElementById('machineryForm').reset();
    document.getElementById('machinery_id').value = '';
    document.getElementById('form_code').disabled = false;
}

function openEditModal(mac) {
    document.getElementById('modalTitle').textContent = 'Edit Machinery Asset: ' + mac.machinery_code;
    document.getElementById('machinery_id').value = mac.id;
    
    document.getElementById('form_code').value = mac.machinery_code;
    document.getElementById('form_code').disabled = true;
    
    document.getElementById('form_name').value = mac.machinery_name;
    document.getElementById('form_rate').value = mac.default_rental_rate;
    document.getElementById('form_unit').value = mac.rental_unit;

    const modal = new bootstrap.Modal(document.getElementById('machineryModal'));
    modal.show();
}
</script>
