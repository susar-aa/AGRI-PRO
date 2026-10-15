<?php 
$activeNav = $activeNav ?? 'reports_machinery';
$records = $records ?? [];
$totals = $totals ?? ['total_machinery' => 0, 'total_rentals' => 0, 'total_revenue' => 0];
$filters = $filters ?? [];
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= \Core\Helper::baseUrl('reports'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Hub</a>
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="bi bi-truck-flatbed text-primary me-2"></i>Machinery Rental & Utilization Report
                </h4>
            </div>
            <p class="text-muted small mb-0">Tractor, harvester, plow, and agricultural equipment rental revenue and utilization analytics</p>
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
            <form action="<?= \Core\Helper::baseUrl('reports/machinery-utilization'); ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-md-9">
                    <label class="form-label small font-weight-bold text-muted mb-1">Fleet Operational Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">-- All Equipment Statuses --</option>
                        <option value="AVAILABLE" <?= (($filters['status'] ?? '') === 'AVAILABLE') ? 'selected' : ''; ?>>AVAILABLE</option>
                        <option value="RENTED" <?= (($filters['status'] ?? '') === 'RENTED') ? 'selected' : ''; ?>>RENTED / IN FIELD</option>
                        <option value="MAINTENANCE" <?= (($filters['status'] ?? '') === 'MAINTENANCE') ? 'selected' : ''; ?>>MAINTENANCE / OVERHAUL</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 font-weight-bold">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="<?= \Core\Helper::baseUrl('reports/machinery-utilization'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-primary rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Machinery Fleet</div>
                    <div class="h4 font-weight-bold text-primary mb-0"><?= number_format($totals['total_machinery']); ?> Machines</div>
                    <small class="text-muted">Registered agricultural assets</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-info rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Completed Rental Contracts</div>
                    <div class="h4 font-weight-bold text-info mb-0"><?= number_format($totals['total_rentals']); ?> Bookings</div>
                    <small class="text-muted">Total field deployments</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Rental Revenue</div>
                    <div class="h4 font-weight-bold text-success mb-0">LKR <?= number_format($totals['total_revenue'], 2); ?></div>
                    <small class="text-muted">Cumulative gross rental earnings</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Fleet Machinery Performance Registry</h6>
                <small class="text-muted">Ranked by highest rental revenue generated</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 14%;">Equipment Code</th>
                            <th style="width: 26%;">Machinery Description</th>
                            <th style="width: 10%;">Unit</th>
                            <th style="width: 12%; text-align: right;">Rate (LKR)</th>
                            <th style="width: 12%;">Status</th>
                            <th style="width: 12%; text-align: center;">Bookings</th>
                            <th style="width: 14%; text-align: right;">Revenue Earned (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No machinery fleet equipment registered yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($records as $m): ?>
                                <tr>
                                    <td class="font-monospace text-muted small"><?= htmlspecialchars($m['machinery_code']); ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($m['machinery_name']); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($m['rental_unit'] ?: 'Hour'); ?></span>
                                    </td>
                                    <td class="text-end font-monospace"><?= number_format((float)$m['default_rental_rate'], 2); ?></td>
                                    <td>
                                        <?php 
                                        $badge = match($m['status']) {
                                            'AVAILABLE' => 'bg-success',
                                            'RENTED' => 'bg-primary',
                                            'MAINTENANCE' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badge; ?> small"><?= htmlspecialchars($m['status']); ?></span>
                                    </td>
                                    <td class="text-center font-monospace font-weight-bold"><?= number_format($m['total_rentals']); ?></td>
                                    <td class="text-end font-monospace font-weight-bold fs-6 text-success">
                                        <?= number_format((float)$m['total_revenue'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-dark font-weight-bold">
                        <tr>
                            <td colspan="5" class="text-uppercase py-3">FLEET TOTALS</td>
                            <td class="text-center font-monospace py-3 text-warning"><?= number_format($totals['total_rentals']); ?></td>
                            <td class="text-end font-monospace py-3 text-warning">LKR <?= number_format($totals['total_revenue'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
