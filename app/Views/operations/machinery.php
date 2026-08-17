<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Machinery Renting Operations Control Panel</h4>
        <p class="text-muted small mb-0">Manage mechanical equipment, rentals booking schedules, expenses, and profitability.</p>
    </div>
</div>

<!-- Quick Statistics -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3 mb-4">
    <!-- Card 1 -->
    <div class="col">
        <div class="card kpi-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="kpi-icon bg-success-subtle text-success p-2 rounded-3 fs-4" style="background-color: #e8f5e9 !important;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <span class="text-muted small d-block mb-0" style="font-size: 0.75rem; font-weight: 500;">Available Machinery</span>
                    <h4 class="fw-bold text-success mb-0 font-monospace" style="font-size: 1.25rem;"><?= $stats['available']; ?></h4>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 2 -->
    <div class="col">
        <div class="card kpi-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="kpi-icon bg-warning-subtle text-warning p-2 rounded-3 fs-4" style="background-color: #fff3cd !important;">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div>
                    <span class="text-muted small d-block mb-0" style="font-size: 0.75rem; font-weight: 500;">Currently Rented</span>
                    <h4 class="fw-bold text-warning mb-0 font-monospace" style="font-size: 1.25rem;"><?= $stats['rented']; ?></h4>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 3 -->
    <div class="col">
        <div class="card kpi-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="kpi-icon bg-primary-subtle text-primary p-2 rounded-3 fs-4" style="background-color: #cfe2ff !important;">
                    <i class="bi bi-play-circle-fill"></i>
                </div>
                <div>
                    <span class="text-muted small d-block mb-0" style="font-size: 0.75rem; font-weight: 500;">Active Rentals</span>
                    <h4 class="fw-bold text-primary mb-0 font-monospace" style="font-size: 1.25rem;"><?= $stats['active_rentals']; ?></h4>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 4 -->
    <div class="col">
        <div class="card kpi-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="kpi-icon bg-info-subtle text-info p-2 rounded-3 fs-4" style="background-color: #cff4fc !important;">
                    <i class="bi bi-check2-all"></i>
                </div>
                <div>
                    <span class="text-muted small d-block mb-0" style="font-size: 0.75rem; font-weight: 500;">Completed Rentals</span>
                    <h4 class="fw-bold text-info mb-0 font-monospace" style="font-size: 1.25rem;"><?= $stats['completed_rentals']; ?></h4>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 5 -->
    <div class="col">
        <div class="card kpi-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="kpi-icon bg-danger-subtle text-danger p-2 rounded-3 fs-4" style="background-color: #f8d7da !important;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <span class="text-muted small d-block mb-0" style="font-size: 0.75rem; font-weight: 500;">Rental Revenue</span>
                    <h5 class="fw-bold text-danger mb-0 font-monospace" style="font-size: 1.05rem; white-space: nowrap;">LKR <?= number_format($stats['revenue'], 2); ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Options -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-diagram-2 text-success me-2"></i> Related Options</h6>
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="card border p-3 rounded-4 h-100 text-center shadow-none hover-shadow">
                    <i class="bi bi-calendar-event text-success fs-3 mb-2"></i>
                    <strong class="text-dark d-block">Rentals</strong>
                    <span class="text-muted small d-block mb-2">Manage rental orders</span>
                    <a href="<?= \Core\Helper::baseUrl('modules/machinery-rentals'); ?>" class="btn btn-sm btn-success w-100 rounded-pill mt-auto" style="background-color: #1b4332; border-color: #1b4332;">Configure</a>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border p-3 rounded-4 h-100 text-center shadow-none hover-shadow">
                    <i class="bi bi-wallet2 text-danger fs-3 mb-2"></i>
                    <strong class="text-dark d-block">Rental Expenses</strong>
                    <span class="text-muted small d-block mb-2">Repair and fuel expense logs</span>
                    <a href="<?= \Core\Helper::baseUrl('expenses?source_module=MACHINERY'); ?>" class="btn btn-sm btn-success w-100 rounded-pill mt-auto" style="background-color: #1b4332; border-color: #1b4332;">Configure</a>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border p-3 rounded-4 h-100 text-center shadow-none hover-shadow">
                    <i class="bi bi-tools text-info fs-3 mb-2"></i>
                    <strong class="text-dark d-block">Machinery Registry</strong>
                    <span class="text-muted small d-block mb-2">Manage mechanical assets catalog</span>
                    <a href="<?= \Core\Helper::baseUrl('modules/machinery'); ?>" class="btn btn-sm btn-success w-100 rounded-pill mt-auto" style="background-color: #1b4332; border-color: #1b4332;">Configure</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history text-success me-2"></i> Recent Activity Logs</h6>
        <div class="text-center py-4 text-muted small">
            <i class="bi bi-info-circle fs-4 d-block mb-1"></i> No recent activity recorded.
        </div>
    </div>
</div>
