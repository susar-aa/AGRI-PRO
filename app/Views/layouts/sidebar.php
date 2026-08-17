<?php 
$activeNav = $activeNav ?? 'dashboard'; 

// Determine which accordion groups should be open based on active nav
$openSales    = in_array($activeNav, ['invoices', 'expenses', 'coa', 'journal_entries', 'general_ledger', 'trial_balance']);
$openParties  = in_array($activeNav, ['directory', 'customers', 'suppliers']);
$openInventory= in_array($activeNav, ['marketplace_products', 'products']);
$openOps      = in_array($activeNav, ['marketplace', 'ops_plantation', 'ops_machinery', 'ops_fruit_packing', 'ops_brick_manufacturing', 'ops_construction', 'ops_grinding_mill', 'services', 'fixed_deposits']);
$openFinance  = in_array($activeNav, ['cash_bank_overview', 'cash_accounts', 'bank_accounts', 'cheques', 'deposits']);
$openSystem   = in_array($activeNav, ['cost_centers', 'users', 'company_settings', 'audit_logs', 'customer_activities']);
?>
<aside class="sidebar-wrapper">
    <div class="sidebar-brand">
        <div class="logo-icon"><i class="bi bi-tree-fill"></i></div>
        <div class="brand-text">
            <h6 class="text-white mb-0 font-weight-bold" style="font-size: 0.95rem;">AGRI CO-OP ERP</h6>
            <small class="text-success" style="font-size: 0.72rem; letter-spacing: 0.5px;">සමූපකාර ERP පද්ධතිය</small>
        </div>
    </div>

    <ul class="sidebar-menu" id="sidebarAccordion">

        <!-- DASHBOARD -->
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('dashboard'); ?>" class="menu-link <?= $activeNav === 'dashboard' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
            </a>
        </li>

        <!-- DIRECTORY GROUP -->
        <li class="menu-item">
            <a href="#partiesGroup" class="menu-link menu-group-toggle <?= $openParties ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" aria-expanded="<?= $openParties ? 'true' : 'false'; ?>">
                <i class="bi bi-people"></i> <span>Directory</span>
                <i class="bi bi-chevron-down ms-auto toggle-icon"></i>
            </a>
            <div id="partiesGroup" class="collapse <?= $openParties ? 'show' : ''; ?>">
                <ul class="submenu-list">
                    <li><a href="<?= \Core\Helper::baseUrl('modules/members/directory'); ?>" class="submenu-link <?= $activeNav === 'directory' ? 'active' : ''; ?>"><i class="bi bi-person-lines-fill"></i> Members</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('modules/directors/directory'); ?>" class="submenu-link <?= $activeNav === 'directors' ? 'active' : ''; ?>"><i class="bi bi-person-badge-fill"></i> Directors</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('parties/customers'); ?>" class="submenu-link <?= $activeNav === 'customers' ? 'active' : ''; ?>"><i class="bi bi-person-badge"></i> Customers</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('parties/suppliers'); ?>" class="submenu-link <?= $activeNav === 'suppliers' ? 'active' : ''; ?>"><i class="bi bi-truck"></i> Suppliers</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('parties/staff'); ?>" class="submenu-link <?= $activeNav === 'staff' ? 'active' : ''; ?>"><i class="bi bi-person-workspace"></i> Staff Directory</a></li>
                </ul>
            </div>
        </li>

        <!-- INVENTORY GROUP -->
        <li class="menu-item">
            <a href="#inventoryGroup" class="menu-link menu-group-toggle <?= $openInventory ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" aria-expanded="<?= $openInventory ? 'true' : 'false'; ?>">
                <i class="bi bi-box-seam"></i> <span>Inventory</span>
                <i class="bi bi-chevron-down ms-auto toggle-icon"></i>
            </a>
            <div id="inventoryGroup" class="collapse <?= $openInventory ? 'show' : ''; ?>">
                <ul class="submenu-list">
                    <li><a href="<?= \Core\Helper::baseUrl('modules/marketplace/products'); ?>" class="submenu-link <?= ($activeNav === 'marketplace_products' || $activeNav === 'products') ? 'active' : ''; ?>"><i class="bi bi-archive"></i> Products</a></li>
                </ul>
            </div>
        </li>

        <!-- SALES & FINANCE GROUP -->
        <li class="menu-item">
            <a href="#salesGroup" class="menu-link menu-group-toggle <?= $openSales ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" aria-expanded="<?= $openSales ? 'true' : 'false'; ?>">
                <i class="bi bi-receipt"></i> <span>Sales & Finance</span>
                <i class="bi bi-chevron-down ms-auto toggle-icon"></i>
            </a>
            <div id="salesGroup" class="collapse <?= $openSales ? 'show' : ''; ?>">
                <ul class="submenu-list">
                    <li><a href="<?= \Core\Helper::baseUrl('modules/invoices'); ?>" class="submenu-link <?= $activeNav === 'invoices' ? 'active' : ''; ?>"><i class="bi bi-file-earmark-text"></i> Invoices</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('expenses'); ?>" class="submenu-link <?= $activeNav === 'expenses' ? 'active' : ''; ?>"><i class="bi bi-wallet2"></i> Expenses</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('accounting/coa'); ?>" class="submenu-link <?= in_array($activeNav, ['coa', 'journal_entries', 'general_ledger', 'trial_balance']) ? 'active' : ''; ?>"><i class="bi bi-diagram-3"></i> Accounting</a></li>
                </ul>
            </div>
        </li>

        <!-- BUSINESS OPERATIONS GROUP -->
        <li class="menu-item">
            <a href="#opsGroup" class="menu-link menu-group-toggle <?= $openOps ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" aria-expanded="<?= $openOps ? 'true' : 'false'; ?>">
                <i class="bi bi-buildings"></i> <span>Operations</span>
                <i class="bi bi-chevron-down ms-auto toggle-icon"></i>
            </a>
            <div id="opsGroup" class="collapse <?= $openOps ? 'show' : ''; ?>">
                <ul class="submenu-list">
                    <li><a href="<?= \Core\Helper::baseUrl('modules/services'); ?>" class="submenu-link <?= $activeNav === 'services' ? 'active' : ''; ?>"><i class="bi bi-briefcase"></i> Services</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('modules/fixed-deposits'); ?>" class="submenu-link <?= $activeNav === 'fixed_deposits' ? 'active' : ''; ?>"><i class="bi bi-safe2"></i> Fixed Deposits</a></li>
                    <li style="display: none;"><a href="<?= \Core\Helper::baseUrl('operations/plantation'); ?>" class="submenu-link <?= $activeNav === 'ops_plantation' ? 'active' : ''; ?>"><i class="bi bi-flower2"></i> Plantation</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('modules/machinery'); ?>" class="submenu-link <?= ($activeNav === 'machinery' || $activeNav === 'ops_machinery') ? 'active' : ''; ?>"><i class="bi bi-truck-flatbed"></i> Machinery Renting</a></li>
                    <li style="display: none;"><a href="<?= \Core\Helper::baseUrl('operations/fruit-packing'); ?>" class="submenu-link <?= $activeNav === 'ops_fruit_packing' ? 'active' : ''; ?>"><i class="bi bi-basket"></i> Fruit Packing</a></li>
                    <li style="display: none;"><a href="<?= \Core\Helper::baseUrl('operations/brick-manufacturing'); ?>" class="submenu-link <?= $activeNav === 'ops_brick_manufacturing' ? 'active' : ''; ?>"><i class="bi bi-bricks"></i> Brick Manufacturing</a></li>
                    <li style="display: none;"><a href="<?= \Core\Helper::baseUrl('operations/construction'); ?>" class="submenu-link <?= $activeNav === 'ops_construction' ? 'active' : ''; ?>"><i class="bi bi-building"></i> Construction</a></li>
                    <li style="display: none;"><a href="<?= \Core\Helper::baseUrl('operations/grinding-mill'); ?>" class="submenu-link <?= $activeNav === 'ops_grinding_mill' ? 'active' : ''; ?>"><i class="bi bi-gear-wide-connected"></i> Grinding Mill</a></li>
                </ul>
            </div>
        </li>

        <!-- CASH & BANK GROUP -->
        <li class="menu-item">
            <a href="#financeGroup" class="menu-link menu-group-toggle <?= $openFinance ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" aria-expanded="<?= $openFinance ? 'true' : 'false'; ?>">
                <i class="bi bi-bank2"></i> <span>Cash & Banking</span>
                <i class="bi bi-chevron-down ms-auto toggle-icon"></i>
            </a>
            <div id="financeGroup" class="collapse <?= $openFinance ? 'show' : ''; ?>">
                <ul class="submenu-list">
                    <li><a href="<?= \Core\Helper::baseUrl('modules/cash-bank-overview'); ?>" class="submenu-link <?= $activeNav === 'cash_bank_overview' ? 'active' : ''; ?>"><i class="bi bi-speedometer2"></i> Cash & Bank Overview</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('modules/cash-accounts'); ?>" class="submenu-link <?= $activeNav === 'cash_accounts' ? 'active' : ''; ?>"><i class="bi bi-cash-stack"></i> Cash Accounts</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('modules/bank-accounts'); ?>" class="submenu-link <?= $activeNav === 'bank_accounts' ? 'active' : ''; ?>"><i class="bi bi-bank"></i> Bank Accounts</a></li>
                    <?php if (\Core\Auth::hasPermission('cheques.view')): ?>
                    <li><a href="<?= \Core\Helper::baseUrl('cheques'); ?>" class="submenu-link <?= $activeNav === 'cheques' ? 'active' : ''; ?>"><i class="bi bi-wallet"></i> Cheques</a></li>
                    <?php endif; ?>
                    <?php if (\Core\Auth::hasPermission('deposits.view')): ?>
                    <li><a href="<?= \Core\Helper::baseUrl('deposits'); ?>" class="submenu-link <?= $activeNav === 'deposits' ? 'active' : ''; ?>"><i class="bi bi-box-arrow-in-down-right"></i> Bank Deposits</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </li>

        <!-- SYSTEM GROUP -->
        <li class="menu-item">
            <a href="#systemGroup" class="menu-link menu-group-toggle <?= $openSystem ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" aria-expanded="<?= $openSystem ? 'true' : 'false'; ?>">
                <i class="bi bi-gear-fill"></i> <span>System</span>
                <i class="bi bi-chevron-down ms-auto toggle-icon"></i>
            </a>
            <div id="systemGroup" class="collapse <?= $openSystem ? 'show' : ''; ?>">
                <ul class="submenu-list">
                    <li><a href="<?= \Core\Helper::baseUrl('cost-centers'); ?>" class="submenu-link <?= $activeNav === 'cost_centers' ? 'active' : ''; ?>"><i class="bi bi-pie-chart-fill"></i> Cost Centers</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('modules/users'); ?>" class="submenu-link <?= $activeNav === 'users' ? 'active' : ''; ?>"><i class="bi bi-person-gear"></i> Users & Roles</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('system/customer-activities'); ?>" class="submenu-link <?= $activeNav === 'customer_activities' ? 'active' : ''; ?>"><i class="bi bi-list-stars"></i> Customer Activities</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('admin/company'); ?>" class="submenu-link <?= $activeNav === 'company_settings' ? 'active' : ''; ?>"><i class="bi bi-sliders"></i> Company Settings</a></li>
                    <li><a href="<?= \Core\Helper::baseUrl('modules/audit-logs'); ?>" class="submenu-link <?= $activeNav === 'audit_logs' ? 'active' : ''; ?>"><i class="bi bi-shield-check"></i> Audit Logs</a></li>
                </ul>
            </div>
        </li>

    </ul>

    <!-- USER CARD at BOTTOM -->
    <?php
        $sessionUser = \Core\Session::get('full_name') ?? 'User';
        $sessionUsername = \Core\Session::get('username') ?? '';
        $initials = '';
        $nameParts = explode(' ', trim($sessionUser));
        foreach ($nameParts as $part) { if ($part) $initials .= strtoupper($part[0]); }
        $initials = substr($initials, 0, 2);
    ?>
    <div class="sidebar-user-card">
        <div class="sidebar-user-avatar"><?= htmlspecialchars($initials ?: 'U'); ?></div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name"><?= htmlspecialchars($sessionUser); ?></div>
            <div class="sidebar-user-role">@<?= htmlspecialchars($sessionUsername); ?></div>
        </div>
        <a href="<?= \Core\Helper::baseUrl('logout'); ?>" class="sidebar-logout-btn" title="Logout">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</aside>
