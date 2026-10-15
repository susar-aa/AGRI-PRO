<?php 
$activeNav = $activeNav ?? 'reports_balance_sheet';
$balanceSheet = $balanceSheet ?? [
    'assets' => [], 'total_assets' => 0, 
    'liabilities' => [], 'total_liabilities' => 0, 
    'equity' => [], 'total_base_equity' => 0, 
    'current_earnings' => 0, 'total_equity' => 0, 
    'total_liabilities_and_equity' => 0, 
    'is_balanced' => true
];
$selectedMonth = $selectedMonth ?? date('Y-m');
$asOfDate = $asOfDate ?? date('Y-m-d');
?>

<div class="content-wrapper p-4">
    <!-- Header Section (Screen Only) -->
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= \Core\Helper::baseUrl('reports'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Hub</a>
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="bi bi-bank text-primary me-2"></i>Balance Sheet Statement
                </h4>
            </div>
            <p class="text-muted small mb-0">Statement of Financial Position: Assets, Liabilities, and Cooperative Equity</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= \Core\Helper::baseUrl('reports/print-preview?' . http_build_query(array_merge($_GET, ['type' => 'balance_sheet']))); ?>" 
               target="_blank" 
               class="btn btn-dark btn-sm shadow-sm d-flex align-items-center gap-1 font-weight-bold">
                <i class="bi bi-printer-fill text-warning"></i> Print Preview
            </a>
            <a href="<?= \Core\Helper::baseUrl('reports/export?' . http_build_query(array_merge($_GET, ['type' => 'balance_sheet']))); ?>" 
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
                <span class="badge bg-dark text-white p-2">BALANCE SHEET STATEMENT</span>
                <div class="small fw-bold text-muted mt-1">As of: <?= htmlspecialchars($asOfDate); ?></div>
            </div>
        </div>
    </div>

    <!-- Filter Card (Screen Only) -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 d-print-none">
        <div class="card-body p-3">
            <form id="filterForm" method="GET" action="<?= \Core\Helper::baseUrl('reports/balance-sheet'); ?>" class="row g-2 align-items-end">
                <!-- Direct Month Selector -->
                <div class="col-md-4">
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

                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">Statement As Of Date</label>
                    <input type="date" name="as_of_date" id="as_of_date" class="form-control form-control-sm" value="<?= htmlspecialchars($asOfDate); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 font-weight-bold">
                        <i class="bi bi-funnel me-1"></i> Update
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-center justify-content-end">
                    <?php if ($balanceSheet['is_balanced']): ?>
                        <div class="badge bg-success bg-opacity-10 text-success border border-success p-2 px-3 rounded-pill d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill fs-6"></i>
                            <span class="font-weight-bold">Status: BALANCED</span>
                        </div>
                    <?php else: ?>
                        <div class="badge bg-danger bg-opacity-10 text-danger border border-danger p-2 px-3 rounded-pill d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                            <span class="font-weight-bold">Status: OUT OF BALANCE</span>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Summary Row (Screen Only) -->
    <div class="row g-3 mb-4 d-print-none">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-primary rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Assets (1)</div>
                    <div class="h3 font-weight-bold text-primary mb-0">LKR <?= number_format($balanceSheet['total_assets'], 2); ?></div>
                    <small class="text-muted">Total economic resources owned</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-danger rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Liabilities (2)</div>
                    <div class="h3 font-weight-bold text-danger mb-0">LKR <?= number_format($balanceSheet['total_liabilities'], 2); ?></div>
                    <small class="text-muted">Payables & obligations</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Equity & Reserves (3)</div>
                    <div class="h3 font-weight-bold text-success mb-0">LKR <?= number_format($balanceSheet['total_equity'], 2); ?></div>
                    <small class="text-muted">Net cooperative wealth</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statement Table Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center d-print-none">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Statement of Financial Position</h6>
                <small class="text-muted">As of: <?= htmlspecialchars($asOfDate); ?></small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="width: 100%; table-layout: auto;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">Account Code</th>
                            <th style="width: 55%;">Account Title / Classification</th>
                            <th style="width: 15%; text-align: right;">Detail (LKR)</th>
                            <th style="width: 15%; text-align: right;">Total (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1. ASSETS -->
                        <tr class="table-light font-weight-bold">
                            <td colspan="4" class="text-uppercase text-primary py-2">
                                <i class="bi bi-box-seam me-1"></i> Assets (Current & Non-Current)
                            </td>
                        </tr>
                        <?php if (empty($balanceSheet['assets'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-2 text-muted small">No assets recorded as of this date.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($balanceSheet['assets'] as $row): ?>
                                <tr>
                                    <td class="font-monospace text-muted small ps-4"><?= htmlspecialchars($row['account_code']); ?></td>
                                    <td><?= htmlspecialchars($row['account_name']); ?></td>
                                    <td class="text-end font-monospace"><?= number_format($row['net_balance'], 2); ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tr class="bg-primary bg-opacity-10 font-weight-bold">
                            <td colspan="3" class="ps-4">TOTAL ASSETS (1)</td>
                            <td class="text-end font-monospace text-primary"><?= number_format($balanceSheet['total_assets'], 2); ?></td>
                        </tr>

                        <!-- 2. LIABILITIES -->
                        <tr class="table-light font-weight-bold">
                            <td colspan="4" class="text-uppercase text-danger py-2">
                                <i class="bi bi-shield-exclamation me-1"></i> Liabilities (Current & Long Term)
                            </td>
                        </tr>
                        <?php if (empty($balanceSheet['liabilities'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-2 text-muted small">No liabilities recorded as of this date.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($balanceSheet['liabilities'] as $row): ?>
                                <tr>
                                    <td class="font-monospace text-muted small ps-4"><?= htmlspecialchars($row['account_code']); ?></td>
                                    <td><?= htmlspecialchars($row['account_name']); ?></td>
                                    <td class="text-end font-monospace"><?= number_format($row['net_balance'], 2); ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tr class="bg-danger bg-opacity-10 font-weight-bold">
                            <td colspan="3" class="ps-4">TOTAL LIABILITIES (2)</td>
                            <td class="text-end font-monospace text-danger"><?= number_format($balanceSheet['total_liabilities'], 2); ?></td>
                        </tr>

                        <!-- 3. EQUITY & RESERVES -->
                        <tr class="table-light font-weight-bold">
                            <td colspan="4" class="text-uppercase text-success py-2">
                                <i class="bi bi-gem me-1"></i> Cooperative Equity & Accumulated Reserves
                            </td>
                        </tr>
                        <?php foreach ($balanceSheet['equity'] as $eq): ?>
                            <tr>
                                <td class="font-monospace text-muted small ps-4"><?= htmlspecialchars($eq['account_code']); ?></td>
                                <td><?= htmlspecialchars($eq['account_name']); ?></td>
                                <td class="text-end font-monospace"><?= number_format($eq['net_balance'], 2); ?></td>
                                <td></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td class="font-monospace text-muted small ps-4">3999</td>
                            <td class="fst-italic text-muted">Retained Operating Surplus / (Deficit) - Current Period</td>
                            <td class="text-end font-monospace <?= $balanceSheet['current_earnings'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <?= number_format($balanceSheet['current_earnings'], 2); ?>
                            </td>
                            <td></td>
                        </tr>
                        <tr class="bg-success bg-opacity-10 font-weight-bold">
                            <td colspan="3" class="ps-4">TOTAL EQUITY & RESERVES (3)</td>
                            <td class="text-end font-monospace text-success"><?= number_format($balanceSheet['total_equity'], 2); ?></td>
                        </tr>

                        <!-- TOTAL LIABILITIES & EQUITY -->
                        <tr class="bg-dark bg-opacity-10 font-weight-bold" style="border-top: 3px double #333; border-bottom: 3px double #333;">
                            <td colspan="3" class="py-3 fs-5">
                                TOTAL LIABILITIES & EQUITY (2 + 3)
                            </td>
                            <td class="text-end font-monospace py-3 fs-5 text-dark">
                                <?= number_format($balanceSheet['total_liabilities_and_equity'], 2); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white text-muted small d-flex justify-content-between d-print-none">
            <span>Generated on: <?= date('Y-m-d H:i:s'); ?> | System Engine: Agri Co-Op ERP</span>
            <span>Accounting Equation: Total Assets = Total Liabilities + Equity</span>
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
    
    document.getElementById('as_of_date').value = `${monthVal}-${String(lastDay).padStart(2, '0')}`;
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
