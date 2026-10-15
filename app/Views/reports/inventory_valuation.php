<?php 
$activeNav = $activeNav ?? 'reports_inventory_valuation';
$items = $items ?? [];
$warehouses = $warehouses ?? [];
$categories = $categories ?? [];
$selectedLocationId = $selectedLocationId ?? null;
$selectedCategoryId = $selectedCategoryId ?? null;
$totalValuation = $totalValuation ?? 0;
$totalQuantity = $totalQuantity ?? 0;
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= \Core\Helper::baseUrl('reports'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Hub</a>
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="bi bi-boxes text-success me-2"></i>Stock Valuation & Inventory Summary
                </h4>
            </div>
            <p class="text-muted small mb-0">Physical stock quantities on hand, weighted average costs (AVCO), and asset valuation</p>
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
            <form action="<?= \Core\Helper::baseUrl('reports/inventory-valuation'); ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small font-weight-bold text-muted mb-1">Warehouse / Location</label>
                    <select class="form-select form-select-sm" name="location_id">
                        <option value="">-- All Warehouses / Stores --</option>
                        <?php foreach ($warehouses as $wh): ?>
                            <option value="<?= $wh['id']; ?>" <?= ($selectedLocationId == $wh['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($wh['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-muted mb-1">Product Category</label>
                    <select class="form-select form-select-sm" name="category_id">
                        <option value="">-- All Product Categories --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id']; ?>" <?= ($selectedCategoryId == $cat['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-sm w-100 font-weight-bold">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="<?= \Core\Helper::baseUrl('reports/inventory-valuation'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Inventory Asset Valuation</div>
                    <div class="h3 font-weight-bold text-success mb-0">LKR <?= number_format($totalValuation, 2); ?></div>
                    <small class="text-muted">Total balance sheet inventory value</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-primary rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Physical Units on Hand</div>
                    <div class="h3 font-weight-bold text-primary mb-0"><?= number_format($totalQuantity, 2); ?></div>
                    <small class="text-muted">Combined warehouse stocks</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-info rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Active Stock Lines / SKUs</div>
                    <div class="h3 font-weight-bold text-info mb-0"><?= count($items); ?> SKUs</div>
                    <small class="text-muted">Cataloged products</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Valuation Table Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Warehouse Stock Valuation Schedule</h6>
                <small class="text-muted">As of: <?= date('Y-m-d H:i'); ?></small>
            </div>
            <span class="badge bg-light text-dark border">Cost Method: Weighted Average (AVCO)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 14%;">Product Code</th>
                            <th style="width: 28%;">Product Name</th>
                            <th style="width: 16%;">Category</th>
                            <th style="width: 14%;">Warehouse</th>
                            <th style="width: 12%; text-align: right;">Quantity on Hand</th>
                            <th style="width: 12%; text-align: right;">Average Unit Cost (LKR)</th>
                            <th style="width: 14%; text-align: right;">Total Valuation (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No inventory records found for the selected warehouses.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="font-monospace text-muted small"><?= htmlspecialchars($item['product_code']); ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($item['name_en']); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($item['category_name']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-dark border"><?= htmlspecialchars($item['warehouse_name']); ?></span>
                                    </td>
                                    <td class="text-end font-monospace font-weight-bold text-primary">
                                        <?= number_format((float)$item['quantity_on_hand'], 2); ?>
                                    </td>
                                    <td class="text-end font-monospace text-muted">
                                        <?= number_format((float)$item['average_cost'], 2); ?>
                                    </td>
                                    <td class="text-end font-monospace font-weight-bold text-success fs-6">
                                        <?= number_format((float)$item['inventory_value'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-dark font-weight-bold">
                        <tr>
                            <td colspan="4" class="text-uppercase py-3">PORTFOLIO INVENTORY TOTALS</td>
                            <td class="text-end font-monospace text-warning py-3"><?= number_format($totalQuantity, 2); ?></td>
                            <td class="py-3"></td>
                            <td class="text-end font-monospace text-warning py-3">LKR <?= number_format($totalValuation, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
