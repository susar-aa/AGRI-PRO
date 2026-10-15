<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('expenses/view?id=' . $expense['id']); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Voucher
        </a>
        <h4 class="fw-bold mb-1 text-dark">Edit Expense Voucher: <span class="font-monospace text-primary"><?= htmlspecialchars($expense['expense_number']); ?></span></h4>
        <p class="text-muted small mb-0">Modify expense voucher details. All changes are logged in the financial audit trail.</p>
    </div>
</div>

<?php if ($expense['status'] === 'posted'): ?>
    <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center" role="alert">
        <i class="bi bi-shield-lock-fill fs-3 me-3 text-warning"></i>
        <div>
            <h6 class="fw-bold mb-1">Safe Double-Entry Ledger Synchronization Active</h6>
            <span class="small">This voucher is <strong>POSTED</strong> (Linked Journal: <span class="fw-bold text-dark font-monospace"><?= htmlspecialchars($expense['journal_number'] ?: ('#' . $expense['journal_entry_id'])); ?></span>). Modifying the date, amount, category, or payment method will atomically update the general ledger entries and adjust cash/bank account balances.</span>
        </div>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="<?= \Core\Helper::baseUrl('expenses/update'); ?>" method="POST" enctype="multipart/form-data" id="editExpenseForm">
            <?= \Core\CSRF::getFormField(); ?>
            <input type="hidden" name="id" value="<?= $expense['id']; ?>">

            <!-- Hidden operational links -->
            <input type="hidden" name="project_id" value="<?= htmlspecialchars($expense['project_id'] ?? ''); ?>">
            <input type="hidden" name="batch_id" value="<?= htmlspecialchars($expense['batch_id'] ?? ''); ?>">
            <input type="hidden" name="service_job_id" value="<?= htmlspecialchars($expense['service_job_id'] ?? ''); ?>">
            <input type="hidden" name="machinery_id" value="<?= htmlspecialchars($expense['machinery_id'] ?? ''); ?>">
            <input type="hidden" name="machinery_rental_id" value="<?= htmlspecialchars($expense['machinery_rental_id'] ?? ''); ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="expense_date" class="form-label fw-semibold small">Expense Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm" id="expense_date" name="expense_date" value="<?= htmlspecialchars($expense['expense_date']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="cost_center_id" class="form-label fw-semibold small">Operation Management (Cost Center) <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="cost_center_id" name="cost_center_id" required>
                        <option value="">-- Select Operation --</option>
                        <?php foreach ($costCenters as $op): ?>
                            <option value="<?= $op['id']; ?>" <?= ($expense['cost_center_id'] == $op['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($op['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="expense_category_id" class="form-label fw-semibold small">Expense Category <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <select class="form-select" id="expense_category_id" name="expense_category_id" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id']; ?>" <?= ($expense['expense_category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($cat['name']); ?> (<?= htmlspecialchars($cat['account_code'] ?? ''); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#addCategoryModal" title="Add New Category"><i class="bi bi-plus-lg"></i></button>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="payee" class="form-label fw-semibold small">Payee / Recipient <span class="text-danger">*</span></label>
                    <select class="form-control" id="payee" name="payee" required>
                        <option value="<?= htmlspecialchars($expense['payee']); ?>" selected><?= htmlspecialchars($expense['payee']); ?></option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="amount" class="form-label fw-semibold small">Amount (LKR) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" class="form-control form-control-sm fw-bold font-monospace" id="amount" name="amount" value="<?= htmlspecialchars($expense['amount']); ?>" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="payment_method" class="form-label fw-semibold small">Payment Method <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="payment_method" name="payment_method" required onchange="togglePaymentInputs()">
                        <option value="Cash" <?= $expense['payment_method'] === 'Cash' ? 'selected' : ''; ?>>Cash</option>
                        <option value="Bank Transfer" <?= $expense['payment_method'] === 'Bank Transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                        <option value="Cheque" <?= $expense['payment_method'] === 'Cheque' ? 'selected' : ''; ?>>Cheque</option>
                        <option value="Credit" <?= $expense['payment_method'] === 'Credit' ? 'selected' : ''; ?>>Credit (Accounts Payable)</option>
                    </select>
                </div>

                <!-- Hidden cash account since we always use cash in hand -->
                <input type="hidden" id="cash_account_id" name="cash_account_id" value="<?= $expense['cash_account_id'] ?: ($cashAccounts[0]['id'] ?? 1); ?>">

                <div class="col-md-8" id="bankAccountSection" style="<?= in_array($expense['payment_method'], ['Bank Transfer', 'Cheque']) ? 'display: block;' : 'display: none;'; ?>">
                    <label for="bank_account_id" class="form-label fw-semibold small">Source Bank Account <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="bank_account_id" name="bank_account_id">
                        <option value="">-- Select Bank Account --</option>
                        <?php foreach ($bankAccounts as $ba): ?>
                            <option value="<?= $ba['id']; ?>" <?= ($expense['bank_account_id'] == $ba['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($ba['bank_name']); ?> - <?= htmlspecialchars($ba['account_number']); ?> (Balance: LKR <?= number_format($ba['current_balance'], 2); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="reference_number" class="form-label fw-semibold small">Reference / Invoice #</label>
                    <input type="text" class="form-control form-control-sm" id="reference_number" name="reference_number" value="<?= htmlspecialchars($expense['reference_number'] ?? ''); ?>" placeholder="e.g. INV-10492">
                </div>
                <div class="col-md-6">
                    <label for="edit_reason" class="form-label fw-semibold small text-primary">Reason for Editing <span class="text-muted">(Audit Trail Justification)</span></label>
                    <input type="text" class="form-control form-control-sm" id="edit_reason" name="edit_reason" placeholder="e.g. Corrected typo in payee / amount / cost center reclassification">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold small">Expense Description</label>
                <textarea class="form-control form-control-sm" id="description" name="description" rows="2" placeholder="Describe the purpose of this expense voucher..."><?= htmlspecialchars($expense['description'] ?? ''); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label fw-semibold small">Internal Notes</label>
                <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2" placeholder="Optional internal notes..."><?= htmlspecialchars($expense['notes'] ?? ''); ?></textarea>
            </div>

            <!-- Existing & New Attachments -->
            <div class="card border rounded-3 p-3 bg-light mb-3">
                <h6 class="fw-bold mb-2 text-dark small"><i class="bi bi-paperclip me-1"></i> Receipts & Documents</h6>
                <?php if (!empty($expense['attachments'])): ?>
                    <div class="mb-2">
                        <span class="small text-muted me-2">Current Files:</span>
                        <?php foreach ($expense['attachments'] as $att): ?>
                            <a href="<?= \Core\Helper::baseUrl($att['file_path']); ?>" target="_blank" class="badge bg-white text-dark border me-1 text-decoration-none py-1 px-2">
                                <i class="bi bi-file-earmark-text text-danger me-1"></i><?= htmlspecialchars($att['original_name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div>
                    <label for="attachment_file" class="form-label small text-muted mb-1">Upload New Document (Optional, PDF / JPG / PNG, Max 5MB):</label>
                    <input type="file" class="form-control form-control-sm bg-white" id="attachment_file" name="attachment_file">
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 mt-4">
                <a href="<?= \Core\Helper::baseUrl('expenses/view?id=' . $expense['id']); ?>" class="btn btn-secondary rounded-pill px-4">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-success rounded-pill px-5" style="background-color: #1b4332; border-color: #1b4332;">
                    <i class="bi bi-check-circle-fill me-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePaymentInputs() {
    const paymentMethod = document.getElementById('payment_method').value;
    const bankSection = document.getElementById('bankAccountSection');
    const bankInput = document.getElementById('bank_account_id');

    if (paymentMethod === 'Bank Transfer' || paymentMethod === 'Cheque') {
        bankSection.style.display = 'block';
        bankInput.required = true;
    } else {
        bankSection.style.display = 'none';
        bankInput.required = false;
    }
}
</script>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>New Category</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label fw-semibold small">Category Name</label>
                    <input type="text" id="new_category_name" class="form-control form-control-sm" placeholder="e.g. Refreshments">
                    <div id="cat_err" class="text-danger small mt-1" style="display:none;"></div>
                </div>
            </div>
            <div class="modal-footer p-2 bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-success px-3" id="btnSaveCategory">Save</button>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single { height: 31px; padding: 2px 0px; font-size: 0.875rem; border-color: #dee2e6; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 28px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 25px; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#payee').select2({
        tags: true,
        placeholder: "Search payee or type new name...",
        allowClear: true,
        minimumInputLength: 0,
        ajax: {
            url: '<?= \Core\Helper::baseUrl("expenses/api/search-payees"); ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        }
    });

    $('#btnSaveCategory').click(function() {
        let name = $('#new_category_name').val().trim();
        let err = $('#cat_err');
        
        if (!name) {
            err.text('Please enter a name').show();
            return;
        }
        
        $(this).prop('disabled', true).text('Saving...');
        err.hide();

        $.ajax({
            url: '<?= \Core\Helper::baseUrl("expenses/api/add-category"); ?>',
            type: 'POST',
            data: {
                name: name,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            success: function(res) {
                if(res.success) {
                    let newOption = new Option(res.name, res.id, true, true);
                    $('#expense_category_id').append(newOption).trigger('change');
                    $('#addCategoryModal').modal('hide');
                    $('#new_category_name').val('');
                } else {
                    err.text(res.message || 'Error occurred').show();
                }
            },
            error: function() {
                err.text('Network error. Try again.').show();
            },
            complete: function() {
                $('#btnSaveCategory').prop('disabled', false).text('Save');
            }
        });
    });
});
</script>
