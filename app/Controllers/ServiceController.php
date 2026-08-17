<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\ServiceModel;

class ServiceController extends Controller {
    private ServiceModel $serviceModel;

    public function __construct() {
        $this->serviceModel = new ServiceModel();
    }

    public function index(): void {
        Auth::requirePermission('services.view');

        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'category_id' => $_GET['category_id'] ?? '',
            'is_active' => $_GET['is_active'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $services = $this->serviceModel->getAll($filters, $limit, $offset);
        $totalItems = $this->serviceModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        $this->render('services/index', [
            'pageTitle' => 'Service Master Registry',
            'activeNav' => 'services',
            'services' => $services,
            'filters' => $filters,
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
            Auth::requirePermission('services.edit');
        } else {
            Auth::requirePermission('services.create');
        }
        $this->validateCsrf();

        $data = [
            'id' => $id ?: null,
            'service_code' => trim($_POST['service_code'] ?? ''),
            'service_name' => trim($_POST['service_name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'default_price' => (float)($_POST['default_price'] ?? 0),
            'unit' => trim($_POST['unit'] ?? 'Job'),
            'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1
        ];

        try {
            if (empty($data['service_name'])) {
                throw new \Exception("Service Name is required.");
            }

            $this->serviceModel->save($data);
            Session::setFlash('success', 'Service registry item saved successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to save service: ' . $e->getMessage());
        }

        Helper::redirect('modules/services');
    }

    public function deactivate(): void {
        Auth::requirePermission('services.deactivate');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            $this->serviceModel->deactivate($id);
            Session::setFlash('success', 'Service item successfully marked inactive.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Deactivation failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/services');
    }
}
