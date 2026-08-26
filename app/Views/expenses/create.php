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
                <div class="col-md-4">
                    <label for="expense_date" class="form-label fw-semibold small">Expense Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm" id="expense_date" name="expense_date" value="<?= date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="cost_center_id" class="form-label fw-semibold small">Operation Management</label>
                    <select class="form-select form-select-sm" id="cost_center_id" name="cost_center_id">
                        <option value="">-- Select Operation --</option>
                        <?php foreach ($costCenters as $op): ?>
                            <option value="<?= $op['id']; ?>" <?= (isset($prefilled['cost_center_id']) && $prefilled['cost_center_id'] == $op['id']) ? 'selected' : ''; ?>>
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
                                <option value="<?= $cat['id']; ?>">
                                    <?= htmlspecialchars($cat['name']); ?>
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
                    <select class="form-control" id="payee" name="payee" required></select>
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
                    </select>
                </div>

                <!-- Hidden cash account since we always use cash in hand -->
                <input type="hidden" id="cash_account_id" name="cash_account_id" value="<?= $cashAccounts[0]['id'] ?? 1; ?>">

                <div class="col-md-8" id="bankAccountSection" style="display: none;">
                    <label for="bank_account_id" class="form-label fw-semibold small">Select Bank Account <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="bank_account_id" name="bank_account_id">
                        <option value="">-- Select Bank Account --</option>
                        <?php foreach ($bankAccounts as $ba): ?>
                            <option value="<?= $ba['id']; ?>"><?= htmlspecialchars($ba['bank_name']); ?> - <?= htmlspecialchars($ba['account_number']); ?> (Balance: LKR <?= number_format($ba['current_balance'], 2); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Issued Cheque Details Section -->
            <div class="card border border-dashed rounded-4 p-3 bg-light mb-3" id="chequeSection" style="display: none;">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-wallet2 me-1 text-success"></i> Issued Cheque Specifications</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="cheque_number_input" class="form-label fw-semibold small">Cheque Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="cheque_number_input" name="cheque_number_input" placeholder="e.g. 010204">
                    </div>
                    <div class="col-md-4">
                        <label for="cheque_bank_name" class="form-label fw-semibold small">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="cheque_bank_name" name="cheque_bank_name" placeholder="e.g. Bank of Ceylon">
                    </div>
                    <div class="col-md-4">
                        <label for="cheque_date" class="form-label fw-semibold small">Cheque Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="cheque_date" name="cheque_date" value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="mt-3 text-muted small">
                    <i class="bi bi-info-circle me-1"></i> The cheque will be recorded as ISSUED. Ensure you also select the Source Bank Account above from which this cheque is drawn.
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
    const bankSection = document.getElementById('bankAccountSection');
    const chequeSection = document.getElementById('chequeSection');
 
    // Reset required states
    document.getElementById('bank_account_id').required = false;
    document.getElementById('cheque_number_input').required = false;
    document.getElementById('cheque_bank_name').required = false;
 
    bankSection.style.display = 'none';
    if(chequeSection) chequeSection.style.display = 'none';
 
    if (paymentMethod === 'Cash') {
        // No additional fields required for cash
    } else if (paymentMethod === 'Bank Transfer') {
        bankSection.style.display = 'block';
        document.getElementById('bank_account_id').required = true;
    } else if (paymentMethod === 'Cheque') {
        bankSection.style.display = 'block'; // Need source bank
        if(chequeSection) chequeSection.style.display = 'block';
        document.getElementById('bank_account_id').required = true;
        document.getElementById('cheque_number_input').required = true;
        document.getElementById('cheque_bank_name').required = true;
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
