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
        <h4 class="fw-bold mb-1 text-dark">Service Master Registry</h4>
        <p class="text-muted small mb-0">Manage services provided by the society, configure default rates, and map to ledger revenue accounts.</p>
    </div>
    <div>
        <?php if (\Core\Auth::hasPermission('services.create')): ?>
            <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#editServiceModal" style="background-color: #1b4332; border-color: #1b4332;">
                <i class="bi bi-plus-lg me-1"></i> Register Service Item
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('modules/services'); ?>" method="GET" class="row g-3 small">
            <div class="col-md-8">
                <input type="text" class="form-control form-control-sm" name="search" value="<?= htmlspecialchars($filters['search']); ?>" placeholder="Search by code or name...">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 w-100" style="background-color: #1b4332; border-color: #1b4332;">Search</button>
                <a href="<?= \Core\Helper::baseUrl('modules/services'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 w-100">Reset</a>
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
                        <th>Code</th>
                        <th>Service Name</th>
                        <th class="text-end">Default Price</th>
                        <th>Unit</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($services)): ?>
                        <?php foreach ($services as $srv): ?>
                            <tr>
                                <td class="font-monospace text-secondary fw-semibold"><?= htmlspecialchars($srv['service_code']); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($srv['service_name']); ?></div>
                                    <?php if ($srv['description']): ?>
                                        <small class="text-muted d-block text-truncate" style="max-width: 250px;"><?= htmlspecialchars($srv['description']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end font-monospace fw-bold text-dark">LKR <?= number_format($srv['default_price'], 2); ?></td>
                                <td><?= htmlspecialchars($srv['unit']); ?></td>
                                <td class="text-center">
                                    <span class="badge <?= $srv['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?= $srv['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group gap-1">
                                        <?php if (\Core\Auth::hasPermission('services.edit')): ?>
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editServiceModal"
                                                    data-id="<?= $srv['id']; ?>"
                                                    data-code="<?= htmlspecialchars($srv['service_code']); ?>"
                                                    data-name="<?= htmlspecialchars($srv['service_name']); ?>"
                                                    data-price="<?= $srv['default_price']; ?>"
                                                    data-unit="<?= htmlspecialchars($srv['unit']); ?>"
                                                    data-desc="<?= htmlspecialchars($srv['description']); ?>"
                                                    data-active="<?= $srv['is_active']; ?>">
                                                Edit
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($srv['is_active'] && \Core\Auth::hasPermission('services.deactivate')): ?>
                                            <form action="<?= \Core\Helper::baseUrl('modules/services/deactivate'); ?>" method="POST" class="d-inline" onsubmit="return confirm('Deactivate this service item?');">
                                                <?= \Core\CSRF::getFormField(); ?>
                                                <input type="hidden" name="id" value="<?= $srv['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Deactivate</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No services registered.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Create / Edit Service -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #1b4332;">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="bi bi-gear-wide-connected me-2"></i> Service Configuration</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/services/store'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" id="srvId" value="">
                <div class="modal-body p-4 small">
                    
                    <div class="mb-3">
                        <label for="service_code" class="form-label fw-semibold">Service Code</label>
                        <input type="text" class="form-control form-control-sm font-monospace" id="service_code" name="service_code" placeholder="Auto-generated if left blank">
                    </div>

                    <div class="mb-3">
                        <label for="service_name" class="form-label fw-semibold">Service Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="service_name" name="service_name" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label for="unit" class="form-label fw-semibold">Service Unit <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="unit" name="unit" required>
                                <option value="Job">Job</option>
                                <option value="Hour">Hour</option>
                                <option value="Day">Day</option>
                                <option value="KG">KG</option>
                                <option value="Unit">Unit</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label for="default_price" class="form-label fw-semibold">Default Pricing Rate (LKR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace" id="default_price" name="default_price" required>
                        </div>
                    </div>



                    <div class="mb-3" id="statusSection" style="display: none;">
                        <label for="is_active" class="form-label fw-semibold">Active Status</label>
                        <select class="form-select form-select-sm" id="is_active" name="is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editServiceModal = document.getElementById('editServiceModal');
    if (editServiceModal) {
        editServiceModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const title = document.getElementById('modalTitle');
            const statusSection = document.getElementById('statusSection');

            if (id) {
                // Edit mode
                title.innerHTML = '<i class="bi bi-pencil-square me-2"></i> Edit Service Details';
                statusSection.style.display = 'block';
                
                document.getElementById('srvId').value = id;
                document.getElementById('service_code').value = button.getAttribute('data-code');
                document.getElementById('service_code').readOnly = true;
                document.getElementById('service_name').value = button.getAttribute('data-name');
                document.getElementById('default_price').value = button.getAttribute('data-price');
                document.getElementById('unit').value = button.getAttribute('data-unit');
                document.getElementById('is_active').value = button.getAttribute('data-active');
            } else {
                // Create mode
                title.innerHTML = '<i class="bi bi-plus-lg me-2"></i> Register Service Item';
                statusSection.style.display = 'none';
                
                document.getElementById('srvId').value = '';
                document.getElementById('service_code').value = '';
                document.getElementById('service_code').readOnly = false;
                document.getElementById('service_name').value = '';
                document.getElementById('default_price').value = '0.00';
                document.getElementById('unit').value = 'Job';
                document.getElementById('is_active').value = '1';
            }
        });
    }
});
</script>
