<?php
// Variables available from controller:
// $customers, $members, $staff, $selectedCustomerVal, $products, $services, $machineryAssets, 
// $cashAccounts, $bankAccounts, $defaultWarehouseId, $invoice, $editItems, $chequeData
?>

<style>
/* ═══════════════════════════════════════════════════════════
   AGRI PRO — Edit Invoice Panel
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

/* ── Item Cards (Mobile-first Modal Layout) ─────────────── */
.item-card {
    background: #fff;
    border: 1.5px solid #e8edf2;
    border-radius: 14px;
    overflow: hidden;
    transition: border-color .15s, box-shadow .15s;
}
.item-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
}
.item-card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: .75rem;
    padding: .85rem 1rem;
    background: #f8fafc;
    border-bottom: 1px solid #e8edf2;
}
.item-card-info { flex: 1; min-width: 0; }
.item-card-name {
    font-weight: 700;
    font-size: .88rem;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: .35rem;
}
.item-card-meta {
    display: flex; flex-wrap: wrap; gap: .35rem;
}
.item-meta-chip {
    display: inline-flex; align-items: center; gap: .25rem;
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 50px; padding: .15rem .55rem;
    font-size: .7rem; color: #64748b; white-space: nowrap;
}
.item-meta-chip.stock-chip { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
.item-card-price {
    text-align: right; flex-shrink: 0;
}
.item-card-price .price-label {
    font-size: .65rem; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em;
}
.item-card-price .price-val {
    font-size: .9rem; font-weight: 800; font-family: 'Courier New', monospace;
    color: #0f172a; white-space: nowrap;
}
.item-card-actions {
    display: flex;
    align-items: flex-end;
    gap: .65rem;
    padding: .75rem 1rem;
    flex-wrap: wrap;
}
.action-field {
    display: flex; flex-direction: column; gap: .25rem;
    flex: 1; min-width: 90px;
}
.action-field label {
    font-size: .68rem; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: .04em; margin: 0;
}
.item-add-btn {
    display: flex; align-items: center; gap: .35rem;
    padding: .45rem .9rem; border-radius: 10px;
    font-size: .8rem; font-weight: 700; border: none;
    cursor: pointer; white-space: nowrap;
    transition: opacity .15s, transform .1s;
    align-self: flex-end;
}
.item-add-btn:hover { opacity: .88; transform: scale(1.03); }
.product-add-btn { background: #4f46e5; color: #fff; }
.service-add-btn { background: #d97706; color: #fff; }
.rental-add-btn  { background: #0d9488; color: #fff; }

@media (min-width: 600px) {
    #prodCardGrid, #srvCardGrid, #rentalCardGrid {
        grid-template-columns: 1fr 1fr;
    }
}
@media (max-width: 599px) {
    .modal-dialog { margin: .5rem; }
    .item-card-top { flex-direction: column; gap: .5rem; }
    .item-card-price { text-align: left; }
    .item-card-actions { flex-direction: column; }
    .action-field { min-width: unset; width: 100%; }
    .item-add-btn { width: 100%; justify-content: center; padding: .65rem; }
}
</style>

<!-- ═══ PAGE HEADER ════════════════════════════════════════ -->
<div class="inv-page-header">
    <div class="header-left">
        <div class="inv-icon"><i class="bi bi-pencil-square"></i></div>
        <div>
            <h4>Edit Invoice <?= htmlspecialchars($invoice['invoice_number']); ?></h4>
            <p>Modify sales invoice — adjust customer, line items or payment details.</p>
        </div>
    </div>
    <a href="<?= \Core\Helper::baseUrl('modules/invoices'); ?>" class="inv-back-btn">
        <i class="bi bi-arrow-left"></i> Invoice Log
    </a>
</div>

<!-- ═══ FORM ═══════════════════════════════════════════════ -->
<form action="<?= \Core\Helper::baseUrl('modules/invoices/update'); ?>" method="POST" id="invoiceForm">
    <?= \Core\CSRF::getFormField(); ?>
    <input type="hidden" name="id" value="<?= $invoice['id']; ?>">
    <input type="hidden" name="warehouse_id" id="warehouse_id" value="<?= $defaultWarehouseId; ?>">

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
                            <select class="form-select form-select-sm select2-customer" id="customer_id" name="customer_id">
                                <option value="" <?= ($selectedCustomerVal === '') ? 'selected' : ''; ?>>-- Walk-in Customer (No Account) --</option>
                                <optgroup label="Registered Customers">
                                    <?php foreach ($customers as $c): ?>
                                        <?php $cval = (string)$c['id']; $sel = ($selectedCustomerVal === $cval) ? 'selected' : ''; ?>
                                        <option value="<?= $cval ?>" <?= $sel ?>>
                                            <?= htmlspecialchars($c['party_code']); ?> &mdash; <?= htmlspecialchars($c['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Society Members & Directors">
                                    <?php foreach ($members as $m): ?>
                                        <?php $mval = "M_" . $m['id']; $sel = ($selectedCustomerVal === $mval) ? 'selected' : ''; ?>
                                        <option value="<?= $mval ?>" <?= $sel ?> data-is-member="1">
                                            <?= htmlspecialchars($m['member_no']); ?> &mdash; <?= htmlspecialchars($m['full_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Staff (Internal Users)">
                                    <?php foreach ($staff as $s): ?>
                                        <?php $sval = "U_" . $s['id']; $sel = ($selectedCustomerVal === $sval) ? 'selected' : ''; ?>
                                        <option value="<?= $sval ?>" <?= $sel ?> data-is-user="1">
                                            @<?= htmlspecialchars($s['username']); ?> &mdash; <?= htmlspecialchars($s['full_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                            <div class="form-text text-warning-emphasis small d-none" id="creditWarning">
                                <i class="bi bi-exclamation-triangle-fill"></i> Credit sales require a registered customer.
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="custom_customer_name" class="form-label fw-semibold small text-muted text-uppercase mb-1">
                                Type Customer Name <small class="text-lowercase fw-normal text-muted">(Optional / One-time)</small>
                            </label>
                            <input type="text" class="form-control form-control-sm" id="custom_customer_name" name="custom_customer_name" placeholder="e.g. Mr. John Silva" value="<?= htmlspecialchars($customCustomerName ?? ''); ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="invoice_date" class="form-label fw-semibold small text-muted text-uppercase mb-1">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="invoice_date" name="invoice_date" value="<?= htmlspecialchars($invoice['invoice_date'] ?? date('Y-m-d')); ?>" required>
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
                        <button type="button" class="add-item-tile" style="background:#eef2ff; color:#3730a3; border-color:#c7d2fe;" onclick="addDirectAccountItem('MEMBER_FEE', 'Member Fee')">
                            <span class="tile-icon" style="background:#c7d2fe; color:#3730a3;"><i class="bi bi-person-badge"></i></span>
                            Member Fee
                        </button>
                        <button type="button" class="add-item-tile" style="background:#fff7ed; color:#9a3412; border-color:#ffedd5;" onclick="addDirectAccountItem('SHARE_CAPITAL', 'Share Capital')">
                            <span class="tile-icon" style="background:#ffedd5; color:#9a3412;"><i class="bi bi-bank"></i></span>
                            Share Capital
                        </button>
                        <button type="button" class="add-item-tile" style="background:#f0fdf4; color:#166534; border-color:#bbf7d0;" onclick="addDirectAccountItem('DONATION', 'Donation')">
                            <span class="tile-icon" style="background:#bbf7d0; color:#166534;"><i class="bi bi-heart-fill"></i></span>
                            Donations
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
                        placeholder="Specific terms, delivery instructions, or remarks..."><?= htmlspecialchars($invoice['notes'] ?? ''); ?></textarea>
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
                            <button type="button" class="pay-tab <?= ($invoice['payment_type'] === 'CASH') ? 'active' : ''; ?>" data-value="CASH" onclick="selectPayTab(this)">
                                <i class="bi bi-cash-coin"></i> Cash
                            </button>
                            <button type="button" class="pay-tab <?= ($invoice['payment_type'] === 'BANK') ? 'active' : ''; ?>" data-value="BANK" onclick="selectPayTab(this)">
                                <i class="bi bi-bank2"></i> Bank
                            </button>
                            <button type="button" class="pay-tab <?= ($invoice['payment_type'] === 'CHEQUE') ? 'active' : ''; ?>" data-value="CHEQUE" onclick="selectPayTab(this)">
                                <i class="bi bi-journal-check"></i> Cheque
                            </button>
                            <button type="button" class="pay-tab <?= ($invoice['payment_type'] === 'CREDIT') ? 'active' : ''; ?>" data-value="CREDIT" id="creditTab" onclick="selectPayTab(this)">
                                <i class="bi bi-clock-history"></i> Credit
                            </button>
                        </div>
                        <input type="hidden" id="payment_type" name="payment_type" value="<?= htmlspecialchars($invoice['payment_type']); ?>">

                        <!-- Cash Section -->
                        <div id="cashAccountSection" class="mb-3" style="<?= ($invoice['payment_type'] === 'CASH') ? 'display:block;' : 'display:none;'; ?>">
                            <label for="cash_account_id" class="form-label fw-semibold small text-muted text-uppercase mb-1">Cash Account / Drawer</label>
                            <select class="form-select form-select-sm" id="cash_account_id" name="cash_account_id">
                                <?php foreach ($cashAccounts as $ca): ?>
                                    <?php $sel = ($invoice['cash_account_id'] == $ca['id']) ? 'selected' : ''; ?>
                                    <option value="<?= $ca['id']; ?>" <?= $sel ?>><?= htmlspecialchars($ca['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Bank Section -->
                        <div id="bankAccountSection" class="mb-3" style="<?= ($invoice['payment_type'] === 'BANK') ? 'display:block;' : 'display:none;'; ?>">
                            <label for="bank_account_id" class="form-label fw-semibold small text-muted text-uppercase mb-1">Bank Account <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="bank_account_id" name="bank_account_id">
                                <?php foreach ($bankAccounts as $ba): ?>
                                    <?php $sel = ($invoice['bank_account_id'] == $ba['id']) ? 'selected' : ''; ?>
                                    <option value="<?= $ba['id']; ?>" <?= $sel ?>><?= htmlspecialchars($ba['bank_name']); ?> &mdash; <?= htmlspecialchars($ba['account_number'] ?? $ba['account_name'] ?? ''); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Cheque Section -->
                        <div id="chequeDetailsSection" style="<?= ($invoice['payment_type'] === 'CHEQUE') ? 'display:block;' : 'display:none;'; ?>">
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label fw-semibold small mb-1">Cheque Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="cheque_number" name="cheque_number" value="<?= htmlspecialchars($chequeData['cheque_number'] ?? ''); ?>" placeholder="e.g. 012356">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small mb-1">Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="cheque_bank" name="cheque_bank" value="<?= htmlspecialchars($chequeData['bank_name'] ?? ''); ?>" placeholder="e.g. BOC, Sampath">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small mb-1">Cheque Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-sm" id="cheque_date" name="cheque_date" value="<?= htmlspecialchars($chequeData['cheque_date'] ?? date('Y-m-d')); ?>">
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
                                       id="discount" name="discount" value="<?= number_format($invoice['discount'] ?? 0, 2, '.', ''); ?>" oninput="clearDiscountPercent(); calculateInvoiceTotal()">
                            </div>
                        </div>

                        <div class="summary-line grand">
                            <span class="s-label">Grand Total</span>
                            <span class="s-val" id="summaryTotal">LKR 0.00</span>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" name="action" value="post" class="post-btn" onclick="return validateInvoiceForm(event)">
                                <i class="bi bi-save"></i> Save Changes
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

<!-- MODALS -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content shadow-lg">
            <div class="modal-header" style="background:linear-gradient(135deg,#312e81,#4f46e5);color:#fff;padding:1rem 1.25rem !important;">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="bi bi-box-seam me-2"></i>Select Product
                    </h5>
                    <div style="font-size:.75rem;opacity:.75;margin-top:.15rem;">Tap a product to configure qty &amp; price, then add</div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:1rem !important;">
                <div class="position-relative mb-3">
                    <i class="bi bi-search position-absolute" style="left:.85rem;top:50%;transform:translateY(-50%);color:#94a3b8;"></i>
                    <input type="text" id="prodSearchInput" class="form-control rounded-pill" style="padding-left:2.4rem;font-size:.85rem;" placeholder="Search products..." oninput="filterModalItems('PRODUCT', this.value)">
                </div>
                <div id="prodCardGrid" style="max-height:65vh;overflow-y:auto;display:grid;grid-template-columns:1fr;gap:.65rem;">
                    <?php foreach ($products as $p): ?>
                    <div class="item-card prod-row" data-search="<?= htmlspecialchars(strtolower($p['name_en'] . ' ' . ($p['sku'] ?? '') . ' ' . ($p['category_name'] ?? ''))); ?>">
                        <div class="item-card-top">
                            <div class="item-card-info">
                                <div class="item-card-name"><?= htmlspecialchars($p['name_en']); ?></div>
                                <div class="item-card-meta">
                                    <span class="item-meta-chip"><i class="bi bi-upc-scan"></i> <?= htmlspecialchars($p['sku'] ?? '-'); ?></span>
                                    <span class="item-meta-chip"><i class="bi bi-tag"></i> <?= htmlspecialchars($p['category_name'] ?? 'General'); ?></span>
                                    <span class="item-meta-chip stock-chip"><i class="bi bi-archive"></i> Stock: <?= number_format($p['stocks'][$defaultWarehouseId] ?? 0, 2); ?></span>
                                </div>
                            </div>
                            <div class="item-card-price">
                                <div class="price-label">Base Price</div>
                                <div class="price-val">LKR <?= number_format($p['default_selling_price'], 2); ?></div>
                            </div>
                        </div>
                        <div class="item-card-actions">
                            <div class="action-field">
                                <label>Qty</label>
                                <input type="number" step="1" min="1" class="form-control form-control-sm font-monospace modal-qty-input" value="1">
                            </div>
                            <div class="action-field">
                                <label>Unit Price (LKR)</label>
                                <input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace modal-price-input" value="<?= number_format($p['default_selling_price'], 2, '.', ''); ?>">
                            </div>
                            <button type="button" class="item-add-btn product-add-btn" onclick="addProductRowFromModal(<?= htmlspecialchars(json_encode($p)); ?>, this)">
                                <i class="bi bi-plus-lg"></i> Add
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content shadow-lg">
            <div class="modal-header" style="background:linear-gradient(135deg,#92400e,#d97706);color:#fff;padding:1rem 1.25rem !important;">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="bi bi-gear-wide-connected me-2"></i>Select Service
                    </h5>
                    <div style="font-size:.75rem;opacity:.75;margin-top:.15rem;">Set the quantity and price, then add to invoice</div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:1rem !important;">
                <div class="position-relative mb-3">
                    <i class="bi bi-search position-absolute" style="left:.85rem;top:50%;transform:translateY(-50%);color:#94a3b8;"></i>
                    <input type="text" id="srvSearchInput" class="form-control rounded-pill" style="padding-left:2.4rem;font-size:.85rem;" placeholder="Search services..." oninput="filterModalItems('SERVICE', this.value)">
                </div>
                <div id="srvCardGrid" style="max-height:65vh;overflow-y:auto;display:grid;grid-template-columns:1fr;gap:.65rem;">
                    <?php foreach ($services as $s): ?>
                    <div class="item-card srv-row" data-search="<?= htmlspecialchars(strtolower($s['service_name'] . ' ' . $s['service_code'])); ?>">
                        <div class="item-card-top">
                            <div class="item-card-info">
                                <div class="item-card-name"><?= htmlspecialchars($s['service_name']); ?></div>
                                <div class="item-card-meta">
                                    <span class="item-meta-chip"><i class="bi bi-hash"></i> <?= htmlspecialchars($s['service_code']); ?></span>
                                    <span class="item-meta-chip"><i class="bi bi-rulers"></i> <?= htmlspecialchars($s['unit']); ?></span>
                                </div>
                            </div>
                            <div class="item-card-price">
                                <div class="price-label">Base Price</div>
                                <div class="price-val">LKR <?= number_format($s['default_price'], 2); ?></div>
                            </div>
                        </div>
                        <div class="item-card-actions">
                            <div class="action-field">
                                <label>Qty</label>
                                <input type="number" step="1" min="1" class="form-control form-control-sm font-monospace modal-qty-input" value="1">
                            </div>
                            <div class="action-field">
                                <label>Unit Price (LKR)</label>
                                <input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace modal-price-input" value="<?= number_format($s['default_price'], 2, '.', ''); ?>">
                            </div>
                            <button type="button" class="item-add-btn service-add-btn" onclick="addServiceRowFromModal(<?= htmlspecialchars(json_encode($s)); ?>, this)">
                                <i class="bi bi-plus-lg"></i> Add
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rentalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content shadow-lg">
            <div class="modal-header" style="background:linear-gradient(135deg,#064e3b,#0d9488);color:#fff;padding:1rem 1.25rem !important;">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="bi bi-truck-flatbed me-2"></i>Select Machinery / Rental
                    </h5>
                    <div style="font-size:.75rem;opacity:.75;margin-top:.15rem;">Set qty and rate, then add to invoice</div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:1rem !important;">
                <div class="position-relative mb-3">
                    <i class="bi bi-search position-absolute" style="left:.85rem;top:50%;transform:translateY(-50%);color:#94a3b8;"></i>
                    <input type="text" id="rentalSearchInput" class="form-control rounded-pill" style="padding-left:2.4rem;font-size:.85rem;" placeholder="Search machinery..." oninput="filterModalItems('RENTAL', this.value)">
                </div>
                <div id="rentalCardGrid" style="max-height:65vh;overflow-y:auto;display:grid;grid-template-columns:1fr;gap:.65rem;">
                    <?php foreach ($machineryAssets as $m): ?>
                    <div class="item-card rental-row" data-search="<?= htmlspecialchars(strtolower((string)($m['machinery_name'] ?? '') . ' ' . (string)($m['machinery_code'] ?? '') . ' ' . (string)($m['category'] ?? ''))); ?>">
                        <div class="item-card-top">
                            <div class="item-card-info">
                                <div class="item-card-name"><?= htmlspecialchars($m['machinery_name']); ?></div>
                                <div class="item-card-meta">
                                    <span class="item-meta-chip"><i class="bi bi-qr-code"></i> <?= htmlspecialchars($m['machinery_code']); ?></span>
                                    <span class="item-meta-chip"><i class="bi bi-fingerprint"></i> S/N: <?= htmlspecialchars($m['serial_number'] ?: '-'); ?></span>
                                    <span class="item-meta-chip">
                                        <span class="badge rounded-pill <?= ($m['status'] === 'AVAILABLE') ? 'bg-success' : (($m['status'] === 'RENTED') ? 'bg-warning text-dark' : 'bg-danger'); ?>" style="font-size:.65rem;">
                                            <?= htmlspecialchars($m['status']); ?>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="item-card-price">
                                <div class="price-label">Rate / <?= htmlspecialchars($m['rental_unit'] ?? 'Hour'); ?></div>
                                <div class="price-val">LKR <?= number_format($m['default_rental_rate'], 2); ?></div>
                            </div>
                        </div>
                        <div class="item-card-actions">
                            <div class="action-field">
                                <label><?= htmlspecialchars($m['rental_unit'] ?? 'Qty'); ?></label>
                                <input type="number" step="1" min="1" class="form-control form-control-sm font-monospace modal-machine-qty-input" value="1">
                            </div>
                            <div class="action-field">
                                <label>Price (LKR)</label>
                                <input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace modal-machine-price-input" value="<?= number_format($m['default_rental_rate'], 2, '.', ''); ?>">
                            </div>
                            <button type="button" class="item-add-btn rental-add-btn" onclick="addMachineRowFromDirectory(<?= htmlspecialchars(json_encode($m)); ?>, this)">
                                <i class="bi bi-plus-lg"></i> Add
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const availableProducts  = <?= json_encode($products); ?>;
const editItemsData      = <?= json_encode($editItems); ?>;
let rowCount = 0;
const defaultWarehouseId = <?= json_encode($defaultWarehouseId); ?>;
const defaultServiceId   = <?= !empty($services) ? $services[0]['id'] : '0'; ?>;

/* ── Load Existing Item Rows on Load ───────────────────── */
function loadExistingItem(item) {
    rowCount++;
    const tbody = document.getElementById('itemsTableBody');
    const tr = document.createElement('tr');
    tr.id = `row_${rowCount}`;

    const type = item.type || 'PRODUCT';
    const qty = parseFloat(item.quantity) || 1;
    const price = parseFloat(item.unit_price) || 0;
    const total = parseFloat(item.total) || (qty * price);
    const desc = item.description || '';

    if (type === 'PRODUCT') {
        const prodId = item.product_id;
        const prod = availableProducts.find(p => p.id == prodId) || {};
        const stock = prod.stocks && prod.stocks[defaultWarehouseId] !== undefined ? parseFloat(prod.stocks[defaultWarehouseId]) : 0;
        const prodName = item.product_name || prod.name_en || 'Product';
        const sku = item.sku || prod.sku || '-';
        const unit = item.product_unit || prod.unit_code || 'Units';

        tr.innerHTML = `
            <td>
                <span class="type-pill product">PRODUCT</span>
                <input type="hidden" name="items[${rowCount}][item_type]" value="PRODUCT">
                <input type="hidden" name="items[${rowCount}][product_id]" value="${prodId}">
            </td>
            <td>
                <div class="fw-semibold text-dark small">${htmlspecialchars(prodName)}</div>
                <div class="text-muted" style="font-size:.71rem;font-family:monospace;">SKU: ${htmlspecialchars(sku)}</div>
                <input type="text" class="form-control form-control-sm mt-1" name="items[${rowCount}][description]" value="${htmlspecialchars(desc)}" placeholder="Remarks (optional)" style="font-size:.74rem;">
            </td>
            <td class="text-center font-monospace fw-semibold text-muted" id="available_${rowCount}">${stock.toFixed(2)}</td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" step="1" min="1" class="form-control font-monospace qty-input" name="items[${rowCount}][quantity]" value="${qty}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
                    <span class="input-group-text bg-light text-muted" style="font-size:.73rem;">${htmlspecialchars(unit)}</span>
                </div>
            </td>
            <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace price-input" name="items[${rowCount}][unit_price]" value="${price.toFixed(2)}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();"></td>
            <td class="text-end fw-bold font-monospace text-dark row-total" id="rowtotal_${rowCount}">${total.toFixed(2)}</td>
            <td class="text-center"><button type="button" class="btn btn-sm text-danger p-1 border-0 rounded-circle" onclick="removeRow(${rowCount})" title="Remove"><i class="bi bi-x-circle-fill fs-5"></i></button></td>
        `;
    } else if (type === 'SERVICE') {
        const srvId = item.service_id;
        const srvName = item.service_name || 'Service';
        const srvCode = item.service_code || '';
        const unit = item.service_unit || 'Job';

        tr.innerHTML = `
            <td>
                <span class="type-pill service">SERVICE</span>
                <input type="hidden" name="items[${rowCount}][item_type]" value="SERVICE">
                <input type="hidden" name="items[${rowCount}][service_id]" value="${srvId}">
            </td>
            <td>
                <div class="fw-semibold text-dark small">${htmlspecialchars(srvName)} ${srvCode ? '<span class="font-monospace text-muted">(' + htmlspecialchars(srvCode) + ')</span>' : ''}</div>
                <input type="text" class="form-control form-control-sm mt-1" name="items[${rowCount}][description]" value="${htmlspecialchars(desc)}" placeholder="Remarks (optional)" style="font-size:.74rem;">
            </td>
            <td class="text-center text-muted" id="available_${rowCount}">—</td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" step="1" min="1" class="form-control font-monospace qty-input" name="items[${rowCount}][quantity]" value="${qty}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
                    <span class="input-group-text bg-light text-muted" style="font-size:.73rem;">${htmlspecialchars(unit)}</span>
                </div>
            </td>
            <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace price-input" name="items[${rowCount}][unit_price]" value="${price.toFixed(2)}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();"></td>
            <td class="text-end fw-bold font-monospace text-dark row-total" id="rowtotal_${rowCount}">${total.toFixed(2)}</td>
            <td class="text-center"><button type="button" class="btn btn-sm text-danger p-1 border-0 rounded-circle" onclick="removeRow(${rowCount})" title="Remove"><i class="bi bi-x-circle-fill fs-5"></i></button></td>
        `;
    } else {
        let label = (type === 'SHARE_CAPITAL') ? 'Share Capital' : ((type === 'DONATION') ? 'Donation' : 'Member Fee');
        let pillClass = (type === 'SHARE_CAPITAL') ? 'text-bg-warning' : ((type === 'DONATION') ? 'text-bg-success' : 'text-bg-info');

        let qtyHtml = `
            <div class="input-group input-group-sm">
                <input type="number" step="1" min="1" class="form-control font-monospace qty-input" name="items[${rowCount}][quantity]" value="${qty}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();">
                <span class="input-group-text bg-light text-muted" style="font-size:.73rem;">Unit</span>
            </div>
        `;
        if (type === 'SHARE_CAPITAL' || type === 'MEMBER_FEE' || type === 'DONATION') {
            qtyHtml = `
                <div class="text-center text-muted pt-1">—</div>
                <input type="hidden" class="qty-input" name="items[${rowCount}][quantity]" value="1">
            `;
        }

        tr.innerHTML = `
            <td>
                <span class="badge ${pillClass} bg-opacity-10 text-dark fw-bold rounded-pill" style="font-size:0.65rem; padding:0.35rem 0.6rem;">${label.toUpperCase()}</span>
                <input type="hidden" name="items[${rowCount}][item_type]" value="${type}">
            </td>
            <td>
                <div class="fw-semibold text-dark small">${label}</div>
                <input type="text" class="form-control form-control-sm mt-1" name="items[${rowCount}][description]" value="${htmlspecialchars(desc)}" placeholder="Remarks (optional)" style="font-size:.74rem;">
            </td>
            <td class="text-center text-muted" id="available_${rowCount}">—</td>
            <td>${qtyHtml}</td>
            <td><input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace price-input" name="items[${rowCount}][unit_price]" value="${price.toFixed(2)}" required oninput="calculateRowTotal(${rowCount}); calculateInvoiceTotal();" placeholder="Enter Value"></td>
            <td class="text-end fw-bold font-monospace text-dark row-total" id="rowtotal_${rowCount}">${total.toFixed(2)}</td>
            <td class="text-center"><button type="button" class="btn btn-sm text-danger p-1 border-0 rounded-circle" onclick="removeRow(${rowCount})" title="Remove"><i class="bi bi-x-circle-fill fs-5"></i></button></td>
        `;
    }

    tbody.appendChild(tr);
}

/* ── Payment Tabs ──────────────────────────────────────── */
function selectPayTab(btn) {
    const val = btn.getAttribute('data-value');
    const sel = document.getElementById('customer_id');

    if (val === 'CREDIT' && sel.value === '') {
        alert('Please select a registered Customer first to use Credit payment.');
        if ($('.select2-customer').length) {
            $('.select2-customer').select2('open');
        } else {
            sel.focus();
        }
        return;
    }

    document.querySelectorAll('.pay-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('payment_type').value = val;
    togglePaymentFields(val);
}

function togglePaymentFields(method) {
    if (!method) method = document.getElementById('payment_type').value;
    const cashSect   = document.getElementById('cashAccountSection');
    const bankSect   = document.getElementById('bankAccountSection');
    const chequeSect = document.getElementById('chequeDetailsSection');

    if (cashSect) cashSect.style.display = (method === 'CASH') ? 'block' : 'none';
    if (bankSect) bankSect.style.display = (method === 'BANK') ? 'block' : 'none';
    if (chequeSect) chequeSect.style.display = (method === 'CHEQUE') ? 'block' : 'none';

    const bankSelect = document.getElementById('bank_account_id');
    const chqNum = document.getElementById('cheque_number');
    const chqBank = document.getElementById('cheque_bank');

    if (bankSelect) bankSelect.required = (method === 'BANK');
    if (chqNum) chqNum.required = (method === 'CHEQUE');
    if (chqBank) chqBank.required = (method === 'CHEQUE');
}

/* ── Customer Change ───────────────────────────────────── */
let lastDiscountConfirm = null;
function handleCustomerChange() {
    const sel             = document.getElementById('customer_id');
    const creditTab       = document.getElementById('creditTab');
    const walkinIndicator = document.getElementById('walkinIndicator');
    const isWalkin = (sel.value === '');

    if (walkinIndicator) walkinIndicator.style.display = isWalkin ? '' : 'none';

    if (isWalkin) {
        if (document.getElementById('payment_type').value === 'CREDIT') {
            document.querySelector('.pay-tab[data-value="CASH"]').click();
        }
        if (creditTab) {
            creditTab.disabled = true;
            creditTab.classList.remove('active');
        }
    } else {
        if (creditTab) creditTab.disabled = false;
    }

    const selected = sel.options[sel.selectedIndex];
    if (selected && selected.getAttribute('data-is-member') === '1') {
        if (lastDiscountConfirm === sel.value) return;
        lastDiscountConfirm = sel.value;
        if (confirm('This person is a society member. Apply 10% member discount?')) {
            document.getElementById('discount_percent').value = '10.00';
            calculateDiscountAmount();
        }
    } else {
        lastDiscountConfirm = sel.value;
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

/* ── Modal Add Handlers ────────────────────────────────── */
function addProductRowFromModal(prod, btn) {
    const row   = btn.closest('.item-card');
    const qty   = parseInt(row.querySelector('.modal-qty-input').value) || 1;
    const price = parseFloat(row.querySelector('.modal-price-input').value) || parseFloat(prod.default_selling_price);
    
    loadExistingItem({
        type: 'PRODUCT',
        product_id: prod.id,
        product_name: prod.name_en,
        sku: prod.sku,
        product_unit: prod.unit_code,
        quantity: qty,
        unit_price: price,
        total: qty * price,
        description: ''
    });

    calculateInvoiceTotal(); updateItemCount();
    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
    row.querySelector('.modal-qty-input').value = '1';
    row.querySelector('.modal-price-input').value = parseFloat(prod.default_selling_price).toFixed(2);
    document.getElementById('prodSearchInput').value = '';
    filterModalItems('PRODUCT', '');
}

function addServiceRowFromModal(srv, btn) {
    const row   = btn.closest('.item-card');
    const qty   = parseInt(row.querySelector('.modal-qty-input').value) || 1;
    const price = parseFloat(row.querySelector('.modal-price-input').value) || parseFloat(srv.default_price);

    loadExistingItem({
        type: 'SERVICE',
        service_id: srv.id,
        service_name: srv.service_name,
        service_code: srv.service_code,
        service_unit: srv.unit,
        quantity: qty,
        unit_price: price,
        total: qty * price,
        description: srv.description || ''
    });

    calculateInvoiceTotal(); updateItemCount();
    bootstrap.Modal.getInstance(document.getElementById('serviceModal')).hide();
    row.querySelector('.modal-qty-input').value = '1';
    row.querySelector('.modal-price-input').value = parseFloat(srv.default_price).toFixed(2);
    document.getElementById('srvSearchInput').value = '';
    filterModalItems('SERVICE', '');
}

function addDirectAccountItem(type, label) {
    loadExistingItem({
        type: type,
        description: label,
        quantity: 1,
        unit_price: 0,
        total: 0
    });
    calculateInvoiceTotal(); updateItemCount();
}

function addMachineRowFromDirectory(machine, btn) {
    const row   = btn.closest('.item-card');
    const qty   = parseInt(row.querySelector('.modal-machine-qty-input').value) || 1;
    const price = parseFloat(row.querySelector('.modal-machine-price-input').value) || parseFloat(machine.default_rental_rate);

    loadExistingItem({
        type: 'SERVICE',
        service_id: defaultServiceId,
        service_name: 'Rental: ' + machine.machinery_name,
        service_code: machine.machinery_code,
        service_unit: machine.rental_unit || 'Hour',
        quantity: qty,
        unit_price: price,
        total: qty * price,
        description: 'Machinery Rental Billing'
    });

    calculateInvoiceTotal(); updateItemCount();
    bootstrap.Modal.getInstance(document.getElementById('rentalModal')).hide();
    row.querySelector('.modal-machine-qty-input').value = '1';
    row.querySelector('.modal-machine-price-input').value = parseFloat(machine.default_rental_rate).toFixed(2);
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
    if (!row) return;
    const qty   = parseFloat(row.querySelector('.qty-input')?.value) || 0;
    const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
    const totEl = document.getElementById(`rowtotal_${id}`);
    if (totEl) totEl.textContent = (qty * price).toFixed(2);
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
        alert('Please add at least one item to the invoice before saving.');
        event.preventDefault(); return false;
    }
    return true;
}

/* ── Utility ───────────────────────────────────────────── */
function htmlspecialchars(str) {
    if (typeof str !== 'string') return '';
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.addEventListener('DOMContentLoaded', () => {
    const custSelect = document.getElementById('customer_id');
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        $('.select2-customer').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: "-- Walk-in Customer (No Account) --",
            allowClear: true
        });
        $('.select2-customer').on('change', function() {
            handleCustomerChange();
        });
    } else if (custSelect) {
        custSelect.addEventListener('change', handleCustomerChange);
    }

    handleCustomerChange();
    togglePaymentFields();

    // Populate existing invoice line items
    if (Array.isArray(editItemsData) && editItemsData.length > 0) {
        editItemsData.forEach(item => loadExistingItem(item));
        calculateInvoiceTotal();
        updateItemCount();
    }
});
</script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
