<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Cash & Banking Overview</h4>
        <p class="text-muted small mb-0">Centralized dashboard for tracking cash in hand, total bank deposits, cheques registry, and outstanding transactions.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Cash Balance Box -->
    <div class="col-12 col-md-4">
        <a href="<?= \Core\Helper::baseUrl('modules/cash-accounts'); ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-success text-white hover-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Cash In Hand</h6>
                        <h3 class="fw-bold mb-0">LKR <?= number_format($cashBalance, 2); ?></h3>
                        <small class="text-white-50 d-block mt-2">Manage Cash Book & Drawer balances →</small>
                    </div>
                    <div class="fs-1"><i class="bi bi-cash-stack"></i></div>
                </div>
            </div>
        </a>
    </div>

    <!-- Bank Balance Box -->
    <div class="col-12 col-md-4">
        <a href="<?= \Core\Helper::baseUrl('modules/bank-accounts'); ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary text-white hover-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Bank Balance</h6>
                        <h3 class="fw-bold mb-0">LKR <?= number_format($bankBalance, 2); ?></h3>
                        <small class="text-white-50 d-block mt-2">Manage Bank Accounts & Transfers →</small>
                    </div>
                    <div class="fs-1"><i class="bi bi-bank"></i></div>
                </div>
            </div>
        </a>
    </div>

    <!-- Cheque Balance Box -->
    <div class="col-12 col-md-4">
        <a href="<?= \Core\Helper::baseUrl('cheques'); ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-info text-white hover-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Cheque In Hand</h6>
                        <h3 class="fw-bold mb-0">LKR <?= number_format($chequeInHand, 2); ?></h3>
                        <small class="text-white-50 d-block mt-2">View Cheque Registry →</small>
                    </div>
                    <div class="fs-1"><i class="bi bi-wallet2"></i></div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Outstanding Cheques -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-exclamation-circle text-warning me-2"></i> Outstanding Cheques (RECEIVED / DEPOSITED)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Cheque Number</th>
                                <th>Customer</th>
                                <th>Bank</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($outstandingCheques)): ?>
                                <?php foreach ($outstandingCheques as $ch): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace text-success"><?= htmlspecialchars($ch['cheque_number']); ?></td>
                                        <td><?= htmlspecialchars($ch['customer_name']); ?></td>
                                        <td><?= htmlspecialchars($ch['bank_name']); ?></td>
                                        <td class="text-end fw-bold font-monospace">LKR <?= number_format($ch['amount'], 2); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark rounded-pill"><?= htmlspecialchars($ch['status']); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No outstanding cheques.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-success me-2"></i> Recent Cash & Bank Transactions</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Journal #</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentTransactions)): ?>
                                <?php foreach ($recentTransactions as $tx): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace"><?= htmlspecialchars($tx['journal_number']); ?></td>
                                        <td><?= htmlspecialchars($tx['transaction_date']); ?></td>
                                        <td><?= htmlspecialchars($tx['description']); ?></td>
                                        <td class="text-end text-success font-monospace"><?= $tx['total_debit'] > 0 ? 'LKR ' . number_format($tx['total_debit'], 2) : '-'; ?></td>
                                        <td class="text-end text-danger font-monospace"><?= $tx['total_credit'] > 0 ? 'LKR ' . number_format($tx['total_credit'], 2) : '-'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No recent banking transactions found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.15)!important;
}
</style>
