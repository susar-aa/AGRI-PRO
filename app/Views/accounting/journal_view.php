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
        <a href="<?= \Core\Helper::baseUrl('accounting/journal-entries'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to History
        </a>
        <h4 class="fw-bold mb-1 text-dark">Journal Voucher: <?= htmlspecialchars($entry['journal_number']); ?></h4>
        <p class="text-muted small mb-0">Review journal details, workflow status, and audit log history.</p>
    </div>

    <!-- Contextual Workflow Actions -->
    <div class="d-flex gap-2 align-items-center">
        <?php $status = $entry['status']; ?>
        
        <?php if ($status === 'draft'): ?>
            <?php if (\Core\Auth::hasPermission('journal.submit')): ?>
                <form action="<?= \Core\Helper::baseUrl('accounting/journal-entries/submit'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $entry['id']; ?>">
                    <button type="submit" class="btn btn-warning rounded-pill px-3 text-dark">
                        <i class="bi bi-send me-1"></i> Submit for Approval
                    </button>
                </form>
            <?php endif; ?>
            <?php if (\Core\Auth::hasPermission('journal.cancel')): ?>
                <form action="<?= \Core\Helper::baseUrl('accounting/journal-entries/cancel'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $entry['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to cancel this draft voucher?')">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </form>
            <?php endif; ?>

        <?php elseif ($status === 'pending_approval'): ?>
            <?php if (\Core\Auth::hasPermission('journal.approve')): ?>
                <form action="<?= \Core\Helper::baseUrl('accounting/journal-entries/approve'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $entry['id']; ?>">
                    <button type="submit" class="btn btn-info rounded-pill px-3 text-dark">
                        <i class="bi bi-check-lg me-1"></i> Approve Voucher
                    </button>
                </form>
            <?php endif; ?>
            <?php if (\Core\Auth::hasPermission('journal.cancel')): ?>
                <form action="<?= \Core\Helper::baseUrl('accounting/journal-entries/cancel'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $entry['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to cancel this pending voucher?')">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </form>
            <?php endif; ?>

        <?php elseif ($status === 'approved'): ?>
            <?php if (\Core\Auth::hasPermission('journal.post')): ?>
                <form action="<?= \Core\Helper::baseUrl('accounting/journal-entries/post'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $entry['id']; ?>">
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                        <i class="bi bi-journal-arrow-up me-1"></i> Post to Ledger
                    </button>
                </form>
            <?php endif; ?>
            <?php if (\Core\Auth::hasPermission('journal.cancel')): ?>
                <form action="<?= \Core\Helper::baseUrl('accounting/journal-entries/cancel'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $entry['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to cancel this approved voucher?')">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </form>
            <?php endif; ?>

        <?php elseif ($status === 'posted'): ?>
            <?php if (\Core\Auth::hasPermission('journal.reverse')): ?>
                <button class="btn btn-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#reverseJournalModal">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reverse Entry
                </button>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Reversal Linkage Banners -->
<?php if ($status === 'reversed' && $linkedJournal): ?>
    <div class="alert alert-danger rounded-4 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-arrow-left-right fs-4 me-3"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Reversal Warning</h6>
            <span>This journal has been officially reversed. Reversal entry is logged under: 
                <a href="<?= \Core\Helper::baseUrl('accounting/journal-entries/view?id=' . $linkedJournal['id']); ?>" class="alert-link font-monospace"><?= htmlspecialchars($linkedJournal['journal_number']); ?></a>.
            </span>
        </div>
    </div>
