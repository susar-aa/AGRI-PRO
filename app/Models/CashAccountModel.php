<?php
namespace App\Models;

use Core\Model;

class CashAccountModel extends Model {

    public function getAll(): array {
        $stmt = $this->db->prepare("
            SELECT ca.*, a.account_code, a.account_name AS coa_name
            FROM cash_accounts ca
            LEFT JOIN accounts a ON ca.account_id = a.id
            ORDER BY ca.name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getActiveAccounts(): array {
        $stmt = $this->db->prepare("SELECT * FROM cash_accounts WHERE status = 'active' ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
