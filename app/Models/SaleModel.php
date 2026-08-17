<?php
namespace App\Models;

use Core\Model;

class SaleModel extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT s.*, p.name AS customer_name, p.party_code,
                   l.name AS warehouse_name, u.full_name AS creator_name,
                   je.journal_number, rje.journal_number AS reversal_journal_number
            FROM sales s
            JOIN parties p ON s.customer_id = p.id
            JOIN inventory_locations l ON s.warehouse_id = l.id
            LEFT JOIN users u ON s.created_by = u.id
            LEFT JOIN journal_entries je ON s.journal_entry_id = je.id
            LEFT JOIN journal_entries rje ON s.reversal_journal_entry_id = rje.id
            WHERE s.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $sale = $stmt->fetch();

        if (!$sale) {
            return null;
        }

        $sale['items'] = $this->getSaleItems($id);
        return $sale;
    }

    public function getSaleItems(int $saleId): array {
        $stmt = $this->db->prepare("
            SELECT si.*, p.name_en AS product_name, p.sku, u.code AS unit_code
            FROM sale_items si
            JOIN products p ON si.product_id = p.id
            LEFT JOIN units_of_measure u ON p.sales_unit_id = u.id
            WHERE si.sale_id = :sale_id
        ");
        $stmt->execute(['sale_id' => $saleId]);
        return $stmt->fetchAll();
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT s.*, p.name AS customer_name, p.party_code, je.journal_number
            FROM sales s
            JOIN parties p ON s.customer_id = p.id
            LEFT JOIN journal_entries je ON s.journal_entry_id = je.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND s.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['customer_id'])) {
            $sql .= " AND s.customer_id = :customer_id";
            $params['customer_id'] = (int)$filters['customer_id'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND s.sale_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND s.sale_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (s.sale_number LIKE :search_num OR s.notes LIKE :search_notes)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_notes'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY s.sale_date DESC, s.id DESC LIMIT :limit OFFSET :offset";

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
            FROM sales s
            JOIN parties p ON s.customer_id = p.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND s.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['customer_id'])) {
            $sql .= " AND s.customer_id = :customer_id";
            $params['customer_id'] = (int)$filters['customer_id'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND s.sale_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND s.sale_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (s.sale_number LIKE :search_num OR s.notes LIKE :search_notes)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_notes'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function generateSaleNumber(): string {
        $prefix = 'SL-' . date('Y') . '-';
        $stmt = $this->db->prepare("SELECT sale_number FROM sales WHERE sale_number LIKE :prefix ORDER BY id DESC LIMIT 1");
        $stmt->execute(['prefix' => $prefix . '%']);
        $lastNum = $stmt->fetchColumn();

        if ($lastNum) {
            $seq = (int)substr($lastNum, -6);
            $newSeq = str_pad($seq + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '000001';
        }

        return $prefix . $newSeq;
    }
}
