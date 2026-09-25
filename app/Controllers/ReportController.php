<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Helper;
use Core\Session;
use App\Models\ReportModel;

class ReportController extends Controller {

    private ReportModel $reportModel;

    public function __construct() {
        $this->reportModel = new ReportModel();
    }

    /**
     * Directory Report Screen
     */
    public function directory(): void {
        if (!Auth::check()) {
            Helper::redirect('login');
            exit;
        }

        $filters = [
            'type' => strtolower(trim($_GET['type'] ?? 'all')),
            'search' => trim($_GET['search'] ?? ''),
            'status' => strtolower(trim($_GET['status'] ?? 'all'))
        ];

        $records = $this->reportModel->getDirectoryReport($filters);
        $counts = $this->reportModel->getDirectoryCounts($filters);

        $this->render('reports/directory', [
            'pageTitle' => 'Central Directory Report',
            'activeNav' => 'reports_directory',
            'records' => $records,
            'counts' => $counts,
            'filters' => $filters
        ]);
    }

    /**
     * Export Directory Report (PDF / Excel)
     */
    public function exportDirectory(): void {
        if (!Auth::check()) {
            Helper::redirect('login');
            exit;
        }

        $filters = [
            'type' => strtolower(trim($_GET['type'] ?? 'all')),
            'search' => trim($_GET['search'] ?? ''),
            'status' => strtolower(trim($_GET['status'] ?? 'all'))
        ];
        $format = strtolower(trim($_GET['format'] ?? 'excel'));

        $records = $this->reportModel->getDirectoryReport($filters);
        
        $companyConfig = file_exists(__DIR__ . '/../../config/company.php')
            ? require __DIR__ . '/../../config/company.php'
            : [];

        $currentUser = Auth::user();
        $generatedBy = $currentUser['full_name'] ?? Session::get('full_name') ?? 'User';
        $generatedAt = date('Y-m-d H:i:s');

        if ($format === 'pdf') {
            // Render space-saving, print-ready document view
            $this->renderPartial('reports/directory_pdf', [
                'pageTitle' => 'Directory Report Print - Agri Co-Op ERP',
                'records' => $records,
                'filters' => $filters,
                'company' => $companyConfig,
                'generatedBy' => $generatedBy,
                'generatedAt' => $generatedAt
            ]);
            exit;
        }

        // Default Excel Export
        $this->exportExcel($records, $filters, $companyConfig, $generatedBy, $generatedAt);
    }

    /**
     * Stream Excel file for download with UTF-8 support
     */
    private function exportExcel(array $records, array $filters, array $company, string $generatedBy, string $generatedAt): void {
        $filename = 'Directory_Report_' . ($filters['type'] ?: 'All') . '_' . date('Ymd_His') . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        // Output UTF-8 BOM so Excel decodes Sinhala & special characters properly
        echo "\xEF\xBB\xBF";

        $typeName = match($filters['type']) {
            'director', 'directors' => 'Directors Only',
            'member', 'members' => 'Members Only',
            'staff' => 'Staff Only',
            'customer', 'customers' => 'Customers Only',
            default => 'All Directory Categories'
        };

        $companyNameEn = $company['company_name_en'] ?? 'Agri Co-Op Cooperative Society Limited';
        $companyNameSi = $company['company_name_si'] ?? 'සීමා සහිත ඇග්රි කෝප් සමූපකාර සමිතිය';
        $addressEn = $company['address_en'] ?? 'Rambukkana';
        $contacts = implode(', ', $company['contact_numbers'] ?? []);

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 11px; }
                th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; border: 1px solid #000000; padding: 6px; text-align: left; }
                td { border: 1px solid #cccccc; padding: 5px; vertical-align: top; }
                .title-row { font-size: 16px; font-weight: bold; color: #1e3a8a; text-align: center; }
                .subtitle-row { font-size: 12px; font-weight: bold; text-align: center; color: #4b5563; }
                .meta-table { margin-bottom: 15px; }
                .meta-table td { border: none; font-size: 11px; }
                .badge { padding: 3px 6px; font-weight: bold; border-radius: 3px; text-align: center; }
                .active-status { background-color: #d1fae5; color: #065f46; }
                .inactive-status { background-color: #fee2e2; color: #991b1b; }
            </style>
        </head>
        <body>
            <table>
                <tr>
                    <td colspan="10" class="title-row"><?= htmlspecialchars($companyNameEn); ?></td>
                </tr>
                <tr>
                    <td colspan="10" class="subtitle-row"><?= htmlspecialchars($companyNameSi); ?> - <?= htmlspecialchars($addressEn); ?></td>
                </tr>
                <tr>
                    <td colspan="10" class="subtitle-row" style="font-size: 14px; font-weight: bold; color: #047857; padding-top: 5px;">DIRECTORY REPORT (<?= strtoupper($typeName); ?>)</td>
                </tr>
                <tr><td colspan="10" style="border:none;"></td></tr>
            </table>

            <table class="meta-table">
                <tr>
                    <td><strong>Filter Type:</strong> <?= htmlspecialchars($typeName); ?></td>
                    <td><strong>Search Query:</strong> <?= htmlspecialchars($filters['search'] !== '' ? $filters['search'] : 'None (All Results)'); ?></td>
                </tr>
                <tr>
                    <td><strong>Status Filter:</strong> <?= htmlspecialchars(ucfirst($filters['status'] ?: 'all')); ?></td>
                    <td><strong>Total Matched Records:</strong> <?= count($records); ?></td>
                </tr>
                <tr>
                    <td><strong>Generated Date:</strong> <?= htmlspecialchars($generatedAt); ?></td>
                    <td><strong>Generated By:</strong> <?= htmlspecialchars($generatedBy); ?></td>
                </tr>
            </table>

            <br>

            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th style="width: 90px;">Category</th>
                        <th style="width: 100px;">Code / Reg No</th>
                        <th style="width: 180px;">Full Name</th>
                        <th style="width: 110px;">Username</th>
                        <th style="width: 110px;">Phone Number</th>
                        <th style="width: 110px;">NIC / Reg No</th>
                        <th style="width: 220px;">Address</th>
                        <th style="width: 110px;">City</th>
                        <th style="width: 80px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; color: #6b7280; padding: 15px;">No directory records match the selected filter and search criteria.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($records as $index => $row): ?>
                            <tr>
                                <td style="text-align: center;"><?= $index + 1; ?></td>
                                <td style="font-weight: bold; color: #1e40af;"><?= htmlspecialchars($row['entity_type'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['code'] ?? '-'); ?></td>
                                <td style="font-weight: bold;"><?= htmlspecialchars($row['name'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['username'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['phone'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['nic'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['address'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['city'] ?? '-'); ?></td>
                                <td style="text-align: center; uppercase;">
                                    <?= htmlspecialchars(strtoupper($row['status'] ?? 'ACTIVE')); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </body>
        </html>
        <?php
        exit;
    }
}
