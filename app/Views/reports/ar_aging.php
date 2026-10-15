<?php 
$activeNav = $activeNav ?? 'reports_ar_aging';
$records = $records ?? [];
$totals = $totals ?? ['total_receivable' => 0, 'current_0_30' => 0, 'days_31_60' => 0, 'days_61_90' => 0, 'days_over_90' => 0];
$filters = $filters ?? [];
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= \Core\Helper::baseUrl('reports'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Hub</a>
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="bi bi-clock-history text-danger me-2"></i>Accounts Receivable (AR) Aging Report
                </h4>
            </div>
            <p class="text-muted small mb-0">Customer credit outstanding breakdown across 30, 60, 90, and 90+ day aging brackets</p>
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
            <form action="<?= \Core\Helper::baseUrl('reports/ar-aging'); ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-md-9">
                    <label class="form-label small font-weight-bold text-muted mb-1">Search Customer Name / Code / Phone</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Type to filter customer..." value="<?= htmlspecialchars($filters['search'] ?? ''); ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-danger btn-sm w-100 font-weight-bold">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                    <a href="<?= \Core\Helper::baseUrl('reports/ar-aging'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Aging Bracket Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-danger rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Receivable</div>
                    <div class="h4 font-weight-bold text-danger mb-0">LKR <?= number_format($totals['total_receivable'], 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Current (0 - 30 Days)</div>
                    <div class="h5 font-weight-bold text-success mb-0">LKR <?= number_format($totals['current_0_30'], 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-warning rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">31 - 60 Days</div>
                    <div class="h5 font-weight-bold text-warning mb-0">LKR <?= number_format($totals['days_31_60'], 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-orange rounded-3" style="border-left-color: #fd7e14 !important;">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">61 - 90 Days</div>
                    <div class="h5 font-weight-bold text-dark mb-0">LKR <?= number_format($totals['days_61_90'], 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-dark rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Over 90 Days (Overdue)</div>
                    <div class="h5 font-weight-bold text-danger mb-0">LKR <?= number_format($totals['days_over_90'], 2); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aging Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Customer Aging Analysis Schedule</h6>
                <small class="text-muted"><?= count($records); ?> customer accounts with outstanding balances</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 12%;">Customer Code</th>
                            <th style="width: 28%;">Customer Name</th>
                            <th style="width: 12%; text-align: right;">Current (0-30 d)</th>
                            <th style="width: 12%; text-align: right;">31-60 Days</th>
                            <th style="width: 12%; text-align: right;">61-90 Days</th>
                            <th style="width: 12%; text-align: right;">Over 90 Days</th>
                            <th style="width: 12%; text-align: right;">Total Due (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No outstanding credit balances found. All customer accounts are settled!</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($records as $r): ?>
                                <tr>
                                    <td class="font-monospace text-muted small"><?= htmlspecialchars($r['party_code']); ?></td>
                                    <td>
                                        <a href="<?= \Core\Helper::baseUrl('parties/view?id=' . $r['party_id']); ?>" class="fw-bold text-dark text-decoration-none">
                                            <?= htmlspecialchars($r['customer_name']); ?>
                                        </a>
                                        <?php if (!empty($r['phone'])): ?>
                                            <div class="text-muted small"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($r['phone']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end font-monospace"><?= (float)$r['current_0_30'] > 0 ? number_format((float)$r['current_0_30'], 2) : '-'; ?></td>
                                    <td class="text-end font-monospace text-warning"><?= (float)$r['days_31_60'] > 0 ? number_format((float)$r['days_31_60'], 2) : '-'; ?></td>
                                    <td class="text-end font-monospace text-danger"><?= (float)$r['days_61_90'] > 0 ? number_format((float)$r['days_61_90'], 2) : '-'; ?></td>
                                    <td class="text-end font-monospace font-weight-bold text-danger"><?= (float)$r['days_over_90'] > 0 ? number_format((float)$r['days_over_90'], 2) : '-'; ?></td>
                                    <td class="text-end font-monospace font-weight-bold fs-6 text-dark"><?= number_format((float)$r['total_receivable'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-dark font-weight-bold">
                        <tr>
                            <td colspan="2" class="text-uppercase py-3">PORTFOLIO TOTALS</td>
                            <td class="text-end font-monospace text-success py-3">LKR <?= number_format($totals['current_0_30'], 2); ?></td>
                            <td class="text-end font-monospace text-warning py-3">LKR <?= number_format($totals['days_31_60'], 2); ?></td>
                            <td class="text-end font-monospace text-danger py-3">LKR <?= number_format($totals['days_61_90'], 2); ?></td>
                            <td class="text-end font-monospace text-danger py-3">LKR <?= number_format($totals['days_over_90'], 2); ?></td>
                            <td class="text-end font-monospace py-3 text-warning">LKR <?= number_format($totals['total_receivable'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
