<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\MachineryModel;

class MachineryController extends Controller {
    private MachineryModel $machineryModel;

    public function __construct() {
        $this->machineryModel = new MachineryModel();
    }

    public function index(): void {
        Auth::requirePermission('machinery.view');

        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'category' => $_GET['category'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $machineryList = $this->machineryModel->getAll($filters, $limit, $offset);
        $totalItems = $this->machineryModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        $categories = $this->machineryModel->getCategories();

        $this->render('machinery/index', [
            'pageTitle' => 'Machinery Assets Directory',
            'activeNav' => 'machinery',
            'machineryList' => $machineryList,
            'filters' => $filters,
            'categories' => $categories,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function store(): void {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0) {
            Auth::requirePermission('machinery.edit');
        } else {
            Auth::requirePermission('machinery.create');
        }
        $this->validateCsrf();

        $data = [
            'id' => $id ?: null,
            'machinery_code' => trim($_POST['machinery_code'] ?? ''),
            'machinery_name' => trim($_POST['machinery_name'] ?? ''),
            'category' => $_POST['category'] ?? 'Other Machinery',
            'description' => trim($_POST['description'] ?? ''),
            'serial_number' => trim($_POST['serial_number'] ?? ''),
            'default_rental_rate' => (float)($_POST['default_rental_rate'] ?? 0),
            'rental_unit' => $_POST['rental_unit'] ?? 'Hour',
            'status' => $_POST['status'] ?? 'AVAILABLE',
            'notes' => trim($_POST['notes'] ?? '')
        ];

        try {
            if (empty($data['machinery_name'])) {
                throw new \Exception("Machinery Name is required.");
            }

            $this->machineryModel->save($data);
            Session::setFlash('success', 'Machinery asset saved successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to save machinery: ' . $e->getMessage());
        }

        Helper::redirect('modules/machinery');
    }

    public function markMaintenance(): void {
        Auth::requirePermission('machinery.maintenance');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            $this->machineryModel->updateStatus($id, 'MAINTENANCE');
            Session::setFlash('success', 'Machinery marked as under MAINTENANCE.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Update failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/machinery');
    }

    public function deactivate(): void {
        Auth::requirePermission('machinery.deactivate');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            $this->machineryModel->updateStatus($id, 'INACTIVE');
            Session::setFlash('success', 'Machinery marked as INACTIVE.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Deactivation failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/machinery');
    }
}
