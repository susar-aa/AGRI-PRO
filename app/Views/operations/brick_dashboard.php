<?php
// Variables available: $project, $totalExpenses, $totalProduced, $productionRecordsCount, $totalTransferred
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <div class="mb-1">
            <a href="<?= \Core\Helper::baseUrl('operations/brick-manufacturing'); ?>" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i> Back to Brick Manufacturing</a>
        </div>
        <h4 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($project['project_name']); ?></h4>
        <p class="text-muted small mb-0">
            <span class="me-3"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($project['location']); ?></span>
            <span class="me-3"><i class="bi bi-calendar3 me-1"></i>Started: <?= date('M d, Y', strtotime($project['start_date'])); ?></span>
            <?php if ($project['expected_completion_date']): ?>
                <span><i class="bi bi-calendar-check me-1"></i>Expected: <?= date('M d, Y', strtotime($project['expected_completion_date'])); ?></span>
            <?php endif; ?>
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-<?= ($project['status'] === 'ACTIVE') ? 'success' : (($project['status'] === 'COMPLETED') ? 'secondary' : 'danger'); ?> px-3 py-2 rounded-pill fs-7">
            <?= htmlspecialchars($project['status']); ?>
        </span>
        
        <?php if ($project['status'] === 'ACTIVE'): ?>
            <form action="<?= \Core\Helper::baseUrl('operations/brick-manufacturing/update-status'); ?>" method="POST" class="d-inline">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $project['id']; ?>">
                <input type="hidden" name="status" value="COMPLETED">
                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="return confirm('Are you sure you want to mark this production project as COMPLETED?')">
                    <i class="bi bi-check-lg me-1"></i> Complete Project
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Project Metrics -->
<div class="row g-3 mb-4">
    <!-- Total Expenses -->
    <div class="col-6 col-md-3">
        <a href="<?= \Core\Helper::baseUrl('operations/brick-manufacturing/expenses?id=' . $project['id']); ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-danger border-4 h-100 hover-card">
                <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.65rem;">Total Expenses</span>
                <h4 class="fw-bold text-danger mb-0 font-monospace">LKR <?= number_format($totalExpenses, 2); ?></h4>
                <small class="text-muted mt-1 d-block">Click to view list</small>
            </div>
        </a>
    </div>
    <!-- Total Produced -->
    <div class="col-6 col-md-3">
        <a href="<?= \Core\Helper::baseUrl('operations/brick-manufacturing/production?id=' . $project['id']); ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-warning border-4 h-100 hover-card">
                <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.65rem;">Total Produced</span>
                <h4 class="fw-bold text-dark mb-0 font-monospace"><?= number_format($totalProduced); ?> / <?= number_format($project['planned_quantity']); ?> Pcs</h4>
                <small class="text-muted mt-1 d-block"><?= $productionRecordsCount; ?> production batches</small>
            </div>
        </a>
    </div>
    <!-- Marketplace Transferred -->
    <div class="col-6 col-md-3">
        <a href="<?= \Core\Helper::baseUrl('operations/brick-manufacturing/marketplace?id=' . $project['id']); ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-primary border-4 h-100 hover-card">
                <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.65rem;">Transferred</span>
                <h4 class="fw-bold text-primary mb-0 font-monospace"><?= number_format($totalTransferred); ?> Pcs</h4>
                <small class="text-muted mt-1 d-block">Click to transfer finished bricks</small>
            </div>
        </a>
    </div>
    <!-- Cost Per Brick -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-info border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.65rem;">Cost Per Brick</span>
            <h4 class="fw-bold text-info mb-0 font-monospace">LKR <?= number_format($totalProduced > 0 ? ($totalExpenses / $totalProduced) : 0, 2); ?></h4>
            <small class="text-muted mt-1 d-block">Based on posted expenses</small>
        </div>
    </div>
</div>

<!-- Workflow Navigation Menu Cards -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-diagram-2 text-success me-2"></i> Production Workflow options</h6>
        <div class="row g-3">
            <!-- Expenses -->
            <div class="col-12 col-md-4">
                <div class="card border p-3 rounded-4 h-100 hover-card d-flex flex-column" style="cursor: pointer;" onclick="location.href='<?= \Core\Helper::baseUrl('operations/brick-manufacturing/expenses?id=' . $project['id']); ?>'">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-wallet2 text-danger fs-4"></i>
                        <strong class="text-dark">Expenses</strong>
                    </div>
                    <span class="text-muted small mb-3">Record and view all operational costs like wood fuel, labor, cement, and sand.</span>
                    <button class="btn btn-sm btn-outline-danger rounded-pill w-100 mt-auto">Manage Expenses</button>
                </div>
            </div>
            <!-- Production logs -->
            <div class="col-12 col-md-4">
                <div class="card border p-3 rounded-4 h-100 hover-card d-flex flex-column" style="cursor: pointer;" onclick="location.href='<?= \Core\Helper::baseUrl('operations/brick-manufacturing/production?id=' . $project['id']); ?>'">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-gear-fill text-warning fs-4"></i>
                        <strong class="text-dark">Production Logs</strong>
                    </div>
                    <span class="text-muted small mb-3">Log brick clay production batches and finished outputs from kiln runs.</span>
                    <button class="btn btn-sm btn-outline-warning text-dark rounded-pill w-100 mt-auto">Record Production</button>
                </div>
            </div>
            <!-- Marketplace transfers -->
            <div class="col-12 col-md-4">
                <div class="card border p-3 rounded-4 h-100 hover-card d-flex flex-column" style="cursor: pointer;" onclick="location.href='<?= \Core\Helper::baseUrl('operations/brick-manufacturing/marketplace?id=' . $project['id']); ?>'">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-box-seam text-primary fs-4"></i>
                        <strong class="text-dark">Marketplace</strong>
                    </div>
                    <span class="text-muted small mb-3">Transfer finished bricks to marketplace stock ledger and set selling price.</span>
                    <button class="btn btn-sm btn-outline-primary rounded-pill w-100 mt-auto">Transfer to Marketplace</button>
                </div>
            </div>
        </div>
    </div>
</div>
