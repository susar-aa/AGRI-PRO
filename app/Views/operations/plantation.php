<?php
// Variables available: $stats, $activeProjects, $completedProjects, $products
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-flower2 text-success me-2"></i>Plantation Operations Overview</h4>
        <p class="text-muted small mb-0">Manage plantation projects, land utilization, crops cultivation, expenses, and harvests.</p>
    </div>
    <div>
        <button class="btn btn-success rounded-pill px-4 shadow-sm" style="background-color: #1b4332; border-color: #1b4332;" data-bs-toggle="modal" data-bs-target="#newProjectModal">
            <i class="bi bi-plus-lg me-1"></i> Start New Project
        </button>
    </div>
</div>

<!-- Quick Statistics -->
<div class="row g-3 mb-4">
    <!-- Active Projects -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-primary border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold uppercase-label">Active Projects</span>
            <div class="d-flex align-items-baseline">
                <h3 class="fw-bold text-dark mb-0 font-monospace"><?= $stats['active_projects']; ?></h3>
            </div>
        </div>
    </div>
    <!-- Completed Projects -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-success border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold uppercase-label">Completed Projects</span>
            <div class="d-flex align-items-baseline">
                <h3 class="fw-bold text-dark mb-0 font-monospace"><?= $stats['completed_projects']; ?></h3>
            </div>
        </div>
    </div>
    <!-- Total Expenses -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-danger border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold uppercase-label">Total Expenses</span>
            <div class="d-flex align-items-baseline">
                <h4 class="fw-bold text-danger mb-0 font-monospace">LKR <?= number_format($stats['total_expenses'], 2); ?></h4>
            </div>
        </div>
    </div>
    <!-- Total Harvest -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-warning border-4 h-100">
            <span class="text-muted small d-block mb-1 fw-bold uppercase-label">Total Harvest</span>
            <div class="d-flex align-items-baseline">
                <h3 class="fw-bold text-dark mb-0 font-monospace"><?= $stats['total_harvest']; ?> KG</h3>
            </div>
        </div>
    </div>
</div>

