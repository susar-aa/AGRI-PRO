<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\ServiceJobModel;
use App\Models\Party;
use App\Models\ServiceModel;

class ServiceJobController extends Controller {
    private ServiceJobModel $jobModel;
    private Party $partyModel;
    private ServiceModel $serviceModel;

    public function __construct() {
        $this->jobModel = new ServiceJobModel();
        $this->partyModel = new Party();
        $this->serviceModel = new ServiceModel();
    }

    public function index(): void {
        Auth::requirePermission('service_jobs.view');

        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'status' => $_GET['status'] ?? '',
            'customer_id' => $_GET['customer_id'] ?? '',
            'service_id' => $_GET['service_id'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $jobs = $this->jobModel->getAll($filters, $limit, $offset);
        $totalItems = $this->jobModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        // Fetch active customers
        $db = \Core\Database::getInstance();
        $customers = $db->query("SELECT id, party_code, name FROM parties WHERE party_type IN ('CUSTOMER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();

        // Fetch active services
        $services = $db->query("SELECT id, service_code, service_name FROM services WHERE is_active = 1 ORDER BY service_name ASC")->fetchAll();

        $this->render('service_jobs/index', [
            'pageTitle' => 'Service Jobs Registry',
            'activeNav' => 'service_jobs',
            'jobs' => $jobs,
            'filters' => $filters,
            'customers' => $customers,
            'services' => $services,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function view(): void {
        Auth::requirePermission('service_jobs.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $job = $this->jobModel->getById($id);

        if (!$job) {
            Session::setFlash('error', 'Service Job record not found.');
            Helper::redirect('modules/service-jobs');
        }

        $this->render('service_jobs/view', [
            'pageTitle' => 'Job Details: ' . $job['job_number'],
            'activeNav' => 'service_jobs',
            'job' => $job
        ]);
    }

    public function create(): void {
        Auth::requirePermission('service_jobs.create');

        $db = \Core\Database::getInstance();

        // Fetch active customers
        $customers = $db->query("SELECT id, party_code, name FROM parties WHERE party_type IN ('CUSTOMER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();

        // Fetch active services
        $services = $db->query("SELECT id, service_code, service_name, default_price FROM services WHERE is_active = 1 ORDER BY service_name ASC")->fetchAll();

        $this->render('service_jobs/create', [
            'pageTitle' => 'Register Service Job',
            'activeNav' => 'service_jobs',
            'customers' => $customers,
            'services' => $services
        ]);
    }

    public function store(): void {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0) {
            Auth::requirePermission('service_jobs.edit');
        } else {
            Auth::requirePermission('service_jobs.create');
        }
        $this->validateCsrf();

        $data = [
            'id' => $id ?: null,
            'customer_id' => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0,
            'service_id' => !empty($_POST['service_id']) ? (int)$_POST['service_id'] : 0,
            'start_date' => $_POST['start_date'] ?? date('Y-m-d'),
            'end_date' => $_POST['end_date'] ?? null,
            'location' => trim($_POST['location'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'assigned_employee' => trim($_POST['assigned_employee'] ?? ''),
            'status' => $_POST['status'] ?? 'OPEN',
            'invoice_id' => !empty($_POST['invoice_id']) ? (int)$_POST['invoice_id'] : null,
            'created_by' => Auth::id() ?? 1
        ];

        try {
            if (!$data['customer_id']) {
                throw new \Exception("Customer selection is required.");
            }
            if (!$data['service_id']) {
                throw new \Exception("Service selection is required.");
            }

            $jobId = $this->jobModel->save($data);
            Session::setFlash('success', 'Service Job record saved successfully.');
            Helper::redirect('modules/service-jobs/view?id=' . $jobId);
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to save job: ' . $e->getMessage());
            Helper::redirect('modules/service-jobs/create');
        }
    }

    public function complete(): void {
        Auth::requirePermission('service_jobs.complete');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            $db = \Core\Database::getInstance();
            $db->prepare("UPDATE service_jobs SET status = 'COMPLETED', end_date = CURDATE(), updated_at = NOW() WHERE id = :id")
               ->execute(['id' => $id]);
            Session::setFlash('success', 'Service Job successfully marked as COMPLETED.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to complete job: ' . $e->getMessage());
        }

        Helper::redirect('modules/service-jobs/view?id=' . $id);
    }

    public function cancel(): void {
        Auth::requirePermission('service_jobs.cancel');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            $db = \Core\Database::getInstance();
            $db->prepare("UPDATE service_jobs SET status = 'CANCELLED', updated_at = NOW() WHERE id = :id")
               ->execute(['id' => $id]);
            Session::setFlash('success', 'Service Job successfully marked as CANCELLED.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to cancel job: ' . $e->getMessage());
        }

        Helper::redirect('modules/service-jobs/view?id=' . $id);
    }
}
