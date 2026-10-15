<?php 
$activeNav = $activeNav ?? 'reports_purchases';
$purchases = $purchases ?? [];
$suppliers = $suppliers ?? [];
$filters = $filters ?? [];
$selectedMonth = $selectedMonth ?? ($filters['month'] ?? date('Y-m'));
$pagination = $pagination ?? ['current' => 1, 'total' => 1, 'count' => 0];

$totQty = array_sum(array_column($purchases, 'quantity'));
$totCost = array_sum(array_column($purchases, 'total_cost'));
?>

<div class="content-wrapper p-4">
    <!-- Header Section (Screen Only) -->
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= \Core\Helper::baseUrl('reports'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Hub</a>
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="bi bi-cart-check text-warning me-2"></i>Purchases & Goods Receipt (GRN) Report
                </h4>
            </div>
            <p class="text-muted small mb-0">Supplier inward consignments, received quantities, cost prices, and physical receipts</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= \Core\Helper::baseUrl('reports/print-preview?' . http_build_query(array_merge($_GET, ['type' => 'purchases']))); ?>" 
               target="_blank" 
               class="btn btn-dark btn-sm shadow-sm d-flex align-items-center gap-1 font-weight-bold">
                <i class="bi bi-printer-fill text-warning"></i> Print Preview
            </a>
            <a href="<?= \Core\Helper::baseUrl('reports/export?' . http_build_query(array_merge($_GET, ['type' => 'purchases']))); ?>" 
               class="btn btn-outline-success btn-sm shadow-sm d-flex align-items-center gap-1 font-weight-bold">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-printer"></i> Quick Print
            </button>
        </div>
    </div>

    <!-- Official Cooperative Letterhead Header (Visible in Direct Browser Print) -->
    <div class="d-none d-print-block mb-3 border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="mb-0 fw-bold" style="color:#15803d;">සීමා සහිත ඇග්රි කෝප් සමූපකාර සමිතිය</h5>
                <h6 class="mb-0 fw-bold text-dark">AGRI CO-OP COOPERATIVE SOCIETY LIMITED</h6>
                <small class="text-muted">Reg: KE/1027 | Miduma, Yatagama, Rambukkana | Tel: 075 377 0 145</small>
            </div>
            <div class="text-end">
                <span class="badge bg-dark text-white p-2">PURCHASES & GRN STATEMENT</span>
                <div class="small fw-bold text-muted mt-1">Period: <?= htmlspecialchars($filters['from_date'] ?? ''); ?> to <?= htmlspecialchars($filters['to_date'] ?? ''); ?></div>
            </div>
        </div>
    </div>

    <!-- Filter Card (Screen Only) -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 d-print-none">
        <div class="card-body p-3">
            <form id="filterForm" action="<?= \Core\Helper::baseUrl('reports/purchases'); ?>" method="GET" class="row g-2 align-items-end">
                <!-- Direct Month Selector -->
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-success mb-1">
                        <i class="bi bi-calendar-month text-success me-1"></i>Select Month Directly
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="month" class="form-control" name="month" id="filter_month" 
                               value="<?= htmlspecialchars($selectedMonth); ?>" 
                               onchange="applyMonthFilter(this.value)">
                        <button class="btn btn-outline-secondary" type="button" onclick="setQuickMonth('current')" title="Current Month">This</button>
                        <button class="btn btn-outline-secondary" type="button" onclick="setQuickMonth('last')" title="Last Month">Last</button>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted mb-1">From Date</label>
                    <input type="date" class="form-control form-control-sm" name="from_date" id="from_date" value="<?= htmlspecialchars($filters['from_date'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted mb-1">To Date</label>
                    <input type="date" class="form-control form-control-sm" name="to_date" id="to_date" value="<?= htmlspecialchars($filters['to_date'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">Supplier</label>
                    <select class="form-select form-select-sm" name="supplier_id">
                        <option value="">-- All Suppliers --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id']; ?>" <?= (($filters['supplier_id'] ?? '') == $s['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($s['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-warning btn-sm w-100 font-weight-bold text-dark">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="<?= \Core\Helper::baseUrl('reports/purchases'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4 d-print-none">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-warning rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Quantity Received</div>
                    <div class="h4 font-weight-bold text-dark mb-0"><?= number_format($totQty, 2); ?> Units</div>
                    <small class="text-muted">Inward stock quantity</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-primary rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Inward Value</div>
                    <div class="h4 font-weight-bold text-primary mb-0">LKR <?= number_format($totCost, 2); ?></div>
                    <small class="text-muted">GRN procurement expenditure</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-success rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase">Total Receipts</div>
                    <div class="h4 font-weight-bold text-success mb-0"><?= count($purchases); ?> Consignments</div>
                    <small class="text-muted">Recorded inward transactions</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Sheet -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center d-print-none">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">Inward Consignments Schedule</h6>
                <small class="text-muted">Detailed supplier goods receipt log</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" style="width: 100%; table-layout: auto;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%;">Date</th>
                            <th style="width: 12%;">GRN / Ref</th>
                            <th style="width: 20%;">Supplier</th>
                            <th style="width: 22%;">Product Received</th>
                            <th style="width: 12%;">Warehouse</th>
                            <th style="width: 10%; text-align: right;">Unit Cost</th>
                            <th style="width: 10%; text-align: right;">Qty</th>
                            <th style="width: 14%; text-align: right;">Total Cost (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($purchases)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No Goods Received Notes (GRN) found for the selected criteria.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($purchases as $row): ?>
                                <tr>
                                    <td class="font-monospace text-muted small"><?= htmlspecialchars($row['movement_date']); ?></td>
                                    <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($row['reference_number'] ?: 'GRN-DIRECT'); ?></td>
                                    <td><?= htmlspecialchars($row['supplier_name']); ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['product_name']); ?></div>
                                        <small class="font-monospace text-muted"><?= htmlspecialchars($row['product_code']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($row['warehouse_name']); ?></span>
                                    </td>
                                    <td class="text-end font-monospace"><?= number_format((float)$row['unit_cost'], 2); ?></td>
                                    <td class="text-end font-monospace font-weight-bold text-primary"><?= number_format((float)$row['quantity'], 2); ?></td>
                                    <td class="text-end font-monospace font-weight-bold text-dark"><?= number_format((float)$row['total_cost'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light font-weight-bold">
                        <tr>
                            <td colspan="6" class="text-uppercase py-3">PAGE TOTALS</td>
                            <td class="text-end font-monospace text-primary py-3"><?= number_format($totQty, 2); ?></td>
                            <td class="text-end font-monospace text-dark py-3">LKR <?= number_format($totCost, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total'] > 1): ?>
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center d-print-none">
                <small class="text-muted">Total: <?= number_format($pagination['count']); ?> entries</small>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php for ($p = 1; $p <= $pagination['total']; $p++): ?>
                            <li class="page-item <?= ($p == $pagination['current']) ? 'active' : ''; ?>">
                                <a class="page-link" href="<?= \Core\Helper::baseUrl('reports/purchases') . '?' . http_build_query(array_merge($_GET, ['page' => $p])); ?>">
                                    <?= $p; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>

    <!-- Printable Signature Footer (Visible Only in Print) -->
    <div class="d-none d-print-block mt-4 pt-4 border-top">
        <div class="row text-center" style="font-size: 10px;">
            <div class="col-4">
                <div style="border-top: 1px dotted #333; width: 80%; margin: 30px auto 4px auto;"></div>
                <strong>Prepared By</strong><br><span class="text-muted">Accounts Officer</span>
            </div>
            <div class="col-4">
                <div style="border-top: 1px dotted #333; width: 80%; margin: 30px auto 4px auto;"></div>
                <strong>Checked & Verified</strong><br><span class="text-muted">Internal Auditor / Secretary</span>
            </div>
            <div class="col-4">
                <div style="border-top: 1px dotted #333; width: 80%; margin: 30px auto 4px auto;"></div>
                <strong>Approved By</strong><br><span class="text-muted">General Manager / President</span>
            </div>
        </div>
    </div>
</div>

<script>
function applyMonthFilter(monthVal) {
    if (!monthVal) return;
    const parts = monthVal.split('-');
    const year = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10);
    const lastDay = new Date(year, month, 0).getDate();
    
    document.getElementById('from_date').value = `${monthVal}-01`;
    document.getElementById('to_date').value = `${monthVal}-${String(lastDay).padStart(2, '0')}`;
    document.getElementById('filterForm').submit();
}

function setQuickMonth(period) {
    const now = new Date();
    let year = now.getFullYear();
    let month = now.getMonth() + 1;
    if (period === 'last') {
        month -= 1;
        if (month === 0) {
            month = 12;
            year -= 1;
        }
    }
    const mStr = `${year}-${String(month).padStart(2, '0')}`;
    const mInput = document.getElementById('filter_month');
    if (mInput) {
        mInput.value = mStr;
        applyMonthFilter(mStr);
    }
}
</script>
