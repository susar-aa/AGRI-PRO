<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use Core\Database;
use App\Models\MemberModel;
use App\Models\Party;
use App\Services\AccountingEngine;
use App\Services\AuditService;

class MemberController extends Controller {
    private MemberModel $memberModel;
    private Party $partyModel;

    public function __construct() {
        $this->memberModel = new MemberModel();
        $this->partyModel = new Party();
    }

    public function directory(): void {
        Auth::requirePermission('parties.view');

        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? '';

        // Fetch Society Members
        $filters = ['search' => $search];
        if ($status) $filters['status'] = strtoupper($status);
        $members = $this->memberModel->getAll($filters, 100);

        $this->render('members/directory', [
            'pageTitle' => 'Members',
            'activeNav' => 'directory',
            'search' => $search,
            'status' => $status,
            'members' => $members
        ]);
    }

    public function registerForm(): void {
        Auth::requirePermission('parties.create');

        $db = \Core\Database::getInstance();
        $sectors = $db->query("SELECT name FROM agricultural_sectors ORDER BY name")->fetchAll();

        $this->render('members/register', [
            'pageTitle' => 'Register Member',
            'activeNav' => 'directory',
            'member_no' => $this->memberModel->generateMembershipNumber(),
            'sectors' => $sectors
        ]);
    }

