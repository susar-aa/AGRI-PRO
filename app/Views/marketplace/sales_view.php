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
        <a href="<?= \Core\Helper::baseUrl('modules/marketplace/sales'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Logs
        </a>
        <h4 class="fw-bold mb-1 text-dark">Invoice: <?= htmlspecialchars($sale['sale_number']); ?></h4>
        <p class="text-muted small mb-0">Record Date: <strong><?= htmlspecialchars($sale['sale_date']); ?></strong></p>
    </div>
    
    <div class="d-flex gap-2">
        <?php if ($sale['status'] === 'DRAFT' && \Core\Auth::hasPermission('marketplace.sales.post')): ?>
            <form action="<?= \Core\Helper::baseUrl('modules/marketplace/sales/post'); ?>" method="POST" class="d-inline">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $sale['id']; ?>">
                
                <?php if ($sale['payment_method'] === 'CHEQUE'): ?>
                    <!-- Cheque details inputs inside simple trigger modal or form -->
                    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#postChequeModal" style="background-color: #1b4332; border-color: #1b4332;">
                        <i class="bi bi-send me-1"></i> Post Invoice (Cheque)
                    </button>
                <?php else: ?>
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                        <i class="bi bi-send me-1"></i> Post Invoice
                    </button>
                <?php endif; ?>
            </form>
        <?php endif; ?>
        <?php if ($sale['status'] === 'POSTED' && \Core\Auth::hasPermission('marketplace.sales.cancel')): ?>
            <button class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#cancelSaleModal">
                <i class="bi bi-x-circle me-1"></i> Cancel Invoice
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Items & details -->
    <div class="col-12 col-lg-8">
        <!-- Details Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt text-success me-2"></i> Sales Summary</h6>
                <div>
                    <?php
                    $badgeClass = 'bg-secondary';
                    if ($sale['status'] === 'POSTED') $badgeClass = 'bg-success';
                    elseif ($sale['status'] === 'CANCELLED') $badgeClass = 'bg-danger';
                    ?>
                    <span class="badge <?= $badgeClass ?> px-3 py-1"><?= htmlspecialchars($sale['status']); ?></span>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3 small">
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Invoice Number</small>
                        <span class="fw-bold text-dark font-monospace"><?= htmlspecialchars($sale['sale_number']); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Payment Method</small>
                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($sale['payment_method']); ?></span>
                        <?php if ($sale['sale_type'] === 'CREDIT'): ?>
                            <span class="badge bg-warning-subtle text-warning-emphasis">Credit Sale</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Warehouse Dispatch</small>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($sale['warehouse_name']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sale Items Table -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-task text-success me-2"></i> Invoiced Products</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Product Details</th>
                                <th>SKU</th>
                                <th class="text-center">Quantity</th>
                                <th>Unit</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Discount</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sale['items'] as $item): ?>
                                <tr>
                                    <td><strong class="text-dark"><?= htmlspecialchars($item['product_name']); ?></strong></td>
                                    <td class="font-monospace text-secondary"><?= htmlspecialchars($item['sku']); ?></td>
                                    <td class="text-center fw-bold"><?= number_format($item['quantity'], 2); ?></td>
                                    <td><?= htmlspecialchars($item['unit_code'] ?: '-'); ?></td>
                                    <td class="text-end font-monospace">LKR <?= number_format($item['unit_price'], 2); ?></td>
                                    <td class="text-end font-monospace text-danger">-<?= number_format($item['discount'], 2); ?></td>
                                    <td class="text-end font-monospace fw-bold text-dark">LKR <?= number_format($item['total'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Notes / Remarks -->
        <?php if (!empty($sale['notes'])): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 small">
                <span class="text-muted d-block mb-1">Invoice Notes / Remarks</span>
                <p class="mb-0 fw-semibold text-dark"><?= nl2br(htmlspecialchars($sale['notes'])); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($sale['status'] === 'CANCELLED'): ?>
            <div class="alert alert-danger border-danger-subtle rounded-4 p-4 mb-4">
                <h6 class="fw-bold alert-heading mb-2"><i class="bi bi-exclamation-octagon-fill me-2"></i> Invoice Cancellation Information</h6>
                <div class="row g-3 small">
                    <div class="col-6">
                        <span class="text-muted">Reversal Journal Reference:</span>
                        <strong class="font-monospace text-dark d-block"><?= htmlspecialchars($sale['reversal_journal_number'] ?: '-'); ?></strong>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <span class="text-muted">Reason for Cancellation:</span>
                        <p class="mb-0 fw-semibold text-dark"><?= nl2br(htmlspecialchars($sale['reversal_reason'])); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Sidebar Summaries -->
    <div class="col-12 col-lg-4">
        <!-- Totals Summary -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <span class="fw-semibold text-dark font-monospace">LKR <?= number_format($sale['subtotal'], 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Invoice Discount:</span>
                    <span class="fw-semibold text-danger font-monospace">-LKR <?= number_format($sale['discount'], 2); ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-0">
                    <span class="fw-bold text-dark fs-6">Invoice Total:</span>
                    <span class="fw-bold text-success fs-5 font-monospace">LKR <?= number_format($sale['total'], 2); ?></span>
                </div>
            </div>
        </div>

        <!-- Profitability / Accounting Summary (POSTED only) -->
        <?php if ($sale['status'] === 'POSTED'): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-light border">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart text-success me-2"></i> Profitability Summary</h6>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Sales Revenue:</span>
                    <span class="fw-semibold text-success font-monospace">LKR <?= number_format($sale['total'], 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Cost of Goods Sold (COGS):</span>
                    <span class="fw-semibold text-danger font-monospace">LKR <?= number_format($totalCogs, 2); ?></span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between mb-0">
                    <span class="fw-bold text-dark small">Gross Margin Profit:</span>
                    <span class="fw-bold text-success font-monospace">LKR <?= number_format($grossProfit, 2); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Customer & Audit card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person text-success me-2"></i> Customer Details</h6>
            </div>
            <div class="card-body p-3 pt-0 small">
                <div class="p-3 bg-light rounded-4 mb-3 border">
                    <strong class="text-dark d-block"><?= htmlspecialchars($sale['customer_name']); ?></strong>
                    <span class="text-muted font-monospace"><?= htmlspecialchars($sale['party_code']); ?></span>
                    <hr class="my-2">
                    <a href="<?= \Core\Helper::baseUrl('parties/view?id=' . $sale['customer_id']); ?>" class="btn btn-sm btn-success rounded-pill px-3 w-100">
                        <i class="bi bi-person-badge"></i> View Customer Profile
                    </a>
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Journal Number:</span>
                        <span class="fw-bold text-dark font-monospace"><?= htmlspecialchars($sale['journal_number'] ?: '-'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Registered By:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($sale['creator_name'] ?? 'System'); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Post Invoice (Cheque) -->
<div class="modal fade" id="postChequeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white bg-success" style="background-color: #1b4332;">
                <h5 class="modal-title fw-bold"><i class="bi bi-wallet2 me-2"></i> Cheque Specifications</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/marketplace/sales/post'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $sale['id']; ?>">
                <div class="modal-body p-4">
                    <p class="small text-muted">Please input received cheque details below to post this invoice. This will automatically register the customer cheque inside the registry.</p>
                    <div class="mb-3">
                        <label for="cheque_number" class="form-label fw-semibold">Cheque Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cheque_number" name="cheque_number" required placeholder="e.g. 102040">
                    </div>
                    <div class="mb-3">
                        <label for="cheque_bank" class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cheque_bank" name="cheque_bank" required placeholder="e.g. Sampath Bank">
                    </div>
                    <div class="mb-3">
                        <label for="cheque_date" class="form-label fw-semibold">Cheque Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="cheque_date" name="cheque_date" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">Post Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Cancel Sale -->
<div class="modal fade" id="cancelSaleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white bg-danger">
                <h5 class="modal-title fw-bold"><i class="bi bi-x-circle me-2"></i> Cancel Sale Invoice</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/marketplace/sales/cancel'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $sale['id']; ?>">
                <div class="modal-body p-4">
                    <p>Cancelling this sale invoice will reverse the ledger accounting entries, replenish stock back into warehouse balances, and cancel any related cheques or customer outstanding balances.</p>
                    <div class="mb-3">
                        <label for="reversal_reason" class="form-label fw-semibold">Reason for Cancellation <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reversal_reason" name="reversal_reason" rows="3" placeholder="Explain why this invoice is being cancelled..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Execute Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>
