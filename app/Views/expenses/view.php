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

<?php if ($expense['source_module'] === 'MACHINERY' || (isset($_GET['source_module']) && $_GET['source_module'] === 'MACHINERY')): ?>
    <div class="mb-3">
        <a href="<?= \Core\Helper::baseUrl('operations/machinery'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Back to Machinery Renting
        </a>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('expenses'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Ledger
        </a>
        <h4 class="fw-bold mb-1 text-dark">Expense Voucher: <?= htmlspecialchars($expense['expense_number']); ?></h4>
        <p class="text-muted small mb-0">Review details, transaction attachment logs, and approval workflow status.</p>
    </div>

    <!-- Contextual Workflow Actions -->
    <div class="d-flex gap-2 align-items-center">
        <?php $status = $expense['status']; ?>
        
        <?php if ($status === 'draft'): ?>
            <?php if (\Core\Auth::hasPermission('expenses.submit')): ?>
                <form action="<?= \Core\Helper::baseUrl('expenses/submit'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $expense['id']; ?>">
                    <button type="submit" class="btn btn-warning rounded-pill px-3 text-dark">
                        <i class="bi bi-send me-1"></i> Submit for Approval
                    </button>
                </form>
            <?php endif; ?>
            <?php if (\Core\Auth::hasPermission('expenses.cancel')): ?>
                <form action="<?= \Core\Helper::baseUrl('expenses/cancel'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $expense['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to cancel this draft expense?')">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </form>
            <?php endif; ?>

        <?php elseif ($status === 'pending_approval'): ?>
            <?php if (\Core\Auth::hasPermission('expenses.approve')): ?>
                <form action="<?= \Core\Helper::baseUrl('expenses/approve'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $expense['id']; ?>">
                    <button type="submit" class="btn btn-info rounded-pill px-3 text-dark">
                        <i class="bi bi-check-lg me-1"></i> Approve Expense
                    </button>
                </form>
            <?php endif; ?>
            <?php if (\Core\Auth::hasPermission('expenses.cancel')): ?>
                <form action="<?= \Core\Helper::baseUrl('expenses/cancel'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $expense['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to cancel this pending expense?')">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </form>
            <?php endif; ?>

        <?php elseif ($status === 'approved'): ?>
            <?php if (\Core\Auth::hasPermission('expenses.post')): ?>
                <form action="<?= \Core\Helper::baseUrl('expenses/post'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $expense['id']; ?>">
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                        <i class="bi bi-journal-arrow-up me-1"></i> Post to Ledger
                    </button>
                </form>
            <?php endif; ?>
            <?php if (\Core\Auth::hasPermission('expenses.cancel')): ?>
                <form action="<?= \Core\Helper::baseUrl('expenses/cancel'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $expense['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to cancel this approved expense?')">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </form>
            <?php endif; ?>

        <?php elseif ($status === 'posted'): ?>
            <?php if (\Core\Auth::hasPermission('expenses.reverse')): ?>
                <button class="btn btn-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#reverseExpenseModal">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reverse Posting
                </button>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Reversal linkage banners -->
<?php if ($status === 'reversed' && !empty($expense['reversal_journal_entry_id'])): ?>
    <div class="alert alert-danger rounded-4 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-arrow-left-right fs-4 me-3"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Reversal Warning</h6>
            <span>This expense posting has been reversed. The offset accounting entries are logged in: 
                <a href="<?= \Core\Helper::baseUrl('accounting/journal-entries/view?id=' . $expense['reversal_journal_entry_id']); ?>" class="alert-link font-monospace"><?= htmlspecialchars($expense['reversal_journal_number']); ?></a>.
                <br>Reversal Reason: <strong><?= htmlspecialchars($expense['reversal_reason'] ?? '-'); ?></strong>
            </span>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Header Details Card -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-text text-success me-2"></i> Expense Metadata</h6>
                <?php
                $badgeClass = 'bg-secondary';
                if ($status === 'posted') $badgeClass = 'bg-success';
                elseif ($status === 'pending_approval') $badgeClass = 'bg-warning text-dark';
                elseif ($status === 'approved') $badgeClass = 'bg-info text-dark';
                elseif ($status === 'reversed') $badgeClass = 'bg-danger';
                elseif ($status === 'cancelled') $badgeClass = 'bg-dark';
                ?>
                <span class="badge <?= $badgeClass ?> px-3 py-1 fs-6"><?= ucfirst(str_replace('_', ' ', $status)) ?></span>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Expense Date</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($expense['expense_date']); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Payee / Recipient</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($expense['payee']); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Expense Category</small>
                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($expense['category_name']); ?></span>
                    </div>

                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Payment Method</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($expense['payment_method']); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Cost Center</small>
                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($expense['cost_center_name']); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Reference #</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($expense['reference_number'] ?: '-'); ?></span>
                    </div>

                    <?php if ($expense['payment_method'] === 'Cash' && !empty($expense['cash_account_name'])): ?>
                        <div class="col-6 col-md-4">
                            <small class="text-muted d-block">Cash Account</small>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($expense['cash_account_name']); ?></span>
                        </div>
                    <?php elseif ($expense['payment_method'] === 'Credit' && !empty($expense['supplier_name'])): ?>
                        <div class="col-6 col-md-4">
                            <small class="text-muted d-block">Supplier (Pay Later)</small>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($expense['supplier_name']); ?></span>
                        </div>
                    <?php elseif ($expense['payment_method'] !== 'Cash' && $expense['payment_method'] !== 'Credit' && !empty($expense['bank_account_name'])): ?>
                        <div class="col-6 col-md-4">
                            <small class="text-muted d-block">Bank Account</small>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($expense['bank_account_name']); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Source Module</small>
                        <span class="badge bg-secondary-subtle text-secondary"><?= ucfirst(htmlspecialchars($expense['source_module'])); ?></span>
                    </div>
                    
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Total Amount</small>
                        <span class="fw-bold text-success fs-5">LKR <?= number_format((float)$expense['amount'], 2); ?></span>
                    </div>

                    <div class="col-12 border-top pt-3">
                        <small class="text-muted d-block">Voucher Description</small>
                        <p class="text-dark fw-medium mb-0"><?= htmlspecialchars($expense['description']); ?></p>
                    </div>

                    <?php if (!empty($expense['notes'])): ?>
                        <div class="col-12">
                            <small class="text-muted d-block">Internal Notes</small>
                            <p class="text-secondary small mb-0"><?= htmlspecialchars($expense['notes']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Attachment Details Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-paperclip text-success me-2"></i> Supporting Documents</h6>
            </div>
            <div class="card-body pt-0">
                <?php if (!empty($expense['attachments'])): ?>
                    <div class="row g-3">
                        <?php foreach ($expense['attachments'] as $att): ?>
                            <div class="col-md-6">
                                <div class="p-3 border rounded d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-3"></i>
                                        <div>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 180px;"><?= htmlspecialchars($att['original_name']); ?></div>
                                            <small class="text-muted"><?= round($att['file_size'] / 1024, 1); ?> KB</small>
                                        </div>
                                    </div>
                                    <a href="<?= \Core\Helper::baseUrl($att['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                        <i class="bi bi-download"></i> View
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted small mb-0">No documents or receipts attached to this expense voucher.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar Info / Audit Trail & Accounting Integration -->
    <div class="col-12 col-lg-4">
        <!-- Accounting Linkage -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-coin text-success me-2"></i> Accounting Integration</h6>
            </div>
            <div class="card-body p-3 pt-0 small">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Expense Debit Account:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($expense['expense_account_code']); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Journal Entry Voucher:</span>
                        <?php if (!empty($expense['journal_entry_id'])): ?>
                            <a href="<?= \Core\Helper::baseUrl('accounting/journal-entries/view?id=' . $expense['journal_entry_id']); ?>" class="fw-bold text-success font-monospace text-decoration-none">
                                <i class="bi bi-link-45deg"></i> <?= htmlspecialchars($expense['journal_number']); ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Not Posted</span>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-success me-2"></i> Audit Trail & Workflow</h6>
            </div>
            <div class="card-body p-3 pt-0">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Created By:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($expense['creator_name'] ?? 'System'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Created At:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($expense['created_at']); ?></span>
                    </li>
                    
                    <?php if (!empty($expense['approved_by'])): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent border-top">
                            <span class="text-secondary">Approved By:</span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($expense['approver_name']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                            <span class="text-secondary">Approved At:</span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($expense['approved_at']); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if (!empty($expense['posted_by'])): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent border-top">
                            <span class="text-secondary">Posted By:</span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($expense['poster_name']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                            <span class="text-secondary">Posted At:</span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($expense['posted_at']); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if (!empty($expense['reversed_by'])): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent border-top">
                            <span class="text-secondary">Reversed By:</span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($expense['reverser_name']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                            <span class="text-secondary">Reversed At:</span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($expense['reversed_at']); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

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

<!-- Modal: Reverse Posted Expense -->
<?php if ($status === 'posted' && \Core\Auth::hasPermission('expenses.reverse')): ?>
<div class="modal fade" id="reverseExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white bg-danger">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-counterclockwise me-2"></i> Reverse Expense Posting</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('expenses/reverse'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $expense['id']; ?>">
                <div class="modal-body p-4">
                    <p>Reversing this posting will generate an offsetting journal entry (debits and credits swapped), revert any cash drawer or bank account balances, and mark this voucher as <strong>Reversed</strong>.</p>
                    <div class="mb-3">
                        <label for="reversal_reason" class="form-label fw-semibold">Reason for Reversal <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reversal_reason" name="reversal_reason" rows="3" placeholder="Explain why this expense posting is being reversed..." required></textarea>
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
