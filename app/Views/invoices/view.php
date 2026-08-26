<?php if ($flashSuccess = \Core\Session::getFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3 d-print-none" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($flashError = \Core\Session::getFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3 d-print-none" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<style>
/* ═══════════════════════════════════════════════════════
   INVOICE VIEW — Screen + Print Styles
   ═══════════════════════════════════════════════════════ */

/* ── Screen Action Bar ──────────────────────────────── */
.inv-view-bar {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: .75rem;
    background: linear-gradient(135deg,#0f4c2a,#166534,#15803d);
    border-radius: 16px; padding: 1.1rem 1.5rem;
    margin-bottom: 1.5rem; color: #fff;
    box-shadow: 0 6px 24px rgba(15,76,42,.2);
}
.inv-view-bar .bar-left { display: flex; align-items: center; gap: .85rem; }
.inv-view-bar .inv-icon {
    width: 46px; height: 46px; background: rgba(255,255,255,.15);
    border-radius: 12px; display: flex; align-items: center;
    justify-content: center; font-size: 1.35rem; flex-shrink: 0;
}
.inv-view-bar h5 { margin: 0; font-weight: 800; font-size: 1.05rem; }
.inv-view-bar .sub  { margin: 0; font-size: .78rem; opacity: .72; }
.inv-view-bar .bar-right { display: flex; gap: .6rem; flex-wrap: wrap; }
.bar-btn {
    display: flex; align-items: center; gap: .35rem;
    padding: .45rem 1rem; border-radius: 50px; font-size: .8rem; font-weight: 600;
    cursor: pointer; text-decoration: none; border: 1.5px solid;
    transition: all .18s;
}
.bar-btn.ghost { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.3); color: #fff; }
.bar-btn.ghost:hover { background: rgba(255,255,255,.22); color: #fff; }
.bar-btn.print-btn { background: #fff; border-color: #fff; color: #15803d; }
.bar-btn.print-btn:hover { background: #f0fdf4; }
.bar-btn.post-btn-s { background: #22c55e; border-color: #22c55e; color: #fff; }
.bar-btn.post-btn-s:hover { background: #16a34a; }
.bar-btn.danger-btn { background: rgba(239,68,68,.15); border-color: rgba(239,68,68,.5); color: #fca5a5; }
.bar-btn.danger-btn:hover { background: rgba(239,68,68,.3); color: #fff; }

/* ── Status Badge ───────────────────────────────────── */
.inv-status-pill {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .25rem .8rem; border-radius: 50px; font-size: .72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em;
}
.inv-status-pill.posted   { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.inv-status-pill.draft    { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
.inv-status-pill.cancelled{ background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

/* ── Screen Layout ──────────────────────────────────── */
.inv-main-card {
    background: #fff; border-radius: 18px;
    border: 1px solid #e8edf2;
    box-shadow: 0 3px 20px rgba(0,0,0,.07);
    overflow: hidden;
}
.inv-side-card {
    background: #fff; border-radius: 14px;
    border: 1px solid #e8edf2;
    box-shadow: 0 2px 10px rgba(0,0,0,.05);
    overflow: hidden; margin-bottom: 1rem;
}
.side-card-head {
    padding: .7rem 1.1rem;
    background: #f8fafb; border-bottom: 1px solid #e8edf2;
    font-size: .73rem; font-weight: 700; color: #475569;
    text-transform: uppercase; letter-spacing: .05em;
    display: flex; align-items: center; gap: .45rem;
}
.side-card-body { padding: 1rem 1.1rem; font-size: .82rem; }
.side-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: .3rem 0; color: #64748b;
}
.side-row .s-val { font-weight: 600; color: #1e293b; font-family: monospace; }
.grand-side {
    border-top: 2px solid #e2e8f0; margin-top: .5rem; padding-top: .7rem;
    display: flex; justify-content: space-between; align-items: baseline;
}
.grand-side .g-label { font-weight: 700; color: #0f172a; font-size: .9rem; }
.grand-side .g-val   { font-weight: 900; color: #16a34a; font-size: 1.2rem; font-family: monospace; }

/* ── Invoice Document ───────────────────────────────── */
.inv-doc { padding: 2rem 2.5rem; }
@media (max-width: 576px) { .inv-doc { padding: 1.25rem; } }

/* Company header */
.doc-company-header { text-align: center; padding-bottom: 1.25rem; margin-bottom: 1.25rem; border-bottom: 2px solid #e2e8f0; }
.doc-company-header .co-name-si { font-size: 1.2rem; font-weight: 800; color: #14532d; line-height: 1.3; margin-bottom: .2rem; }
.doc-company-header .co-name-en { font-size: .88rem; color: #64748b; font-weight: 600; margin-bottom: .35rem; }
.doc-company-header .co-meta    { font-size: .75rem; color: #94a3b8; margin-bottom: .35rem; }
.doc-company-header .co-reg     { display: inline-block; background: #f0fdf4; border: 1px solid #86efac; color: #166534; border-radius: 50px; padding: .18rem .75rem; font-size: .7rem; font-weight: 700; }

/* Invoice meta row */
.inv-meta-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: .6rem 1.2rem; margin-bottom: 1.1rem; }
@media (min-width: 576px) { .inv-meta-grid { grid-template-columns: repeat(4, 1fr); } }
.meta-block .meta-lbl { font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #94a3b8; margin-bottom: .1rem; }
.meta-block .meta-val { font-size: .85rem; font-weight: 700; color: #1e293b; }
.meta-block .meta-val.mono { font-family: monospace; }

/* Customer block */
.inv-customer-block {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 10px; padding: .75rem 1rem; margin-bottom: 1.1rem;
    display: flex; align-items: center; gap: .75rem;
}
.inv-customer-block .cust-icon {
    width: 38px; height: 38px; background: #dcfce7; color: #16a34a;
    border-radius: 10px; display: flex; align-items: center; justify-content: center;
    font-size: 1.15rem; flex-shrink: 0;
}
.inv-customer-block .cust-label { font-size: .65rem; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; }
.inv-customer-block .cust-name  { font-size: .95rem; font-weight: 800; color: #0f172a; }
.inv-customer-block .cust-code  { font-size: .73rem; color: #64748b; font-family: monospace; }

/* Items table */
.inv-items-head {
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: #64748b; margin-bottom: .5rem;
    padding-bottom: .4rem; border-bottom: 2px solid #e2e8f0;
    display: flex; gap: .5rem;
}
.inv-item-row {
    display: flex; gap: .5rem; align-items: flex-start;
    padding: .55rem 0; border-bottom: 1px solid #f1f5f9; font-size: .8rem;
}
.inv-item-row:last-child { border-bottom: none; }
.col-item  { flex: 1; min-width: 0; }
.col-qty   { width: 55px; text-align: center; flex-shrink: 0; }
.col-price { width: 90px; text-align: right; flex-shrink: 0; }
.col-total { width: 100px; text-align: right; flex-shrink: 0; font-weight: 700; }
.item-name  { font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.item-sub   { font-size: .68rem; color: #94a3b8; font-family: monospace; }
.item-desc  { font-size: .7rem; color: #64748b; font-style: italic; margin-top: .1rem; }
.type-dot {
    display: inline-block; width: 7px; height: 7px;
    border-radius: 50%; margin-right: .3rem; vertical-align: middle;
}
.type-dot.product { background: #6366f1; }
.type-dot.service { background: #f59e0b; }

/* Totals block */
.inv-totals {
    display: flex; justify-content: flex-end; margin-top: 1rem;
}
.totals-box { width: 100%; max-width: 260px; }
.totals-box .t-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: .25rem 0; font-size: .8rem; color: #64748b;
}
.totals-box .t-row .t-val { font-family: monospace; font-weight: 600; color: #1e293b; }
.totals-box .t-grand {
    border-top: 2px solid #0f172a; margin-top: .4rem; padding-top: .6rem;
    display: flex; justify-content: space-between; align-items: baseline;
}
.totals-box .t-grand .g-l { font-size: .9rem; font-weight: 800; color: #0f172a; }
.totals-box .t-grand .g-v { font-size: 1.1rem; font-weight: 900; color: #16a34a; font-family: monospace; }

/* Notes */
.inv-notes { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: .7rem .9rem; margin-top: 1rem; font-size: .78rem; color: #78350f; }
.inv-notes strong { display: block; margin-bottom: .25rem; font-size: .68rem; text-transform: uppercase; letter-spacing: .06em; }

/* Footer */
.inv-doc-footer { margin-top: 1.5rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0; display: flex; justify-content: space-between; align-items: flex-end; font-size: .7rem; color: #94a3b8; }
.sig-line { width: 140px; border-top: 1px solid #64748b; padding-top: .3rem; text-align: center; font-size: .68rem; color: #64748b; }

/* Cancelled watermark */
.cancelled-overlay {
    border: 3px solid #ef4444; border-radius: 8px; padding: .65rem 1rem;
    background: #fff5f5; margin-top: 1rem;
}
.cancelled-overlay h6 { color: #dc2626; font-weight: 800; margin-bottom: .5rem; }

/* ═══════════════════════════════════════════════════════
   PRINT STYLES — Compact, paper-saving
   ═══════════════════════════════════════════════════════ */
@media print {
    @page { margin: 8mm 10mm; size: A4; }
    body, html { font-size: 10pt !important; background: #fff !important; }

    /* Hide everything not needed */
    .d-print-none, .inv-view-bar, .inv-side-card,
    nav, header, footer, .sidebar, aside,
    .alert { display: none !important; }

    /* Full width print */
    .col-lg-9, .col-12 { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; }
    .row { display: block !important; }

    /* Remove shadows/borders for print */
    .inv-main-card { border: none !important; box-shadow: none !important; border-radius: 0 !important; }
    .inv-doc { padding: 0 !important; }

    /* Compact company header */
    .doc-company-header { padding-bottom: 4pt !important; margin-bottom: 6pt !important; }
    .doc-company-header .co-name-si { font-size: 11pt !important; }
    .doc-company-header .co-name-en { font-size: 8pt !important; }
    .doc-company-header .co-meta    { font-size: 7.5pt !important; }

    /* Compact meta grid */
    .inv-meta-grid { margin-bottom: 6pt !important; gap: 2pt 10pt !important; }
    .meta-block .meta-lbl { font-size: 6.5pt !important; }
    .meta-block .meta-val { font-size: 8.5pt !important; }

    /* Customer block compact */
    .inv-customer-block { padding: 4pt 8pt !important; margin-bottom: 6pt !important; }
    .inv-customer-block .cust-name { font-size: 9pt !important; }

    /* Items compact */
    .inv-items-head { font-size: 6pt !important; padding-bottom: 2pt !important; }
    .inv-item-row   { padding: 3pt 0 !important; font-size: 8pt !important; }
    .item-name      { font-size: 8.5pt !important; }
    .item-sub       { font-size: 6.5pt !important; }
    .col-qty   { width: 40pt !important; }
    .col-price { width: 65pt !important; }
    .col-total { width: 70pt !important; }

    /* Totals compact */
    .inv-totals { margin-top: 5pt !important; }
    .totals-box .t-row  { padding: 1.5pt 0 !important; font-size: 8pt !important; }
    .totals-box .t-grand .g-l { font-size: 9pt !important; }
    .totals-box .t-grand .g-v { font-size: 10pt !important; }

    /* Footer */
    .inv-doc-footer { margin-top: 10pt !important; font-size: 7pt !important; }
    .sig-line { font-size: 7pt !important; }

    /* Notes */
    .inv-notes { padding: 3pt 6pt !important; font-size: 7.5pt !important; margin-top: 5pt !important; }
}
</style>

<!-- ═══ ACTION BAR (screen only) ══════════════════════════ -->
<div class="inv-view-bar d-print-none">
    <div class="bar-left">
        <div class="inv-icon"><i class="bi bi-receipt-cutoff"></i></div>
        <div>
            <h5><?= htmlspecialchars($invoice['invoice_number']); ?>
                <span class="inv-status-pill <?= strtolower($invoice['status']); ?> ms-2">
                    <i class="bi bi-<?= $invoice['status'] === 'POSTED' ? 'check-circle-fill' : ($invoice['status'] === 'DRAFT' ? 'clock' : 'x-circle-fill'); ?>"></i>
                    <?= htmlspecialchars($invoice['status']); ?>
                </span>
            </h5>
            <p class="sub">Invoice Date: <?= htmlspecialchars($invoice['invoice_date']); ?> &bull; <?= htmlspecialchars($invoice['customer_name']); ?></p>
        </div>
    </div>
    <div class="bar-right">
        <a href="<?= \Core\Helper::baseUrl('modules/invoices'); ?>" class="bar-btn ghost">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <a href="<?= \Core\Helper::baseUrl('modules/invoices/create'); ?>" class="bar-btn ghost">
            <i class="bi bi-plus-lg"></i> New
        </a>
        <button onclick="window.print()" class="bar-btn print-btn">
            <i class="bi bi-printer-fill"></i> Print
        </button>

        <?php if ($invoice['status'] === 'DRAFT' && \Core\Auth::hasPermission('invoices.post')): ?>
            <?php if ($invoice['payment_type'] === 'CHEQUE'): ?>
                <button type="button" class="bar-btn post-btn-s" data-bs-toggle="modal" data-bs-target="#postChequeModal">
                    <i class="bi bi-send-fill"></i> Post Invoice
                </button>
            <?php else: ?>
                <form action="<?= \Core\Helper::baseUrl('modules/invoices/post'); ?>" method="POST" class="d-inline">
                    <?= \Core\CSRF::getFormField(); ?>
                    <input type="hidden" name="id" value="<?= $invoice['id']; ?>">
                    <button type="submit" class="bar-btn post-btn-s">
                        <i class="bi bi-send-fill"></i> Post Invoice
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($invoice['status'] === 'POSTED' && \Core\Auth::hasPermission('invoices.cancel')): ?>
            <button class="bar-btn danger-btn" data-bs-toggle="modal" data-bs-target="#cancelInvoiceModal">
                <i class="bi bi-arrow-counterclockwise"></i> Reverse
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- ═══ MAIN LAYOUT ════════════════════════════════════════ -->
<div class="row g-4">

    <!-- ── Invoice Document ────────────────────────────── -->
    <div class="col-12 col-lg-9">
        <div class="inv-main-card">
            <div class="inv-doc">

                <!-- Company Header -->
                <div class="doc-company-header">
                    <div class="co-name-si">සීමා සහිත ඇග්‍රි කෝප් සමූපකාර සමිතිය</div>
                    <div class="co-name-en">Agri Co-Op Cooperative Society Limited</div>
                    <div class="co-meta">Miduma, Yatagama, Rambukkana</div>
                    <div class="co-meta" style="margin-bottom:.5rem;">
                        <i class="bi bi-telephone-fill" style="color:#16a34a;font-size:.7rem;"></i>
                        075-3770145 &bull; 070-6296150 &bull; 071-8211010 &bull; 071-8460172 &bull; 071-8028774
                    </div>
                    <span class="co-reg">Reg. No: KE/1027</span>
                </div>

                <!-- Invoice Meta -->
                <div class="inv-meta-grid">
                    <div class="meta-block">
                        <div class="meta-lbl">Invoice No.</div>
                        <div class="meta-val mono"><?= htmlspecialchars($invoice['invoice_number']); ?></div>
                    </div>
                    <div class="meta-block">
                        <div class="meta-lbl">Date</div>
                        <div class="meta-val"><?= htmlspecialchars($invoice['invoice_date']); ?></div>
                    </div>
                    <div class="meta-block">
                        <div class="meta-lbl">Payment</div>
                        <div class="meta-val"><?= htmlspecialchars($invoice['payment_type']); ?></div>
                    </div>
                    <div class="meta-block">
                        <div class="meta-lbl">Status</div>
                        <div class="meta-val">
                            <span class="inv-status-pill <?= strtolower($invoice['status']); ?>">
                                <?= htmlspecialchars($invoice['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Customer -->
                <div class="inv-customer-block">
                    <div class="cust-icon"><i class="bi bi-person-fill"></i></div>
                    <div>
                        <div class="cust-label">Invoiced To</div>
                        <div class="cust-name"><?= htmlspecialchars($invoice['customer_name']); ?></div>
                        <div class="cust-code"><?= htmlspecialchars($invoice['party_code']); ?></div>
                    </div>
                </div>

                <!-- Items -->
                <div class="inv-items-head">
                    <div class="col-item">Item / Description</div>
                    <div class="col-qty">Qty</div>
                    <div class="col-price">Unit Price</div>
                    <div class="col-total">Total</div>
                </div>

                <?php foreach ($invoice['items'] as $item): ?>
                    <div class="inv-item-row">
                        <div class="col-item">
                            <div class="item-name">
                                <span class="type-dot <?= strtolower($item['item_type']); ?>"></span>
                                <?php if ($item['item_type'] === 'PRODUCT'): ?>
                                    <?= htmlspecialchars($item['product_name']); ?>
                                    <span class="item-sub"><?= htmlspecialchars($item['sku']); ?></span>
                                <?php else: ?>
                                    <?= htmlspecialchars($item['service_name']); ?>
                                    <span class="item-sub"><?= htmlspecialchars($item['service_code']); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($item['description'])): ?>
                                <div class="item-desc"><?= htmlspecialchars($item['description']); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-qty text-center">
                            <?= number_format($item['quantity'], 2); ?>
                            <div style="font-size:.65rem;color:#94a3b8;"><?= htmlspecialchars($item['item_type'] === 'PRODUCT' ? $item['product_unit'] : $item['service_unit']); ?></div>
                        </div>
                        <div class="col-price">LKR <?= number_format($item['unit_price'], 2); ?></div>
                        <div class="col-total">LKR <?= number_format($item['total'], 2); ?></div>
                    </div>
                <?php endforeach; ?>

                <!-- Totals -->
                <div class="inv-totals">
                    <div class="totals-box">
                        <div class="t-row">
                            <span>Subtotal</span>
                            <span class="t-val">LKR <?= number_format($invoice['subtotal'], 2); ?></span>
                        </div>
                        <?php if ($invoice['discount'] > 0): ?>
                        <div class="t-row">
                            <span>Discount</span>
                            <span class="t-val" style="color:#ef4444;">- LKR <?= number_format($invoice['discount'], 2); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="t-grand">
                            <span class="g-l">Grand Total</span>
                            <span class="g-v">LKR <?= number_format($invoice['total'], 2); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <?php if (!empty($invoice['notes'])): ?>
                    <div class="inv-notes">
                        <strong><i class="bi bi-chat-left-text me-1"></i> Notes / Remarks</strong>
                        <?= nl2br(htmlspecialchars($invoice['notes'])); ?>
                    </div>
                <?php endif; ?>

                <!-- Cancelled overlay -->
                <?php if ($invoice['status'] === 'CANCELLED'): ?>
                    <div class="cancelled-overlay">
                        <h6><i class="bi bi-exclamation-octagon-fill me-2"></i>Invoice Cancelled / Reversed</h6>
                        <div style="font-size:.8rem;">
                            <strong>Reversal Journal:</strong>
                            <span class="font-monospace"><?= htmlspecialchars($invoice['reversal_journal_number'] ?: '-'); ?></span>
                        </div>
                        <div style="font-size:.8rem;margin-top:.4rem;">
                            <strong>Reason:</strong>
                            <?= nl2br(htmlspecialchars($invoice['reversal_reason'])); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Footer (signatures + generated notice) -->
                <div class="inv-doc-footer">
                    <div>
                        <div style="font-size:.72rem;color:#64748b;margin-bottom:.6rem;">
                            Printed: <?= date('Y-m-d H:i'); ?> &bull;
                            Prepared by: <?= htmlspecialchars($invoice['creator_name'] ?? 'System'); ?>
                        </div>
                        <div style="font-size:.68rem;color:#94a3b8;font-style:italic;">
                            This is a computer-generated invoice. No physical signature required.
                        </div>
                    </div>
                    <div class="d-flex gap-5">
                        <div class="sig-line">Issued By</div>
                        <div class="sig-line">Received By</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ── Right Side Panel (screen only) ─────────────── -->
    <div class="col-12 col-lg-3 d-print-none">

        <!-- Totals -->
        <div class="inv-side-card">
            <div class="side-card-head"><i class="bi bi-calculator text-primary"></i> Invoice Summary</div>
            <div class="side-card-body">
                <div class="side-row">
                    <span>Subtotal</span>
                    <span class="s-val">LKR <?= number_format($invoice['subtotal'], 2); ?></span>
                </div>
                <div class="side-row">
                    <span>Discount</span>
                    <span class="s-val" style="color:#ef4444;">- LKR <?= number_format($invoice['discount'], 2); ?></span>
                </div>
                <div class="grand-side">
                    <span class="g-label">Grand Total</span>
                    <span class="g-val">LKR <?= number_format($invoice['total'], 2); ?></span>
                </div>
            </div>
        </div>

        <!-- Profitability -->
        <?php if ($invoice['status'] === 'POSTED'): ?>
        <div class="inv-side-card">
            <div class="side-card-head"><i class="bi bi-pie-chart text-success"></i> Profitability</div>
            <div class="side-card-body">
                <div class="side-row">
                    <span>Revenue</span>
                    <span class="s-val" style="color:#16a34a;">LKR <?= number_format($invoice['total'], 2); ?></span>
                </div>
                <div class="side-row">
                    <span>COGS</span>
                    <span class="s-val" style="color:#ef4444;">LKR <?= number_format($totalCogs, 2); ?></span>
                </div>
                <div class="grand-side">
                    <span class="g-label">Gross Profit</span>
                    <span class="g-val" style="font-size:.95rem;">LKR <?= number_format($grossProfit, 2); ?></span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Audit -->
        <div class="inv-side-card">
            <div class="side-card-head"><i class="bi bi-shield-check text-secondary"></i> Audit Info</div>
            <div class="side-card-body">
                <div class="side-row">
                    <span>Status</span>
                    <span class="inv-status-pill <?= strtolower($invoice['status']); ?> s-val"><?= htmlspecialchars($invoice['status']); ?></span>
                </div>
                <div class="side-row">
                    <span>Journal</span>
                    <span class="s-val"><?= htmlspecialchars($invoice['journal_number'] ?: '-'); ?></span>
                </div>
                <div class="side-row">
                    <span>Created By</span>
                    <span class="s-val"><?= htmlspecialchars($invoice['creator_name'] ?? 'System'); ?></span>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     MODAL: Post Invoice (Cheque)
     ══════════════════════════════════════════════════════ -->
<div class="modal fade" id="postChequeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
            <div class="modal-header text-white" style="background:linear-gradient(135deg,#14532d,#16a34a);border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold"><i class="bi bi-journal-check me-2"></i>Cheque Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/invoices/post'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $invoice['id']; ?>">
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">Enter the received cheque details. The cheque will be automatically registered in the cheque registry.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Cheque Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="cheque_number" required placeholder="e.g. 102040">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="cheque_bank" required placeholder="e.g. Sampath Bank">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Cheque Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="cheque_date" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius:0 0 16px 16px;">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4" style="background:#16a34a;border-color:#16a34a;">
                        <i class="bi bi-send-fill me-1"></i> Post Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     MODAL: Cancel / Reverse Invoice
     ══════════════════════════════════════════════════════ -->
<div class="modal fade" id="cancelInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
            <div class="modal-header text-white bg-danger" style="border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-counterclockwise me-2"></i>Reverse Invoice</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= \Core\Helper::baseUrl('modules/invoices/cancel'); ?>" method="POST">
                <?= \Core\CSRF::getFormField(); ?>
                <input type="hidden" name="id" value="<?= $invoice['id']; ?>">
                <div class="modal-body p-4">
                    <div class="alert alert-warning rounded-3 small">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Reversing this invoice will perform a full ledger reversal, restore inventory quantities, and refund any credit balance allocations.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Reason for Reversal <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reversal_reason" rows="3" placeholder="State the reason for reversal..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius:0 0 16px 16px;">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Execute Reversal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
