<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\CostCenter;

class CostCenterController extends Controller {
    private CostCenter $ccModel;

    public function __construct() {
        $this->ccModel = new CostCenter();
    }

    public function index(): void {
        Auth::requirePermission('settings.manage');
        $costCenters = $this->ccModel->getAll();

        $this->render('cost_centers/index', [
            'pageTitle' => 'Cost Centers Management',
            'activeNav' => 'cost_centers',
            'costCenters' => $costCenters
        ]);
    }

    public function store(): void {
        Auth::requirePermission('settings.manage');
        $this->validateCsrf();

        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($code) || empty($name)) {
            Session::setFlash('error', 'Cost Center code and name are required.');
            Helper::redirect('cost_centers');
        }

        try {
            $this->ccModel->create([
                'code' => strtoupper($code),
                'name' => $name,
                'description' => $description,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ]);
            Session::setFlash('success', "Cost Center [{$code}] added successfully!");
        } catch (\Exception $e) {
            Session::setFlash('error', "Failed to add Cost Center: " . $e->getMessage());
        }

        Helper::redirect('cost_centers');
    }
}
