<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('supplier-payments'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Ledger
        </a>
        <h4 class="fw-bold mb-1 text-dark">Make Supplier Payment</h4>
        <p class="text-muted small mb-0">Record cash drawer or bank transfer payment disbursements to supplier accounts.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="<?= \Core\Helper::baseUrl('supplier-payments/store'); ?>" method="POST" id="paymentForm">
            <?= \Core\CSRF::getFormField(); ?>
            <input type="hidden" name="payment_type" value="PAYMENT">

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="party_id" class="form-label fw-semibold">Select Supplier <span class="text-danger">*</span></label>
                    <select class="form-select" id="party_id" name="party_id" required>
                        <option value="">-- Select Supplier Account --</option>
                        <?php foreach ($suppliers as $supp): ?>
                            <option value="<?= $supp['id']; ?>" <?= (!empty($selectedParty) && $selectedParty['id'] == $supp['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($supp['party_code']); ?> - <?= htmlspecialchars($supp['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="payment_date" class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?= date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="amount" class="form-label fw-semibold">Payment Amount (LKR) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" class="form-control fw-bold text-danger" id="amount" name="amount" placeholder="0.00" required>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="payment_method" class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                    <select class="form-select" id="payment_method" name="payment_method" required onchange="togglePaymentInputs()">
                        <option value="">-- Select Method --</option>
                        <option value="Cash">Cash Drawer</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>

                <!-- Dynamic input fields -->
                <div class="col-md-8" id="cashAccountSection" style="display: none;">
                    <label for="cash_account_id" class="form-label fw-semibold">Select Cash Account Source Drawer <span class="text-danger">*</span></label>
                    <select class="form-select" id="cash_account_id" name="cash_account_id">
                        <option value="">-- Select Cash Drawer --</option>
                        <?php foreach ($cashAccounts as $ca): ?>
                            <option value="<?= $ca['id']; ?>"><?= htmlspecialchars($ca['name']); ?> (Balance: LKR <?= number_format($ca['current_balance'], 2); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-8" id="bankAccountSection" style="display: none;">
                    <label for="bank_account_id" class="form-label fw-semibold">Select Source Bank Account <span class="text-danger">*</span></label>
                    <select class="form-select" id="bank_account_id" name="bank_account_id">
                        <option value="">-- Select Bank Account --</option>
                        <?php foreach ($bankAccounts as $ba): ?>
                            <option value="<?= $ba['id']; ?>"><?= htmlspecialchars($ba['bank_name']); ?> - <?= htmlspecialchars($ba['account_number']); ?> (Balance: LKR <?= number_format($ba['current_balance'], 2); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label fw-semibold">Notes / Description <span class="text-danger">*</span></label>
                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="e.g. Settlement of supplier invoice" required></textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="reference_number" class="form-label fw-semibold">Reference / cheque / Slip Number</label>
                    <input type="text" class="form-control" id="reference_number" name="reference_number" placeholder="e.g. Reference number, transaction id">
                </div>
            </div>

            <div class="modal-footer bg-light p-3 rounded-3 mt-4 gap-2">
                <a href="<?= \Core\Helper::baseUrl('supplier-payments'); ?>" class="btn btn-secondary rounded-pill px-3">Cancel</a>
                <button type="submit" name="action" value="post" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Post Payment</button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePaymentInputs() {
    const paymentMethod = document.getElementById('payment_method').value;
    const cashSection = document.getElementById('cashAccountSection');
    const bankSection = document.getElementById('bankAccountSection');

    // Reset required states
    document.getElementById('cash_account_id').required = false;
    document.getElementById('bank_account_id').required = false;

    cashSection.style.display = 'none';
    bankSection.style.display = 'none';

    if (paymentMethod === 'Cash') {
        cashSection.style.display = 'block';
        document.getElementById('cash_account_id').required = true;
    } else if (paymentMethod === 'Bank Transfer') {
        bankSection.style.display = 'block';
        document.getElementById('bank_account_id').required = true;
    }
}
</script>
