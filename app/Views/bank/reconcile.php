<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('modules/bank-accounts'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Accounts
        </a>
        <h4 class="fw-bold mb-1 text-dark">Bank Reconciliation Workstation</h4>
        <p class="text-muted small mb-0">Select bank account and statement date to reconcile system journal lines with bank statement records.</p>
    </div>
</div>

<!-- Parameters Form -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= \Core\Helper::baseUrl('modules/bank-accounts/reconcile'); ?>" class="row g-3 small">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Select Bank Account</label>
                <select class="form-select form-select-sm" name="bank_account_id" onchange="this.form.submit()" required>
                    <option value="">-- Choose Account --</option>
                    <?php foreach ($bankAccounts as $ba): ?>
                        <option value="<?= $ba['id']; ?>" <?= ($selectedBankId == $ba['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($ba['bank_name']); ?> - <?= htmlspecialchars($ba['account_number']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Statement Ending Date</label>
                <input type="date" class="form-control form-control-sm" name="statement_date" value="<?= htmlspecialchars($statementDate); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Statement Ending Balance (LKR)</label>
                <input type="number" step="0.01" class="form-control form-control-sm font-monospace" name="ending_balance" id="stmtEndingBalance" value="<?= htmlspecialchars($endingBalance); ?>" oninput="calculateDifference()">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-success btn-sm rounded-pill w-100" style="background-color: #1b4332; border-color: #1b4332;">Initialize</button>
            </div>
        </form>
    </div>
</div>

<?php if ($selectedBankId): ?>
<form action="<?= \Core\Helper::baseUrl('modules/bank-accounts/post-reconcile'); ?>" method="POST" id="reconcileForm">
    <?= \Core\CSRF::getFormField(); ?>
    <input type="hidden" name="bank_account_id" value="<?= $selectedBankId; ?>">
    <input type="hidden" name="statement_date" value="<?= htmlspecialchars($statementDate); ?>">
    <input type="hidden" name="ending_balance" value="<?= htmlspecialchars($endingBalance); ?>">
    <input type="hidden" name="book_balance" value="<?= htmlspecialchars($bookBalance); ?>">

    <div class="row g-4">
        <!-- Reconcile Checklist -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check text-success me-2"></i> Unreconciled System Transactions</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;" class="text-center">Clear</th>
                                    <th>Journal #</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th class="text-end">Debit (+)</th>
                                    <th class="text-end">Credit (-)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($transactions)): ?>
                                    <?php foreach ($transactions as $tx): ?>
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input tx-checkbox" type="checkbox" name="reconcile_lines[]" value="<?= $tx['line_id']; ?>" 
                                                       data-debit="<?= $tx['debit']; ?>" data-credit="<?= $tx['credit']; ?>" <?= $tx['reconciled'] ? 'checked' : ''; ?>
                                                       onchange="recalculateTotals()">
                                            </td>
                                            <td class="fw-bold font-monospace"><?= htmlspecialchars($tx['journal_number']); ?></td>
                                            <td><?= htmlspecialchars($tx['transaction_date']); ?></td>
                                            <td><?= htmlspecialchars($tx['description']); ?></td>
                                            <td class="text-end text-success font-monospace"><?= $tx['debit'] > 0 ? number_format($tx['debit'], 2) : '-'; ?></td>
                                            <td class="text-end text-danger font-monospace"><?= $tx['credit'] > 0 ? number_format($tx['credit'], 2) : '-'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No unreconciled transactions.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Totals Summary Sidebar -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-calculator text-success me-2"></i> Reconciliation Balances</h6>
                    
                    <div class="mb-3 border-bottom pb-2">
                        <small class="text-muted d-block">Book Balance (System)</small>
                        <span class="fw-bold font-monospace fs-5 text-dark">LKR <?= number_format($bookBalance, 2); ?></span>
                    </div>

                    <div class="mb-3 border-bottom pb-2">
                        <small class="text-muted d-block">Bank Statement Ending Balance</small>
                        <span class="fw-bold font-monospace fs-5 text-dark" id="displayEndingBalance">LKR <?= number_format($endingBalance, 2); ?></span>
                    </div>

                    <div class="mb-3 border-bottom pb-2">
                        <small class="text-muted d-block">Reconciled Amount</small>
                        <span class="fw-bold font-monospace fs-5 text-success" id="displayReconciledAmount">LKR <?= number_format($reconciledAmount, 2); ?></span>
                    </div>

                    <div class="mb-4">
                        <small class="text-muted d-block">Difference</small>
                        <span class="fw-bold font-monospace fs-4 text-danger" id="displayDifference">LKR <?= number_format($endingBalance - $bookBalance, 2); ?></span>
                        <input type="hidden" name="difference" id="inputDifference" value="<?= $endingBalance - $bookBalance; ?>">
                    </div>

                    <button type="submit" class="btn btn-success rounded-pill w-100" style="background-color: #1b4332; border-color: #1b4332;">
                        <i class="bi bi-check-lg me-1"></i> Save Reconciliation
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php endif; ?>

<script>
function calculateDifference() {
    const endingVal = parseFloat(document.getElementById('stmtEndingBalance').value) || 0;
    const bookBalance = <?= (float)($bookBalance ?? 0); ?>;
    
    document.getElementById('displayEndingBalance').textContent = 'LKR ' + endingVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    recalculateTotals();
}

function recalculateTotals() {
    const endingVal = parseFloat(document.getElementById('stmtEndingBalance').value) || 0;
    let reconciledAmount = 0;

    const checkboxes = document.querySelectorAll('.tx-checkbox:checked');
    checkboxes.forEach(cb => {
        const deb = parseFloat(cb.getAttribute('data-debit')) || 0;
        const cred = parseFloat(cb.getAttribute('data-credit')) || 0;
        reconciledAmount += (deb - cred);
    });

    const diff = endingVal - reconciledAmount;

    document.getElementById('displayReconciledAmount').textContent = 'LKR ' + reconciledAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('displayDifference').textContent = 'LKR ' + diff.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    
    // Update colors based on zero difference
    const diffDisplay = document.getElementById('displayDifference');
    if (Math.abs(diff) < 0.01) {
        diffDisplay.className = 'fw-bold font-monospace fs-4 text-success';
    } else {
        diffDisplay.className = 'fw-bold font-monospace fs-4 text-danger';
    }

    document.getElementById('inputDifference').value = diff;
}
</script>
