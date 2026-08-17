<?php
// Variables available: $project, $crops, $totalExpenses, $totalHarvest, $harvestRecordsCount, $totalTransferred
?>

<div class="mb-3">
    <a href="<?= \Core\Helper::baseUrl('operations/plantation'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill fw-medium">
        <i class="bi bi-arrow-left me-1"></i> Back to All Plantations
    </a>
</div>

<!-- Project Hero Banner -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);">
    <div class="card-body p-4 p-md-5 position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-md-8 text-white">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white rounded-pill px-3 py-1 shadow-sm" style="color: #1b4332; font-weight: 700; letter-spacing: 0.5px;">
                        <i class="bi bi-circle-fill small me-1 <?= ($project['status'] === 'ACTIVE') ? 'text-success' : 'text-secondary'; ?>"></i>
                        <?= $project['status']; ?>
                    </span>
                    <span class="badge bg-dark bg-opacity-25 rounded-pill px-3 py-1 border border-white border-opacity-25">
                        <i class="bi bi-calendar-event me-1"></i> Started <?= date('M j, Y', strtotime($project['start_date'])); ?>
                    </span>
                </div>
                <h2 class="fw-bold mb-1 display-6"><?= htmlspecialchars($project['project_name']); ?></h2>
                <p class="mb-0 text-white-50 fs-5"><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($project['location']); ?></p>
            </div>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <form action="<?= \Core\Helper::baseUrl('operations/plantation/update-status'); ?>" method="POST" class="d-inline-block bg-white bg-opacity-10 p-2 rounded-4 border border-white border-opacity-25 shadow-sm">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $project['id']; ?>">
                    <label class="text-white-50 small d-block text-start px-2 mb-1 fw-medium">Update Status</label>
                    <select class="form-select border-0 shadow-none bg-white text-dark rounded-pill fw-medium" name="status" onchange="this.form.submit()" style="min-width: 160px;">
                        <option value="ACTIVE" <?= $project['status'] === 'ACTIVE' ? 'selected' : ''; ?>>ACTIVE</option>
                        <option value="COMPLETED" <?= $project['status'] === 'COMPLETED' ? 'selected' : ''; ?>>COMPLETED</option>
                        <option value="CANCELLED" <?= $project['status'] === 'CANCELLED' ? 'selected' : ''; ?>>CANCELLED</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
    <!-- Decorative Icon -->
    <i class="bi bi-flower2 text-white position-absolute opacity-10" style="font-size: 15rem; right: -2rem; bottom: -4rem; z-index: 1;"></i>
</div>

<!-- Project Totals -->
<div class="row g-3 mb-4">
    <!-- Total Expenses -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-danger border-4">
            <span class="text-muted small d-block mb-1 fw-bold uppercase-label">Total Expenses</span>
            <h4 class="fw-bold text-dark mb-0 font-monospace">LKR <?= number_format($totalExpenses, 2); ?></h4>
        </div>
    </div>
    <!-- Total Harvest -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-warning border-4">
            <span class="text-muted small d-block mb-1 fw-bold uppercase-label">Total Harvest Logged</span>
            <h4 class="fw-bold text-dark mb-0 font-monospace"><?= number_format($totalHarvest, 2); ?> <small class="text-muted fs-6 fw-normal">Units</small></h4>
        </div>
    </div>
    <!-- Transferred to Market -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-info border-4">
            <span class="text-muted small d-block mb-1 fw-bold uppercase-label">Transferred to Market</span>
            <h4 class="fw-bold text-dark mb-0 font-monospace"><?= number_format($totalTransferred, 2); ?> <small class="text-muted fs-6 fw-normal">Units</small></h4>
        </div>
    </div>
    <!-- Remaining Yield -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-success border-4">
            <span class="text-muted small d-block mb-1 fw-bold uppercase-label">Remaining Yield</span>
            <h4 class="fw-bold text-dark mb-0 font-monospace"><?= number_format($totalHarvest - $totalTransferred, 2); ?> <small class="text-muted fs-6 fw-normal">Units</small></h4>
        </div>
    </div>
</div>

