<?php 
$activeNav = $activeNav ?? 'reports_fixed_deposits';
$fds = $fds ?? [];
$totals = $totals ?? ['total_principal' => 0, 'total_expected_interest' => 0, 'total_maturity_amount' => 0, 'active_count' => 0, 'matured_count' => 0];
$members = $members ?? [];
$filters = $filters ?? [];
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= \Core\Helper::baseUrl('reports'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Hub</a>
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="bi bi-safe text-warning me-2"></i>Fixed Deposits Portfolio Report
                </h4>
            </div>
            <p class="text-muted small mb-0">Member fixed deposit investments, maturity liabilities, interest accruals, and status registry</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-printer"></i> Print Statement
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 d-print-none">
        <div class="card-body p-3">
            <form action="<?= \Core\Helper::baseUrl('reports/fixed-deposits'); ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-muted mb-1">Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">-- All Statuses --</option>
                        <option value="ACTIVE" <?= (($filters['status'] ?? '') === 'ACTIVE') ? 'selected' : ''; ?>>ACTIVE</option>
                        <option value="MATURED" <?= (($filters['status'] ?? '') === 'MATURED') ? 'selected' : ''; ?>>MATURED</option>
                        <option value="CLOSED" <?= (($filters['status'] ?? '') === 'CLOSED') ? 'selected' : ''; ?>>CLOSED</option>
                        <option value="PREMATURELY_CLOSED" <?= (($filters['status'] ?? '') === 'PREMATURELY_CLOSED') ? 'selected' : ''; ?>>PREMATURELY CLOSED</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small font-weight-bold text-muted mb-1">Society Member</label>
                    <select class="form-select form-select-sm" name="member_id">
                        <option value="">-- All Society Members --</option>
                        <?php foreach ($members as $m): ?>
                            <option value="<?= $m['id']; ?>" <?= (($filters['member_id'] ?? '') == $m['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($m['member_no'] . ' - ' . $m['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-warning btn-sm w-100 font-weight-bold text-dark">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="<?= \Core\Helper::baseUrl('reports/fixed-deposits'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-warning rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total FD Principal Invested</div>
                    <div class="h4 font-weight-bold text-dark mb-0">LKR <?= number_format($totals['total_principal'], 2); ?></div>
                    <small class="text-muted">Total deposited principal</small>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-danger rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Interest Payable</div>
                    <div class="h4 font-weight-bold text-danger mb-0">LKR <?= number_format($totals['total_expected_interest'], 2); ?></div>
                    <small class="text-muted">Expected interest obligations</small>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-primary rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Maturity Liability</div>
                    <div class="h4 font-weight-bold text-primary mb-0">LKR <?= number_format($totals['total_maturity_amount'], 2); ?></div>
                    <small class="text-muted">Principal + Interest</small>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Active FDs</div>
                    <div class="h4 font-weight-bold text-success mb-0"><?= number_format($totals['active_count']); ?> Active</div>
                    <small class="text-muted"><?= number_format($totals['matured_count']); ?> Matured (Pending payout)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Fixed Deposit Contracts Registry</h6>
                <small class="text-muted"><?= count($fds); ?> total records found</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 12%;">FD Number</th>
                            <th style="width: 20%;">Member Details</th>
                            <th style="width: 10%;">Start Date</th>
                            <th style="width: 10%;">Maturity Date</th>
                            <th style="width: 8%; text-align: center;">Term</th>
                            <th style="width: 8%; text-align: center;">Rate %</th>
                            <th style="width: 11%; text-align: right;">Principal (LKR)</th>
                            <th style="width: 10%; text-align: right;">Interest (LKR)</th>
                            <th style="width: 11%; text-align: right;">Maturity Amt</th>
                            <th style="width: 10%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($fds)): ?>
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">No fixed deposits found matching criteria.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($fds as $fd): ?>
                                <tr>
                                    <td>
                                        <a href="<?= \Core\Helper::baseUrl('modules/fixed-deposits/view?id=' . $fd['id']); ?>" class="font-monospace fw-bold text-decoration-none">
                                            <?= htmlspecialchars($fd['deposit_number']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($fd['member_name']); ?></div>
                                        <small class="font-monospace text-muted"><?= htmlspecialchars($fd['member_no']); ?> | NIC: <?= htmlspecialchars($fd['nic'] ?? 'N/A'); ?></small>
                                    </td>
                                    <td class="font-monospace text-muted small"><?= htmlspecialchars($fd['start_date']); ?></td>
                                    <td class="font-monospace fw-bold <?= ($fd['days_to_maturity'] <= 30 && $fd['status'] === 'ACTIVE') ? 'text-danger' : 'text-dark'; ?>">
                                        <?= htmlspecialchars($fd['maturity_date']); ?>
                                        <?php if ($fd['days_to_maturity'] <= 30 && $fd['status'] === 'ACTIVE'): ?>
                                            <span class="badge bg-danger small ms-1"><?= $fd['days_to_maturity'] <= 0 ? 'Matured' : $fd['days_to_maturity'] . 'd left'; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center font-monospace"><?= htmlspecialchars($fd['term_months']); ?> m</td>
                                    <td class="text-center font-monospace font-weight-bold text-primary"><?= number_format((float)$fd['interest_rate'], 2); ?>%</td>
                                    <td class="text-end font-monospace font-weight-bold text-dark"><?= number_format((float)$fd['principal_amount'], 2); ?></td>
                                    <td class="text-end font-monospace text-muted"><?= number_format((float)$fd['expected_interest'], 2); ?></td>
                                    <td class="text-end font-monospace font-weight-bold fs-6 text-success"><?= number_format((float)$fd['maturity_amount'], 2); ?></td>
                                    <td>
                                        <?php 
                                        $badgeClass = match($fd['status']) {
                                            'ACTIVE' => 'bg-success',
                                            'MATURED' => 'bg-warning text-dark',
                                            'CLOSED' => 'bg-secondary',
                                            'PREMATURELY_CLOSED' => 'bg-danger',
                                            default => 'bg-light text-dark'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($fd['status']); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-dark font-weight-bold">
                        <tr>
                            <td colspan="6" class="text-uppercase py-3">GRAND TOTALS</td>
                            <td class="text-end font-monospace py-3 text-warning">LKR <?= number_format($totals['total_principal'], 2); ?></td>
                            <td class="text-end font-monospace py-3 text-warning">LKR <?= number_format($totals['total_expected_interest'], 2); ?></td>
                            <td class="text-end font-monospace py-3 text-success">LKR <?= number_format($totals['total_maturity_amount'], 2); ?></td>
                            <td class="py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
