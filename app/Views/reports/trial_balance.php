<?php 
$activeNav = $activeNav ?? 'reports_trial_balance';
$trialBalance = $trialBalance ?? [];
$costCenters = $costCenters ?? [];
$fromDate = $fromDate ?? '';
$toDate = $toDate ?? date('Y-m-d');
$selectedCostCenterId = $selectedCostCenterId ?? null;
$showZero = $showZero ?? false;

$totDebit = 0.00;
$totCredit = 0.00;
$totNetDebit = 0.00;
$totNetCredit = 0.00;

foreach ($trialBalance as $tb) {
    $totDebit += (float)$tb['total_debit'];
    $totCredit += (float)$tb['total_credit'];
    $totNetDebit += (float)($tb['net_debit'] ?? 0);
    $totNetCredit += (float)($tb['net_credit'] ?? 0);
}
$isBalanced = abs($totDebit - $totCredit) < 0.01;
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= \Core\Helper::baseUrl('reports'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Hub</a>
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="bi bi-calculator text-warning me-2"></i>Trial Balance Statement
                </h4>
            </div>
            <p class="text-muted small mb-0">Debit & Credit equality verification across all active general ledger accounts</p>
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
            <form action="<?= \Core\Helper::baseUrl('reports/trial-balance'); ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">From Date (Optional)</label>
                    <input type="date" class="form-control form-control-sm" name="from_date" value="<?= htmlspecialchars($fromDate); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">As of Date / To Date</label>
                    <input type="date" class="form-control form-control-sm" name="to_date" value="<?= htmlspecialchars($toDate); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">Cost Center / Division</label>
                    <select class="form-select form-select-sm" name="cost_center_id">
                        <option value="">-- All Divisions --</option>
                        <?php foreach ($costCenters as $cc): ?>
                            <option value="<?= $cc['id']; ?>" <?= ($selectedCostCenterId == $cc['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cc['code'] . ' - ' . $cc['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex flex-column justify-content-end">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="show_zero" value="1" id="showZeroCheck" <?= $showZero ? 'checked' : ''; ?>>
                        <label class="form-check-label small font-weight-bold text-dark" for="showZeroCheck">
                            Show All Accounts (Incl. Zero Balances)
                        </label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning btn-sm w-100 font-weight-bold text-dark">
                            <i class="bi bi-funnel me-1"></i> Update
                        </button>
                        <a href="<?= \Core\Helper::baseUrl('reports/trial-balance'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Balance Status Banner -->
    <div class="mb-4">
        <?php if ($isBalanced): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-0 py-2">
                <i class="bi bi-check-circle-fill fs-4 text-success me-3"></i>
                <div>
                    <h6 class="font-weight-bold text-success mb-0">Trial Balance is Perfectly Balanced</h6>
                    <small class="text-muted">Total Debits (LKR <?= number_format($totDebit, 2); ?>) equal Total Credits (LKR <?= number_format($totCredit, 2); ?>). All transactions adhere to double-entry rules.</small>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center mb-0 py-2">
                <i class="bi bi-exclamation-triangle-fill fs-4 text-danger me-3"></i>
                <div>
                    <h6 class="font-weight-bold text-danger mb-0">Trial Balance Imbalance Detected!</h6>
                    <small>Total Debits (LKR <?= number_format($totDebit, 2); ?>) do not equal Total Credits (LKR <?= number_format($totCredit, 2); ?>). Difference: <strong>LKR <?= number_format(abs($totDebit - $totCredit), 2); ?></strong>.</small>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Table Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Account Balances Schedule</h6>
                <small class="text-muted">As of: <?= htmlspecialchars($toDate); ?></small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border">
                    <?= count($trialBalance); ?> <?= $showZero ? 'Total Accounts (Full Chart of Accounts)' : 'Active Accounts Displayed'; ?>
                </span>
                <?php if (!$showZero): ?>
                    <a href="<?= \Core\Helper::baseUrl('reports/trial-balance') . '?' . http_build_query(array_merge($_GET, ['show_zero' => 1])); ?>" 
                       class="btn btn-xs btn-outline-primary py-1 px-2 small">
                        <i class="bi bi-eye me-1"></i> Show Inactive Accounts
                    </a>
                <?php else: ?>
                    <a href="<?= \Core\Helper::baseUrl('reports/trial-balance') . '?' . http_build_query(array_merge($_GET, ['show_zero' => 0])); ?>" 
                       class="btn btn-xs btn-outline-secondary py-1 px-2 small">
                        <i class="bi bi-eye-slash me-1"></i> Hide Inactive Accounts
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%;">Code</th>
                            <th style="width: 30%;">Account Name</th>
                            <th style="width: 12%;">Category</th>
                            <th style="width: 12%; text-align: right;" class="border-start">Cumulative Debit</th>
                            <th style="width: 12%; text-align: right;">Cumulative Credit</th>
                            <th style="width: 12%; text-align: right;" class="table-warning border-start font-weight-bold">Net Debit (LKR)</th>
                            <th style="width: 12%; text-align: right;" class="table-warning font-weight-bold">Net Credit (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($trialBalance)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No account balances found for the selected criteria.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($trialBalance as $row): ?>
                                <tr>
                                    <td class="font-monospace text-muted small"><?= htmlspecialchars($row['account_code']); ?></td>
                                    <td class="font-weight-bold text-dark">
                                        <a href="<?= \Core\Helper::baseUrl('accounting/general-ledger?account_id=' . $row['account_id']); ?>" class="text-dark text-decoration-none">
                                            <?= htmlspecialchars($row['account_name']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($row['category']); ?></span>
                                    </td>
                                    <td class="text-end font-monospace border-start text-muted"><?= number_format((float)$row['total_debit'], 2); ?></td>
                                    <td class="text-end font-monospace text-muted"><?= number_format((float)$row['total_credit'], 2); ?></td>
                                    <td class="text-end font-monospace border-start font-weight-bold <?= (float)($row['net_debit'] ?? 0) > 0 ? 'text-primary' : 'text-muted'; ?>">
                                        <?= (float)($row['net_debit'] ?? 0) > 0 ? number_format((float)$row['net_debit'], 2) : '-'; ?>
                                    </td>
                                    <td class="text-end font-monospace font-weight-bold <?= (float)($row['net_credit'] ?? 0) > 0 ? 'text-success' : 'text-muted'; ?>">
                                        <?= (float)($row['net_credit'] ?? 0) > 0 ? number_format((float)$row['net_credit'], 2) : '-'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-dark font-weight-bold">
                        <tr>
                            <td colspan="3" class="text-uppercase py-3">GRAND TOTALS</td>
                            <td class="text-end font-monospace py-3 text-white border-start">LKR <?= number_format($totDebit, 2); ?></td>
                            <td class="text-end font-monospace py-3 text-white">LKR <?= number_format($totCredit, 2); ?></td>
                            <td class="text-end font-monospace py-3 text-warning border-start">LKR <?= number_format($totNetDebit, 2); ?></td>
                            <td class="text-end font-monospace py-3 text-warning">LKR <?= number_format($totNetCredit, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
