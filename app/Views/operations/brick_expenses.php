<?php
// Variables available: $project, $expenses, $categories, $costCenters, $cashAccounts, $bankAccounts, $suppliers, $totalExpenses
?>

<?php if ($flashSuccess = \Core\Session::getFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($flashError = \Core\Session::getFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <div class="mb-1">
            <a href="<?= \Core\Helper::baseUrl('operations/brick-manufacturing/view?id=' . $project['id']); ?>" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
        </div>
        <h4 class="fw-bold mb-1 text-dark">Production Project Expenses</h4>
        <p class="text-muted small mb-0"><i class="bi bi-bricks text-danger me-1"></i>Project: <?= htmlspecialchars($project['project_name']); ?></p>
    </div>
    <div>
        <?php if ($project['status'] === 'ACTIVE'): ?>
            <button type="button" class="btn btn-danger rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#recordProjectExpenseModal">
                <i class="bi bi-plus-lg me-1"></i> Record Project Expense
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Financial Summary Row -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, #721c24, #c82333); color: white;">
    <span class="d-block mb-1 text-uppercase fw-bold opacity-75" style="font-size: 0.72rem; letter-spacing: 0.05em;">Total Posted Project Expenses</span>
    <h2 class="fw-bold mb-0 font-monospace">LKR <?= number_format($totalExpenses, 2); ?></h2>
</div>

<!-- Expenses Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-wallet2 text-danger me-2"></i>Expenses Log</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Voucher #</th>
                        <th>Category</th>
                        <th>Payee / Description</th>
                        <th>Status</th>
                        <th>JV Reference</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($expenses)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No expenses recorded for this project yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($expenses as $e): ?>
                            <tr>
                                <td class="font-monospace"><?= date('Y-m-d', strtotime($e['expense_date'])); ?></td>
                                <td class="fw-bold"><a href="<?= \Core\Helper::baseUrl('expenses/view?id=' . $e['id']); ?>" class="text-decoration-none"><?= htmlspecialchars($e['expense_number']); ?></a></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($e['category_name']); ?></span></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($e['payee']); ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($e['description']); ?></small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill <?= ($e['status'] === 'posted') ? 'bg-success' : (($e['status'] === 'approved') ? 'bg-info' : 'bg-warning text-dark'); ?>">
                                        <?= htmlspecialchars(strtoupper($e['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($e['journal_number']): ?>
                                        <a href="<?= \Core\Helper::baseUrl('journals/view?number=' . $e['journal_number']); ?>" class="font-monospace text-decoration-none fw-semibold"><?= htmlspecialchars($e['journal_number']); ?></a>
                                    <?php else: ?>
                                        <span class="text-muted font-monospace">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold font-monospace <?= ($e['status'] === 'posted') ? 'text-danger' : 'text-muted'; ?>">
                                    LKR <?= number_format($e['amount'], 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: RECORD PROJECT EXPENSE -->
<div class="modal fade" id="recordProjectExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white py-3 border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Record Brick Production Expense</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('expenses/store'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                
                <!-- Hidden inputs to link with accounting engine and current project -->
                <input type="hidden" name="source_module" value="BRICK_PRODUCTION">
                <input type="hidden" name="source_type" value="BRICK_PRODUCTION_EXPENSE">
                <input type="hidden" name="project_id" value="<?= $project['id']; ?>">
                <input type="hidden" name="cost_center_id" value="5"> <!-- Brick Manufacturing cost center -->
                <input type="hidden" name="reference_number" value="BRK-PROJ-<?= $project['id']; ?>">
                <input type="hidden" name="redirect_to" value="operations/brick-manufacturing/expenses?id=<?= $project['id']; ?>">
                <input type="hidden" name="action" value="post"> <!-- Save directly posts to ledger -->

                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="expense_date" class="form-label fw-semibold small">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm rounded-3" id="expense_date" name="expense_date" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="expense_category_id" class="form-label fw-semibold small">Expense Category <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm rounded-3" id="expense_category_id" name="expense_category_id" required>
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="payee" class="form-label fw-semibold small">Payee / Recipient <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm rounded-3" id="payee" name="payee" placeholder="e.g. Cement Supplier, Labour team" required>
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label fw-semibold small">Amount (LKR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" class="form-control form-control-sm rounded-3 fw-bold font-monospace" id="amount" name="amount" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="payment_method" class="form-label fw-semibold small">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm rounded-3" id="payment_method" name="payment_method" required onchange="togglePaymentInputs()">
                                <option value="">-- Select Payment --</option>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Card">Card</option>
                                <option value="Online Payment">Online Payment</option>
                                <option value="Credit">Credit / Pay Later</option>
                            </select>
                        </div>

                        <!-- Dynamic payment account drawers -->
                        <div class="col-md-8" id="cashAccountSection" style="display: none;">
                            <label for="cash_account_id" class="form-label fw-semibold small">Select Cash Drawer <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm rounded-3" id="cash_account_id" name="cash_account_id">
                                <option value="">-- Select Cash Drawer --</option>
                                <?php foreach ($cashAccounts as $ca): ?>
                                    <option value="<?= $ca['id']; ?>"><?= htmlspecialchars($ca['name']); ?> (Bal: LKR <?= number_format($ca['current_balance'], 2); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-8" id="bankAccountSection" style="display: none;">
                            <label for="bank_account_id" class="form-label fw-semibold small">Select Bank Account <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm rounded-3" id="bank_account_id" name="bank_account_id">
                                <option value="">-- Select Bank Account --</option>
                                <?php foreach ($bankAccounts as $ba): ?>
                                    <option value="<?= $ba['id']; ?>"><?= htmlspecialchars($ba['bank_name']); ?> - <?= htmlspecialchars($ba['account_number']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-8" id="supplierSection" style="display: none;">
                            <label for="supplier_id" class="form-label fw-semibold small">Select Supplier Payee <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm rounded-3" id="supplier_id" name="supplier_id">
                                <option value="">-- Select Supplier AP --</option>
                                <?php foreach ($suppliers as $s): ?>
                                    <option value="<?= $s['id']; ?>"><?= htmlspecialchars($s['supplier_code']); ?> - <?= htmlspecialchars($s['name_en']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold small">Expense Description (Optional)</label>
                        <textarea class="form-control form-control-sm rounded-3" id="description" name="description" rows="2" placeholder="Describe the purpose of this expense voucher..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold small">Remarks / Notes (Optional)</label>
                        <input type="text" class="form-control form-control-sm rounded-3" id="notes" name="notes" placeholder="Any additional notes...">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4" style="background-color: #dc3545; border-color: #dc3545;">Save Expense</button>
                </div>
            </form>
        </div>
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
