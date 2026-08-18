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
        <h4 class="fw-bold mb-1 text-dark">Goods Receipt Notes (GRN)</h4>
        <p class="text-muted small mb-0">Track and manage received stock and physical goods receipts into your inventory.</p>
    </div>
    <div>
        <a href="<?= \Core\Helper::baseUrl('grn/create'); ?>" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
            <i class="bi bi-plus-lg me-1"></i> New GRN
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Warehouse</th>
                        <th>Supplier</th>
                        <th>Reference</th>
                        <th class="text-end">Qty Received</th>
                        <th class="text-end">Unit Cost</th>
                        <th class="text-end">Total Cost</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($grns)): ?>
                        <?php foreach ($grns as $g): ?>
                            <tr>
                                <td><?= htmlspecialchars($g['movement_date']); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($g['product_name']); ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($g['product_code']); ?></small>
                                </td>
                                <td><?= htmlspecialchars($g['location_name']); ?></td>
                                <td><?= htmlspecialchars($g['supplier_name'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($g['reference_number'] ?: '-'); ?></td>
                                <td class="text-end fw-bold text-success"><?= number_format($g['quantity_in'], 2); ?></td>
                                <td class="text-end font-monospace">LKR <?= number_format($g['unit_cost'], 2); ?></td>
                                <td class="text-end font-monospace fw-bold text-dark">LKR <?= number_format($g['total_cost'], 2); ?></td>
                                <td><?= htmlspecialchars($g['user_name'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No GRNs recorded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
