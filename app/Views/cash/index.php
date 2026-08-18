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
</div>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Cash Accounts Book</h4>
        <p class="text-muted small mb-0">Manage cash drawers and record manual cash receipts/payments.</p>
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


