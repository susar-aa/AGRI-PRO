<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\Party;
use App\Models\PartyLedger;
use App\Services\OpeningBalanceEngine;
use App\Services\AuditService;

class PartyController extends Controller {
    private Party $partyModel;
    private PartyLedger $partyLedgerModel;

    public function __construct() {
        $this->partyModel = new Party();
        $this->partyLedgerModel = new PartyLedger();
    }

    public function index(): void {
        Auth::requirePermission('parties.view');

        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'party_type' => $_GET['party_type'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $parties = $this->partyModel->getAll($filters, $limit, $offset);
        $totalItems = $this->partyModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        $this->render('parties/index', [
            'pageTitle' => 'Central Business Contacts Directory',
            'activeNav' => 'parties',
            'parties' => $parties,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function customers(): void {
        Auth::requirePermission('parties.view');

        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'status' => $_GET['status'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $customers = $this->partyModel->getCustomers($filters, $limit, $offset);
        $totalItems = $this->partyModel->getCustomersCount($filters);
        $totalPages = ceil($totalItems / $limit);

        $this->render('parties/customers', [
            'pageTitle' => 'Cooperative Customer Ledger Accounts',
            'activeNav' => 'customers',
            'customers' => $customers,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function suppliers(): void {
        Auth::requirePermission('parties.view');

        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'status' => $_GET['status'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $suppliers = $this->partyModel->getSuppliers($filters, $limit, $offset);
        $totalItems = $this->partyModel->getSuppliersCount($filters);
        $totalPages = ceil($totalItems / $limit);

        $this->render('parties/suppliers', [
            'pageTitle' => 'Cooperative Supplier Ledger Accounts',
            'activeNav' => 'suppliers',
            'suppliers' => $suppliers,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function staff(): void {
        Auth::requirePermission('parties.view');

        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'status' => $_GET['status'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $staff = $this->partyModel->getStaff($filters, $limit, $offset);
        $totalItems = $this->partyModel->getStaffCount($filters);
        $totalPages = ceil($totalItems / $limit);

        $this->render('parties/staff', [
            'pageTitle' => 'Cooperative Staff & Employee Directory',
            'activeNav' => 'staff',
            'staff' => $staff,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function view(): void {
        Auth::requirePermission('parties.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $party = $this->partyModel->getById($id);

        if (!$party) {
            Session::setFlash('error', 'Business party contact not found.');
            Helper::redirect('parties');
        }

        // Fetch audit logs
        $db = \Core\Database::getInstance();
        $stmt = $db->prepare("
            SELECT al.*, u.full_name 
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE al.module = 'parties' AND al.record_id = :id
            ORDER BY al.id DESC
        ");
        $stmt->execute(['id' => $id]);
        $auditLogs = $stmt->fetchAll();

        // Fetch Party Ledger details (Stage 5B)
        $ledgerEntries = $this->partyLedgerModel->getLedgerEntries($id, $party['party_type']);
        $currentBalance = $this->partyLedgerModel->calculateBalance($id, $party['party_type']);
        $openingBalanceVal = $this->partyLedgerModel->getOpeningBalance($id);

        // Fetch posted opening balance record if exists
        $obStmt = $db->prepare("SELECT * FROM party_opening_balances WHERE party_id = :party_id AND status = 'posted' LIMIT 1");
        $obStmt->execute(['party_id' => $id]);
        $postedOpeningBalance = $obStmt->fetch() ?: null;

        $this->render('parties/view', [
            'pageTitle' => 'Party Profile: ' . $party['name'],
            'activeNav' => 'parties',
            'party' => $party,
            'auditLogs' => $auditLogs,
            'ledgerEntries' => $ledgerEntries,
            'currentBalance' => $currentBalance,
            'openingBalanceVal' => $openingBalanceVal,
            'postedOpeningBalance' => $postedOpeningBalance
        ]);
    }

    public function create(): void {
        Auth::requirePermission('parties.create');

        $type = $_GET['prefill_type'] ?? 'CUSTOMER';
        $view = 'parties/create_customer';
        $title = 'Register Customer';
        
        if ($type === 'SUPPLIER') {
            $view = 'parties/create_supplier';
            $title = 'Register Supplier';
        } elseif ($type === 'EMPLOYEE') {
            $view = 'parties/create_staff';
            $title = 'Register Staff';
        }

        $activities = [];
        if ($type === 'CUSTOMER') {
            $activities = (new \App\Models\CostCenter())->getAll();
        }

        $this->render($view, [
            'pageTitle' => $title,
            'activeNav' => 'parties',
            'activities' => $activities
        ]);
    }

    public function store(): void {
        Auth::requirePermission('parties.create');
        $this->validateCsrf();

        $partyData = [
            'name' => trim($_POST['name'] ?? ''),
            'party_type' => $_POST['party_type'] ?? '',
            'contact_person' => trim($_POST['contact_person'] ?? ''),
            'nic_reg_no' => trim($_POST['nic_reg_no'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'district' => trim($_POST['district'] ?? ''),
            'credit_limit' => (float)($_POST['credit_limit'] ?? 0.00),
            'credit_days' => (int)($_POST['credit_days'] ?? 0),
            'payment_terms' => trim($_POST['payment_terms'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'customer_type' => !empty($_POST['customer_type']) ? $_POST['customer_type'] : null,
            'supplier_type' => !empty($_POST['supplier_type']) ? $_POST['supplier_type'] : null,
            'customer_activity_id' => !empty($_POST['customer_activity_id']) ? (int)$_POST['customer_activity_id'] : null,
            'status' => 'active',
            'created_by' => Auth::id() ?? 1
        ];

        if (empty($partyData['name']) || empty($partyData['party_type'])) {
            Session::setFlash('error', 'Name and Party Type are required fields.');
            $redirectType = urlencode($partyData['party_type'] ?: 'CUSTOMER');
            Helper::redirect('parties/create?prefill_type=' . $redirectType);
        }

        try {
            $partyId = $this->partyModel->create($partyData);
            
            $code = $this->partyModel->getById($partyId)['party_code'];

            AuditService::log('create_party', 'parties', $partyId, null, [
                'party_code' => $code,
                'name' => $partyData['name'],
                'party_type' => $partyData['party_type']
            ]);

            Session::setFlash('success', "Business party successfully created! (Code: {$code})");
            Helper::redirect('parties/view?id=' . $partyId);
        } catch (\Exception $e) {
            Session::setFlash('error', 'Recording failed: ' . $e->getMessage());
            $redirectType = urlencode($partyData['party_type'] ?: 'CUSTOMER');
            Helper::redirect('parties/create?prefill_type=' . $redirectType);
        }
    }

    public function edit(): void {
        Auth::requirePermission('parties.edit');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $party = $this->partyModel->getById($id);

        if (!$party) {
            Session::setFlash('error', 'Business party not found.');
            Helper::redirect('parties');
        }

        $type = $party['party_type'];
        $view = 'parties/edit_customer';
        $title = 'Edit Customer Profile';
        
        if ($type === 'SUPPLIER' || $type === 'BOTH') {
            $view = 'parties/edit_supplier';
            $title = 'Edit Supplier Profile';
        } elseif ($type === 'EMPLOYEE') {
            $view = 'parties/edit_staff';
            $title = 'Edit Staff Profile';
        }

        $activities = [];
        if ($type === 'CUSTOMER' || $type === 'BOTH') {
            $activities = (new \App\Models\CostCenter())->getAll();
        }

        $this->render($view, [
            'pageTitle' => $title,
            'activeNav' => 'parties',
            'party' => $party,
            'activities' => $activities
        ]);
    }

    public function update(): void {
        Auth::requirePermission('parties.edit');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $party = $this->partyModel->getById($id);

        if (!$party) {
            Session::setFlash('error', 'Business party not found.');
            Helper::redirect('parties');
        }

        $partyData = [
            'name' => trim($_POST['name'] ?? ''),
            'party_type' => $_POST['party_type'] ?? '',
            'contact_person' => trim($_POST['contact_person'] ?? ''),
            'nic_reg_no' => trim($_POST['nic_reg_no'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'district' => trim($_POST['district'] ?? ''),
            'credit_limit' => (float)($_POST['credit_limit'] ?? 0.00),
            'credit_days' => (int)($_POST['credit_days'] ?? 0),
            'payment_terms' => trim($_POST['payment_terms'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'customer_type' => !empty($_POST['customer_type']) ? $_POST['customer_type'] : null,
            'supplier_type' => !empty($_POST['supplier_type']) ? $_POST['supplier_type'] : null,
            'customer_activity_id' => !empty($_POST['customer_activity_id']) ? (int)$_POST['customer_activity_id'] : null,
            'status' => $_POST['status'] ?? 'active'
        ];

        if (empty($partyData['name']) || empty($partyData['party_type'])) {
            Session::setFlash('error', 'Name and Party Type are required fields.');
            Helper::redirect('parties/edit?id=' . $id);
        }

        try {
            $this->partyModel->update($id, $partyData);

            AuditService::log('update_party', 'parties', $id, null, [
                'party_code' => $party['party_code'],
                'name' => $partyData['name'],
                'party_type' => $partyData['party_type']
            ]);

            Session::setFlash('success', 'Business party profile successfully updated.');
            Helper::redirect('parties/view?id=' . $id);
        } catch (\Exception $e) {
            Session::setFlash('error', 'Update failed: ' . $e->getMessage());
            Helper::redirect('parties/edit?id=' . $id);
        }
    }

    public function deactivate(): void {
        Auth::requirePermission('parties.deactivate');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $party = $this->partyModel->getById($id);

        if (!$party) {
            Session::setFlash('error', 'Business party not found.');
            Helper::redirect('parties');
        }

        try {
            if ($this->partyModel->deactivate($id)) {
                AuditService::log('deactivate_party', 'parties', $id, null, ['party_code' => $party['party_code']]);
                Session::setFlash('success', 'Business party successfully deactivated.');
            } else {
                Session::setFlash('error', 'Deactivation failed.');
            }
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        Helper::redirect('parties/view?id=' . $id);
    }

    public function openingBalance(): void {
        $id = !empty($_GET['party_id']) ? (int)$_GET['party_id'] : 0;
        $party = $this->partyModel->getById($id);

        if (!$party) {
            Session::setFlash('error', 'Business party contact not found.');
            Helper::redirect('parties');
        }

        // Verify correct permission depending on role
        if ($party['party_type'] === 'CUSTOMER') {
            Auth::requirePermission('customer.opening_balance');
        } elseif ($party['party_type'] === 'SUPPLIER') {
            Auth::requirePermission('supplier.opening_balance');
        } else {
            if (!Auth::hasPermission('customer.opening_balance') && !Auth::hasPermission('supplier.opening_balance')) {
                Auth::requirePermission('customer.opening_balance');
            }
        }

        $this->render('parties/opening_balance', [
            'pageTitle' => 'Record Opening Balance — ' . $party['name'],
            'activeNav' => 'parties',
            'party' => $party
        ]);
    }

    public function storeOpeningBalance(): void {
        $partyId = !empty($_POST['party_id']) ? (int)$_POST['party_id'] : 0;
        $party = $this->partyModel->getById($partyId);

        if (!$party) {
            Session::setFlash('error', 'Business party contact not found.');
            Helper::redirect('parties');
        }

        if ($party['party_type'] === 'CUSTOMER') {
            Auth::requirePermission('customer.opening_balance');
        } else {
            Auth::requirePermission('supplier.opening_balance');
        }

        $this->validateCsrf();

        $data = [
            'party_id' => $partyId,
            'type' => $_POST['type'] ?? '',
            'amount' => (float)($_POST['amount'] ?? 0),
            'balance_date' => $_POST['balance_date'] ?? date('Y-m-d'),
            'description' => trim($_POST['description'] ?? 'Opening Balance Entry')
        ];

        try {
            OpeningBalanceEngine::postOpeningBalance($data);
            Session::setFlash('success', 'Opening balance successfully posted to accounting ledger.');
            Helper::redirect('parties/view?id=' . $partyId);
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to post opening balance: ' . $e->getMessage());
            Helper::redirect('parties/opening-balance?party_id=' . $partyId);
        }
    }

    public function reverseOpeningBalance(): void {
        $obId = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $reason = trim($_POST['reversal_reason'] ?? '');

        if (empty($reason)) {
            Session::setFlash('error', 'Reversal reason is required.');
            Helper::redirect('parties');
        }

        $db = \Core\Database::getInstance();
        $stmt = $db->prepare("SELECT party_id FROM party_opening_balances WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $obId]);
        $partyId = (int)$stmt->fetchColumn();

        if (!$partyId) {
            Session::setFlash('error', 'Opening balance record not found.');
            Helper::redirect('parties');
        }

        $party = $this->partyModel->getById($partyId);
        if ($party['party_type'] === 'CUSTOMER') {
            Auth::requirePermission('customer.opening_balance.reverse');
        } else {
            Auth::requirePermission('supplier.opening_balance.reverse');
        }

        $this->validateCsrf();

        try {
            OpeningBalanceEngine::reverseOpeningBalance($obId, $reason);
            Session::setFlash('success', 'Opening balance reversed successfully. Accounting adjustments posted.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to reverse opening balance: ' . $e->getMessage());
        }

        Helper::redirect('parties/view?id=' . $partyId);
    }
}
