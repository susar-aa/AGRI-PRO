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

<!-- Summary KPI Stats Section -->
<div class="row g-3 mb-4">
    <!-- Active FDs -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-success text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-white-50 small fw-semibold text-uppercase">Active FDs</span>
                    <h4 class="fw-bold mb-0 mt-1"><?= $stats['active_count']; ?> Deposits</h4>
                </div>
                <div class="fs-2"><i class="bi bi-wallet2"></i></div>
            </div>
        </div>
    </div>
    <!-- Total Principal -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total FD Principal</span>
                    <h4 class="fw-bold text-dark mb-0 mt-1">LKR <?= number_format($stats['total_principal'], 2); ?></h4>
                </div>
                <div class="fs-2 text-success"><i class="bi bi-safe"></i></div>
            </div>
        </div>
    </div>
    <!-- Expected Interest -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Interest Payable</span>
                    <h4 class="fw-bold text-dark mb-0 mt-1">LKR <?= number_format($stats['total_interest'], 2); ?></h4>
                </div>
                <div class="fs-2 text-primary"><i class="bi bi-percent"></i></div>
            </div>
        </div>
    </div>
    <!-- Outstanding Matured -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Matured Pending Payout</span>
                    <h4 class="fw-bold text-danger mb-0 mt-1"><?= $stats['matured_count']; ?> Deposits</h4>
                </div>
                <div class="fs-2 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Fixed Deposits Overview</h4>
        <p class="text-muted small mb-0">Record investments, verify automatic date calculations, and process matured payouts.</p>
    </div>
    <div>
        <a href="<?= \Core\Helper::baseUrl('modules/fixed-deposits/create'); ?>" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
            <i class="bi bi-plus-lg me-1"></i> Create Fixed Deposit
        </a>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= \Core\Helper::baseUrl('modules/fixed-deposits'); ?>" class="row g-3 small">
            <div class="col-md-5">
                <label class="form-label fw-semibold">Filter Member</label>
                <select class="form-select form-select-sm" name="member_id">
                    <option value="">-- All Members --</option>
                    <?php foreach ($members as $m): ?>
                        <option value="<?= $m['id']; ?>" <?= ($filters['member_id'] == $m['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($m['full_name']); ?> (<?= htmlspecialchars($m['membership_no']); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">-- All Statuses --</option>
                    <option value="ACTIVE" <?= $filters['status'] === 'ACTIVE' ? 'selected' : ''; ?>>Active</option>
                    <option value="MATURED" <?= $filters['status'] === 'MATURED' ? 'selected' : ''; ?>>Matured</option>
                    <option value="CLOSED" <?= $filters['status'] === 'CLOSED' ? 'selected' : ''; ?>>Closed</option>
                    <option value="PREMATURELY_CLOSED" <?= $filters['status'] === 'PREMATURELY_CLOSED' ? 'selected' : ''; ?>>Prematurely Closed</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill" style="background-color: #1b4332; border-color: #1b4332;">Search</button>
                <a href="<?= \Core\Helper::baseUrl('modules/fixed-deposits'); ?>" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Grid List Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>FD Number</th>
                        <th>Member Details</th>
                        <th>Start Date</th>
                        <th>Maturity Date</th>
                        <th class="text-end">Principal</th>
                        <th class="text-center">Rate</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($fds)): ?>
                        <?php foreach ($fds as $fd): ?>
                            <tr>
                                <td class="fw-bold font-monospace text-success"><?= htmlspecialchars($fd['deposit_number']); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($fd['member_name']); ?></div>
                                    <small class="text-muted font-monospace"><?= htmlspecialchars($fd['membership_no']); ?></small>
                                </td>
                                <td><?= htmlspecialchars($fd['start_date']); ?></td>
                                <td class="<?= $fd['status'] === 'MATURED' ? 'fw-bold text-danger' : ''; ?>"><?= htmlspecialchars($fd['maturity_date']); ?></td>
                                <td class="text-end fw-bold font-monospace">LKR <?= number_format($fd['maturity_amount'] - $fd['expected_interest'], 2); ?></td>
                                <td class="text-center fw-semibold"><?= htmlspecialchars($fd['interest_rate']); ?>%</td>
                                <td class="text-center">
                                    <?php
                                    $st = $fd['status'];
                                    $badge = 'bg-success';
                                    if ($st === 'MATURED') $badge = 'bg-danger animate-pulse';
                                    elseif ($st === 'CLOSED') $badge = 'bg-secondary';
                                    elseif ($st === 'PREMATURELY_CLOSED') $badge = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?= $badge; ?> rounded-pill px-3 py-1"><?= htmlspecialchars($st); ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= \Core\Helper::baseUrl('modules/fixed-deposits/view?id=' . $fd['id']); ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">View Receipt</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No Fixed Deposits found matching the search criteria.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}
.animate-pulse {
    animation: pulse 1.5s infinite;
}
</style>
