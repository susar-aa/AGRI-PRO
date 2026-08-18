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
            'pageTitle' => 'Operations Management',
            'activeNav' => 'cost_centers',
            'costCenters' => $costCenters
        ]);
    }

    public function store(): void {
        Auth::requirePermission('settings.manage');
        $this->validateCsrf();

        $db = \Core\Database::getInstance();
        $stmt = $db->query("SELECT MAX(id) FROM cost_centers");
        $maxId = (int)$stmt->fetchColumn();
        $code = 'OP-' . str_pad($maxId + 1, 3, '0', STR_PAD_LEFT);
        
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            Session::setFlash('error', 'Operation name is required.');
            Helper::redirect('cost_centers');
        }

        try {
            $this->ccModel->create([
                'code' => strtoupper($code),
                'name' => $name,
                'description' => $description,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ]);
            Session::setFlash('success', "Operation [{$code}] added successfully!");
        } catch (\Exception $e) {
            Session::setFlash('error', "Failed to add Operation: " . $e->getMessage());
        }

        Helper::redirect('cost_centers');
    }
}
