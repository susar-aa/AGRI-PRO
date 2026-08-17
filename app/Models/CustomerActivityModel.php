<?php
namespace App\Models;

use Core\Model;

class CustomerActivityModel extends Model {

    public function getAllActive(): array {
        $stmt = $this->db->query("SELECT * FROM customer_activities WHERE status = 'active' ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM customer_activities ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM customer_activities WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO customer_activities (name, status) 
            VALUES (:name, :status)
        ");
        $stmt->execute([
            'name' => trim($data['name']),
            'status' => $data['status'] ?? 'active'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE customer_activities 
            SET name = :name, status = :status 
            WHERE id = :id
        ");
        return $stmt->execute([
            'name' => trim($data['name']),
            'status' => $data['status'] ?? 'active',
            'id' => $id
        ]);
    }
}
