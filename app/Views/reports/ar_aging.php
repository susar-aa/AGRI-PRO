<?php 
$activeNav = $activeNav ?? 'reports_ar_aging';
$records = $records ?? [];
$totals = $totals ?? ['total_receivable' => 0, 'current_0_30' => 0, 'days_31_60' => 0, 'days_61_90' => 0, 'days_over_90' => 0];
$filters = $filters ?? [];
?>

<div class="content-wrapper p-4">
    <!-- Header Section (Screen Only) -->
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
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
            <a href="<?= \Core\Helper::baseUrl('reports/print-preview?' . http_build_query(array_merge($_GET, ['type' => 'ar_aging']))); ?>" 
               target="_blank" 
               class="btn btn-dark btn-sm shadow-sm d-flex align-items-center gap-1 font-weight-bold">
                <i class="bi bi-printer-fill text-warning"></i> Print Preview
            </a>
            <a href="<?= \Core\Helper::baseUrl('reports/export?' . http_build_query(array_merge($_GET, ['type' => 'ar_aging']))); ?>" 
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
                <span class="badge bg-dark text-white p-2">AR AGING SCHEDULE</span>
                <div class="small fw-bold text-muted mt-1">As of: <?= date('Y-m-d'); ?></div>
            </div>
        </div>
    </div>

    <!-- Filter Card (Screen Only) -->
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

    <!-- KPI Row (Screen Only) -->
    <div class="row g-3 mb-4 d-print-none">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Current (0 - 30 Days)</div>
                    <div class="h4 font-weight-bold text-success mb-0">LKR <?= number_format($totals['current_0_30'], 2); ?></div>
                    <small class="text-muted">Healthy credit</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-warning rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Due (31 - 60 Days)</div>
                    <div class="h4 font-weight-bold text-warning mb-0">LKR <?= number_format($totals['days_31_60'], 2); ?></div>
                    <small class="text-muted">Follow-up needed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-danger rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Overdue (61 - 90 Days)</div>
                    <div class="h4 font-weight-bold text-danger mb-0">LKR <?= number_format($totals['days_61_90'], 2); ?></div>
                    <small class="text-muted">Notice required</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-dark rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Receivables</div>
                    <div class="h4 font-weight-bold text-dark mb-0">LKR <?= number_format($totals['total_receivable'], 2); ?></div>
                    <small class="text-muted">Across all accounts</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Aging Table Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center d-print-none">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Customer Aging Matrix</h6>
                <small class="text-muted">Aging calculated relative to invoice dates</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" style="width: 100%; table-layout: auto;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 12%;">Code</th>
                            <th style="width: 28%;">Customer Name</th>
                            <th style="width: 12%; text-align: right;">0 - 30 Days</th>
                            <th style="width: 12%; text-align: right;">31 - 60 Days</th>
                            <th style="width: 12%; text-align: right;">61 - 90 Days</th>
                            <th style="width: 12%; text-align: right;">> 90 Days</th>
                            <th style="width: 12%; text-align: right;">Total Due (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No outstanding customer receivables found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($records as $r): ?>
                                <tr>
                                    <td class="font-monospace text-muted small"><?= htmlspecialchars($r['code']); ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($r['name']); ?></div>
                                        <?php if (!empty($r['phone'])): ?>
                                            <small class="text-muted"><?= htmlspecialchars($r['phone']); ?></small>
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
