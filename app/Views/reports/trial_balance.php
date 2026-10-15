<?php 
$activeNav = $activeNav ?? 'reports_trial_balance';
$trialBalance = $trialBalance ?? [];
$costCenters = $costCenters ?? [];
$fromDate = $fromDate ?? '';
$toDate = $toDate ?? date('Y-m-d');
$selectedCostCenterId = $selectedCostCenterId ?? null;

$totDebit = 0.00;
$totCredit = 0.00;
foreach ($trialBalance as $tb) {
    $totDebit += (float)$tb['total_debit'];
    $totCredit += (float)$tb['total_credit'];
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
                <i class="bi bi-printer"></i> Print
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
                <div class="col-md-4">
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
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-warning btn-sm w-100 font-weight-bold text-dark">
                        <i class="bi bi-funnel me-1"></i> Update
                    </button>
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
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Account Balances Schedule</h6>
                <small class="text-muted">As of: <?= htmlspecialchars($toDate); ?></small>
            </div>
            <span class="badge bg-light text-dark border"><?= count($trialBalance); ?> Active Accounts</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">Account Code</th>
                            <th style="width: 45%;">Account Name</th>
                            <th style="width: 15%;">Category</th>
                            <th style="width: 12%; text-align: right;">Debit Total (LKR)</th>
                            <th style="width: 13%; text-align: right;">Credit Total (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($trialBalance)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No account balances found for the selected criteria.</td>
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
                                    <td class="text-end font-monospace"><?= number_format((float)$row['total_debit'], 2); ?></td>
                                    <td class="text-end font-monospace"><?= number_format((float)$row['total_credit'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-dark font-weight-bold">
                        <tr>
                            <td colspan="3" class="text-uppercase py-3">GRAND TOTALS</td>
                            <td class="text-end font-monospace py-3 text-warning">LKR <?= number_format($totDebit, 2); ?></td>
                            <td class="text-end font-monospace py-3 text-warning">LKR <?= number_format($totCredit, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
