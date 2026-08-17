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
        <a href="<?= \Core\Helper::baseUrl('modules/service-jobs'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Logs
        </a>
        <h4 class="fw-bold mb-1 text-dark">Service Job: <?= htmlspecialchars($job['job_number']); ?></h4>
        <p class="text-muted small mb-0">Operator: <strong><?= htmlspecialchars($job['assigned_employee'] ?: 'Not assigned'); ?></strong></p>
    </div>
    
    <div class="d-flex gap-2">
        <?php if ($job['status'] !== 'COMPLETED' && $job['status'] !== 'CANCELLED'): ?>
            <?php if (\Core\Auth::hasPermission('service_jobs.complete')): ?>
                <form action="<?= \Core\Helper::baseUrl('modules/service-jobs/complete'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $job['id']; ?>">
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                        <i class="bi bi-check2-circle me-1"></i> Mark Completed
                    </button>
                </form>
            <?php endif; ?>
            <?php if (\Core\Auth::hasPermission('service_jobs.cancel')): ?>
                <form action="<?= \Core\Helper::baseUrl('modules/service-jobs/cancel'); ?>" method="POST" class="d-inline" onsubmit="return confirm('Cancel this service job?');">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $job['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                        <i class="bi bi-x-circle me-1"></i> Cancel Job
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Core Job Details & Expenses -->
    <div class="col-12 col-lg-8">
        <!-- Details Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle text-success me-2"></i> Work Order Specifications</h6>
                <div>
                    <?php
                    $badgeClass = 'bg-secondary';
                    if ($job['status'] === 'OPEN') $badgeClass = 'bg-info text-dark';
                    elseif ($job['status'] === 'IN_PROGRESS') $badgeClass = 'bg-warning text-dark';
                    elseif ($job['status'] === 'COMPLETED') $badgeClass = 'bg-success';
                    elseif ($job['status'] === 'CANCELLED') $badgeClass = 'bg-danger';
                    ?>
                    <span class="badge <?= $badgeClass ?> px-3 py-1"><?= htmlspecialchars($job['status']); ?></span>
                </div>
            </div>
            <div class="card-body pt-0 small">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <span class="text-muted d-block mb-1">Service Type</span>
                        <strong class="text-dark"><?= htmlspecialchars($job['service_name']); ?></strong>
                        <small class="text-muted d-block font-monospace"><?= htmlspecialchars($job['service_code']); ?></small>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted d-block mb-1">Dates</span>
                        <span class="text-dark fw-semibold">Start: <?= htmlspecialchars($job['start_date']); ?></span>
                        <?php if ($job['end_date']): ?>
                            <span class="text-muted d-block small">End: <?= htmlspecialchars($job['end_date']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted d-block mb-1">Work Location</span>
                        <strong class="text-dark"><?= htmlspecialchars($job['location'] ?: '-'); ?></strong>
                    </div>
                </div>

                <?php if ($job['description']): ?>
                    <hr>
                    <div>
                        <span class="text-muted d-block mb-1">Work Description</span>
                        <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($job['description'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Expenses list linked to this Job -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wallet2 text-success me-2"></i> Allocated Job Expenses</h6>
                <a href="<?= \Core\Helper::baseUrl('expenses/create?source_module=SERVICES&service_job_id=' . $job['id'] . '&reference=' . $job['job_number']); ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    <i class="bi bi-plus-lg me-1"></i> Record Expense
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Voucher #</th>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Payee / Description</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($job['expenses'])): ?>
                                <?php foreach ($job['expenses'] as $exp): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace">
                                            <a href="<?= \Core\Helper::baseUrl('expenses/view?id=' . $exp['id']); ?>" class="text-success text-decoration-none">
                                                <?= htmlspecialchars($exp['expense_number']); ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($exp['expense_date']); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($exp['category_name']); ?></span></td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($exp['payee']); ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($exp['description']); ?></small>
                                        </td>
                                        <td class="text-end font-monospace fw-bold text-dark">LKR <?= number_format($exp['amount'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No posted expenses linked to this service job.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Financial Summary & Invoice Link -->
    <div class="col-12 col-lg-4">
        <!-- Financial Summary -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light border p-3">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart text-success me-2"></i> Financial Profitability Summary</h6>
            
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Total Revenue:</span>
                <?php if ($job['invoice_id'] && $job['invoice_status'] === 'POSTED'): ?>
                    <span class="fw-semibold text-success font-monospace">LKR <?= number_format($job['revenue'], 2); ?></span>
                <?php else: ?>
                    <span class="badge bg-secondary text-white">Not Invoiced</span>
                <?php endif; ?>
            </div>

            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Job Expenses (Cost):</span>
                <span class="fw-semibold text-danger font-monospace">LKR <?= number_format($job['total_cost'], 2); ?></span>
            </div>
            
            <hr class="my-2">
            
            <div class="d-flex justify-content-between mb-2">
                <span class="fw-bold text-dark small">Gross Margin Profit:</span>
                <?php if ($job['invoice_id'] && $job['invoice_status'] === 'POSTED'): ?>
                    <span class="fw-bold text-success font-monospace">LKR <?= number_format($job['gross_profit'], 2); ?></span>
                <?php else: ?>
                    <span class="text-muted italic small">Current: -LKR <?= number_format($job['total_cost'], 2); ?></span>
                <?php endif; ?>
            </div>

            <div class="d-flex justify-content-between mb-0">
                <span class="fw-bold text-dark small">Profit Margin:</span>
                <span class="fw-bold text-dark font-monospace"><?= number_format($job['margin'], 2); ?>%</span>
            </div>
        </div>

        <!-- Invoice Link status card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt text-success me-2"></i> Invoice Reference</h6>
            </div>
            <div class="card-body p-3 pt-0 small">
                <?php if ($job['invoice_id']): ?>
                    <div class="p-3 bg-light rounded-4 mb-2 border text-center">
                        <span class="text-muted d-block small mb-1">LINKED INVOICE</span>
                        <strong class="font-monospace text-success fs-6 d-block"><?= htmlspecialchars($job['invoice_number']); ?></strong>
                        <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 mt-1"><?= htmlspecialchars($job['invoice_status']); ?></span>
                        <hr class="my-2">
                        <a href="<?= \Core\Helper::baseUrl('modules/invoices/view?id=' . $job['invoice_id']); ?>" class="btn btn-sm btn-success rounded-pill px-3 w-100" style="background-color: #1b4332; border-color: #1b4332;">
                            <i class="bi bi-eye"></i> View Invoice Details
                        </a>
                    </div>
                <?php else: ?>
                    <div class="p-3 bg-warning-subtle text-warning-emphasis rounded-4 mb-3 border text-center">
                        <i class="bi bi-exclamation-triangle-fill fs-4 d-block mb-1"></i>
                        <strong>Not Invoiced</strong>
                        <p class="mb-0 small mt-1">This job has not been billed to the customer yet.</p>
                    </div>

                    <?php if ($job['status'] !== 'CANCELLED' && \Core\Auth::hasPermission('invoices.create')): ?>
                        <a href="<?= \Core\Helper::baseUrl('modules/invoices/create?customer_id=' . $job['customer_id'] . '&service_id=' . $job['service_id'] . '&service_job_id=' . $job['id'] . '&reference=' . $job['job_number']); ?>" class="btn btn-success rounded-pill w-100" style="background-color: #1b4332; border-color: #1b4332;">
                            <i class="bi bi-receipt me-1"></i> Create Invoice
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Customer profile specs -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person text-success me-2"></i> Customer Profile</h6>
            </div>
            <div class="card-body p-3 pt-0 small">
                <div class="p-3 bg-light rounded-4 border">
                    <strong class="text-dark d-block"><?= htmlspecialchars($job['customer_name']); ?></strong>
                    <span class="text-secondary font-monospace"><?= htmlspecialchars($job['party_code']); ?></span>
                    <hr class="my-2">
                    <a href="<?= \Core\Helper::baseUrl('parties/view?id=' . $job['customer_id']); ?>" class="btn btn-sm btn-success rounded-pill px-3 w-100">
                        View Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
