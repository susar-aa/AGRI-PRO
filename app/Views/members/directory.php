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
        <h4 class="fw-bold mb-1 text-dark">Members</h4>
        <p class="text-muted small mb-0">Search directory for registered society members.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= \Core\Helper::baseUrl('modules/members/register'); ?>" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
            <i class="bi bi-person-plus-fill me-1"></i> Register New Member
        </a>
    </div>
</div>

<!-- Search Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= \Core\Helper::baseUrl('modules/members/directory'); ?>" class="row g-3 small">
            <div class="col-md-8">
                <input type="text" class="form-control form-control-sm" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Search name, NIC, membership code, phone...">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill" style="background-color: #1b4332; border-color: #1b4332;">Filter Results</button>
                <a href="<?= \Core\Helper::baseUrl('modules/members/directory'); ?>" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">Reset</a>
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
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Member Details</th>
                                <th>NIC / Contact</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($members)): ?>
                                <?php foreach ($members as $m): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($m['full_name']); ?></div>
                                            <small class="text-success font-monospace fw-semibold"><?= htmlspecialchars($m['member_no']); ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-medium"><?= htmlspecialchars($m['nic']); ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($m['phone']); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success"><?= htmlspecialchars($m['status']); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= \Core\Helper::baseUrl('modules/members/view?id=' . $m['id']); ?>" class="btn btn-outline-success px-3" title="Profile"><i class="bi bi-person-fill"></i> Profile</a>
                                                <a href="<?= \Core\Helper::baseUrl('modules/members/edit?id=' . $m['id']); ?>" class="btn btn-outline-primary px-3" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                <form action="<?= \Core\Helper::baseUrl('modules/members/delete'); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this member?');">
                                                    <?= \Core\CSRF::getFormField(); ?>
                                                    <input type="hidden" name="id" value="<?= $m['id']; ?>">
                                                    <button type="submit" class="btn btn-outline-danger px-3" title="Delete"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No society members found.</td>
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
