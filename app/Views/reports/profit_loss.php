<?php 
$activeNav = $activeNav ?? 'reports_pnl';
$pnl = $pnl ?? ['revenues' => [], 'total_revenue' => 0, 'cogs' => [], 'total_cogs' => 0, 'gross_profit' => 0, 'expenses' => [], 'total_expenses' => 0, 'net_profit' => 0];
$costCenters = $costCenters ?? [];
$fromDate = $fromDate ?? date('Y-01-01');
$toDate = $toDate ?? date('Y-m-d');
$selectedCostCenterId = $selectedCostCenterId ?? null;
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= \Core\Helper::baseUrl('reports'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Hub</a>
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="bi bi-graph-up text-success me-2"></i>Profit & Loss Statement
                </h4>
            </div>
            <p class="text-muted small mb-0">Income Statement: Operating Revenues, Cost of Goods Sold, and Operating Expenses</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= \Core\Helper::baseUrl('reports/export?type=profit_loss&from_date=' . urlencode($fromDate) . '&to_date=' . urlencode($toDate) . ($selectedCostCenterId ? '&cost_center_id=' . $selectedCostCenterId : '')); ?>" 
               class="btn btn-outline-success btn-sm font-weight-bold shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-printer"></i> Print Statement
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 d-print-none">
        <div class="card-body p-3">
            <form method="GET" action="<?= \Core\Helper::baseUrl('reports/profit-loss'); ?>" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">From Date</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" value="<?= htmlspecialchars($fromDate); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">To Date</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" value="<?= htmlspecialchars($toDate); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-muted mb-1">Cost Center / Division</label>
                    <select name="cost_center_id" class="form-select form-select-sm">
                        <option value="">-- All Divisions / Entire Society --</option>
                        <?php foreach ($costCenters as $cc): ?>
                            <option value="<?= $cc['id']; ?>" <?= ($selectedCostCenterId == $cc['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cc['code'] . ' - ' . $cc['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-sm w-100 font-weight-bold">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="<?= \Core\Helper::baseUrl('reports/profit-loss'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Highlights Bar -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Revenue</div>
                    <div class="h4 font-weight-bold text-success mb-0">LKR <?= number_format($pnl['total_revenue'], 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-warning rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Cost of Goods Sold</div>
                    <div class="h4 font-weight-bold text-warning mb-0">LKR <?= number_format($pnl['total_cogs'], 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-primary rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Gross Profit</div>
                    <div class="h4 font-weight-bold text-primary mb-0">LKR <?= number_format($pnl['gross_profit'], 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 <?= $pnl['net_profit'] >= 0 ? 'border-success' : 'border-danger'; ?> rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Net Operating Surplus</div>
                    <div class="h4 font-weight-bold <?= $pnl['net_profit'] >= 0 ? 'text-success' : 'text-danger'; ?> mb-0">
                        LKR <?= number_format($pnl['net_profit'], 2); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statement Document Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Statement of Profit & Loss</h6>
                <small class="text-muted">Period: <?= htmlspecialchars($fromDate); ?> to <?= htmlspecialchars($toDate); ?></small>
            </div>
            <span class="badge bg-light text-dark border">Currency: Sri Lankan Rupee (LKR)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">Account Code</th>
                            <th style="width: 55%;">Account Particulars</th>
                            <th style="width: 15%; text-align: right;">Detail (LKR)</th>
                            <th style="width: 15%; text-align: right;">Total (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1. OPERATING REVENUES -->
                        <tr class="table-light font-weight-bold">
                            <td colspan="4" class="text-uppercase text-success py-2">
                                <i class="bi bi-plus-circle me-1"></i> Operating Revenues & Income
                            </td>
                        </tr>
                        <?php if (empty($pnl['revenues'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">No revenue recorded in this period.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pnl['revenues'] as $row): ?>
                                <tr>
                                    <td class="font-monospace text-muted small ps-4"><?= htmlspecialchars($row['account_code']); ?></td>
                                    <td><?= htmlspecialchars($row['account_name']); ?></td>
                                    <td class="text-end font-monospace"><?= number_format($row['net_amount'], 2); ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tr class="bg-success bg-opacity-10 font-weight-bold">
                            <td colspan="3" class="ps-4">TOTAL OPERATING REVENUE (A)</td>
                            <td class="text-end font-monospace text-success"><?= number_format($pnl['total_revenue'], 2); ?></td>
                        </tr>

                        <!-- 2. COST OF GOODS SOLD -->
                        <tr class="table-light font-weight-bold">
                            <td colspan="4" class="text-uppercase text-warning text-dark py-2">
                                <i class="bi bi-dash-circle me-1"></i> Cost of Goods Sold (COGS)
                            </td>
                        </tr>
                        <?php if (empty($pnl['cogs'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-2 text-muted small">No direct COGS recorded in this period.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pnl['cogs'] as $row): ?>
                                <tr>
                                    <td class="font-monospace text-muted small ps-4"><?= htmlspecialchars($row['account_code']); ?></td>
                                    <td><?= htmlspecialchars($row['account_name']); ?></td>
                                    <td class="text-end font-monospace"><?= number_format($row['net_amount'], 2); ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tr class="bg-warning bg-opacity-10 font-weight-bold">
                            <td colspan="3" class="ps-4">TOTAL COST OF GOODS SOLD (B)</td>
                            <td class="text-end font-monospace text-warning text-dark"><?= number_format($pnl['total_cogs'], 2); ?></td>
                        </tr>

                        <!-- GROSS PROFIT -->
                        <tr class="bg-primary bg-opacity-10 font-weight-bold" style="border-top: 2px solid #0d6efd; border-bottom: 2px solid #0d6efd;">
                            <td colspan="3" class="fs-6">GROSS PROFIT / (LOSS) (A - B)</td>
                            <td class="text-end font-monospace fs-6 text-primary"><?= number_format($pnl['gross_profit'], 2); ?></td>
                        </tr>

                        <!-- 3. OPERATING EXPENSES -->
                        <tr class="table-light font-weight-bold">
                            <td colspan="4" class="text-uppercase text-danger py-2">
                                <i class="bi bi-dash-circle me-1"></i> Operating & Administrative Expenses
                            </td>
                        </tr>
                        <?php if (empty($pnl['expenses'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-2 text-muted small">No operating expenses recorded in this period.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pnl['expenses'] as $row): ?>
                                <tr>
                                    <td class="font-monospace text-muted small ps-4"><?= htmlspecialchars($row['account_code']); ?></td>
                                    <td><?= htmlspecialchars($row['account_name']); ?></td>
                                    <td class="text-end font-monospace"><?= number_format($row['net_amount'], 2); ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tr class="bg-danger bg-opacity-10 font-weight-bold">
                            <td colspan="3" class="ps-4">TOTAL OPERATING EXPENSES (C)</td>
                            <td class="text-end font-monospace text-danger"><?= number_format($pnl['total_expenses'], 2); ?></td>
                        </tr>

                        <!-- NET SURPLUS -->
                        <tr class="<?= $pnl['net_profit'] >= 0 ? 'bg-success' : 'bg-danger'; ?> bg-opacity-25 font-weight-bold" style="border-top: 3px double #333; border-bottom: 3px double #333;">
                            <td colspan="3" class="py-3 fs-5">
                                NET OPERATING <?= $pnl['net_profit'] >= 0 ? 'SURPLUS' : 'DEFICIT'; ?> FOR THE PERIOD (Gross Profit - Expenses)
                            </td>
                            <td class="text-end font-monospace py-3 fs-5 <?= $pnl['net_profit'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <?= number_format($pnl['net_profit'], 2); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white text-muted small d-flex justify-content-between">
            <span>Generated on: <?= date('Y-m-d H:i:s'); ?> | System Engine: Agri Co-Op ERP</span>
            <span>All amounts certified in accordance with General Cooperative Accounting Standards</span>
        </div>
    </div>
</div>
