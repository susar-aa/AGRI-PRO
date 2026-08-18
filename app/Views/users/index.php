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
        <h4 class="fw-bold mb-1 text-dark">System Users</h4>
        <p class="text-muted small mb-0">Manage user accounts, passwords, and access status.</p>
    </div>
    <div>
        <a href="<?= \Core\Helper::baseUrl('modules/users/create'); ?>" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
            <i class="bi bi-person-plus-fill me-1"></i> Add New User
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">User</th>
                        <th>Contact Info</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Created</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-secondary fw-bold" style="width:40px; height:40px;">
                                            <?= strtoupper(substr($user['full_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-0"><?= htmlspecialchars($user['full_name']); ?></div>
                                            <small class="text-muted">@<?= htmlspecialchars($user['username']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <?php if ($user['email']): ?>
                                            <div><i class="bi bi-envelope text-muted me-1"></i> <?= htmlspecialchars($user['email']); ?></div>
                                        <?php endif; ?>
                                        <?php if ($user['phone']): ?>
                                            <div><i class="bi bi-telephone text-muted me-1"></i> <?= htmlspecialchars($user['phone']); ?></div>
                                        <?php endif; ?>
                                        <?php if (!$user['email'] && !$user['phone']) echo '-'; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <span class="badge bg-success rounded-pill px-3">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger rounded-pill px-3">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= $user['last_login'] ? date('M j, Y h:i A', strtotime($user['last_login'])) : 'Never'; ?>
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('M j, Y', strtotime($user['created_at'])); ?>
                                    </small>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= \Core\Helper::baseUrl('modules/users/edit?id=' . $user['id']); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 me-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    
                                    <form action="<?= \Core\Helper::baseUrl('modules/users/toggle'); ?>" method="POST" class="d-inline">
                                        <?= \Core\CSRF::getFormField(); ?>
                                        <input type="hidden" name="id" value="<?= $user['id']; ?>">
                                        <?php if ($user['status'] === 'active'): ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to deactivate this user?');">
                                                <i class="bi bi-ban"></i> Deactivate
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                                <i class="bi bi-check-circle"></i> Activate
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
