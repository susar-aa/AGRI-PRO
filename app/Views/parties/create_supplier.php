<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('parties/suppliers'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Suppliers
        </a>
        <h4 class="fw-bold mb-1 text-dark">Register Supplier</h4>
        <p class="text-muted small mb-0">Add a new supplier to the cooperative ledger system.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="<?= \Core\Helper::baseUrl('parties/store'); ?>" method="POST" id="partyForm">
            <?= \Core\CSRF::getFormField(); ?>
            <input type="hidden" name="party_type" value="SUPPLIER">

                <div class="col-md-12">
                    <label for="name" class="form-label fw-semibold">Name / Business Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="e.g. ABC Traders, Sunimal Perera" required>
                </div>
            </div>

            <!-- Contact & Registration -->
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label for="contact_person" class="form-label fw-semibold">Contact Person</label>
                    <input type="text" class="form-control" id="contact_person" name="contact_person" placeholder="Name of primary contact person">
                </div>
                <div class="col-md-3">
                    <label for="nic_reg_no" class="form-label fw-semibold">NIC / Business Registration #</label>
                    <input type="text" class="form-control" id="nic_reg_no" name="nic_reg_no" placeholder="e.g. 199012345678, PV-88741">
                </div>
                <div class="col-md-3">
                    <label for="phone" class="form-label fw-semibold">Contact Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="e.g. 0771234567">
                </div>
                <div class="col-md-3">
                    <label for="whatsapp_number" class="form-label fw-semibold">Whatsapp Number</label>
                    <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" placeholder="e.g. 0771234567">
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
                <label for="address" class="form-label fw-semibold">Postal / Billing Address</label>
                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Full postal address details..."></textarea>
            </div>



            <div class="modal-footer bg-light p-3 rounded-3 mt-4 gap-2">
                <a href="<?= \Core\Helper::baseUrl('parties/suppliers'); ?>" class="btn btn-secondary rounded-pill px-3">Cancel</a>
                <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Register Supplier</button>
            </div>
        </form>
    </div>
</div>
