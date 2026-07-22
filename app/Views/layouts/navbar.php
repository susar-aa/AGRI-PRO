<header class="top-navbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-light d-lg-none" id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="page-header-info">
            <h5 class="mb-0 text-dark font-weight-bold"><?= htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES, 'UTF-8'); ?></h5>
            <small class="text-muted d-none d-md-inline-block">
                <?= htmlspecialchars($companyConfig['company_name_si'] ?? '', ENT_QUOTES, 'UTF-8'); ?> | Reg: <?= htmlspecialchars($companyConfig['reg_no_en'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </small>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- Date indicator -->
        <span class="badge bg-light text-dark border px-3 py-2 d-none d-sm-inline-block">
            <i class="bi bi-calendar3 me-1"></i> <?= date('F j, Y'); ?>
        </span>

        <!-- Notifications Bell -->
        <div class="dropdown">
            <button class="btn btn-light position-relative rounded-circle p-2" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle">
                    <span class="visually-hidden">New alerts</span>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 style-dropdown">
                <li class="dropdown-header font-weight-bold">Notifications</li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item small text-muted text-center py-2" href="#">No new notifications</a></li>
            </ul>
        </div>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <button class="btn btn-light d-flex align-items-center gap-2 rounded-pill px-3 py-1 border" type="button" data-bs-toggle="dropdown">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 600;">
                    <?= strtoupper(substr(\Core\Auth::user()['username'] ?? 'A', 0, 1)); ?>
                </div>
                <span class="d-none d-md-inline-block font-weight-medium small">
                    <?= htmlspecialchars(\Core\Auth::user()['full_name'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <i class="bi bi-chevron-down small text-muted"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                <li class="dropdown-header">
                    <strong><?= htmlspecialchars(\Core\Auth::user()['full_name'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?></strong><br>
                    <small class="text-muted">@<?= htmlspecialchars(\Core\Auth::user()['username'] ?? 'user', ENT_QUOTES, 'UTF-8'); ?></small>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= \Core\Helper::baseUrl('logout'); ?>"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</header>
