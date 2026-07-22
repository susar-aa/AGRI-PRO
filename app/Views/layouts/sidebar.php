<?php $activeNav = $activeNav ?? 'dashboard'; ?>
<aside class="sidebar-wrapper">
    <div class="sidebar-brand">
        <div class="logo-icon"><i class="bi bi-tree-fill"></i></div>
        <div class="brand-text">
            <h6 class="text-white mb-0 font-weight-bold" style="font-size: 0.95rem;">AGRI CO-OP ERP</h6>
            <small class="text-success" style="font-size: 0.72rem; letter-spacing: 0.5px;">සමූපකාර ERP පද්ධතිය</small>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('dashboard'); ?>" class="menu-link <?= $activeNav === 'dashboard' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
            </a>
        </li>

        <!-- ACCOUNTING -->
        <li class="menu-header">Accounting</li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('accounting/coa'); ?>" class="menu-link <?= $activeNav === 'coa' ? 'active' : ''; ?>">
                <i class="bi bi-diagram-3"></i> <span>Chart of Accounts</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('accounting/journal-entries'); ?>" class="menu-link <?= $activeNav === 'journal_entries' ? 'active' : ''; ?>">
                <i class="bi bi-journal-text"></i> <span>Journal Entries</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('accounting/general-ledger'); ?>" class="menu-link <?= $activeNav === 'general_ledger' ? 'active' : ''; ?>">
                <i class="bi bi-book"></i> <span>General Ledger</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('accounting/trial-balance'); ?>" class="menu-link <?= $activeNav === 'trial_balance' ? 'active' : ''; ?>">
                <i class="bi bi-calculator"></i> <span>Trial Balance</span>
            </a>
        </li>

        <!-- SALES & MARKETPLACE -->
        <li class="menu-header">Sales & Marketplace</li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/marketplace'); ?>" class="menu-link <?= $activeNav === 'marketplace' ? 'active' : ''; ?>">
                <i class="bi bi-shop"></i> <span>Marketplace</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/products'); ?>" class="menu-link <?= $activeNav === 'products' ? 'active' : ''; ?>">
                <i class="bi bi-box-seam"></i> <span>Products</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/sales'); ?>" class="menu-link <?= $activeNav === 'sales' ? 'active' : ''; ?>">
                <i class="bi bi-receipt"></i> <span>Sales Orders</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/customers'); ?>" class="menu-link <?= $activeNav === 'customers' ? 'active' : ''; ?>">
                <i class="bi bi-people"></i> <span>Customers</span>
            </a>
        </li>

        <!-- PURCHASING -->
        <li class="menu-header">Purchasing</li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/suppliers'); ?>" class="menu-link <?= $activeNav === 'suppliers' ? 'active' : ''; ?>">
                <i class="bi bi-truck"></i> <span>Suppliers</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/purchases'); ?>" class="menu-link <?= $activeNav === 'purchases' ? 'active' : ''; ?>">
                <i class="bi bi-cart-check"></i> <span>Purchases</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/grn'); ?>" class="menu-link <?= $activeNav === 'grn' ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-arrow-down"></i> <span>GRN (Goods Received)</span>
            </a>
        </li>

        <!-- INVENTORY -->
        <li class="menu-header">Inventory</li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/stock-overview'); ?>" class="menu-link <?= $activeNav === 'stock_overview' ? 'active' : ''; ?>">
                <i class="bi bi-boxes"></i> <span>Stock Overview</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/stock-ledger'); ?>" class="menu-link <?= $activeNav === 'stock_ledger' ? 'active' : ''; ?>">
                <i class="bi bi-list-columns"></i> <span>Stock Ledger</span>
            </a>
        </li>

        <!-- SERVICES -->
        <li class="menu-header">Services</li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/agri-services'); ?>" class="menu-link <?= $activeNav === 'agri_services' ? 'active' : ''; ?>">
                <i class="bi bi-tractor"></i> <span>Agricultural Services</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/machinery-rental'); ?>" class="menu-link <?= $activeNav === 'machinery_rental' ? 'active' : ''; ?>">
                <i class="bi bi-tools"></i> <span>Machinery Rental</span>
            </a>
        </li>

        <!-- PRODUCTION -->
        <li class="menu-header">Production</li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/plantation'); ?>" class="menu-link <?= $activeNav === 'plantation' ? 'active' : ''; ?>">
                <i class="bi bi-flower2"></i> <span>Plantation Projects</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/brick-manufacturing'); ?>" class="menu-link <?= $activeNav === 'brick_manufacturing' ? 'active' : ''; ?>">
                <i class="bi bi-bricks"></i> <span>Brick Manufacturing</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/fruit-packing'); ?>" class="menu-link <?= $activeNav === 'fruit_packing' ? 'active' : ''; ?>">
                <i class="bi bi-basket"></i> <span>Fruit Packing</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/grinding-mill'); ?>" class="menu-link <?= $activeNav === 'grinding_mill' ? 'active' : ''; ?>">
                <i class="bi bi-gear-wide-connected"></i> <span>Grinding Mill</span>
            </a>
        </li>

        <!-- PROJECTS -->
        <li class="menu-header">Projects</li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/construction'); ?>" class="menu-link <?= $activeNav === 'construction' ? 'active' : ''; ?>">
                <i class="bi bi-building"></i> <span>Construction Contracts</span>
            </a>
        </li>

        <!-- FINANCE -->
        <li class="menu-header">Finance</li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/expenses'); ?>" class="menu-link <?= $activeNav === 'expenses' ? 'active' : ''; ?>">
                <i class="bi bi-wallet2"></i> <span>Expenses Engine</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/cash-accounts'); ?>" class="menu-link <?= $activeNav === 'cash_accounts' ? 'active' : ''; ?>">
                <i class="bi bi-cash-stack"></i> <span>Cash Accounts</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/bank-accounts'); ?>" class="menu-link <?= $activeNav === 'bank_accounts' ? 'active' : ''; ?>">
                <i class="bi bi-bank"></i> <span>Bank Accounts</span>
            </a>
        </li>

        <!-- REPORTS -->
        <li class="menu-header">Reports</li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/financial-reports'); ?>" class="menu-link <?= $activeNav === 'financial_reports' ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-bar-graph"></i> <span>Financial Reports</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/business-reports'); ?>" class="menu-link <?= $activeNav === 'business_reports' ? 'active' : ''; ?>">
                <i class="bi bi-pie-chart"></i> <span>Business Reports</span>
            </a>
        </li>

        <!-- ADMINISTRATION -->
        <li class="menu-header">Administration</li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('cost-centers'); ?>" class="menu-link <?= $activeNav === 'cost_centers' ? 'active' : ''; ?>">
                <i class="bi bi-pie-chart-fill"></i> <span>Cost Centers</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/users'); ?>" class="menu-link <?= $activeNav === 'users' ? 'active' : ''; ?>">
                <i class="bi bi-person-gear"></i> <span>Users & Roles</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('admin/company'); ?>" class="menu-link <?= $activeNav === 'company_settings' ? 'active' : ''; ?>">
                <i class="bi bi-gear"></i> <span>Company Settings</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= \Core\Helper::baseUrl('modules/audit-logs'); ?>" class="menu-link <?= $activeNav === 'audit_logs' ? 'active' : ''; ?>">
                <i class="bi bi-shield-check"></i> <span>Audit Logs</span>
            </a>
        </li>
    </ul>
</aside>
