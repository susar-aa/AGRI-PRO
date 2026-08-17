<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('modules/service-jobs'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Logs
        </a>
        <h4 class="fw-bold mb-1 text-dark">Register Service Job</h4>
        <p class="text-muted small mb-0">Record a new service work order/operation performed for a society customer.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="<?= \Core\Helper::baseUrl('modules/service-jobs/store'); ?>" method="POST">
            <?= \Core\CSRF::getFormField(); ?>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="customer_id" class="form-label fw-semibold">Select Customer <span class="text-danger">*</span></label>
                    <select class="form-select" id="customer_id" name="customer_id" required>
                        <option value="">-- Select Customer --</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['party_code']); ?> - <?= htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="service_id" class="form-label fw-semibold">Select Service Operation <span class="text-danger">*</span></label>
                    <select class="form-select" id="service_id" name="service_id" required>
                        <option value="">-- Select Service --</option>
                        <?php foreach ($services as $srv): ?>
                            <option value="<?= $srv['id']; ?>"><?= htmlspecialchars($srv['service_code']); ?> - <?= htmlspecialchars($srv['service_name']); ?> (Default Rate: LKR <?= number_format($srv['default_price'], 2); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label fw-semibold">End Date <small class="text-muted">(Optional)</small></label>
                    <input type="date" class="form-control" id="end_date" name="end_date">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label fw-semibold">Current Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="OPEN">Open</option>
                        <option value="IN_PROGRESS">In Progress</option>
                        <option value="COMPLETED">Completed</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="location" class="form-label fw-semibold">Job Work Location</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="e.g. Miduma Farm, Rambukkana">
                </div>
                <div class="col-md-6">
                    <label for="assigned_employee" class="form-label fw-semibold">Assigned Employee / Operator / Driver</label>
                    <input type="text" class="form-control" id="assigned_employee" name="assigned_employee" placeholder="e.g. Driver Pathmasiri">
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label fw-semibold">Work Order Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Specify acreage to plow, grinding quantity specifications, rental time windows, etc..."></textarea>
            </div>

            <div class="modal-footer bg-light p-3 rounded-3 mt-4 gap-2">
                <a href="<?= \Core\Helper::baseUrl('modules/service-jobs'); ?>" class="btn btn-secondary rounded-pill px-3">Cancel</a>
                <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Register Job</button>
            </div>
        </form>
    </div>
</div>
