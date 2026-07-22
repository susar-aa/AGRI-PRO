<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Company & Society Profile Settings</h4>
        <p class="text-muted small mb-0">Official cooperative registration metadata, addresses, and contacts.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-building-fill text-success me-2"></i> Cooperative Profile Details</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold text-uppercase">Company Name (Sinhala)</label>
                        <div class="fw-bold text-dark fs-5"><?= htmlspecialchars($company['company_name_si'] ?? ''); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold text-uppercase">Company Name (English)</label>
                        <div class="fw-bold text-dark fs-5"><?= htmlspecialchars($company['company_name_en'] ?? ''); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold text-uppercase">Registration No (Sinhala)</label>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($company['reg_no_si'] ?? ''); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold text-uppercase">Registration No (English)</label>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($company['reg_no_en'] ?? ''); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold text-uppercase">Registration Date</label>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($company['reg_date'] ?? ''); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold text-uppercase">System Currency</label>
                        <div class="fw-semibold text-dark">LKR (Sri Lankan Rupee)</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold text-uppercase">Address (Sinhala)</label>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($company['address_si'] ?? ''); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold text-uppercase">Address (English)</label>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($company['address_en'] ?? ''); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-telephone-fill text-success me-2"></i> Official Contact Numbers</h6>
            </div>
            <div class="card-body p-4">
                <ul class="list-group list-group-flush">
                    <?php if (!empty($company['contact_numbers'])): ?>
                        <?php foreach ($company['contact_numbers'] as $num): ?>
                            <li class="list-group-item px-0 py-2 border-0 d-flex align-items-center gap-2">
                                <i class="bi bi-phone text-success fs-5"></i>
                                <span class="fw-bold text-dark font-monospace" style="font-size: 1.05rem;"><?= htmlspecialchars($num); ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
