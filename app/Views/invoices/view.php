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

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 d-print-none">
    <div>
        <div class="d-flex gap-2 mb-2">
            <a href="<?= \Core\Helper::baseUrl('modules/invoices'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
                <i class="bi bi-x-circle me-1"></i> Close
            </a>
            <a href="<?= \Core\Helper::baseUrl('modules/invoices/create'); ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                <i class="bi bi-plus-circle me-1"></i> New Invoice
            </a>
        </div>
        <h4 class="fw-bold mb-1 text-dark">Invoice: <?= htmlspecialchars($invoice['invoice_number']); ?></h4>
        <p class="text-muted small mb-0">Record Date: <strong><?= htmlspecialchars($invoice['invoice_date']); ?></strong></p>
    </div>
    
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-4">
            <i class="bi bi-printer me-1"></i> Print Receipt
        </button>
        
        <?php if ($invoice['status'] === 'DRAFT' && \Core\Auth::hasPermission('invoices.post')): ?>
            <form action="<?= \Core\Helper::baseUrl('modules/invoices/post'); ?>" method="POST" class="d-inline">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $invoice['id']; ?>">
                
                <?php if ($invoice['payment_type'] === 'CHEQUE'): ?>
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
        <?php if ($invoice['status'] === 'POSTED' && \Core\Auth::hasPermission('invoices.cancel')): ?>
            <button class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#cancelInvoiceModal">
                <i class="bi bi-x-circle me-1"></i> Reverse Invoice
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Print invoice layout -->
<div class="row g-4">
    <!-- Main Invoice Area -->
    <div class="col-12 col-lg-9">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <!-- Sinhalese Co-op Header -->
            <div class="text-center mb-4 pb-4 border-bottom">
                <h4 class="fw-bold mb-1" style="color: #1b4332; font-family: 'Inter', sans-serif;">සීමා සහිත ඇග්‍රි කෝප් සමූපකාර සමිතිය</h4>
                <h5 class="text-muted fw-semibold mb-2">(Agri Co-Op Cooperative Society Limited)</h5>
                <p class="text-secondary small mb-1">Miduma, Yatagama, Rambukkana</p>
                <div class="text-secondary small mb-2">
                    <strong>Contact:</strong> 075 377 0 145, 070 629 61 50, 071 82 110 10, 071 846 0 172, 071 80 28 774
                </div>
                <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-1 fw-bold">Registration No: KE/1027</span>
            </div>

            <!-- Invoice Meta Row -->
            <div class="row g-3 mb-4 small">
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">INVOICE NUMBER</span>
                    <strong class="font-monospace fs-6 text-dark"><?= htmlspecialchars($invoice['invoice_number']); ?></strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">DATE</span>
                    <strong class="text-dark"><?= htmlspecialchars($invoice['invoice_date']); ?></strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">PAYMENT TYPE</span>
                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($invoice['payment_type']); ?></span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">REFERENCE</span>
                    <strong class="text-dark"><?= htmlspecialchars($invoice['reference'] ?: '-'); ?></strong>
                </div>
            </div>

            <!-- Customer & Warehouse -->
            <div class="row g-3 mb-4 p-3 bg-light rounded-4 border small">
                <div class="col-md-12">
                    <span class="text-muted d-block">INVOICED TO</span>
                    <strong class="text-dark fs-6 d-block"><?= htmlspecialchars($invoice['customer_name']); ?></strong>
                    <span class="font-monospace text-secondary"><?= htmlspecialchars($invoice['party_code']); ?></span>
                </div>
            </div>

            <!-- Items Table -->
            <h6 class="fw-bold text-dark mb-3">Itemized Bill details</h6>
            <div class="table-responsive mb-4">
                <table class="table align-middle table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Line Type</th>
                            <th>Description / Code</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoice['items'] as $item): ?>
                            <tr>
                                <td>
                                    <span class="badge <?= $item['item_type'] === 'PRODUCT' ? 'bg-info-subtle text-info-emphasis' : 'bg-warning-subtle text-warning-emphasis'; ?> px-2 py-0.5">
                                        <?= htmlspecialchars($item['item_type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($item['item_type'] === 'PRODUCT'): ?>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($item['product_name']); ?></div>
                                        <small class="text-muted font-monospace"><?= htmlspecialchars($item['sku']); ?></small>
                                    <?php else: ?>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($item['service_name']); ?></div>
                                        <small class="text-muted font-monospace"><?= htmlspecialchars($item['service_code']); ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($item['description'])): ?>
                                        <div class="text-muted small mt-1 italic">Note: <?= htmlspecialchars($item['description']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= number_format($item['quantity'], 2); ?></td>
                                <td>
                                    <?= htmlspecialchars($item['item_type'] === 'PRODUCT' ? $item['product_unit'] : $item['service_unit']); ?>
                                </td>
                                <td class="text-end font-monospace">LKR <?= number_format($item['unit_price'], 2); ?></td>
                                <td class="text-end font-monospace text-danger">-<?= number_format($item['discount'], 2); ?></td>
                                <td class="text-end font-monospace fw-bold text-dark">LKR <?= number_format($item['total'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Print summary totals inside A4 sheet for windows printing -->
            <div class="d-none d-print-block border-top pt-3 mb-4">
                <div class="row justify-content-end">
                    <div class="col-md-5 col-6 text-end">
                        <div class="row mb-1">
                            <div class="col-6 text-muted">Subtotal:</div>
                            <div class="col-6 fw-semibold text-dark font-monospace">LKR <?= number_format($invoice['subtotal'], 2); ?></div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-6 text-muted">Discount:</div>
                            <div class="col-6 fw-semibold text-danger font-monospace">-LKR <?= number_format($invoice['discount'], 2); ?></div>
                        </div>
                        <hr class="my-1">
                        <div class="row">
                            <div class="col-6 fw-bold text-dark">Grand Total:</div>
                            <div class="col-6 fw-bold text-success font-monospace fs-6">LKR <?= number_format($invoice['total'], 2); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <?php if (!empty($invoice['notes'])): ?>
                <div class="p-3 bg-light rounded-4 mb-4 small">
                    <span class="text-muted d-block mb-1">Invoice Notes / Special Remarks:</span>
                    <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($invoice['notes'])); ?></p>
                </div>
            <?php endif; ?>

            <!-- Reversal block (if cancelled) -->
            <?php if ($invoice['status'] === 'CANCELLED'): ?>
                <div class="alert alert-danger border-danger-subtle rounded-4 p-4 mb-0">
                    <h6 class="fw-bold alert-heading mb-2"><i class="bi bi-exclamation-octagon-fill me-2"></i> Invoice Cancelled / Reversed</h6>
                    <div class="row g-3 small">
                        <div class="col-md-6">
                            <span class="text-muted">Reversal Journal Entry:</span>
                            <strong class="font-monospace text-dark d-block"><?= htmlspecialchars($invoice['reversal_journal_number'] ?: '-'); ?></strong>
                        </div>
                        <div class="col-12 border-top pt-2">
                            <span class="text-muted">Reason for Cancellation:</span>
                            <p class="mb-0 fw-semibold text-dark"><?= nl2br(htmlspecialchars($invoice['reversal_reason'])); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Summary Column -->
    <div class="col-12 col-lg-3 d-print-none">
        <!-- Summary totals -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <span class="fw-semibold text-dark font-monospace">LKR <?= number_format($invoice['subtotal'], 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Invoice Discount:</span>
                    <span class="fw-semibold text-danger font-monospace">-LKR <?= number_format($invoice['discount'], 2); ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-0">
                    <span class="fw-bold text-dark fs-6">Grand Total:</span>
                    <span class="fw-bold text-success fs-5 font-monospace">LKR <?= number_format($invoice['total'], 2); ?></span>
                </div>
            </div>
        </div>

        <!-- Profitability (POSTED only) -->
        <?php if ($invoice['status'] === 'POSTED'): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-light border">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart text-success me-2"></i> Profitability Summary</h6>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Revenue:</span>
                    <span class="fw-semibold text-success font-monospace">LKR <?= number_format($invoice['total'], 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Cost of Goods Sold (COGS):</span>
                    <span class="fw-semibold text-danger font-monospace">LKR <?= number_format($totalCogs, 2); ?></span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between mb-0">
                    <span class="fw-bold text-dark small">Gross profit margin:</span>
                    <span class="fw-bold text-success font-monospace">LKR <?= number_format($grossProfit, 2); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Audit specs -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 small">
                <ul class="list-group list-group-flush mb-0">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Status:</span>
                        <span class="badge bg-success"><?= htmlspecialchars($invoice['status']); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Journal ID:</span>
                        <span class="fw-bold text-dark font-monospace"><?= htmlspecialchars($invoice['journal_number'] ?: '-'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent">
                        <span class="text-secondary">Registered By:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($invoice['creator_name'] ?? 'System'); ?></span>
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
            <form action="<?= \Core\Helper::baseUrl('modules/invoices/post'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $invoice['id']; ?>">
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

<!-- Modal: Cancel/Reverse Invoice -->
<div class="modal fade" id="cancelInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white bg-danger">
                <h5 class="modal-title fw-bold"><i class="bi bi-x-circle me-2"></i> Reverse Sales Invoice</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/invoices/cancel'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $invoice['id']; ?>">
                <div class="modal-body p-4">
                    <p>Reversing this posted invoice will perform a full ledger reversal, restore any product quantities back to inventory balances, and refund customer outstanding balance allocations.</p>
                    <div class="mb-3">
                        <label for="reversal_reason" class="form-label fw-semibold">Reason for Reversal <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reversal_reason" name="reversal_reason" rows="3" placeholder="State reason for reversal..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Execute Reversal</button>
                </div>
            </form>
        </div>
    </div>
</div>
