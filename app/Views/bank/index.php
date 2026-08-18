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
        <h4 class="fw-bold mb-1 text-dark">Bank Accounts Management</h4>
        <p class="text-muted small mb-0">Add and configure bank accounts, monitor current balances, and post bank transfers.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= \Core\Helper::baseUrl('deposits'); ?>" class="btn btn-warning rounded-pill px-3 shadow-sm">
            <i class="bi bi-box-arrow-in-down-right me-1"></i> Bank Deposits
        </a>
        <button class="btn btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#bankTxModal">
            <i class="bi bi-arrow-left-right me-1"></i> Post Bank Transaction
        </button>
        <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addAccountModal">
            <i class="bi bi-plus-lg me-1"></i> Add Bank Account
        </button>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Cash in Hand Balance Card -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-success text-white p-3 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Cash in Hand Balance</h6>
                    <h3 class="fw-bold mb-0">LKR <?= number_format($cashBalance, 2); ?></h3>
                </div>
                <div class="fs-1"><i class="bi bi-cash-stack"></i></div>
            </div>
        </div>
    </div>
    
    <!-- Bank Accounts Cards -->
    <?php foreach ($bankAccounts as $ba): ?>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 <?= $ba['status'] === 'active' ? 'bg-light' : 'bg-secondary text-white opacity-75'; ?>">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="fw-bold <?= $ba['status'] === 'active' ? 'text-dark' : 'text-white'; ?> mb-0"><?= htmlspecialchars($ba['bank_name']); ?></h6>
                            <small class="<?= $ba['status'] === 'active' ? 'text-muted' : 'text-white-50'; ?>"><?= htmlspecialchars($ba['branch'] ?? '-'); ?></small>
                        </div>
                        <button class="btn btn-sm <?= $ba['status'] === 'active' ? 'btn-outline-secondary' : 'btn-outline-light'; ?> rounded-circle border-0" data-bs-toggle="modal" data-bs-target="#addAccountModal" 
                                data-id="<?= $ba['id']; ?>" 
                                data-bank_name="<?= htmlspecialchars($ba['bank_name']); ?>"
                                data-branch="<?= htmlspecialchars($ba['branch'] ?? ''); ?>"
                                data-account_number="<?= htmlspecialchars($ba['account_number']); ?>"
                                data-account_name="<?= htmlspecialchars($ba['account_name']); ?>"
                                data-swift_code="<?= htmlspecialchars($ba['swift_code'] ?? ''); ?>"
                                data-account_id="<?= $ba['account_id']; ?>"
                                data-status="<?= $ba['status']; ?>" title="Edit Account">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                    <div>
                        <div class="<?= $ba['status'] === 'active' ? 'text-secondary' : 'text-white-50'; ?> small mb-1"><?= htmlspecialchars($ba['account_name']); ?></div>
                        <div class="<?= $ba['status'] === 'active' ? 'text-success' : 'text-white'; ?> font-monospace mb-2"><?= htmlspecialchars($ba['account_number']); ?></div>
                        <h4 class="fw-bold <?= $ba['status'] === 'active' ? ($ba['current_balance'] >= 0 ? 'text-dark' : 'text-danger') : 'text-white'; ?> mb-0">
                            LKR <?= number_format($ba['current_balance'], 2); ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Recent Transactions -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-success me-2"></i> Recent Bank Transactions</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Journal #</th>
                        <th>Transaction Date</th>
                        <th>Bank Account</th>
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
                                <td>
                                    <div class="fw-bold small text-dark"><?= htmlspecialchars($tx['bank_name']); ?></div>
                                    <small class="text-muted font-monospace"><?= htmlspecialchars($tx['account_number']); ?></small>
                                </td>
                                <td><?= htmlspecialchars($tx['entry_desc']); ?></td>
                                <td class="text-end font-monospace text-success fw-semibold"><?= $tx['debit'] > 0 ? 'LKR ' . number_format($tx['debit'], 2) : '-'; ?></td>
                                <td class="text-end font-monospace text-danger fw-semibold"><?= $tx['credit'] > 0 ? 'LKR ' . number_format($tx['credit'], 2) : '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No recent bank transactions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add / Edit Bank Account -->
