<?php 
$activeNav = $activeNav ?? 'reports_cash_bank';
$entries = $entries ?? [];
$accounts = $accounts ?? [];
$fromDate = $fromDate ?? date('Y-m-01');
$toDate = $toDate ?? date('Y-m-d');
$selectedAccountId = $selectedAccountId ?? null;
$pagination = $pagination ?? ['current' => 1, 'total' => 1, 'count' => 0];

$totDebit = array_sum(array_column($entries, 'debit'));
$totCredit = array_sum(array_column($entries, 'credit'));
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= \Core\Helper::baseUrl('reports'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Hub</a>
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="bi bi-cash-stack text-info me-2"></i>Cash & Bank Book Statement
                </h4>
            </div>
            <p class="text-muted small mb-0">Complete receipts, disbursements and bank journal audit trail</p>
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
            <form action="<?= \Core\Helper::baseUrl('reports/cash-bank-book'); ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">Account Filter</label>
                    <select class="form-select form-select-sm" name="account_id">
                        <option value="">-- All Cash & Bank Accounts --</option>
                        <?php foreach ($accounts as $acc): ?>
                            <option value="<?= $acc['id']; ?>" <?= ($selectedAccountId == $acc['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($acc['account_code'] . ' - ' . $acc['account_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">From Date</label>
                    <input type="date" class="form-control form-control-sm" name="from_date" value="<?= htmlspecialchars($fromDate); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">To Date</label>
                    <input type="date" class="form-control form-control-sm" name="to_date" value="<?= htmlspecialchars($toDate); ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-info btn-sm w-100 font-weight-bold text-white">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="<?= \Core\Helper::baseUrl('reports/cash-bank-book'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Cash & Bank In (Receipts)</div>
                    <div class="h4 font-weight-bold text-success mb-0">LKR <?= number_format($totDebit, 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-danger rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Cash & Bank Out (Payments)</div>
                    <div class="h4 font-weight-bold text-danger mb-0">LKR <?= number_format($totCredit, 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-info rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Net Cash Flow for Period</div>
                    <div class="h4 font-weight-bold text-info mb-0">LKR <?= number_format($totDebit - $totCredit, 2); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Cash & Bank Transactions</h6>
                <small class="text-muted">Showing page <?= $pagination['current']; ?> of <?= max(1, $pagination['total']); ?> (<?= number_format($pagination['count']); ?> total records)</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 12%;">Date</th>
                            <th style="width: 14%;">Voucher #</th>
                            <th style="width: 16%;">Account</th>
                            <th style="width: 34%;">Description & Source</th>
                            <th style="width: 12%; text-align: right;">Receipt / In (LKR)</th>
                            <th style="width: 12%; text-align: right;">Payment / Out (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($entries)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No cash or bank movements recorded in this period.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($entries as $row): ?>
                                <tr>
                                    <td class="font-monospace text-muted small"><?= htmlspecialchars($row['transaction_date']); ?></td>
                                    <td>
                                        <a href="<?= \Core\Helper::baseUrl('accounting/journal-entries/view?id=' . $row['journal_number']); ?>" class="font-monospace text-decoration-none fw-bold">
                                            <?= htmlspecialchars($row['journal_number']); ?>
                                        </a>
                                        <?php if (!empty($row['reference'])): ?>
                                            <span class="badge bg-light text-muted border small"><?= htmlspecialchars($row['reference']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-dark border small">
                                            <?= htmlspecialchars($row['account_code'] . ' ' . $row['account_name']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-dark"><?= htmlspecialchars($row['description'] ?: 'Cash/Bank transaction'); ?></div>
                                        <small class="text-muted text-uppercase" style="font-size: 0.72rem;">Module: <?= htmlspecialchars($row['source_module']); ?></small>
                                    </td>
                                    <td class="text-end font-monospace font-weight-bold text-success">
                                        <?= (float)$row['debit'] > 0 ? number_format((float)$row['debit'], 2) : '-'; ?>
                                    </td>
                                    <td class="text-end font-monospace font-weight-bold text-danger">
                                        <?= (float)$row['credit'] > 0 ? number_format((float)$row['credit'], 2) : '-'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light font-weight-bold">
                        <tr>
                            <td colspan="4" class="text-uppercase py-3">PAGE TOTALS</td>
                            <td class="text-end font-monospace text-success py-3">LKR <?= number_format($totDebit, 2); ?></td>
                            <td class="text-end font-monospace text-danger py-3">LKR <?= number_format($totCredit, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total'] > 1): ?>
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center d-print-none">
                <small class="text-muted">Total: <?= number_format($pagination['count']); ?> transactions</small>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php for ($p = 1; $p <= $pagination['total']; $p++): ?>
                            <li class="page-item <?= ($p == $pagination['current']) ? 'active' : ''; ?>">
                                <a class="page-link" href="<?= \Core\Helper::baseUrl('reports/cash-bank-book') . '?' . http_build_query(array_merge($_GET, ['page' => $p])); ?>">
                                    <?= $p; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>
