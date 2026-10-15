<?php 
$activeNav = $activeNav ?? 'reports_purchases';
$purchases = $purchases ?? [];
$suppliers = $suppliers ?? [];
$filters = $filters ?? [];
$pagination = $pagination ?? ['current' => 1, 'total' => 1, 'count' => 0];

$totQty = array_sum(array_column($purchases, 'quantity'));
$totCost = array_sum(array_column($purchases, 'total_cost'));
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= \Core\Helper::baseUrl('reports'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Hub</a>
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="bi bi-cart-check text-warning me-2"></i>Purchases & Goods Receipt (GRN) Report
                </h4>
            </div>
            <p class="text-muted small mb-0">Supplier inward consignments, received quantities, cost prices, and physical receipts</p>
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
            <form action="<?= \Core\Helper::baseUrl('reports/purchases'); ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">From Date</label>
                    <input type="date" class="form-control form-control-sm" name="from_date" value="<?= htmlspecialchars($filters['from_date'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">To Date</label>
                    <input type="date" class="form-control form-control-sm" name="to_date" value="<?= htmlspecialchars($filters['to_date'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-muted mb-1">Supplier</label>
                    <select class="form-select form-select-sm" name="supplier_id">
                        <option value="">-- All Suppliers --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id']; ?>" <?= (($filters['supplier_id'] ?? '') == $s['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($s['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-warning btn-sm w-100 font-weight-bold text-dark">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="<?= \Core\Helper::baseUrl('reports/purchases'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-warning rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Page Purchase Inflow</div>
                    <div class="h4 font-weight-bold text-dark mb-0">LKR <?= number_format($totCost, 2); ?></div>
                    <small class="text-muted">Current page goods value</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-primary rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Quantity Received</div>
                    <div class="h4 font-weight-bold text-primary mb-0"><?= number_format($totQty, 2); ?></div>
                    <small class="text-muted">Units / pieces delivered</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total GRN Records</div>
                    <div class="h4 font-weight-bold text-success mb-0"><?= number_format($pagination['count']); ?></div>
                    <small class="text-muted">Recorded deliveries</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchases Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Goods Received Registry</h6>
                <small class="text-muted">Page <?= $pagination['current']; ?> of <?= max(1, $pagination['total']); ?></small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 12%;">Date</th>
                            <th style="width: 14%;">GRN Ref #</th>
                            <th style="width: 20%;">Supplier</th>
                            <th style="width: 22%;">Product Particulars</th>
                            <th style="width: 12%;">Warehouse</th>
                            <th style="width: 10%; text-align: right;">Unit Cost</th>
                            <th style="width: 10%; text-align: right;">Qty</th>
                            <th style="width: 12%; text-align: right;">Total Cost (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($purchases)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No Goods Received Notes (GRN) found for the selected criteria.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($purchases as $row): ?>
                                <tr>
                                    <td class="font-monospace text-muted small"><?= htmlspecialchars($row['movement_date']); ?></td>
                                    <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($row['reference_number'] ?: 'GRN-DIRECT'); ?></td>
                                    <td><?= htmlspecialchars($row['supplier_name']); ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['product_name']); ?></div>
                                        <small class="font-monospace text-muted"><?= htmlspecialchars($row['product_code']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($row['warehouse_name']); ?></span>
                                    </td>
                                    <td class="text-end font-monospace"><?= number_format((float)$row['unit_cost'], 2); ?></td>
                                    <td class="text-end font-monospace font-weight-bold text-primary"><?= number_format((float)$row['quantity'], 2); ?></td>
                                    <td class="text-end font-monospace font-weight-bold text-dark"><?= number_format((float)$row['total_cost'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light font-weight-bold">
                        <tr>
                            <td colspan="6" class="text-uppercase py-3">PAGE TOTALS</td>
                            <td class="text-end font-monospace text-primary py-3"><?= number_format($totQty, 2); ?></td>
                            <td class="text-end font-monospace text-dark py-3">LKR <?= number_format($totCost, 2); ?></td>
                        </tr>
                    </tfoot>
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
                                <a class="page-link" href="<?= \Core\Helper::baseUrl('reports/purchases') . '?' . http_build_query(array_merge($_GET, ['page' => $p])); ?>">
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