    public function store(): void {
        Auth::requirePermission('parties.create');
        $this->validateCsrf();

        $db = Database::getInstance();
        $memberData = [
            'member_no' => trim($_POST['member_no'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'nic' => !empty(trim($_POST['nic'] ?? '')) ? trim($_POST['nic'] ?? '') : null,
            'dob' => !empty($_POST['dob']) ? $_POST['dob'] : null,
            'gender' => $_POST['gender'] ?? 'Male',
            'occupation' => trim($_POST['occupation'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'whatsapp' => trim($_POST['whatsapp'] ?? ''),
            'agricultural_sector' => implode(', ', array_filter(array_map('trim', $_POST['agricultural_sectors'] ?? []))),
            'heir_name' => trim($_POST['heir_name'] ?? ''),
            'heir_address' => trim($_POST['heir_address'] ?? ''),
            'heir_nic' => trim($_POST['heir_nic'] ?? ''),
            'heir_contact_number' => trim($_POST['heir_contact_number'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'registration_date' => !empty($_POST['registration_date']) ? $_POST['registration_date'] : date('Y-m-d'),
            'membership_type' => $_POST['membership_type'] ?? 'Ordinary',
            'status' => 'ACTIVE',
            'notes' => trim($_POST['notes'] ?? ''),
            'party_id' => !empty($_POST['party_id']) ? (int)$_POST['party_id'] : null
        ];

        // Ensure no duplicate NIC exists
        if (!empty($memberData['nic'])) {
            $nicExists = $db->prepare("SELECT id FROM coop_members WHERE nic = :nic AND member_type = 'MEMBER'");
            $nicExists->execute(['nic' => $memberData['nic']]);
            if ($nicExists->fetch()) {
                Session::setFlash('error', 'A member with this NIC is already registered.');
                Helper::redirect('modules/members/register');
            }
        }

        // Ensure registration number is provided and unique
        if (empty($memberData['member_no'])) {
            Session::setFlash('error', 'Registration number is required.');
            Helper::redirect('modules/members/register');
        }
        $noExists = $db->prepare("SELECT id FROM coop_members WHERE member_no = :no");
        $noExists->execute(['no' => $memberData['member_no']]);
        if ($noExists->fetch()) {
            Session::setFlash('error', 'This Registration Number is already in use.');
            Helper::redirect('modules/members/register');
        }

        try {
            $db->beginTransaction();

            foreach($_POST['agricultural_sectors'] ?? [] as $sec) {
                $sec = trim($sec);
                if (!empty($sec)) {
                    $stmt = $db->prepare("INSERT IGNORE INTO agricultural_sectors (name) VALUES (:name)");
                    $stmt->execute(['name' => $sec]);
                }
            }

            $memberId = $this->memberModel->create($memberData);

            $db->commit();
            Session::setFlash('success', 'Member registered successfully!');
            Helper::redirect('modules/members/view?id=' . $memberId);
        } catch (\Exception $e) {
            $db->rollBack();
            Session::setFlash('error', 'Registration failed: ' . $e->getMessage());
            Helper::redirect('modules/members/register');
        }
    }

    public function view(): void {
        Auth::requirePermission('parties.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $member = $this->memberModel->getById($id);

        if (!$member) {
            Session::setFlash('error', 'Member not found.');
            Helper::redirect('modules/members/directory');
        }

        $fixedDeposits = $this->memberModel->getFixedDepositsByMember($id);

        // Fetch recent posted payments
        $db = Database::getInstance();
        $journal = null;
        if ($member['journal_entry_id']) {
            $journal = $db->query("SELECT * FROM journal_entries WHERE id = " . (int)$member['journal_entry_id'])->fetch();
        }

        // Fetch Financial/System Activity if member is linked to a customer
        $invoices = [];
        $payments = [];
        $rentals = [];
        $ledgerEntries = [];

        if (!empty($member['party_id'])) {
            $partyId = (int)$member['party_id'];
            $invoiceModel = new \App\Models\InvoiceModel();
            $paymentModel = new \App\Models\ReceiptPaymentModel();
            
            $ledgerModel = new \App\Models\PartyLedger();

            $invoices = $invoiceModel->getAll(['customer_id' => $partyId], 50);
            $payments = $paymentModel->getAll(['party_id' => $partyId], 50);
            
            $ledgerEntries = $ledgerModel->getLedgerEntries($partyId, 'CUSTOMER');
        }

        $this->render('members/view', [
            'pageTitle' => 'Member Profile: ' . $member['full_name'],
            'activeNav' => 'directory',
            'member' => $member,
            'fixedDeposits' => $fixedDeposits,
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
        $member = $this->memberModel->getById($id);

        if (!$member) {
            Session::setFlash('error', 'Member not found.');
            Helper::redirect('modules/members/directory');
        }

        $db = \Core\Database::getInstance();
        $sectors = $db->query("SELECT name FROM agricultural_sectors ORDER BY name")->fetchAll();

        $this->render('members/edit', [
            'pageTitle' => 'Edit Member: ' . $member['full_name'],
            'activeNav' => 'directory',
            'member' => $member,
            'customers' => [],
            'sectors' => $sectors
        ]);
    }

    public function update(): void {
        Auth::requirePermission('parties.edit');
        $this->validateCsrf();

        $id = (int)$_POST['id'];
        $member = $this->memberModel->getById($id);

        if (!$member) {
            Session::setFlash('error', 'Member not found.');
            Helper::redirect('modules/members/directory');
        }

        $memberData = [
            'member_no' => trim($_POST['member_no'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'nic' => !empty(trim($_POST['nic'] ?? '')) ? trim($_POST['nic'] ?? '') : null,
            'dob' => !empty($_POST['dob']) ? $_POST['dob'] : null,
            'gender' => $_POST['gender'] ?? 'Male',
            'occupation' => trim($_POST['occupation'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'whatsapp' => trim($_POST['whatsapp'] ?? ''),
            'agricultural_sector' => implode(', ', array_filter(array_map('trim', $_POST['agricultural_sectors'] ?? []))),
            'heir_name' => trim($_POST['heir_name'] ?? ''),
            'heir_address' => trim($_POST['heir_address'] ?? ''),
            'heir_nic' => trim($_POST['heir_nic'] ?? ''),
            'heir_contact_number' => trim($_POST['heir_contact_number'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'status' => $_POST['status'] ?? $member['status'],
            'notes' => trim($_POST['notes'] ?? ''),
            'party_id' => !empty($_POST['party_id']) ? (int)$_POST['party_id'] : $member['party_id']
        ];
        
        $db = Database::getInstance();
        
        // Ensure no duplicate NIC exists
        if (!empty($memberData['nic'])) {
            $nicExists = $db->prepare("SELECT id FROM coop_members WHERE nic = :nic AND member_type = 'MEMBER' AND id != :id");
            $nicExists->execute(['nic' => $memberData['nic'], 'id' => $id]);
            if ($nicExists->fetch()) {
                Session::setFlash('error', 'A member with this NIC is already registered.');
                Helper::redirect("modules/members/edit?id=$id");
            }
        }

        // Ensure registration number is provided and unique
        if (empty($memberData['member_no'])) {
            Session::setFlash('error', 'Registration number is required.');
            Helper::redirect('modules/members/edit?id=' . $id);
        }
        $noExists = $db->prepare("SELECT id FROM coop_members WHERE member_no = :no AND id != :id");
        $noExists->execute(['no' => $memberData['member_no'], 'id' => $id]);
        if ($noExists->fetch()) {
            Session::setFlash('error', 'This Registration Number is already in use.');
            Helper::redirect('modules/members/edit?id=' . $id);
        }

        try {
            foreach($_POST['agricultural_sectors'] ?? [] as $sec) {
                $sec = trim($sec);
                if (!empty($sec)) {
                    $stmt = $db->prepare("INSERT IGNORE INTO agricultural_sectors (name) VALUES (:name)");
                    $stmt->execute(['name' => $sec]);
                }
            }
            $this->memberModel->update($id, $memberData);
            Session::setFlash('success', 'Member updated successfully!');
            Helper::redirect('modules/members/view?id=' . $id);
        } catch (\Exception $e) {
            Session::setFlash('error', 'Update failed: ' . $e->getMessage());
            Helper::redirect('modules/members/edit?id=' . $id);
        }
    }

    public function delete(): void {
        Auth::requirePermission('parties.deactivate');
        $this->validateCsrf();

        $id = (int)$_POST['id'];
        $db = Database::getInstance();
        
        try {
            $stmt = $db->prepare("UPDATE coop_members SET status = 'INACTIVE' WHERE id = :id AND member_type = 'MEMBER'");
            $stmt->execute(['id' => $id]);
            Session::setFlash('success', 'Member marked as inactive.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Deletion failed: ' . $e->getMessage());
        }
        
        Helper::redirect('modules/members/directory');
    }

    public function linkCustomer(): void {
        Auth::requirePermission('parties.edit');
        $this->validateCsrf();

        $memberId = (int)$_POST['member_id'];
        $customerId = !empty($_POST['party_id']) ? (int)$_POST['party_id'] : null;

        $db = Database::getInstance();

        try {
            if (!$customerId) {
                // If member does not exist as a Customer, auto-create a Customer profile
                $member = $this->memberModel->getById($memberId);
                $partyData = [
                    'name' => $member['full_name'],
                    'party_type' => 'CUSTOMER',
                    'contact_person' => $member['full_name'],
                    'nic_reg_no' => $member['nic'],
                    'phone' => $member['phone'],
                    'email' => null,
                    'address' => $member['address'],
                    'city' => $member['city'],
                    'credit_limit' => 0.00,
                    'credit_days' => 0,
                    'status' => 'active',
                    'created_by' => Auth::id() ?? 1
                ];
                $customerId = $this->partyModel->create($partyData);
            }

            $db->prepare("UPDATE coop_members SET party_id = :party_id WHERE id = :id AND member_type = 'MEMBER'")
               ->execute(['party_id' => $customerId, 'id' => $memberId]);

            Session::setFlash('success', 'Member linked to Customer profile successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Linking failed: ' . $e->getMessage());
        }

        Helper::redirect('modules/members/view?id=' . $memberId);
    }
}
