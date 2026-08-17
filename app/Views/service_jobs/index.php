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
        <h4 class="fw-bold mb-1 text-dark">Service Jobs Registry Logs</h4>
        <p class="text-muted small mb-0">Track and monitor field plowings, rentals, grinding runs, and construction jobs performed for cooperative customers.</p>
    </div>
    <div>
        <?php if (\Core\Auth::hasPermission('service_jobs.create')): ?>
            <a href="<?= \Core\Helper::baseUrl('modules/service-jobs/create'); ?>" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                <i class="bi bi-plus-lg me-1"></i> Register Service Job
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('modules/service-jobs'); ?>" method="GET" class="row g-3 small">
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Search Job</label>
                <input type="text" class="form-control form-control-sm" name="search" value="<?= htmlspecialchars($filters['search']); ?>" placeholder="Job #, location, remarks...">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Customer</label>
                <select class="form-select form-select-sm" name="customer_id">
                    <option value="">-- All Customers --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id']; ?>" <?= ($filters['customer_id'] == $c['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($c['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold">Service</label>
                <select class="form-select form-select-sm" name="service_id">
                    <option value="">-- All --</option>
                    <?php foreach ($services as $srv): ?>
                        <option value="<?= $srv['id']; ?>" <?= ($filters['service_id'] == $srv['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($srv['service_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">-- All --</option>
                    <option value="OPEN" <?= ($filters['status'] === 'OPEN') ? 'selected' : ''; ?>>Open</option>
                    <option value="IN_PROGRESS" <?= ($filters['status'] === 'IN_PROGRESS') ? 'selected' : ''; ?>>In Progress</option>
                    <option value="COMPLETED" <?= ($filters['status'] === 'COMPLETED') ? 'selected' : ''; ?>>Completed</option>
                    <option value="CANCELLED" <?= ($filters['status'] === 'CANCELLED') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 w-100" style="background-color: #1b4332; border-color: #1b4332;">Filter</button>
                <a href="<?= \Core\Helper::baseUrl('modules/service-jobs'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Job Number</th>
                        <th>Customer</th>
                        <th>Service Job Type</th>
                        <th>Start Date</th>
                        <th class="text-end">Revenue</th>
                        <th class="text-end">Cost</th>
                        <th class="text-end">Gross Profit</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($jobs)): ?>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td class="fw-bold font-monospace">
                                    <a href="<?= \Core\Helper::baseUrl('modules/service-jobs/view?id=' . $job['id']); ?>" class="text-success text-decoration-none">
                                        <?= htmlspecialchars($job['job_number']); ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($job['customer_name']); ?></div>
                                    <small class="text-muted font-monospace"><?= htmlspecialchars($job['party_code']); ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($job['service_name']); ?></div>
                                    <small class="text-muted font-monospace"><?= htmlspecialchars($job['service_code']); ?></small>
                                </td>
                                <td><?= htmlspecialchars($job['start_date']); ?></td>
                                <td class="text-end font-monospace fw-semibold text-success">LKR <?= number_format($job['revenue'], 2); ?></td>
                                <td class="text-end font-monospace text-danger">LKR <?= number_format($job['total_cost'], 2); ?></td>
                                <td class="text-end font-monospace fw-bold <?= $job['profit'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    LKR <?= number_format($job['profit'], 2); ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $st = $job['status'];
                                    $badgeClass = 'bg-secondary';
                                    if ($st === 'OPEN') $badgeClass = 'bg-info text-dark';
                                    elseif ($st === 'IN_PROGRESS') $badgeClass = 'bg-warning text-dark';
                                    elseif ($st === 'COMPLETED') $badgeClass = 'bg-success';
                                    elseif ($st === 'CANCELLED') $badgeClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badgeClass ?> px-3 py-1"><?= htmlspecialchars($st); ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group gap-1">
                                        <a href="<?= \Core\Helper::baseUrl('modules/service-jobs/view?id=' . $job['id']); ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">View</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No service job logs recorded.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
