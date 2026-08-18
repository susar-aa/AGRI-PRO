<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('modules/members/directory'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Directory
        </a>
        <h4 class="fw-bold mb-1 text-dark">Edit Society Member</h4>
        <p class="text-muted small mb-0">Update personal details and membership settings.</p>
    </div>
</div>

<form action="<?= \Core\Helper::baseUrl('modules/members/update'); ?>" method="POST" id="editForm">
    <?= \Core\CSRF::getFormField(); ?>
    <input type="hidden" name="id" value="<?= $member['id'] ?? '' ?>">
    <input type="hidden" name="party_id" value="<?= $member['party_id'] ?? '' ?>">

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-12 col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge text-success me-2"></i> 1. Personal Information</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="full_name" required placeholder="Enter member full name" value="<?= htmlspecialchars($member['full_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">NIC / National ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nic" required placeholder="NIC number" value="<?= htmlspecialchars($member['nic'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="dob" required value="<?= htmlspecialchars($member['dob'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" name="gender" required>
                                <option value="Male" <?= ($member['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($member['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= ($member['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="phone" required placeholder="Phone number" value="<?= htmlspecialchars($member['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Occupation</label>
                            <input type="text" class="form-control" name="occupation" placeholder="Occupation" value="<?= htmlspecialchars($member['occupation'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="address" required placeholder="Home address" value="<?= htmlspecialchars($member['address'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="city" required placeholder="City name" value="<?= htmlspecialchars($member['city'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Status</label>
                            <select class="form-select" name="status">
                                <option value="ACTIVE" <?= ($member['status'] ?? '') === 'ACTIVE' ? 'selected' : '' ?>>ACTIVE</option>
                                <option value="INACTIVE" <?= ($member['status'] ?? '') === 'INACTIVE' ? 'selected' : '' ?>>INACTIVE</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Heir Information Section -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people text-success me-2"></i> 2. Heir Information</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Heir Name</label>
                            <input type="text" class="form-control" name="heir_name" placeholder="Full name of the heir" value="<?= htmlspecialchars($member['heir_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Heir NIC</label>
                            <input type="text" class="form-control" name="heir_nic" placeholder="Heir's NIC number" value="<?= htmlspecialchars($member['heir_nic'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Heir Contact Number</label>
                            <input type="text" class="form-control" name="heir_contact_number" placeholder="Phone number" value="<?= htmlspecialchars($member['heir_contact_number'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Heir Address</label>
                            <input type="text" class="form-control" name="heir_address" placeholder="Heir's address" value="<?= htmlspecialchars($member['heir_address'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Society Information Section -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock-fill text-success me-2"></i> 3. Society Settings</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Additional cooperative notes..."><?= htmlspecialchars($member['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Panel -->
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-3 text-center">
                    <button type="submit" class="btn btn-primary rounded-pill w-100" style="background-color: #1b4332; border-color: #1b4332;">
                        <i class="bi bi-save me-1"></i> Update Member
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
