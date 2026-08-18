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

        $this->render('directors/register', [
            'pageTitle' => 'Register New Society director',
            'activeNav' => 'directory',
            'customers' => []
        ]);
    }

    public function store(): void {
        Auth::requirePermission('parties.create');
        $this->validateCsrf();

        $db = Database::getInstance();
        $isPaid = ($_POST['payment_status'] ?? 'UNPAID') === 'PAID';
        $paymentMethod = $_POST['payment_method'] ?? 'Unpaid';
        $fee = (float)($_POST['registration_fee'] ?? 0);
        $sharesFee = (float)($_POST['shares_fee'] ?? 0);

        $directorData = [
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
            'status' => 'ACTIVE',
            'registration_fee' => $fee,
            'shares_fee' => $sharesFee,
            'payment_method' => $paymentMethod,
            'payment_status' => $_POST['payment_status'] ?? 'UNPAID',
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

        try {
            $db->beginTransaction();

            $journalEntryId = null;

            if ($isPaid && ($fee > 0 || $sharesFee > 0)) {
                $totalPaid = $fee + $sharesFee;
                // Post double-entry registration journal entry
                
                $debitAccountGL = null;
                $refNo = 'DIR-FEE-' . time();

                if ($paymentMethod === 'Cash') {
                    // Dr Cash Drawer GL (first active cash account)
                    $debitAccountGL = (int)$db->query("SELECT account_id FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                    // Update cash balance
                    $cashAccId = $db->query("SELECT id FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                    $db->prepare("UPDATE cash_accounts SET current_balance = current_balance + :amt WHERE id = :id")->execute(['amt' => $totalPaid, 'id' => $cashAccId]);
                } elseif ($paymentMethod === 'Bank Transfer') {
                    // Dr Bank Account GL (first active bank account)
                    $debitAccountGL = (int)$db->query("SELECT account_id FROM bank_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                    // Update bank balance
                    $bankAccId = $db->query("SELECT id FROM bank_accounts WHERE status = 'active' LIMIT 1")->fetchColumn();
                    $db->prepare("UPDATE bank_accounts SET current_balance = current_balance + :amt WHERE id = :id")->execute(['amt' => $totalPaid, 'id' => $bankAccId]);
                } elseif ($paymentMethod === 'Cheque') {
                    // Dr Cheque In Hand (GL code 1115 / Undeposited Cheques)
                    $debitAccountGL = (int)$db->query("SELECT id FROM accounts WHERE account_code = '1115'")->fetchColumn();
                    
                    // Add cheque entry to cheques table
                    $chequeNo = $_POST['cheque_number'] ?? 'CHQ-' . time();
                    $chqStmt = $db->prepare("
                        INSERT INTO cheques (cheque_number, cheque_type, party_id, bank_name, cheque_date, amount, received_issued_date, status, created_by)
                        VALUES (:num, 'RECEIVED', :party, :bank, :dt, :amt, :rcv_dt, 'RECEIVED', :by)
                    ");
                    $chqStmt->execute([
                        'num' => $chequeNo,
                        'party' => $directorData['party_id'] ?: 1, // Fallback to cash customer / first customer if unlinked
                        'bank' => $_POST['cheque_bank'] ?? 'Main Bank',
                        'dt' => date('Y-m-d'),
                        'amt' => $totalPaid,
                        'rcv_dt' => date('Y-m-d'),
                        'by' => Auth::id() ?? 1
                    ]);
                }

                if ($debitAccountGL) {
                    $costCenterId = (int)$db->query("SELECT id FROM cost_centers LIMIT 1")->fetchColumn();
                    $lines = [
                        [
                            'account_id' => $debitAccountGL,
                            'debit' => $totalPaid,
                            'credit' => 0.00,
                            'description' => "directorship Fees Received"
                        ]
                    ];
                    
                    if ($fee > 0) {
                        $revAccountGL = (int)$db->query("SELECT id FROM accounts WHERE account_code = '4250'")->fetchColumn();
                        if ($revAccountGL) {
                            $lines[] = [
                                'account_id' => $revAccountGL,
                                'debit' => 0.00,
                                'credit' => $fee,
                                'description' => "directorship Registration Fee"
                            ];
                        }
                    }
                    if ($sharesFee > 0) {
                        $shareAccountGL = (int)$db->query("SELECT id FROM accounts WHERE account_code = '3100'")->fetchColumn();
                        if ($shareAccountGL) {
                            $lines[] = [
                                'account_id' => $shareAccountGL,
                                'debit' => 0.00,
                                'credit' => $sharesFee,
                                'description' => "director Share Capital"
                            ];
                        }
                    }

                    $journalData = [
                        'transaction_date' => $directorData['registration_date'],
                        'description' => "Registration & Share Fees for director: " . $directorData['full_name'],
                        'reference' => $refNo,
                        'source_module' => 'finance',
                        'cost_center_id' => $costCenterId,
                        'status' => 'approved',
                        'lines' => $lines
                    ];
                    $journalEntryId = AccountingEngine::postJournalEntry($journalData);
                }
            }

            $directorData['journal_entry_id'] = $journalEntryId;
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
            $rentalModel = new \App\Models\MachineryRentalModel();
            $ledgerModel = new \App\Models\PartyLedger();

            $invoices = $invoiceModel->getAll(['customer_id' => $partyId], 50);
            $payments = $paymentModel->getAll(['party_id' => $partyId], 50);
            $rentals = $rentalModel->getAll(['customer_id' => $partyId], 50);
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