<?php elseif (!empty($entry['reversal_of_journal_id']) && $linkedJournal): ?>
    <div class="alert alert-warning rounded-4 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-arrow-counterclockwise fs-4 me-3"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Reversal Entry Info</h6>
            <span>This is a reversal voucher created to offset: 
                <a href="<?= \Core\Helper::baseUrl('accounting/journal-entries/view?id=' . $linkedJournal['id']); ?>" class="alert-link font-monospace"><?= htmlspecialchars($linkedJournal['journal_number']); ?></a>.
                <br>Reversal Reason: <strong><?= htmlspecialchars($entry['reversal_reason'] ?? '-'); ?></strong>
            </span>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Header Details Card -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-text text-success me-2"></i> Voucher Header</h6>
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
                        <small class="text-muted d-block">Transaction Date</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($entry['transaction_date']); ?></span>
                    </div>
                    <?php if (!empty($entry['posting_date'])): ?>
                        <div class="col-6 col-md-4">
                            <small class="text-muted d-block">Posting Date</small>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($entry['posting_date']); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Reference</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($entry['reference'] ?: '-'); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Cost Center</small>
                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($entry['cost_center_name'] ?: 'General Administration'); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Source Module</small>
                        <span class="badge bg-secondary-subtle text-secondary"><?= ucfirst(htmlspecialchars($entry['source_module'])); ?></span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Description</small>
                        <span class="text-dark fw-medium"><?= htmlspecialchars($entry['description']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Voucher Line Items -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-columns text-success me-2"></i> Journal Line Postings</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 45%;">Account Code & Name</th>
                                <th>Line Description</th>
                                <th class="text-end" style="width: 160px;">Debit</th>
                                <th class="text-end" style="width: 160px;">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entry['lines'] as $line): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($line['account_code']); ?></span> 
                                        - <?= htmlspecialchars($line['account_name']); ?>
                                    </td>
                                    <td class="text-muted small"><?= htmlspecialchars($line['description']); ?></td>
                                    <td class="text-end text-success fw-bold">
                                        <?= ($line['debit'] > 0) ? \Core\Helper::formatCurrency($line['debit'], false) : '-'; ?>
                                    </td>
                                    <td class="text-end text-danger fw-bold">
                                        <?= ($line['credit'] > 0) ? \Core\Helper::formatCurrency($line['credit'], false) : '-'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light fw-bold fs-6">
                            <tr>
                                <td colspan="2" class="text-end text-dark">Totals:</td>
                                <td class="text-end text-success fs-5"><?= \Core\Helper::formatCurrency($entry['total_debit'], false); ?></td>
                                <td class="text-end text-danger fs-5"><?= \Core\Helper::formatCurrency($entry['total_credit'], false); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Info / Audit Trail -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-success me-2"></i> Audit Trail & Workflow</h6>
            </div>
            <div class="card-body p-3 pt-0">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Created By:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($entry['creator_name'] ?? 'System'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Created At:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($entry['created_at']); ?></span>
                    </li>
                    
                    <?php if (!empty($entry['approved_by'])): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent border-top">
                            <span class="text-secondary">Approved By:</span>
                            <span class="fw-semibold text-dark">User ID: <?= htmlspecialchars($entry['approved_by']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                            <span class="text-secondary">Approved At:</span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($entry['approved_at']); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if (!empty($entry['posted_by'])): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent border-top">
                            <span class="text-secondary">Posted By:</span>
                            <span class="fw-semibold text-dark">User ID: <?= htmlspecialchars($entry['posted_by']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                            <span class="text-secondary">Posted At:</span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($entry['posted_at']); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if (!empty($entry['reversed_by'])): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent border-top">
                            <span class="text-secondary">Reversed By:</span>
                            <span class="fw-semibold text-dark">User ID: <?= htmlspecialchars($entry['reversed_by']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                            <span class="text-secondary">Reversed At:</span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($entry['reversed_at']); ?></span>
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
                    <div class="vstack gap-3" style="max-height: 300px; overflow-y: auto;">
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

<!-- Modal: Reverse Journal Entry -->
<?php if ($status === 'posted' && \Core\Auth::hasPermission('journal.reverse')): ?>
<div class="modal fade" id="reverseJournalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white bg-danger">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-counterclockwise me-2"></i> Reverse Journal Voucher</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('accounting/journal-entries/reverse'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $entry['id']; ?>">
                <div class="modal-body p-4">
                    <p>Reversing a journal voucher will create a new offsetting journal entry with debits and credits swapped, and mark this voucher as <strong>Reversed</strong>. This action is permanent and cannot be undone.</p>
                    <div class="mb-3">
                        <label for="reversal_reason" class="form-label fw-semibold">Reason for Reversal <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reversal_reason" name="reversal_reason" rows="3" placeholder="Explain why this posting is being reversed..." required></textarea>
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
