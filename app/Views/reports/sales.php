<?php 
$activeNav = $activeNav ?? 'reports_sales';
$sales = $sales ?? [];
$aggregates = $aggregates ?? [
    'total_invoices' => 0, 'total_subtotal' => 0, 'total_discount' => 0, 'total_sales' => 0,
    'cash_sales' => 0, 'credit_sales' => 0, 'bank_sales' => 0, 'cheque_sales' => 0
];
$customers = $customers ?? [];
$filters = $filters ?? [];
$pagination = $pagination ?? ['current' => 1, 'total' => 1, 'count' => 0];
?>

<div class="content-wrapper p-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= \Core\Helper::baseUrl('reports'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Hub</a>
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="bi bi-receipt-cutoff text-primary me-2"></i>Sales & Revenue Summary Report
                </h4>
            </div>
            <p class="text-muted small mb-0">Invoice sales audit by customer, payment method, discounts, and receivables</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 d-print-none">
        <div class="card-body p-3">
            <form action="<?= \Core\Helper::baseUrl('reports/sales'); ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted mb-1">From Date</label>
                    <input type="date" class="form-control form-control-sm" name="from_date" value="<?= htmlspecialchars($filters['from_date'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted mb-1">To Date</label>
                    <input type="date" class="form-control form-control-sm" name="to_date" value="<?= htmlspecialchars($filters['to_date'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">Customer</label>
                    <select class="form-select form-select-sm" name="customer_id">
                        <option value="">-- All Customers --</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id']; ?>" <?= (($filters['customer_id'] ?? '') == $c['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($c['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted mb-1">Payment Type</label>
                    <select class="form-select form-select-sm" name="payment_type">
                        <option value="">-- All Payment Types --</option>
                        <option value="CASH" <?= (($filters['payment_type'] ?? '') === 'CASH') ? 'selected' : ''; ?>>Cash</option>
                        <option value="CREDIT" <?= (($filters['payment_type'] ?? '') === 'CREDIT') ? 'selected' : ''; ?>>Credit</option>
                        <option value="BANK" <?= (($filters['payment_type'] ?? '') === 'BANK') ? 'selected' : ''; ?>>Bank Transfer</option>
                        <option value="CHEQUE" <?= (($filters['payment_type'] ?? '') === 'CHEQUE') ? 'selected' : ''; ?>>Cheque</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 font-weight-bold">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="<?= \Core\Helper::baseUrl('reports/sales'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-primary rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Invoiced Sales</div>
                    <div class="h4 font-weight-bold text-primary mb-0">LKR <?= number_format($aggregates['total_sales'], 2); ?></div>
                    <small class="text-muted"><?= number_format($aggregates['total_invoices']); ?> invoices issued</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Cash Sales</div>
                    <div class="h4 font-weight-bold text-success mb-0">LKR <?= number_format($aggregates['cash_sales'], 2); ?></div>
                    <small class="text-muted">Instant collection</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-danger rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Credit Sales (Receivable)</div>
                    <div class="h4 font-weight-bold text-danger mb-0">LKR <?= number_format($aggregates['credit_sales'], 2); ?></div>
                    <small class="text-muted">Pending customer payment</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-info rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Bank & Cheque Sales</div>
                    <div class="h4 font-weight-bold text-info mb-0">LKR <?= number_format($aggregates['bank_sales'] + $aggregates['cheque_sales'], 2); ?></div>
                    <small class="text-muted">Bank transfers & cheques</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Sales Transactions Registry</h6>
                <small class="text-muted">Page <?= $pagination['current']; ?> of <?= max(1, $pagination['total']); ?> (<?= number_format($pagination['count']); ?> total invoices)</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 14%;">Invoice #</th>
                            <th style="width: 12%;">Date</th>
                            <th style="width: 28%;">Customer Name</th>
                            <th style="width: 12%;">Method</th>
                            <th style="width: 11%; text-align: right;">Subtotal (LKR)</th>
                            <th style="width: 11%; text-align: right;">Discount (LKR)</th>
                            <th style="width: 12%; text-align: right;">Total (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sales)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No sales invoices found matching filter criteria.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sales as $inv): ?>
                                <tr>
                                    <td>
                                        <a href="<?= \Core\Helper::baseUrl('modules/invoices/view?id=' . $inv['id']); ?>" class="font-monospace fw-bold text-decoration-none">
                                            <?= htmlspecialchars($inv['invoice_number']); ?>
                                        </a>
                                        <?php if ($inv['status'] === 'CANCELLED'): ?>
                                            <span class="badge bg-danger small ms-1">CANCELLED</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="font-monospace text-muted small"><?= htmlspecialchars($inv['invoice_date']); ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($inv['customer_name'] ?? 'Walk-in Customer'); ?></div>
                                        <?php if (!empty($inv['customer_phone'])): ?>
                                            <small class="text-muted"><?= htmlspecialchars($inv['customer_phone']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $methodClass = match($inv['payment_type']) {
                                            'CASH' => 'bg-success',
                                            'CREDIT' => 'bg-danger',
                                            'BANK' => 'bg-primary',
                                            'CHEQUE' => 'bg-warning text-dark',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $methodClass; ?> small"><?= htmlspecialchars($inv['payment_type']); ?></span>
                                    </td>
                                    <td class="text-end font-monospace"><?= number_format((float)$inv['subtotal'], 2); ?></td>
                                    <td class="text-end font-monospace text-muted"><?= number_format((float)$inv['discount'], 2); ?></td>
                                    <td class="text-end font-monospace font-weight-bold text-dark"><?= number_format((float)$inv['total'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total'] > 1): ?>
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center d-print-none">
                <small class="text-muted">Total: <?= number_format($pagination['count']); ?> invoices</small>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php for ($p = 1; $p <= $pagination['total']; $p++): ?>
                            <li class="page-item <?= ($p == $pagination['current']) ? 'active' : ''; ?>">
                                <a class="page-link" href="<?= \Core\Helper::baseUrl('reports/sales') . '?' . http_build_query(array_merge($_GET, ['page' => $p])); ?>">
                                    <?= $p; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>
