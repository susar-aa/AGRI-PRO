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
        <h4 class="fw-bold mb-1 text-dark">Marketplace Sales Logs Registry</h4>
        <p class="text-muted small mb-0">List and filter internal marketplace sales invoices, record drafts, or post transactions.</p>
    </div>
    <div>
        <?php if (\Core\Auth::hasPermission('marketplace.sales.create')): ?>
            <a href="<?= \Core\Helper::baseUrl('modules/marketplace/sales/create'); ?>" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                <i class="bi bi-plus-lg me-1"></i> Record Sale Invoice
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('modules/marketplace/sales'); ?>" method="GET" class="row g-3 small">
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Search Invoice</label>
                <input type="text" class="form-control form-control-sm" name="search" value="<?= htmlspecialchars($filters['search']); ?>" placeholder="Sale #, notes...">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Customer</label>
                <select class="form-select form-select-sm" name="customer_id">
                    <option value="">-- All Customers --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id']; ?>" <?= ($filters['customer_id'] == $c['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($c['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">-- All --</option>
                    <option value="DRAFT" <?= ($filters['status'] === 'DRAFT') ? 'selected' : ''; ?>>Draft</option>
                    <option value="POSTED" <?= ($filters['status'] === 'POSTED') ? 'selected' : ''; ?>>Posted</option>
                    <option value="CANCELLED" <?= ($filters['status'] === 'CANCELLED') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold">From Date</label>
                <input type="date" class="form-control form-control-sm" name="date_from" value="<?= htmlspecialchars($filters['date_from']); ?>">
            </div>
            <div class="col-6 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 w-100" style="background-color: #1b4332; border-color: #1b4332;">Filter</button>
                <a href="<?= \Core\Helper::baseUrl('modules/marketplace/sales'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice Number</th>
                        <th>Sale Date</th>
                        <th>Customer</th>
                        <th class="text-end">Total</th>
                        <th>Payment Method</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sales)): ?>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td class="fw-bold font-monospace">
                                    <a href="<?= \Core\Helper::baseUrl('modules/marketplace/sales/view?id=' . $sale['id']); ?>" class="text-success text-decoration-none">
                                        <?= htmlspecialchars($sale['sale_number']); ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($sale['sale_date']); ?></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($sale['customer_name']); ?></div>
                                    <small class="text-muted font-monospace"><?= htmlspecialchars($sale['party_code']); ?></small>
                                </td>
                                <td class="text-end fw-bold text-dark font-monospace"><?= \Core\Helper::formatCurrency($sale['total']); ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($sale['payment_method']); ?></span>
                                    <?php if ($sale['sale_type'] === 'CREDIT'): ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis">Credit Sale</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $st = $sale['status'];
                                    $badgeClass = 'bg-secondary';
                                    if ($st === 'POSTED') $badgeClass = 'bg-success';
                                    elseif ($st === 'CANCELLED') $badgeClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badgeClass ?> px-3 py-1"><?= htmlspecialchars($st); ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group gap-1">
                                        <a href="<?= \Core\Helper::baseUrl('modules/marketplace/sales/view?id=' . $sale['id']); ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">View</a>
                                        
                                        <?php if ($sale['status'] === 'DRAFT' && \Core\Auth::hasPermission('marketplace.sales.post')): ?>
                                            <form action="<?= \Core\Helper::baseUrl('modules/marketplace/sales/post'); ?>" method="POST" class="d-inline">
                                                <?= \Core\CSRF::getFormField(); ?>
                                                <input type="hidden" name="id" value="<?= $sale['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">Post</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No marketplace invoices recorded.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
