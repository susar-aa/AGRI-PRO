<?php
// Variables available: $project, $expenses, $categories, $costCenters, $cashAccounts, $bankAccounts, $suppliers
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

<div class="mb-3">
    <a href="<?= \Core\Helper::baseUrl('operations/plantation/view?id=' . $project['id']); ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Back to Project Dashboard
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Plantation Expenses Log</h4>
        <p class="text-muted small mb-0">Project: <strong class="text-dark"><?= htmlspecialchars($project['project_name']); ?></strong> &bull; Location: <?= htmlspecialchars($project['location']); ?></p>
    </div>
    <div>
        <!-- Trigger Modal instead of redirecting -->
        <button type="button" class="btn btn-danger rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#recordProjectExpenseModal">
            <i class="bi bi-plus-lg me-1"></i> Record Project Expense
        </button>
    </div>
</div>

<!-- Expenses Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-journal-text text-danger me-2"></i>Expense Vouchers Ledger</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Voucher #</th>
                        <th>Category</th>
                        <th>Payee</th>
                        <th>Description</th>
                        <th class="text-end">Amount (LKR)</th>
                        <th class="text-center">Status</th>
                        <th style="width: 80px;" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($expenses)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-info-circle fs-3 d-block mb-1 text-secondary opacity-50"></i>
                                No expenses recorded under this project.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($expenses as $e): ?>
                            <tr>
                                <td><?= date('m/d/Y', strtotime($e['expense_date'])); ?></td>
                                <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($e['expense_number']); ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($e['category_name']); ?></span></td>
                                <td><?= htmlspecialchars($e['payee']); ?></td>
                                <td class="text-muted text-truncate" style="max-width: 250px;"><?= htmlspecialchars($e['description'] ?: '-'); ?></td>
                                <td class="text-end fw-bold font-monospace text-dark">LKR <?= number_format($e['amount'], 2); ?></td>
                                <td class="text-center">
                                    <?php 
                                    $statusClass = 'bg-secondary';
                                    if ($e['status'] === 'posted') $statusClass = 'bg-success';
                                    elseif ($e['status'] === 'pending_approval') $statusClass = 'bg-warning text-dark';
                                    elseif ($e['status'] === 'draft') $statusClass = 'bg-info text-dark';
                                    ?>
                                    <span class="badge <?= $statusClass; ?> text-capitalize"><?= htmlspecialchars($e['status']); ?></span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= \Core\Helper::baseUrl('expenses/view?id=' . $e['id']); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2">
                                        View
                                    </a>
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
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Record Plantation Expense</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('expenses/store'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                
                <!-- Hidden inputs to link with accounting engine and current project -->
                <input type="hidden" name="source_module" value="PLANTATION">
                <input type="hidden" name="source_type" value="PLANTATION_EXPENSE">
                <input type="hidden" name="project_id" value="<?= $project['id']; ?>">
                <input type="hidden" name="cost_center_id" value="4"> <!-- Plantation cost center -->
                <input type="hidden" name="reference_number" value="PLANT-PROJ-<?= $project['id']; ?>">
                <input type="hidden" name="redirect_to" value="operations/plantation/expenses?id=<?= $project['id']; ?>">
                <input type="hidden" name="action" value="post"> <!-- Save directly posts to ledger -->

                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="expense_date" class="form-label fw-semibold small">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="expense_date" name="expense_date" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="expense_category_id" class="form-label fw-semibold small">Expense Category <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="expense_category_id" name="expense_category_id" required onchange="togglePayeeInput()">
                                <option value="" data-name="">-- Select Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id']; ?>" data-name="<?= htmlspecialchars($cat['name']); ?>"><?= htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="payee" class="form-label fw-semibold small">Payee / Recipient <span class="text-danger">*</span></label>
                            
                            <!-- Standard Payee Input -->
                            <input type="text" class="form-control form-control-sm" id="payee" name="payee" placeholder="e.g. Ceylon Electricity Board, fuel station" required>
                            
                            <!-- Dynamic Staff Select (Hidden initially) -->
                            <select class="form-select form-select-sm" id="staff_payee" name="payee_staff_id" style="display: none;">
                                <option value="">-- Select Staff Member --</option>
                                <?php foreach ($staffMembers as $sm): ?>
                                    <option value="<?= htmlspecialchars($sm['name_en']); ?>"><?= htmlspecialchars($sm['name_en']); ?></option>
                                <?php endforeach; ?>
                            </select>
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
                            <select class="form-select form-select-sm" id="cash_account_id" name="cash_account_id">
                                <option value="">-- Select Cash Drawer --</option>
                                <?php foreach ($cashAccounts as $ca): ?>
                                    <option value="<?= $ca['id']; ?>"><?= htmlspecialchars($ca['name']); ?> (Bal: LKR <?= number_format($ca['current_balance'], 2); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-8" id="bankAccountSection" style="display: none;">
                            <label for="bank_account_id" class="form-label fw-semibold small">Select Bank Account <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="bank_account_id" name="bank_account_id">
                                <option value="">-- Select Bank Account --</option>
                                <?php foreach ($bankAccounts as $ba): ?>
                                    <option value="<?= $ba['id']; ?>"><?= htmlspecialchars($ba['bank_name']); ?> - <?= htmlspecialchars($ba['account_number']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-8" id="supplierSection" style="display: none;">
                            <label for="supplier_id" class="form-label fw-semibold small">Select Supplier Payee <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="supplier_id" name="supplier_id">
                                <option value="">-- Select Supplier AP --</option>
                                <?php foreach ($suppliers as $s): ?>
                                    <option value="<?= $s['id']; ?>"><?= htmlspecialchars($s['supplier_code']); ?> - <?= htmlspecialchars($s['name_en']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold small">Expense Description (Optional)</label>
                        <textarea class="form-control form-control-sm" id="description" name="description" rows="2" placeholder="Describe the purpose of this expense voucher..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4" style="background-color: #dc3545; border-color: #dc3545;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePaymentInputs() {
    const val = document.getElementById('payment_method').value;
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
 
    if (val === 'Cash') {
        cashSection.style.display = 'block';
        document.getElementById('cash_account_id').required = true;
    } else if (val === 'Bank Transfer' || val === 'Cheque' || val === 'Card' || val === 'Online Payment') {
        bankSection.style.display = 'block';
        document.getElementById('bank_account_id').required = true;
    } else if (val === 'Credit') {
        supplierSection.style.display = 'block';
        document.getElementById('supplier_id').required = true;
    }
}

function togglePayeeInput() {
    const catSelect = document.getElementById('expense_category_id');
    const selectedOption = catSelect.options[catSelect.selectedIndex];
    const catName = selectedOption.getAttribute('data-name');
    
    const payeeInput = document.getElementById('payee');
    const staffSelect = document.getElementById('staff_payee');
    
    if (catName === 'Labour charges') {
        payeeInput.style.display = 'none';
        payeeInput.required = false;
        // Clear standard input
        payeeInput.value = '';
        
        staffSelect.style.display = 'block';
        staffSelect.required = true;
        staffSelect.name = 'payee'; // Override name so it submits as payee
        payeeInput.name = 'payee_ignore';
    } else {
        payeeInput.style.display = 'block';
        payeeInput.required = true;
        payeeInput.name = 'payee'; // Restore standard name
        
        staffSelect.style.display = 'none';
        staffSelect.required = false;
        staffSelect.name = 'payee_staff_id';
        staffSelect.value = '';
    }
}
</script>
