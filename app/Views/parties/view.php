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
        <?php
        $backLink = \Core\Helper::baseUrl('parties');
        if (in_array($party['party_type'], ['CUSTOMER', 'BOTH'])) {
            $backLink = \Core\Helper::baseUrl('parties/customers');
        } elseif ($party['party_type'] === 'SUPPLIER') {
            $backLink = \Core\Helper::baseUrl('parties/suppliers');
        }
        ?>
        <a href="<?= $backLink; ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Directory
        </a>
        <h4 class="fw-bold mb-1 text-dark">Business Partner Profile: <?= htmlspecialchars($party['name']); ?></h4>
        <p class="text-muted small mb-0">Record Reference: <strong class="font-monospace text-success"><?= htmlspecialchars($party['party_code']); ?></strong></p>
    </div>
    
    <div class="d-flex gap-2">
        <?php if ($party['status'] === 'active' && \Core\Auth::hasPermission('parties.deactivate')): ?>
            <form action="<?= \Core\Helper::baseUrl('parties/deactivate'); ?>" method="POST" class="d-inline">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $party['id']; ?>">
                <button type="submit" class="btn btn-outline-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to deactivate this business party profile?')">
                    <i class="bi bi-person-x me-1"></i> Deactivate
                </button>
            </form>
        <?php endif; ?>
        <?php if (\Core\Auth::hasPermission('parties.edit')): ?>
            <a href="<?= \Core\Helper::baseUrl('parties/edit?id=' . $party['id']); ?>" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                <i class="bi bi-pencil-square me-1"></i> Edit Profile
            </a>
        <?php endif; ?>
    </div>
</div>

