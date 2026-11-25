<?php if ($flashSuccess = \Core\Session::getFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($flashWarning = \Core\Session::getFlash('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($flashWarning, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($flashError = \Core\Session::getFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
        <i class="bi bi-exclamation-octagon-fill me-2"></i> <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($importResult)): ?>
    <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-success">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-file-earmark-spreadsheet-fill text-success me-2"></i> Excel Import Results Summary
                </h6>
                <span class="badge bg-light text-muted border">Total rows processed: <?= (int)($importResult['total_rows'] ?? 0); ?></span>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-sm-4">
                    <div class="p-3 bg-success-subtle text-success rounded-3 text-center">
                        <div class="fs-4 fw-bold"><?= (int)($importResult['imported'] ?? 0); ?></div>
                        <small class="fw-semibold">New Members Added</small>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-primary-subtle text-primary rounded-3 text-center">
                        <div class="fs-4 fw-bold"><?= (int)($importResult['updated'] ?? 0); ?></div>
                        <small class="fw-semibold">Existing Members Updated</small>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-warning-subtle text-warning rounded-3 text-center">
                        <div class="fs-4 fw-bold"><?= (int)($importResult['skipped'] ?? 0); ?></div>
                        <small class="fw-semibold">Rows Skipped / Duplicates</small>
                    </div>
                </div>
            </div>

            <?php if (!empty($importResult['skipped_details']) || !empty($importResult['errors'])): ?>
                <div class="accordion accordion-flush" id="importDetailsAccordion">
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2 px-3 bg-light rounded-3 text-muted small" type="button" data-bs-toggle="collapse" data-bs-target="#collapseImportDetails">
                                <i class="bi bi-info-circle me-2"></i> View skipped rows & validation notices (<?= count($importResult['skipped_details'] ?? []) + count($importResult['errors'] ?? []); ?>)
                            </button>
                        </h2>
                        <div id="collapseImportDetails" class="accordion-collapse collapse">
                            <div class="accordion-body px-0 pt-3">
                                <?php if (!empty($importResult['errors'])): ?>
                                    <div class="text-danger small mb-2 fw-semibold">Errors:</div>
                                    <ul class="list-group list-group-flush small mb-3">
                                        <?php foreach ($importResult['errors'] as $err): ?>
                                            <li class="list-group-item px-2 py-1 text-danger"><i class="bi bi-x-circle me-1"></i> <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <?php if (!empty($importResult['skipped_details'])): ?>
                                    <div class="text-muted small mb-2 fw-semibold">Skipped Records:</div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered small">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Spreadsheet Row</th>
                                                    <th>Member Name</th>
                                                    <th>Reason</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($importResult['skipped_details'] as $skip): ?>
                                                    <tr>
                                                        <td class="text-center font-monospace"><?= htmlspecialchars((string)$skip['line']); ?></td>
                                                        <td class="fw-medium"><?= htmlspecialchars($skip['name']); ?></td>
                                                        <td class="text-muted"><?= htmlspecialchars($skip['reason']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Header Section with Action Buttons -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Society Members</h4>
        <p class="text-muted small mb-0">Central registry for cooperative society members and agricultural profiles.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <!-- Download Template -->
        <a href="<?= \Core\Helper::baseUrl('modules/members/template'); ?>" class="btn btn-outline-secondary rounded-pill px-3 btn-sm" title="Download Excel template for importing members">
            <i class="bi bi-file-earmark-arrow-down me-1"></i> Template (.xls)
        </a>

        <!-- Export Excel -->
        <a href="<?= \Core\Helper::baseUrl('modules/members/export' . (!empty($search) || !empty($status) ? '?' . http_build_query(array_filter(['search' => $search, 'status' => $status])) : '')); ?>" class="btn btn-outline-success rounded-pill px-3 btn-sm" title="Export current members list to Excel">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
        </a>

        <!-- Import Modal Trigger -->
        <button type="button" class="btn btn-outline-primary rounded-pill px-3 btn-sm" data-bs-toggle="modal" data-bs-target="#importExcelModal">
            <i class="bi bi-file-earmark-arrow-up-fill me-1"></i> Import Excel
        </button>

        <!-- Register New Member -->
        <a href="<?= \Core\Helper::baseUrl('modules/members/register'); ?>" class="btn btn-success rounded-pill px-4 btn-sm" style="background-color: #1b4332; border-color: #1b4332;">
            <i class="bi bi-person-plus-fill me-1"></i> Register Member
        </a>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= \Core\Helper::baseUrl('modules/members/directory'); ?>" class="row g-2 align-items-center small">
            <div class="col-md-6 col-lg-7">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control form-control-sm border-start-0" name="search" value="<?= htmlspecialchars($search ?? ''); ?>" placeholder="Search by name, NIC, membership code (e.g. AGC/25/...), phone...">
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <select class="form-select form-select-sm" name="status">
                    <option value="">All Statuses</option>
                    <option value="ACTIVE" <?= (strtoupper($status ?? '') === 'ACTIVE') ? 'selected' : ''; ?>>ACTIVE</option>
                    <option value="INACTIVE" <?= (strtoupper($status ?? '') === 'INACTIVE') ? 'selected' : ''; ?>>INACTIVE</option>
                    <option value="SUSPENDED" <?= (strtoupper($status ?? '') === 'SUSPENDED') ? 'selected' : ''; ?>>SUSPENDED</option>
                    <option value="RESIGNED" <?= (strtoupper($status ?? '') === 'RESIGNED') ? 'selected' : ''; ?>>RESIGNED</option>
                </select>
            </div>
            <div class="col-md-3 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill" style="background-color: #1b4332; border-color: #1b4332;">Filter</button>
                <a href="<?= \Core\Helper::baseUrl('modules/members/directory'); ?>" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Directory Listings Grid -->
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-people-fill text-success me-2"></i> Registered Members
                    <span class="badge bg-light text-muted border ms-2"><?= (int)($total ?? count($members)); ?> Total</span>
                </h6>
                <div class="small text-muted">
                    Page <?= (int)($page ?? 1); ?> of <?= (int)($totalPages ?? 1); ?>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Member Details</th>
                                <th style="width: 20%;">NIC / Contact</th>
                                <th style="width: 25%;">Agricultural Sectors / City</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 20%;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($members)): ?>
                                <?php foreach ($members as $m): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($m['full_name'] ?? ''); ?></div>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <span class="badge bg-light text-success font-monospace fw-semibold border"><?= htmlspecialchars($m['member_no'] ?? ''); ?></span>
                                                <?php if (!empty($m['occupation'])): ?>
                                                    <span class="text-muted small"><i class="bi bi-briefcase me-1"></i><?= htmlspecialchars($m['occupation']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-medium font-monospace"><?= htmlspecialchars($m['nic'] ?? '—'); ?></div>
                                            <small class="text-muted d-block"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($m['phone'] ?? '—'); ?></small>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 260px;" title="<?= htmlspecialchars($m['agricultural_sector'] ?? ''); ?>">
                                                <?= htmlspecialchars($m['agricultural_sector'] ?? '—'); ?>
                                            </div>
                                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($m['city'] ?? '—'); ?></small>
                                        </td>
                                        <td>
                                            <?php 
                                            $st = strtoupper($m['status'] ?? 'ACTIVE');
                                            $badgeClass = ($st === 'ACTIVE') ? 'bg-success-subtle text-success' : (($st === 'INACTIVE') ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning');
                                            ?>
                                            <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($st); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= \Core\Helper::baseUrl('modules/members/view?id=' . $m['id']); ?>" class="btn btn-outline-success px-3" title="View Profile">
                                                    <i class="bi bi-person-fill"></i> Profile
                                                </a>
                                                <a href="<?= \Core\Helper::baseUrl('modules/members/edit?id=' . $m['id']); ?>" class="btn btn-outline-primary px-3" title="Edit Member">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="<?= \Core\Helper::baseUrl('modules/members/delete'); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to deactivate this member?');">
                                                    <?= \Core\CSRF::getFormField(); ?>
                                                    <input type="hidden" name="id" value="<?= $m['id']; ?>">
                                                    <button type="submit" class="btn btn-outline-danger px-3" title="Deactivate"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-people display-6 text-muted d-block mb-2"></i>
                                        No society members found matching the criteria.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Server-Side Pagination Footer -->
            <?php if (!empty($totalPages) && $totalPages > 1): ?>
                <div class="card-footer bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">
                        Showing <?= min((($page - 1) * $perPage) + 1, $total); ?> to <?= min($page * $perPage, $total); ?> of <?= $total; ?> members
                    </div>
                    <nav aria-label="Members pagination">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Previous Page -->
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?= \Core\Helper::baseUrl('modules/members/directory?' . http_build_query(array_merge($_GET, ['page' => $page - 1]))); ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>

                            <?php 
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            if ($startPage > 1): ?>
                                <li class="page-item"><a class="page-link" href="<?= \Core\Helper::baseUrl('modules/members/directory?' . http_build_query(array_merge($_GET, ['page' => 1]))); ?>">1</a></li>
                                <?php if ($startPage > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                            <?php endif; ?>

                            <?php for ($p = $startPage; $p <= $endPage; $p++): ?>
                                <li class="page-item <?= ($p === $page) ? 'active' : ''; ?>">
                                    <a class="page-link <?= ($p === $page) ? 'bg-success border-success text-white' : ''; ?>" href="<?= \Core\Helper::baseUrl('modules/members/directory?' . http_build_query(array_merge($_GET, ['page' => $p]))); ?>"><?= $p; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                                <li class="page-item"><a class="page-link" href="<?= \Core\Helper::baseUrl('modules/members/directory?' . http_build_query(array_merge($_GET, ['page' => $totalPages]))); ?>"><?= $totalPages; ?></a></li>
                            <?php endif; ?>

                            <!-- Next Page -->
                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?= \Core\Helper::baseUrl('modules/members/directory?' . http_build_query(array_merge($_GET, ['page' => $page + 1]))); ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Import Excel Modal -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="importExcelModalLabel">
                        <i class="bi bi-file-earmark-excel-fill text-success me-2"></i> Import Society Members
                    </h5>
                    <p class="text-muted small mb-0">Upload an Excel (.xlsx, .xls) or CSV spreadsheet to bulk register or update members.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/members/import'); ?>" method="POST" enctype="multipart/form-data">
                <?= \Core\CSRF::getFormField(); ?>
                <div class="modal-body p-4">
                    <!-- Step 1: Download Template Callout -->
                    <div class="alert alert-light border rounded-3 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-cloud-arrow-down-fill text-success fs-3"></i>
                            <div>
                                <div class="fw-semibold text-dark small">Need the standardized template?</div>
                                <div class="text-muted" style="font-size: 0.8rem;">Download our ready-to-use template with formatted sample rows and column instructions.</div>
                            </div>
                        </div>
                        <a href="<?= \Core\Helper::baseUrl('modules/members/template'); ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
                            <i class="bi bi-download me-1"></i> Download Template
                        </a>
                    </div>

                    <!-- Step 2: File Upload Box -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-dark">
                            Select Excel / CSV File <span class="text-danger">*</span>
                        </label>
                        <div class="border rounded-4 p-4 text-center bg-light" id="dropArea" style="border: 2px dashed #1b4332 !important; cursor: pointer;">
                            <i class="bi bi-file-earmark-arrow-up text-success display-5 d-block mb-2"></i>
                            <div class="fw-semibold text-dark small mb-1">Click to browse or drag and drop your file here</div>
                            <div class="text-muted small mb-3">Supported formats: <strong>.xlsx</strong>, <strong>.xls</strong>, <strong>.csv</strong> (Max 10MB)</div>
                            <input type="file" name="excel_file" id="excel_file_input" class="form-control d-none" accept=".xlsx, .xls, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, text/csv" required>
                            <span class="badge bg-success-subtle text-success px-3 py-2 fs-6 d-none" id="selectedFileName"></span>
                        </div>
                    </div>

                    <!-- Step 3: Duplicate Handling Strategy -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Duplicate Record Handling</label>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <div class="form-check p-3 border rounded-3 bg-white h-100">
                                    <input class="form-check-input" type="radio" name="duplicate_mode" id="dup_skip" value="skip" checked>
                                    <label class="form-check-label small ms-1" for="dup_skip">
                                        <strong class="d-block text-dark">Skip Duplicates (Safe)</strong>
                                        <span class="text-muted" style="font-size: 0.8rem;">Keeps existing database records intact; skips any row with matching NIC or Member No.</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-check p-3 border rounded-3 bg-white h-100">
                                    <input class="form-check-input" type="radio" name="duplicate_mode" id="dup_update" value="update">
                                    <label class="form-check-label small ms-1" for="dup_update">
                                        <strong class="d-block text-dark">Update Existing Records</strong>
                                        <span class="text-muted" style="font-size: 0.8rem;">Updates existing member details if matching NIC or Member No is detected.</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions Checklist -->
                    <div class="small text-muted border-top pt-3">
                        <div class="fw-semibold text-dark mb-1"><i class="bi bi-shield-check text-success me-1"></i> Safety & Validation Rules:</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.8rem;">
                            <li><strong>Full Name</strong> is required for every member row.</li>
                            <li><strong>Member No</strong> can be left blank: the system will automatically assign the next sequential number (e.g. <code>AGC/25/XXX</code>).</li>
                            <li><strong>NIC</strong> values are checked for duplication to maintain data integrity.</li>
                            <li><strong>Agricultural Sectors</strong> (comma separated) are automatically added to the sector directory.</li>
                            <li>All database changes are protected inside a safe atomic database transaction.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4" id="submitImportBtn" style="background-color: #1b4332; border-color: #1b4332;">
                        <i class="bi bi-cloud-upload-fill me-1"></i> Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropArea = document.getElementById('dropArea');
    const fileInput = document.getElementById('excel_file_input');
    const selectedBadge = document.getElementById('selectedFileName');

    if (dropArea && fileInput) {
        dropArea.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                selectedBadge.textContent = this.files[0].name;
                selectedBadge.classList.remove('d-none');
            }
        });

        // Drag and drop handlers
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropArea.classList.add('bg-white');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropArea.classList.remove('bg-white');
            }, false);
        });

        dropArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                fileInput.files = files;
                selectedBadge.textContent = files[0].name;
                selectedBadge.classList.remove('d-none');
            }
        });
    }
});
</script>
