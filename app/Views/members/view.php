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
        <a href="<?= \Core\Helper::baseUrl('modules/members/directory'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Directory
        </a>
        <h4 class="fw-bold mb-1 text-dark">Member Profile: <?= htmlspecialchars($member['full_name']); ?></h4>
        <p class="text-muted small mb-0">Registered on: <strong><?= htmlspecialchars($member['registration_date']); ?></strong> | Membership No: <strong class="text-success font-monospace"><?= htmlspecialchars($member['member_no']); ?></strong></p>
    </div>
    
    <div class="d-flex gap-2">
        <a href="<?= \Core\Helper::baseUrl('modules/members/edit?id=' . $member['id']); ?>" class="btn btn-outline-primary rounded-pill px-3">
            <i class="bi bi-pencil-square me-1"></i> Edit
        </a>
        <form action="<?= \Core\Helper::baseUrl('modules/members/delete'); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this member?');">
            <?= \Core\CSRF::getFormField(); ?>
            <input type="hidden" name="id" value="<?= $member['id']; ?>">
            <button type="submit" class="btn btn-outline-danger rounded-pill px-3">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </form>
        <?php if (!$member['party_id']): ?>
            <button class="btn btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#linkModal">
                <i class="bi bi-link-45deg me-1"></i> Link Customer Ledger
            </button>
        <?php endif; ?>
    </div>
</div>

