<?php
/**
 * Universal Executive Print Preview & PDF Export Template
 * Agri Co-Op Cooperative Society Limited (KE/1027)
 */

$companyNameEn = $company['company_name_en'] ?? 'Agri Co-Op Cooperative Society Limited';
$companyNameSi = $company['company_name_si'] ?? 'සීමා සහිත ඇග්රි කෝප් සමූපකාර සමිතිය';
$addressEn = $company['address_en'] ?? 'Miduma, Yatagama, Rambukkana';
$addressSi = $company['address_si'] ?? 'මීදූම, යටගම, රඹුක්කන';
$regNoEn = $company['reg_no_en'] ?? 'KE/1027';
$regNoSi = $company['reg_no_si'] ?? 'කෑ/1027';
$contacts = implode(' / ', array_slice($company['contact_numbers'] ?? ['075 377 0 145', '070 629 61 50'], 0, 3));

$reportType = $reportType ?? 'report';
$reportTitle = $reportTitle ?? 'Financial & Operational Report';
$reportSubtitle = $reportSubtitle ?? 'Cooperative Management Statement';
$orientation = $orientation ?? 'landscape'; // 'landscape' or 'portrait'
$generatedBy = $generatedBy ?? 'System Officer';
$generatedAt = $generatedAt ?? date('Y-m-d H:i:s');
$periodLabel = $periodLabel ?? ('As of ' . date('Y-m-d'));
$scopeLabel = $scopeLabel ?? 'All Divisions / Society Wide';
$backUrl = $backUrl ?? \Core\Helper::baseUrl('reports');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($reportTitle); ?> - <?= date('Ymd_His'); ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            color: #0f172a;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        /* Screen Toolbar */
        .toolbar {
            max-width: <?= $orientation === 'landscape' ? '1180px' : '900px'; ?>;
            margin: 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
        }

        /* Document Container */
        .document-container {
            max-width: <?= $orientation === 'landscape' ? '1180px' : '900px'; ?>;
            margin: 0 auto 30px auto;
            background: #ffffff;
            padding: 30px 35px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border-radius: 6px;
        }

        /* Official Society Letterhead */
        .society-header {
            border-bottom: 2.5px solid #15803d;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .society-title-si {
            font-size: 17px;
            font-weight: 700;
            color: #15803d;
            line-height: 1.2;
        }

        .society-title-en {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .society-sub {
            font-size: 11px;
            color: #475569;
            margin-top: 2px;
        }

        .report-badge {
            background-color: #15803d;
            color: #ffffff;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: inline-block;
            border-radius: 3px;
            text-transform: uppercase;
        }

        /* Metadata Strip */
        .meta-strip {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 18px;
            font-size: 11px;
        }

        /* Standardized High-Precision Printable Table */
        .print-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-bottom: 20px;
        }

        .print-table th {
            background-color: #1e293b !important;
            color: #ffffff !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5px;
            padding: 6px 8px;
            border: 1px solid #0f172a;
            vertical-align: middle;
            letter-spacing: 0.3px;
        }

        .print-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
            line-height: 1.3;
        }

        .print-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .print-table tfoot td, .print-table tfoot th {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-weight: 700;
            padding: 7px 8px;
            border: 1px solid #0f172a;
        }

        .num-cell {
            text-align: right;
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        }

        /* Triple Endorsement Signature Block */
        .signature-block {
            margin-top: 35px;
            padding-top: 15px;
            border-top: 1px dashed #cbd5e1;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-top: 1.5px dotted #64748b;
            margin: 40px auto 6px auto;
            width: 85%;
        }

        .signature-title {
            font-weight: 700;
            font-size: 10.5px;
            color: #1e293b;
        }

        .signature-sub {
            font-size: 9.5px;
            color: #64748b;
        }

        /* Print Media Styles */
        @media print {
            @page {
                size: A4 <?= $orientation; ?>;
                margin: 8mm 10mm;
            }

            body {
                background-color: #ffffff !important;
                font-size: 10px !important;
                color: #000000 !important;
            }

            .no-print, .toolbar {
                display: none !important;
            }

            .document-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            .society-header {
                border-bottom: 2pt solid #000000 !important;
            }

            .report-badge {
                background-color: #000000 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .meta-strip {
                background-color: #f8fafc !important;
                border: 1pt solid #64748b !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print-table {
                width: 100% !important;
                font-size: 9.5px !important;
            }

            .print-table th {
                background-color: #1e293b !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-size: 9px !important;
                padding: 4px 6px !important;
                border: 0.5pt solid #000000 !important;
            }

            .print-table td {
                padding: 3.5px 6px !important;
                font-size: 9px !important;
                border: 0.5pt solid #64748b !important;
            }

            .print-table tfoot td, .print-table tfoot th {
                background-color: #000000 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .signature-block {
                margin-top: 25px !important;
            }

            tr {
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Toolbar (Screen Only) -->
    <div class="toolbar no-print">
        <div>
            <a href="<?= htmlspecialchars($backUrl); ?>" class="btn btn-outline-secondary btn-sm me-2 shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to Report View
            </a>
            <span class="badge bg-secondary font-monospace"><?= strtoupper($orientation); ?> A4</span>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= \Core\Helper::baseUrl('reports/export?' . http_build_query($_GET)); ?>" class="btn btn-outline-success btn-sm font-weight-bold shadow-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel (.xls)
            </a>
            <button onclick="window.print()" class="btn btn-danger btn-sm font-weight-bold shadow-sm">
                <i class="bi bi-printer-fill me-1"></i> Save as PDF / Print Document
            </button>
        </div>
    </div>

    <!-- Official Printable Document -->
    <div class="document-container">
        
        <!-- Society Letterhead -->
        <div class="society-header d-flex justify-content-between align-items-start">
            <div>
                <div class="society-title-si"><?= htmlspecialchars($companyNameSi); ?></div>
                <div class="society-title-en"><?= htmlspecialchars($companyNameEn); ?></div>
                <div class="society-sub">
                    <span><?= htmlspecialchars($addressEn); ?></span> | 
                    <span>ලියාපදිංචි අංකය: <?= htmlspecialchars($regNoSi); ?> (Reg: <?= htmlspecialchars($regNoEn); ?>)</span>
                </div>
                <div class="society-sub text-muted">
                    <span>දුරකථන / Tel: <?= htmlspecialchars($contacts); ?></span>
                </div>
            </div>
            <div class="text-end">
                <div class="report-badge mb-1"><?= htmlspecialchars($reportTitle); ?></div>
                <div class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 0.5px;">
                    <?= htmlspecialchars($reportSubtitle); ?>
                </div>
            </div>
        </div>

        <!-- Metadata Strip -->
        <div class="meta-strip">
            <div class="row g-2 align-items-center">
                <div class="col-7">
                    <div><strong>Period / As of:</strong> <span class="text-dark fw-bold"><?= htmlspecialchars($periodLabel); ?></span></div>
                    <div><strong>Reporting Scope:</strong> <?= htmlspecialchars($scopeLabel); ?></div>
                    <div><strong>Reporting Currency:</strong> Sri Lankan Rupees (LKR)</div>
                </div>
                <div class="col-5 text-end">
                    <div><strong>Generated At:</strong> <?= htmlspecialchars($generatedAt); ?></div>
                    <div><strong>Generated By:</strong> <?= htmlspecialchars($generatedBy); ?></div>
                    <div><strong>Document Status:</strong> <span class="text-success fw-bold">Official Management Copy</span></div>
                </div>
            </div>
        </div>

        <!-- Dynamic Report Body Based on $reportType -->
        <?php if ($reportType === 'trial_balance'): ?>
            <?php 
                $tbRows = $data ?? [];
                $totDebit = 0; $totCredit = 0; $totNetDebit = 0; $totNetCredit = 0;
                foreach ($tbRows as $r) {
                    $totDebit += (float)$r['total_debit'];
                    $totCredit += (float)$r['total_credit'];
                    $totNetDebit += (float)($r['net_debit'] ?? 0);
                    $totNetCredit += (float)($r['net_credit'] ?? 0);
                }
                $isBalanced = abs($totDebit - $totCredit) < 0.01;
            ?>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Code</th>
                        <th style="width: 28%;">Account Name</th>
                        <th style="width: 12%;">Category</th>
                        <th style="width: 12%; text-align: right;">Cumulative Debit</th>
                        <th style="width: 12%; text-align: right;">Cumulative Credit</th>
                        <th style="width: 13%; text-align: right;">Net Debit (LKR)</th>
                        <th style="width: 13%; text-align: right;">Net Credit (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tbRows)): ?>
                        <tr><td colspan="7" class="text-center py-3 text-muted">No accounts posted for this period.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tbRows as $row): ?>
                            <?php $isSub = !empty($row['parent_id']) && in_array((string)($row['parent_code'] ?? ''), ['1210', '4100', '4200']); ?>
                            <tr>
                                <td class="font-monospace text-muted"><?= $isSub ? '&nbsp;&nbsp;↳ ' : ''; ?><?= htmlspecialchars($row['account_code']); ?></td>
                                <td class="<?= $isSub ? 'ps-3' : 'fw-bold'; ?>">
                                    <?= htmlspecialchars($row['account_name']); ?>
                                    <?php if ($isSub): ?>
                                        <small style="font-size: 8px; color: #64748b; font-weight: normal;">(Child of <?= htmlspecialchars($row['parent_code']); ?>)</small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['category']); ?></td>
                                <td class="num-cell"><?= number_format((float)$row['total_debit'], 2); ?></td>
                                <td class="num-cell"><?= number_format((float)$row['total_credit'], 2); ?></td>
                                <td class="num-cell fw-bold"><?= (float)($row['net_debit'] ?? 0) > 0 ? number_format((float)$row['net_debit'], 2) : '-'; ?></td>
                                <td class="num-cell fw-bold"><?= (float)($row['net_credit'] ?? 0) > 0 ? number_format((float)$row['net_credit'], 2) : '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-uppercase">GRAND TOTALS (<?= count($tbRows); ?> ACCOUNTS)</td>
                        <td class="num-cell"><?= number_format($totDebit, 2); ?></td>
                        <td class="num-cell"><?= number_format($totCredit, 2); ?></td>
                        <td class="num-cell text-warning"><?= number_format($totNetDebit, 2); ?></td>
                        <td class="num-cell text-warning"><?= number_format($totNetCredit, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
            <div class="mb-3 p-2 border rounded <?= $isBalanced ? 'bg-light text-success border-success' : 'bg-light text-danger border-danger'; ?>" style="font-size: 10px;">
                <strong>Verification Status:</strong> <?= $isBalanced ? '✓ TRIAL BALANCE IS BALANCED. Total Debits strictly equal Total Credits.' : '⚠ IMBALANCE DETECTED. Net Difference: LKR ' . number_format(abs($totDebit - $totCredit), 2); ?>
            </div>

        <?php elseif ($reportType === 'profit_loss'): ?>
            <?php $pnl = $data ?? ['revenues' => [], 'cogs' => [], 'expenses' => [], 'total_revenue' => 0, 'total_cogs' => 0, 'gross_profit' => 0, 'total_expenses' => 0, 'net_profit' => 0]; ?>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Account Code</th>
                        <th style="width: 55%;">Account Title / Classification</th>
                        <th style="width: 30%; text-align: right;">Amount (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background-color: #e2e8f0; font-weight: bold;"><td colspan="3">1. OPERATING REVENUE & INCOME</td></tr>
                    <?php foreach ($pnl['revenues'] as $r): ?>
                        <tr>
                            <td class="font-monospace text-muted"><?= htmlspecialchars($r['account_code']); ?></td>
                            <td class="ps-3"><?= htmlspecialchars($r['account_name']); ?></td>
                            <td class="num-cell"><?= number_format($r['net_amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold" style="background-color: #f1f5f9;"><td colspan="2" class="text-end">Total Operating Revenue:</td><td class="num-cell text-success"><?= number_format($pnl['total_revenue'], 2); ?></td></tr>

                    <tr style="background-color: #e2e8f0; font-weight: bold;"><td colspan="3">2. COST OF GOODS SOLD (COGS)</td></tr>
                    <?php foreach ($pnl['cogs'] as $c): ?>
                        <tr>
                            <td class="font-monospace text-muted"><?= htmlspecialchars($c['account_code']); ?></td>
                            <td class="ps-3"><?= htmlspecialchars($c['account_name']); ?></td>
                            <td class="num-cell"><?= number_format($c['net_amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold" style="background-color: #f1f5f9;"><td colspan="2" class="text-end">Total Cost of Sales:</td><td class="num-cell text-danger"><?= number_format($pnl['total_cogs'], 2); ?></td></tr>

                    <tr class="fw-bold table-primary" style="font-size: 11px;"><td colspan="2" class="text-uppercase">GROSS OPERATING SURPLUS / (DEFICIT)</td><td class="num-cell"><?= number_format($pnl['gross_profit'], 2); ?></td></tr>

                    <tr style="background-color: #e2e8f0; font-weight: bold;"><td colspan="3">3. ADMINISTRATIVE & OPERATIONAL OVERHEADS</td></tr>
                    <?php foreach ($pnl['expenses'] as $e): ?>
                        <tr>
                            <td class="font-monospace text-muted"><?= htmlspecialchars($e['account_code']); ?></td>
                            <td class="ps-3"><?= htmlspecialchars($e['account_name']); ?></td>
                            <td class="num-cell"><?= number_format($e['net_amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold" style="background-color: #f1f5f9;"><td colspan="2" class="text-end">Total Operating Expenses:</td><td class="num-cell text-danger"><?= number_format($pnl['total_expenses'], 2); ?></td></tr>
                </tbody>
                <tfoot>
                    <tr style="font-size: 12px;">
                        <td colspan="2" class="text-uppercase py-2">NET OPERATING SURPLUS / (DEFICIT) FOR THE PERIOD</td>
                        <td class="num-cell py-2 text-warning font-weight-bold">LKR <?= number_format($pnl['net_profit'], 2); ?></td>
                    </tr>
                </tfoot>
            </table>

        <?php elseif ($reportType === 'balance_sheet'): ?>
            <?php $bs = $data ?? ['assets' => [], 'liabilities' => [], 'equity' => [], 'total_assets' => 0, 'total_liabilities' => 0, 'total_equity' => 0, 'current_earnings' => 0, 'total_liabilities_and_equity' => 0]; ?>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Account Code</th>
                        <th style="width: 55%;">Account Title / Classification</th>
                        <th style="width: 30%; text-align: right;">Net Balance (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background-color: #e2e8f0; font-weight: bold;"><td colspan="3">ASSETS (CURRENT & NON-CURRENT)</td></tr>
                    <?php foreach ($bs['assets'] as $a): ?>
                        <tr>
                            <td class="font-monospace text-muted"><?= htmlspecialchars($a['account_code']); ?></td>
                            <td class="ps-3 fw-bold"><?= htmlspecialchars($a['account_name']); ?></td>
                            <td class="num-cell"><?= number_format($a['net_balance'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold table-light"><td colspan="2" class="text-end text-uppercase">TOTAL ASSETS</td><td class="num-cell text-primary fw-bold"><?= number_format($bs['total_assets'], 2); ?></td></tr>

                    <tr style="background-color: #e2e8f0; font-weight: bold;"><td colspan="3">LIABILITIES (CURRENT & LONG TERM)</td></tr>
                    <?php foreach ($bs['liabilities'] as $l): ?>
                        <tr>
                            <td class="font-monospace text-muted"><?= htmlspecialchars($l['account_code']); ?></td>
                            <td class="ps-3 fw-bold"><?= htmlspecialchars($l['account_name']); ?></td>
                            <td class="num-cell"><?= number_format($l['net_balance'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold table-light"><td colspan="2" class="text-end text-uppercase">TOTAL LIABILITIES</td><td class="num-cell text-danger fw-bold"><?= number_format($bs['total_liabilities'], 2); ?></td></tr>

                    <tr style="background-color: #e2e8f0; font-weight: bold;"><td colspan="3">EQUITY & SOCIETY RESERVES</td></tr>
                    <?php foreach ($bs['equity'] as $eq): ?>
                        <tr>
                            <td class="font-monospace text-muted"><?= htmlspecialchars($eq['account_code']); ?></td>
                            <td class="ps-3 fw-bold"><?= htmlspecialchars($eq['account_name']); ?></td>
                            <td class="num-cell"><?= number_format($eq['net_balance'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td class="font-monospace text-muted">-</td>
                        <td class="ps-3 font-italic">Current Year Retained Surplus / (Deficit)</td>
                        <td class="num-cell"><?= number_format($bs['current_earnings'], 2); ?></td>
                    </tr>
                    <tr class="fw-bold table-light"><td colspan="2" class="text-end text-uppercase">TOTAL EQUITY & RESERVES</td><td class="num-cell text-success fw-bold"><?= number_format($bs['total_equity'], 2); ?></td></tr>
                </tbody>
                <tfoot>
                    <tr style="font-size: 11px;">
                        <td colspan="2" class="text-uppercase py-2">TOTAL LIABILITIES & EQUITY</td>
                        <td class="num-cell py-2 text-warning">LKR <?= number_format($bs['total_liabilities_and_equity'], 2); ?></td>
                    </tr>
                </tfoot>
            </table>

        <?php elseif ($reportType === 'cash_bank_book'): ?>
            <?php $entries = $data ?? []; ?>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Date</th>
                        <th style="width: 12%;">Voucher / Ref</th>
                        <th style="width: 18%;">Account</th>
                        <th style="width: 26%;">Description / Party</th>
                        <th style="width: 11%; text-align: right;">Debit (Inflow)</th>
                        <th style="width: 11%; text-align: right;">Credit (Outflow)</th>
                        <th style="width: 12%; text-align: right;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $running = 0; $totIn = 0; $totOut = 0;
                        foreach ($entries as $e): 
                            $deb = (float)$e['debit'];
                            $cred = (float)$e['credit'];
                            $running += ($deb - $cred);
                            $totIn += $deb;
                            $totOut += $cred;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($e['transaction_date']); ?></td>
                            <td class="font-monospace fw-bold"><?= htmlspecialchars($e['entry_number'] ?? $e['reference_number'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($e['account_code'] . ' - ' . $e['account_name']); ?></td>
                            <td><?= htmlspecialchars($e['description'] ?? $e['party_name'] ?? '-'); ?></td>
                            <td class="num-cell"><?= $deb > 0 ? number_format($deb, 2) : '-'; ?></td>
                            <td class="num-cell"><?= $cred > 0 ? number_format($cred, 2) : '-'; ?></td>
                            <td class="num-cell fw-bold"><?= number_format($running, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-uppercase">TOTAL TRANSACTIONS (<?= count($entries); ?> ENTRIES)</td>
                        <td class="num-cell text-success"><?= number_format($totIn, 2); ?></td>
                        <td class="num-cell text-danger"><?= number_format($totOut, 2); ?></td>
                        <td class="num-cell text-warning"><?= number_format($running, 2); ?></td>
                    </tr>
                </tfoot>
            </table>

        <?php elseif ($reportType === 'sales'): ?>
            <?php 
                $salesList = $data['sales'] ?? [];
                $agg = $data['aggregates'] ?? [];
            ?>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Date</th>
                        <th style="width: 12%;">Invoice #</th>
                        <th style="width: 24%;">Customer</th>
                        <th style="width: 12%;">Payment Type</th>
                        <th style="width: 14%; text-align: right;">Subtotal</th>
                        <th style="width: 12%; text-align: right;">Discount</th>
                        <th style="width: 16%; text-align: right;">Net Sales (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salesList as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('Y-m-d', strtotime($s['invoice_date']))); ?></td>
                            <td class="font-monospace fw-bold"><?= htmlspecialchars($s['invoice_number']); ?></td>
                            <td><?= htmlspecialchars($s['customer_name'] ?? 'Walking Customer'); ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($s['payment_type']); ?></span></td>
                            <td class="num-cell"><?= number_format((float)$s['subtotal'], 2); ?></td>
                            <td class="num-cell"><?= number_format((float)$s['discount_amount'], 2); ?></td>
                            <td class="num-cell fw-bold"><?= number_format((float)$s['total_amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-uppercase">TOTAL SALES SUMMARY (<?= count($salesList); ?> INVOICES)</td>
                        <td class="num-cell"><?= number_format((float)($agg['total_subtotal'] ?? 0), 2); ?></td>
                        <td class="num-cell"><?= number_format((float)($agg['total_discount'] ?? 0), 2); ?></td>
                        <td class="num-cell text-warning"><?= number_format((float)($agg['total_sales'] ?? 0), 2); ?></td>
                    </tr>
                </tfoot>
            </table>

        <?php elseif ($reportType === 'purchases'): ?>
            <?php $purchases = $data ?? []; $totPur = 0; ?>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Date</th>
                        <th style="width: 14%;">Purchase/GRN #</th>
                        <th style="width: 26%;">Supplier</th>
                        <th style="width: 14%;">Payment Status</th>
                        <th style="width: 18%; text-align: right;">Total Amount (LKR)</th>
                        <th style="width: 18%; text-align: right;">Paid Amount (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchases as $p): $totPur += (float)$p['total_amount']; ?>
                        <tr>
                            <td><?= htmlspecialchars($p['purchase_date']); ?></td>
                            <td class="font-monospace fw-bold"><?= htmlspecialchars($p['purchase_number']); ?></td>
                            <td><?= htmlspecialchars($p['supplier_name']); ?></td>
                            <td><?= htmlspecialchars(strtoupper($p['payment_status'] ?? 'PENDING')); ?></td>
                            <td class="num-cell fw-bold"><?= number_format((float)$p['total_amount'], 2); ?></td>
                            <td class="num-cell"><?= number_format((float)($p['paid_amount'] ?? 0), 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-uppercase">GRAND TOTAL PURCHASES</td>
                        <td class="num-cell text-warning"><?= number_format($totPur, 2); ?></td>
                        <td class="num-cell">-</td>
                    </tr>
                </tfoot>
            </table>

        <?php elseif ($reportType === 'expenses'): ?>
            <?php $exp = $data ?? ['rows' => [], 'total_amount' => 0]; ?>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 14%;">Account Code</th>
                        <th style="width: 38%;">Expense Title / Account</th>
                        <th style="width: 22%;">Cost Center / Division</th>
                        <th style="width: 16%; text-align: right;">Amount (LKR)</th>
                        <th style="width: 10%; text-align: right;">% Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exp['rows'] as $e): 
                        $pct = $exp['total_amount'] > 0 ? ((float)$e['amount'] / $exp['total_amount']) * 100 : 0;
                    ?>
                        <tr>
                            <td class="font-monospace text-muted"><?= htmlspecialchars($e['account_code']); ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($e['account_name']); ?></td>
                            <td><?= htmlspecialchars($e['cost_center_name'] ?? 'Society Overhead'); ?></td>
                            <td class="num-cell fw-bold"><?= number_format((float)$e['amount'], 2); ?></td>
                            <td class="num-cell text-muted"><?= number_format($pct, 1); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-uppercase">TOTAL OPERATING EXPENDITURE</td>
                        <td class="num-cell text-warning">LKR <?= number_format($exp['total_amount'], 2); ?></td>
                        <td class="num-cell">100.0%</td>
                    </tr>
                </tfoot>
            </table>

        <?php elseif ($reportType === 'ar_aging'): ?>
            <?php $aging = $data ?? []; $totRec = 0; ?>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Code</th>
                        <th style="width: 28%;">Customer Name</th>
                        <th style="width: 15%; text-align: right;">Total Due</th>
                        <th style="width: 11%; text-align: right;">0-30 Days</th>
                        <th style="width: 11%; text-align: right;">31-60 Days</th>
                        <th style="width: 11%; text-align: right;">61-90 Days</th>
                        <th style="width: 12%; text-align: right;">> 90 Days</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aging as $a): $totRec += (float)$a['total_receivable']; ?>
                        <tr>
                            <td class="font-monospace"><?= htmlspecialchars($a['code']); ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($a['name']); ?></td>
                            <td class="num-cell fw-bold text-primary"><?= number_format((float)$a['total_receivable'], 2); ?></td>
                            <td class="num-cell"><?= number_format((float)$a['current_0_30'], 2); ?></td>
                            <td class="num-cell"><?= number_format((float)$a['days_31_60'], 2); ?></td>
                            <td class="num-cell"><?= number_format((float)$a['days_61_90'], 2); ?></td>
                            <td class="num-cell text-danger"><?= number_format((float)$a['days_over_90'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-uppercase">TOTAL RECEIVABLES SCHEDULE</td>
                        <td class="num-cell text-warning">LKR <?= number_format($totRec, 2); ?></td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>

        <?php elseif ($reportType === 'cost_centers'): ?>
            <?php $divisions = $data ?? []; ?>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Code</th>
                        <th style="width: 32%;">Operational Division / Project</th>
                        <th style="width: 18%; text-align: right;">Revenue (LKR)</th>
                        <th style="width: 18%; text-align: right;">Expenses (LKR)</th>
                        <th style="width: 20%; text-align: right;">Net Margin (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $tRev = 0; $tExp = 0; $tNet = 0;
                        foreach ($divisions as $d): 
                            $rev = (float)$d['total_revenue'];
                            $exp = (float)$d['total_expense'];
                            $net = (float)$d['net_margin'];
                            $tRev += $rev; $tExp += $exp; $tNet += $net;
                    ?>
                        <tr>
                            <td class="font-monospace fw-bold"><?= htmlspecialchars($d['code']); ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($d['name']); ?></td>
                            <td class="num-cell text-success"><?= number_format($rev, 2); ?></td>
                            <td class="num-cell text-danger"><?= number_format($exp, 2); ?></td>
                            <td class="num-cell fw-bold <?= $net >= 0 ? 'text-primary' : 'text-danger'; ?>"><?= number_format($net, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-uppercase">TOTAL DIVISIONAL PERFORMANCE</td>
                        <td class="num-cell"><?= number_format($tRev, 2); ?></td>
                        <td class="num-cell"><?= number_format($tExp, 2); ?></td>
                        <td class="num-cell text-warning">LKR <?= number_format($tNet, 2); ?></td>
                    </tr>
                </tfoot>
            </table>

        <?php else: ?>
            <p class="text-center py-4 text-muted">Statement loaded successfully.</p>
        <?php endif; ?>

        <!-- Triple Endorsement Signature Block -->
        <div class="signature-block">
            <div class="row">
                <div class="col-4 signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-title">Prepared By</div>
                    <div class="signature-sub">Accounts Officer / Data Entry</div>
                </div>
                <div class="col-4 signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-title">Checked & Verified</div>
                    <div class="signature-sub">Internal Auditor / Hon. Secretary</div>
                </div>
                <div class="col-4 signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-title">Authorized Approval</div>
                    <div class="signature-sub">General Manager / President</div>
                </div>
            </div>
        </div>

        <!-- Official Footer -->
        <div class="mt-4 pt-2 border-top d-flex justify-content-between text-muted" style="font-size: 9px;">
            <div>AGRI CO-OP COOPERATIVE SOCIETY LIMITED (KE/1027) | Financial Reporting Engine</div>
            <div>Print Timestamp: <?= date('Y-m-d H:i:s'); ?> | Page 1 of 1</div>
        </div>
    </div>

</body>
</html>
