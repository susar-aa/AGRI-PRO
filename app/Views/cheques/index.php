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
        <h4 class="fw-bold mb-1 text-dark">Cheques Registry</h4>
        <p class="text-muted small mb-0">Monitor received customer cheques and track issued supplier cheques.</p>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= \Core\Helper::baseUrl('cheques'); ?>" method="GET" class="row g-3 small">
            <div class="col-12 col-md-5">
                <label class="form-label fw-semibold">Search Cheques</label>
                <input type="text" class="form-control form-control-sm" name="search" value="<?= htmlspecialchars($filters['search'] ?? ''); ?>" placeholder="Cheque #, Bank name...">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Filter Party</label>
                <select class="form-select form-select-sm" name="party_id">
                    <option value="">-- All --</option>
                    <?php foreach ($parties as $c): ?>
                        <option value="<?= $c['id']; ?>" <?= (($filters['party_id'] ?? '') == $c['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($c['name']); ?> (<?= htmlspecialchars($c['party_type']); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">-- All --</option>
                    <option value="RECEIVED" <?= (($filters['status'] ?? '') === 'RECEIVED') ? 'selected' : ''; ?>>Received</option>
                    <option value="ISSUED" <?= (($filters['status'] ?? '') === 'ISSUED') ? 'selected' : ''; ?>>Issued</option>
                    <option value="DEPOSITED" <?= (($filters['status'] ?? '') === 'DEPOSITED') ? 'selected' : ''; ?>>Deposited</option>
                    <option value="CLEARED" <?= (($filters['status'] ?? '') === 'CLEARED') ? 'selected' : ''; ?>>Cleared</option>
                    <option value="BOUNCED" <?= (($filters['status'] ?? '') === 'BOUNCED') ? 'selected' : ''; ?>>Bounced</option>
                    <option value="CANCELLED" <?= (($filters['status'] ?? '') === 'CANCELLED') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 w-100" style="background-color: #1b4332; border-color: #1b4332;">Filter</button>
                <a href="<?= \Core\Helper::baseUrl('cheques'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<ul class="nav nav-tabs nav-fill mb-4 border-bottom-0" id="chequeTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold border-0 rounded-pill px-4 me-2" id="received-tab" data-bs-toggle="tab" data-bs-target="#received" type="button" role="tab" style="background-color: #f1f8f5; color: #1b4332;">
            <i class="bi bi-arrow-down-left-circle me-1"></i> Received Cheques
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold border-0 rounded-pill px-4" id="issued-tab" data-bs-toggle="tab" data-bs-target="#issued" type="button" role="tab" style="background-color: #f8f9fa; color: #495057;">
            <i class="bi bi-arrow-up-right-circle me-1"></i> Issued Cheques
        </button>
    </li>
</ul>

<div class="tab-content" id="chequeTabsContent">
    <!-- RECEIVED CHEQUES TAB -->
    <div class="tab-pane fade show active" id="received" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Cheque Number</th>
                                <th>Cheque Date</th>
                                <th>Customer</th>
                                <th>Bank Name</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($receivedCheques)): ?>
                                <?php foreach ($receivedCheques as $ch): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace text-success"><?= htmlspecialchars($ch['cheque_number']); ?></td>
                                        <td><?= htmlspecialchars($ch['cheque_date']); ?></td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($ch['party_name']); ?></div>
                                            <small class="text-muted font-monospace"><?= htmlspecialchars($ch['party_code']); ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($ch['bank_name']); ?></td>
                                        <td class="text-end fw-bold text-dark"><?= \Core\Helper::formatCurrency($ch['amount']); ?></td>
                                        <td class="text-center">
                                            <?php
                                            $st = $ch['status'];
                                            $badgeClass = 'bg-secondary';
                                            if ($st === 'DEPOSITED') $badgeClass = 'bg-info';
                                            elseif ($st === 'CLEARED') $badgeClass = 'bg-success';
                                            elseif ($st === 'BOUNCED') $badgeClass = 'bg-danger';
                                            ?>
                                            <span class="badge <?= $badgeClass ?> px-3 py-1"><?= htmlspecialchars($st); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group gap-1">
                                                <?php if ($ch['status'] === 'DEPOSITED' && \Core\Auth::hasPermission('cheques.update_status')): ?>
                                                    <form action="<?= \Core\Helper::baseUrl('cheques/clear'); ?>" method="POST" class="d-inline">
                                                        <?= \Core\CSRF::getFormField(); ?>
                                                        <input type="hidden" name="id" value="<?= $ch['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2">Clear</button>
                                                    </form>
                                                <?php endif; ?>

                                                <?php if (in_array($ch['status'], ['RECEIVED', 'DEPOSITED', 'CLEARED']) && \Core\Auth::hasPermission('cheques.update_status')): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#bounceModal" data-id="<?= $ch['id']; ?>" data-num="<?= htmlspecialchars($ch['cheque_number']); ?>">
                                                        Bounce
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($ch['status'] === 'RECEIVED' && \Core\Auth::hasPermission('cheques.update_status')): ?>
                                                    <form action="<?= \Core\Helper::baseUrl('cheques/cancel'); ?>" method="POST" class="d-inline" onsubmit="return confirm('Cancel this cheque?')">
                                                        <?= \Core\CSRF::getFormField(); ?>
                                                        <input type="hidden" name="id" value="<?= $ch['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-2">Cancel</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No received cheques recorded.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ISSUED CHEQUES TAB -->
    <div class="tab-pane fade" id="issued" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Cheque Number</th>
                                <th>Cheque Date</th>
                                <th>Payee (Supplier)</th>
                                <th>Bank Name</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($issuedCheques)): ?>
                                <?php foreach ($issuedCheques as $ch): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace text-primary"><?= htmlspecialchars($ch['cheque_number']); ?></td>
                                        <td><?= htmlspecialchars($ch['cheque_date']); ?></td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($ch['party_name']); ?></div>
                                            <small class="text-muted font-monospace"><?= htmlspecialchars($ch['party_code']); ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($ch['bank_name']); ?></td>
                                        <td class="text-end fw-bold text-danger"><?= \Core\Helper::formatCurrency($ch['amount']); ?></td>
                                        <td class="text-center">
                                            <?php
                                            $st = $ch['status'];
                                            $badgeClass = 'bg-secondary';
                                            if ($st === 'ISSUED') $badgeClass = 'bg-warning text-dark';
                                            elseif ($st === 'PASSED') $badgeClass = 'bg-success';
                                            elseif ($st === 'RETURNED') $badgeClass = 'bg-danger';
                                            ?>
                                            <span class="badge <?= $badgeClass ?> px-3 py-1"><?= htmlspecialchars($st); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group gap-1">
                                                <?php if ($ch['status'] === 'ISSUED' && \Core\Auth::hasPermission('cheques.update_status')): ?>
                                                    <form action="<?= \Core\Helper::baseUrl('cheques/pass'); ?>" method="POST" class="d-inline" onsubmit="return confirm('Mark this cheque as passed and deduct from bank balance?');">
                                                        <?= \Core\CSRF::getFormField(); ?>
                                                        <input type="hidden" name="id" value="<?= $ch['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2">Pass</button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#returnModal" data-id="<?= $ch['id']; ?>" data-num="<?= htmlspecialchars($ch['cheque_number']); ?>">
                                                        Return
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No issued cheques recorded.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Bounce Cheque Reason -->
<div class="modal fade" id="bounceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white bg-danger">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i> Report Bounced Cheque</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('cheques/bounce'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" id="bounceChequeId" value="">
                <div class="modal-body p-4">
                    <p>Marking Cheque #<strong id="bounceChequeNum" class="text-danger"></strong> as bounced will reverse the related customer collection receipt, restore the customer's outstanding balance, and post accounting adjustments.</p>
                    <div class="mb-3">
                        <label for="reversal_reason" class="form-label fw-semibold">Reason for Bounce <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="reversal_reason" name="reversal_reason" value="Insufficient Funds / Refer to Drawer" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Submit Bounce Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Return Issued Cheque -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white bg-danger">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-return-left me-2"></i> Report Returned Issued Cheque</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('cheques/return'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" id="returnChequeId" value="">
                <div class="modal-body p-4">
                    <p>Marking Issued Cheque #<strong id="returnChequeNum" class="text-danger"></strong> as returned will reverse the related supplier payment and restore the supplier's outstanding balance.</p>
                    <div class="mb-3">
                        <label for="return_reason" class="form-label fw-semibold">Reason for Return <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="return_reason" name="reversal_reason" value="Returned by Bank" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Submit Return Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var bounceModal = document.getElementById('bounceModal');
        if (bounceModal) {
            bounceModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var num = button.getAttribute('data-num');
                document.getElementById('bounceChequeId').value = id;
                document.getElementById('bounceChequeNum').textContent = num;
            });
        }
        
        var returnModal = document.getElementById('returnModal');
        if (returnModal) {
            returnModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var num = button.getAttribute('data-num');
                document.getElementById('returnChequeId').value = id;
                document.getElementById('returnChequeNum').textContent = num;
            });
        }
        
        // Tab click styling
        const tabs = document.querySelectorAll('#chequeTabs .nav-link');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => {
                    t.style.backgroundColor = '#f8f9fa';
                    t.style.color = '#495057';
                });
                this.style.backgroundColor = '#f1f8f5';
                this.style.color = '#1b4332';
            });
        });
    });
</script>
