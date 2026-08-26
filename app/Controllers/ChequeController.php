<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\ChequeModel;
use App\Services\ChequeDepositEngine;

class ChequeController extends Controller {
    private ChequeModel $chequeModel;

    public function __construct() {
        $this->chequeModel = new ChequeModel();
    }

    public function index(): void {
        Auth::requirePermission('cheques.view');

        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'status' => $_GET['status'] ?? '',
            'party_id' => $_GET['party_id'] ?? ''
        ];

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $filtersReceived = $filters;
        $filtersReceived['cheque_type'] = 'RECEIVED';
        $receivedCheques = $this->chequeModel->getAll($filtersReceived, $limit, $offset);
        $totalReceived = $this->chequeModel->getCount($filtersReceived);
        
        $filtersIssued = $filters;
        $filtersIssued['cheque_type'] = 'ISSUED';
        $issuedCheques = $this->chequeModel->getAll($filtersIssued, $limit, $offset);
        $totalIssued = $this->chequeModel->getCount($filtersIssued);

        $totalPages = ceil(max($totalReceived, $totalIssued) / $limit);

        // Fetch active customers and suppliers for filtering dropdown
        $db = \Core\Database::getInstance();
        $parties = $db->query("SELECT id, party_code, name, party_type FROM parties WHERE party_type IN ('CUSTOMER', 'SUPPLIER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();

        $this->render('cheques/index', [
            'pageTitle' => 'Cheques Registry',
            'activeNav' => 'cheques',
            'receivedCheques' => $receivedCheques,
            'issuedCheques' => $issuedCheques,
            'filters' => $filters,
            'parties' => $parties,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => max($totalReceived, $totalIssued)
            ]
        ]);
    }

    public function clear(): void {
        Auth::requirePermission('cheques.update_status');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            ChequeDepositEngine::markChequeCleared($id);
            Session::setFlash('success', 'Cheque successfully marked as CLEARED.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Action failed: ' . $e->getMessage());
        }

        Helper::redirect('cheques');
    }

    public function bounce(): void {
        Auth::requirePermission('cheques.update_status');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $reason = trim($_POST['reversal_reason'] ?? 'Cheque Bounced');

        try {
            ChequeDepositEngine::markChequeBounced($id, $reason);
            Session::setFlash('success', 'Cheque marked as BOUNCED. Original receipt reversed and customer balance restored.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Action failed: ' . $e->getMessage());
        }

        Helper::redirect('cheques');
    }

    public function cancel(): void {
        Auth::requirePermission('cheques.update_status');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            $db = \Core\Database::getInstance();
            $db->prepare("UPDATE cheques SET status = 'CANCELLED', updated_at = NOW() WHERE id = :id")->execute(['id' => $id]);
            Session::setFlash('success', 'Cheque successfully marked as CANCELLED.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Action failed: ' . $e->getMessage());
        }

        Helper::redirect('cheques');
    }

    public function pass(): void {
        Auth::requirePermission('cheques.update_status');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            \App\Services\ChequeDepositEngine::passIssuedCheque($id);
            Session::setFlash('success', 'Issued Cheque successfully marked as PASSED and bank balance updated.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Action failed: ' . $e->getMessage());
        }

        Helper::redirect('cheques');
    }

    public function return(): void {
        Auth::requirePermission('cheques.update_status');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $reason = trim($_POST['reversal_reason'] ?? 'Cheque Returned');

        try {
            \App\Services\ChequeDepositEngine::returnIssuedCheque($id, $reason);
            Session::setFlash('success', 'Issued Cheque marked as RETURNED. Supplier payment reversed.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Action failed: ' . $e->getMessage());
        }

        Helper::redirect('cheques');
    }
}
