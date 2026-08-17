<?php if (isset($_GET['source_module']) && $_GET['source_module'] === 'MACHINERY'): ?>
    <div class="mb-3">
        <a href="<?= \Core\Helper::baseUrl('operations/machinery'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Back to Machinery Renting
        </a>
    </div>
<?php elseif (isset($prefilled['project_id']) && $prefilled['source_module'] === 'PLANTATION'): ?>
    <div class="mb-3">
        <a href="<?= \Core\Helper::baseUrl('operations/plantation/expenses?id=' . $prefilled['project_id']); ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Back to Project Expenses
        </a>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('expenses'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
        <h4 class="fw-bold mb-1 text-dark">Record Operational Expense</h4>
        <p class="text-muted small mb-0">Fill out details to record a new business expense. Fields will adjust dynamically based on payment type.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="<?= \Core\Helper::baseUrl('expenses/store'); ?>" method="POST" enctype="multipart/form-data" id="expenseForm">
            <?= \Core\CSRF::getFormField(); ?>

            <!-- Prefilled references from caller modules -->
            <input type="hidden" name="source_module" value="<?= htmlspecialchars($prefilled['source_module']); ?>">
            <input type="hidden" name="source_type" value="<?= htmlspecialchars($prefilled['source_type']); ?>">
            <input type="hidden" name="source_transaction_id" value="<?= htmlspecialchars($prefilled['source_transaction_id'] ?? ''); ?>">
            <input type="hidden" name="project_id" value="<?= htmlspecialchars($prefilled['project_id'] ?? ''); ?>">
            <input type="hidden" name="batch_id" value="<?= htmlspecialchars($prefilled['batch_id'] ?? ''); ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="expense_date" class="form-label fw-semibold small">Expense Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm" id="expense_date" name="expense_date" value="<?= date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="expense_category_id" class="form-label fw-semibold small">Expense Category <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="expense_category_id" name="expense_category_id" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id']; ?>">
                                <?= htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="payee" class="form-label fw-semibold small">Payee / Recipient <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" id="payee" name="payee" placeholder="e.g. Ceylon Electricity Board, Rambukkana Fuel Station" required>
                </div>
                <div class="col-md-6">
                    <label for="amount" class="form-label fw-semibold small">Amount (LKR) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" class="form-control form-control-sm fw-bold font-monospace" id="amount" name="amount" placeholder="0.00" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="payment_method" class="form-label fw-semibold small">Payment Method <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="payment_method" name="payment_method" required onchange="togglePaymentInputs()">
                        <option value="">-- Select Payment Method --</option>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Card">Card</option>
                        <option value="Online Payment">Online Payment</option>
                        <option value="Credit">Credit / Pay Later</option>
                    </select>
                </div>

                <!-- Dynamic input fields -->
                <div class="col-md-8" id="cashAccountSection" style="display: none;">
                    <label for="cash_account_id" class="form-label fw-semibold small">Select Cash Account Drawer <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="cash_account_id" name="cash_account_id">
                        <option value="">-- Select Cash Drawer --</option>
                        <?php foreach ($cashAccounts as $ca): ?>
                            <option value="<?= $ca['id']; ?>"><?= htmlspecialchars($ca['name']); ?> (Balance: LKR <?= number_format($ca['current_balance'], 2); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-8" id="bankAccountSection" style="display: none;">
                    <label for="bank_account_id" class="form-label fw-semibold small">Select Bank Account <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="bank_account_id" name="bank_account_id">
                        <option value="">-- Select Bank Account --</option>
                        <?php foreach ($bankAccounts as $ba): ?>
                            <option value="<?= $ba['id']; ?>"><?= htmlspecialchars($ba['bank_name']); ?> - <?= htmlspecialchars($ba['account_number']); ?> (Balance: LKR <?= number_format($ba['current_balance'], 2); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-8" id="supplierSection" style="display: none;">
                    <label for="supplier_id" class="form-label fw-semibold small">Select Supplier Payee <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="supplier_id" name="supplier_id">
                        <option value="">-- Select Supplier Accounts Payable --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id']; ?>"><?= htmlspecialchars($s['supplier_code']); ?> - <?= htmlspecialchars($s['name_en']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold small">Expense Description</label>
                <textarea class="form-control form-control-sm" id="description" name="description" rows="2" placeholder="Describe the purpose of this expense voucher..."></textarea>
            </div>



            <?php if (!empty($prefilled['source_module']) && $prefilled['source_module'] !== 'GENERAL'): ?>
                <div class="alert alert-info py-2 rounded-3 small mt-3 mb-0">
                    <i class="bi bi-info-circle-fill me-1"></i> Pre-linked to operational source <strong><?= htmlspecialchars($prefilled['source_module']); ?></strong> (Project Ref: <?= htmlspecialchars($prefilled['reference'] ?: '-'); ?>).
                </div>
            <?php endif; ?>

            <div class="modal-footer bg-light p-3 rounded-3 mt-4 gap-2">
                <a href="<?= \Core\Helper::baseUrl('expenses'); ?>" class="btn btn-secondary rounded-pill px-3">Cancel</a>
                <button type="submit" name="action" value="post" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePaymentInputs() {
    const paymentMethod = document.getElementById('payment_method').value;
    const cashSection = document.getElementById('cashAccountSection');
    const bankSection = document.getElementById('bankAccountSection');
    const supplierSection = document.getElementById('supplierSection');
 
    // Reset required states
    document.getElementById('cash_account_id').required = false;
    document.getElementById('bank_account_id').required = false;
    document.getElementById('supplier_id').required = false;
 
    cashSection.style.display = 'none';
    bankSection.style.display = 'none';
    supplierSection.style.display = 'none';
 
    if (paymentMethod === 'Cash') {
        cashSection.style.display = 'block';
        document.getElementById('cash_account_id').required = true;
    } else if (paymentMethod === 'Credit') {
        supplierSection.style.display = 'block';
        document.getElementById('supplier_id').required = true;
    } else if (paymentMethod !== '') {
        bankSection.style.display = 'block';
        document.getElementById('bank_account_id').required = true;
    }
}
</script>
