<?php 
$isEdit = !empty($user); 
$actionUrl = $isEdit ? \Core\Helper::baseUrl('modules/users/update') : \Core\Helper::baseUrl('modules/users/store');
?>

<?php if ($flashError = \Core\Session::getFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= \Core\Helper::baseUrl('modules/users'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Users
        </a>
        <h4 class="fw-bold mb-1 text-dark"><?= $isEdit ? 'Edit User Profile' : 'Create New User'; ?></h4>
        <p class="text-muted small mb-0"><?= $isEdit ? 'Update details and reset passwords.' : 'Register a new user to access the system.'; ?></p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="<?= $actionUrl; ?>" method="POST">
                    <?= \Core\CSRF::getFormField(); ?>
                    
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $user['id']; ?>">
                    <?php endif; ?>

                    <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i> Basic Information</h6>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="full_name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?= $isEdit ? htmlspecialchars($user['full_name']) : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= $isEdit ? htmlspecialchars($user['username']) : ''; ?>" required>
                            <div class="form-text small text-muted">Used for login. Must be unique.</div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= $isEdit ? htmlspecialchars($user['email']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="<?= $isEdit ? htmlspecialchars($user['phone']) : ''; ?>">
                        </div>
                    </div>

                    <hr class="my-4 text-muted">
                    <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock-fill me-2"></i> Security & Access</h6>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">Password <?= !$isEdit ? '<span class="text-danger">*</span>' : ''; ?></label>
                            <input type="password" class="form-control" id="password" name="password" <?= !$isEdit ? 'required' : ''; ?>>
                            <?php if ($isEdit): ?>
                                <div class="form-text small text-muted">Leave blank to keep the current password unchanged.</div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">Account Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= ($isEdit && $user['status'] === 'active') ? 'selected' : ''; ?>>Active (Can Login)</option>
                                <option value="inactive" <?= ($isEdit && $user['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive (Access Denied)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= \Core\Helper::baseUrl('modules/users'); ?>" class="btn btn-light border rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                            <i class="bi bi-check2-circle me-1"></i> Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 d-none d-lg-block">
        <div class="card border-0 shadow-sm rounded-4 bg-light">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i> Account Security Tips</h6>
                <ul class="small text-muted ps-3 mb-0">
                    <li class="mb-2"><strong>Passwords:</strong> Encourage users to use strong passwords mixing letters, numbers, and symbols.</li>
                    <li class="mb-2"><strong>Deactivation vs Deletion:</strong> Users cannot be fully deleted from the system if they have created financial records. Use the "Inactive" status to disable their login without corrupting the historical audit trails.</li>
                    <li><strong>Roles:</strong> Ensure that the correct permissions are assigned. Currently, all active users will require explicit permissions to access certain modules.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