<div class="modal fade" id="addAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="bi bi-bank me-2"></i> Setup Bank Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/bank-accounts/store'); ?>" method="POST" id="bankAccountForm">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" id="accId" value="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="bank_name" id="accBankName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Branch Location</label>
                        <input type="text" class="form-control" name="branch" id="accBranch">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Holder Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="account_name" id="accHolderName" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Account Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="account_number" id="accNumber" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Swift Code</label>
                            <input type="text" class="form-control" name="swift_code" id="accSwift">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Linked GL Chart Account <span class="text-danger">*</span></label>
                        <select class="form-select" name="account_id" id="accGL" required>
                            <option value="">-- Choose GL Account --</option>
                            <?php foreach ($bankAccountsGL as $gl): ?>
                                <option value="<?= $gl['id']; ?>"><?= htmlspecialchars($gl['account_name']); ?> (<?= htmlspecialchars($gl['account_code']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3" id="openingBalanceSection">
                        <label class="form-label fw-semibold">Opening Balance (LKR)</label>
                        <input type="number" step="0.01" class="form-control" name="opening_balance" value="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" name="status" id="accStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Post Bank Transaction -->
<div class="modal fade" id="bankTxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-left-right me-2"></i> Record Bank Transaction</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/bank-accounts/transaction'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Transaction Type</label>
                        <select class="form-select" name="type" id="txType" onchange="toggleTxFields()" required>
                            <option value="deposit">Cash → Bank Deposit</option>
                            <option value="withdrawal">Bank → Cash Withdrawal</option>
                            <option value="transfer">Bank → Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Source/Primary Bank Account <span class="text-danger">*</span></label>
                        <select class="form-select" name="bank_account_id" required>
                            <?php foreach ($bankAccounts as $ba): ?>
                                <option value="<?= $ba['id']; ?>"><?= htmlspecialchars($ba['bank_name']); ?> - <?= htmlspecialchars($ba['account_number']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3" id="cashAccountField">
                        <label class="form-label fw-semibold">Target/Source Cash Drawer</label>
                        <select class="form-select" name="cash_account_id">
                            <?php foreach ($cashAccounts as $ca): ?>
                                <option value="<?= $ca['id']; ?>"><?= htmlspecialchars($ca['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3" id="targetBankField" style="display: none;">
                        <label class="form-label fw-semibold">Target Destination Bank Account</label>
                        <select class="form-select" name="target_bank_account_id">
                            <?php foreach ($bankAccounts as $ba): ?>
                                <option value="<?= $ba['id']; ?>"><?= htmlspecialchars($ba['bank_name']); ?> - <?= htmlspecialchars($ba['account_number']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Transaction Date</label>
                        <input type="date" class="form-control" name="date" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount (LKR)</label>
                        <input type="number" step="0.01" min="0.01" class="form-control font-monospace" name="amount" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description / Reference</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Reference details..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Post Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addAccountModal = document.getElementById('addAccountModal');
    if (addAccountModal) {
        addAccountModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            if (id) {
                document.getElementById('modalTitle').textContent = 'Edit Bank Account';
                document.getElementById('accId').value = id;
                document.getElementById('accBankName').value = button.getAttribute('data-bank_name');
                document.getElementById('accBranch').value = button.getAttribute('data-branch');
                document.getElementById('accHolderName').value = button.getAttribute('data-account_name');
                document.getElementById('accNumber').value = button.getAttribute('data-account_number');
                document.getElementById('accSwift').value = button.getAttribute('data-swift_code');
                document.getElementById('accGL').value = button.getAttribute('data-account_id');
                document.getElementById('accStatus').value = button.getAttribute('data-status');
                document.getElementById('openingBalanceSection').style.display = 'none';
            } else {
                document.getElementById('modalTitle').textContent = 'Setup Bank Account';
                document.getElementById('bankAccountForm').reset();
                document.getElementById('accId').value = '';
                document.getElementById('openingBalanceSection').style.display = 'block';
            }
        });
    }
});

function toggleTxFields() {
    const type = document.getElementById('txType').value;
    if (type === 'transfer') {
        document.getElementById('targetBankField').style.display = 'block';
        document.getElementById('cashAccountField').style.display = 'none';
    } else {
        document.getElementById('targetBankField').style.display = 'none';
        document.getElementById('cashAccountField').style.display = 'block';
    }
}
</script>
