<?php
// Variables available: $stats, $activeProjects, $completedProjects, $products
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-bricks text-danger me-2"></i>Brick Manufacturing Overview</h4>
        <p class="text-muted small mb-0">Manage brick clay production projects, batches, operational costs, and inventory transfers.</p>
    </div>
    <div>
        <button class="btn btn-success rounded-pill px-4 shadow-sm" style="background-color: #1b4332; border-color: #1b4332;" data-bs-toggle="modal" data-bs-target="#newProjectModal">
            <i class="bi bi-plus-lg me-1"></i> Start New Production
        </button>
    </div>
</div>

<!-- Quick Statistics -->
<div class="row g-3 mb-4">
    <!-- Active Projects -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-primary border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Active Projects</span>
            <div class="d-flex align-items-baseline">
                <h3 class="fw-bold text-dark mb-0 font-monospace"><?= $stats['active_projects']; ?></h3>
            </div>
        </div>
    </div>
    <!-- Completed Projects -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-success border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Completed Projects</span>
            <div class="d-flex align-items-baseline">
                <h3 class="fw-bold text-dark mb-0 font-monospace"><?= $stats['completed_projects']; ?></h3>
            </div>
        </div>
    </div>
    <!-- Total Produced -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-warning border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Total Produced</span>
            <div class="d-flex align-items-baseline">
                <h3 class="fw-bold text-dark mb-0 font-monospace"><?= number_format($stats['total_produced']); ?> Pcs</h3>
            </div>
        </div>
    </div>
    <!-- Total Expenses -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-danger border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Total Expenses</span>
            <div class="d-flex align-items-baseline">
                <h4 class="fw-bold text-danger mb-0 font-monospace" style="font-size: 1.15rem;">LKR <?= number_format($stats['total_expenses'], 2); ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- Tabbed Projects Section -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-3 pb-0">
        <ul class="nav nav-tabs border-bottom-0" id="brickTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-dark border-bottom border-3 border-success" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-pane" type="button" role="tab" aria-controls="active-pane" aria-selected="true">
                    <i class="bi bi-play-circle me-1"></i> Active Projects (<?= count($activeProjects); ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-muted" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-pane" type="button" role="tab" aria-controls="completed-pane" aria-selected="false">
                    <i class="bi bi-check2-all me-1"></i> Completed Projects (<?= count($completedProjects); ?>)
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body p-4">
        <div class="tab-content" id="brickTabsContent">
            <!-- Active Projects Pane -->
            <div class="tab-pane fade show active" id="active-pane" role="tabpanel" aria-labelledby="active-tab" tabindex="0">
                <?php if (empty($activeProjects)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bricks fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        <p class="mb-0">No active brick production projects found.</p>
                        <button class="btn btn-sm btn-success rounded-pill mt-3" data-bs-toggle="modal" data-bs-target="#newProjectModal">Start First Project</button>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($activeProjects as $p): ?>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card h-100 border rounded-4 shadow-sm hover-card" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onclick="location.href='<?= \Core\Helper::baseUrl('operations/brick-manufacturing/view?id=' . $p['id']); ?>'">
                                    <div class="card-body p-3 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($p['project_name']); ?></h6>
                                            <span class="badge bg-success rounded-pill px-2" style="font-size: 0.7rem;">ACTIVE</span>
                                        </div>
                                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($p['location']); ?></p>
                                        
                                        <div class="mb-3 mt-auto">
                                            <span class="text-muted small d-block mb-1">Brick Type:</span>
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.72rem;">
                                                <?= htmlspecialchars($p['product_name']); ?> (Planned: <?= number_format($p['planned_quantity'], 0); ?> <?= htmlspecialchars($p['unit']); ?>)
                                            </span>
                                        </div>

                                        <div class="border-top pt-2 d-flex justify-content-between align-items-center mt-2" style="font-size: 0.78rem;">
                                            <span class="text-muted">Expenses: <strong class="text-danger font-monospace">LKR <?= number_format($p['total_expenses'], 2); ?></strong></span>
                                            <span class="text-muted">Start: <?= date('Y-m-d', strtotime($p['start_date'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Completed Projects Pane -->
            <div class="tab-pane fade" id="completed-pane" role="tabpanel" aria-labelledby="completed-tab" tabindex="0">
                <?php if (empty($completedProjects)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check2-all fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        <p class="mb-0">No completed brick production projects recorded.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($completedProjects as $p): ?>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card h-100 border rounded-4 shadow-sm hover-card" style="cursor: pointer;" onclick="location.href='<?= \Core\Helper::baseUrl('operations/brick-manufacturing/view?id=' . $p['id']); ?>'">
                                    <div class="card-body p-3 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($p['project_name']); ?></h6>
                                            <span class="badge bg-secondary rounded-pill px-2" style="font-size: 0.7rem;">COMPLETED</span>
                                        </div>
                                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($p['location']); ?></p>
                                        
                                        <div class="mb-3 mt-auto">
                                            <span class="text-muted small d-block mb-1">Brick Type:</span>
                                            <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.72rem;">
                                                <?= htmlspecialchars($p['product_name']); ?> (Produced: <?= number_format($p['planned_quantity'], 0); ?> <?= htmlspecialchars($p['unit']); ?>)
                                            </span>
                                        </div>

                                        <div class="border-top pt-2 d-flex justify-content-between align-items-center mt-2" style="font-size: 0.78rem;">
                                            <span class="text-muted">Expenses: <strong class="text-danger font-monospace">LKR <?= number_format($p['total_expenses'], 2); ?></strong></span>
                                            <span class="text-muted">Start: <?= date('Y-m-d', strtotime($p['start_date'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Start New Production -->
<div class="modal fade" id="newProjectModal" tabindex="-1" aria-labelledby="newProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-success text-white border-0 py-3" style="background: linear-gradient(135deg, #1e4620, #2e7d32) !important;">
                <h5 class="modal-title fw-bold" id="newProjectModalLabel"><i class="bi bi-plus-lg me-2"></i>Start New Brick Production</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('operations/brick-manufacturing/store'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label for="project_name" class="form-label small fw-semibold">Production Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm rounded-3" id="project_name" name="project_name" placeholder="e.g. 2026 Yatagama Kiln Batch 01" required>
                        </div>
                        <div class="col-md-4">
                            <label for="start_date" class="form-label small fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm rounded-3" id="start_date" name="start_date" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-gear-fill text-success me-2"></i>Product Details</h6>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label for="product_id" class="form-label small fw-semibold">Select Brick Product / Type <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm rounded-3" id="product_id" name="product_id" required>
                                <option value="">-- Select Brick Type --</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id']; ?>"><?= htmlspecialchars($p['product_code']); ?> - <?= htmlspecialchars($p['name_en']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="unit" class="form-label small fw-semibold">Unit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm rounded-3" id="unit" name="unit" value="Pieces" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label small fw-semibold">Description / Notes</label>
                        <textarea class="form-control form-control-sm rounded-3" id="notes" name="notes" rows="3" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Start Production</button>
                </div>
            </form>
        </div>
    </div>
</div>
