<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use Exception;

class SettingsController extends Controller {

    public function company(): void {
        Auth::requirePermission('settings.manage');
        $companyConfig = file_exists(__DIR__ . '/../../config/company.php') 
            ? require __DIR__ . '/../../config/company.php' 
            : [];

        $this->render('admin/company', [
            'pageTitle' => 'Company & Society Profile Settings',
            'activeNav' => 'company_settings',
            'company' => $companyConfig
        ]);
    }

    public function updateCompany(): void {
        Auth::requirePermission('settings.manage');

        try {
            $companyNameSi = trim($_POST['company_name_si'] ?? '');
            $companyNameEn = trim($_POST['company_name_en'] ?? '');
            $addressSi     = trim($_POST['address_si'] ?? '');
            $addressEn     = trim($_POST['address_en'] ?? '');
            $regNoSi       = trim($_POST['reg_no_si'] ?? '');
            $regNoEn       = trim($_POST['reg_no_en'] ?? '');
            $regDate       = trim($_POST['reg_date'] ?? '');
            
            $contactsRaw   = $_POST['contact_numbers'] ?? '';
            $contactNumbers = [];
            if (is_array($contactsRaw)) {
                foreach ($contactsRaw as $c) {
                    $c = trim($c);
                    if ($c !== '') $contactNumbers[] = $c;
                }
            } else {
                $lines = preg_split('/[\r\n,]+/', $contactsRaw);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line !== '') $contactNumbers[] = $line;
                }
            }

            if (empty($companyNameEn) || empty($companyNameSi)) {
                throw new Exception("Company Name (Sinhala and English) cannot be empty.");
            }

            $newConfig = [
                'company_name_si' => $companyNameSi,
                'company_name_en' => $companyNameEn,
                'address_si' => $addressSi,
                'address_en' => $addressEn,
                'reg_no_si' => $regNoSi,
                'reg_no_en' => $regNoEn,
                'reg_date' => $regDate,
                'contact_numbers' => $contactNumbers
            ];

            $configFilePath = __DIR__ . '/../../config/company.php';
            $fileContent = "<?php\n/**\n * Company Metadata Configuration\n */\n\nreturn " . var_export($newConfig, true) . ";\n";

            if (file_put_contents($configFilePath, $fileContent) === false) {
                throw new Exception("Failed to save configuration file. Check file permissions.");
            }

            // Audit log if service available
            if (class_exists('\\App\\Services\\AuditService')) {
                \App\Services\AuditService::log('update', 'company_settings', 0, null, ['company_name_en' => $companyNameEn]);
            }

            Session::setFlash('success', 'Company & Society Profile updated successfully! All report headlines will now display the updated details.');
        } catch (Exception $e) {
            Session::setFlash('danger', 'Error updating profile: ' . $e->getMessage());
        }

        Helper::redirect('admin/company');
    }
}
