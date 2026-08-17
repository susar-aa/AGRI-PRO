<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('parties/view?id=' . $party['id']); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Profile
        </a>
        <h4 class="fw-bold mb-1 text-dark">Record Opening Balance</h4>
        <p class="text-muted small mb-0">Record initial outstanding financial ledger balances for: <strong><?= htmlspecialchars($party['name']); ?></strong></p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4" style="max-width: 600px;">
    <div class="card-body p-4">
        <form action="<?= \Core\Helper::baseUrl('parties/opening-balance/store'); ?>" method="POST" id="openingBalanceForm">
            <?= \Core\CSRF::getFormField(); ?>
            <input type="hidden" name="party_id" value="<?= $party['id']; ?>">

            <div class="mb-3">
                <label for="type" class="form-label fw-semibold">Opening Balance Type <span class="text-danger">*</span></label>
                <select class="form-select" id="type" name="type" required>
                    <?php
                    $isCustomer = ($party['party_type'] === 'CUSTOMER' || $party['party_type'] === 'BOTH');
                    $isSupplier = ($party['party_type'] === 'SUPPLIER' || $party['party_type'] === 'BOTH');
                    ?>
                    <?php if ($isCustomer): ?>
                        <option value="receivable">Customer Receivable (Debit Accounts Receivable)</option>
                    <?php endif; ?>
                    <?php if ($isSupplier): ?>
                        <option value="payable">Supplier Payable (Credit Accounts Payable)</option>
                    <?php endif; ?>
                </select>
                <small class="text-muted d-block mt-1">This will automatically post double-entry balances offset against **Equity**.</small>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="amount" class="form-label fw-semibold">Opening Balance Amount (LKR) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" class="form-control fw-bold" id="amount" name="amount" placeholder="0.00" required>
                </div>
                <div class="col-md-6">
                    <label for="balance_date" class="form-label fw-semibold">Balance Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="balance_date" name="balance_date" value="<?= date('Y-m-d'); ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label fw-semibold">Description / Remarks <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="description" name="description" value="Opening Balance Entry" required>
            </div>

            <div class="alert alert-info py-2 rounded-3 small">
                <i class="bi bi-info-circle-fill me-1"></i> Once saved and posted, this opening balance cannot be edited. It must be reversed via a correction entry if any adjustments are needed.
            </div>

            <div class="modal-footer bg-light p-3 rounded-3 mt-4 gap-2">
                <a href="<?= \Core\Helper::baseUrl('parties/view?id=' . $party['id']); ?>" class="btn btn-secondary rounded-pill px-3">Cancel</a>
                <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Post Opening Balance</button>
            </div>
        </form>
    </div>
</div>
