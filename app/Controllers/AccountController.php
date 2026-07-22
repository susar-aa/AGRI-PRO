<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Helper;
use App\Models\Account;

class AccountController extends Controller {
    private Account $accountModel;

    public function __construct() {
        $this->accountModel = new Account();
    }

    public function index(): void {
        Auth::requirePermission('coa.view');

        $accountsTree = $this->accountModel->getAllHierarchical();
        $flatAccounts = $this->accountModel->getAllFlat();
        $accountTypes = $this->accountModel->getAccountTypes();

        $this->render('accounting/coa', [
            'pageTitle' => 'Chart of Accounts (COA)',
            'activeNav' => 'coa',
            'accountsTree' => $accountsTree,
            'flatAccounts' => $flatAccounts,
            'accountTypes' => $accountTypes
        ]);
    }

    public function store(): void {
        Auth::requirePermission('coa.manage');
        $this->validateCsrf();

        $code = trim($_POST['account_code'] ?? '');
        $name = trim($_POST['account_name'] ?? '');
        $accountTypeId = (int)($_POST['account_type_id'] ?? 0);
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        if (empty($code) || empty($name) || $accountTypeId <= 0) {
            Session::setFlash('error', 'Please fill all required account fields.');
            Helper::redirect('accounting/coa');
        }

        // Determine category and normal balance from type
        $types = $this->accountModel->getAccountTypes();
        $selectedType = null;
        foreach ($types as $t) {
            if ($t['id'] == $accountTypeId) {
                $selectedType = $t;
                break;
            }
        }

        if (!$selectedType) {
            Session::setFlash('error', 'Invalid account type selected.');
            Helper::redirect('accounting/coa');
        }

        try {
            $this->accountModel->create([
                'account_code' => $code,
                'account_name' => $name,
                'parent_id' => $parentId,
                'account_type_id' => $accountTypeId,
                'category' => $selectedType['category'],
                'normal_balance' => $selectedType['normal_balance'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'allow_manual_posting' => isset($_POST['allow_manual_posting']) ? 1 : 0,
                'description' => trim($_POST['description'] ?? '')
            ]);

            \App\Services\AuditService::log('create_account', 'accounting', null, null, ['code' => $code, 'name' => $name]);
            Session::setFlash('success', "Account [{$code} - {$name}] created successfully!");
        } catch (\Exception $e) {
            Session::setFlash('error', "Failed to create account: " . $e->getMessage());
        }

        Helper::redirect('accounting/coa');
    }
}
