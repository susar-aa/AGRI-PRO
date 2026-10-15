<?php 
$activeNav = $activeNav ?? 'reports_expenses';
$expenseData = $expenseData ?? ['rows' => [], 'total_amount' => 0];
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
                    <i class="bi bi-pie-chart text-info me-2"></i>Operating Expense Analysis & Distribution
                </h4>
            </div>
            <p class="text-muted small mb-0">Cost center distribution, category breakdowns, and overhead expenditure analysis</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= \Core\Helper::baseUrl('reports/print-preview?' . http_build_query(array_merge($_GET, ['type' => 'expenses']))); ?>" 
               target="_blank" 
               class="btn btn-dark btn-sm shadow-sm d-flex align-items-center gap-1 font-weight-bold">
                <i class="bi bi-printer-fill text-warning"></i> Print Preview
            </a>
            <a href="<?= \Core\Helper::baseUrl('reports/export?' . http_build_query(array_merge($_GET, ['type' => 'expenses']))); ?>" 
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
                <span class="badge bg-dark text-white p-2">EXPENSE ANALYSIS STATEMENT</span>
                <div class="small fw-bold text-muted mt-1">Period: <?= htmlspecialchars($fromDate); ?> to <?= htmlspecialchars($toDate); ?></div>
            </div>
        </div>
    </div>

    <!-- Filter Card (Screen Only) -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 d-print-none">
        <div class="card-body p-3">
            <form id="filterForm" action="<?= \Core\Helper::baseUrl('reports/expenses-analysis'); ?>" method="GET" class="row g-2 align-items-end">
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
                    <input type="date" class="form-control form-control-sm" name="from_date" id="from_date" value="<?= htmlspecialchars($fromDate); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted mb-1">To Date</label>
                    <input type="date" class="form-control form-control-sm" name="to_date" id="to_date" value="<?= htmlspecialchars($toDate); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">Cost Center / Division</label>
                    <select class="form-select form-select-sm" name="cost_center_id">
                        <option value="">-- All Divisions / Entire Society --</option>
                        <?php foreach ($costCenters as $cc): ?>
                            <option value="<?= $cc['id']; ?>" <?= ($selectedCostCenterId == $cc['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cc['code'] . ' - ' . $cc['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-info btn-sm w-100 font-weight-bold text-white">
                        <i class="bi bi-funnel me-1"></i> Update
                    </button>
                    <a href="<?= \Core\Helper::baseUrl('reports/expenses-analysis'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Row -->
    <div class="row g-3 mb-4 d-print-none">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-danger rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Operating Expenses (Period)</div>
                    <div class="h3 font-weight-bold text-danger mb-0">LKR <?= number_format($expenseData['total_amount'], 2); ?></div>
                    <small class="text-muted">Total overhead and cost allocation</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-info rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Active Expense Categories</div>
                    <div class="h3 font-weight-bold text-info mb-0"><?= count($expenseData['rows']); ?> Categories</div>
                    <small class="text-muted">Classified according to General Ledger</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Table Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center d-print-none">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Category-wise Expenditure Breakdown</h6>
                <small class="text-muted">Sorted by highest expenditure share</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" style="width: 100%; table-layout: auto;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 25%;">Expense Category</th>
                            <th style="width: 25%;">Linked GL Account</th>
                            <th style="width: 15%; text-align: center;">Transactions</th>
                            <th style="width: 15%; text-align: right;">Total Amount (LKR)</th>
                            <th style="width: 20%;">Percentage Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenseData['rows'])): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No operating expenses posted in this date range.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenseData['rows'] as $r): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($r['category_name']); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">
                                            <?= htmlspecialchars($r['account_code'] ? $r['account_code'] . ' - ' . $r['account_name'] : 'Auto-Resolved GL'); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill"><?= number_format($r['expense_count']); ?></span>
                                    </td>
                                    <td class="text-end font-monospace font-weight-bold text-danger">
                                        <?= number_format((float)$r['total_amount'], 2); ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $r['percentage']; ?>%"></div>
                                            </div>
                                            <small class="font-monospace text-muted" style="min-width: 45px;"><?= $r['percentage']; ?>%</small>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-dark font-weight-bold">
                        <tr>
                            <td colspan="3" class="text-uppercase py-3">GRAND TOTAL EXPENSES</td>
                            <td class="text-end font-monospace text-warning py-3">LKR <?= number_format($expenseData['total_amount'], 2); ?></td>
                            <td class="py-3 text-warning font-monospace">100.00%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
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
