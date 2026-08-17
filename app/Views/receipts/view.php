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
        <h4 class="fw-bold mb-1 text-dark">Customer Receipt: <?= htmlspecialchars($pr['payment_number']); ?></h4>
        <p class="text-muted small mb-0">Record Date: <strong><?= htmlspecialchars($pr['payment_date']); ?></strong></p>
    </div>
    
    <div class="d-flex gap-2">
        <?php if ($pr['status'] === 'draft' && \Core\Auth::hasPermission('receipts.post')): ?>
            <form action="<?= \Core\Helper::baseUrl('receipts/post'); ?>" method="POST" class="d-inline">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $pr['id']; ?>">
                <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                    <i class="bi bi-send me-1"></i> Post Receipt
                </button>
            </form>
        <?php endif; ?>
        <?php if ($pr['status'] === 'posted' && \Core\Auth::hasPermission('receipts.reverse')): ?>
            <button class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#reverseReceiptModal">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reverse Receipt
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt text-success me-2"></i> Receipt Voucher Details</h6>
                <div>
                    <?php
                    $badgeClass = 'bg-secondary';
                    if ($pr['status'] === 'posted') $badgeClass = 'bg-success';
                    elseif ($pr['status'] === 'reversed') $badgeClass = 'bg-danger';
                    ?>
                    <span class="badge <?= $badgeClass ?> px-3 py-1"><?= ucfirst($pr['status']); ?></span>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Receipt Number</small>
                        <span class="fw-bold text-dark font-monospace"><?= htmlspecialchars($pr['payment_number']); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Payment Method</small>
                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($pr['payment_method']); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Ref / Slip Number</small>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($pr['reference_number'] ?: '-'); ?></span>
                    </div>

                    <div class="col-12 col-md-8">
                        <small class="text-muted d-block">Destination Account Ledger</small>
                        <span class="fw-bold text-success">
                            <?php if ($pr['payment_method'] === 'Cash'): ?>
                                <i class="bi bi-safe me-1"></i> <?= htmlspecialchars($pr['cash_account_name']); ?> (Cash Drawer)
                            <?php elseif ($pr['payment_method'] === 'Bank Transfer'): ?>
                                <i class="bi bi-bank me-1"></i> <?= htmlspecialchars($pr['bank_account_name']); ?> - <?= htmlspecialchars($pr['bank_account_num']); ?> (Bank Account)
                            <?php else: ?>
                                <i class="bi bi-wallet2 me-1"></i> Undeposited Cheques Ledger (Suspense Holding)
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Total Receipt Amount</small>
                        <span class="fw-bold text-success fs-5">LKR <?= number_format($pr['amount'], 2); ?></span>
                    </div>

                    <div class="col-12 border-top pt-3">
                        <small class="text-muted d-block">Notes / Remarks</small>
                        <p class="text-dark fw-medium mb-0"><?= nl2br(htmlspecialchars($pr['notes'] ?? '-')); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($cheque)): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wallet2 text-success me-2"></i> Linked Customer Cheque Details</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3 small">
                        <div class="col-6 col-md-3">
                            <span class="text-muted d-block">Cheque Number</span>
                            <strong class="text-dark font-monospace"><?= htmlspecialchars($cheque['cheque_number']); ?></strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="text-muted d-block">Bank Name</span>
                            <strong class="text-dark"><?= htmlspecialchars($cheque['bank_name']); ?></strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="text-muted d-block">Cheque Date</span>
                            <strong class="text-dark"><?= htmlspecialchars($cheque['cheque_date']); ?></strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="text-muted d-block">Cheque Status</span>
                            <?php
                            $chSt = $cheque['status'];
                            $chClass = 'bg-secondary';
                            if ($chSt === 'CLEARED') $chClass = 'bg-success';
                            elseif ($chSt === 'DEPOSITED') $chClass = 'bg-info';
                            elseif ($chSt === 'BOUNCED') $chClass = 'bg-danger';
                            ?>
                            <span class="badge <?= $chClass ?> px-2 py-1"><?= htmlspecialchars($chSt); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($pr['status'] === 'reversed'): ?>
            <div class="alert alert-danger border-danger-subtle rounded-4 p-4 mb-4">
                <h6 class="fw-bold alert-heading mb-2"><i class="bi bi-exclamation-octagon-fill me-2"></i> Reversal Record Information</h6>
                <div class="row g-3 small">
                    <div class="col-6">
                        <span class="text-muted">Reversal Journal Reference:</span>
                        <strong class="font-monospace text-dark d-block"><?= htmlspecialchars($pr['reversal_journal_number'] ?: '-'); ?></strong>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <span class="text-muted">Reason for Reversal:</span>
                        <p class="mb-0 fw-semibold text-dark"><?= nl2br(htmlspecialchars($pr['reversal_reason'])); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar details -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person text-success me-2"></i> Customer Profile</h6>
            </div>
            <div class="card-body p-3 pt-0 small">
                <div class="p-3 bg-light rounded-4 mb-3 border">
                    <strong class="text-dark d-block"><?= htmlspecialchars($pr['party_name']); ?></strong>
                    <span class="text-muted font-monospace"><?= htmlspecialchars($pr['party_code']); ?></span>
                    <hr class="my-2">
                    <a href="<?= \Core\Helper::baseUrl('parties/view?id=' . $pr['party_id']); ?>" class="btn btn-sm btn-success rounded-pill px-3 w-100">
                        <i class="bi bi-person-badge"></i> View Customer Profile
                    </a>
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Audit Journal Code:</span>
                        <span class="fw-bold text-dark font-monospace"><?= htmlspecialchars($pr['journal_number'] ?: '-'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Registered By:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($pr['creator_name'] ?? 'System'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Registered At:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($pr['created_at']); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Reverse Receipt -->
<div class="modal fade" id="reverseReceiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white bg-danger">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-counterclockwise me-2"></i> Reverse Receipt Entry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('receipts/reverse'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $pr['id']; ?>">
                <div class="modal-body p-4">
                    <p>Reversing this collection receipt will post an offsetting ledger adjustment (Dr Accounts Receivable / Cr Cash/Bank/Cheques) and restore the customer's outstanding balance.</p>
                    <div class="mb-3">
                        <label for="reversal_reason" class="form-label fw-semibold">Reason for Reversal <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reversal_reason" name="reversal_reason" rows="3" placeholder="Explain why this receipt is being reversed..." required></textarea>
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
