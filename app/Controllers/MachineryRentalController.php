<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\MachineryRentalModel;
use App\Models\MachineryModel;
use App\Models\Party;
use App\Models\ServiceModel;

class MachineryRentalController extends Controller {
    private MachineryRentalModel $rentalModel;
    private MachineryModel $machineryModel;
    private Party $partyModel;
    private ServiceModel $serviceModel;

    public function __construct() {
        $this->rentalModel = new MachineryRentalModel();
        $this->machineryModel = new MachineryModel();
        $this->partyModel = new Party();
        $this->serviceModel = new ServiceModel();
    }

    public function index(): void {
        Auth::requirePermission('machinery_rentals.view');

        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'status' => $_GET['status'] ?? '',
            'customer_id' => $_GET['customer_id'] ?? '',
            'machinery_id' => $_GET['machinery_id'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $rentals = $this->rentalModel->getAll($filters, $limit, $offset);
        $totalItems = $this->rentalModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        // Fetch active customers
        $db = \Core\Database::getInstance();
        $customers = $db->query("SELECT id, party_code, name FROM parties WHERE party_type IN ('CUSTOMER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();

        // Fetch all machinery
        $machineryList = $this->machineryModel->getAll([]);

        $this->render('machinery_rentals/index', [
            'pageTitle' => 'Machinery Rentals Registry',
            'activeNav' => 'machinery_rentals',
            'rentals' => $rentals,
            'filters' => $filters,
            'customers' => $customers,
            'machineryList' => $machineryList,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function view(): void {
        Auth::requirePermission('machinery_rentals.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $rental = $this->rentalModel->getById($id);

        if (!$rental) {
            Session::setFlash('error', 'Machinery Rental record not found.');
            Helper::redirect('modules/machinery-rentals');
        }

        // We also need to map or find if a related Machinery Rental service exists to preselect
        $db = \Core\Database::getInstance();
        $srv = $db->query("SELECT id FROM services WHERE service_code = 'SRV-TRAC-RNT' OR service_code = 'SRV-GEN-RNT' OR service_name LIKE '%Rental%' LIMIT 1")->fetch();
        $serviceId = $srv ? (int)$srv['id'] : 0;

        $this->render('machinery_rentals/view', [
            'pageTitle' => 'Rental Details: ' . $rental['rental_number'],
            'activeNav' => 'machinery_rentals',
            'rental' => $rental,
            'serviceId' => $serviceId
        ]);
    }

    public function store(): void {
        Auth::requirePermission('machinery_rentals.view');
        $this->validateCsrf();

        $data = [
            'customer_id' => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0,
            'machinery_id' => !empty($_POST['machinery_id']) ? (int)$_POST['machinery_id'] : 0,
            'start_date' => $_POST['start_date'] ?? date('Y-m-d'),
            'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
            'quantity' => (float)($_POST['quantity'] ?? 1),
            'rental_rate' => (float)($_POST['rental_rate'] ?? 0),
            'notes' => trim($_POST['notes'] ?? ''),
            'status' => 'ACTIVE',
            'created_by' => Auth::id() ?? 1
        ];

        $data['total_charge'] = $data['quantity'] * $data['rental_rate'];

        if (empty($data['customer_id']) || empty($data['machinery_id']) || empty($data['start_date'])) {
            Session::setFlash('error', 'Customer, Machinery Asset, and Start Date are required.');
            Helper::redirect('modules/machinery-rentals');
        }

        try {
            $this->rentalModel->save($data);
            Session::setFlash('success', 'Machinery rental recorded successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to save rental: ' . $e->getMessage());
        }

        Helper::redirect('modules/machinery-rentals');
    }

    public function complete(): void {
        Auth::requirePermission('machinery_rentals.complete');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            $rental = $this->rentalModel->getById($id);
            if (!$rental) {
                throw new \Exception("Rental record not found.");
            }

            $db = \Core\Database::getInstance();
            $db->prepare("UPDATE machinery_rentals SET status = 'COMPLETED', end_date = CURDATE(), updated_at = NOW() WHERE id = :id")
               ->execute(['id' => $id]);

            // Release machinery
            $this->machineryModel->updateStatus((int)$rental['machinery_id'], 'AVAILABLE');

            Session::setFlash('success', 'Machinery Rental marked as COMPLETED. Equipment released back to inventory.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to complete rental: ' . $e->getMessage());
        }

        Helper::redirect('modules/machinery-rentals/view?id=' . $id);
    }

    public function cancel(): void {
        Auth::requirePermission('machinery_rentals.cancel');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            $rental = $this->rentalModel->getById($id);
            if (!$rental) {
                throw new \Exception("Rental record not found.");
            }

            $db = \Core\Database::getInstance();
            $db->prepare("UPDATE machinery_rentals SET status = 'CANCELLED', updated_at = NOW() WHERE id = :id")
               ->execute(['id' => $id]);

            // Release machinery
            $this->machineryModel->updateStatus((int)$rental['machinery_id'], 'AVAILABLE');

            Session::setFlash('success', 'Machinery Rental marked as CANCELLED. Equipment released.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to cancel rental: ' . $e->getMessage());
        }

        Helper::redirect('modules/machinery-rentals/view?id=' . $id);
    }
}
