<?php
namespace App\Models;

use Core\Model;

class MachineryRentalModel extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT r.*, p.name AS customer_name, p.party_code,
                   m.machinery_name, m.machinery_code, m.category AS machinery_category,
                   inv.invoice_number, inv.total AS invoice_total, inv.status AS invoice_status,
                   u.full_name AS creator_name
            FROM machinery_rentals r
            JOIN parties p ON r.customer_id = p.id
            JOIN machinery m ON r.machinery_id = m.id
            LEFT JOIN invoices inv ON r.invoice_id = inv.id
            LEFT JOIN users u ON r.created_by = u.id
            WHERE r.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $rental = $stmt->fetch();

        if (!$rental) {
            return null;
        }

        // Fetch posted expenses linked to this rental or machinery
        $expStmt = $this->db->prepare("
            SELECT e.*, ec.name AS category_name
            FROM expenses e
            LEFT JOIN expense_categories ec ON e.expense_category_id = ec.id
            WHERE (e.machinery_rental_id = :rental_id OR (e.machinery_id = :machinery_id AND e.machinery_rental_id IS NULL)) AND e.status = 'posted'
            ORDER BY e.expense_date DESC, e.id DESC
        ");
        $expStmt->execute([
            'rental_id' => $id,
            'machinery_id' => (int)$rental['machinery_id']
        ]);
        $rental['expenses'] = $expStmt->fetchAll();

        // Calculate Costings
        $totalCost = 0.00;
        foreach ($rental['expenses'] as $exp) {
            $totalCost += (float)$exp['amount'];
        }
        $rental['total_cost'] = $totalCost;

        // Calculate Revenue & profit margin
        $revenue = 0.00;
        if ($rental['invoice_id'] && $rental['invoice_status'] === 'POSTED') {
            $revenue = (float)$rental['invoice_total'];
        }
        $rental['revenue'] = $revenue;

        $profit = $revenue - $totalCost;
        $rental['profit'] = $profit;

        $margin = 0.00;
        if ($revenue > 0) {
            $margin = ($profit / $revenue) * 100;
        }
        $rental['margin'] = $margin;

        return $rental;
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT r.*, p.name AS customer_name, p.party_code,
                   m.machinery_name, m.machinery_code,
                   inv.invoice_number, inv.total AS invoice_total, inv.status AS invoice_status
            FROM machinery_rentals r
            JOIN parties p ON r.customer_id = p.id
            JOIN machinery m ON r.machinery_id = m.id
            LEFT JOIN invoices inv ON r.invoice_id = inv.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND r.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['customer_id'])) {
            $sql .= " AND r.customer_id = :customer_id";
            $params['customer_id'] = (int)$filters['customer_id'];
        }
        if (!empty($filters['machinery_id'])) {
            $sql .= " AND r.machinery_id = :machinery_id";
            $params['machinery_id'] = (int)$filters['machinery_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (r.rental_number LIKE :search_num OR r.notes LIKE :search_notes)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_notes'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY r.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rentals = $stmt->fetchAll();

        // Cost & Revenue calculated dynamically for grid list
        foreach ($rentals as &$r) {
            $cost = (float)$this->db->query("
                SELECT COALESCE(SUM(amount), 0.00) 
                FROM expenses 
                WHERE (machinery_rental_id = " . (int)$r['id'] . " OR (machinery_id = " . (int)$r['machinery_id'] . " AND machinery_rental_id IS NULL)) AND status = 'posted'
            ")->fetchColumn();
            
            $r['total_cost'] = $cost;
            $revenue = 0.00;
            if ($r['invoice_id'] && $r['invoice_status'] === 'POSTED') {
                $revenue = (float)$r['invoice_total'];
            }
            $r['revenue'] = $revenue;
            $r['profit'] = $revenue - $cost;
        }

        return $rentals;
    }

    public function getCount(array $filters = []): int {
        $sql = "
            SELECT COUNT(*)
            FROM machinery_rentals r
            JOIN parties p ON r.customer_id = p.id
            JOIN machinery m ON r.machinery_id = m.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND r.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['customer_id'])) {
            $sql .= " AND r.customer_id = :customer_id";
            $params['customer_id'] = (int)$filters['customer_id'];
        }
        if (!empty($filters['machinery_id'])) {
            $sql .= " AND r.machinery_id = :machinery_id";
            $params['machinery_id'] = (int)$filters['machinery_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (r.rental_number LIKE :search_num OR r.notes LIKE :search_notes)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_notes'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function generateRentalNumber(): string {
        $prefix = 'RNT-' . date('Y') . '-';
        $stmt = $this->db->prepare("SELECT rental_number FROM machinery_rentals WHERE rental_number LIKE :prefix ORDER BY id DESC LIMIT 1");
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

    public function save(array $data): int {
        if (!empty($data['id'])) {
            $stmt = $this->db->prepare("
                UPDATE machinery_rentals 
                SET customer_id = :customer_id,
                    machinery_id = :machinery_id,
                    start_date = :start_date,
                    end_date = :end_date,
                    start_time = :start_time,
                    end_time = :end_time,
                    rental_unit = :rental_unit,
                    quantity = :quantity,
                    rental_rate = :rental_rate,
                    total_charge = :total_charge,
                    notes = :notes,
                    status = :status,
                    invoice_id = :invoice_id,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                'customer_id' => (int)$data['customer_id'],
                'machinery_id' => (int)$data['machinery_id'],
                'start_date' => $data['start_date'],
                'end_date' => !empty($data['end_date']) ? $data['end_date'] : null,
                'start_time' => !empty($data['start_time']) ? $data['start_time'] : null,
                'end_time' => !empty($data['end_time']) ? $data['end_time'] : null,
                'rental_unit' => $data['rental_unit'] ?? 'Hour',
                'quantity' => (float)$data['quantity'],
                'rental_rate' => (float)$data['rental_rate'],
                'total_charge' => (float)$data['total_charge'],
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'DRAFT',
                'invoice_id' => !empty($data['invoice_id']) ? (int)$data['invoice_id'] : null,
                'id' => (int)$data['id']
            ]);
            return (int)$data['id'];
        } else {
            $rentalNum = $this->generateRentalNumber();
            $stmt = $this->db->prepare("
                INSERT INTO machinery_rentals 
                (rental_number, customer_id, machinery_id, start_date, end_date, start_time, end_time, rental_unit, quantity, rental_rate, total_charge, notes, status, invoice_id, created_by)
                VALUES 
                (:rental_number, :customer_id, :machinery_id, :start_date, :end_date, :start_time, :end_time, :rental_unit, :quantity, :rental_rate, :total_charge, :notes, :status, :invoice_id, :created_by)
            ");
            $stmt->execute([
                'rental_number' => $rentalNum,
                'customer_id' => (int)$data['customer_id'],
                'machinery_id' => (int)$data['machinery_id'],
                'start_date' => $data['start_date'],
                'end_date' => !empty($data['end_date']) ? $data['end_date'] : null,
                'start_time' => !empty($data['start_time']) ? $data['start_time'] : null,
                'end_time' => !empty($data['end_time']) ? $data['end_time'] : null,
                'rental_unit' => $data['rental_unit'] ?? 'Hour',
                'quantity' => (float)$data['quantity'],
                'rental_rate' => (float)$data['rental_rate'],
                'total_charge' => (float)$data['total_charge'],
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'DRAFT',
                'invoice_id' => !empty($data['invoice_id']) ? (int)$data['invoice_id'] : null,
                'created_by' => (int)($data['created_by'] ?? 1)
            ]);
            return (int)$this->db->lastInsertId();
        }
    }
}
