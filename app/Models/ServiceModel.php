<?php
namespace App\Models;

use Core\Model;

class ServiceModel extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT s.*, a.account_code AS revenue_account_code, a.account_name AS revenue_account_name
            FROM services s
            LEFT JOIN accounts a ON s.revenue_account_id = a.id
            WHERE s.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT s.*, a.account_code AS revenue_account_code, a.account_name AS revenue_account_name
            FROM services s
            LEFT JOIN accounts a ON s.revenue_account_id = a.id
            WHERE 1=1
        ";
        $params = [];

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $sql .= " AND s.is_active = :is_act";
            $params['is_act'] = (int)$filters['is_active'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (s.service_code LIKE :search_code OR s.service_name LIKE :search_name)";
            $params['search_code'] = '%' . $filters['search'] . '%';
            $params['search_name'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY s.service_name ASC LIMIT :limit OFFSET :offset";

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
        $sql = "
            SELECT COUNT(*)
            FROM services s
            WHERE 1=1
        ";
        $params = [];

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $sql .= " AND s.is_active = :is_act";
            $params['is_act'] = (int)$filters['is_active'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (s.service_code LIKE :search_code OR s.service_name LIKE :search_name)";
            $params['search_code'] = '%' . $filters['search'] . '%';
            $params['search_name'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function save(array $data): int {
        $revAccountId = !empty($data['revenue_account_id']) ? (int)$data['revenue_account_id'] : null;

        if (!empty($data['id'])) {
            $stmt = $this->db->prepare("
                UPDATE services 
                SET service_name = :name,
                    description = :desc,
                    default_price = :price,
                    unit = :unit,
                    revenue_account_id = :revenue_account_id,
                    is_active = :is_active
                WHERE id = :id
            ");
            $stmt->execute([
                'name' => $data['service_name'],
                'desc' => $data['description'] ?? null,
                'price' => (float)$data['default_price'],
                'unit' => $data['unit'] ?? 'Job',
                'revenue_account_id' => $revAccountId,
                'is_active' => (int)$data['is_active'],
                'id' => (int)$data['id']
            ]);
            return (int)$data['id'];
        } else {
            // Generate Code if blank
            if (empty($data['service_code'])) {
                $count = (int)$this->db->query("SELECT COUNT(*) FROM services")->fetchColumn() + 1;
                $data['service_code'] = 'SRV-' . str_pad((string)$count, 4, '0', STR_PAD_LEFT);
            }

            $stmt = $this->db->prepare("
                INSERT INTO services 
                (service_code, service_name, description, default_price, unit, revenue_account_id, is_active)
                VALUES 
                (:code, :name, :desc, :price, :unit, :revenue_account_id, :is_active)
            ");
            $stmt->execute([
                'code' => $data['service_code'],
                'name' => $data['service_name'],
                'desc' => $data['description'] ?? null,
                'price' => (float)$data['default_price'],
                'unit' => $data['unit'] ?? 'Job',
                'revenue_account_id' => $revAccountId,
                'is_active' => (int)$data['is_active']
            ]);
            return (int)$this->db->lastInsertId();
        }
    }

    public function deactivate(int $id): bool {
        $stmt = $this->db->prepare("UPDATE services SET is_active = 0, updated_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
