<?php 
$activeNav = $activeNav ?? 'reports_pnl';
$pnl = $pnl ?? ['revenues' => [], 'total_revenue' => 0, 'cogs' => [], 'total_cogs' => 0, 'gross_profit' => 0, 'expenses' => [], 'total_expenses' => 0, 'net_profit' => 0];
$costCenters = $costCenters ?? [];
$selectedMonth = $selectedMonth ?? date('Y-m');
$fromDate = $fromDate ?? date('Y-01-01');
$toDate = $toDate ?? date('Y-m-d');
$selectedCostCenterId = $selectedCostCenterId ?? null;
?>

<div class="content-wrapper p-4">
    <!-- Header Section (Screen Only) -->
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
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
            <a href="<?= \Core\Helper::baseUrl('reports/print-preview?' . http_build_query(array_merge($_GET, ['type' => 'profit_loss']))); ?>" 
               target="_blank" 
               class="btn btn-dark btn-sm shadow-sm d-flex align-items-center gap-1 font-weight-bold">
                <i class="bi bi-printer-fill text-warning"></i> Print Preview
            </a>
            <a href="<?= \Core\Helper::baseUrl('reports/export?' . http_build_query(array_merge($_GET, ['type' => 'profit_loss']))); ?>" 
               class="btn btn-outline-success btn-sm shadow-sm d-flex align-items-center gap-1 font-weight-bold">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-printer"></i> Quick Print
            </button>
        </div>
    </div>

    <!-- Official Cooperative Letterhead Header (Visible in Direct Browser Print) -->
    <div class="d-none d-print-block mb-3 border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="mb-0 fw-bold" style="color:#15803d;">සීමා සහිත ඇග්රි කෝප් සමූපකාර සමිතිය</h5>
                <h6 class="mb-0 fw-bold text-dark">AGRI CO-OP COOPERATIVE SOCIETY LIMITED</h6>
                <small class="text-muted">Reg: KE/1027 | Miduma, Yatagama, Rambukkana | Tel: 075 377 0 145</small>
            </div>
            <div class="text-end">
                <span class="badge bg-dark text-white p-2">PROFIT & LOSS STATEMENT</span>
                <div class="small fw-bold text-muted mt-1">Period: <?= htmlspecialchars($fromDate); ?> to <?= htmlspecialchars($toDate); ?></div>
            </div>
        </div>
    </div>

    <!-- Filter Card (Screen Only) -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 d-print-none">
        <div class="card-body p-3">
            <form id="filterForm" method="GET" action="<?= \Core\Helper::baseUrl('reports/profit-loss'); ?>" class="row g-2 align-items-end">
                <!-- Direct Month Selector -->
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-success mb-1">
                        <i class="bi bi-calendar-month text-success me-1"></i>Select Month Directly
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="month" class="form-control" name="month" id="filter_month" 
                               value="<?= htmlspecialchars($selectedMonth); ?>" 
                               onchange="applyMonthFilter(this.value)">
                        <button class="btn btn-outline-secondary" type="button" onclick="setQuickMonth('current')" title="Current Month">This</button>
                        <button class="btn btn-outline-secondary" type="button" onclick="setQuickMonth('last')" title="Last Month">Last</button>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted mb-1">From Date</label>
                    <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" value="<?= htmlspecialchars($fromDate); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted mb-1">To Date</label>
                    <input type="date" name="to_date" id="to_date" class="form-control form-control-sm" value="<?= htmlspecialchars($toDate); ?>">
                </div>
                <div class="col-md-3">
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

    <!-- KPI Highlights Bar (Screen Only) -->
    <div class="row g-3 mb-4 d-print-none">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Operating Revenue</div>
                    <div class="h4 font-weight-bold text-success mb-0">LKR <?= number_format($pnl['total_revenue'], 2); ?></div>
                    <small class="text-muted">Inflow from sales & services</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-danger rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Cost of Goods Sold (COGS)</div>
                    <div class="h4 font-weight-bold text-danger mb-0">LKR <?= number_format($pnl['total_cogs'], 2); ?></div>
                    <small class="text-muted">Direct procurement & costs</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-primary rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Gross Operating Profit</div>
                    <div class="h4 font-weight-bold text-primary mb-0">LKR <?= number_format($pnl['gross_profit'], 2); ?></div>
                    <small class="text-muted">Revenue minus COGS</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 <?= $pnl['net_profit'] >= 0 ? 'border-success' : 'border-danger'; ?> rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Net Surplus / (Deficit)</div>
                    <div class="h4 font-weight-bold <?= $pnl['net_profit'] >= 0 ? 'text-success' : 'text-danger'; ?> mb-0">
                        LKR <?= number_format($pnl['net_profit'], 2); ?>
                    </div>
                    <small class="text-muted">After all operating overheads</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statement Table Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center d-print-none">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Statement of Financial Performance</h6>
                <small class="text-muted">Reporting period: <?= htmlspecialchars($fromDate); ?> to <?= htmlspecialchars($toDate); ?></small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="width: 100%; table-layout: auto;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">Account Code</th>
                            <th style="width: 55%;">Account Classification / Title</th>
                            <th style="width: 15%; text-align: right;">Detail (LKR)</th>
                            <th style="width: 15%; text-align: right;">Total (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1. OPERATING REVENUE -->
                        <tr class="table-light font-weight-bold">
                            <td colspan="4" class="text-uppercase text-success py-2">
                                <i class="bi bi-plus-circle me-1"></i> Operating Revenues & Sales
                            </td>
                        </tr>
                        <?php if (empty($pnl['revenues'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-2 text-muted small">No revenue transactions recorded in this period.</td>
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
                            <td colspan="4" class="text-uppercase text-danger py-2">
                                <i class="bi bi-dash-circle me-1"></i> Cost of Goods Sold (COGS)
                            </td>
                        </tr>
                        <?php if (empty($pnl['cogs'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-2 text-muted small">No direct inventory costs recorded in this period.</td>
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
                        <tr class="bg-danger bg-opacity-10 font-weight-bold">
                            <td colspan="3" class="ps-4">TOTAL COST OF GOODS SOLD (B)</td>
                            <td class="text-end font-monospace text-danger"><?= number_format($pnl['total_cogs'], 2); ?></td>
                        </tr>

                        <!-- GROSS PROFIT -->
                        <tr class="table-primary font-weight-bold border-top border-bottom border-2">
                            <td colspan="3" class="py-2 fs-6">GROSS OPERATING PROFIT / (LOSS) (A - B)</td>
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
        <div class="card-footer bg-white text-muted small d-flex justify-content-between d-print-none">
            <span>Generated on: <?= date('Y-m-d H:i:s'); ?> | System Engine: Agri Co-Op ERP</span>
            <span>All amounts certified in accordance with General Cooperative Accounting Standards</span>
        </div>
    </div>

    <!-- Printable Signature Footer (Visible Only in Print) -->
    <div class="d-none d-print-block mt-4 pt-4 border-top">
        <div class="row text-center" style="font-size: 10px;">
            <div class="col-4">
                <div style="border-top: 1px dotted #333; width: 80%; margin: 30px auto 4px auto;"></div>
                <strong>Prepared By</strong><br><span class="text-muted">Accounts Officer</span>
            </div>
            <div class="col-4">
                <div style="border-top: 1px dotted #333; width: 80%; margin: 30px auto 4px auto;"></div>
                <strong>Checked & Verified</strong><br><span class="text-muted">Internal Auditor / Secretary</span>
            </div>
            <div class="col-4">
                <div style="border-top: 1px dotted #333; width: 80%; margin: 30px auto 4px auto;"></div>
                <strong>Approved By</strong><br><span class="text-muted">General Manager / President</span>
            </div>
        </div>
    </div>
</div>

<script>
function applyMonthFilter(monthVal) {
    if (!monthVal) return;
    const parts = monthVal.split('-');
    const year = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10);
    const lastDay = new Date(year, month, 0).getDate();
    
    document.getElementById('from_date').value = `${monthVal}-01`;
    document.getElementById('to_date').value = `${monthVal}-${String(lastDay).padStart(2, '0')}`;
    document.getElementById('filterForm').submit();
}

function setQuickMonth(period) {
    const now = new Date();
    let year = now.getFullYear();
    let month = now.getMonth() + 1;
    if (period === 'last') {
        month -= 1;
        if (month === 0) {
            month = 12;
            year -= 1;
        }
    }
    const mStr = `${year}-${String(month).padStart(2, '0')}`;
    const mInput = document.getElementById('filter_month');
    if (mInput) {
        mInput.value = mStr;
        applyMonthFilter(mStr);
    }
}
</script>
