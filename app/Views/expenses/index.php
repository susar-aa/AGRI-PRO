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

<?php if (isset($_GET['source_module']) && $_GET['source_module'] === 'MACHINERY'): ?>
    <div class="mb-3">
        <a href="<?= \Core\Helper::baseUrl('operations/machinery'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Back to Machinery Renting
        </a>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Central Expense Ledger</h4>
        <p class="text-muted small mb-0">Record, track, and manage all operating and credit expenses across business modules.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= \Core\Helper::baseUrl('expenses/reports'); ?>" class="btn btn-outline-success rounded-pill px-3">
            <i class="bi bi-bar-chart-line-fill me-1"></i> Reports & Analytics
        </a>
        <?php if (\Core\Auth::hasPermission('expenses.create')): ?>
            <a href="<?= \Core\Helper::baseUrl('expenses/create' . (isset($_GET['source_module']) ? '?source_module=' . urlencode($_GET['source_module']) : '')); ?>" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                <i class="bi bi-plus-lg me-1"></i> Record Expense
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('expenses'); ?>" method="GET" class="row g-3 small">
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label fw-semibold">Expense #</label>
                <input type="text" class="form-control form-control-sm" name="expense_number" value="<?= htmlspecialchars($filters['expense_number']); ?>" placeholder="EXP-YYYY-...">
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label fw-semibold">Category</label>
                <select class="form-select form-select-sm" name="category_id">
                    <option value="">-- All --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id']; ?>" <?= ($filters['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label fw-semibold">Cost Center</label>
                <select class="form-select form-select-sm" name="cost_center_id">
                    <option value="">-- All --</option>
                    <?php foreach ($costCenters as $cc): ?>
                        <option value="<?= $cc['id']; ?>" <?= ($filters['cost_center_id'] == $cc['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($cc['code']); ?> - <?= htmlspecialchars($cc['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label fw-semibold">Payment Method</label>
                <select class="form-select form-select-sm" name="payment_method">
                    <option value="">-- All --</option>
                    <option value="Cash" <?= ($filters['payment_method'] === 'Cash') ? 'selected' : ''; ?>>Cash</option>
                    <option value="Bank Transfer" <?= ($filters['payment_method'] === 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                    <option value="Cheque" <?= ($filters['payment_method'] === 'Cheque') ? 'selected' : ''; ?>>Cheque</option>
                    <option value="Card" <?= ($filters['payment_method'] === 'Card') ? 'selected' : ''; ?>>Card</option>
                    <option value="Online Payment" <?= ($filters['payment_method'] === 'Online Payment') ? 'selected' : ''; ?>>Online Payment</option>
                    <option value="Credit" <?= ($filters['payment_method'] === 'Credit') ? 'selected' : ''; ?>>Credit (Pay Later)</option>
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label fw-semibold">Source Module</label>
                <select class="form-select form-select-sm" name="source_module">
                    <option value="">-- All --</option>
                    <option value="GENERAL" <?= ($filters['source_module'] === 'GENERAL') ? 'selected' : ''; ?>>General</option>
                    <option value="PLANTATION" <?= ($filters['source_module'] === 'PLANTATION') ? 'selected' : ''; ?>>Plantation</option>
                    <option value="CONSTRUCTION" <?= ($filters['source_module'] === 'CONSTRUCTION') ? 'selected' : ''; ?>>Construction</option>
                    <option value="GRINDING_MILL" <?= ($filters['source_module'] === 'GRINDING_MILL') ? 'selected' : ''; ?>>Grinding Mill</option>
                    <option value="MARKETPLACE" <?= ($filters['source_module'] === 'MARKETPLACE') ? 'selected' : ''; ?>>Marketplace</option>
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">-- All --</option>
                    <option value="draft" <?= ($filters['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                    <option value="pending_approval" <?= ($filters['status'] === 'pending_approval') ? 'selected' : ''; ?>>Pending Approval</option>
                    <option value="approved" <?= ($filters['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                    <option value="posted" <?= ($filters['status'] === 'posted') ? 'selected' : ''; ?>>Posted</option>
                    <option value="reversed" <?= ($filters['status'] === 'reversed') ? 'selected' : ''; ?>>Reversed</option>
                    <option value="cancelled" <?= ($filters['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label fw-semibold">From Date</label>
                <input type="date" class="form-control form-control-sm" name="date_from" value="<?= htmlspecialchars($filters['date_from']); ?>">
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label fw-semibold">To Date</label>
                <input type="date" class="form-control form-control-sm" name="date_to" value="<?= htmlspecialchars($filters['date_to']); ?>">
            </div>
            <div class="col-12 col-md-4 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill" style="background-color: #1b4332; border-color: #1b4332;">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="<?= \Core\Helper::baseUrl('expenses'); ?>" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">Reset</a>
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
                        <th>Expense #</th>
                        <th>Date</th>
                        <th>Payee</th>
                        <th>Category</th>
                        <th>Cost Center</th>
                        <th class="text-end">Amount</th>
                        <th>Payment</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($expenses)): ?>
                        <?php foreach ($expenses as $exp): ?>
                            <tr>
                                <td class="fw-bold font-monospace">
                                    <a href="<?= \Core\Helper::baseUrl('expenses/view?id=' . $exp['id']); ?>" class="text-success text-decoration-none">
                                        <?= htmlspecialchars($exp['expense_number']); ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($exp['expense_date']); ?></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($exp['payee']); ?></div>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;"><?= htmlspecialchars($exp['description']); ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($exp['category_name']); ?></span></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($exp['cost_center_name']); ?></span></td>
                                <td class="text-end fw-bold text-dark"><?= \Core\Helper::formatCurrency($exp['amount']); ?></td>
                                <td>
                                    <span class="small fw-semibold"><?= htmlspecialchars($exp['payment_method']); ?></span>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $st = $exp['status'];
                                    $badgeClass = 'bg-secondary';
                                    if ($st === 'posted') $badgeClass = 'bg-success';
                                    elseif ($st === 'pending_approval') $badgeClass = 'bg-warning text-dark';
                                    elseif ($st === 'approved') $badgeClass = 'bg-info text-dark';
                                    elseif ($st === 'reversed') $badgeClass = 'bg-danger';
                                    elseif ($st === 'cancelled') $badgeClass = 'bg-dark';
                                    ?>
                                    <span class="badge <?= $badgeClass ?> px-3 py-1"><?= ucfirst(str_replace('_', ' ', $st)) ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= \Core\Helper::baseUrl('expenses/view?id=' . $exp['id']); ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                        <i class="bi bi-eye-fill"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No expense records found.</td>
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
