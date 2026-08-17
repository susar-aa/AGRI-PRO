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

        $cheques = $this->chequeModel->getAll($filters, $limit, $offset);
        $totalItems = $this->chequeModel->getCount($filters);
        $totalPages = ceil($totalItems / $limit);

        // Fetch active customers for filtering dropdown
        $db = \Core\Database::getInstance();
        $customers = $db->query("SELECT id, party_code, name FROM parties WHERE party_type IN ('CUSTOMER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();

        $this->render('cheques/index', [
            'pageTitle' => 'Cheques Registry',
            'activeNav' => 'cheques',
            'cheques' => $cheques,
            'filters' => $filters,
            'customers' => $customers,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'count' => $totalItems
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
}
