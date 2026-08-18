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
        <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.5px;">Members Directory</h4>
        <p class="text-muted small mb-0">Search and manage registered society members.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= \Core\Helper::baseUrl('modules/members/register'); ?>" class="btn btn-success rounded-pill px-4 hover-elevate" style="background-color: var(--agri-primary); border-color: var(--agri-primary); font-weight: 500;">
            <i class="bi bi-person-plus-fill me-1"></i> Register New Member
        </a>
    </div>
</div>

<!-- Search Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4 glass-card hover-elevate">
    <div class="card-body p-3">
        <form method="GET" action="<?= \Core\Helper::baseUrl('modules/members/directory'); ?>" class="row g-3 small align-items-center">
            <div class="col-md-8 position-relative">
                <i class="bi bi-search search-icon-wrapper fs-5"></i>
                <input type="text" class="form-control form-control-lg search-icon-input rounded-pill bg-light border-0" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Search name, NIC, membership code, phone..." style="box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-success w-100 rounded-pill hover-elevate" style="background-color: var(--agri-primary); border-color: var(--agri-primary); font-weight: 500;">Filter Results</button>
                <a href="<?= \Core\Helper::baseUrl('modules/members/directory'); ?>" class="btn btn-outline-secondary w-100 rounded-pill hover-elevate" style="font-weight: 500;">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Directory Listings Grid -->
<div class="row g-4">
    <!-- Society Members Column -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people-fill text-success me-2"></i> Registered Society Members</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-row-hover align-middle mb-0">
                        <thead class="table-light text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <tr>
                                <th class="border-0 rounded-start-4 ps-4">Member Details</th>
                                <th class="border-0">NIC / Contact</th>
                                <th class="border-0 text-center">Status</th>
                                <th class="border-0 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php if (!empty($members)): ?>
                                <?php foreach ($members as $m): ?>
                                    <?php 
                                        $initials = substr(preg_replace('/[^a-zA-Z]/', '', $m['full_name']), 0, 2); 
                                        $initials = strtoupper($initials ?: 'M');
                                    ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3 py-1">
                                                <div class="avatar-circle avatar-circle-sm">
                                                    <?= $initials; ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($m['full_name']); ?></div>
                                                    <small class="text-success font-monospace fw-semibold"><i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($m['member_no']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-medium text-dark"><i class="bi bi-card-text text-muted me-2"></i><?= htmlspecialchars($m['nic']); ?></div>
                                            <small class="text-muted"><i class="bi bi-telephone text-muted me-2"></i><?= htmlspecialchars($m['phone']); ?></small>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($m['status'] === 'active'): ?>
                                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Active</span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group btn-group-sm rounded-pill shadow-sm">
                                                <a href="<?= \Core\Helper::baseUrl('modules/members/view?id=' . $m['id']); ?>" class="btn btn-light border text-success" title="Profile"><i class="bi bi-person-fill"></i> View</a>
                                                <a href="<?= \Core\Helper::baseUrl('modules/members/edit?id=' . $m['id']); ?>" class="btn btn-light border text-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                <form action="<?= \Core\Helper::baseUrl('modules/members/delete'); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this member?');">
                                                    <?= \Core\CSRF::getFormField(); ?>
                                                    <input type="hidden" name="id" value="<?= $m['id']; ?>">
                                                    <button type="submit" class="btn btn-light border text-danger" title="Delete" style="border-top-left-radius: 0; border-bottom-left-radius: 0;"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="bi bi-people text-secondary" style="font-size: 3rem; opacity: 0.5;"></i>
                                        <p class="mt-3 mb-0 fs-6">No society members found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
