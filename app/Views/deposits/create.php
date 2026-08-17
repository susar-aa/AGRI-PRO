<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('deposits'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to History
        </a>
        <h4 class="fw-bold mb-1 text-dark">Record Bank Deposit</h4>
        <p class="text-muted small mb-0">Record bank deposits combining cash from drawers and undeposited customer cheques.</p>
    </div>
</div>

<form action="<?= \Core\Helper::baseUrl('deposits/store'); ?>" method="POST" id="depositForm">
    <?= \Core\CSRF::getFormField(); ?>

    <div class="row g-4">
        <!-- Details Column -->
        <div class="col-12 col-lg-8">
            <!-- Header Info -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="bank_account_id" class="form-label fw-semibold">Destination Bank Account <span class="text-danger">*</span></label>
                            <select class="form-select" id="bank_account_id" name="bank_account_id" required>
                                <option value="">-- Select Bank Account --</option>
                                <?php foreach ($bankAccounts as $ba): ?>
                                    <option value="<?= $ba['id']; ?>"><?= htmlspecialchars($ba['bank_name']); ?> - <?= htmlspecialchars($ba['account_number']); ?> (Balance: LKR <?= number_format($ba['current_balance'], 2); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="deposit_date" class="form-label fw-semibold">Deposit Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="deposit_date" name="deposit_date" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Estimated Total (LKR)</label>
                            <div class="form-control fw-bold bg-light text-success font-monospace" id="calcTotalDisplay">0.00</div>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Deposit Description / Remarks <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="description" name="description" value="Bank Deposit Voucher" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cheques Selection -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wallet2 text-success me-2"></i> Select Undeposited Cheques to Deposit</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;" class="text-center">Select</th>
                                    <th>Cheque Number</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Bank</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($cheques)): ?>
                                    <?php foreach ($cheques as $ch): ?>
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input cheque-checkbox" type="checkbox" name="cheque_ids[]" value="<?= $ch['id']; ?>" data-amount="<?= $ch['amount']; ?>" onchange="recalculateTotal()">
                                            </td>
                                            <td class="fw-bold font-monospace text-success"><?= htmlspecialchars($ch['cheque_number']); ?></td>
                                            <td><?= htmlspecialchars($ch['cheque_date']); ?></td>
                                            <td>
                                                <div class="fw-semibold text-dark"><?= htmlspecialchars($ch['customer_name']); ?></div>
                                                <small class="text-muted font-monospace"><?= htmlspecialchars($ch['party_code']); ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($ch['bank_name']); ?></td>
                                            <td class="text-end fw-bold font-monospace"><?= number_format($ch['amount'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No undeposited customer cheques available.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Deposit Sidebar Column -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-coin text-success me-2"></i> Cash Deposit Component</h6>
                </div>
                <div class="card-body p-3 pt-0 small">
                    <div class="mb-3">
                        <label for="cash_account_id" class="form-label fw-semibold">Select Cash Account Source <span class="text-danger">* if cash deposited</span></label>
                        <select class="form-select form-select-sm" id="cash_account_id" name="cash_account_id" onchange="validateCashInputs()">
                            <option value="">-- Select Cash Drawer --</option>
                            <?php foreach ($cashAccounts as $ca): ?>
                                <option value="<?= $ca['id']; ?>"><?= htmlspecialchars($ca['name']); ?> (Bal: LKR <?= number_format($ca['current_balance'], 2); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cash_amount" class="form-label fw-semibold">Deposited Cash Amount (LKR)</label>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace fw-bold text-success" id="cash_amount" name="cash_amount" value="0.00" oninput="validateCashInputs(); recalculateTotal();">
                    </div>
                </div>
            </div>

            <!-- Action panel -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 text-center">
                    <button type="submit" name="action" value="draft" class="btn btn-outline-secondary rounded-pill w-100 mb-2">Save as Draft</button>
                    <button type="submit" name="action" value="post" class="btn btn-success rounded-pill w-100" style="background-color: #1b4332; border-color: #1b4332;">Post Deposit</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function recalculateTotal() {
    let total = 0.00;

    // Sum cash
    const cashVal = parseFloat(document.getElementById('cash_amount').value);
    if (!isNaN(cashVal) && cashVal > 0) {
        total += cashVal;
    }

    // Sum cheques
    const checkboxes = document.querySelectorAll('.cheque-checkbox:checked');
    checkboxes.forEach(cb => {
        const amt = parseFloat(cb.getAttribute('data-amount'));
        if (!isNaN(amt)) {
            total += amt;
        }
    });

    document.getElementById('calcTotalDisplay').textContent = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function validateCashInputs() {
    const cashAmt = parseFloat(document.getElementById('cash_amount').value);
    const cashAccountId = document.getElementById('cash_account_id').value;

    if (!isNaN(cashAmt) && cashAmt > 0) {
        document.getElementById('cash_account_id').required = true;
    } else {
        document.getElementById('cash_account_id').required = false;
    }
}
</script>
