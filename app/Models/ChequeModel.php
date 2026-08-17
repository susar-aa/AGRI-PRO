<?php
namespace App\Models;

use Core\Model;

class ChequeModel extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT c.*, p.name AS customer_name, p.party_code,
                   u.full_name AS creator_name
            FROM cheques c
            JOIN parties p ON c.party_id = p.id
            LEFT JOIN users u ON c.created_by = u.id
            WHERE c.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT c.*, p.name AS customer_name, p.party_code 
            FROM cheques c
            JOIN parties p ON c.party_id = p.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND c.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['party_id'])) {
            $sql .= " AND c.party_id = :party_id";
            $params['party_id'] = (int)$filters['party_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (c.cheque_number LIKE :search_num OR p.name LIKE :search_name OR c.bank_name LIKE :search_bank)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_name'] = '%' . $filters['search'] . '%';
            $params['search_bank'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY c.cheque_date DESC, c.id DESC LIMIT :limit OFFSET :offset";

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
            FROM cheques c
            JOIN parties p ON c.party_id = p.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND c.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['party_id'])) {
            $sql .= " AND c.party_id = :party_id";
            $params['party_id'] = (int)$filters['party_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (c.cheque_number LIKE :search_num OR p.name LIKE :search_name OR c.bank_name LIKE :search_bank)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_name'] = '%' . $filters['search'] . '%';
            $params['search_bank'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getUndepositedCheques(): array {
        $stmt = $this->db->prepare("
            SELECT c.*, p.name AS customer_name, p.party_code
            FROM cheques c
            JOIN parties p ON c.party_id = p.id
            WHERE c.status = 'RECEIVED' AND c.cheque_type = 'RECEIVED'
            ORDER BY c.cheque_date ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
