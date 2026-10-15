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
$asOfDate = $asOfDate ?? date('Y-m-d');
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
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
            <a href="<?= \Core\Helper::baseUrl('reports/export?type=balance_sheet&as_of_date=' . urlencode($asOfDate)); ?>" 
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
            <form method="GET" action="<?= \Core\Helper::baseUrl('reports/balance-sheet'); ?>" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-muted mb-1">Statement As Of Date</label>
                    <input type="date" name="as_of_date" class="form-control form-control-sm" value="<?= htmlspecialchars($asOfDate); ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100 font-weight-bold">
                        <i class="bi bi-funnel me-1"></i> Update Statement
                    </button>
                </div>
                <div class="col-md-5 d-flex align-items-center justify-content-end">
                    <?php if ($balanceSheet['is_balanced']): ?>
                        <div class="badge bg-success bg-opacity-10 text-success border border-success p-2 px-3 rounded-pill d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill fs-6"></i>
                            <span class="font-weight-bold">Balance Sheet Equality: BALANCED</span>
                        </div>
                    <?php else: ?>
                        <div class="badge bg-danger bg-opacity-10 text-danger border border-danger p-2 px-3 rounded-pill d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                            <span class="font-weight-bold">Equality Warning: OUT OF BALANCE</span>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-primary rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Assets</div>
                    <div class="h4 font-weight-bold text-primary mb-0">LKR <?= number_format($balanceSheet['total_assets'], 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-danger rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Liabilities</div>
                    <div class="h4 font-weight-bold text-danger mb-0">LKR <?= number_format($balanceSheet['total_liabilities'], 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Cooperative Equity</div>
                    <div class="h4 font-weight-bold text-success mb-0">LKR <?= number_format($balanceSheet['total_equity'], 2); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Sheet Statement Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Statement of Financial Position (Balance Sheet)</h6>
                <small class="text-muted">As of: <?= htmlspecialchars($asOfDate); ?></small>
            </div>
            <span class="badge bg-light text-dark border">Currency: LKR</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">Account Code</th>
                            <th style="width: 55%;">Account Description</th>
                            <th style="width: 15%; text-align: right;">Subtotal (LKR)</th>
                            <th style="width: 15%; text-align: right;">Amount (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1. ASSETS -->
                        <tr class="table-primary font-weight-bold">
                            <td colspan="4" class="text-uppercase py-2">
                                <i class="bi bi-wallet2 me-1"></i> ASSETS
                            </td>
                        </tr>
                        <?php if (empty($balanceSheet['assets'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-2 text-muted">No asset balances found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($balanceSheet['assets'] as $a): ?>
                                <tr>
                                    <td class="font-monospace text-muted small ps-4"><?= htmlspecialchars($a['account_code']); ?></td>
                                    <td><?= htmlspecialchars($a['account_name']); ?></td>
                                    <td class="text-end font-monospace"><?= number_format($a['net_balance'], 2); ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tr class="bg-primary bg-opacity-10 font-weight-bold" style="border-top: 2px solid #0d6efd; border-bottom: 2px solid #0d6efd;">
                            <td colspan="3" class="fs-6 ps-4">TOTAL ASSETS (1)</td>
                            <td class="text-end font-monospace fs-6 text-primary"><?= number_format($balanceSheet['total_assets'], 2); ?></td>
                        </tr>

                        <!-- 2. LIABILITIES -->
                        <tr class="table-danger font-weight-bold">
                            <td colspan="4" class="text-uppercase py-2">
                                <i class="bi bi-credit-card me-1"></i> LIABILITIES
                            </td>
                        </tr>
                        <?php if (empty($balanceSheet['liabilities'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-2 text-muted small">No liabilities recorded.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($balanceSheet['liabilities'] as $l): ?>
                                <tr>
                                    <td class="font-monospace text-muted small ps-4"><?= htmlspecialchars($l['account_code']); ?></td>
                                    <td><?= htmlspecialchars($l['account_name']); ?></td>
                                    <td class="text-end font-monospace"><?= number_format($l['net_balance'], 2); ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tr class="bg-danger bg-opacity-10 font-weight-bold">
                            <td colspan="3" class="ps-4">TOTAL LIABILITIES (2)</td>
                            <td class="text-end font-monospace text-danger"><?= number_format($balanceSheet['total_liabilities'], 2); ?></td>
                        </tr>

                        <!-- 3. EQUITY -->
                        <tr class="table-success font-weight-bold">
                            <td colspan="4" class="text-uppercase py-2">
                                <i class="bi bi-pie-chart me-1"></i> COOPERATIVE EQUITY & RESERVES
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
        <div class="card-footer bg-white text-muted small d-flex justify-content-between">
            <span>Generated on: <?= date('Y-m-d H:i:s'); ?> | System Engine: Agri Co-Op ERP</span>
            <span>Accounting Equation: Total Assets = Total Liabilities + Equity</span>
        </div>
    </div>
</div>
