<?php
$companyNameEn = $company['company_name_en'] ?? 'Agri Co-Op Cooperative Society Limited';
$companyNameSi = $company['company_name_si'] ?? 'සීමා සහිත ඇග්රි කෝප් සමූපකාර සමිතිය';
$addressEn = $company['address_en'] ?? 'Miduma, Yatagama, Rambukkana';
$addressSi = $company['address_si'] ?? 'මීදූම, යටගම, රඹුක්කන';
$regNoEn = $company['reg_no_en'] ?? 'KE/1027';
$regNoSi = $company['reg_no_si'] ?? 'කෑ/1027';
$contacts = implode(' / ', array_slice($company['contact_numbers'] ?? ['075 377 0 145', '070 629 61 50'], 0, 3));

$typeName = match($filters['type'] ?? 'all') {
    'director', 'directors' => 'Directors Only',
    'member', 'members' => 'Members Only',
    'staff' => 'Staff Only',
    'customer', 'customers' => 'Customers Only',
    default => 'All Categories (Directors, Members, Staff, Customers)'
};

$searchQuery = trim($filters['search'] ?? '');
$statusFilter = ucfirst($filters['status'] ?? 'all');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directory_Report_<?= date('Ymd_His'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* Base Screen Styling */
        body {
            background-color: #f3f4f6;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1f2937;
            font-size: 12px;
        }

        .document-container {
            max-width: 1050px;
            margin: 20px auto;
            background: #ffffff;
            padding: 25px 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
        }

        /* Action Toolbar */
        .toolbar {
            max-width: 1050px;
            margin: 15px auto 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Header Layout */
        .society-header {
            border-bottom: 2px solid #15803d;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .society-title-si {
            font-size: 16px;
            font-weight: 700;
            color: #15803d;
            margin-bottom: 2px;
        }

        .society-title-en {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .society-sub {
            font-size: 11px;
            color: #4b5563;
        }

        .report-title-badge {
            background-color: #15803d;
            color: #ffffff;
            padding: 4px 12px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: inline-block;
            border-radius: 2px;
        }

        /* Metadata Box */
        .meta-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 15px;
            font-size: 11px;
        }

        /* Compact Space-Saving Print Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        .report-table th {
            background-color: #1e293b !important;
            color: #ffffff !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9.5px;
            padding: 5px 6px;
            border: 1px solid #0f172a;
            vertical-align: middle;
        }

        .report-table td {
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            line-height: 1.25;
        }

        .report-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge-type {
            font-size: 9px;
            padding: 2px 5px;
            font-weight: 700;
            border-radius: 2px;
            text-transform: uppercase;
            display: inline-block;
        }

        .badge-director { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-member { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-staff { background-color: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }
        .badge-customer { background-color: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }

        .search-highlight {
            background-color: #fef08a;
            font-weight: bold;
            padding: 0 2px;
        }

        /* Print Media Styles */
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm 10mm;
            }

            body {
                background-color: #ffffff !important;
                font-size: 10px !important;
                color: #000000 !important;
            }

            .document-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            .no-print {
                display: none !important;
            }

            .society-header {
                border-bottom: 1.5pt solid #000000 !important;
                padding-bottom: 8px !important;
                margin-bottom: 10px !important;
            }

            .report-title-badge {
                background-color: #000000 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .report-table th {
                background-color: #1e293b !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-size: 9px !important;
                padding: 4px 5px !important;
            }

            .report-table td {
                padding: 3.5px 5px !important;
                font-size: 9.5px !important;
                border: 0.5pt solid #64748b !important;
            }

            .report-table tr {
                page-break-inside: avoid !important;
            }

            .meta-card {
                background-color: #ffffff !important;
                border: 1pt solid #94a3b8 !important;
                padding: 6px 10px !important;
                margin-bottom: 10px !important;
            }
        }
    </style>
