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

        $this->render('members/register', [
            'pageTitle' => 'Register New Society Member',
            'activeNav' => 'directory',
            'customers' => []
        ]);
    }

    public function store(): void {
        Auth::requirePermission('parties.create');
        $this->validateCsrf();

        $db = Database::getInstance();
        $memberData = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'nic' => trim($_POST['nic'] ?? ''),
            'dob' => $_POST['dob'] ?? '',
            'gender' => $_POST['gender'] ?? 'Male',
            'phone' => trim($_POST['phone'] ?? ''),
            'heir_name' => trim($_POST['heir_name'] ?? ''),
            'heir_address' => trim($_POST['heir_address'] ?? ''),
            'heir_nic' => trim($_POST['heir_nic'] ?? ''),
            'heir_contact_number' => trim($_POST['heir_contact_number'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'registration_date' => $_POST['registration_date'] ?? date('Y-m-d'),
            'membership_type' => $_POST['membership_type'] ?? 'Ordinary',
            'status' => 'ACTIVE',
            'notes' => trim($_POST['notes'] ?? ''),
            'party_id' => !empty($_POST['party_id']) ? (int)$_POST['party_id'] : null
        ];

        // Ensure no duplicate NIC exists
        $nicExists = $db->prepare("SELECT id FROM coop_members WHERE nic = :nic AND member_type = 'MEMBER'");
        $nicExists->execute(['nic' => $memberData['nic']]);
        if ($nicExists->fetch()) {
            Session::setFlash('error', 'A member with this NIC is already registered.');
            Helper::redirect('modules/members/register');
        }

        try {
            $db->beginTransaction();

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

        $this->render('members/edit', [
            'pageTitle' => 'Edit Member: ' . $member['full_name'],
            'activeNav' => 'directory',
            'member' => $member,
            'customers' => []
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
            'full_name' => trim($_POST['full_name'] ?? ''),
            'nic' => trim($_POST['nic'] ?? ''),
            'dob' => $_POST['dob'] ?? '',
            'gender' => $_POST['gender'] ?? 'Male',
            'phone' => trim($_POST['phone'] ?? ''),
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

        try {
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
