<?php 
$activeNav = $activeNav ?? 'reports_stock_movement';
$movements = $movements ?? [];
$products = $products ?? [];
$warehouses = $warehouses ?? [];
$filters = $filters ?? [];
$pagination = $pagination ?? ['current' => 1, 'total' => 1, 'count' => 0];

$totIn = array_sum(array_column($movements, 'quantity_in'));
$totOut = array_sum(array_column($movements, 'quantity_out'));
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= \Core\Helper::baseUrl('reports'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Hub</a>
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="bi bi-arrow-left-right text-dark me-2"></i>Stock Movement Audit Trail (Stock Ledger)
                </h4>
            </div>
            <p class="text-muted small mb-0">Chronological stock ledger tracking inventory inflows, outflows, transfers and balance reconciliations</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-printer"></i> Print Statement
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 d-print-none">
        <div class="card-body p-3">
            <form action="<?= \Core\Helper::baseUrl('reports/stock-movement'); ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted mb-1">From Date</label>
                    <input type="date" class="form-control form-control-sm" name="from_date" value="<?= htmlspecialchars($filters['from_date'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted mb-1">To Date</label>
                    <input type="date" class="form-control form-control-sm" name="to_date" value="<?= htmlspecialchars($filters['to_date'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">Product</label>
                    <select class="form-select form-select-sm" name="product_id">
                        <option value="">-- All Products --</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id']; ?>" <?= (($filters['product_id'] ?? '') == $p['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($p['product_code'] . ' - ' . $p['name_en']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted mb-1">Movement Type</label>
                    <select class="form-select form-select-sm" name="movement_type">
                        <option value="">-- All Types --</option>
                        <option value="GRN" <?= (($filters['movement_type'] ?? '') === 'GRN') ? 'selected' : ''; ?>>Goods Receipt (GRN)</option>
                        <option value="SALES_INVOICE" <?= (($filters['movement_type'] ?? '') === 'SALES_INVOICE') ? 'selected' : ''; ?>>Sales Invoice</option>
                        <option value="TRANSFER" <?= (($filters['movement_type'] ?? '') === 'TRANSFER') ? 'selected' : ''; ?>>Warehouse Transfer</option>
                        <option value="ADJUSTMENT" <?= (($filters['movement_type'] ?? '') === 'ADJUSTMENT') ? 'selected' : ''; ?>>Adjustment</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-dark btn-sm w-100 font-weight-bold">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="<?= \Core\Helper::baseUrl('reports/stock-movement'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary KPI Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Page Stock Inflow (Units IN)</div>
                    <div class="h4 font-weight-bold text-success mb-0">+ <?= number_format($totIn, 2); ?></div>
                    <small class="text-muted">Goods Received & Transfers In</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-danger rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Page Stock Outflow (Units OUT)</div>
                    <div class="h4 font-weight-bold text-danger mb-0">- <?= number_format($totOut, 2); ?></div>
                    <small class="text-muted">Invoiced Sales & Deliveries</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-dark rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Movement Records</div>
                    <div class="h4 font-weight-bold text-dark mb-0"><?= number_format($pagination['count']); ?> Entries</div>
                    <small class="text-muted">Stock movements recorded</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Movements Table Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Stock Ledger Audit Entries</h6>
                <small class="text-muted">Page <?= $pagination['current']; ?> of <?= max(1, $pagination['total']); ?></small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 11%;">Date</th>
                            <th style="width: 14%;">Ref Number</th>
                            <th style="width: 12%;">Type</th>
                            <th style="width: 25%;">Product Description</th>
                            <th style="width: 12%;">Warehouse</th>
                            <th style="width: 8%; text-align: right;">Qty IN</th>
                            <th style="width: 8%; text-align: right;">Qty OUT</th>
                            <th style="width: 10%; text-align: right;">Balance Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($movements)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No stock movements found for the selected criteria.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($movements as $m): ?>
                                <tr>
                                    <td class="font-monospace text-muted small"><?= htmlspecialchars($m['movement_date']); ?></td>
                                    <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($m['reference_number'] ?: 'TX-' . $m['id']); ?></td>
                                    <td>
                                        <?php 
                                        $typeBadge = match($m['movement_type']) {
                                            'GRN' => 'bg-success',
                                            'SALES_INVOICE' => 'bg-danger',
                                            'TRANSFER' => 'bg-info text-dark',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $typeBadge; ?> small"><?= htmlspecialchars($m['movement_type']); ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($m['product_name']); ?></div>
                                        <small class="font-monospace text-muted"><?= htmlspecialchars($m['product_code']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($m['warehouse_name']); ?></span>
                                    </td>
                                    <td class="text-end font-monospace text-success font-weight-bold">
                                        <?= (float)$m['quantity_in'] > 0 ? '+' . number_format((float)$m['quantity_in'], 2) : '-'; ?>
                                    </td>
                                    <td class="text-end font-monospace text-danger font-weight-bold">
                                        <?= (float)$m['quantity_out'] > 0 ? '-' . number_format((float)$m['quantity_out'], 2) : '-'; ?>
                                    </td>
                                    <td class="text-end font-monospace font-weight-bold fs-6 text-dark">
                                        <?= number_format((float)$m['balance_quantity'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total'] > 1): ?>
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center d-print-none">
                <small class="text-muted">Total: <?= number_format($pagination['count']); ?> entries</small>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php for ($p = 1; $p <= $pagination['total']; $p++): ?>
                            <li class="page-item <?= ($p == $pagination['current']) ? 'active' : ''; ?>">
                                <a class="page-link" href="<?= \Core\Helper::baseUrl('reports/stock-movement') . '?' . http_build_query(array_merge($_GET, ['page' => $p])); ?>">
                                    <?= $p; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>
