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
        <h4 class="fw-bold mb-1 text-dark">Operations Management</h4>
        <p class="text-muted small mb-0">Manage operational categories for individual business activities.</p>
    </div>
    <div>
        <button class="btn btn-success rounded-pill px-4 shadow-sm" style="background-color: #1b4332; border-color: #1b4332;" data-bs-toggle="modal" data-bs-target="#addCcModal">
            <i class="bi bi-plus-lg me-1"></i> Add Operation
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart-fill text-success me-2"></i> Registered Operations</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 120px;">Code</th>
                        <th>Operation Name</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($costCenters)): ?>
                        <?php foreach ($costCenters as $cc): ?>
                            <tr>
                                <td class="fw-bold font-monospace text-dark"><?= htmlspecialchars($cc['code']); ?></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($cc['name']); ?></td>
                                <td class="text-center">
                                    <?php if ($cc['is_active']): ?>
                                        <span class="badge bg-success rounded-pill">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No operations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Cost Center -->
<div class="modal fade" id="addCcModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #1b4332 !important;">
                <h5 class="modal-title font-weight-bold"><i class="bi bi-plus-circle me-2"></i> Add Operation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('cost-centers/store'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Operation Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Nursery & Seed Production" required>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Active Status</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Save Operation</button>
                </div>
            </form>
        </div>
    </div>
</div>
