<?php
// Variables available from controller:
// $customers, $products, $services, $machineryAssets, $bankAccounts
// $defaultWarehouseId, $prefilled
?>

<style>
/* ─── Invoice Composer Styles ─────────────────────────────────── */
.inv-header-bar {
    background: linear-gradient(135deg, #0f4c2a 0%, #1b6b3a 60%, #2d9249 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    color: #fff;
    margin-bottom: 1.75rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    box-shadow: 0 6px 30px rgba(15,76,42,.22);
}
.inv-header-bar .inv-icon-wrap {
    width: 52px; height: 52px;
    background: rgba(255,255,255,.15);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; flex-shrink: 0;
}
.inv-header-bar h4 { margin: 0; font-weight: 700; font-size: 1.2rem; }
.inv-header-bar p  { margin: 0; opacity: .75; font-size: .82rem; }
.inv-back-btn {
    margin-left: auto;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff; border-radius: 50px;
    padding: .4rem 1.1rem; font-size: .82rem;
    text-decoration: none;
    display: flex; align-items: center; gap: .4rem;
    transition: background .2s; flex-shrink: 0;
}
.inv-back-btn:hover { background: rgba(255,255,255,.22); color: #fff; }

.inv-card {
    background: #fff; border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,.06);
    border: 1px solid #eef0f2;
    margin-bottom: 1.25rem; overflow: hidden;
}
.inv-card-head {
    display: flex; align-items: center; gap: .6rem;
    padding: .85rem 1.25rem;
    background: #f8fafb; border-bottom: 1px solid #eef0f2;
    font-size: .82rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .04em; color: #4a5568;
}
.inv-card-head i { font-size: 1rem; }
.inv-card-body { padding: 1.25rem; }

.cust-badge {
    display: inline-flex; align-items: center;
    background: #e6f4ec; color: #1a6334;
    border-radius: 50px; padding: .2rem .75rem;
    font-size: .78rem; font-weight: 600;
    border: 1px solid #b7dfc9; gap: .3rem;
}
.cust-badge.walkin { background: #fff3cd; color: #856404; border-color: #ffc107; }

.add-item-btn {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: .35rem; padding: .9rem 1.5rem; border-radius: 12px;
    font-size: .8rem; font-weight: 600; border: 2px dashed;
    transition: all .2s; cursor: pointer; background: transparent; min-width: 120px;
}
.add-item-btn i { font-size: 1.4rem; }
.add-item-btn.product  { border-color: #4f46e5; color: #4f46e5; }
.add-item-btn.product:hover  { background: #4f46e5; color: #fff; }
.add-item-btn.service  { border-color: #d97706; color: #d97706; }
.add-item-btn.service:hover  { background: #d97706; color: #fff; }
.add-item-btn.rental   { border-color: #059669; color: #059669; }
.add-item-btn.rental:hover   { background: #059669; color: #fff; }

#itemsTable { font-size: .82rem; }
#itemsTable thead th {
    background: #f1f5f9; border-bottom: 2px solid #e2e8f0;
    color: #64748b; font-weight: 600; font-size: .75rem;
    text-transform: uppercase; letter-spacing: .04em;
    padding: .6rem .75rem; white-space: nowrap;
}
#itemsTable tbody tr { transition: background .15s; }
#itemsTable tbody tr:hover { background: #f8fafc; }
#itemsTable td { padding: .55rem .75rem; vertical-align: middle; }
.item-type-badge { font-size: .7rem; font-weight: 700; padding: .2rem .55rem; border-radius: 50px; }
.empty-cart {
    padding: 3rem 1rem; text-align: center; color: #94a3b8;
    display: flex; flex-direction: column; align-items: center; gap: .6rem;
}
.empty-cart i { font-size: 2.5rem; opacity: .4; }

.summary-panel { position: sticky; top: 80px; }
.total-row { display: flex; justify-content: space-between; align-items: center; }
.total-row .label { color: #64748b; font-size: .82rem; }
.total-row .val { font-family: 'Courier New', monospace; font-weight: 600; color: #1e293b; font-size: .85rem; }
.grand-total-row .label { font-size: 1rem; font-weight: 700; color: #1e293b; }
.grand-total-row .val { font-size: 1.25rem; color: #059669; font-weight: 800; }
.post-btn {
    background: linear-gradient(135deg, #0f4c2a, #1b6b3a);
    color: #fff; border: none; border-radius: 10px;
    padding: .8rem; font-weight: 700; font-size: .95rem;
    width: 100%; cursor: pointer; transition: opacity .2s;
    box-shadow: 0 4px 14px rgba(15,76,42,.3);
}
.post-btn:hover { opacity: .9; }
.draft-btn {
    background: transparent; color: #64748b;
    border: 2px solid #cbd5e1; border-radius: 10px;
    padding: .65rem; font-weight: 600; font-size: .85rem;
    width: 100%; cursor: pointer; transition: all .2s;
}
.draft-btn:hover { border-color: #94a3b8; color: #1e293b; }

.modal-content { border-radius: 16px !important; border: 0 !important; }
.modal-header  { border-radius: 16px 16px 0 0 !important; border-bottom: 0 !important; padding: 1.25rem 1.5rem !important; }
.modal-body    { padding: 1.25rem 1.5rem !important; }
.modal-search input {
    border-radius: 50px; background: #f1f5f9;
    border: 1px solid #e2e8f0; padding: .55rem 1rem;
    font-size: .85rem; width: 100%; outline: none; transition: border .2s;
}
.modal-search input:focus { box-shadow: 0 0 0 3px rgba(5,150,105,.15); border-color: #059669; }
.modal-table { font-size: .8rem; }
.modal-table thead th {
    background: #f8fafc; color: #64748b;
    font-size: .72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .04em;
    padding: .5rem .75rem; border-bottom: 2px solid #e2e8f0;
}
.modal-table tbody tr:hover td { background: #f0fdf4; }
.modal-table td { padding: .5rem .75rem; vertical-align: middle; }
.add-row-btn {
    width: 30px; height: 30px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; border: 0; cursor: pointer; transition: opacity .15s;
}
.add-row-btn:hover { opacity: .8; }
</style>

<!-- HEADER -->
<div class="inv-header-bar">
    <div class="inv-icon-wrap"><i class="bi bi-receipt-cutoff"></i></div>
    <div>
        <h4>Compose Central Invoice</h4>
        <p>Record a new sales invoice — mix products, services and machinery rentals.</p>
    </div>
    <a href="<?= \Core\Helper::baseUrl('modules/invoices'); ?>" class="inv-back-btn">
        <i class="bi bi-arrow-left"></i> Back to Logs
    </a>
</div>

<form action="<?= \Core\Helper::baseUrl('modules/invoices/store'); ?>" method="POST" id="invoiceForm">
    <?= \Core\CSRF::getFormField(); ?>
    <input type="hidden" name="service_job_id"      id="service_job_id"      value="<?= htmlspecialchars($prefilled['service_job_id'] ?? ''); ?>">
    <input type="hidden" name="machinery_rental_id" id="machinery_rental_id" value="<?= htmlspecialchars($prefilled['machinery_rental_id'] ?? ''); ?>">
    <input type="hidden" name="warehouse_id"        id="warehouse_id"        value="<?= $defaultWarehouseId; ?>">

    <div class="row g-4">

        <!-- LEFT COLUMN -->
        <div class="col-12 col-xl-8">



            <!-- Add Items Toolbar -->
            <div class="inv-card">
                <div class="inv-card-head">
                    <i class="bi bi-cart-plus text-success"></i> Add Items to Invoice
                </div>
                <div class="inv-card-body">
                    <div class="d-flex flex-wrap gap-3">
                        <button type="button" class="add-item-btn product" data-bs-toggle="modal" data-bs-target="#productModal">
                            <i class="bi bi-box-seam"></i>
                            Add Product
                        </button>
                        <button type="button" class="add-item-btn service" data-bs-toggle="modal" data-bs-target="#serviceModal">
                            <i class="bi bi-gear-wide-connected"></i>
                            Add Service
                        </button>
                        <button type="button" class="add-item-btn rental" data-bs-toggle="modal" data-bs-target="#rentalModal">
                            <i class="bi bi-truck-flatbed"></i>
                            Add Rental
                        </button>
                    </div>
                </div>
            </div>

            <!-- Invoice Items Table -->
            <div class="inv-card">
                <div class="inv-card-head">
                    <i class="bi bi-table text-info"></i> Invoice Line Items
                    <span class="ms-auto badge bg-secondary rounded-pill" id="itemCountBadge">0 items</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width:90px;">Type</th>
                                <th>Item / Description</th>
                                <th style="width:80px;" class="text-center">Stock</th>
                                <th style="width:130px;">Qty &amp; Unit</th>
                                <th style="width:130px;">Unit Price</th>
                                <th style="width:130px;" class="text-end">Total (LKR)</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- rows injected by JS -->
                        </tbody>
                    </table>
                    <div class="empty-cart" id="emptyCartMsg">
                        <i class="bi bi-cart3"></i>
                        <span>No items added yet. Use the buttons above to add products, services or rentals.</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="inv-card">
                <div class="inv-card-head">
                    <i class="bi bi-chat-left-text text-secondary"></i> Notes &amp; Remarks
                </div>
                <div class="inv-card-body">
                    <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2"
                        placeholder="Describe specific terms, machinery rented, plowing sites, etc…"></textarea>
                </div>
            </div>

        </div><!-- /col-xl-8 -->

        <!-- RIGHT COLUMN -->
        <div class="col-12 col-xl-4">
            <div class="summary-panel">

                <!-- Invoice Details -->
                <div class="inv-card">
                    <div class="inv-card-head">
                        <i class="bi bi-info-circle text-primary"></i> Invoice Details
                    </div>
                    <div class="inv-card-body">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label fw-semibold small">
                                Customer
                                <span id="walkinIndicator" class="cust-badge walkin ms-2">
                                    <i class="bi bi-person-walking"></i> Walk-in
                                </span>
                            </label>
                            <!-- NO "required" attr — controller maps empty value to PTY-WALKIN -->
                            <select class="form-select form-select-sm" id="customer_id" name="customer_id" onchange="handleCustomerChange()">
                                <option value="">-- Walk-in Customer (No Account) --</option>
                                <optgroup label="Registered Customers">
                                    <?php foreach ($customers as $c): ?>
                                        <option value="<?= $c['id']; ?>" <?= ($prefilled['customer_id'] == $c['id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($c['party_code']); ?> &mdash; <?= htmlspecialchars($c['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Society Members">
                                    <?php foreach ($members as $m): ?>
                                        <option value="M_<?= $m['id']; ?>" data-is-member="1">
                                            <?= htmlspecialchars($m['membership_no']); ?> &mdash; <?= htmlspecialchars($m['full_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                            <div class="form-text text-warning-emphasis small" id="creditWarning" style="display:none;">
                                <i class="bi bi-exclamation-triangle-fill"></i> Credit sales require a registered customer.
                            </div>
                        </div>
                        <div>
                            <label for="invoice_date" class="form-label fw-semibold small">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="invoice_date" name="invoice_date" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="inv-card">
                    <div class="inv-card-head">
                        <i class="bi bi-credit-card text-success"></i> Payment Method
                    </div>
                    <div class="inv-card-body">
                        <select class="form-select form-select-sm mb-3" id="payment_type" name="payment_type" required onchange="togglePaymentFields()">
                            <option value="CASH">&#128181; Cash Drawer</option>
                            <option value="BANK">&#127974; Bank Deposit</option>
                            <option value="CHEQUE">&#128196; Received Cheque</option>
                            <option value="CREDIT" id="payment_credit_option">&#128220; On Credit (Ledger)</option>
                        </select>

                        <div id="bankAccountSection" style="display:none;">
                            <label for="bank_account_id" class="form-label fw-semibold small">Bank Account <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="bank_account_id" name="bank_account_id">
                                <?php foreach ($bankAccounts as $ba): ?>
                                    <option value="<?= $ba['id']; ?>"><?= htmlspecialchars($ba['bank_name']); ?> &mdash; <?= htmlspecialchars($ba['account_number']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="chequeDetailsSection" style="display:none;">
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label fw-semibold small mb-1">Cheque Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="cheque_number" name="cheque_number" placeholder="e.g. 012356">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small mb-1">Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="cheque_bank" name="cheque_bank" placeholder="e.g. BOC">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small mb-1">Cheque Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-sm" id="cheque_date" name="cheque_date" value="<?= date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="inv-card">
                    <div class="inv-card-head">
                        <i class="bi bi-calculator text-primary"></i> Invoice Summary
                    </div>
                    <div class="inv-card-body">
                        <div class="total-row mb-2">
                            <span class="label">Subtotal</span>
                            <span class="val" id="summarySubtotal">LKR 0.00</span>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="discount_percent" class="form-label small fw-semibold text-muted mb-1">Discount (%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm text-end font-monospace"
                                       id="discount_percent" value="0.00" oninput="calculateDiscountAmount()">
                            </div>
                            <div class="col-6">
                                <label for="discount" class="form-label small fw-semibold text-muted mb-1">Discount (LKR)</label>
                                <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end font-monospace"
                                       id="discount" name="discount" value="0.00" oninput="clearDiscountPercent(); calculateInvoiceTotal()">
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="total-row grand-total-row mb-4">
                            <span class="label">Grand Total</span>
                            <span class="val" id="summaryTotal">LKR 0.00</span>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="post" class="post-btn" onclick="return validateInvoiceForm(event)">
                                <i class="bi bi-send-check me-1"></i> Post Invoice
                            </button>
                            <button type="submit" name="action" value="draft" class="draft-btn">
                                <i class="bi bi-floppy me-1"></i> Save as Draft
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /col-xl-4 -->

    </div>
</form>

<!-- MODAL: ADD PRODUCT -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <div class="modal-header" style="background:linear-gradient(135deg,#4338ca,#6366f1);color:#fff;">
                <h5 class="modal-title fw-bold" id="productModalLabel"><i class="bi bi-box-seam me-2"></i>Add Product from Marketplace</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-search mb-3">
                    <input type="text" id="prodSearchInput" placeholder="Search by product name or SKU..." oninput="filterModalItems('PRODUCT', this.value)">
                </div>
                <div class="table-responsive" style="max-height:380px;overflow-y:auto;">
                    <table class="table modal-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Stock</th>
                                <th class="text-end">Base Price</th>
                                <th style="width:90px;">Qty</th>
                                <th style="width:110px;">Price (LKR)</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="prodModalTableBody">
                            <?php foreach ($products as $p): ?>
                                <tr class="prod-row" data-search="<?= htmlspecialchars(strtolower($p['name_en'] . ' ' . ($p['sku'] ?? ''))); ?>">
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($p['name_en']); ?></div>
                                        <small class="text-muted font-monospace">SKU: <?= htmlspecialchars($p['sku'] ?? '-'); ?> &bull; <?= htmlspecialchars($p['category_name'] ?? 'General'); ?></small>
                                    </td>
                                    <td class="text-center fw-semibold font-monospace text-muted"><?= number_format($p['stocks'][$defaultWarehouseId] ?? 0, 2); ?></td>
                                    <td class="text-end font-monospace">LKR <?= number_format($p['default_selling_price'], 2); ?></td>
                                    <td><input type="number" step="1" min="1" class="form-control form-control-sm font-monospace modal-qty-input" value="1"></td>
                                    <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace modal-price-input" value="<?= number_format($p['default_selling_price'], 2, '.', ''); ?>"></td>
                                    <td class="text-center">
                                        <button type="button" class="add-row-btn" style="background:#4f46e5;color:#fff;" onclick="addProductRowFromModal(<?= htmlspecialchars(json_encode($p)); ?>, this)">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ADD SERVICE -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <div class="modal-header" style="background:linear-gradient(135deg,#b45309,#d97706);color:#fff;">
                <h5 class="modal-title fw-bold" id="serviceModalLabel"><i class="bi bi-gear-wide-connected me-2"></i>Add Service</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-search mb-3">
                    <input type="text" id="srvSearchInput" placeholder="Search by service name or code..." oninput="filterModalItems('SERVICE', this.value)">
                </div>
                <div class="table-responsive" style="max-height:380px;overflow-y:auto;">
                    <table class="table modal-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Unit</th>
                                <th class="text-end">Base Price</th>
                                <th style="width:90px;">Qty</th>
                                <th style="width:110px;">Price (LKR)</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="srvModalTableBody">
                            <?php foreach ($services as $s): ?>
                                <tr class="srv-row" data-search="<?= htmlspecialchars(strtolower($s['service_name'] . ' ' . $s['service_code'])); ?>">
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($s['service_name']); ?></div>
                                        <small class="text-muted">Code: <strong class="text-dark font-monospace"><?= htmlspecialchars($s['service_code']); ?></strong></small>
                                    </td>
                                    <td><?= htmlspecialchars($s['unit']); ?></td>
                                    <td class="text-end font-monospace">LKR <?= number_format($s['default_price'], 2); ?></td>
                                    <td><input type="number" step="1" min="1" class="form-control form-control-sm font-monospace modal-qty-input" value="1"></td>
                                    <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace modal-price-input" value="<?= number_format($s['default_price'], 2, '.', ''); ?>"></td>
                                    <td class="text-center">
                                        <button type="button" class="add-row-btn" style="background:#d97706;color:#fff;" onclick="addServiceRowFromModal(<?= htmlspecialchars(json_encode($s)); ?>, this)">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ADD RENTAL -->
<div class="modal fade" id="rentalModal" tabindex="-1" aria-labelledby="rentalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <div class="modal-header" style="background:linear-gradient(135deg,#065f46,#059669);color:#fff;">
                <h5 class="modal-title fw-bold" id="rentalModalLabel"><i class="bi bi-truck-flatbed me-2"></i>Add Machinery / Rental Asset</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-search mb-3">
                    <input type="text" id="rentalSearchInput" placeholder="Search machinery by name, code or category..." oninput="filterModalItems('RENTAL', this.value)">
                </div>
                <div class="table-responsive" style="max-height:380px;overflow-y:auto;">
                    <table class="table modal-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Machine Details</th>
                                <th>Status</th>
                                <th class="text-end">Default Rate</th>
                                <th style="width:90px;">Qty</th>
                                <th style="width:110px;">Price (LKR)</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($machineryAssets as $m): ?>
                                <tr class="rental-row" data-search="<?= htmlspecialchars(strtolower($m['machinery_name'] . ' ' . $m['machinery_code'] . ' ' . $m['category'])); ?>">
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($m['machinery_name']); ?></div>
                                        <small class="text-muted font-monospace">Code: <?= htmlspecialchars($m['machinery_code']); ?> &bull; Serial: <?= htmlspecialchars($m['serial_number'] ?: '-'); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill <?= ($m['status'] === 'AVAILABLE') ? 'bg-success' : (($m['status'] === 'RENTED') ? 'bg-warning text-dark' : 'bg-danger'); ?>">
                                            <?= htmlspecialchars($m['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end font-monospace">LKR <?= number_format($m['default_rental_rate'], 2); ?> / <?= htmlspecialchars($m['rental_unit']); ?></td>
                                    <td><input type="number" step="1" min="1" class="form-control form-control-sm font-monospace modal-machine-qty-input" value="1"></td>
                                    <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace modal-machine-price-input" value="<?= number_format($m['default_rental_rate'], 2, '.', ''); ?>"></td>
                                    <td class="text-center">
                                        <button type="button" class="add-row-btn" style="background:#059669;color:#fff;" onclick="addMachineRowFromDirectory(<?= htmlspecialchars(json_encode($m)); ?>, this)">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const availableProducts  = <?= json_encode($products); ?>;
let rowCount = 0;
const defaultWarehouseId = <?= json_encode($defaultWarehouseId); ?>;
const defaultServiceId = <?= !empty($services) ? $services[0]['id'] : '0'; ?>;

function handleCustomerChange() {
    const customerSelect    = document.getElementById('customer_id');
    const paymentTypeSelect = document.getElementById('payment_type');
    const creditOption      = document.getElementById('payment_credit_option');
    const walkinIndicator   = document.getElementById('walkinIndicator');

    const isWalkin = (customerSelect.value === "");
    walkinIndicator.style.display = isWalkin ? '' : 'none';

    if (isWalkin) {
        if (paymentTypeSelect.value === "CREDIT") {
            paymentTypeSelect.value = "CASH";
            togglePaymentFields();
        }
        creditOption.disabled = true;
    } else {
        creditOption.disabled = false;
    }

    // Handle Member Discount
    const selectedOption = customerSelect.options[customerSelect.selectedIndex];
    if (selectedOption && selectedOption.getAttribute('data-is-member') === "1") {
        if (confirm("This person is a member. Do you want to add a 10% member discount automatically?")) {
            document.getElementById('discount_percent').value = "10.00";
            calculateDiscountAmount();
        } else {
            clearDiscountPercent();
            calculateInvoiceTotal();
        }
    }
}

function filterModalItems(type, query) {
    query = query.toLowerCase();
    let selector = '';
    if (type === 'PRODUCT') selector = '.prod-row';
    else if (type === 'SERVICE') selector = '.srv-row';
    else if (type === 'RENTAL')  selector = '.rental-row';
    document.querySelectorAll(selector).forEach(row => {
        const text = row.getAttribute('data-search') || '';
        row.style.setProperty('display', text.includes(query) ? '' : 'none', 'important');
    });
}

function updateItemCount() {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    document.getElementById('itemCountBadge').textContent = rows.length + ' item' + (rows.length === 1 ? '' : 's');
    document.getElementById('emptyCartMsg').style.display = rows.length ? 'none' : '';
}

function addProductRowFromModal(prod, btn) {
    const row   = btn.closest('tr');
    const qty   = parseInt(row.querySelector('.modal-qty-input').value) || 1;
    const price = parseFloat(row.querySelector('.modal-price-input').value) || parseFloat(prod.default_selling_price);
    rowCount++;
    const tbody = document.getElementById('itemsTableBody');
    const tr    = document.createElement('tr');
    tr.id = `row_${rowCount}`;
    const stock = prod.stocks && prod.stocks[defaultWarehouseId] !== undefined ? parseFloat(prod.stocks[defaultWarehouseId]) : 0;
    const total = (qty * price).toFixed(2);
    tr.innerHTML = `
        <td>
            <span class="item-type-badge" style="background:#ede9fe;color:#4f46e5;">PRODUCT</span>
            <input type="hidden" name="items[${rowCount}][item_type]" value="PRODUCT">
            <input type="hidden" name="items[${rowCount}][product_id]" value="${prod.id}">
        </td>
        <td>
            <div class="fw-semibold text-dark small">${htmlspecialchars(prod.name_en)}</div>
            <div class="text-muted" style="font-size:.72rem;font-family:monospace;">SKU: ${htmlspecialchars(prod.sku || '-')}</div>
            <input type="text" class="form-control form-control-sm mt-1" name="items[${rowCount}][description]" placeholder="Optional remarks" style="font-size:.75rem;">
        </td>
        <td class="font-monospace fw-semibold text-center text-muted" id="available_${rowCount}">${stock.toFixed(2)}</td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="1" min="1" class="form-control font-monospace qty-input" name="items[${rowCount}][quantity]" value="${qty}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
                <span class="input-group-text bg-light text-muted" style="font-size:.75rem;">${htmlspecialchars(prod.unit_code || 'Units')}</span>
            </div>
        </td>
        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace price-input" name="items[${rowCount}][unit_price]" value="${price.toFixed(2)}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();"></td>
        <td class="text-end fw-bold font-monospace text-dark row-total" id="rowtotal_${rowCount}">${total}</td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="removeRow(${rowCount})"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
    calculateInvoiceTotal(); updateItemCount();
    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
    row.querySelector('.modal-qty-input').value = "1";
    row.querySelector('.modal-price-input').value = parseFloat(prod.default_selling_price).toFixed(2);
    document.getElementById('prodSearchInput').value = "";
    filterModalItems('PRODUCT', '');
}

function addServiceRowFromModal(srv, btn) {
    const row   = btn.closest('tr');
    const qty   = parseInt(row.querySelector('.modal-qty-input').value) || 1;
    const price = parseFloat(row.querySelector('.modal-price-input').value) || parseFloat(srv.default_price);
    rowCount++;
    const tbody = document.getElementById('itemsTableBody');
    const tr    = document.createElement('tr');
    tr.id = `row_${rowCount}`;
    const total = (qty * price).toFixed(2);
    
    // Set the hidden service_job_id so that the backend knows which job this invoice links to!
    document.getElementById('service_job_id').value = srv.service_job_id || srv.id;

    tr.innerHTML = `
        <td>
            <span class="item-type-badge" style="background:#fef3c7;color:#b45309;">SERVICE</span>
            <input type="hidden" name="items[${rowCount}][item_type]" value="SERVICE">
            <input type="hidden" name="items[${rowCount}][service_id]" value="${srv.id}">
        </td>
        <td>
            <div class="fw-semibold text-dark small">${htmlspecialchars(srv.service_name)} (Code: ${htmlspecialchars(srv.service_code)})</div>
            <input type="text" class="form-control form-control-sm mt-1" name="items[${rowCount}][description]" value="${htmlspecialchars(srv.description || '')}" placeholder="Optional remarks" style="font-size:.75rem;">
        </td>
        <td class="text-center text-muted" id="available_${rowCount}">-</td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="1" min="1" class="form-control font-monospace qty-input" name="items[${rowCount}][quantity]" value="${qty}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
                <span class="input-group-text bg-light text-muted" style="font-size:.75rem;">${htmlspecialchars(srv.unit || 'Job')}</span>
            </div>
        </td>
        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace price-input" name="items[${rowCount}][unit_price]" value="${price.toFixed(2)}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();"></td>
        <td class="text-end fw-bold font-monospace text-dark row-total" id="rowtotal_${rowCount}">${total}</td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="removeRow(${rowCount})"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
    calculateInvoiceTotal(); updateItemCount();
    bootstrap.Modal.getInstance(document.getElementById('serviceModal')).hide();
    row.querySelector('.modal-qty-input').value = "1";
    row.querySelector('.modal-price-input').value = parseFloat(srv.default_price).toFixed(2);
    document.getElementById('srvSearchInput').value = "";
    filterModalItems('SERVICE', '');
}

function addRentalRowFromModal(rental, btn) {
    const row        = btn.closest('tr');
    const totalCharge = parseFloat(row.querySelector('.modal-price-input').value) || parseFloat(rental.total_charge);
    const qty        = parseInt(rental.quantity) || 1;
    const rate       = (totalCharge / qty);
    rowCount++;
    const tbody = document.getElementById('itemsTableBody');
    const tr    = document.createElement('tr');
    tr.id = `row_${rowCount}`;
    let linkedServiceId = defaultServiceId;
    document.getElementById('machinery_rental_id').value = rental.id;
    tr.innerHTML = `
        <td>
            <span class="item-type-badge" style="background:#d1fae5;color:#065f46;">RENTAL</span>
            <input type="hidden" name="items[${rowCount}][item_type]" value="SERVICE">
            <input type="hidden" name="items[${rowCount}][service_id]" value="${linkedServiceId}">
        </td>
        <td>
            <div class="fw-semibold text-dark small">${htmlspecialchars(rental.rental_number)}: Rental of ${htmlspecialchars(rental.machinery_name)}</div>
            <div class="text-muted" style="font-size:.72rem;font-family:monospace;">Serial: ${htmlspecialchars(rental.serial_number || '-')}</div>
            <input type="text" class="form-control form-control-sm mt-1" name="items[${rowCount}][description]" value="Machinery Rental Invoice Link" placeholder="Optional remarks" style="font-size:.75rem;">
        </td>
        <td class="text-center text-muted" id="available_${rowCount}">-</td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="1" min="1" class="form-control font-monospace qty-input" name="items[${rowCount}][quantity]" value="${qty}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
                <span class="input-group-text bg-light text-muted" style="font-size:.75rem;">${htmlspecialchars(rental.rental_unit || 'Hour')}</span>
            </div>
        </td>
        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace price-input" name="items[${rowCount}][unit_price]" value="${rate.toFixed(2)}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();"></td>
        <td class="text-end fw-bold font-monospace text-dark row-total" id="rowtotal_${rowCount}">${totalCharge.toFixed(2)}</td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="removeRow(${rowCount})"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
    calculateInvoiceTotal(); updateItemCount();
    bootstrap.Modal.getInstance(document.getElementById('rentalModal')).hide();
    document.getElementById('rentalSearchInput').value = "";
    filterModalItems('RENTAL', '');
}

function addMachineRowFromDirectory(machine, btn) {
    const row   = btn.closest('tr');
    const qty   = parseInt(row.querySelector('.modal-machine-qty-input').value) || 1;
    const price = parseFloat(row.querySelector('.modal-machine-price-input').value) || parseFloat(machine.default_rental_rate);
    rowCount++;
    const tbody = document.getElementById('itemsTableBody');
    const tr    = document.createElement('tr');
    tr.id = `row_${rowCount}`;
    let linkedServiceId = defaultServiceId;
    const total = (qty * price).toFixed(2);
    tr.innerHTML = `
        <td>
            <span class="item-type-badge" style="background:#d1fae5;color:#065f46;">RENTAL</span>
            <input type="hidden" name="items[${rowCount}][item_type]" value="SERVICE">
            <input type="hidden" name="items[${rowCount}][service_id]" value="${linkedServiceId}">
        </td>
        <td>
            <div class="fw-semibold text-dark small">Rental: ${htmlspecialchars(machine.machinery_name)}</div>
            <div class="text-muted" style="font-size:.72rem;font-family:monospace;">Code: ${htmlspecialchars(machine.machinery_code)} | Serial: ${htmlspecialchars(machine.serial_number || '-')}</div>
            <input type="text" class="form-control form-control-sm mt-1" name="items[${rowCount}][description]" value="Direct Machinery Asset Billing" placeholder="Optional remarks" style="font-size:.75rem;">
        </td>
        <td class="text-center text-muted" id="available_${rowCount}">-</td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="1" min="1" class="form-control font-monospace qty-input" name="items[${rowCount}][quantity]" value="${qty}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
                <span class="input-group-text bg-light text-muted" style="font-size:.75rem;">${htmlspecialchars(machine.rental_unit || 'Hour')}</span>
            </div>
        </td>
        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace price-input" name="items[${rowCount}][unit_price]" value="${price.toFixed(2)}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();"></td>
        <td class="text-end fw-bold font-monospace text-dark row-total" id="rowtotal_${rowCount}">${total}</td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="removeRow(${rowCount})"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
    calculateInvoiceTotal(); updateItemCount();
    bootstrap.Modal.getInstance(document.getElementById('rentalModal')).hide();
    row.querySelector('.modal-machine-qty-input').value = "1";
    row.querySelector('.modal-machine-price-input').value = parseFloat(machine.default_rental_rate).toFixed(2);
    document.getElementById('rentalSearchInput').value = "";
    filterModalItems('RENTAL', '');
}

function removeRow(id) {
    const row = document.getElementById(`row_${id}`);
    if (row) { row.remove(); calculateInvoiceTotal(); updateItemCount(); }
}

function calculateRowTotal(id) {
    const row   = document.getElementById(`row_${id}`);
    const qty   = parseFloat(row.querySelector('.qty-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const total = (qty * price);
    document.getElementById(`rowtotal_${id}`).textContent = total.toFixed(2);
}

function calculateDiscountAmount() {
    let subtotal = 0;
    document.querySelectorAll('.row-total').forEach(el => { subtotal += parseFloat(el.textContent) || 0; });
    const percent = parseFloat(document.getElementById('discount_percent').value) || 0;
    const discountAmt = (subtotal * (percent / 100));
    document.getElementById('discount').value = discountAmt.toFixed(2);
    calculateInvoiceTotal();
}

function clearDiscountPercent() {
    document.getElementById('discount_percent').value = "0.00";
}

function calculateInvoiceTotal() {
    let subtotal = 0;
    document.querySelectorAll('.row-total').forEach(el => { subtotal += parseFloat(el.textContent) || 0; });
    
    const percent = parseFloat(document.getElementById('discount_percent').value) || 0;
    if (percent > 0) {
        const discountAmt = (subtotal * (percent / 100));
        document.getElementById('discount').value = discountAmt.toFixed(2);
    }
    
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const total    = Math.max(0, subtotal - discount);
    document.getElementById('summarySubtotal').textContent = 'LKR ' + subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summaryTotal').textContent    = 'LKR ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function togglePaymentFields() {
    const method     = document.getElementById('payment_type').value;
    const bankSect   = document.getElementById('bankAccountSection');
    const chequeSect = document.getElementById('chequeDetailsSection');
    document.getElementById('bank_account_id').required = false;
    document.getElementById('cheque_number').required   = false;
    document.getElementById('cheque_bank').required     = false;
    bankSect.style.display   = 'none';
    chequeSect.style.display = 'none';
    if (method === 'BANK') {
        bankSect.style.display = 'block';
        document.getElementById('bank_account_id').required = true;
    } else if (method === 'CHEQUE') {
        chequeSect.style.display = 'block';
        document.getElementById('cheque_number').required = true;
        document.getElementById('cheque_bank').required   = true;
    }
}

function validateInvoiceForm(event) {
    const customerSelect    = document.getElementById('customer_id');
    const paymentTypeSelect = document.getElementById('payment_type');
    if (customerSelect.value === "" && paymentTypeSelect.value === "CREDIT") {
        alert("Walk-in Customer is NOT allowed to make purchases on Credit.\nPlease select a registered Customer.");
        event.preventDefault();
        return false;
    }
    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (rows.length === 0) {
        alert("Please add at least one item to the invoice before posting.");
        event.preventDefault();
        return false;
    }
    let valid = true;
    rows.forEach(row => {
        const typeInput = row.querySelector('input[name$="[item_type]"]');
        if (!typeInput || typeInput.value !== 'PRODUCT') return;
        const prodName  = row.querySelector('.fw-semibold')?.textContent || 'Unknown';
        const available = parseFloat(row.querySelector('[id^="available_"]')?.textContent) || 0;
        const qty       = parseFloat(row.querySelector('.qty-input')?.value) || 0;
        if (qty > available) {
            alert(`Stock Error: Qty for "${prodName}" (${qty}) exceeds available stock (${available}).`);
            valid = false;
        }
    });
    if (!valid) { event.preventDefault(); return false; }
    return true;
}

function htmlspecialchars(str) {
    if (typeof str !== 'string') return '';
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.addEventListener('DOMContentLoaded', () => {
    handleCustomerChange();
    togglePaymentFields();
    updateItemCount();
});
</script>
