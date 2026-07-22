<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class SettingsController extends Controller {

    public function company(): void {
        Auth::requirePermission('settings.manage');
        $companyConfig = require __DIR__ . '/../../config/company.php';

        $this->render('admin/company', [
            'pageTitle' => 'Company & Society Profile Settings',
            'activeNav' => 'company_settings',
            'company' => $companyConfig
        ]);
    }
}
