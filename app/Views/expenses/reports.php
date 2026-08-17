<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('expenses'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Ledger
        </a>
        <h4 class="fw-bold mb-1 text-dark">Expense Analytics & Reports</h4>
        <p class="text-muted small mb-0">Detailed analytical breakdowns of all posted cooperative expenditures.</p>
    </div>
</div>

<!-- Filter Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('expenses/reports'); ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label for="cost_center_id" class="form-label small fw-semibold">Filter by Cost Center</label>
                <select class="form-select" id="cost_center_id" name="cost_center_id">
                    <option value="">-- All Cost Centers --</option>
                    <?php foreach ($costCenters as $cc): ?>
                        <option value="<?= $cc['id']; ?>" <?= ($filters['cost_center_id'] == $cc['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cc['code']); ?> - <?= htmlspecialchars($cc['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label for="date_from" class="form-label small fw-semibold">From Date</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="<?= htmlspecialchars($filters['date_from']); ?>">
            </div>
            <div class="col-6 col-md-3">
                <label for="date_to" class="form-label small fw-semibold">To Date</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="<?= htmlspecialchars($filters['date_to']); ?>">
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-success w-100 rounded-pill" style="background-color: #1b4332; border-color: #1b4332;">
                    <i class="bi bi-filter me-1"></i> Generate
                </button>
            </div>
        </form>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-success text-white" style="background-color: #1b4332 !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="fs-1"><i class="bi bi-wallet2"></i></div>
                <div>
                    <small class="d-block opacity-75">Total Expenditures</small>
                    <span class="fs-4 fw-bold">LKR <?= number_format($report['total_expenses'], 2); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light border">
            <div class="d-flex align-items-center gap-3">
                <div class="fs-1 text-success"><i class="bi bi-diagram-3"></i></div>
                <div>
                    <small class="d-block text-muted">Primary Category</small>
                    <span class="fs-6 fw-bold text-dark text-truncate d-inline-block" style="max-width: 160px;">
                        <?= !empty($report['by_category']) ? htmlspecialchars($report['by_category'][0]['category_name']) : 'None'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light border">
            <div class="d-flex align-items-center gap-3">
                <div class="fs-1 text-success"><i class="bi bi-pie-chart"></i></div>
                <div>
                    <small class="d-block text-muted">Highest Cost Center</small>
                    <span class="fs-6 fw-bold text-dark text-truncate d-inline-block" style="max-width: 160px;">
                        <?= !empty($report['by_cost_center']) ? htmlspecialchars($report['by_cost_center'][0]['cost_center_name']) : 'None'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light border">
            <div class="d-flex align-items-center gap-3">
                <div class="fs-1 text-success"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <small class="d-block text-muted">Primary Payment</small>
                    <span class="fs-6 fw-bold text-dark">
                        <?= !empty($report['by_payment_method']) ? htmlspecialchars($report['by_payment_method'][0]['payment_method']) : 'None'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Expenses by Category -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-tags-fill text-success me-2"></i> Expenses by Category</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Category Name</th>
                                <th class="text-end" style="width: 150px;">Total Spent</th>
                                <th class="text-end" style="width: 100px;">Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($report['by_category'])): ?>
                                <?php foreach ($report['by_category'] as $cat): 
                                    $pct = ($report['total_expenses'] > 0) ? ($cat['total_amount'] / $report['total_expenses']) * 100 : 0;
                                    ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($cat['category_name']); ?></td>
                                        <td class="text-end fw-bold"><?= \Core\Helper::formatCurrency((float)$cat['total_amount'], false); ?></td>
                                        <td class="text-end text-muted small"><?= number_format($pct, 1); ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center py-3 text-muted">No records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses by Cost Center -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart-fill text-success me-2"></i> Expenses by Cost Center</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Cost Center Name</th>
                                <th class="text-end" style="width: 150px;">Total Spent</th>
                                <th class="text-end" style="width: 100px;">Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($report['by_cost_center'])): ?>
                                <?php foreach ($report['by_cost_center'] as $cc): 
                                    $pct = ($report['total_expenses'] > 0) ? ($cc['total_amount'] / $report['total_expenses']) * 100 : 0;
                                    ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($cc['cost_center_name']); ?></td>
                                        <td class="text-end fw-bold"><?= \Core\Helper::formatCurrency((float)$cc['total_amount'], false); ?></td>
                                        <td class="text-end text-muted small"><?= number_format($pct, 1); ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center py-3 text-muted">No records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Expenses Trend -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-graph-up-arrow text-success me-2"></i> Monthly Expense Trend</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                <th class="text-end" style="width: 180px;">Total Spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($report['monthly_trend'])): ?>
                                <?php foreach ($report['monthly_trend'] as $mt): ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($mt['month']); ?></td>
                                        <td class="text-end fw-bold text-success"><?= \Core\Helper::formatCurrency((float)$mt['total_amount'], false); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="2" class="text-center py-3 text-muted">No records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses by Payment Method / Supplier -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wallet2 text-success me-2"></i> Expenses by Payment Method</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Payment Method</th>
                                <th class="text-end" style="width: 180px;">Total Spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($report['by_payment_method'])): ?>
                                <?php foreach ($report['by_payment_method'] as $pm): ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($pm['payment_method']); ?></td>
                                        <td class="text-end fw-bold"><?= \Core\Helper::formatCurrency((float)$pm['total_amount'], false); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="2" class="text-center py-3 text-muted">No records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
