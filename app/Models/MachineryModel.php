<?php
namespace App\Models;

use Core\Model;

class MachineryModel extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   aa.account_code AS asset_account_code, aa.account_name AS asset_account_name,
                   ra.account_code AS revenue_account_code, ra.account_name AS revenue_account_name
            FROM machinery m
            LEFT JOIN accounts aa ON m.asset_account_id = aa.id
            LEFT JOIN accounts ra ON m.revenue_account_id = ra.id
            WHERE m.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT m.*, 
                   aa.account_code AS asset_account_code, aa.account_name AS asset_account_name,
                   ra.account_code AS revenue_account_code, ra.account_name AS revenue_account_name
            FROM machinery m
            LEFT JOIN accounts aa ON m.asset_account_id = aa.id
            LEFT JOIN accounts ra ON m.revenue_account_id = ra.id
            WHERE 1=1
        ";
        $params = [];

        if (isset($filters['status'])) {
            $sql .= " AND m.is_active = :is_active";
            $params['is_active'] = ($filters['status'] === 'INACTIVE') ? 0 : 1;
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (m.machinery_code LIKE :search_code OR m.machinery_name LIKE :search_name)";
            $params['search_code'] = '%' . $filters['search'] . '%';
            $params['search_name'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY m.machinery_name ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getCount(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM machinery WHERE 1=1";
        $params = [];

        if (isset($filters['status'])) {
            $sql .= " AND is_active = :is_active";
            $params['is_active'] = ($filters['status'] === 'INACTIVE') ? 0 : 1;
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (machinery_code LIKE :search_code OR machinery_name LIKE :search_name)";
            $params['search_code'] = '%' . $filters['search'] . '%';
            $params['search_name'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getCategories(): array {
        return ['Pressure Washer', 'Generator', 'Grill', 'Other Machinery'];
    }

    public function save(array $data): int {
        $assetAccId = !empty($data['asset_account_id']) ? (int)$data['asset_account_id'] : null;
        $revAccId = !empty($data['revenue_account_id']) ? (int)$data['revenue_account_id'] : null;

        if (!empty($data['id'])) {
            $stmt = $this->db->prepare("
                UPDATE machinery 
                SET machinery_name = :name,
                    default_rental_rate = :default_rental_rate,
                    rental_unit = :rental_unit,
                    asset_account_id = :asset_account_id,
                    revenue_account_id = :revenue_account_id,
                    is_active = :is_active
                WHERE id = :id
            ");
            $isActive = (isset($data['status']) && $data['status'] === 'INACTIVE') ? 0 : 1;
            $stmt->execute([
                'name' => $data['machinery_name'],
                'default_rental_rate' => (float)$data['default_rental_rate'],
                'rental_unit' => $data['rental_unit'] ?? 'Hour',
                'asset_account_id' => $assetAccId,
                'revenue_account_id' => $revAccId,
                'is_active' => $isActive,
                'id' => (int)$data['id']
            ]);
            return (int)$data['id'];
        } else {
            // Generate Machinery Code if blank
            if (empty($data['machinery_code'])) {
                $count = (int)$this->db->query("SELECT COUNT(*) FROM machinery")->fetchColumn() + 1;
                $data['machinery_code'] = 'MAC-' . str_pad((string)$count, 4, '0', STR_PAD_LEFT);
            }

            $stmt = $this->db->prepare("
                INSERT INTO machinery 
                (machinery_code, machinery_name, default_rental_rate, rental_unit, asset_account_id, revenue_account_id, is_active)
                VALUES 
                (:code, :name, :default_rental_rate, :rental_unit, :asset_account_id, :revenue_account_id, :is_active)
            ");
            $isActive = (isset($data['status']) && $data['status'] === 'INACTIVE') ? 0 : 1;
            $stmt->execute([
                'code' => $data['machinery_code'],
                'name' => $data['machinery_name'],
                'default_rental_rate' => (float)$data['default_rental_rate'],
                'rental_unit' => $data['rental_unit'] ?? 'Hour',
                'asset_account_id' => $assetAccId,
                'revenue_account_id' => $revAccId,
                'is_active' => $isActive
            ]);
            return (int)$this->db->lastInsertId();
        }
    }

    public function updateStatus(int $id, string $status): bool {
        $isActive = ($status === 'INACTIVE') ? 0 : 1;
        $stmt = $this->db->prepare("UPDATE machinery SET is_active = :is_active WHERE id = :id");
        return $stmt->execute(['id' => $id, 'is_active' => $isActive]);
    }
}
