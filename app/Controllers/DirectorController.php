<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use Core\Database;
use App\Models\DirectorModel;
use App\Models\Party;
use App\Services\AccountingEngine;
use App\Services\AuditService;

class DirectorController extends Controller {
    private DirectorModel $directorModel;
    private Party $partyModel;

    public function __construct() {
        $this->directorModel = new DirectorModel();
        $this->partyModel = new Party();
    }

    public function directory(): void {
        Auth::requirePermission('parties.view');

        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? '';

        // Fetch Society directors
        $filters = ['search' => $search];
        if ($status) $filters['status'] = strtoupper($status);
        $directors = $this->directorModel->getAll($filters, 100);

        $this->render('directors/directory', [
            'pageTitle' => 'directors',
            'activeNav' => 'directory',
            'search' => $search,
            'status' => $status,
            'directors' => $directors
        ]);
    }

    public function registerForm(): void {
        Auth::requirePermission('parties.create');

        $db = \Core\Database::getInstance();
        $sectors = $db->query("SELECT name FROM agricultural_sectors ORDER BY name")->fetchAll();

        $this->render('directors/register', [
            'pageTitle' => 'Register Director',
            'activeNav' => 'directory',
            'member_no' => $this->directorModel->generateDirectorNumber(),
            'sectors' => $sectors
        ]);
    }

