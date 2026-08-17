<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\DepositModel;
use App\Models\ChequeModel;
use App\Models\Expense; // for bank/cash account lists
use App\Services\ChequeDepositEngine;

class DepositController extends Controller {
    private DepositModel $depositModel;
    private ChequeModel $chequeModel;
    private Expense $expenseModel;

    public function __construct() {
        $this->depositModel = new DepositModel();
        $this->chequeModel = new ChequeModel();
        $this->expenseModel = new Expense();
    }

    public function index(): void {
        Auth::requirePermission('deposits.view');

        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'status' => $_GET['status'] ?? '',
            'bank_account_id' => $_GET['bank_account_id'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $deposits = $this->depositModel->getAll($filters, $limit, $offset);
        $totalItems = $this->depositModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        $bankAccounts = $this->expenseModel->getBankAccounts();

        $this->render('deposits/index', [
            'pageTitle' => 'Bank Deposits Journal',
            'activeNav' => 'deposits',
            'deposits' => $deposits,
            'filters' => $filters,
            'bankAccounts' => $bankAccounts,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
            ]
        ]);
    }

    public function view(): void {
        Auth::requirePermission('deposits.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $deposit = $this->depositModel->getById($id);

        if (!$deposit) {
            Session::setFlash('error', 'Deposit voucher not found.');
            Helper::redirect('deposits');
        }

        $this->render('deposits/view', [
            'pageTitle' => 'Deposit Voucher #' . $deposit['deposit_number'],
            'activeNav' => 'deposits',
            'deposit' => $deposit
        ]);
    }

    public function create(): void {
        Auth::requirePermission('deposits.create');

        $bankAccounts = $this->expenseModel->getBankAccounts();
        $cashAccounts = $this->expenseModel->getCashAccounts();
        $undepositedCheques = $this->chequeModel->getUndepositedCheques();

        $this->render('deposits/create', [
            'pageTitle' => 'New Bank Deposit',
            'activeNav' => 'deposits',
            'bankAccounts' => $bankAccounts,
            'cashAccounts' => $cashAccounts,
            'cheques' => $undepositedCheques
        ]);
    }

    public function store(): void {
        Auth::requirePermission('deposits.create');
        $this->validateCsrf();

        $data = [
            'bank_account_id' => !empty($_POST['bank_account_id']) ? (int)$_POST['bank_account_id'] : 0,
            'deposit_date' => $_POST['deposit_date'] ?? date('Y-m-d'),
            'description' => trim($_POST['description'] ?? 'Bank Deposit Entry'),
            'cash_amount' => (float)($_POST['cash_amount'] ?? 0),
            'cash_account_id' => !empty($_POST['cash_account_id']) ? (int)$_POST['cash_account_id'] : null,
            'cheque_ids' => $_POST['cheque_ids'] ?? []
        ];

        try {
            $depositId = ChequeDepositEngine::recordBankDeposit($data);

            if (!empty($_POST['action']) && $_POST['action'] === 'post') {
                Auth::requirePermission('deposits.post');
                ChequeDepositEngine::postBankDeposit($depositId);
                Session::setFlash('success', 'Deposit voucher created and posted successfully.');
            } else {
                Session::setFlash('success', 'Deposit voucher recorded as Draft.');
            }

            Helper::redirect('deposits/view?id=' . $depositId);

        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to record deposit: ' . $e->getMessage());
            Helper::redirect('deposits/create');
        }
    }

    public function post(): void {
        Auth::requirePermission('deposits.post');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            ChequeDepositEngine::postBankDeposit($id);
            Session::setFlash('success', 'Deposit voucher posted successfully to general ledger.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Posting failed: ' . $e->getMessage());
        }

        Helper::redirect('deposits/view?id=' . $id);
    }

    public function cancel(): void {
        Auth::requirePermission('deposits.cancel');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $reason = trim($_POST['reversal_reason'] ?? 'Voucher Cancelled');

        try {
            ChequeDepositEngine::cancelBankDeposit($id, $reason);
            Session::setFlash('success', 'Deposit voucher cancelled. Ledger adjustments posted.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Cancellation failed: ' . $e->getMessage());
        }

        Helper::redirect('deposits/view?id=' . $id);
    }
}
