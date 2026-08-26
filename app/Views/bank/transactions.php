

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('modules/bank-accounts'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Accounts
        </a>
        <h4 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($accountName); ?></h4>
        <p class="text-muted small mb-0">View recent transactions for this account.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-success me-2"></i> Recent Transactions</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Journal #</th>
                        <th>Transaction Date</th>
                        <th>Description</th>
                        <th class="text-end">Debit (Deposit)</th>
                        <th class="text-end">Credit (Withdrawal)</th>
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
                            <td colspan="5" class="text-center text-muted py-4">No recent transactions found for this account.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