    public function store(): void {
        Auth::requirePermission('parties.create');
        $this->validateCsrf();

        $db = Database::getInstance();
        $directorData = [
            'member_no' => trim($_POST['member_no'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'nic' => trim($_POST['nic'] ?? ''),
            'dob' => $_POST['dob'] ?? '',
            'gender' => $_POST['gender'] ?? 'Male',
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'whatsapp' => trim($_POST['whatsapp'] ?? ''),
            'agricultural_sector' => trim($_POST['agricultural_sector'] ?? ''),
            'heir_name' => trim($_POST['heir_name'] ?? ''),
            'heir_address' => trim($_POST['heir_address'] ?? ''),
            'heir_nic' => trim($_POST['heir_nic'] ?? ''),
            'heir_contact_number' => trim($_POST['heir_contact_number'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'registration_date' => $_POST['registration_date'] ?? date('Y-m-d'),
            'status' => 'ACTIVE',
            'notes' => trim($_POST['notes'] ?? ''),
            'party_id' => !empty($_POST['party_id']) ? (int)$_POST['party_id'] : null
        ];

        // Ensure no duplicate NIC exists
        $nicExists = $db->prepare("SELECT id FROM coop_members WHERE nic = :nic AND member_type = 'DIRECTOR'");
        $nicExists->execute(['nic' => $directorData['nic']]);
        if ($nicExists->fetch()) {
            Session::setFlash('error', 'A director with this NIC is already registered.');
            Helper::redirect('modules/directors/register');
        }

        // Ensure registration number is provided and unique
        if (empty($directorData['member_no'])) {
            Session::setFlash('error', 'Registration number is required.');
            Helper::redirect('modules/directors/register');
        }
        $noExists = $db->prepare("SELECT id FROM coop_members WHERE member_no = :no");
        $noExists->execute(['no' => $directorData['member_no']]);
        if ($noExists->fetch()) {
            Session::setFlash('error', 'This Registration Number is already in use.');
            Helper::redirect('modules/directors/register');
        }

        try {
            $db->beginTransaction();

            if (!empty($directorData['agricultural_sector'])) {
                $stmt = $db->prepare("INSERT IGNORE INTO agricultural_sectors (name) VALUES (:name)");
                $stmt->execute(['name' => $directorData['agricultural_sector']]);
            }

            $directorId = $this->directorModel->create($directorData);

            $db->commit();
            Session::setFlash('success', 'Director registered successfully!');
            Helper::redirect('modules/directors/view?id=' . $directorId);
        } catch (\Exception $e) {
            $db->rollBack();
            Session::setFlash('error', 'Registration failed: ' . $e->getMessage());
            Helper::redirect('modules/directors/register');
        }
    }

    public function view(): void {
        Auth::requirePermission('parties.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $director = $this->directorModel->getById($id);

        if (!$director) {
            Session::setFlash('error', 'Director not found.');
            Helper::redirect('modules/directors/directory');
        }

        // Fetch recent posted payments
        $db = Database::getInstance();
        $journal = null;
        if ($director['journal_entry_id']) {
            $journal = $db->query("SELECT * FROM journal_entries WHERE id = " . (int)$director['journal_entry_id'])->fetch();
        }

        // Fetch Financial/System Activity if director is linked to a customer
        $invoices = [];
        $payments = [];
        $rentals = [];
        $ledgerEntries = [];

        if (!empty($director['party_id'])) {
            $partyId = (int)$director['party_id'];
            $invoiceModel = new \App\Models\InvoiceModel();
            $paymentModel = new \App\Models\ReceiptPaymentModel();
            
            $ledgerModel = new \App\Models\PartyLedger();

            $invoices = $invoiceModel->getAll(['customer_id' => $partyId], 50);
            $payments = $paymentModel->getAll(['party_id' => $partyId], 50);
            
            $ledgerEntries = $ledgerModel->getLedgerEntries($partyId, 'CUSTOMER');
        }

        $this->render('directors/view', [
            'pageTitle' => 'Director Profile: ' . $director['full_name'],
            'activeNav' => 'directory',
            'director' => $director,
            'journal' => $journal,
            'invoices' => $invoices,
            'payments' => $payments,
            'rentals' => $rentals,
            'ledgerEntries' => $ledgerEntries
        ]);
    }

    public function edit(): void {
        Auth::requirePermission('parties.edit');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $director = $this->directorModel->getById($id);

        if (!$director) {
            Session::setFlash('error', 'Director not found.');
            Helper::redirect('modules/directors/directory');
        }

        $db = \Core\Database::getInstance();
        $sectors = $db->query("SELECT name FROM agricultural_sectors ORDER BY name")->fetchAll();

        $this->render('directors/edit', [
            'pageTitle' => 'Edit Director: ' . $director['full_name'],
            'activeNav' => 'directory',
            'director' => $director,
            'customers' => [],
            'sectors' => $sectors
        ]);
    }

    public function update(): void {
        Auth::requirePermission('parties.edit');
        $this->validateCsrf();

        $id = (int)$_POST['id'];
        $director = $this->directorModel->getById($id);

        if (!$director) {
            Session::setFlash('error', 'Director not found.');
            Helper::redirect('modules/directors/directory');
        }

        $directorData = [
            'member_no' => trim($_POST['member_no'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'nic' => trim($_POST['nic'] ?? ''),
            'dob' => $_POST['dob'] ?? '',
            'gender' => $_POST['gender'] ?? 'Male',
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'whatsapp' => trim($_POST['whatsapp'] ?? ''),
            'agricultural_sector' => trim($_POST['agricultural_sector'] ?? ''),
            'heir_name' => trim($_POST['heir_name'] ?? ''),
            'heir_address' => trim($_POST['heir_address'] ?? ''),
            'heir_nic' => trim($_POST['heir_nic'] ?? ''),
            'heir_contact_number' => trim($_POST['heir_contact_number'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'status' => $_POST['status'] ?? $director['status'],
            'notes' => trim($_POST['notes'] ?? ''),
            'party_id' => !empty($_POST['party_id']) ? (int)$_POST['party_id'] : $director['party_id']
        ];
        
        $db = Database::getInstance();
        
        // Ensure no duplicate NIC exists
        $nicExists = $db->prepare("SELECT id FROM coop_members WHERE nic = :nic AND member_type = 'DIRECTOR' AND id != :id");
        $nicExists->execute(['nic' => $directorData['nic'], 'id' => $id]);
        if ($nicExists->fetch()) {
            Session::setFlash('error', 'A director with this NIC is already registered.');
            Helper::redirect('modules/directors/edit?id=' . $id);
        }

        // Ensure registration number is provided and unique
        if (empty($directorData['member_no'])) {
            Session::setFlash('error', 'Registration number is required.');
            Helper::redirect('modules/directors/edit?id=' . $id);
        }
        $noExists = $db->prepare("SELECT id FROM coop_members WHERE member_no = :no AND id != :id");
        $noExists->execute(['no' => $directorData['member_no'], 'id' => $id]);
        if ($noExists->fetch()) {
            Session::setFlash('error', 'This Registration Number is already in use.');
            Helper::redirect('modules/directors/edit?id=' . $id);
        }

        try {
            if (!empty($directorData['agricultural_sector'])) {
                $stmt = $db->prepare("INSERT IGNORE INTO agricultural_sectors (name) VALUES (:name)");
                $stmt->execute(['name' => $directorData['agricultural_sector']]);
            }
            $this->directorModel->update($id, $directorData);
            Session::setFlash('success', 'Director updated successfully!');
            Helper::redirect('modules/directors/view?id=' . $id);
        } catch (\Exception $e) {
            Session::setFlash('error', 'Update failed: ' . $e->getMessage());
            Helper::redirect('modules/directors/edit?id=' . $id);
        }
    }

    public function delete(): void {
        Auth::requirePermission('parties.deactivate');
        $this->validateCsrf();

        $id = (int)$_POST['id'];
        $db = Database::getInstance();
        
        try {
            $stmt = $db->prepare("UPDATE coop_members SET status = 'INACTIVE' WHERE id = :id AND member_type = 'DIRECTOR'");
            $stmt->execute(['id' => $id]);
            Session::setFlash('success', 'Director marked as inactive.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Deletion failed: ' . $e->getMessage());
        }
        
        Helper::redirect('modules/directors/directory');
    }

    public function linkCustomer(): void {
        Auth::requirePermission('parties.edit');
        $this->validateCsrf();

        $directorId = (int)$_POST['director_id'];
        $customerId = !empty($_POST['party_id']) ? (int)$_POST['party_id'] : null;

        $db = Database::getInstance();

        try {
            if (!$customerId) {
                // If director does not exist as a Customer, auto-create a Customer profile
                $director = $this->directorModel->getById($directorId);
                $partyData = [
                    'name' => $director['full_name'],
                    'party_type' => 'CUSTOMER',
                    'contact_person' => $director['full_name'],
                    'nic_reg_no' => $director['nic'],
                    'phone' => $director['phone'],
                    'email' => null,
                    'address' => $director['address'],
                    'city' => $director['city'],
                    'credit_limit' => 0.00,
                    'credit_days' => 0,
                    'status' => 'active',
                    'created_by' => Auth::id() ?? 1
                ];
                $customerId = $this->partyModel->create($partyData);
            }

            $db->prepare("UPDATE coop_members SET party_id = :party_id WHERE id = :id AND member_type = 'DIRECTOR'")
               ->execute(['party_id' => $customerId, 'id' => $directorId]);

            Session::setFlash('success', 'Director linked to Customer profile successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Linking failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/directors/view?id=' . $directorId);
    }
}