<!-- Tabbed Projects Section -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-3 pb-0">
        <ul class="nav nav-tabs border-bottom-0" id="plantationTabs" role="tablist">
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
        <div class="tab-content" id="plantationTabsContent">
            <!-- Active Projects Pane -->
            <div class="tab-pane fade show active" id="active-pane" role="tabpanel" aria-labelledby="active-tab" tabindex="0">
                <?php if (empty($activeProjects)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-flower2 fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        <p class="mb-0">No active plantation projects found.</p>
                        <button class="btn btn-sm btn-success rounded-pill mt-3" data-bs-toggle="modal" data-bs-target="#newProjectModal">Start First Project</button>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($activeProjects as $p): ?>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card h-100 border rounded-4 shadow-sm hover-card" style="cursor: pointer;" onclick="location.href='<?= \Core\Helper::baseUrl('operations/plantation/view?id=' . $p['id']); ?>'">
                                    <div class="card-body p-3 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($p['project_name']); ?></h6>
                                            <span class="badge bg-success rounded-pill px-2" style="font-size: 0.7rem;">ACTIVE</span>
                                        </div>
                                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($p['location']); ?></p>
                                        
                                        <div class="mb-3 mt-auto">
                                            <span class="text-muted small d-block mb-1">Crops cultivating:</span>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php if (empty($p['crops'])): ?>
                                                    <span class="badge bg-light text-muted border px-2 py-1">No crops selected</span>
                                                <?php else: ?>
                                                    <?php foreach ($p['crops'] as $c): ?>
                                                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.72rem;">
                                                            <?= htmlspecialchars($c['product_name']); ?> (<?= number_format($c['planned_quantity'], 0); ?> <?= htmlspecialchars($c['unit']); ?>)
                                                        </span>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
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
                        <p class="mb-0">No completed plantation projects recorded.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($completedProjects as $p): ?>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card h-100 border rounded-4 shadow-sm hover-card" style="cursor: pointer;" onclick="location.href='<?= \Core\Helper::baseUrl('operations/plantation/view?id=' . $p['id']); ?>'">
                                    <div class="card-body p-3 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($p['project_name']); ?></h6>
                                            <span class="badge bg-secondary rounded-pill px-2" style="font-size: 0.7rem;">COMPLETED</span>
                                        </div>
                                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($p['location']); ?></p>
                                        
                                        <div class="mb-3 mt-auto">
                                            <span class="text-muted small d-block mb-1">Crops cultivated:</span>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php foreach ($p['crops'] as $c): ?>
                                                    <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.72rem;">
                                                        <?= htmlspecialchars($c['product_name']); ?> (<?= number_format($c['planned_quantity'], 0); ?> <?= htmlspecialchars($c['unit']); ?>)
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="border-top pt-2 d-flex justify-content-between align-items-center mt-2" style="font-size: 0.78rem;">
                                            <span class="text-muted">Expenses: <strong class="text-dark font-monospace">LKR <?= number_format($p['total_expenses'], 2); ?></strong></span>
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

<!-- MODAL: START NEW PLANTATION PROJECT -->
<div class="modal fade" id="newProjectModal" tabindex="-1" aria-labelledby="newProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-success text-white py-3 border-0">
                <h5 class="modal-title fw-bold" id="newProjectModalLabel"><i class="bi bi-flower2 me-2"></i> Start New Plantation Project</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('operations/plantation/store'); ?>" method="POST" id="newProjectForm">
                <?= \Core\CSRF::getFormField(); ?>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="project_name" class="form-label fw-semibold small">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="project_name" name="project_name" placeholder="e.g. 2026 Yatagama Plantation" required>
                        </div>

                        <div class="col-md-6">
                            <label for="start_date" class="form-label fw-semibold small">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= date('Y-m-d'); ?>" required>
                        </div>

                    </div>

                    <!-- Grow Crops Section -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-seedling text-success me-2"></i>What are you going to grow?</h6>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill" onclick="addCropRow()">
                                <i class="bi bi-plus-lg me-1"></i> Add Plant/Crop
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-sm align-middle border-0" id="cropsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Select Crop/Product <span class="text-danger">*</span></th>
                                        <th style="width: 150px;">Unit <span class="text-danger">*</span></th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dynamic rows inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Start Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let cropRowCount = 0;
const products = <?= json_encode($products); ?>;

function addCropRow() {
    cropRowCount++;
    const tbody = document.querySelector('#cropsTable tbody');
    const tr = document.createElement('tr');
    tr.id = `crop_row_${cropRowCount}`;
    
    let optionsHtml = '<option value="">-- Select Product --</option>';
    products.forEach(p => {
        optionsHtml += `<option value="${p.id}" data-unit="${p.unit_code || 'Units'}">${p.product_code} - ${p.name_en}</option>`;
    });

    tr.innerHTML = `
        <td>
            <select class="form-select form-select-sm" name="crops[${cropRowCount}][product_id]" required onchange="handleProductSelect(${cropRowCount}, this)">
                ${optionsHtml}
            </select>
            <input type="hidden" name="crops[${cropRowCount}][planned_quantity]" value="0.00">
            <input type="hidden" name="crops[${cropRowCount}][notes]" value="">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="crops[${cropRowCount}][unit]" id="crop_unit_${cropRowCount}" value="Units" required readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="removeCropRow(${cropRowCount})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
}

function removeCropRow(id) {
    const row = document.getElementById(`crop_row_${id}`);
    if (row) row.remove();
}

function handleProductSelect(rowId, selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const unit = selectedOption.getAttribute('data-unit') || 'Units';
    document.getElementById(`crop_unit_${rowId}`).value = unit;
}

// Add one default row on page load/modal init
document.addEventListener('DOMContentLoaded', () => {
    addCropRow();
});
</script>

<style>
.hover-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1) !important;
}
.uppercase-label {
    letter-spacing: 0.05em;
    text-transform: uppercase;
    font-size: 0.72rem;
}
</style>
