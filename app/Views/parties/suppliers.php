<?php if ($flashSuccess = \Core\Session::getFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Suppliers Accounts Directory</h4>
        <p class="text-muted small mb-0">Record and manage supplier classifications, payment terms, and cooperative trading arrangements.</p>
    </div>
    <div>
        <?php if (\Core\Auth::hasPermission('parties.create')): ?>
            <a href="<?= \Core\Helper::baseUrl('parties/create?prefill_type=SUPPLIER'); ?>" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                <i class="bi bi-plus-lg me-1"></i> Register Supplier
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('parties/suppliers'); ?>" method="GET" class="row g-3 small">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Search Suppliers</label>
                <input type="text" class="form-control form-control-sm" name="search" value="<?= htmlspecialchars($filters['search']); ?>" placeholder="Name, Supplier Code, Contact Person...">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">-- All Statuses --</option>
                    <option value="active" <?= ($filters['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?= ($filters['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill" style="background-color: #1b4332; border-color: #1b4332;">
                    <i class="bi bi-search"></i> Search
                </button>
                <a href="<?= \Core\Helper::baseUrl('parties/suppliers'); ?>" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">Reset</a>
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
                        <th>Supplier Details</th>
                        <th>Contact</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($suppliers)): ?>
                        <?php foreach ($suppliers as $supp): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($supp['name']); ?></div>
                                    <small class="text-success font-monospace fw-semibold">
                                        <a href="<?= \Core\Helper::baseUrl('parties/view?id=' . $supp['id']); ?>" class="text-success text-decoration-none">
                                            <?= htmlspecialchars($supp['party_code']); ?>
                                        </a>
                                    </small>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><?= htmlspecialchars($supp['phone'] ?: '-'); ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($supp['contact_person'] ?: ''); ?></small>
                                </td>
                                <td class="text-center">
                                    <?php if ($supp['status'] === 'active'): ?>
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= \Core\Helper::baseUrl('parties/view?id=' . $supp['id']); ?>" class="btn btn-outline-success px-3" title="Profile"><i class="bi bi-person-fill"></i> Profile</a>
                                        <?php if (\Core\Auth::hasPermission('parties.edit')): ?>
                                            <a href="<?= \Core\Helper::baseUrl('parties/edit?id=' . $supp['id']); ?>" class="btn btn-outline-primary px-3" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <?php endif; ?>
                                        <?php if (\Core\Auth::hasPermission('parties.delete')): ?>
                                            <form action="<?= \Core\Helper::baseUrl('parties/delete'); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                                                <?= \Core\CSRF::getFormField(); ?>
                                                <input type="hidden" name="id" value="<?= $supp['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger px-3" title="Delete"><i class="bi bi-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No suppliers registered.</td>
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
