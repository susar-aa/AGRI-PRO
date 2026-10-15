<?php 
$activeNav = $activeNav ?? 'reports_hub';
$stats = $stats ?? [];
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-dark font-weight-bold">
                <i class="bi bi-grid-fill text-success me-2"></i>Central Reports & Analytics Hub
            </h4>
            <p class="text-muted small mb-0">Complete cooperative financial intelligence, audit trails, operations & ledger reporting</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-printer me-1"></i> Print Overview
            </button>
        </div>
    </div>

    <!-- Executive KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3 h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small font-weight-bold text-uppercase">Operating Surplus (Net)</div>
                            <div class="h4 font-weight-bold <?= ($stats['net_profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger'; ?> mb-0">
                                LKR <?= number_format($stats['net_profit'] ?? 0, 2); ?>
                            </div>
                        </div>
                        <div class="p-3 bg-success bg-opacity-10 text-success rounded-3">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-primary rounded-3 h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small font-weight-bold text-uppercase">Total Balance Sheet Assets</div>
                            <div class="h4 font-weight-bold text-primary mb-0">
                                LKR <?= number_format($stats['total_assets'] ?? 0, 2); ?>
                            </div>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3">
                            <i class="bi bi-bank fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-info rounded-3 h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small font-weight-bold text-uppercase">Cash & Bank Liquidity</div>
                            <div class="h4 font-weight-bold text-info mb-0">
                                LKR <?= number_format($stats['cash_bank_balance'] ?? 0, 2); ?>
                            </div>
                        </div>
                        <div class="p-3 bg-info bg-opacity-10 text-info rounded-3">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-warning rounded-3 h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small font-weight-bold text-uppercase">Active Member FDs</div>
                            <div class="h4 font-weight-bold text-warning mb-0">
                                LKR <?= number_format($stats['active_fd_principal'] ?? 0, 2); ?>
                            </div>
                            <small class="text-muted"><?= number_format($stats['active_fd_count'] ?? 0); ?> Active Deposits</small>
                        </div>
                        <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3">
                            <i class="bi bi-safe fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categorized Report Directories -->
    
    <!-- 1. FINANCIAL & EXECUTIVE STATEMENTS -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success p-2"><i class="bi bi-cash-coin fs-6"></i></span>
                <h6 class="mb-0 font-weight-bold text-dark">1. Financial Statements & Executive Ledgers</h6>
            </div>
            <span class="badge bg-light text-muted border">General Ledger & Core Accounting</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle">
                                <i class="bi bi-graph-up fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/profit-loss'); ?>" class="text-dark text-decoration-none">Profit & Loss (P&L)</a>
                                </h6>
                                <p class="text-muted small mb-2">Revenue, COGS, Gross Margin, Operating Expenses and Net Surplus statement.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/profit-loss'); ?>" class="btn btn-sm btn-outline-success py-1 px-2">Generate Statement &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle">
                                <i class="bi bi-bank fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/balance-sheet'); ?>" class="text-dark text-decoration-none">Balance Sheet</a>
                                </h6>
                                <p class="text-muted small mb-2">Statement of Financial Position: Current & Fixed Assets, Liabilities, and Member Equity.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/balance-sheet'); ?>" class="btn btn-sm btn-outline-primary py-1 px-2">Generate Statement &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-circle">
                                <i class="bi bi-calculator fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/trial-balance'); ?>" class="text-dark text-decoration-none">Trial Balance</a>
                                </h6>
                                <p class="text-muted small mb-2">Balanced debit/credit schedule covering all operational, system, and control accounts.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/trial-balance'); ?>" class="btn btn-sm btn-outline-warning py-1 px-2">Generate Statement &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-info bg-opacity-10 text-info rounded-circle">
                                <i class="bi bi-cash-stack fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/cash-bank-book'); ?>" class="text-dark text-decoration-none">Cash & Bank Book</a>
                                </h6>
                                <p class="text-muted small mb-2">Comprehensive receipts and disbursements register with running account balances.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/cash-bank-book'); ?>" class="btn btn-sm btn-outline-info py-1 px-2">View Cash Book &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-secondary bg-opacity-10 text-secondary rounded-circle">
                                <i class="bi bi-journal-bookmark fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('accounting/general-ledger'); ?>" class="text-dark text-decoration-none">General Ledger</a>
                                </h6>
                                <p class="text-muted small mb-2">Detailed transactional audit trails filtered by individual Chart of Accounts.</p>
                                <a href="<?= \Core\Helper::baseUrl('accounting/general-ledger'); ?>" class="btn btn-sm btn-outline-secondary py-1 px-2">View Ledger &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SALES & ACCOUNTS RECEIVABLE (AR) -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary p-2"><i class="bi bi-receipt-cutoff fs-6"></i></span>
                <h6 class="mb-0 font-weight-bold text-dark">2. Sales, Revenue & Accounts Receivable (AR)</h6>
            </div>
            <span class="badge bg-light text-muted border">Invoices & Credit Analysis</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle">
                                <i class="bi bi-receipt fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/sales'); ?>" class="text-dark text-decoration-none">Sales Summary & Invoice Breakdown</a>
                                </h6>
                                <p class="text-muted small mb-2">Detailed sales transactions filtered by customer, payment method (Cash, Bank, Cheque, Credit) and date.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/sales'); ?>" class="btn btn-sm btn-outline-primary py-1 px-2">View Sales Report &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-circle">
                                <i class="bi bi-clock-history fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/ar-aging'); ?>" class="text-dark text-decoration-none">Accounts Receivable (AR) Aging</a>
                                </h6>
                                <p class="text-muted small mb-2">Customer overdue receivables categorized into 30, 60, 90, and 90+ day aging brackets.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/ar-aging'); ?>" class="btn btn-sm btn-outline-danger py-1 px-2">View AR Aging &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. PURCHASES, EXPENSES & PAYABLES (AP) -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark p-2"><i class="bi bi-cart-check fs-6"></i></span>
                <h6 class="mb-0 font-weight-bold text-dark">3. Procurement, Expenses & Accounts Payable (AP)</h6>
            </div>
            <span class="badge bg-light text-muted border">Purchases & Cost Analysis</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-warning bg-opacity-10 text-dark rounded-circle">
                                <i class="bi bi-truck fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/purchases'); ?>" class="text-dark text-decoration-none">Purchases & GRN Summary</a>
                                </h6>
                                <p class="text-muted small mb-2">Goods Received Notes (GRN), supplier consignments, item quantities, and cost audit.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/purchases'); ?>" class="btn btn-sm btn-outline-warning text-dark py-1 px-2">View Purchases &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-circle">
                                <i class="bi bi-hourglass-split fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/ap-aging'); ?>" class="text-dark text-decoration-none">Accounts Payable (AP) Aging</a>
                                </h6>
                                <p class="text-muted small mb-2">Supplier balances and pending credit liabilities sorted by aging duration.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/ap-aging'); ?>" class="btn btn-sm btn-outline-danger py-1 px-2">View AP Aging &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-info bg-opacity-10 text-info rounded-circle">
                                <i class="bi bi-pie-chart fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/expenses-analysis'); ?>" class="text-dark text-decoration-none">Expense Breakdown</a>
                                </h6>
                                <p class="text-muted small mb-2">Category-wise and cost center expense analysis with percentage distribution charts.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/expenses-analysis'); ?>" class="btn btn-sm btn-outline-info py-1 px-2">Analyze Expenses &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. INVENTORY, WAREHOUSE & STOCK LEDGER -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary p-2"><i class="bi bi-boxes fs-6"></i></span>
                <h6 class="mb-0 font-weight-bold text-dark">4. Inventory, Warehouse Logistics & Stock Ledger</h6>
            </div>
            <span class="badge bg-light text-muted border">Physical Valuation & Movements</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle">
                                <i class="bi bi-box-seam fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/inventory-valuation'); ?>" class="text-dark text-decoration-none">Stock Valuation Report</a>
                                </h6>
                                <p class="text-muted small mb-2">Warehouse stock quantities on hand, weighted average unit costs (AVCO), and asset valuation.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/inventory-valuation'); ?>" class="btn btn-sm btn-outline-success py-1 px-2">View Stock Valuation &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-dark bg-opacity-10 text-dark rounded-circle">
                                <i class="bi bi-arrow-left-right fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/stock-movement'); ?>" class="text-dark text-decoration-none">Stock Movement Audit Trail</a>
                                </h6>
                                <p class="text-muted small mb-2">Complete stock ledger history of Stock IN, Stock OUT, and warehouse balance changes.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/stock-movement'); ?>" class="btn btn-sm btn-outline-dark py-1 px-2">Audit Stock Ledger &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. COOPERATIVE SOCIETY & OPERATIONS -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success p-2"><i class="bi bi-people-fill fs-6"></i></span>
                <h6 class="mb-0 font-weight-bold text-dark">5. Cooperative Society, Member Deposits & Operations</h6>
            </div>
            <span class="badge bg-light text-muted border">Society & Divisional Performance</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-circle">
                                <i class="bi bi-safe2 fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/fixed-deposits'); ?>" class="text-dark text-decoration-none">Fixed Deposits</a>
                                </h6>
                                <p class="text-muted small mb-2">Member FD portfolio, interest liability, maturing deposits, and closure history.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/fixed-deposits'); ?>" class="btn btn-sm btn-outline-warning text-dark py-1 px-2">View FD Report &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle">
                                <i class="bi bi-truck-flatbed fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/machinery-utilization'); ?>" class="text-dark text-decoration-none">Machinery Rental</a>
                                </h6>
                                <p class="text-muted small mb-2">Machinery rental hours, rental charges billed, and asset status.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/machinery-utilization'); ?>" class="btn btn-sm btn-outline-primary py-1 px-2">View Machinery &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-info bg-opacity-10 text-info rounded-circle">
                                <i class="bi bi-building fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/cost-centers'); ?>" class="text-dark text-decoration-none">Divisions Profitability</a>
                                </h6>
                                <p class="text-muted small mb-2">Revenue vs Expense margins for Plantation, Brick, Services, and Contracts.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/cost-centers'); ?>" class="btn btn-sm btn-outline-info py-1 px-2">View Divisions &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border rounded-3 p-3 hover-shadow transition">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle">
                                <i class="bi bi-journal-text fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">
                                    <a href="<?= \Core\Helper::baseUrl('reports/directory'); ?>" class="text-dark text-decoration-none">Unified Directory</a>
                                </h6>
                                <p class="text-muted small mb-2">Full registry of Members, Directors, Customers, and Staff with PDF & Excel.</p>
                                <a href="<?= \Core\Helper::baseUrl('reports/directory'); ?>" class="btn btn-sm btn-outline-success py-1 px-2">View Directory &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
}
@media print {
    .sidebar-wrapper, .navbar, .btn {
        display: none !important;
    }
    .content-wrapper {
        padding: 0 !important;
    }
}
</style>
