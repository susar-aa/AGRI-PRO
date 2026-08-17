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

<div class="row g-4 mb-4">
    <!-- Cash in Hand Balance Card -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-success text-white p-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Cash in Hand Balance</h6>
                    <h3 class="fw-bold mb-0">LKR <?= number_format($cashBalance, 2); ?></h3>
                </div>
                <div class="fs-1"><i class="bi bi-cash-stack"></i></div>
            </div>
        </div>
    </div>
    <!-- Total Received Card -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Total Cash Received</h6>
                    <h3 class="fw-bold text-dark mb-0">LKR <?= number_format($cashReceived, 2); ?></h3>
                </div>
                <div class="fs-1 text-success"><i class="bi bi-arrow-down-left-circle"></i></div>
            </div>
        </div>
    </div>
    <!-- Total Paid Card -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Total Cash Payments</h6>
                    <h3 class="fw-bold text-dark mb-0">LKR <?= number_format($cashPayments, 2); ?></h3>
                </div>
                <div class="fs-1 text-danger"><i class="bi bi-arrow-up-right-circle"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Cash Accounts Book</h4>
        <p class="text-muted small mb-0">Manage cash drawers, petty cash, and record manual cash receipts/payments.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#receiptModal">
            <i class="bi bi-plus-lg me-1"></i> Cash Receipt
        </button>
        <button class="btn btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#paymentModal">
            <i class="bi bi-dash-lg me-1"></i> Cash Payment
        </button>
    </div>
</div>

<!-- Cash Drawers List -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-safe text-success me-2"></i> Cash Accounts & Drawers</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Drawer Name</th>
                        <th>Linked Account</th>
                        <th class="text-end">Current Balance</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cashAccounts as $ca): ?>
                        <tr>
                            <td class="fw-bold font-monospace"><?= htmlspecialchars($ca['code']); ?></td>
                            <td><?= htmlspecialchars($ca['name']); ?></td>
                            <td><?= htmlspecialchars($ca['coa_name']); ?> (<?= htmlspecialchars($ca['account_code']); ?>)</td>
                            <td class="text-end fw-bold font-monospace <?= $ca['current_balance'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                LKR <?= number_format($ca['current_balance'], 2); ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success rounded-pill px-2">Active</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-success me-2"></i> Recent Cash Book Entries</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Journal #</th>
                        <th>Transaction Date</th>
                        <th>Description</th>
                        <th class="text-end">Debit (In)</th>
                        <th class="text-end">Credit (Out)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentTransactions)): ?>
                        <?php foreach ($recentTransactions as $tx): ?>
                            <tr>
                                <td class="fw-bold font-monospace"><?= htmlspecialchars($tx['journal_number']); ?></td>
                                <td><?= htmlspecialchars($tx['transaction_date']); ?></td>
                                <td><?= htmlspecialchars($tx['entry_desc']); ?></td>
                                <td class="text-end font-monospace text-success fw-semibold"><?= $tx['debit'] > 0 ? 'LKR ' . number_format($tx['debit'], 2) : '-'; ?></td>
                                <td class="text-end font-monospace text-danger fw-semibold"><?= $tx['credit'] > 0 ? 'LKR ' . number_format($tx['credit'], 2) : '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No recent cash transactions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Cash Receipt -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Record Cash Receipt</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/cash-accounts/transaction'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="type" value="receipt">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Destination Cash Drawer</label>
                        <select class="form-select" name="cash_account_id" required>
                            <?php foreach ($cashAccounts as $ca): ?>
                                <option value="<?= $ca['id']; ?>"><?= htmlspecialchars($ca['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Receipt Date</label>
                        <input type="date" class="form-control" name="date" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount (LKR)</label>
                        <input type="number" step="0.01" min="0.01" class="form-control font-monospace" name="amount" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description / Reference</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Describe the source of cash..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Post Receipt</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Cash Payment -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-dash-circle me-2"></i> Record Cash Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/cash-accounts/transaction'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="type" value="payment">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Source Cash Drawer</label>
                        <select class="form-select" name="cash_account_id" required>
                            <?php foreach ($cashAccounts as $ca): ?>
                                <option value="<?= $ca['id']; ?>"><?= htmlspecialchars($ca['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Date</label>
                        <input type="date" class="form-control" name="date" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount (LKR)</label>
                        <input type="number" step="0.01" min="0.01" class="form-control font-monospace" name="amount" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description / Purpose</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Describe the purpose of cash payment..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Post Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
