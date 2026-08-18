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
                            <label for="cash_amount" class="form-label fw-semibold">Deposit Amount (LKR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" max="<?= $cashBalance; ?>" class="form-control font-monospace fw-bold text-success" id="cash_amount" name="cash_amount" value="" required>
                            <small class="text-muted d-block mt-1">Available: LKR <?= number_format($cashBalance, 2); ?></small>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Deposit Description / Remarks <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="description" name="description" value="Bank Deposit Voucher" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 text-end">
                    <button type="submit" name="action" value="post" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Post Deposit</button>
                </div>
            </div>
        </div>
    </div>
</form>
