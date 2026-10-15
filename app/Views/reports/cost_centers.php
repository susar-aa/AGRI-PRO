<?php 
$activeNav = $activeNav ?? 'reports_cost_centers';
$divisions = $divisions ?? [];
$totals = $totals ?? ['total_revenue' => 0, 'total_expense' => 0, 'net_margin' => 0];
$fromDate = $fromDate ?? date('Y-01-01');
$toDate = $toDate ?? date('Y-m-d');
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= \Core\Helper::baseUrl('reports'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Hub</a>
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="bi bi-building text-info me-2"></i>Cost Centers & Operational Divisions Performance
                </h4>
            </div>
            <p class="text-muted small mb-0">Divisional profitability analysis across Plantation, Brick, Machinery, Marketplace, Fruit Packing, and Contracts</p>
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
            <form action="<?= \Core\Helper::baseUrl('reports/cost-centers'); ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-muted mb-1">From Date</label>
                    <input type="date" class="form-control form-control-sm" name="from_date" value="<?= htmlspecialchars($fromDate); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-muted mb-1">To Date</label>
                    <input type="date" class="form-control form-control-sm" name="to_date" value="<?= htmlspecialchars($toDate); ?>">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-info btn-sm w-100 font-weight-bold text-white">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="<?= \Core\Helper::baseUrl('reports/cost-centers'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary KPI Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Operational Revenue</div>
                    <div class="h4 font-weight-bold text-success mb-0">LKR <?= number_format($totals['total_revenue'], 2); ?></div>
                    <small class="text-muted">Combined divisional income</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-danger rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Operational Expenses</div>
                    <div class="h4 font-weight-bold text-danger mb-0">LKR <?= number_format($totals['total_expense'], 2); ?></div>
                    <small class="text-muted">Direct costs & overhead allocations</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 <?= $totals['net_margin'] >= 0 ? 'border-primary' : 'border-danger'; ?> rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Net Operational Margin</div>
                    <div class="h4 font-weight-bold <?= $totals['net_margin'] >= 0 ? 'text-primary' : 'text-danger'; ?> mb-0">
                        LKR <?= number_format($totals['net_margin'], 2); ?>
                    </div>
                    <small class="text-muted">Net surplus across operations</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Divisions Table Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Divisional Contribution Schedule</h6>
                <small class="text-muted">Period: <?= htmlspecialchars($fromDate); ?> to <?= htmlspecialchars($toDate); ?></small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 14%;">Division Code</th>
                            <th style="width: 32%;">Operational Division / Cost Center</th>
                            <th style="width: 18%; text-align: right;">Total Revenue (LKR)</th>
                            <th style="width: 18%; text-align: right;">Total Expense (LKR)</th>
                            <th style="width: 18%; text-align: right;">Net Margin (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($divisions)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No operational cost centers found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($divisions as $d): ?>
                                <tr>
                                    <td class="font-monospace text-muted small"><?= htmlspecialchars($d['code']); ?></td>
                                    <td class="fw-bold text-dark">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-circle-fill text-muted" style="font-size: 0.5rem;"></i>
                                            <?= htmlspecialchars($d['name']); ?>
                                        </div>
                                    </td>
                                    <td class="text-end font-monospace text-success font-weight-bold">
                                        <?= (float)$d['total_revenue'] > 0 ? number_format((float)$d['total_revenue'], 2) : '-'; ?>
                                    </td>
                                    <td class="text-end font-monospace text-danger">
                                        <?= (float)$d['total_expense'] > 0 ? number_format((float)$d['total_expense'], 2) : '-'; ?>
                                    </td>
                                    <td class="text-end font-monospace font-weight-bold fs-6 <?= (float)$d['net_margin'] >= 0 ? 'text-primary' : 'text-danger'; ?>">
                                        <?= number_format((float)$d['net_margin'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-dark font-weight-bold">
                        <tr>
                            <td colspan="2" class="text-uppercase py-3">PORTFOLIO TOTALS</td>
                            <td class="text-end font-monospace text-success py-3">LKR <?= number_format($totals['total_revenue'], 2); ?></td>
                            <td class="text-end font-monospace text-danger py-3">LKR <?= number_format($totals['total_expense'], 2); ?></td>
                            <td class="text-end font-monospace text-warning py-3">LKR <?= number_format($totals['net_margin'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