</head>
<body>

    <!-- Action Toolbar (Hidden on Print) -->
    <div class="toolbar no-print">
        <div>
            <a href="<?= \Core\Helper::baseUrl('reports/directory') . '?' . http_build_query($filters); ?>" class="btn btn-outline-secondary btn-sm me-2">
                <i class="bi bi-arrow-left"></i> Back to Report View
            </a>
            <span class="text-muted small">Print Preview Mode</span>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= \Core\Helper::baseUrl('reports/directory/export') . '?' . http_build_query(array_merge($filters, ['format' => 'excel'])); ?>" class="btn btn-outline-success btn-sm font-weight-bold">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm font-weight-bold">
                <i class="bi bi-printer-fill"></i> Print Report Now
            </button>
        </div>
    </div>

    <!-- Printable A4 Document Body -->
    <div class="document-container">
        
        <!-- Header -->
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
                <div class="report-title-badge mb-1">DIRECTORY REPORT</div>
                <div class="text-uppercase fw-bold text-secondary" style="font-size: 10px; letter-spacing: 0.5px;">
                    Category: <?= htmlspecialchars($typeName); ?>
                </div>
            </div>
        </div>

        <!-- Filter & Meta Info Strip -->
        <div class="meta-card">
            <div class="row g-2">
                <div class="col-6">
                    <div><strong>Category Filter:</strong> <?= htmlspecialchars($typeName); ?></div>
                    <div><strong>Search Term:</strong> <?= $searchQuery !== '' ? '<span class="fw-bold text-success">"' . htmlspecialchars($searchQuery) . '"</span>' : 'All Records (No Search Filter)'; ?></div>
                    <div><strong>Status Filter:</strong> <?= htmlspecialchars($statusFilter); ?></div>
                </div>
                <div class="col-6 text-end">
                    <div><strong>Date & Time Generated:</strong> <?= htmlspecialchars($generatedAt); ?></div>
                    <div><strong>Generated By User:</strong> <?= htmlspecialchars($generatedBy); ?></div>
                    <div><strong>Total Matched Records:</strong> <span class="fw-bold text-primary"><?= count($records); ?></span></div>
                </div>
            </div>
        </div>

        <!-- Space-Saving Compact Table -->
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">#</th>
                    <th style="width: 75px;">Category</th>
                    <th style="width: 85px;">Code / No</th>
                    <th style="width: 160px;">Full Name</th>
                    <th style="width: 90px;">Username</th>
                    <th style="width: 95px;">Phone</th>
                    <th style="width: 95px;">NIC / Reg No</th>
                    <th>Address</th>
                    <th style="width: 90px;">City</th>
                    <th style="width: 55px;" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            No directory records match the search filter "<?= htmlspecialchars($searchQuery); ?>".
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $index => $row): ?>
                        <tr>
                            <td class="text-center text-muted"><?= $index + 1; ?></td>
                            <td>
                                <?php 
                                    $cat = $row['entity_type'] ?? 'Member';
                                    $badgeClass = match($cat) {
                                        'Director' => 'badge-director',
                                        'Member' => 'badge-member',
                                        'Staff' => 'badge-staff',
                                        'Customer' => 'badge-customer',
                                        default => 'badge-member'
                                    };
                                ?>
                                <span class="badge-type <?= $badgeClass; ?>"><?= htmlspecialchars($cat); ?></span>
                            </td>
                            <td class="fw-bold font-monospace text-dark"><?= htmlspecialchars($row['code'] ?? '-'); ?></td>
                            <td class="fw-bold">
                                <?php 
                                    $name = $row['name'] ?? '';
                                    if ($searchQuery !== '' && stripos($name, $searchQuery) !== false) {
                                        echo '<span class="search-highlight">' . htmlspecialchars($name) . '</span>';
                                    } else {
                                        echo htmlspecialchars($name);
                                    }
                                ?>
                            </td>
                            <td class="font-monospace">
                                <?php if (!empty($row['username'])): ?>
                                    @<?= htmlspecialchars($row['username']); ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['phone'] ?? '-') ?: '-'; ?></td>
                            <td class="font-monospace"><?= htmlspecialchars($row['nic'] ?? '-') ?: '-'; ?></td>
                            <td>
                                <?php 
                                    $addr = $row['address'] ?? '';
                                    if ($searchQuery !== '' && stripos($addr, $searchQuery) !== false) {
                                        echo '<span class="search-highlight">' . htmlspecialchars($addr) . '</span>';
                                    } else {
                                        echo htmlspecialchars($addr ?: '-');
                                    }
                                ?>
                            </td>
                            <td>
                                <?php 
                                    $city = $row['city'] ?? '';
                                    if ($searchQuery !== '' && stripos($city, $searchQuery) !== false) {
                                        echo '<span class="search-highlight">' . htmlspecialchars($city) . '</span>';
                                    } else {
                                        echo htmlspecialchars($city ?: '-');
                                    }
                                ?>
                            </td>
                            <td class="text-center fw-bold small">
                                <?php if (strtolower($row['status'] ?? 'active') === 'active'): ?>
                                    <span class="text-success">ACT</span>
                                <?php else: ?>
                                    <span class="text-danger">INA</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Printable Footer -->
        <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 9px;">
            <div>Agri Co-Op ERP | Central Directory Reporting Engine</div>
            <div>Page 1 of 1 | Printable Record Count: <?= count($records); ?></div>
        </div>
    </div>

</body>
</html>
