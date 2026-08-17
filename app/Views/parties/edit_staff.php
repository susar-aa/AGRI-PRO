<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('parties/view?id=' . $party['id']); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Profile
        </a>
        <h4 class="fw-bold mb-1 text-dark">Edit Staff Profile</h4>
        <p class="text-muted small mb-0">Modify contact credentials and status variables for: <strong><?= htmlspecialchars($party['name']); ?></strong></p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="<?= \Core\Helper::baseUrl('parties/update'); ?>" method="POST" id="partyForm">
            <?= \Core\CSRF::getFormField(); ?>
            <input type="hidden" name="id" value="<?= $party['id']; ?>">
            <input type="hidden" name="party_type" value="EMPLOYEE">

            <div class="row g-3 mb-3">
                <div class="col-md-9">
                    <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($party['name']); ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label fw-semibold">Staff Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="active" <?= ($party['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?= ($party['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Contact & Registration -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="nic_reg_no" class="form-label fw-semibold">NIC Number</label>
                    <input type="text" class="form-control" id="nic_reg_no" name="nic_reg_no" value="<?= htmlspecialchars($party['nic_reg_no'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold">Contact Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($party['phone'] ?? ''); ?>">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="city" class="form-label fw-semibold">City</label>
                    <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($party['city'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label for="district" class="form-label fw-semibold">District</label>
                    <input type="text" class="form-control" id="district" name="district" value="<?= htmlspecialchars($party['district'] ?? ''); ?>">
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label fw-semibold">Residential Address</label>
                <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($party['address'] ?? ''); ?></textarea>
            </div>


            <div class="modal-footer bg-light p-3 rounded-3 mt-4 gap-2">
                <a href="<?= \Core\Helper::baseUrl('parties/view?id=' . $party['id']); ?>" class="btn btn-secondary rounded-pill px-3">Cancel</a>
                <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Save Profile Updates</button>
            </div>
        </form>
    </div>
</div>
