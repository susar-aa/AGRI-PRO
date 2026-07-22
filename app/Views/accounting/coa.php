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
        <h4 class="fw-bold mb-1 text-dark">Chart of Accounts (COA)</h4>
        <p class="text-muted small mb-0">Hierarchical General Ledger account structure for Agri Co-Op ERP.</p>
    </div>
    <div>
        <?php if (\Core\Auth::hasPermission('coa.manage')): ?>
            <button class="btn btn-success rounded-pill px-4 shadow-sm" style="background-color: #1b4332; border-color: #1b4332;" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                <i class="bi bi-plus-lg me-1"></i> Create New Account
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-diagram-3-fill text-success me-2"></i> General Ledger Accounts Hierarchy</h6>
        <div class="d-flex gap-2">
            <span class="badge bg-primary-subtle text-primary border">Asset</span>
            <span class="badge bg-warning-subtle text-warning border text-dark">Liability</span>
            <span class="badge bg-info-subtle text-info border">Equity</span>
            <span class="badge bg-success-subtle text-success border">Revenue</span>
            <span class="badge bg-danger-subtle text-danger border">COGS / Expense</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="coaTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 140px;">Account Code</th>
                        <th>Account Name</th>
                        <th>Category</th>
                        <th>Normal Balance</th>
                        <th class="text-center">Posting</th>
                        <th class="text-center">Type</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    function renderCoaRows(array $branch, int $level = 1) {
                        foreach ($branch as $acc) {
                            $indentClass = "coa-tree-level-{$level}";
                            $badgeClass = match($acc['category']) {
                                'Asset' => 'bg-primary-subtle text-primary border-primary-subtle',
                                'Liability' => 'bg-warning-subtle text-dark border-warning-subtle',
                                'Equity' => 'bg-info-subtle text-info border-info-subtle',
                                'Revenue' => 'bg-success-subtle text-success border-success-subtle',
                                'COGS', 'Expense' => 'bg-danger-subtle text-danger border-danger-subtle',
                                default => 'bg-secondary-subtle text-dark'
                            };
                            ?>
                            <tr class="<?= $indentClass; ?>">
                                <td class="fw-bold font-monospace text-dark"><?= htmlspecialchars($acc['account_code']); ?></td>
                                <td>
                                    <?php if ($level > 1): ?>
                                        <span class="text-muted me-1">↳</span>
                                    <?php endif; ?>
                                    <span class="<?= $acc['allow_manual_posting'] == 0 ? 'fw-bold text-dark' : 'text-body'; ?>">
                                        <?= htmlspecialchars($acc['account_name']); ?>
                                    </span>
                                    <?php if (!empty($acc['description'])): ?>
                                        <small class="text-muted d-block" style="font-size: 0.8rem;"><?= htmlspecialchars($acc['description']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge <?= $badgeClass; ?> px-2 py-1"><?= htmlspecialchars($acc['category']); ?></span></td>
                                <td><span class="text-uppercase small fw-semibold text-muted"><?= htmlspecialchars($acc['normal_balance']); ?></span></td>
                                <td class="text-center">
                                    <?php if ($acc['allow_manual_posting']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Posting</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border">Header</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($acc['is_system']): ?>
                                        <span class="badge bg-dark text-white">System</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">Custom</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($acc['is_active']): ?>
                                        <span class="badge bg-success rounded-pill">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                            if (!empty($acc['children'])) {
                                renderCoaRows($acc['children'], $level + 1);
                            }
                        }
                    }

                    if (!empty($accountsTree)) {
                        renderCoaRows($accountsTree);
                    } else {
                        echo '<tr><td colspan="7" class="text-center text-muted py-4">No Chart of Accounts found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add New Account -->
<?php if (\Core\Auth::hasPermission('coa.manage')): ?>
<div class="modal fade" id="addAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white" style="background-color: #1b4332 !important;">
                <h5 class="modal-title font-weight-bold"><i class="bi bi-plus-circle me-2"></i> Add Chart of Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('accounting/coa/store'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="account_code" class="form-label fw-semibold">Account Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="account_code" name="account_code" placeholder="e.g. 6991" required>
                    </div>

                    <div class="mb-3">
                        <label for="account_name" class="form-label fw-semibold">Account Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="account_name" name="account_name" placeholder="e.g. Organic Compost Expense" required>
                    </div>

                    <div class="mb-3">
                        <label for="account_type_id" class="form-label fw-semibold">Account Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="account_type_id" name="account_type_id" required>
                            <option value="">-- Select Type --</option>
                            <?php foreach ($accountTypes as $type): ?>
                                <option value="<?= $type['id']; ?>"><?= htmlspecialchars($type['name']); ?> (<?= htmlspecialchars($type['category']); ?> - <?= strtoupper($type['normal_balance']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label fw-semibold">Parent Header Account (Optional)</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">-- None (Top Level) --</option>
                            <?php foreach ($flatAccounts as $acc): ?>
                                <option value="<?= $acc['id']; ?>"><?= htmlspecialchars($acc['account_code']); ?> - <?= htmlspecialchars($acc['account_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2" placeholder="Account purpose or detail..."></textarea>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="allow_manual_posting" name="allow_manual_posting" value="1" checked>
                        <label class="form-check-label" for="allow_manual_posting">Allow Manual Direct Posting</label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Account Active</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Save Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
