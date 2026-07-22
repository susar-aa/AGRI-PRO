<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">General Ledger Report</h4>
        <p class="text-muted small mb-0">Complete audit trail of ledger account postings, debits, credits, and running balances.</p>
    </div>
</div>

<!-- Filter Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('accounting/general-ledger'); ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-md-3">
                <label for="account_id" class="form-label small fw-semibold">Filter by Account</label>
                <select class="form-select" id="account_id" name="account_id">
                    <option value="">-- All Accounts --</option>
                    <?php foreach ($accounts as $acc): ?>
                        <option value="<?= $acc['id']; ?>" <?= ($selectedAccountId == $acc['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($acc['account_code']); ?> - <?= htmlspecialchars($acc['account_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="cost_center_id" class="form-label small fw-semibold">Filter by Cost Center</label>
                <select class="form-select" id="cost_center_id" name="cost_center_id">
                    <option value="">-- All Cost Centers --</option>
                    <?php foreach ($costCenters as $cc): ?>
                        <option value="<?= $cc['id']; ?>" <?= ($selectedCostCenterId == $cc['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cc['code']); ?> - <?= htmlspecialchars($cc['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="from_date" class="form-label small fw-semibold">From Date</label>
                <input type="date" class="form-control" id="from_date" name="from_date" value="<?= htmlspecialchars($fromDate); ?>">
            </div>
            <div class="col-6 col-md-2">
                <label for="to_date" class="form-label small fw-semibold">To Date</label>
                <input type="date" class="form-control" id="to_date" name="to_date" value="<?= htmlspecialchars($toDate); ?>">
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-success w-100 rounded-pill" style="background-color: #1b4332; border-color: #1b4332;">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-book-fill text-success me-2"></i> Ledger Postings Trail</h6>
        <span class="badge bg-light text-dark border"><?= count($ledgerEntries); ?> Entries Found</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Journal #</th>
                        <th>Account</th>
                        <th>Description</th>
                        <th>Cost Center</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                        <th class="text-end">Running Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ledgerEntries)): ?>
                        <?php foreach ($ledgerEntries as $le): ?>
                            <tr>
                                <td><?= htmlspecialchars($le['transaction_date']); ?></td>
                                <td class="fw-bold font-monospace">
                                    <a href="<?= \Core\Helper::baseUrl('accounting/journal-entries/view?id=' . $le['journal_entry_id']); ?>" class="text-success text-decoration-none">
                                        <?= htmlspecialchars($le['journal_number']); ?>
                                    </a>
                                </td>
                                <td><span class="fw-bold text-dark"><?= htmlspecialchars($le['account_code']); ?></span> - <?= htmlspecialchars($le['account_name']); ?></td>
                                <td class="small text-secondary"><?= htmlspecialchars($le['entry_description']); ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($le['cost_center_name'] ?? '-'); ?></span></td>
                                <td class="text-end text-success fw-medium"><?= \Core\Helper::formatCurrency($le['debit']); ?></td>
                                <td class="text-end text-danger fw-medium"><?= \Core\Helper::formatCurrency($le['credit']); ?></td>
                                <td class="text-end fw-bold text-dark"><?= \Core\Helper::formatCurrency($le['running_balance']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No general ledger entries found for the selected criteria.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
