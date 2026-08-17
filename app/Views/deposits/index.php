<?php if ($flashSuccess = \Core\Session::getFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Bank Deposits History</h4>
        <p class="text-muted small mb-0">Record and track cash and cheque bank deposits to verify bank balances against general ledger accounts.</p>
    </div>
    <div>
        <?php if (\Core\Auth::hasPermission('deposits.create')): ?>
            <a href="<?= \Core\Helper::baseUrl('deposits/create'); ?>" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                <i class="bi bi-plus-lg me-1"></i> Record Bank Deposit
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('deposits'); ?>" method="GET" class="row g-3 small">
            <div class="col-12 col-md-5">
                <label class="form-label fw-semibold">Search Deposits</label>
                <input type="text" class="form-control form-control-sm" name="search" value="<?= htmlspecialchars($filters['search']); ?>" placeholder="Deposit #, Description...">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Filter Bank Account</label>
                <select class="form-select form-select-sm" name="bank_account_id">
                    <option value="">-- All Accounts --</option>
                    <?php foreach ($bankAccounts as $ba): ?>
                        <option value="<?= $ba['id']; ?>" <?= ($filters['bank_account_id'] == $ba['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($ba['bank_name']); ?> - <?= htmlspecialchars($ba['account_number']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">-- All --</option>
                    <option value="DRAFT" <?= ($filters['status'] === 'DRAFT') ? 'selected' : ''; ?>>Draft</option>
                    <option value="DEPOSITED" <?= ($filters['status'] === 'DEPOSITED') ? 'selected' : ''; ?>>Deposited</option>
                    <option value="CANCELLED" <?= ($filters['status'] === 'CANCELLED') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 w-100" style="background-color: #1b4332; border-color: #1b4332;">Filter</button>
                <a href="<?= \Core\Helper::baseUrl('deposits'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 w-100">Reset</a>
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
                        <th>Deposit #</th>
                        <th>Deposit Date</th>
                        <th>Destination Bank Account</th>
                        <th>Description</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($deposits)): ?>
                        <?php foreach ($deposits as $d): ?>
                            <tr>
                                <td class="fw-bold font-monospace">
                                    <a href="<?= \Core\Helper::baseUrl('deposits/view?id=' . $d['id']); ?>" class="text-success text-decoration-none">
                                        <?= htmlspecialchars($d['deposit_number']); ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($d['deposit_date']); ?></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($d['bank_name']); ?></div>
                                    <small class="text-muted font-monospace"><?= htmlspecialchars($d['account_number']); ?></small>
                                </td>
                                <td><?= htmlspecialchars($d['description']); ?></td>
                                <td class="text-end fw-bold text-success"><?= \Core\Helper::formatCurrency($d['total_amount']); ?></td>
                                <td class="text-center">
                                    <?php
                                    $st = $d['status'];
                                    $badgeClass = 'bg-secondary';
                                    if ($st === 'DEPOSITED') $badgeClass = 'bg-success';
                                    elseif ($st === 'CANCELLED') $badgeClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badgeClass ?> px-3 py-1"><?= ucfirst(strtolower($st)); ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= \Core\Helper::baseUrl('deposits/view?id=' . $d['id']); ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No deposits recorded.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
