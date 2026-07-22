<?php
namespace App\Models;

use Core\Model;

class CostCenter extends Model {

    public function getAll(): array {
        return $this->db->query("SELECT * FROM cost_centers ORDER BY code ASC")->fetchAll();
    }

    public function getActive(): array {
        return $this->db->query("SELECT * FROM cost_centers WHERE is_active = 1 ORDER BY code ASC")->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM cost_centers WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO cost_centers (code, name, description, is_active)
            VALUES (:code, :name, :description, :is_active)
        ");
        $stmt->execute([
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);
        return (int)$this->db->lastInsertId();
    }
}
