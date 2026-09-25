<?php 
$activeNav = $activeNav ?? 'reports_directory';
$filters = $filters ?? ['type' => 'all', 'search' => '', 'status' => 'all'];
$counts = $counts ?? ['total' => 0, 'directors' => 0, 'members' => 0, 'staff' => 0, 'customers' => 0];
$records = $records ?? [];
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-dark font-weight-bold">
                <i class="bi bi-journal-text text-success me-2"></i>Central Directory Report
            </h4>
            <p class="text-muted small mb-0">Unified contact registry & classification reporting for Agri Co-Op ERP</p>
        </div>
        <div class="d-flex gap-2">
            <!-- EXPORT EXCEL BUTTON -->
            <a href="<?= \Core\Helper::baseUrl('reports/directory/export') . '?' . http_build_query(array_merge($filters, ['format' => 'excel'])); ?>" 
               class="btn btn-outline-success btn-sm font-weight-bold d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
            </a>
            <!-- EXPORT PDF BUTTON -->
            <a href="<?= \Core\Helper::baseUrl('reports/directory/export') . '?' . http_build_query(array_merge($filters, ['format' => 'pdf'])); ?>" 
               target="_blank" 
               class="btn btn-danger btn-sm font-weight-bold d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-primary rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Results</div>
                    <div class="h3 font-weight-bold text-primary mb-0"><?= number_format($counts['total'] ?? count($records)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-warning rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Directors</div>
                    <div class="h3 font-weight-bold text-warning mb-0"><?= number_format($counts['directors'] ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Members</div>
                    <div class="h3 font-weight-bold text-success mb-0"><?= number_format($counts['members'] ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-info rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Staff</div>
                    <div class="h3 font-weight-bold text-info mb-0"><?= number_format($counts['staff'] ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm border-start border-4 border-secondary rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Customers</div>
                    <div class="h3 font-weight-bold text-secondary mb-0"><?= number_format($counts['customers'] ?? 0); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 bg-light rounded-3">
            <form method="GET" action="<?= \Core\Helper::baseUrl('reports/directory'); ?>" class="row g-2 align-items-center">
                <!-- Filter by Entity Type -->
                <div class="col-md-4">
                    <label class="form-label text-muted small font-weight-bold mb-1">Directory Category Filter</label>
                    <select name="type" class="form-select form-select-sm border-secondary-subtle">
                        <option value="all" <?= ($filters['type'] === 'all' || empty($filters['type'])) ? 'selected' : ''; ?>>All Categories (Directors, Members, Staff, Customers)</option>
                        <option value="director" <?= ($filters['type'] === 'director' || $filters['type'] === 'directors') ? 'selected' : ''; ?>>Directors Only</option>
                        <option value="member" <?= ($filters['type'] === 'member' || $filters['type'] === 'members') ? 'selected' : ''; ?>>Members Only</option>
                        <option value="staff" <?= ($filters['type'] === 'staff') ? 'selected' : ''; ?>>Staff Only</option>
                        <option value="customer" <?= ($filters['type'] === 'customer' || $filters['type'] === 'customers') ? 'selected' : ''; ?>>Customers Only</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="col-md-6">
                    <label class="form-label text-muted small font-weight-bold mb-1">Search Name, Address, Phone, NIC...</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" 
                               name="search" 
                               value="<?= htmlspecialchars($filters['search']); ?>" 
                               class="form-control" 
                               placeholder="e.g., Rambukkana, Member No, Name, Phone..." />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="col-md-2 d-flex align-items-end gap-1 mt-auto">
                    <button type="submit" class="btn btn-primary btn-sm w-100 font-weight-bold">
                        <i class="bi bi-filter"></i> Apply
                    </button>
                    <a href="<?= \Core\Helper::baseUrl('reports/directory'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>

            <?php if (!empty($filters['search']) || ($filters['type'] !== 'all' && !empty($filters['type']))): ?>
                <div class="mt-2 pt-2 border-top d-flex align-items-center gap-2">
                    <span class="small text-muted font-weight-bold">Active Filters:</span>
                    <?php if (!empty($filters['type']) && $filters['type'] !== 'all'): ?>
                        <span class="badge bg-primary text-white font-weight-normal">Category: <?= ucfirst($filters['type']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($filters['search'])): ?>
                        <span class="badge bg-success text-white font-weight-normal">Search: "<?= htmlspecialchars($filters['search']); ?>"</span>
                    <?php endif; ?>
                    <span class="small text-muted ms-auto">Showing <?= count($records); ?> record(s)</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3 text-center" style="width: 50px;">#</th>
                            <th style="width: 120px;">Category</th>
                            <th style="width: 140px;">Code / No</th>
                            <th>Full Name</th>
                            <th style="width: 150px;">Phone Number</th>
                            <th style="width: 150px;">NIC / Reg No</th>
                            <th>Address</th>
                            <th style="width: 150px;">City</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-6 d-block text-secondary mb-2"></i>
                                    <span class="font-weight-bold">No Directory Records Found</span><br>
                                    <small>Try broadening your search term or clearing filters.</small>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($records as $idx => $row): ?>
                                <tr>
                                    <td class="ps-3 text-center text-muted small"><?= $idx + 1; ?></td>
                                    <td>
                                        <?php 
                                            $cat = $row['entity_type'] ?? 'Member';
                                            $badgeClass = match($cat) {
                                                'Director' => 'bg-warning text-dark',
                                                'Member' => 'bg-success text-white',
                                                'Staff' => 'bg-info text-dark',
                                                'Customer' => 'bg-secondary text-white',
                                                default => 'bg-primary text-white'
                                            };
                                        ?>
                                        <span class="badge <?= $badgeClass; ?> font-weight-normal px-2 py-1"><?= htmlspecialchars($cat); ?></span>
                                    </td>
                                    <td class="font-monospace fw-bold text-primary"><?= htmlspecialchars($row['code'] ?? '-'); ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['name'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($row['phone'] ?? '-') ?: '-'; ?></td>
                                    <td class="font-monospace small"><?= htmlspecialchars($row['nic'] ?? '-') ?: '-'; ?></td>
                                    <td>
                                        <?php 
                                            $addr = $row['address'] ?? '';
                                            if (!empty($filters['search']) && stripos($addr, $filters['search']) !== false) {
                                                echo '<span class="bg-warning-subtle text-dark px-1 rounded fw-bold">' . htmlspecialchars($addr) . '</span>';
                                            } else {
                                                echo htmlspecialchars($addr ?: '-');
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $city = $row['city'] ?? '';
                                            if (!empty($filters['search']) && stripos($city, $filters['search']) !== false) {
                                                echo '<span class="bg-warning-subtle text-dark px-1 rounded fw-bold">' . htmlspecialchars($city) . '</span>';
                                            } else {
                                                echo htmlspecialchars($city ?: '-');
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top py-2 px-3 text-muted small d-flex justify-content-between">
            <span>Total results returned: <strong><?= count($records); ?></strong></span>
            <span>Agri Co-Op ERP Directory System</span>
        </div>
    </div>
</div>
