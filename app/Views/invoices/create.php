<?php
// Variables available from controller:
// $customers, $products, $services, $machineryAssets, $bankAccounts
// $defaultWarehouseId, $prefilled
?>

<style>
/* ═══════════════════════════════════════════════════════════
   AGRI PRO — Create Invoice  (Premium Redesign)
   ═══════════════════════════════════════════════════════════ */

/* ── Page Header ─────────────────────────────────────────── */
.inv-page-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #0f4c2a 0%, #166534 55%, #15803d 100%);
    border-radius: 18px; color: #fff; margin-bottom: 1.75rem;
    box-shadow: 0 8px 32px rgba(15,76,42,.25);
}
.inv-page-header .header-left { display: flex; align-items: center; gap: 1rem; }
.inv-page-header .inv-icon {
    width: 54px; height: 54px;
    background: rgba(255,255,255,.15); border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; flex-shrink: 0;
}
.inv-page-header h4 { margin: 0; font-weight: 800; font-size: 1.25rem; letter-spacing: -.01em; }
.inv-page-header p  { margin: 0; opacity: .72; font-size: .83rem; }
.inv-back-btn {
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.28);
    color: #fff; border-radius: 50px; padding: .45rem 1.2rem;
    font-size: .82rem; text-decoration: none;
    display: flex; align-items: center; gap: .4rem; transition: background .2s;
}
.inv-back-btn:hover { background: rgba(255,255,255,.24); color: #fff; }

/* ── Section Cards ──────────────────────────────────────── */
.inv-section {
    background: #fff; border-radius: 16px;
    border: 1px solid #e8edf2;
    box-shadow: 0 2px 12px rgba(0,0,0,.05);
    margin-bottom: 1.25rem; overflow: hidden;
}
.inv-section-head {
    display: flex; align-items: center; gap: .65rem;
    padding: .9rem 1.4rem;
    background: #f8fafb; border-bottom: 1px solid #e8edf2;
    font-size: .78rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em; color: #475569;
}
.inv-section-head i { font-size: 1rem; flex-shrink: 0; }
.inv-section-head .ms-auto { font-size: .75rem; }
.inv-section-body { padding: 1.4rem; }
.inv-section-body.p-0 { padding: 0; }

/* ── Step Numbers ────────────────────────────────────────── */
.step-num {
    width: 26px; height: 26px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .72rem; font-weight: 800; flex-shrink: 0;
    background: #16a34a; color: #fff;
}

/* ── Customer & Date row ─────────────────────────────────── */
.cust-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    background: #fefce8; color: #854d0e; border: 1px solid #fde68a;
    border-radius: 50px; padding: .18rem .7rem; font-size: .73rem; font-weight: 600;
}
.cust-badge.linked { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }

