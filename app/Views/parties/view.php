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
        <a href="<?= \Core\Helper::baseUrl('parties'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
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

<div class="row g-4">
    <!-- Summary Cards -->
    <div class="col-12 col-lg-8">
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

        <!-- Dynamic Tabs for Ledgers -->
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

    <!-- Sidebar details -->
    <div class="col-12 col-lg-4">
        <!-- Balances and Limits -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wallet2 text-success me-2"></i> Financial & Credit Settings</h6>
            </div>
            <div class="card-body p-3 pt-0 small">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Current Balance:</span>
                        <span class="fw-bold <?= $currentBalance >= 0 ? 'text-success' : 'text-danger'; ?> fs-6">LKR <?= number_format($currentBalance, 2); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Opening Balance:</span>
                        <span class="fw-bold text-dark">LKR <?= number_format($openingBalanceVal, 2); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Credit Limit:</span>
                        <span class="fw-semibold text-dark"><?= \Core\Helper::formatCurrency($party['credit_limit']); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Credit Grace Days:</span>
                        <span class="fw-semibold text-dark"><?= (int)$party['credit_days']; ?> Days</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Payment Terms:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($party['payment_terms'] ?: 'None Specified'); ?></span>
                    </li>
                </ul>
            </div>
            <div class="card-footer bg-light border-0 text-center py-2">
                <?php if (!$postedOpeningBalance): ?>
                    <?php
                    $canCreate = false;
                    if ($party['party_type'] === 'CUSTOMER' && \Core\Auth::hasPermission('customer.opening_balance')) $canCreate = true;
                    elseif ($party['party_type'] === 'SUPPLIER' && \Core\Auth::hasPermission('supplier.opening_balance')) $canCreate = true;
                    elseif ($party['party_type'] === 'BOTH' && (\Core\Auth::hasPermission('customer.opening_balance') || \Core\Auth::hasPermission('supplier.opening_balance'))) $canCreate = true;
                    ?>
                    <?php if ($canCreate): ?>
                        <a href="<?= \Core\Helper::baseUrl('parties/opening-balance?party_id=' . $party['id']); ?>" class="btn btn-sm btn-success rounded-pill px-3">
                            <i class="bi bi-plus-circle me-1"></i> Record Opening Balance
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <?php
                    $canReverse = false;
                    if ($party['party_type'] === 'CUSTOMER' && \Core\Auth::hasPermission('customer.opening_balance.reverse')) $canReverse = true;
                    elseif ($party['party_type'] === 'SUPPLIER' && \Core\Auth::hasPermission('supplier.opening_balance.reverse')) $canReverse = true;
                    elseif ($party['party_type'] === 'BOTH' && (\Core\Auth::hasPermission('customer.opening_balance.reverse') || \Core\Auth::hasPermission('supplier.opening_balance.reverse'))) $canReverse = true;
                    ?>
                    <?php if ($canReverse): ?>
                        <button class="btn btn-sm btn-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#reverseObModal">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reverse Opening Balance
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="d-grid gap-2 mt-2 pt-2 border-top">
                    <?php if (in_array($party['party_type'], ['CUSTOMER', 'BOTH']) && \Core\Auth::hasPermission('receipts.create')): ?>
                        <a href="<?= \Core\Helper::baseUrl('receipts/create?party_id=' . $party['id']); ?>" class="btn btn-sm btn-outline-success rounded-pill">
                            <i class="bi bi-download me-1"></i> Receive Payment
                        </a>
                    <?php endif; ?>
                    <?php if (in_array($party['party_type'], ['SUPPLIER', 'BOTH']) && \Core\Auth::hasPermission('supplier_payments.create')): ?>
                        <a href="<?= \Core\Helper::baseUrl('supplier-payments/create?party_id=' . $party['id']); ?>" class="btn btn-sm btn-outline-danger rounded-pill">
                            <i class="bi bi-upload me-1"></i> Make Payment
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-success me-2"></i> Registration Log</h6>
            </div>
            <div class="card-body p-3 pt-0">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Registered By:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($party['creator_name'] ?? 'System'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Registered At:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($party['created_at']); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Last Updated:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($party['updated_at']); ?></span>
                    </li>
                </ul>
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

        <!-- Audit Action Log Details -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-check text-success me-2"></i> System Audit Logs</h6>
            </div>
            <div class="card-body p-3 pt-0">
                <?php if (!empty($auditLogs)): ?>
                    <div class="vstack gap-3" style="max-height: 250px; overflow-y: auto;">
                        <?php foreach ($auditLogs as $log): ?>
                            <div class="p-2 border rounded bg-light-subtle">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark text-capitalize small"><?= str_replace('_', ' ', $log['action']); ?></strong>
                                    <span class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($log['created_at']); ?></span>
                                </div>
                                <div class="text-secondary" style="font-size: 0.8rem;">
                                    User: <strong><?= htmlspecialchars($log['full_name'] ?? 'System'); ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted small mb-0">No detailed system audit logs available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

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
