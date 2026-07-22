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
        <h4 class="fw-bold mb-1 text-dark">Double-Entry Journal Vouchers</h4>
        <p class="text-muted small mb-0">Record and review balanced double-entry accounting transaction vouchers.</p>
    </div>
    <div>
        <?php if (\Core\Auth::hasPermission('journal.create')): ?>
            <button class="btn btn-success rounded-pill px-4 shadow-sm" style="background-color: #1b4332; border-color: #1b4332;" data-bs-toggle="modal" data-bs-target="#newJournalModal">
                <i class="bi bi-plus-lg me-1"></i> New Journal Voucher
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-check text-success me-2"></i> Journal Entries History</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Journal #</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Cost Center</th>
                        <th class="text-end">Total Debit</th>
                        <th class="text-end">Total Credit</th>
                        <th class="text-center">Status</th>
                        <th>Created By</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($entries)): ?>
                        <?php foreach ($entries as $je): ?>
                            <tr>
                                <td class="fw-bold font-monospace">
                                    <a href="<?= \Core\Helper::baseUrl('accounting/journal-entries/view?id=' . $je['id']); ?>" class="text-success text-decoration-none">
                                        <?= htmlspecialchars($je['journal_number']); ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($je['transaction_date']); ?></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($je['description']); ?></div>
                                    <?php if (!empty($je['reference'])): ?>
                                        <small class="text-muted">Ref: <?= htmlspecialchars($je['reference']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($je['cost_center_name'])): ?>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($je['cost_center_name']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold text-dark"><?= \Core\Helper::formatCurrency($je['total_debit'], false); ?></td>
                                <td class="text-end fw-bold text-dark"><?= \Core\Helper::formatCurrency($je['total_credit'], false); ?></td>
                                <td class="text-center">
                                    <?php 
                                    $status = $je['status'] ?? 'draft'; 
                                    $badgeClass = 'bg-secondary';
                                    if ($status === 'posted') $badgeClass = 'bg-success';
                                    elseif ($status === 'pending_approval') $badgeClass = 'bg-warning text-dark';
                                    elseif ($status === 'approved') $badgeClass = 'bg-info text-dark';
                                    elseif ($status === 'reversed') $badgeClass = 'bg-danger';
                                    elseif ($status === 'cancelled') $badgeClass = 'bg-dark';
                                    ?>
                                    <span class="badge <?= $badgeClass ?> px-3 py-1"><?= ucfirst(str_replace('_', ' ', $status)) ?></span>
                                </td>
                                <td class="small text-muted"><?= htmlspecialchars($je['creator_name'] ?? 'System'); ?></td>
                                <td class="text-center">
                                    <a href="<?= \Core\Helper::baseUrl('accounting/journal-entries/view?id=' . $je['id']); ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                        <i class="bi bi-eye-fill me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No journal vouchers recorded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New Double-Entry Journal -->
<?php if (\Core\Auth::hasPermission('journal.create')): ?>
<div class="modal fade" id="newJournalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #1b4332 !important;">
                <h5 class="modal-title font-weight-bold"><i class="bi bi-journal-plus me-2"></i> Post Double-Entry Journal Voucher</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('accounting/journal-entries/store'); ?>" method="POST" id="journalForm">
                <?= \Core\CSRF::getFormField(); ?>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="transaction_date" class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="transaction_date" name="transaction_date" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="reference" class="form-label fw-semibold">Reference / Receipt #</label>
                            <input type="text" class="form-control" id="reference" name="reference" placeholder="e.g. REC-1024">
                        </div>
                        <div class="col-md-4">
                            <label for="cost_center_id" class="form-label fw-semibold">Cost Center</label>
                            <select class="form-select" id="cost_center_id" name="cost_center_id">
                                <option value="">-- General Administration --</option>
                                <?php foreach ($costCenters as $cc): ?>
                                    <option value="<?= $cc['id']; ?>"><?= htmlspecialchars($cc['code']); ?> - <?= htmlspecialchars($cc['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Voucher Description <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="description" name="description" placeholder="e.g. Purchased fertilizer inventory for Marketplace" required>
                    </div>

                    <!-- Journal Lines Table -->
                    <h6 class="fw-bold text-dark mt-4 mb-2">Journal Line Items <small class="text-muted font-normal">(Total Debit must equal Total Credit)</small></h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="journalLinesTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45%;">Account <span class="text-danger">*</span></th>
                                    <th style="width: 22%;">Debit (LKR)</th>
                                    <th style="width: 22%;">Credit (LKR)</th>
                                    <th style="width: 11%;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="journalLinesContainer">
                                <tr>
                                    <td>
                                        <select class="form-select account-select" name="account_id[]" required>
                                            <option value="">-- Select Account --</option>
                                            <?php foreach ($accounts as $acc): ?>
                                                <?php if ($acc['allow_manual_posting']): ?>
                                                    <option value="<?= $acc['id']; ?>"><?= htmlspecialchars($acc['account_code']); ?> - <?= htmlspecialchars($acc['account_name']); ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.01" min="0" class="form-control debit-input" name="debit[]" placeholder="0.00" value="0.00" oninput="calcTotals()"></td>
                                    <td><input type="number" step="0.01" min="0" class="form-control credit-input" name="credit[]" placeholder="0.00" value="0.00" oninput="calcTotals()"></td>
                                    <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
                                </tr>
                                <tr>
                                    <td>
                                        <select class="form-select account-select" name="account_id[]" required>
                                            <option value="">-- Select Account --</option>
                                            <?php foreach ($accounts as $acc): ?>
                                                <?php if ($acc['allow_manual_posting']): ?>
                                                    <option value="<?= $acc['id']; ?>"><?= htmlspecialchars($acc['account_code']); ?> - <?= htmlspecialchars($acc['account_name']); ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.01" min="0" class="form-control debit-input" name="debit[]" placeholder="0.00" value="0.00" oninput="calcTotals()"></td>
                                    <td><input type="number" step="0.01" min="0" class="form-control credit-input" name="credit[]" placeholder="0.00" value="0.00" oninput="calcTotals()"></td>
                                    <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td>Total</td>
                                    <td id="totalDebitCell">LKR 0.00</td>
                                    <td id="totalCreditCell">LKR 0.00</td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-success" onclick="addRow()"><i class="bi bi-plus-lg"></i> Line</button></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div id="balanceAlert" class="alert alert-warning py-2 small mb-0 d-none">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <span id="balanceAlertText">Unbalanced Journal Voucher!</span>
                    </div>
                </div>

                <div class="modal-footer bg-light gap-2">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="action" value="draft" class="btn btn-outline-secondary action-btn rounded-pill px-3">Save Draft</button>
                    <button type="submit" name="action" value="submit" class="btn btn-warning action-btn rounded-pill px-3">Submit for Approval</button>
                    <?php if (\Core\Auth::hasPermission('journal.post')): ?>
                        <button type="submit" name="action" value="post" class="btn btn-success action-btn rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Post Directly</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addRow() {
    const container = document.getElementById('journalLinesContainer');
    const firstRow = container.querySelector('tr');
    const newRow = firstRow.cloneNode(true);
    
    // Clear values
    newRow.querySelectorAll('input').forEach(i => i.value = '0.00');
    newRow.querySelector('select').selectedIndex = 0;
    
    container.appendChild(newRow);
    calcTotals();
}

function removeRow(btn) {
    const container = document.getElementById('journalLinesContainer');
    if (container.querySelectorAll('tr').length > 2) {
        btn.closest('tr').remove();
        calcTotals();
    } else {
        alert('A journal entry must contain at least 2 line items.');
    }
}

function calcTotals() {
    let totDr = 0.0;
    let totCr = 0.0;

    document.querySelectorAll('.debit-input').forEach(i => totDr += parseFloat(i.value) || 0);
    document.querySelectorAll('.credit-input').forEach(i => totCr += parseFloat(i.value) || 0);

    document.getElementById('totalDebitCell').innerText = 'LKR ' + totDr.toFixed(2);
    document.getElementById('totalCreditCell').innerText = 'LKR ' + totCr.toFixed(2);

    const alertBox = document.getElementById('balanceAlert');
    const alertText = document.getElementById('balanceAlertText');
    const actionBtns = document.querySelectorAll('.action-btn');

    if (Math.abs(totDr - totCr) > 0.001) {
        alertBox.classList.remove('d-none');
        alertText.innerText = `Unbalanced Voucher! Total Debit (LKR ${totDr.toFixed(2)}) != Total Credit (LKR ${totCr.toFixed(2)}).`;
        actionBtns.forEach(btn => btn.disabled = true);
    } else if (totDr === 0) {
        alertBox.classList.remove('d-none');
        alertText.innerText = `Voucher amounts cannot be zero.`;
        actionBtns.forEach(btn => btn.disabled = true);
    } else {
        alertBox.classList.add('d-none');
        actionBtns.forEach(btn => btn.disabled = false);
    }
}
</script>
<?php endif; ?>