/* ── Add Items Buttons ───────────────────────────────────── */
.add-item-strip {
    display: flex; flex-wrap: wrap; gap: .75rem;
}
.add-item-tile {
    flex: 1; min-width: 130px;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: .4rem; padding: 1rem 1.2rem;
    border-radius: 14px; border: 2px dashed;
    font-size: .8rem; font-weight: 700; cursor: pointer;
    background: transparent; transition: all .2s;
}
.add-item-tile .tile-icon { font-size: 1.6rem; }
.add-item-tile.product  { border-color: #6366f1; color: #4f46e5; }
.add-item-tile.product:hover  { background: #6366f1; color: #fff; border-style: solid; }
.add-item-tile.service  { border-color: #d97706; color: #b45309; }
.add-item-tile.service:hover  { background: #d97706; color: #fff; border-style: solid; }
.add-item-tile.rental   { border-color: #0d9488; color: #0f766e; }
.add-item-tile.rental:hover   { background: #0d9488; color: #fff; border-style: solid; }

/* ── Line Items Table ────────────────────────────────────── */
#lineItemsTable { font-size: .81rem; width: 100%; border-collapse: collapse; }
#lineItemsTable thead th {
    background: #f1f5f9; border-bottom: 2px solid #e2e8f0;
    color: #64748b; font-weight: 700; font-size: .7rem;
    text-transform: uppercase; letter-spacing: .05em;
    padding: .65rem .9rem; white-space: nowrap;
}
#lineItemsTable tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .12s; }
#lineItemsTable tbody tr:last-child { border-bottom: none; }
#lineItemsTable tbody tr:hover { background: #fafcff; }
#lineItemsTable td { padding: .6rem .9rem; vertical-align: middle; }
.type-pill {
    display: inline-block; font-size: .66rem; font-weight: 800;
    padding: .2rem .6rem; border-radius: 50px; white-space: nowrap;
}
.type-pill.product { background: #ede9fe; color: #4f46e5; }
.type-pill.service { background: #fef3c7; color: #b45309; }
.type-pill.rental  { background: #d1fae5; color: #065f46; }

.empty-items {
    padding: 3.5rem 1rem; text-align: center; color: #94a3b8;
    display: flex; flex-direction: column; align-items: center; gap: .75rem;
}
.empty-items .empty-icon { font-size: 3rem; opacity: .35; }
.empty-items p { margin: 0; font-size: .85rem; max-width: 280px; }

/* ── Right Summary Panel ─────────────────────────────────── */
.inv-summary-sticky { position: sticky; top: 75px; }

.summary-line {
    display: flex; justify-content: space-between; align-items: center;
    padding: .45rem 0; font-size: .83rem;
}
.summary-line .s-label { color: #64748b; }
.summary-line .s-val   { font-family: 'Courier New', monospace; font-weight: 600; color: #1e293b; }
.summary-line.grand {
    padding: .75rem 0; margin-top: .25rem;
    border-top: 2px solid #e2e8f0;
}
.summary-line.grand .s-label { font-weight: 700; color: #1e293b; font-size: .95rem; }
.summary-line.grand .s-val   { font-size: 1.2rem; color: #16a34a; font-weight: 800; }

.post-btn {
    background: linear-gradient(135deg, #14532d, #16a34a);
    color: #fff; border: none; border-radius: 12px;
    padding: .85rem 1rem; font-weight: 700; font-size: .95rem;
    width: 100%; cursor: pointer; transition: opacity .2s;
    box-shadow: 0 4px 16px rgba(22,163,74,.35); display: flex;
    align-items: center; justify-content: center; gap: .5rem;
}
.post-btn:hover { opacity: .9; }
.draft-btn {
    background: transparent; color: #64748b;
    border: 2px solid #e2e8f0; border-radius: 12px;
    padding: .7rem 1rem; font-weight: 600; font-size: .85rem;
    width: 100%; cursor: pointer; transition: all .2s;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
}
.draft-btn:hover { border-color: #94a3b8; color: #1e293b; }

/* ── Payment method toggle tabs ─────────────────────────── */
.pay-tabs { display: flex; gap: .5rem; flex-wrap: wrap; }
.pay-tab {
    flex: 1; min-width: 70px;
    border: 2px solid #e2e8f0; border-radius: 10px;
    background: #fff; cursor: pointer; padding: .6rem .5rem;
    display: flex; flex-direction: column; align-items: center; gap: .2rem;
    font-size: .7rem; font-weight: 700; color: #64748b; transition: all .2s;
}
.pay-tab i { font-size: 1.1rem; }
.pay-tab.active { border-color: #16a34a; background: #f0fdf4; color: #15803d; }
.pay-tab:hover:not(.active) { border-color: #94a3b8; color: #334155; }

/* ── Modals ─────────────────────────────────────────────── */
.modal-content { border-radius: 18px !important; border: 0 !important; overflow: hidden; }
.modal-header  { border-bottom: 0 !important; padding: 1.25rem 1.5rem !important; }
.modal-body    { padding: 1.1rem 1.5rem 1.5rem !important; }
.modal-search-wrap {
    position: relative; margin-bottom: .9rem;
}
.modal-search-wrap .search-icon {
    position: absolute; left: .85rem; top: 50%; transform: translateY(-50%);
    color: #94a3b8; font-size: .9rem; pointer-events: none;
}
.modal-search-wrap input {
    width: 100%; border: 1.5px solid #e2e8f0; border-radius: 50px;
    padding: .55rem .9rem .55rem 2.2rem;
    background: #f8fafc; font-size: .85rem; outline: none; transition: all .2s;
}
.modal-search-wrap input:focus { border-color: #16a34a; background: #fff; box-shadow: 0 0 0 3px rgba(22,163,74,.1); }
.modal-tbl { font-size: .8rem; width: 100%; border-collapse: collapse; }
.modal-tbl thead th {
    background: #f8fafc; color: #64748b; font-weight: 700;
    font-size: .7rem; text-transform: uppercase; letter-spacing: .05em;
    padding: .55rem .8rem; border-bottom: 2px solid #e2e8f0;
}
.modal-tbl tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .1s; }
.modal-tbl tbody tr:hover td { background: #f0fdf4; }
.modal-tbl td { padding: .55rem .8rem; vertical-align: middle; }
.modal-add-btn {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    border: none; cursor: pointer; font-size: .85rem; transition: transform .15s;
}
.modal-add-btn:hover { transform: scale(1.1); }
</style>

<!-- ═══ PAGE HEADER ════════════════════════════════════════ -->
<div class="inv-page-header">
    <div class="header-left">
        <div class="inv-icon"><i class="bi bi-receipt-cutoff"></i></div>
        <div>
            <h4>Create New Invoice</h4>
            <p>Compose a sales invoice — mix products, services and machinery rentals.</p>
        </div>
    </div>
    <a href="<?= \Core\Helper::baseUrl('modules/invoices'); ?>" class="inv-back-btn">
        <i class="bi bi-arrow-left"></i> Invoice Log
    </a>
</div>

<!-- ═══ FORM ═══════════════════════════════════════════════ -->
<form action="<?= \Core\Helper::baseUrl('modules/invoices/store'); ?>" method="POST" id="invoiceForm">
    <?= \Core\CSRF::getFormField(); ?>
    <input type="hidden" name="service_job_id"      id="service_job_id"      value="<?= htmlspecialchars($prefilled['service_job_id'] ?? ''); ?>">
    <input type="hidden" name="machinery_rental_id" id="machinery_rental_id" value="<?= htmlspecialchars($prefilled['machinery_rental_id'] ?? ''); ?>">
    <input type="hidden" name="warehouse_id"        id="warehouse_id"        value="<?= $defaultWarehouseId; ?>">

    <div class="row g-4">

        <!-- ════ LEFT COLUMN (main content) ════════════════ -->
        <div class="col-12 col-xl-8">

            <!-- ── STEP 1: Invoice Details ───────────────── -->
            <div class="inv-section">
                <div class="inv-section-head">
                    <span class="step-num">1</span>
                    <i class="bi bi-person-lines-fill text-primary"></i>
                    Invoice Details
                </div>
                <div class="inv-section-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="customer_id" class="form-label fw-semibold small text-muted text-uppercase mb-1">
                                Customer / Party
                                <span id="walkinIndicator" class="cust-badge ms-2">
                                    <i class="bi bi-person-walking"></i> Walk-in
                                </span>
                            </label>
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
                                            <?= htmlspecialchars($m['member_no']); ?> &mdash; <?= htmlspecialchars($m['full_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                            <div class="form-text text-warning-emphasis small d-none" id="creditWarning">
                                <i class="bi bi-exclamation-triangle-fill"></i> Credit sales require a registered customer.
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="invoice_date" class="form-label fw-semibold small text-muted text-uppercase mb-1">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="invoice_date" name="invoice_date" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Reference</label>
                            <input type="text" class="form-control form-control-sm" name="reference" placeholder="PO / Job Ref (optional)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── STEP 2: Add Items ─────────────────────── -->
            <div class="inv-section">
                <div class="inv-section-head">
                    <span class="step-num">2</span>
                    <i class="bi bi-cart-plus text-success"></i>
                    Add Line Items
                    <span class="ms-auto badge bg-secondary rounded-pill" id="itemCountBadge">0 items</span>
                </div>
                <div class="inv-section-body">
                    <!-- Add Item Tiles -->
                    <div class="add-item-strip mb-4">
                        <button type="button" class="add-item-tile product" data-bs-toggle="modal" data-bs-target="#productModal">
                            <span class="tile-icon"><i class="bi bi-box-seam"></i></span>
                            Add Product
                        </button>
                        <button type="button" class="add-item-tile service" data-bs-toggle="modal" data-bs-target="#serviceModal">
                            <span class="tile-icon"><i class="bi bi-gear-wide-connected"></i></span>
                            Add Service
                        </button>
                        <button type="button" class="add-item-tile rental" data-bs-toggle="modal" data-bs-target="#rentalModal">
                            <span class="tile-icon"><i class="bi bi-truck-flatbed"></i></span>
                            Add Rental
                        </button>
                    </div>

                    <!-- Line Items Table -->
                    <div class="table-responsive">
                        <table id="lineItemsTable">
                            <thead>
                                <tr>
                                    <th style="width:90px;">Type</th>
                                    <th>Description</th>
                                    <th style="width:75px;" class="text-center">Stock</th>
                                    <th style="width:140px;">Qty &amp; Unit</th>
                                    <th style="width:130px;">Unit Price</th>
                                    <th style="width:115px;" class="text-end">Total (LKR)</th>
                                    <th style="width:40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <!-- JS-injected rows -->
                            </tbody>
                        </table>
                    </div>
                    <div class="empty-items" id="emptyCartMsg">
                        <div class="empty-icon"><i class="bi bi-cart3"></i></div>
                        <p>No items added yet. Click the buttons above to start adding Products, Services or Rentals.</p>
                    </div>
                </div>
            </div>

            <!-- ── Notes ────────────────────────────────── -->
            <div class="inv-section">
                <div class="inv-section-head">
                    <i class="bi bi-chat-left-text text-secondary"></i>
                    Notes &amp; Remarks
                </div>
                <div class="inv-section-body">
                    <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2"
                        placeholder="Specific terms, delivery instructions, or remarks..."></textarea>
                </div>
            </div>

        </div><!-- /col-xl-8 -->

        <!-- ════ RIGHT COLUMN (sticky summary) ════════════ -->
        <div class="col-12 col-xl-4">
            <div class="inv-summary-sticky">

                <!-- ── Payment Method ────────────────────── -->
                <div class="inv-section">
                    <div class="inv-section-head">
                        <i class="bi bi-credit-card-2-front text-success"></i>
                        Payment Method
                    </div>
                    <div class="inv-section-body">
                        <!-- Stylish toggle tabs -->
                        <div class="pay-tabs mb-3" id="payTabs">
                            <button type="button" class="pay-tab active" data-value="CASH" onclick="selectPayTab(this)">
                                <i class="bi bi-cash-coin"></i> Cash
                            </button>
                            <button type="button" class="pay-tab" data-value="BANK" onclick="selectPayTab(this)">
                                <i class="bi bi-bank2"></i> Bank
                            </button>
                            <button type="button" class="pay-tab" data-value="CHEQUE" onclick="selectPayTab(this)">
                                <i class="bi bi-journal-check"></i> Cheque
                            </button>
                            <button type="button" class="pay-tab" data-value="CREDIT" id="creditTab" onclick="selectPayTab(this)">
                                <i class="bi bi-clock-history"></i> Credit
                            </button>
                        </div>
                        <input type="hidden" id="payment_type" name="payment_type" value="CASH">

                        <!-- Bank Section -->
                        <div id="bankAccountSection" style="display:none;">
                            <label for="bank_account_id" class="form-label fw-semibold small text-muted text-uppercase mb-1">Bank Account <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="bank_account_id" name="bank_account_id">
                                <?php foreach ($bankAccounts as $ba): ?>
                                    <option value="<?= $ba['id']; ?>"><?= htmlspecialchars($ba['bank_name']); ?> &mdash; <?= htmlspecialchars($ba['account_number']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Cheque Section -->
                        <div id="chequeDetailsSection" style="display:none;">
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label fw-semibold small mb-1">Cheque Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="cheque_number" name="cheque_number" placeholder="e.g. 012356">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small mb-1">Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="cheque_bank" name="cheque_bank" placeholder="e.g. BOC, Sampath">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small mb-1">Cheque Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-sm" id="cheque_date" name="cheque_date" value="<?= date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Invoice Summary ───────────────────── -->
                <div class="inv-section">
                    <div class="inv-section-head">
                        <i class="bi bi-calculator text-primary"></i>
                        Invoice Summary
                    </div>
                    <div class="inv-section-body">
                        <div class="summary-line">
                            <span class="s-label">Subtotal</span>
                            <span class="s-val" id="summarySubtotal">LKR 0.00</span>
                        </div>

                        <div class="row g-2 my-2">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Discount (%)</label>
                                <input type="number" step="0.01" min="0" max="100"
                                       class="form-control form-control-sm text-end font-monospace"
                                       id="discount_percent" value="0.00" oninput="calculateDiscountAmount()">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Discount (LKR)</label>
                                <input type="number" step="0.01" min="0"
                                       class="form-control form-control-sm text-end font-monospace"
                                       id="discount" name="discount" value="0.00" oninput="clearDiscountPercent(); calculateInvoiceTotal()">
                            </div>
                        </div>

                        <div class="summary-line grand">
                            <span class="s-label">Grand Total</span>
                            <span class="s-val" id="summaryTotal">LKR 0.00</span>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" name="action" value="post" class="post-btn" onclick="return validateInvoiceForm(event)">
                                <i class="bi bi-send-check-fill"></i> Post Invoice
                            </button>
                            <button type="submit" name="action" value="draft" class="draft-btn">
                                <i class="bi bi-cloud-arrow-up"></i> Save as Draft
                            </button>
                        </div>

                        <p class="text-muted small text-center mt-3 mb-0">
                            <i class="bi bi-shield-check text-success me-1"></i>
                            All amounts are recorded in <strong>LKR</strong>
                        </p>
                    </div>
                </div>

            </div>
        </div><!-- /col-xl-4 -->

    </div>
</form>


<!-- ═══════════════════════════════════════════════════════
     MODAL: ADD PRODUCT
     ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <div class="modal-header" style="background:linear-gradient(135deg,#312e81,#4f46e5);color:#fff;">
                <h5 class="modal-title fw-bold" id="productModalLabel">
                    <i class="bi bi-box-seam me-2"></i>Select Product
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-search-wrap">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="prodSearchInput" placeholder="Search by product name, SKU or category..." oninput="filterModalItems('PRODUCT', this.value)">
                </div>
                <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
                    <table class="modal-tbl align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">In Stock</th>
                                <th class="text-end">Base Price</th>
                                <th style="width:90px;">Qty</th>
                                <th style="width:120px;">Unit Price (LKR)</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="prodModalTableBody">
                            <?php foreach ($products as $p): ?>
                                <tr class="prod-row" data-search="<?= htmlspecialchars(strtolower($p['name_en'] . ' ' . ($p['sku'] ?? '') . ' ' . ($p['category_name'] ?? ''))); ?>">
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($p['name_en']); ?></div>
                                        <small class="text-muted font-monospace">SKU: <?= htmlspecialchars($p['sku'] ?? '-'); ?> &bull; <?= htmlspecialchars($p['category_name'] ?? 'General'); ?></small>
                                    </td>
                                    <td class="text-center fw-semibold font-monospace text-muted"><?= number_format($p['stocks'][$defaultWarehouseId] ?? 0, 2); ?></td>
                                    <td class="text-end font-monospace text-muted">LKR <?= number_format($p['default_selling_price'], 2); ?></td>
                                    <td><input type="number" step="1" min="1" class="form-control form-control-sm font-monospace modal-qty-input" value="1"></td>
                                    <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace modal-price-input" value="<?= number_format($p['default_selling_price'], 2, '.', ''); ?>"></td>
                                    <td class="text-center">
                                        <button type="button" class="modal-add-btn" style="background:#4f46e5;color:#fff;" onclick="addProductRowFromModal(<?= htmlspecialchars(json_encode($p)); ?>, this)">
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

<!-- ═══════════════════════════════════════════════════════
     MODAL: ADD SERVICE
     ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <div class="modal-header" style="background:linear-gradient(135deg,#92400e,#d97706);color:#fff;">
                <h5 class="modal-title fw-bold" id="serviceModalLabel">
                    <i class="bi bi-gear-wide-connected me-2"></i>Select Service
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-search-wrap">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="srvSearchInput" placeholder="Search by service name or code..." oninput="filterModalItems('SERVICE', this.value)">
                </div>
                <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
                    <table class="modal-tbl align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Unit</th>
                                <th class="text-end">Base Price</th>
                                <th style="width:90px;">Qty</th>
                                <th style="width:120px;">Unit Price (LKR)</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="srvModalTableBody">
                            <?php foreach ($services as $s): ?>
                                <tr class="srv-row" data-search="<?= htmlspecialchars(strtolower($s['service_name'] . ' ' . $s['service_code'])); ?>">
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($s['service_name']); ?></div>
                                        <small class="text-muted font-monospace">Code: <?= htmlspecialchars($s['service_code']); ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($s['unit']); ?></td>
                                    <td class="text-end font-monospace text-muted">LKR <?= number_format($s['default_price'], 2); ?></td>
                                    <td><input type="number" step="1" min="1" class="form-control form-control-sm font-monospace modal-qty-input" value="1"></td>
                                    <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace modal-price-input" value="<?= number_format($s['default_price'], 2, '.', ''); ?>"></td>
                                    <td class="text-center">
                                        <button type="button" class="modal-add-btn" style="background:#d97706;color:#fff;" onclick="addServiceRowFromModal(<?= htmlspecialchars(json_encode($s)); ?>, this)">
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

<!-- ═══════════════════════════════════════════════════════
     MODAL: ADD RENTAL / MACHINERY
     ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="rentalModal" tabindex="-1" aria-labelledby="rentalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <div class="modal-header" style="background:linear-gradient(135deg,#064e3b,#0d9488);color:#fff;">
                <h5 class="modal-title fw-bold" id="rentalModalLabel">
                    <i class="bi bi-truck-flatbed me-2"></i>Select Machinery / Rental Asset
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-search-wrap">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="rentalSearchInput" placeholder="Search by machine name, code or category..." oninput="filterModalItems('RENTAL', this.value)">
                </div>
                <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
                    <table class="modal-tbl align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Machine Details</th>
                                <th>Status</th>
                                <th class="text-end">Default Rate</th>
                                <th style="width:90px;">Qty</th>
                                <th style="width:120px;">Total Price (LKR)</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($machineryAssets as $m): ?>
                                <tr class="rental-row" data-search="<?= htmlspecialchars(strtolower((string)($m['machinery_name'] ?? '') . ' ' . (string)($m['machinery_code'] ?? '') . ' ' . (string)($m['category'] ?? ''))); ?>">
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($m['machinery_name']); ?></div>
                                        <small class="text-muted font-monospace">Code: <?= htmlspecialchars($m['machinery_code']); ?> &bull; Serial: <?= htmlspecialchars($m['serial_number'] ?: '-'); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill <?= ($m['status'] === 'AVAILABLE') ? 'bg-success' : (($m['status'] === 'RENTED') ? 'bg-warning text-dark' : 'bg-danger'); ?>">
                                            <?= htmlspecialchars($m['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end font-monospace text-muted">LKR <?= number_format($m['default_rental_rate'], 2); ?> / <?= htmlspecialchars($m['rental_unit']); ?></td>
                                    <td><input type="number" step="1" min="1" class="form-control form-control-sm font-monospace modal-machine-qty-input" value="1"></td>
                                    <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace modal-machine-price-input" value="<?= number_format($m['default_rental_rate'], 2, '.', ''); ?>"></td>
                                    <td class="text-center">
                                        <button type="button" class="modal-add-btn" style="background:#0d9488;color:#fff;" onclick="addMachineRowFromDirectory(<?= htmlspecialchars(json_encode($m)); ?>, this)">
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
const defaultServiceId   = <?= !empty($services) ? $services[0]['id'] : '0'; ?>;

/* ── Payment Tabs ──────────────────────────────────────── */
function selectPayTab(btn) {
    document.querySelectorAll('.pay-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    const val = btn.getAttribute('data-value');
    document.getElementById('payment_type').value = val;
    togglePaymentFields(val);
}

function togglePaymentFields(method) {
    if (!method) method = document.getElementById('payment_type').value;
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

/* ── Customer Change ───────────────────────────────────── */
function handleCustomerChange() {
    const sel             = document.getElementById('customer_id');
    const creditTab       = document.getElementById('creditTab');
    const walkinIndicator = document.getElementById('walkinIndicator');
    const isWalkin = (sel.value === '');

    walkinIndicator.style.display = isWalkin ? '' : 'none';

    if (isWalkin) {
        if (document.getElementById('payment_type').value === 'CREDIT') {
            document.querySelector('.pay-tab[data-value="CASH"]').click();
        }
        creditTab.disabled = true;
        creditTab.classList.remove('active');
    } else {
        creditTab.disabled = false;
    }

    const selected = sel.options[sel.selectedIndex];
    if (selected && selected.getAttribute('data-is-member') === '1') {
        if (confirm('This person is a society member. Apply 10% member discount?')) {
            document.getElementById('discount_percent').value = '10.00';
            calculateDiscountAmount();
        }
    }
}

/* ── Modal Search ──────────────────────────────────────── */
function filterModalItems(type, query) {
    query = query.toLowerCase();
    const map = { PRODUCT: '.prod-row', SERVICE: '.srv-row', RENTAL: '.rental-row' };
    document.querySelectorAll(map[type] || '').forEach(row => {
        const text = row.getAttribute('data-search') || '';
        row.style.setProperty('display', text.includes(query) ? '' : 'none', 'important');
    });
}

/* ── Item Counter ──────────────────────────────────────── */
function updateItemCount() {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    document.getElementById('itemCountBadge').textContent = rows.length + ' item' + (rows.length === 1 ? '' : 's');
    document.getElementById('emptyCartMsg').style.display = rows.length ? 'none' : '';
}

/* ── Add Product ───────────────────────────────────────── */
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
            <span class="type-pill product">PRODUCT</span>
            <input type="hidden" name="items[${rowCount}][item_type]" value="PRODUCT">
            <input type="hidden" name="items[${rowCount}][product_id]" value="${prod.id}">
        </td>
        <td>
            <div class="fw-semibold text-dark small">${htmlspecialchars(prod.name_en)}</div>
            <div class="text-muted" style="font-size:.71rem;font-family:monospace;">SKU: ${htmlspecialchars(prod.sku || '-')}</div>
            <input type="text" class="form-control form-control-sm mt-1" name="items[${rowCount}][description]" placeholder="Remarks (optional)" style="font-size:.74rem;">
        </td>
        <td class="text-center font-monospace fw-semibold text-muted" id="available_${rowCount}">${stock.toFixed(2)}</td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="1" min="1" class="form-control font-monospace qty-input" name="items[${rowCount}][quantity]" value="${qty}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
                <span class="input-group-text bg-light text-muted" style="font-size:.73rem;">${htmlspecialchars(prod.unit_code || 'Units')}</span>
            </div>
        </td>
        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace price-input" name="items[${rowCount}][unit_price]" value="${price.toFixed(2)}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();"></td>
        <td class="text-end fw-bold font-monospace text-dark row-total" id="rowtotal_${rowCount}">${total}</td>
        <td class="text-center"><button type="button" class="btn btn-sm text-danger p-1 border-0 rounded-circle" onclick="removeRow(${rowCount})" title="Remove"><i class="bi bi-x-circle-fill fs-5"></i></button></td>
    `;
    tbody.appendChild(tr);
    calculateInvoiceTotal(); updateItemCount();
    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
    row.querySelector('.modal-qty-input').value = '1';
    row.querySelector('.modal-price-input').value = parseFloat(prod.default_selling_price).toFixed(2);
    document.getElementById('prodSearchInput').value = '';
    filterModalItems('PRODUCT', '');
}

/* ── Add Service ───────────────────────────────────────── */
function addServiceRowFromModal(srv, btn) {
    const row   = btn.closest('tr');
    const qty   = parseInt(row.querySelector('.modal-qty-input').value) || 1;
    const price = parseFloat(row.querySelector('.modal-price-input').value) || parseFloat(srv.default_price);
    rowCount++;
    const tbody = document.getElementById('itemsTableBody');
    const tr    = document.createElement('tr');
    tr.id = `row_${rowCount}`;
    const total = (qty * price).toFixed(2);
    document.getElementById('service_job_id').value = srv.service_job_id || srv.id;
    tr.innerHTML = `
        <td>
            <span class="type-pill service">SERVICE</span>
            <input type="hidden" name="items[${rowCount}][item_type]" value="SERVICE">
            <input type="hidden" name="items[${rowCount}][service_id]" value="${srv.id}">
        </td>
        <td>
            <div class="fw-semibold text-dark small">${htmlspecialchars(srv.service_name)} <span class="font-monospace text-muted">(${htmlspecialchars(srv.service_code)})</span></div>
            <input type="text" class="form-control form-control-sm mt-1" name="items[${rowCount}][description]" value="${htmlspecialchars(srv.description || '')}" placeholder="Remarks (optional)" style="font-size:.74rem;">
        </td>
        <td class="text-center text-muted" id="available_${rowCount}">—</td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="1" min="1" class="form-control font-monospace qty-input" name="items[${rowCount}][quantity]" value="${qty}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
                <span class="input-group-text bg-light text-muted" style="font-size:.73rem;">${htmlspecialchars(srv.unit || 'Job')}</span>
            </div>
        </td>
        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace price-input" name="items[${rowCount}][unit_price]" value="${price.toFixed(2)}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();"></td>
        <td class="text-end fw-bold font-monospace text-dark row-total" id="rowtotal_${rowCount}">${total}</td>
        <td class="text-center"><button type="button" class="btn btn-sm text-danger p-1 border-0 rounded-circle" onclick="removeRow(${rowCount})" title="Remove"><i class="bi bi-x-circle-fill fs-5"></i></button></td>
    `;
    tbody.appendChild(tr);
    calculateInvoiceTotal(); updateItemCount();
    bootstrap.Modal.getInstance(document.getElementById('serviceModal')).hide();
    row.querySelector('.modal-qty-input').value = '1';
    row.querySelector('.modal-price-input').value = parseFloat(srv.default_price).toFixed(2);
    document.getElementById('srvSearchInput').value = '';
    filterModalItems('SERVICE', '');
}

/* ── Add Machine (from rental modal) ──────────────────── */
function addMachineRowFromDirectory(machine, btn) {
    const row   = btn.closest('tr');
    const qty   = parseInt(row.querySelector('.modal-machine-qty-input').value) || 1;
    const price = parseFloat(row.querySelector('.modal-machine-price-input').value) || parseFloat(machine.default_rental_rate);
    rowCount++;
    const tbody = document.getElementById('itemsTableBody');
    const tr    = document.createElement('tr');
    tr.id = `row_${rowCount}`;
    const total = (qty * price).toFixed(2);
    tr.innerHTML = `
        <td>
            <span class="type-pill rental">RENTAL</span>
            <input type="hidden" name="items[${rowCount}][item_type]" value="SERVICE">
            <input type="hidden" name="items[${rowCount}][service_id]" value="${defaultServiceId}">
        </td>
        <td>
            <div class="fw-semibold text-dark small">Rental: ${htmlspecialchars(machine.machinery_name)}</div>
            <div class="text-muted font-monospace" style="font-size:.71rem;">Code: ${htmlspecialchars(machine.machinery_code)} | Serial: ${htmlspecialchars(machine.serial_number || '-')}</div>
            <input type="text" class="form-control form-control-sm mt-1" name="items[${rowCount}][description]" value="Machinery Rental Billing" placeholder="Remarks (optional)" style="font-size:.74rem;">
        </td>
        <td class="text-center text-muted" id="available_${rowCount}">—</td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="1" min="1" class="form-control font-monospace qty-input" name="items[${rowCount}][quantity]" value="${qty}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
                <span class="input-group-text bg-light text-muted" style="font-size:.73rem;">${htmlspecialchars(machine.rental_unit || 'Hour')}</span>
            </div>
        </td>
        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace price-input" name="items[${rowCount}][unit_price]" value="${price.toFixed(2)}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();"></td>
        <td class="text-end fw-bold font-monospace text-dark row-total" id="rowtotal_${rowCount}">${total}</td>
        <td class="text-center"><button type="button" class="btn btn-sm text-danger p-1 border-0 rounded-circle" onclick="removeRow(${rowCount})" title="Remove"><i class="bi bi-x-circle-fill fs-5"></i></button></td>
    `;
    tbody.appendChild(tr);
    calculateInvoiceTotal(); updateItemCount();
    bootstrap.Modal.getInstance(document.getElementById('rentalModal')).hide();
    row.querySelector('.modal-machine-qty-input').value = '1';
    row.querySelector('.modal-machine-price-input').value = parseFloat(machine.default_rental_rate).toFixed(2);
    document.getElementById('rentalSearchInput').value = '';
    filterModalItems('RENTAL', '');
}

/* ── Legacy rental (from job) ──────────────────────────── */
function addRentalRowFromModal(rental, btn) {
    const row         = btn.closest('tr');
    const totalCharge = parseFloat(row.querySelector('.modal-price-input').value) || parseFloat(rental.total_charge);
    const qty         = parseInt(rental.quantity) || 1;
    const rate        = (totalCharge / qty);
    rowCount++;
    const tbody = document.getElementById('itemsTableBody');
    const tr    = document.createElement('tr');
    tr.id = `row_${rowCount}`;
    document.getElementById('machinery_rental_id').value = rental.id;
    tr.innerHTML = `
        <td>
            <span class="type-pill rental">RENTAL</span>
            <input type="hidden" name="items[${rowCount}][item_type]" value="SERVICE">
            <input type="hidden" name="items[${rowCount}][service_id]" value="${defaultServiceId}">
        </td>
        <td>
            <div class="fw-semibold text-dark small">${htmlspecialchars(rental.rental_number)}: Rental — ${htmlspecialchars(rental.machinery_name)}</div>
            <div class="text-muted font-monospace" style="font-size:.71rem;">Serial: ${htmlspecialchars(rental.serial_number || '-')}</div>
            <input type="text" class="form-control form-control-sm mt-1" name="items[${rowCount}][description]" value="Machinery Rental Invoice" placeholder="Remarks" style="font-size:.74rem;">
        </td>
        <td class="text-center text-muted" id="available_${rowCount}">—</td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="1" min="1" class="form-control font-monospace qty-input" name="items[${rowCount}][quantity]" value="${qty}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
                <span class="input-group-text bg-light text-muted" style="font-size:.73rem;">${htmlspecialchars(rental.rental_unit || 'Hour')}</span>
            </div>
        </td>
        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace price-input" name="items[${rowCount}][unit_price]" value="${rate.toFixed(2)}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();"></td>
        <td class="text-end fw-bold font-monospace text-dark row-total" id="rowtotal_${rowCount}">${totalCharge.toFixed(2)}</td>
        <td class="text-center"><button type="button" class="btn btn-sm text-danger p-1 border-0 rounded-circle" onclick="removeRow(${rowCount})" title="Remove"><i class="bi bi-x-circle-fill fs-5"></i></button></td>
    `;
    tbody.appendChild(tr);
    calculateInvoiceTotal(); updateItemCount();
    bootstrap.Modal.getInstance(document.getElementById('rentalModal')).hide();
    document.getElementById('rentalSearchInput').value = '';
    filterModalItems('RENTAL', '');
}

/* ── Calculations ──────────────────────────────────────── */
function removeRow(id) {
    const row = document.getElementById(`row_${id}`);
    if (row) { row.remove(); calculateInvoiceTotal(); updateItemCount(); }
}

function calculateRowTotal(id) {
    const row   = document.getElementById(`row_${id}`);
    const qty   = parseFloat(row.querySelector('.qty-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    document.getElementById(`rowtotal_${id}`).textContent = (qty * price).toFixed(2);
}

function calculateDiscountAmount() {
    let subtotal = 0;
    document.querySelectorAll('.row-total').forEach(el => { subtotal += parseFloat(el.textContent) || 0; });
    const pct = parseFloat(document.getElementById('discount_percent').value) || 0;
    document.getElementById('discount').value = (subtotal * (pct / 100)).toFixed(2);
    calculateInvoiceTotal();
}

function clearDiscountPercent() {
    document.getElementById('discount_percent').value = '0.00';
}

function calculateInvoiceTotal() {
    let subtotal = 0;
    document.querySelectorAll('.row-total').forEach(el => { subtotal += parseFloat(el.textContent) || 0; });
    const pct = parseFloat(document.getElementById('discount_percent').value) || 0;
    if (pct > 0) document.getElementById('discount').value = (subtotal * (pct / 100)).toFixed(2);
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const total    = Math.max(0, subtotal - discount);
    const fmt = n => 'LKR ' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summarySubtotal').textContent = fmt(subtotal);
    document.getElementById('summaryTotal').textContent    = fmt(total);
}

/* ── Validation ────────────────────────────────────────── */
function validateInvoiceForm(event) {
    const sel    = document.getElementById('customer_id');
    const method = document.getElementById('payment_type').value;
    if (sel.value === '' && method === 'CREDIT') {
        alert('Walk-in Customer cannot purchase on Credit.\nPlease select a registered Customer.');
        event.preventDefault(); return false;
    }
    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (rows.length === 0) {
        alert('Please add at least one item to the invoice before posting.');
        event.preventDefault(); return false;
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

/* ── Utility ───────────────────────────────────────────── */
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
