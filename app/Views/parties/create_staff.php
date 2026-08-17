<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('parties/staff'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Staff Directory
        </a>
        <h4 class="fw-bold mb-1 text-dark">Register Staff / Employee</h4>
        <p class="text-muted small mb-0">Add a new staff member to the system.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="<?= \Core\Helper::baseUrl('parties/store'); ?>" method="POST" id="partyForm">
            <?= \Core\CSRF::getFormField(); ?>
            <input type="hidden" name="party_type" value="EMPLOYEE">

            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Sunimal Perera" required>
                </div>
            </div>

            <!-- Contact & Registration -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="nic_reg_no" class="form-label fw-semibold">NIC Number</label>
                    <input type="text" class="form-control" id="nic_reg_no" name="nic_reg_no" placeholder="e.g. 199012345678">
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold">Contact Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="e.g. 0771234567">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="city" class="form-label fw-semibold">City</label>
                    <input type="text" class="form-control" id="city" name="city" placeholder="e.g. Rambukkana">
                </div>
                <div class="col-md-6">
                    <label for="district" class="form-label fw-semibold">District</label>
                    <input type="text" class="form-control" id="district" name="district" placeholder="e.g. Kegalle">
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label fw-semibold">Residential Address</label>
                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Full residential address details..."></textarea>
            </div>


            <div class="modal-footer bg-light p-3 rounded-3 mt-4 gap-2">
                <a href="<?= \Core\Helper::baseUrl('parties/staff'); ?>" class="btn btn-secondary rounded-pill px-3">Cancel</a>
                <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Register Staff</button>
            </div>
        </form>
    </div>
</div>
