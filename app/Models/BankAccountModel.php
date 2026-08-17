<?php
namespace App\Models;

use Core\Model;

class BankAccountModel extends Model {

    public function getAll(): array {
        $stmt = $this->db->prepare("
            SELECT ba.*, a.account_code, a.account_name AS coa_name
            FROM bank_accounts ba
            LEFT JOIN accounts a ON ba.account_id = a.id
            ORDER BY ba.bank_name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT ba.*, a.account_code, a.account_name AS coa_name
            FROM bank_accounts ba
            LEFT JOIN accounts a ON ba.account_id = a.id
            WHERE ba.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data): int {
        if (!empty($data['id'])) {
            $stmt = $this->db->prepare("
                UPDATE bank_accounts 
                SET bank_name = :bank_name, branch = :branch, account_number = :account_number, 
                    account_name = :account_name, swift_code = :swift_code, account_id = :account_id, 
                    status = :status, updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                'id' => (int)$data['id'],
                'bank_name' => $data['bank_name'],
                'branch' => $data['branch'] ?? null,
                'account_number' => $data['account_number'],
                'account_name' => $data['account_name'],
                'swift_code' => $data['swift_code'] ?? null,
                'account_id' => (int)$data['account_id'],
                'status' => $data['status'] ?? 'active'
            ]);
            return (int)$data['id'];
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO bank_accounts 
                (bank_name, branch, account_number, account_name, swift_code, account_id, current_balance, status)
                VALUES 
                (:bank_name, :branch, :account_number, :account_name, :swift_code, :account_id, :opening_balance, :status)
            ");
            $stmt->execute([
                'bank_name' => $data['bank_name'],
                'branch' => $data['branch'] ?? null,
                'account_number' => $data['account_number'],
                'account_name' => $data['account_name'],
                'swift_code' => $data['swift_code'] ?? null,
                'account_id' => (int)$data['account_id'],
                'opening_balance' => (float)($data['opening_balance'] ?? 0),
                'status' => $data['status'] ?? 'active'
            ]);
            return (int)$this->db->lastInsertId();
        }
    }

    public function getActiveAccounts(): array {
        $stmt = $this->db->prepare("SELECT * FROM bank_accounts WHERE status = 'active' ORDER BY bank_name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
