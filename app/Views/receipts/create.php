<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>

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
        <a href="<?= \Core\Helper::baseUrl('receipts'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Ledger
        </a>
        <h4 class="fw-bold mb-1 text-dark">Receive Customer Payment</h4>
        <p class="text-muted small mb-0">Record collection receipts from customer accounts. Toggles dynamically for Cash, Bank deposit, or Received Cheque.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="<?= \Core\Helper::baseUrl('receipts/store'); ?>" method="POST" id="receiptForm">
            <?= \Core\CSRF::getFormField(); ?>
            <input type="hidden" name="payment_type" value="RECEIPT">

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="party_id" class="form-label fw-semibold">Select Customer / Member <span class="text-danger">*</span></label>
                    <span id="customer_balance_display" class="float-end fw-bold text-primary small" style="display: none;"></span>
                    <select class="form-select select2" id="party_id" name="party_id" required>
                        <option value="">-- Select Account --</option>
                        <?php foreach ($customers as $cust): ?>
                            <option value="<?= $cust['id']; ?>" <?= (!empty($selectedParty) && $selectedParty['id'] == $cust['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cust['party_code']); ?> - <?= htmlspecialchars($cust['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="income_account_id" class="form-label fw-semibold">Income Category <span class="text-danger">*</span></label>
                    <select class="form-select" id="income_account_id" name="income_account_id" required>
                        <?php foreach ($incomeAccounts as $acc): 
                            $name = htmlspecialchars($acc['account_name']);
                            if ($acc['id'] == 12) $name = "Customer Invoice Payment";
                            elseif ($acc['id'] == 62) $name = "Member's Registration Fee";
                            elseif ($acc['id'] == 25) $name = "Member's Share Capital";
                            elseif ($acc['id'] == 37) $name = "Other Income";
                        ?>
                            <option value="<?= $acc['id']; ?>" <?= ($acc['id'] == 12) ? 'selected' : ''; ?>>
                                <?= $name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="payment_date" class="form-label fw-semibold">Receipt Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?= date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="amount" class="form-label fw-semibold">Collection Amount (LKR) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" class="form-control fw-bold text-success" id="amount" name="amount" placeholder="0.00" required>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="payment_method" class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                    <select class="form-select" id="payment_method" name="payment_method" required onchange="togglePaymentInputs()">
                        <option value="">-- Select Method --</option>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>

                <!-- Hidden cash account since there's only one -->
                <input type="hidden" id="cash_account_id" name="cash_account_id" value="<?= $cashAccounts[0]['id'] ?? 1; ?>">

                <div class="col-md-8" id="bankAccountSection" style="display: none;">
                    <label for="bank_account_id" class="form-label fw-semibold">Select Destination Bank Account <span class="text-danger">*</span></label>
                    <select class="form-select" id="bank_account_id" name="bank_account_id">
                        <option value="">-- Select Bank Account --</option>
                        <?php foreach ($bankAccounts as $ba): ?>
                            <option value="<?= $ba['id']; ?>"><?= htmlspecialchars($ba['bank_name']); ?> - <?= htmlspecialchars($ba['account_number']); ?> (Balance: LKR <?= number_format($ba['current_balance'], 2); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Dynamic Cheque Details Section -->
            <div class="card border border-dashed rounded-4 p-3 bg-light mb-3" id="chequeSection" style="display: none;">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-wallet2 me-1 text-success"></i> Received Cheque Specifications</h6>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Cheque Source Option</label>
                    <div class="d-flex gap-3 small">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cheque_link_type" id="cheque_link_new" value="new" checked onchange="toggleChequeSource()">
                            <label class="form-check-label" for="cheque_link_new">Enter New Cheque</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cheque_link_type" id="cheque_link_existing" value="existing" onchange="toggleChequeSource()">
                            <label class="form-check-label" for="cheque_link_existing">Link Existing Received Cheque</label>
                        </div>
                    </div>
                </div>

                <!-- New Cheque Inputs -->
                <div class="row g-3" id="newChequeInputs">
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

                <!-- Existing Cheques Dropdown -->
                <div id="existingChequeInput" style="display: none;">
                    <label for="cheque_id" class="form-label fw-semibold small">Select Undeposited Cheque <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="cheque_id" name="cheque_id">
                        <option value="">-- Select Cheque --</option>
                        <?php foreach ($cheques as $ch): ?>
                            <option value="<?= $ch['id']; ?>" data-amount="<?= $ch['amount']; ?>">
                                No: <?= htmlspecialchars($ch['cheque_number']); ?> - <?= htmlspecialchars($ch['bank_name']); ?> (LKR <?= number_format($ch['amount'], 2); ?>) - <?= htmlspecialchars($ch['customer_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label fw-semibold">Notes / Description</label>
                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="e.g. Settlement of customer invoice"></textarea>
            </div>



            <div class="modal-footer bg-light p-3 rounded-3 mt-4 gap-2">
                <a href="<?= \Core\Helper::baseUrl('receipts'); ?>" class="btn btn-secondary rounded-pill px-3">Cancel</a>
                <button type="submit" name="action" value="post" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Post Collection</button>
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
    document.getElementById('cheque_id').required = false;
    document.getElementById('cheque_number_input').required = false;
    document.getElementById('cheque_bank_name').required = false;

    bankSection.style.display = 'none';
    if(chequeSection) chequeSection.style.display = 'none';

    if (paymentMethod === 'Cash') {
        // Handled silently by hidden input
    } else if (paymentMethod === 'Bank Transfer') {
        bankSection.style.display = 'block';
        document.getElementById('bank_account_id').required = true;
    } else if (paymentMethod === 'Cheque') {
        chequeSection.style.display = 'block';
        toggleChequeSource();
    }
}

function toggleChequeSource() {
    const linkType = document.querySelector('input[name="cheque_link_type"]:checked').value;
    const newInputs = document.getElementById('newChequeInputs');
    const existingInput = document.getElementById('existingChequeInput');

    document.getElementById('cheque_number_input').required = false;
    document.getElementById('cheque_bank_name').required = false;
    document.getElementById('cheque_id').required = false;

    if (linkType === 'new') {
        newInputs.style.display = 'flex';
        existingInput.style.display = 'none';
        document.getElementById('cheque_number_input').required = true;
        document.getElementById('cheque_bank_name').required = true;
    } else {
        newInputs.style.display = 'none';
        existingInput.style.display = 'block';
        document.getElementById('cheque_id').required = true;
    }
}

// Automatically update receipt amount if an existing cheque is selected
document.getElementById('cheque_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const amount = selectedOption.getAttribute('data-amount');
    if (amount) {
        document.getElementById('amount').value = amount;
    }
});
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Select Account --",
            allowClear: true
        });

        $('#party_id').on('change', function() {
            var partyId = $(this).val();
            var balanceDisplay = $('#customer_balance_display');
            
            if (partyId) {
                $.ajax({
                    url: '<?= \Core\Helper::baseUrl('parties/api/balance') ?>',
                    type: 'GET',
                    data: { id: partyId },
                    success: function(response) {
                        var bal = parseFloat(response.balance);
                        balanceDisplay.show();
                        if (bal > 0) {
                            balanceDisplay.html('Outstanding: <span class="text-danger">LKR ' + bal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>');
                        } else if (bal < 0) {
                            balanceDisplay.html('Advance: <span class="text-success">LKR ' + Math.abs(bal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>');
                        } else {
                            balanceDisplay.html('Balance: <span class="text-secondary">LKR 0.00</span>');
                        }
                    },
                    error: function() {
                        balanceDisplay.hide();
                    }
                });
            } else {
                balanceDisplay.hide();
            }
        });
        
        // Trigger if already selected
        if ($('#party_id').val()) {
            $('#party_id').trigger('change');
        }
    });
</script>
