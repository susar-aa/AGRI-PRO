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
        <h4 class="fw-bold mb-1 text-dark">Central Business Contacts Directory</h4>
        <p class="text-muted small mb-0">Manage all cooperative buyers, sellers, suppliers, and members in a unified contact list.</p>
    </div>
    <div>
        <?php if (\Core\Auth::hasPermission('parties.create')): ?>
            <a href="<?= \Core\Helper::baseUrl('parties/create'); ?>" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                <i class="bi bi-plus-lg me-1"></i> Add Contact / Party
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('parties'); ?>" method="GET" class="row g-3 small">
            <div class="col-12 col-md-5">
                <label class="form-label fw-semibold">Search Name, Code, Phone, or Registration</label>
                <input type="text" class="form-control form-control-sm" name="search" value="<?= htmlspecialchars($filters['search']); ?>" placeholder="Type name, PTY-..., phone, etc.">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Party Type</label>
                <select class="form-select form-select-sm" name="party_type">
                    <option value="">-- All Types --</option>
                    <option value="CUSTOMER" <?= ($filters['party_type'] === 'CUSTOMER') ? 'selected' : ''; ?>>Customer</option>
                    <option value="SUPPLIER" <?= ($filters['party_type'] === 'SUPPLIER') ? 'selected' : ''; ?>>Supplier</option>
                    <option value="BOTH" <?= ($filters['party_type'] === 'BOTH') ? 'selected' : ''; ?>>Both (Customer & Supplier)</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">-- All --</option>
                    <option value="active" <?= ($filters['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?= ($filters['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill" style="background-color: #1b4332; border-color: #1b4332;">
                    <i class="bi bi-search"></i> Find
                </button>
                <a href="<?= \Core\Helper::baseUrl('parties'); ?>" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Party Code</th>
                        <th>Name / Business Name</th>
                        <th>Type</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($parties)): ?>
                        <?php foreach ($parties as $pty): ?>
                            <tr>
                                <td class="fw-bold font-monospace">
                                    <a href="<?= \Core\Helper::baseUrl('parties/view?id=' . $pty['id']); ?>" class="text-success text-decoration-none">
                                        <?= htmlspecialchars($pty['party_code']); ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($pty['name']); ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($pty['city'] ?? ''); ?><?= !empty($pty['district']) ? ', ' . htmlspecialchars($pty['district']) : ''; ?></small>
                                </td>
                                <td>
                                    <?php
                                    $pt = $pty['party_type'];
                                    $ptClass = 'bg-primary-subtle text-primary border-primary-subtle';
                                    if ($pt === 'SUPPLIER') $ptClass = 'bg-info-subtle text-info border-info-subtle';
                                    elseif ($pt === 'BOTH') $ptClass = 'bg-success-subtle text-success border-success-subtle';
                                    ?>
                                    <span class="badge border <?= $ptClass ?> px-2 py-1"><?= ucfirst(strtolower($pt)); ?></span>
                                </td>
                                <td><?= htmlspecialchars($pty['contact_person'] ?: '-'); ?></td>
                                <td><?= htmlspecialchars($pty['phone'] ?: '-'); ?></td>
                                <td><?= htmlspecialchars($pty['email'] ?: '-'); ?></td>
                                <td class="text-center">
                                    <?php if ($pty['status'] === 'active'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group gap-1">
                                        <a href="<?= \Core\Helper::baseUrl('parties/view?id=' . $pty['id']); ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <?php if (\Core\Auth::hasPermission('parties.edit')): ?>
                                            <a href="<?= \Core\Helper::baseUrl('parties/edit?id=' . $pty['id']); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No business contacts found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total'] > 1): ?>
        <div class="card-footer bg-white border-0 py-3">
            <nav>
                <ul class="pagination pagination-sm justify-content-center mb-0 gap-1">
                    <li class="page-item <?= ($pagination['current'] <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link rounded-circle border-0" href="?<?= http_build_query(array_merge($filters, ['page' => $pagination['current'] - 1])); ?>"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                        <li class="page-item <?= ($pagination['current'] == $i) ? 'active' : ''; ?>">
                            <a class="page-link rounded-circle border-0 px-3 <?= ($pagination['current'] == $i) ? 'bg-success' : 'text-success'; ?>" href="?<?= http_build_query(array_merge($filters, ['page' => $i])); ?>" <?= ($pagination['current'] == $i) ? 'style="background-color: #1b4332 !important; color: white !important;"' : ''; ?>><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($pagination['current'] >= $pagination['total']) ? 'disabled' : ''; ?>">
                        <a class="page-link rounded-circle border-0" href="?<?= http_build_query(array_merge($filters, ['page' => $pagination['current'] + 1])); ?>"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
