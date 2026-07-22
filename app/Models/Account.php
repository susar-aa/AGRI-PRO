<?php
namespace App\Models;

use Core\Model;
use PDO;

class Account extends Model {

    public function getAllHierarchical(): array {
        $stmt = $this->db->query("
            SELECT a.*, at.name AS account_type_name, p.account_code AS parent_code, p.account_name AS parent_name
            FROM accounts a
            JOIN account_types at ON a.account_type_id = at.id
            LEFT JOIN accounts p ON a.parent_id = p.id
            WHERE a.deleted_at IS NULL
            ORDER BY a.account_code ASC
        ");
        $allAccounts = $stmt->fetchAll();

        // Build tree
        return self::buildTree($allAccounts, null);
    }

    public function getAllFlat(): array {
        $stmt = $this->db->query("
            SELECT a.*, at.name AS account_type_name
            FROM accounts a
            JOIN account_types at ON a.account_type_id = at.id
            WHERE a.deleted_at IS NULL
            ORDER BY a.account_code ASC
        ");
        return $stmt->fetchAll();
    }

    public static function buildTree(array $accounts, ?int $parentId = null): array {
        $branch = [];
        foreach ($accounts as $account) {
            if ($account['parent_id'] == $parentId) {
                $children = self::buildTree($accounts, $account['id']);
                if ($children) {
                    $account['children'] = $children;
                } else {
                    $account['children'] = [];
                }
                $branch[] = $account;
            }
        }
        return $branch;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT a.*, at.name AS account_type_name 
            FROM accounts a
            JOIN account_types at ON a.account_type_id = at.id
            WHERE a.id = :id AND a.deleted_at IS NULL LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO accounts 
            (account_code, account_name, parent_id, account_type_id, category, normal_balance, is_system, is_active, allow_manual_posting, description)
            VALUES 
            (:account_code, :account_name, :parent_id, :account_type_id, :category, :normal_balance, 0, :is_active, :allow_manual_posting, :description)
        ");

        $stmt->execute([
            'account_code' => $data['account_code'],
            'account_name' => $data['account_name'],
            'parent_id' => !empty($data['parent_id']) ? $data['parent_id'] : null,
            'account_type_id' => $data['account_type_id'],
            'category' => $data['category'],
            'normal_balance' => $data['normal_balance'],
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
            'allow_manual_posting' => isset($data['allow_manual_posting']) ? (int)$data['allow_manual_posting'] : 1,
            'description' => $data['description'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE accounts SET
                account_name = :account_name,
                is_active = :is_active,
                allow_manual_posting = :allow_manual_posting,
                description = :description,
                updated_at = NOW()
            WHERE id = :id AND is_system = 0
        ");

        return $stmt->execute([
            'id' => $id,
            'account_name' => $data['account_name'],
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
            'allow_manual_posting' => isset($data['allow_manual_posting']) ? (int)$data['allow_manual_posting'] : 1,
            'description' => $data['description'] ?? null
        ]);
    }

    public function hasTransactions(int $accountId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM journal_lines WHERE account_id = :id");
        $stmt->execute(['id' => $accountId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getAccountTypes(): array {
        return $this->db->query("SELECT * FROM account_types ORDER BY id ASC")->fetchAll();
    }
}