<!-- Main Operational Modules -->
<h5 class="fw-bold text-dark mb-3 ps-2 border-start border-4 border-success">Core Operations</h5>
<div class="row g-4 mb-5">
    
    <!-- Crops Module -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 feature-card overflow-hidden" onclick="location.href='<?= \Core\Helper::baseUrl('operations/plantation/crops?id=' . $project['id']); ?>'">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                <div class="icon-circle bg-success-subtle text-success mx-auto mb-3">
                    <i class="bi bi-seedling fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Plants & Crops</h5>
                <p class="text-muted small mb-0">Manage the seeds and crops being cultivated in this project.</p>
                <div class="mt-3 pt-3 border-top">
                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-medium">
                        <?= count($crops); ?> Crop Varieties
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Harvesting Module -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 feature-card overflow-hidden" onclick="location.href='<?= \Core\Helper::baseUrl('operations/plantation/harvesting?id=' . $project['id']); ?>'">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                <div class="icon-circle bg-warning-subtle text-warning mx-auto mb-3">
                    <i class="bi bi-basket fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Yield Harvesting</h5>
                <p class="text-muted small mb-0">Record daily or weekly harvests collected from the land.</p>
                <div class="mt-3 pt-3 border-top">
                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-medium">
                        <?= number_format($totalHarvest, 2); ?> Total Yield Logged
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Marketplace Module -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 feature-card overflow-hidden" onclick="location.href='<?= \Core\Helper::baseUrl('operations/plantation/marketplace?id=' . $project['id']); ?>'">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                <div class="icon-circle bg-info-subtle text-info mx-auto mb-3">
                    <i class="bi bi-shop fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Marketplace</h5>
                <p class="text-muted small mb-0">Transfer your harvested yield to the marketplace for direct sales.</p>
                <div class="mt-3 pt-3 border-top">
                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-medium">
                        <?= number_format($totalTransferred, 2); ?> Transferred
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Module -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 feature-card overflow-hidden" onclick="location.href='<?= \Core\Helper::baseUrl('operations/plantation/expenses?id=' . $project['id']); ?>'">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                <div class="icon-circle bg-danger-subtle text-danger mx-auto mb-3">
                    <i class="bi bi-wallet2 fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Expenses</h5>
                <p class="text-muted small mb-0">Record and track financial costs for this project.</p>
                <div class="mt-3 pt-3 border-top">
                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-medium">
                        View Ledger
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Crops Overview Summary -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-list-ul text-success me-2"></i>Current Cultivation Summary</h6>
        <a href="<?= \Core\Helper::baseUrl('operations/plantation/crops?id=' . $project['id']); ?>" class="btn btn-sm btn-light rounded-pill fw-medium px-3 text-success border">
            Manage <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($crops)): ?>
            <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-seedling text-muted fs-3"></i>
                </div>
                <h6 class="fw-bold text-dark">No crops planted yet</h6>
                <p class="text-muted small mb-0">Click 'Manage' to add crops to this plantation project.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small uppercase-label">
                        <tr>
                            <th class="ps-4">Product / Crop</th>
                            <th>Planned Qty</th>
                            <th class="pe-4">Cultivation Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($crops as $c): ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-success-subtle rounded-3 d-flex align-items-center justify-content-center text-success" style="width: 40px; height: 40px;">
                                            <i class="bi bi-flower1 fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($c['product_name']); ?></div>
                                            <small class="text-muted font-monospace bg-light px-2 rounded"><?= htmlspecialchars($c['product_code']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="fw-bold text-dark mb-0 font-monospace"><?= number_format($c['planned_quantity'], 2); ?> <span class="fs-6 text-muted fw-normal"><?= htmlspecialchars($c['unit']); ?></span></h5>
                                </td>
                                <td class="pe-4 text-muted small">
                                    <?= htmlspecialchars($c['notes'] ?: '-'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.feature-card {
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    border: 2px solid transparent !important;
}
.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
    border-color: #e9ecef !important;
}
.icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}
.feature-card:hover .icon-circle {
    transform: scale(1.1);
}
.uppercase-label {
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-size: 0.7rem;
    font-weight: 700;
}
</style>
