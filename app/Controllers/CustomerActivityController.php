<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\CustomerActivityModel;

class CustomerActivityController extends Controller {

    private CustomerActivityModel $activityModel;

    public function __construct() {
        parent::__construct();
        $this->activityModel = new CustomerActivityModel();
    }

    public function index(): void {
        Auth::requirePermission('settings.view');

        $activities = $this->activityModel->getAll();

        $this->render('system/customer_activities', [
            'pageTitle' => 'Manage Customer Activities',
            'activeNav' => 'customer_activities',
            'activities' => $activities
        ]);
    }

    public function store(): void {
        Auth::requirePermission('settings.manage');
        $this->validateCsrf();

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'status' => $_POST['status'] ?? 'active'
        ];

        if (empty($data['name'])) {
            Session::setFlash('error', 'Activity name is required.');
            Helper::redirect('system/customer-activities');
        }

        try {
            if (!empty($_POST['id'])) {
                $this->activityModel->update((int)$_POST['id'], $data);
                Session::setFlash('success', 'Customer Activity successfully updated.');
            } else {
                $this->activityModel->create($data);
                Session::setFlash('success', 'New Customer Activity successfully added.');
            }
        } catch (\Exception $e) {
            Session::setFlash('error', 'Action failed: ' . $e->getMessage());
        }

        Helper::redirect('system/customer-activities');
    }
}
