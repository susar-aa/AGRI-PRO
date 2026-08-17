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
        <a href="<?= \Core\Helper::baseUrl('modules/fixed-deposits'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Overview
        </a>
        <h4 class="fw-bold mb-1 text-dark">Fixed Deposit Receipt: <?= htmlspecialchars($fd['deposit_number']); ?></h4>
        <p class="text-muted small mb-0">Investment Status: <strong class="text-success"><?= htmlspecialchars($fd['status']); ?></strong></p>
    </div>
    
    <div class="d-flex gap-2">
        <?php if ($fd['status'] === 'MATURED'): ?>
            <button class="btn btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#matureModal">
                <i class="bi bi-send me-1"></i> Process Maturity
            </button>
        <?php endif; ?>
        <?php if ($fd['status'] === 'ACTIVE'): ?>
            <button class="btn btn-outline-warning rounded-pill px-4 text-dark" data-bs-toggle="modal" data-bs-target="#prematureModal">
                <i class="bi bi-x-circle me-1"></i> Close FD Early
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Receipt summary details card -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt-cutoff text-success me-2"></i> Deposit Details</h6>
                <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill"><?= htmlspecialchars($fd['status']); ?></span>
            </div>
            <div class="card-body pt-0 small">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Cooperative Member</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($fd['member_name']); ?> (<?= htmlspecialchars($fd['membership_no']); ?>)</span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Principal Amount</small>
                        <span class="fw-bold text-dark font-monospace">LKR <?= number_format($fd['maturity_amount'] - $fd['expected_interest'], 2); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Interest Rate / Term</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($fd['interest_rate']); ?>% / <?= htmlspecialchars($fd['term_months']); ?> Months</span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Expected Interest</small>
                        <span class="fw-bold text-success font-monospace">LKR <?= number_format($fd['expected_interest'], 2); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Maturity Date</small>
                        <span class="fw-bold text-primary font-monospace"><?= htmlspecialchars($fd['maturity_date']); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Maturity Amount</small>
                        <span class="fw-bold text-primary font-monospace fs-5">LKR <?= number_format($fd['maturity_amount'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <?php if (in_array($fd['status'], ['CLOSED', 'PREMATURELY_CLOSED'])): ?>
            <div class="alert alert-secondary border rounded-4 p-4">
                <h6 class="fw-bold alert-heading mb-2"><i class="bi bi-info-circle-fill me-2"></i> Deposit Closure Details</h6>
                <div class="row g-3 small">
                    <div class="col-6">
                        <span class="text-muted">Closure Date:</span>
                        <strong class="text-dark d-block"><?= htmlspecialchars($fd['closure_date'] ?: '-'); ?></strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted">Final Payable Payout:</span>
                        <strong class="text-success font-monospace d-block">LKR <?= number_format($fd['final_payable_amount'] ?: $fd['maturity_amount'], 2); ?></strong>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <span class="text-muted">Reason:</span>
                        <p class="mb-0 fw-semibold text-dark"><?= htmlspecialchars($fd['closure_reason'] ?: 'Maturity processed'); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Audit lines column -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-success me-2"></i> Audit Specifications</h6>
            </div>
            <div class="card-body p-3 pt-0 small">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Original Journal:</span>
                        <span class="fw-bold font-monospace text-dark"><?= htmlspecialchars($fd['payment_journal'] ?: '-'); ?></span>
                    </li>
                    <?php if ($fd['maturity_journal']): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                            <span class="text-secondary">Payout Journal:</span>
                            <span class="fw-bold font-monospace text-dark"><?= htmlspecialchars($fd['maturity_journal']); ?></span>
                        </li>
                    <?php endif; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Funding Method:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($fd['payment_method']); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Process Maturity -->
<div class="modal fade" id="matureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-send-fill me-2"></i> Process Deposit Maturity Payout</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/fixed-deposits/mature'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $fd['id']; ?>">
                <div class="modal-body p-4">
                    <p>Process maturity and release principal + interest payouts to member.</p>
                    <div class="row bg-light rounded-3 p-3 g-2 small border mb-3">
                        <div class="col-6">
                            <span class="text-muted d-block">Original Principal</span>
                            <strong>LKR <?= number_format($fd['maturity_amount'] - $fd['expected_interest'], 2); ?></strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Expected Interest</span>
                            <strong class="text-success">LKR <?= number_format($fd['expected_interest'], 2); ?></strong>
                        </div>
                        <div class="col-12 border-top pt-2">
                            <span class="text-muted d-block">Total Payout Amount</span>
                            <strong class="text-primary fs-5">LKR <?= number_format($fd['maturity_amount'], 2); ?></strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Payout Payment Method</label>
                        <select class="form-select" name="payout_method" required>
                            <option value="Cash">Cash Drawer</option>
                            <option value="Bank Transfer">Bank Account</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Payout Date</label>
                        <input type="date" class="form-control" name="payout_date" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Post Payout Journal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Premature Closure -->
<div class="modal fade" id="prematureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Close FD Prematurely</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/fixed-deposits/premature-close'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $fd['id']; ?>">
                <div class="modal-body p-4">
                    <p class="text-danger fw-semibold">Note: Premature closure overrides interest payout calculations. Reverses original investment records.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Original Principal (LKR)</label>
                        <input type="number" class="form-control font-monospace" id="originalPrincipal" value="<?= $fd['maturity_amount'] - $fd['expected_interest']; ?>" readonly>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold small">Agreed Interest Rate (%)</label>
                            <input type="text" class="form-control font-monospace" value="<?= htmlspecialchars($fd['interest_rate']); ?>%" readonly>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold small">Early Closure Interest Rate (%)</label>
                            <input type="number" step="0.01" class="form-control font-monospace" name="interest_adjustment_rate" id="adjRate" value="4.00" oninput="calculatePrematureTotal()" required>
                        </div>
                    </div>
                    <input type="hidden" name="interest_adjustment" id="adjInterest" value="0.00">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Final Payable Payout Amount (LKR)</label>
                        <input type="number" step="0.01" class="form-control font-monospace fw-bold text-success" name="final_payable_amount" id="finalPayable" value="<?= $fd['maturity_amount'] - $fd['expected_interest']; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Payout Method</label>
                        <select class="form-select" name="payout_method" required>
                            <option value="Cash">Cash Drawer</option>
                            <option value="Bank Transfer">Bank Account</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Closure Date</label>
                        <input type="date" class="form-control" name="closure_date" id="closureDate" value="<?= date('Y-m-d'); ?>" oninput="calculatePrematureTotal()" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Reason for Early Closure</label>
                        <textarea class="form-control" name="closure_reason" rows="2" placeholder="Explain early closure..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-dark">Confirm Closure</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calculatePrematureTotal() {
    const principalInput = document.getElementById('originalPrincipal');
    const rateInput     = document.getElementById('adjRate');
    const adjInterestInput  = document.getElementById('adjInterest');
    const finalPayableInput = document.getElementById('finalPayable');

    if (!principalInput || !rateInput || !adjInterestInput || !finalPayableInput) return;

    const principal  = parseFloat(principalInput.value) || 0;
    const rate       = parseFloat(rateInput.value) || 0;
    const termMonths = <?= (int)$fd['term_months']; ?>;

    // Simple interest based on original term and early closure rate
    // Interest = Principal × Rate% × (Term Months / 12)
    const interest = principal * (rate / 100) * (termMonths / 12);
    const finalVal = principal + interest;

    adjInterestInput.value  = interest.toFixed(2);
    finalPayableInput.value = finalVal.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    calculatePrematureTotal();

    const adjRateInput = document.getElementById('adjRate');
    if (adjRateInput) {
        adjRateInput.addEventListener('input', calculatePrematureTotal);
    }
});

</script>