<style>
.nav-tabs-custom { border-bottom: 2px solid #e2e8f0; }
.nav-tabs-custom .nav-link { color: #64748b; font-weight: 500; border: none; padding: 0.75rem 1.5rem; margin-bottom: -2px; }
.nav-tabs-custom .nav-link:hover { color: #0f172a; border-bottom: 2px solid #cbd5e1; }
.nav-tabs-custom .nav-link.active { color: #0f172a; border-bottom: 2px solid #16a34a; background: transparent; }
</style>

<ul class="nav nav-tabs nav-tabs-custom mb-4" id="partyTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
    </li>
    <?php if (in_array($party['party_type'], ['CUSTOMER', 'BOTH'])): ?>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab">Sales Invoices</button>
    </li>
    <?php endif; ?>
    <?php if (in_array($party['party_type'], ['SUPPLIER', 'BOTH'])): ?>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="grns-tab" data-bs-toggle="tab" data-bs-target="#grns" type="button" role="tab">Goods Receipt Notes</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">Payments History</button>
    </li>
    <?php endif; ?>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="ledger-tab" data-bs-toggle="tab" data-bs-target="#ledger" type="button" role="tab">Account Ledger</button>
    </li>
</ul>

<div class="tab-content" id="partyTabsContent">
    <div class="tab-pane fade show active" id="overview" role="tabpanel" tabindex="0">
        <div class="row g-4">
            <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-lines-fill text-success me-2"></i> Partner Information</h6>
                <div>
                    <?php
                    $pt = $party['party_type'];
                    $ptClass = 'bg-primary-subtle text-primary border-primary-subtle';
                    if ($pt === 'SUPPLIER') $ptClass = 'bg-info-subtle text-info border-info-subtle';
                    elseif ($pt === 'BOTH') $ptClass = 'bg-success-subtle text-success border-success-subtle';
                    ?>
                    <span class="badge border <?= $ptClass ?> px-3 py-1 mr-2"><?= ucfirst(strtolower($pt)); ?></span>
                    
                    <?php if ($party['status'] === 'active'): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Party Code</small>
                        <span class="fw-bold text-dark font-monospace"><?= htmlspecialchars($party['party_code']); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Business Name / Name</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($party['name']); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">NIC / Reg Number</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($party['nic_reg_no'] ?: '-'); ?></span>
                    </div>

                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Primary Contact Person</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($party['contact_person'] ?: '-'); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Phone Number</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($party['phone'] ?: '-'); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Email Address</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($party['email'] ?: '-'); ?></span>
                    </div>

                    <?php if (in_array($party['party_type'], ['CUSTOMER', 'BOTH'])): ?>
                        <div class="col-6 col-md-4">
                            <small class="text-muted d-block">Customer Type</small>
                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($party['customer_type'] ?: 'Individual'); ?></span>
                        </div>
                        <div class="col-6 col-md-4">
                            <small class="text-muted d-block">Activity (Why Registered)</small>
                            <span class="fw-bold text-dark"><?= !empty($party['customer_activity_name']) ? htmlspecialchars($party['customer_activity_name']) : '-'; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (in_array($party['party_type'], ['SUPPLIER', 'BOTH'])): ?>
                        <div class="col-6 col-md-4">
                            <small class="text-muted d-block">Supplier Type</small>
                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($party['supplier_type'] ?: 'Individual'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold text-secondary mb-1">
                            <?php if (in_array($party['party_type'], ['SUPPLIER', 'BOTH'])): ?>
                                Supplier Outstanding Balance (Payable)
                            <?php else: ?>
                                Customer Outstanding Balance (Receivable)
                            <?php endif; ?>
                        </h6>
                        <h3 class="fw-bold text-dark mb-0 font-monospace">LKR <?= number_format(abs($currentBalance), 2); ?></h3>
                        <?php if ($currentBalance > 0): ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle mt-2">To Pay / Collect</span>
                        <?php elseif ($currentBalance < 0): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle mt-2">Overpaid / Advance</span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle mt-2">Settled</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-end">
                        <i class="bi bi-wallet2 text-success" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing details -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-geo-alt-fill text-success me-2"></i> Billing & Address Coordinates</h6>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <small class="text-muted d-block">District / City</small>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($party['district'] ?: '-'); ?> / <?= htmlspecialchars($party['city'] ?: '-'); ?></span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Postal Address</small>
                        <p class="text-dark fw-medium mb-0"><?= nl2br(htmlspecialchars($party['address'] ?? '-')); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <?php if (!empty($party['notes'])): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-chat-right-text text-success me-2"></i> Notes / Remarks</h6>
                </div>
                <div class="card-body pt-0 small">
                    <p class="text-secondary mb-0"><?= nl2br(htmlspecialchars($party['notes'])); ?></p>
                </div>
            </div>
        <?php endif; ?>
            </div> <!-- End col-12 -->
        </div> <!-- End row -->
    </div> <!-- End overview tab -->

    <!-- INVOICES TAB -->
    <?php if (in_array($party['party_type'], ['CUSTOMER', 'BOTH'])): ?>
    <div class="tab-pane fade" id="invoices" role="tabpanel" tabindex="0">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
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
                                        <td class="font-monospace fw-semibold"><?= number_format($inv['total'] ?? 0, 2); ?></td>
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
    <?php endif; ?>

    <!-- GRNS TAB -->
    <?php if (in_array($party['party_type'], ['SUPPLIER', 'BOTH'])): ?>
    <div class="tab-pane fade" id="grns" role="tabpanel" tabindex="0">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>GRN #</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Cost (LKR)</th>
                                <th>Total (LKR)</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($grns)): ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">No goods receipt notes found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($grns as $grn): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace"><?= htmlspecialchars($grn['reference_number'] ?: 'N/A'); ?></td>
                                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($grn['movement_date']))); ?></td>
                                        <td><?= htmlspecialchars($grn['product_name']); ?></td>
                                        <td class="fw-semibold text-dark"><?= (float)$grn['quantity_in']; ?></td>
                                        <td class="font-monospace text-muted"><?= number_format($grn['unit_cost'], 2); ?></td>
                                        <td class="font-monospace fw-bold text-dark"><?= number_format($grn['quantity_in'] * $grn['unit_cost'], 2); ?></td>
                                        <td><?= htmlspecialchars($grn['location_name']); ?></td>
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
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Account/Bank</th>
                                <th>Reference</th>
                                <th>Amount (LKR)</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($payments)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">No payments found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($payments as $pay): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($pay['payment_date']); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($pay['payment_method']); ?></span></td>
                                        <td>
                                            <?php if ($pay['payment_method'] === 'Cash'): ?>
                                                <i class="bi bi-cash me-1 text-success"></i> <?= htmlspecialchars($pay['cash_account_name'] ?? 'Cash Drawer'); ?>
                                            <?php elseif ($pay['payment_method'] === 'Bank Transfer'): ?>
                                                <i class="bi bi-bank me-1 text-primary"></i> <?= htmlspecialchars($pay['bank_account_name']); ?> - <?= htmlspecialchars($pay['account_number']); ?>
                                            <?php else: ?>
                                                <?= htmlspecialchars($pay['payment_method']); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($pay['reference_number'] ?: '-'); ?></td>
                                        <td class="font-monospace fw-bold text-danger"><?= number_format($pay['amount'], 2); ?></td>
                                        <td class="text-center">
                                            <?php if ($pay['status'] === 'posted'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">Posted</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Draft</span>
                                            <?php endif; ?>
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
    <?php endif; ?>

    <!-- LEDGER TAB -->
    <div class="tab-pane fade" id="ledger" role="tabpanel" tabindex="0">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <ul class="nav nav-tabs border-bottom mb-3" id="profileTabs">
                    <?php if (in_array($party['party_type'], ['CUSTOMER', 'BOTH'])): ?>
                        <li class="nav-item">
                            <button class="nav-link active fw-bold text-success" data-bs-toggle="tab" data-bs-target="#custLedgerTab">Customer Ledger</button>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($party['party_type'], ['SUPPLIER', 'BOTH'])): ?>
                        <li class="nav-item">
                            <button class="nav-link <?= ($party['party_type'] === 'SUPPLIER') ? 'active' : ''; ?> fw-bold text-success" data-bs-toggle="tab" data-bs-target="#suppLedgerTab">Supplier Ledger</button>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="tab-content">
                    <?php if (in_array($party['party_type'], ['CUSTOMER', 'BOTH'])): ?>
                        <div class="tab-pane fade show active py-2" id="custLedgerTab">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 small">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th class="text-end">Debit</th>
                                            <th class="text-end">Credit</th>
                                            <th class="text-end">Running Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($ledgerEntries)): ?>
                                            <?php foreach ($ledgerEntries as $entry): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($entry['date']); ?></td>
                                                    <td class="font-monospace fw-bold text-success"><?= htmlspecialchars($entry['reference']); ?></td>
                                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($entry['tx_type']); ?></span></td>
                                                    <td><?= htmlspecialchars($entry['description']); ?></td>
                                                    <td class="text-end text-danger"><?= $entry['debit'] > 0 ? \Core\Helper::formatCurrency($entry['debit'], false) : '-'; ?></td>
                                                    <td class="text-end text-success"><?= $entry['credit'] > 0 ? \Core\Helper::formatCurrency($entry['credit'], false) : '-'; ?></td>
                                                    <td class="text-end fw-bold text-dark"><?= \Core\Helper::formatCurrency($entry['running_balance'], false); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">No customer ledger records found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (in_array($party['party_type'], ['SUPPLIER', 'BOTH'])): ?>
                        <div class="tab-pane fade <?= ($party['party_type'] === 'SUPPLIER') ? 'show active' : ''; ?> py-2" id="suppLedgerTab">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 small">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th class="text-end">Debit</th>
                                            <th class="text-end">Credit</th>
                                            <th class="text-end">Running Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($ledgerEntries)): ?>
                                            <?php foreach ($ledgerEntries as $entry): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($entry['date']); ?></td>
                                                    <td class="font-monospace fw-bold text-success"><?= htmlspecialchars($entry['reference']); ?></td>
                                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($entry['tx_type']); ?></span></td>
                                                    <td><?= htmlspecialchars($entry['description']); ?></td>
                                                    <td class="text-end text-danger"><?= $entry['debit'] > 0 ? \Core\Helper::formatCurrency($entry['debit'], false) : '-'; ?></td>
                                                    <td class="text-end text-success"><?= $entry['credit'] > 0 ? \Core\Helper::formatCurrency($entry['credit'], false) : '-'; ?></td>
                                                    <td class="text-end fw-bold text-dark"><?= \Core\Helper::formatCurrency($entry['running_balance'], false); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">No supplier ledger records found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div> <!-- End ledger tab -->
</div> <!-- End tab content -->

<!-- Modal: Reverse Opening Balance -->
<?php if ($postedOpeningBalance): ?>
<div class="modal fade" id="reverseObModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white bg-danger">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-counterclockwise me-2"></i> Reverse Opening Balance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('parties/opening-balance/reverse'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $postedOpeningBalance['id']; ?>">
                <div class="modal-body p-4">
                    <p>Reversing this entry will post an offsetting double-entry journal, reverse this party's ledger entry, and restore the opening balance to zero.</p>
                    <div class="mb-3">
                        <label for="reversal_reason" class="form-label fw-semibold">Reason for Reversal <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reversal_reason" name="reversal_reason" rows="3" placeholder="Explain why this opening balance is being reversed..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Execute Reversal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
