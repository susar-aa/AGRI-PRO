<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Marketplace & Trading Dashboard</h4>
        <p class="text-muted small mb-0">Overview of internal retail sales, marketplace inventories, and business performance metrics.</p>
    </div>
    <div>
        <?php if (\Core\Auth::hasPermission('marketplace.sales.create')): ?>
            <a href="<?= \Core\Helper::baseUrl('modules/marketplace/sales/create'); ?>" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                <i class="bi bi-plus-lg me-1"></i> Record Sale Invoice
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Stats row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block mb-1">Today's Transactions</span>
                    <h3 class="fw-bold text-dark mb-0"><?= number_format($todaySalesCount); ?></h3>
                </div>
                <div class="bg-success-subtle text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-receipt fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block mb-1">Today's Sales Revenue</span>
                    <h3 class="fw-bold text-success mb-0">LKR <?= number_format($todayRevenue, 2); ?></h3>
                </div>
                <div class="bg-success-subtle text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-currency-dollar fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block mb-1">Current Marketplace Inventory</span>
                    <h3 class="fw-bold text-info mb-0">LKR <?= number_format($currentStockVal, 2); ?></h3>
                </div>
                <div class="bg-info-subtle text-info rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-boxes fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Sales list -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-stars text-success me-2"></i> Recent Posted Invoices</h6>
                <a href="<?= \Core\Helper::baseUrl('modules/marketplace/sales'); ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">View All Logs</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Method</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentSales)): ?>
                                <?php foreach ($recentSales as $sale): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace">
                                            <a href="<?= \Core\Helper::baseUrl('modules/marketplace/sales/view?id=' . $sale['id']); ?>" class="text-success text-decoration-none">
                                                <?= htmlspecialchars($sale['sale_number']); ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($sale['sale_date']); ?></td>
                                        <td><?= htmlspecialchars($sale['customer_name']); ?></td>
                                        <td class="fw-bold text-dark"><?= \Core\Helper::formatCurrency($sale['total']); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($sale['payment_method']); ?></span></td>
                                        <td class="text-center">
                                            <?php
                                            $st = $sale['status'];
                                            $badgeClass = 'bg-secondary';
                                            if ($st === 'POSTED') $badgeClass = 'bg-success';
                                            elseif ($st === 'CANCELLED') $badgeClass = 'bg-danger';
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($st); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No recent marketplace transactions.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick action links -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-gear-wide-connected text-success me-2"></i> Operations Quick Links</h6>
            <div class="d-grid gap-2">
                <a href="<?= \Core\Helper::baseUrl('modules/marketplace/products'); ?>" class="btn btn-outline-success rounded-pill text-start p-2.5">
                    <i class="bi bi-box-seam me-2"></i> Configure Marketplace Products
                </a>
                <a href="<?= \Core\Helper::baseUrl('modules/marketplace/sales/create'); ?>" class="btn btn-outline-success rounded-pill text-start p-2.5">
                    <i class="bi bi-receipt me-2"></i> Compose Invoice
                </a>
                <a href="<?= \Core\Helper::baseUrl('modules/marketplace/sales'); ?>" class="btn btn-outline-success rounded-pill text-start p-2.5">
                    <i class="bi bi-file-text me-2"></i> View Sales Journals
                </a>
            </div>
        </div>
    </div>
</div>
