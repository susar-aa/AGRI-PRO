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
        <a href="<?= \Core\Helper::baseUrl('deposits'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to History
        </a>
        <h4 class="fw-bold mb-1 text-dark">Bank Deposit Voucher: <?= htmlspecialchars($deposit['deposit_number']); ?></h4>
        <p class="text-muted small mb-0">Record Date: <strong><?= htmlspecialchars($deposit['deposit_date']); ?></strong></p>
    </div>
    
    <div class="d-flex gap-2">
        <?php if ($deposit['status'] === 'DRAFT' && \Core\Auth::hasPermission('deposits.post')): ?>
            <form action="<?= \Core\Helper::baseUrl('deposits/post'); ?>" method="POST" class="d-inline">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $deposit['id']; ?>">
                <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                    <i class="bi bi-send me-1"></i> Post Deposit
                </button>
            </form>
        <?php endif; ?>
        <?php if ($deposit['status'] === 'DEPOSITED' && \Core\Auth::hasPermission('deposits.cancel')): ?>
            <button class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#cancelDepositModal">
                <i class="bi bi-x-circle me-1"></i> Cancel Deposit
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Items & details -->
    <div class="col-12 col-lg-8">
        <!-- Details Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-box-arrow-in-down-right text-success me-2"></i> Bank Deposit Summary</h6>
                <div>
                    <?php
                    $badgeClass = 'bg-secondary';
                    if ($deposit['status'] === 'DEPOSITED') $badgeClass = 'bg-success';
                    elseif ($deposit['status'] === 'CANCELLED') $badgeClass = 'bg-danger';
                    ?>
                    <span class="badge <?= $badgeClass ?> px-3 py-1"><?= htmlspecialchars($deposit['status']); ?></span>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Deposit Number</small>
                        <span class="fw-bold text-dark font-monospace"><?= htmlspecialchars($deposit['deposit_number']); ?></span>
                    </div>
                    <div class="col-6 col-md-8">
                        <small class="text-muted d-block">Destination Bank Account</small>
                        <span class="fw-bold text-success"><i class="bi bi-bank me-1"></i> <?= htmlspecialchars($deposit['bank_name']); ?> - <?= htmlspecialchars($deposit['account_number']); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Deposit Total Amount</small>
                        <span class="fw-bold text-success fs-5">LKR <?= number_format($deposit['total_amount'], 2); ?></span>
                    </div>

                    <div class="col-12 border-top pt-3">
                        <small class="text-muted d-block">Description / Remarks</small>
                        <p class="text-dark fw-medium mb-0"><?= nl2br(htmlspecialchars($deposit['description'] ?? '-')); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deposit Items Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-task text-success me-2"></i> Deposited Item breakdown</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Item Type</th>
                                <th>Cheque Number</th>
                                <th>Bank</th>
                                <th>Customer Name</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deposit['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <?php if ($item['item_type'] === 'CASH'): ?>
                                            <span class="badge bg-success-subtle text-success">Cash</span>
                                        <?php else: ?>
                                            <span class="badge bg-info-subtle text-info">Cheque</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="font-monospace fw-semibold"><?= htmlspecialchars($item['cheque_number'] ?: '-'); ?></td>
                                    <td><?= htmlspecialchars($item['cheque_bank'] ?: '-'); ?></td>
                                    <td><?= htmlspecialchars($item['customer_name'] ?: '-'); ?></td>
                                    <td class="text-end fw-bold font-monospace"><?= number_format($item['amount'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if ($deposit['status'] === 'CANCELLED'): ?>
            <div class="alert alert-danger border-danger-subtle rounded-4 p-4 mb-4">
                <h6 class="fw-bold alert-heading mb-2"><i class="bi bi-exclamation-octagon-fill me-2"></i> Deposit Cancellation Information</h6>
                <div class="row g-3 small">
                    <div class="col-6">
                        <span class="text-muted">Reversal Journal Reference:</span>
                        <strong class="font-monospace text-dark d-block"><?= htmlspecialchars($deposit['reversal_journal_number'] ?: '-'); ?></strong>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <span class="text-muted">Reason for Cancellation:</span>
                        <p class="mb-0 fw-semibold text-dark"><?= nl2br(htmlspecialchars($deposit['reversal_reason'])); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar details -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-success me-2"></i> Audit Specifications</h6>
            </div>
            <div class="card-body p-3 pt-0 small">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Journal Number:</span>
                        <span class="fw-bold text-dark font-monospace"><?= htmlspecialchars($deposit['journal_number'] ?: '-'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Created By:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($deposit['creator_name'] ?? 'System'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Registered At:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($deposit['created_at']); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Cancel Bank Deposit -->
<div class="modal fade" id="cancelDepositModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white bg-danger">
                <h5 class="modal-title fw-bold"><i class="bi bi-x-circle me-2"></i> Cancel Bank Deposit Voucher</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('deposits/cancel'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $deposit['id']; ?>">
                <div class="modal-body p-4">
                    <p>Cancelling this deposit voucher will reverse the ledger accounting entries, deduct deposited amounts from bank balances, and restore associated cheques to the RECEIVED (undeposited) state.</p>
                    <div class="mb-3">
                        <label for="reversal_reason" class="form-label fw-semibold">Reason for Cancellation <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reversal_reason" name="reversal_reason" rows="3" placeholder="Explain why this deposit is being cancelled..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Execute Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>