<style>
.nav-tabs-custom { border-bottom: 2px solid #e2e8f0; }
.nav-tabs-custom .nav-link { color: #64748b; font-weight: 500; border: none; padding: 0.75rem 1.5rem; margin-bottom: -2px; }
.nav-tabs-custom .nav-link:hover { color: #0f172a; border-bottom: 2px solid #cbd5e1; }
.nav-tabs-custom .nav-link.active { color: #0f172a; border-bottom: 2px solid #16a34a; background: transparent; }
</style>

<ul class="nav nav-tabs nav-tabs-custom mb-4" id="memberTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab">Sales Invoices</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">Receipts & Payments</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="rentals-tab" data-bs-toggle="tab" data-bs-target="#rentals" type="button" role="tab">Machinery Rentals</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="ledger-tab" data-bs-toggle="tab" data-bs-target="#ledger" type="button" role="tab">Account Ledger</button>
    </li>
</ul>

<div class="tab-content" id="memberTabsContent">
    <div class="tab-pane fade show active" id="overview" role="tabpanel" tabindex="0">
        <div class="row g-4">
    <!-- Profile & Fixed Deposits Info -->
    <div class="col-12 col-lg-8">
        <!-- Profile Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-fill text-success me-2"></i> Profile Specifications</h6>
                <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill"><?= htmlspecialchars($member['status']); ?></span>
            </div>
            <div class="card-body pt-0 small">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">NIC / ID</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($member['nic']); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Gender & DOB</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($member['gender']); ?> | <?= htmlspecialchars($member['dob']); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Contact Number</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($member['phone']); ?></span>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <small class="text-muted d-block">Home Address</small>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($member['address']); ?>, <?= htmlspecialchars($member['city']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Heir Info Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people-fill text-success me-2"></i> Heir Information</h6>
            </div>
            <div class="card-body pt-0 small">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Heir Name</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($member['heir_name'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Heir NIC</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($member['heir_nic'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Contact Number</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($member['heir_contact_number'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <small class="text-muted d-block">Heir Address</small>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($member['heir_address'] ?: 'N/A'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fixed Deposits List -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wallet2 text-success me-2"></i> Member Fixed Deposits</h6>
                <?php
                $memberActiveFDs = 0;
                $memberTotalPrincipal = 0.00;
                foreach ($fixedDeposits as $fd) {
                    if ($fd['status'] === 'ACTIVE') {
                        $memberActiveFDs++;
                        $memberTotalPrincipal += ($fd['maturity_amount'] - $fd['expected_interest']);
                    }
                }
                ?>
                <div class="small">
                    <span class="badge bg-success-subtle text-success me-2">Active FDs: <?= $memberActiveFDs; ?></span>
                    <span class="badge bg-primary-subtle text-primary">Total Principal: LKR <?= number_format($memberTotalPrincipal, 2); ?></span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Deposit Number</th>
                                <th>Start Date</th>
                                <th>Term</th>
                                <th class="text-end">Principal</th>
                                <th class="text-center">Rate</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($fixedDeposits)): ?>
                                <?php foreach ($fixedDeposits as $fd): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace text-success"><?= htmlspecialchars($fd['deposit_number']); ?></td>
                                        <td><?= htmlspecialchars($fd['start_date']); ?></td>
                                        <td><?= htmlspecialchars($fd['term_months']); ?> Months</td>
                                        <td class="text-end fw-bold font-monospace">LKR <?= number_format($fd['maturity_amount'] - $fd['expected_interest'], 2); ?></td>
                                        <td class="text-center fw-semibold"><?= htmlspecialchars($fd['interest_rate']); ?>%</td>
                                        <td class="text-center">
                                            <span class="badge bg-success rounded-pill px-2"><?= htmlspecialchars($fd['status']); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= \Core\Helper::baseUrl('modules/fixed-deposits/view?id=' . $fd['id']); ?>" class="btn btn-sm btn-outline-success rounded-pill px-2">View FD</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No fixed deposits registered for this member.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Payments Sidebar Details -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-coin text-success me-2"></i> Registration & Share Fees</h6>
            </div>
            <div class="card-body pt-0 small">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Registration Fee:</span>
                        <span class="fw-bold text-success font-monospace">LKR <?= number_format($member['registration_fee'], 2); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Shares Fee:</span>
                        <span class="fw-bold text-success font-monospace">LKR <?= number_format($member['shares_fee'] ?? 0, 2); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Payment Method:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($member['payment_method']); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Status:</span>
                        <span class="badge <?= $member['payment_status'] === 'PAID' ? 'bg-success' : 'bg-danger'; ?> rounded-pill px-2"><?= htmlspecialchars($member['payment_status']); ?></span>
                    </li>
                    <?php if ($journal): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                            <span class="text-secondary">Journal Ledger:</span>
                            <span class="fw-bold font-monospace text-dark"><?= htmlspecialchars($journal['journal_number']); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <?php if ($member['party_id']): ?>
            <div class="card border-0 shadow-sm rounded-4 bg-light">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Linked Customer Account</h6>
                        <small class="text-muted font-monospace d-block"><?= htmlspecialchars($member['party_code']); ?></small>
                    </div>
                    <a href="<?= \Core\Helper::baseUrl('parties/view?id=' . $member['party_id']); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">View Ledger</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
        </div>
    </div> <!-- end overview tab -->

    <!-- INVOICES TAB -->
    <div class="tab-pane fade" id="invoices" role="tabpanel" tabindex="0">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Total (LKR)</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($invoices)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">No invoices found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($invoices as $inv): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace"><?= htmlspecialchars($inv['invoice_number']); ?></td>
                                        <td><?= htmlspecialchars($inv['invoice_date']); ?></td>
                                        <td class="font-monospace fw-semibold"><?= number_format($inv['grand_total'], 2); ?></td>
                                        <td>
                                            <?php
                                            $badge = match($inv['status']) {
                                                'POSTED' => 'bg-success',
                                                'DRAFT' => 'bg-warning text-dark',
                                                'CANCELLED' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badge; ?> rounded-pill"><?= htmlspecialchars($inv['status']); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= \Core\Helper::baseUrl('modules/invoices/view?id=' . $inv['id']); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- PAYMENTS TAB -->
    <div class="tab-pane fade" id="payments" role="tabpanel" tabindex="0">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Amount (LKR)</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($payments)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">No payments found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($payments as $pay): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace"><?= htmlspecialchars($pay['payment_number']); ?></td>
                                        <td><?= htmlspecialchars($pay['payment_date']); ?></td>
                                        <td class="font-monospace fw-semibold text-success"><?= number_format($pay['amount'], 2); ?></td>
                                        <td><?= htmlspecialchars($pay['payment_method']); ?></td>
                                        <td>
                                            <span class="badge <?= $pay['status'] === 'posted' ? 'bg-success' : 'bg-danger'; ?> rounded-pill">
                                                <?= strtoupper($pay['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= \Core\Helper::baseUrl('modules/finance/receipts-payments/view?id=' . $pay['id']); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- RENTALS TAB -->
    <div class="tab-pane fade" id="rentals" role="tabpanel" tabindex="0">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Rental #</th>
                                <th>Machine</th>
                                <th>From - To</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rentals)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">No rentals found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($rentals as $ren): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace"><?= htmlspecialchars($ren['rental_number']); ?></td>
                                        <td><?= htmlspecialchars($ren['machinery_name']); ?></td>
                                        <td><?= htmlspecialchars($ren['start_date']); ?> to <?= htmlspecialchars($ren['end_date']); ?></td>
                                        <td>
                                            <?php
                                            $badge = match($ren['status']) {
                                                'ACTIVE' => 'bg-success',
                                                'COMPLETED' => 'bg-primary',
                                                'CANCELLED' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badge; ?> rounded-pill"><?= htmlspecialchars($ren['status']); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= \Core\Helper::baseUrl('modules/machinery/rentals/view?id=' . $ren['id']); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- LEDGER TAB -->
    <div class="tab-pane fade" id="ledger" role="tabpanel" tabindex="0">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Ref</th>
                                <th>Description</th>
                                <th class="text-end">Debit (LKR)</th>
                                <th class="text-end">Credit (LKR)</th>
                                <th class="text-end">Balance (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ledgerEntries)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">No ledger entries found.</td></tr>
                            <?php else: ?>
                                <?php $runningBalance = 0; ?>
                                <?php foreach ($ledgerEntries as $entry): ?>
                                    <?php $runningBalance += ($entry['debit'] - $entry['credit']); ?>
                                    <tr>
                                        <td><?= htmlspecialchars($entry['date']); ?></td>
                                        <td class="font-monospace text-muted"><?= htmlspecialchars($entry['reference']); ?></td>
                                        <td><?= htmlspecialchars($entry['description']); ?></td>
                                        <td class="text-end font-monospace"><?= $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '-'; ?></td>
                                        <td class="text-end font-monospace"><?= $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '-'; ?></td>
                                        <td class="text-end font-monospace fw-bold <?= $runningBalance > 0 ? 'text-danger' : ($runningBalance < 0 ? 'text-success' : 'text-dark'); ?>">
                                            <?= number_format(abs($runningBalance), 2); ?> <?= $runningBalance > 0 ? 'Dr' : ($runningBalance < 0 ? 'Cr' : ''); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div> <!-- End tab content -->

<!-- Modal: Link customer -->
<div class="modal fade" id="linkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-link-45deg me-2"></i> Link Customer profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/members/link-customer'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="member_id" value="<?= $member['id']; ?>">
                <div class="modal-body p-4">
                    <p>Select an existing customer ledger account to link to this member, or create a brand new customer ledger profile.</p>
                    <div class="mb-3">
                        <select class="form-select" name="party_id">
                            <option value="">-- Create & Link New Customer Profile --</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Execute Link</button>
                </div>
            </form>
        </div>
    </div>
</div>


