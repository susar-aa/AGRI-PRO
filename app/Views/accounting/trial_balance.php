<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Trial Balance Statement</h4>
        <p class="text-muted small mb-0">Summarized Debit and Credit balances of ledger accounts.</p>
    </div>
</div>

<!-- Filter Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('accounting/trial-balance'); ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label for="cost_center_id" class="form-label small fw-semibold">Filter by Cost Center</label>
                <select class="form-select" id="cost_center_id" name="cost_center_id">
                    <option value="">-- All Cost Centers --</option>
                    <?php foreach ($costCenters as $cc): ?>
                        <option value="<?= $cc['id']; ?>" <?= ($selectedCostCenterId == $cc['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cc['code']); ?> - <?= htmlspecialchars($cc['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label for="from_date" class="form-label small fw-semibold">From Date (Optional)</label>
                <input type="date" class="form-control" id="from_date" name="from_date" value="<?= htmlspecialchars($fromDate ?? ''); ?>">
            </div>
            <div class="col-6 col-md-3">
                <label for="to_date" class="form-label small fw-semibold">As of Date / To Date</label>
                <input type="date" class="form-control" id="to_date" name="to_date" value="<?= htmlspecialchars($toDate); ?>">
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-success w-100 rounded-pill" style="background-color: #1b4332; border-color: #1b4332;">
                    <i class="bi bi-filter me-1"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$totDebit = 0.00;
$totCredit = 0.00;
foreach ($trialBalance as $tb) {
    $totDebit += (float)$tb['total_debit'];
    $totCredit += (float)$tb['total_credit'];
}
?>

<!-- Imbalance Warning -->
<?php if (abs($totDebit - $totCredit) > 0.001): ?>
    <div class="alert alert-danger rounded-4 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Trial Balance Imbalance Detected!</h6>
            <span>Total Debits (LKR <?= number_format($totDebit, 2) ?>) do not equal Total Credits (LKR <?= number_format($totCredit, 2) ?>). Difference: <strong>LKR <?= number_format(abs($totDebit - $totCredit), 2) ?></strong>. Please investigate the posted ledger entries.</span>
        </div>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-calculator-fill text-success me-2"></i> 
            Trial Balance as of <?= htmlspecialchars($toDate); ?> 
            <?php if (!empty($fromDate)) echo " from " . htmlspecialchars($fromDate); ?>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 140px;">Account Code</th>
                        <th>Account Name</th>
                        <th>Category</th>
                        <th class="text-end" style="width: 200px;">Debit (LKR)</th>
                        <th class="text-end" style="width: 200px;">Credit (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($trialBalance)): ?>
                        <?php foreach ($trialBalance as $tb): ?>
                            <tr>
                                <td class="fw-bold font-monospace text-dark"><?= htmlspecialchars($tb['account_code']); ?></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($tb['account_name']); ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($tb['category']); ?></span></td>
                                <td class="text-end text-success fw-bold"><?= \Core\Helper::formatCurrency($tb['total_debit'], false); ?></td>
                                <td class="text-end text-danger fw-bold"><?= \Core\Helper::formatCurrency($tb['total_credit'], false); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No balances posted as of <?= htmlspecialchars($toDate); ?>.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light fw-bold fs-6">
                    <tr>
                        <td colspan="3" class="text-end">TOTAL TRIAL BALANCE:</td>
                        <td class="text-end text-success fs-5"><?= \Core\Helper::formatCurrency($totDebit, false); ?></td>
                        <td class="text-end text-danger fs-5"><?= \Core\Helper::formatCurrency($totCredit, false); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
