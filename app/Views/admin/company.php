<?php
$successMsg = \Core\Session::getFlash('success');
$errorMsg = \Core\Session::getFlash('danger');
$contactsText = implode("\n", $company['contact_numbers'] ?? []);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-sliders text-success me-2"></i>Company & Society Profile Settings
        </h4>
        <p class="text-muted small mb-0">Official cooperative registration metadata, addresses, and contacts.</p>
    </div>
</div>

<?php if ($successMsg): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($successMsg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($errorMsg): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($errorMsg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form method="POST" action="<?= \Core\Helper::baseUrl('admin/company/update'); ?>">
    <div class="row g-4">
        <!-- Main Profile Info -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-building-fill text-success me-2"></i> Cooperative Profile Details
                    </h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active Profile</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Company Name (Sinhala) <span class="text-danger">*</span></label>
                            <input type="text" name="company_name_si" class="form-control font-weight-bold" value="<?= htmlspecialchars($company['company_name_si'] ?? ''); ?>" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Company Name (English) <span class="text-danger">*</span></label>
                            <input type="text" name="company_name_en" class="form-control font-weight-bold" value="<?= htmlspecialchars($company['company_name_en'] ?? ''); ?>" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Registration No (Sinhala)</label>
                            <input type="text" name="reg_no_si" class="form-control" value="<?= htmlspecialchars($company['reg_no_si'] ?? ''); ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Registration No (English)</label>
                            <input type="text" name="reg_no_en" class="form-control" value="<?= htmlspecialchars($company['reg_no_en'] ?? ''); ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Registration Date</label>
                            <input type="text" name="reg_date" class="form-control" value="<?= htmlspecialchars($company['reg_date'] ?? ''); ?>" placeholder="e.g. 2025.11.14" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">System Currency</label>
                            <input type="text" class="form-control bg-light" value="LKR (Sri Lankan Rupee)" readonly />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Address (Sinhala)</label>
                            <textarea name="address_si" class="form-control" rows="2"><?= htmlspecialchars($company['address_si'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Address (English)</label>
                            <textarea name="address_en" class="form-control" rows="2"><?= htmlspecialchars($company['address_en'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Official Contact Numbers -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-telephone-fill text-success me-2"></i> Official Contact Numbers
                    </h6>
                </div>
                <div class="card-body p-4">
                    <label class="form-label text-muted small fw-bold text-uppercase mb-2">Phone Numbers (One per line)</label>
                    <textarea name="contact_numbers" class="form-control font-monospace" rows="7" placeholder="e.g.&#10;075 377 0 145&#10;070 629 61 50"><?= htmlspecialchars($contactsText); ?></textarea>
                    <small class="text-muted d-block mt-2">These contact numbers will be displayed on reports, invoices, and letterheads.</small>
                </div>
            </div>
        </div>

        <!-- Bottom Submit Bar -->
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-success btn-lg font-weight-bold px-4 shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i> Save Profile Changes
            </button>
        </div>
    </div>
</form>
