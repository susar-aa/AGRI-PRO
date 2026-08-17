<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('modules/fixed-deposits'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Overview
        </a>
        <h4 class="fw-bold mb-1 text-dark">Create Fixed Deposit investment</h4>
        <p class="text-muted small mb-0">Record new Fixed Deposit for a Society Member, calculate interest, and post GL journal entries.</p>
    </div>
</div>

<form action="<?= \Core\Helper::baseUrl('modules/fixed-deposits/store'); ?>" method="POST" id="fdForm">
    <?= \Core\CSRF::getFormField(); ?>

    <div class="row g-4">
        <!-- Input parameters -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge text-success me-2"></i> 1. Select Society Member</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Choose Member <span class="text-danger">*</span></label>
                        <select class="form-select" name="member_id" id="memberSelect" onchange="updateMemberDetails()" required>
                            <option value="">-- Select Member --</option>
                            <?php foreach ($members as $m): ?>
                                <option value="<?= $m['id']; ?>" data-nic="<?= htmlspecialchars($m['nic']); ?>" data-phone="<?= htmlspecialchars($m['phone']); ?>" data-no="<?= htmlspecialchars($m['membership_no']); ?>">
                                    <?= htmlspecialchars($m['full_name']); ?> (<?= htmlspecialchars($m['membership_no']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="memberMetaDetails" style="display: none;">
                        <div class="row bg-light rounded-3 p-3 g-2 small border mb-3">
                            <div class="col-4">
                                <span class="text-muted d-block">Membership Number</span>
                                <strong class="text-success font-monospace" id="metaNo"></strong>
                            </div>
                            <div class="col-4">
                                <span class="text-muted d-block">NIC / ID</span>
                                <strong id="metaNic"></strong>
                            </div>
                            <div class="col-4">
                                <span class="text-muted d-block">Contact phone</span>
                                <strong id="metaPhone"></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Investment fields -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-safe text-success me-2"></i> 2. Investment Parameters</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Deposit Amount (LKR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="1" class="form-control font-monospace" name="deposit_amount" id="fdAmount" value="50000.00" oninput="calculateFD()" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="start_date" id="fdStart" value="<?= date('Y-m-d'); ?>" oninput="calculateFD()" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">FD Period / Term <span class="text-danger">*</span></label>
                            <select class="form-select" name="term_months" id="fdTerm" onchange="calculateFD()" required>
                                <option value="3">3 Months</option>
                                <option value="6">6 Months</option>
                                <option value="12" selected>12 Months</option>
                                <option value="24">24 Months</option>
                                <option value="36">36 Months</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Annual Interest Rate (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control font-monospace" name="interest_rate" id="fdRate" value="12.00" oninput="calculateFD()" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Notes / Remarks</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Additional details..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic calculations & checkout sidebar -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-calculator text-success me-2"></i> Calculated Forecast</h6>
                </div>
                <div class="card-body pt-0 small">
                    <div class="row g-2 border-bottom pb-2 mb-2">
                        <div class="col-6 text-secondary">Maturity Date:</div>
                        <div class="col-6 text-end fw-bold text-dark font-monospace" id="calcMaturityDate"></div>
                    </div>
                    <div class="row g-2 border-bottom pb-2 mb-2">
                        <div class="col-6 text-secondary">Principal Amount:</div>
                        <div class="col-6 text-end fw-bold text-dark font-monospace" id="calcPrincipal">LKR 0.00</div>
                    </div>
                    <div class="row g-2 border-bottom pb-2 mb-2">
                        <div class="col-6 text-secondary">Expected Interest:</div>
                        <div class="col-6 text-end fw-bold text-success font-monospace" id="calcInterest">LKR 0.00</div>
                    </div>
                    <div class="row g-2 pt-2 mb-0">
                        <div class="col-6 fw-bold text-dark">Maturity Value:</div>
                        <div class="col-6 text-end fw-bold text-primary font-monospace fs-5" id="calcMaturityVal">LKR 0.00</div>
                    </div>
                </div>
            </div>

            <!-- Payment parameters -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-credit-card text-success me-2"></i> Funding Source</h6>
                </div>
                <div class="card-body pt-0 small">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Funding Method</label>
                        <select class="form-select form-select-sm" name="payment_method" id="fdPayMethod" onchange="toggleChequeFields()" required>
                            <option value="Cash">Cash Drawer</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Customer Cheque</option>
                        </select>
                    </div>
                    <div id="chequeDetailsSection" style="display: none;">
                        <div class="mb-2">
                            <label class="form-label fw-semibold small">Cheque Number</label>
                            <input type="text" class="form-control form-control-sm" name="cheque_number" placeholder="Cheque #">
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-semibold small">Bank Name</label>
                            <input type="text" class="form-control form-control-sm" name="cheque_bank" placeholder="Cheque bank">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <button type="submit" class="btn btn-success rounded-pill w-100" style="background-color: #1b4332; border-color: #1b4332;">
                        <i class="bi bi-save me-1"></i> Post investment
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function updateMemberDetails() {
    const sel = document.getElementById('memberSelect');
    const opt = sel.options[sel.selectedIndex];
    if (opt.value) {
        document.getElementById('memberMetaDetails').style.display = 'block';
        document.getElementById('metaNo').textContent = opt.getAttribute('data-no');
        document.getElementById('metaNic').textContent = opt.getAttribute('data-nic');
        document.getElementById('metaPhone').textContent = opt.getAttribute('data-phone');
    } else {
        document.getElementById('memberMetaDetails').style.display = 'none';
    }
}

function calculateFD() {
    const amount = parseFloat(document.getElementById('fdAmount').value) || 0;
    const startVal = document.getElementById('fdStart').value;
    const termMonths = parseInt(document.getElementById('fdTerm').value) || 0;
    const rate = parseFloat(document.getElementById('fdRate').value) || 0;

    // Dates
    if (startVal) {
        const start = new Date(startVal);
        start.setMonth(start.getMonth() + termMonths);
        const maturityDateStr = start.toISOString().split('T')[0];
        document.getElementById('calcMaturityDate').textContent = maturityDateStr;
    } else {
        document.getElementById('calcMaturityDate').textContent = '-';
    }

    // Money Simple Interest Formula: P * R * T
    const interest = amount * (rate / 100) * (termMonths / 12);
    const maturityVal = amount + interest;

    document.getElementById('calcPrincipal').textContent = 'LKR ' + amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('calcInterest').textContent = 'LKR ' + interest.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('calcMaturityVal').textContent = 'LKR ' + maturityVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function toggleChequeFields() {
    const method = document.getElementById('fdPayMethod').value;
    if (method === 'Cheque') {
        document.getElementById('chequeDetailsSection').style.display = 'block';
    } else {
        document.getElementById('chequeDetailsSection').style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    calculateFD();
});
</script>
