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
        <h4 class="fw-bold mb-1 text-dark">Machinery Rentals Registry</h4>
        <p class="text-muted small mb-0">Record and review rental bookings, duration quantities, billing terms, and linked customer accounts.</p>
    </div>
    <div>
        <?php if (\Core\Auth::hasPermission('machinery_rentals.view')): ?>
            <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addRentalModal" style="background-color: #1b4332; border-color: #1b4332;">
                <i class="bi bi-plus-lg me-1"></i> Add Rental
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('modules/machinery-rentals'); ?>" method="GET" class="row g-3 small">
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Search Rental</label>
                <input type="text" class="form-control form-control-sm" name="search" value="<?= htmlspecialchars($filters['search']); ?>" placeholder="Rental #, notes, remarks...">
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
                <label class="form-label fw-semibold">Machinery Asset</label>
                <select class="form-select form-select-sm" name="machinery_id">
                    <option value="">-- All Assets --</option>
                    <?php foreach ($machineryList as $mac): ?>
                        <option value="<?= $mac['id']; ?>" <?= ($filters['machinery_id'] == $mac['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($mac['machinery_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">-- All Statuses --</option>
                    <option value="DRAFT" <?= ($filters['status'] === 'DRAFT') ? 'selected' : ''; ?>>Draft</option>
                    <option value="ACTIVE" <?= ($filters['status'] === 'ACTIVE') ? 'selected' : ''; ?>>Active / Rented</option>
                    <option value="COMPLETED" <?= ($filters['status'] === 'COMPLETED') ? 'selected' : ''; ?>>Completed</option>
                    <option value="CANCELLED" <?= ($filters['status'] === 'CANCELLED') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 w-100" style="background-color: #1b4332; border-color: #1b4332;">Filter</button>
                <a href="<?= \Core\Helper::baseUrl('modules/machinery-rentals'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table Grid -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Rental Number</th>
                        <th>Customer</th>
                        <th>Machinery Asset</th>
                        <th>Rental Period</th>
                        <th class="text-end">Quantity / Hours</th>
                        <th class="text-end">Rental Rate</th>
                        <th class="text-end">Total Charge</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rentals)): ?>
                        <?php foreach ($rentals as $r): ?>
                            <tr>
                                <td class="fw-bold font-monospace">
                                    <a href="<?= \Core\Helper::baseUrl('modules/machinery-rentals/view?id=' . $r['id']); ?>" class="text-success text-decoration-none">
                                        <?= htmlspecialchars($r['rental_number']); ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($r['customer_name']); ?></div>
                                    <small class="text-muted font-monospace"><?= htmlspecialchars($r['party_code']); ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($r['machinery_name']); ?></div>
                                    <small class="text-muted font-monospace"><?= htmlspecialchars($r['machinery_code']); ?></small>
                                </td>
                                <td>
                                    <span class="text-dark fw-semibold"><?= htmlspecialchars($r['start_date']); ?></span>
                                    <?php if ($r['end_date']): ?>
                                        <small class="text-muted d-block">to <?= htmlspecialchars($r['end_date']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end font-monospace"><?= number_format($r['quantity'], 2); ?></td>
                                <td class="text-end font-monospace text-muted">LKR <?= number_format($r['rental_rate'], 2); ?></td>
                                <td class="text-end font-monospace fw-bold text-success">LKR <?= number_format($r['total_charge'], 2); ?></td>
                                <td class="text-center">
                                    <?php
                                    $st = $r['status'];
                                    $badgeClass = 'bg-secondary';
                                    if ($st === 'DRAFT') $badgeClass = 'bg-secondary';
                                    elseif ($st === 'ACTIVE') $badgeClass = 'bg-warning text-dark';
                                    elseif ($st === 'COMPLETED') $badgeClass = 'bg-success';
                                    elseif ($st === 'CANCELLED') $badgeClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badgeClass ?> px-3 py-1"><?= htmlspecialchars($st); ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group gap-1">
                                        <a href="<?= \Core\Helper::baseUrl('modules/machinery-rentals/view?id=' . $r['id']); ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">View</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No machinery rentals registered.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add New Rental -->
<div class="modal fade" id="addRentalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #1b4332;">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Add Machinery Rental</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/machinery-rentals/store'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Customer <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" name="customer_id" required>
                            <option value="">-- Select Customer --</option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['name']); ?> (<?= htmlspecialchars($c['party_code']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Machinery Asset <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" name="machinery_id" id="modal_machinery_id" required>
                            <option value="">-- Select Asset --</option>
                            <?php foreach ($machineryList as $mac): ?>
                                <option value="<?= $mac['id']; ?>" data-rate="<?= $mac['default_rental_rate']; ?>"><?= htmlspecialchars($mac['machinery_name']); ?> (<?= htmlspecialchars($mac['machinery_code']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" name="start_date" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">End Date</label>
                            <input type="date" class="form-control form-control-sm" name="end_date">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Quantity / Duration <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="quantity" value="1.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Rental Rate (LKR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="rental_rate" id="modal_rental_rate" value="0.00" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Notes / Remarks</label>
                        <textarea class="form-control form-control-sm" name="notes" rows="2" placeholder="Any specific requirements..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Save Rental</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const machinerySelect = document.getElementById('modal_machinery_id');
    const rateInput = document.getElementById('modal_rental_rate');
    
    if (machinerySelect && rateInput) {
        machinerySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.dataset.rate) {
                rateInput.value = selectedOption.dataset.rate;
            } else {
                rateInput.value = '0.00';
            }
        });
    }
});
</script>
