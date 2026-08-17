<?php if ($flashSuccess = \Core\Session::getFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Welcome Banner -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1b4332, #2d6a4f); color: white; border-radius: 14px;">
    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1">සීමා සහිත ඇග්රි කෝප් සමූපකාර සමිතිය ERP</h4>
            <p class="mb-0 text-white-50">Central Accounting & Business Management System | Miduma, Yatagama, Rambukkana</p>
        </div>
        <div>
            <a href="<?= \Core\Helper::baseUrl('modules/invoices/create'); ?>" class="btn btn-light text-success fw-bold px-4 rounded-pill shadow-sm">
                <i class="bi bi-file-earmark-plus me-1"></i> Create Invoice
            </a>
        </div>
    </div>
</div>

<!-- Financial KPI Cards (8 Key Metrics) -->
<div class="row g-3 mb-4">
    <!-- Total Revenue -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card kpi-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Revenue</span>
                    <h4 class="fw-bold text-success mb-0 mt-1"><?= \Core\Helper::formatCurrency($kpi['revenue'] ?? 0.00); ?></h4>
                </div>
                <div class="kpi-icon bg-success-subtle text-success">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Expenses -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card kpi-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Expenses</span>
                    <h4 class="fw-bold text-danger mb-0 mt-1"><?= \Core\Helper::formatCurrency($kpi['expenses'] ?? 0.00); ?></h4>
                </div>
                <div class="kpi-icon bg-danger-subtle text-danger">
                    <i class="bi bi-graph-down-arrow"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Profit -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card kpi-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Net Profit / Surplus</span>
                    <h4 class="fw-bold <?= ($kpi['net_profit'] ?? 0) >= 0 ? 'text-primary' : 'text-danger'; ?> mb-0 mt-1">
                        <?= \Core\Helper::formatCurrency($kpi['net_profit'] ?? 0.00); ?>
                    </h4>
                </div>
                <div class="kpi-icon bg-primary-subtle text-primary">
                    <i class="bi bi-cash-coin"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Balance -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card kpi-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Cash Balance</span>
                    <h4 class="fw-bold text-dark mb-0 mt-1"><?= \Core\Helper::formatCurrency($kpi['cash'] ?? 0.00); ?></h4>
                </div>
                <div class="kpi-icon bg-warning-subtle text-warning">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Balance -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card kpi-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Bank Balance</span>
                    <h4 class="fw-bold text-dark mb-0 mt-1"><?= \Core\Helper::formatCurrency($kpi['bank'] ?? 0.00); ?></h4>
                </div>
                <div class="kpi-icon bg-info-subtle text-info">
                    <i class="bi bi-bank"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts Receivable -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card kpi-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Accounts Receivable</span>
                    <h4 class="fw-bold text-dark mb-0 mt-1"><?= \Core\Helper::formatCurrency($kpi['receivable'] ?? 0.00); ?></h4>
                </div>
                <div class="kpi-icon bg-success-subtle text-success">
                    <i class="bi bi-arrow-down-left-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts Payable -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card kpi-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Accounts Payable</span>
                    <h4 class="fw-bold text-dark mb-0 mt-1"><?= \Core\Helper::formatCurrency($kpi['payable'] ?? 0.00); ?></h4>
                </div>
                <div class="kpi-icon bg-danger-subtle text-danger">
                    <i class="bi bi-arrow-up-right-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Value -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card kpi-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Inventory Value</span>
                    <h4 class="fw-bold text-dark mb-0 mt-1"><?= \Core\Helper::formatCurrency($kpi['inventory'] ?? 0.00); ?></h4>
                </div>
                <div class="kpi-icon bg-secondary-subtle text-secondary">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Business Cost Centers Performance & Chart Placeholder -->
<div class="row g-4 mb-4">
    <!-- Cost Centers Breakdown -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart-fill text-success me-2"></i> Business Activity Cost Centers</h6>
                <span class="badge bg-light text-dark border">9 Registered Centers</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Cost Center Name</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Expenses</th>
                                <th class="text-end">Profit / Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($costCenterStats)): ?>
                                <?php foreach ($costCenterStats as $cc): ?>
                                    <?php $net = $cc['revenue'] - $cc['expense']; ?>
                                    <tr>
                                        <td><span class="badge bg-secondary-subtle text-dark border fw-bold"><?= htmlspecialchars($cc['code']); ?></span></td>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($cc['name']); ?></td>
                                        <td class="text-end text-success fw-medium"><?= \Core\Helper::formatCurrency($cc['revenue']); ?></td>
                                        <td class="text-end text-danger fw-medium"><?= \Core\Helper::formatCurrency($cc['expense']); ?></td>
                                        <td class="text-end fw-bold <?= $net >= 0 ? 'text-primary' : 'text-danger'; ?>">
                                            <?= \Core\Helper::formatCurrency($net); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No cost center activities recorded yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Analytics Chart Area Placeholder -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i> Monthly Revenue & Expenses</h6>
            </div>
            <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center text-center">
                <div class="p-4 bg-light rounded-4 w-100 border border-dashed mb-3">
                    <i class="bi bi-graph-up text-muted display-4 d-block mb-2"></i>
                    <h6 class="fw-semibold text-secondary">Financial Trend Chart Area</h6>
                    <small class="text-muted d-block">Real-time revenue vs expense chart visualization will render here as financial transactions accumulate.</small>
                </div>
                <div class="d-flex justify-content-center gap-3 w-100">
                    <a href="<?= \Core\Helper::baseUrl('accounting/coa'); ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">View Chart of Accounts</a>
                    <a href="<?= \Core\Helper::baseUrl('accounting/general-ledger'); ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">View General Ledger</a>
                </div>
            </div>
        </div>
    </div>
</div>
