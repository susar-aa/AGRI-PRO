<?php
namespace App\Models;

use Core\Model;

class ServiceJobModel extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT sj.*, p.name AS customer_name, p.party_code,
                   s.service_name, s.service_code, s.default_price,
                   inv.invoice_number, inv.total AS invoice_total, inv.status AS invoice_status,
                   u.full_name AS creator_name
            FROM service_jobs sj
            JOIN parties p ON sj.customer_id = p.id
            JOIN services s ON sj.service_id = s.id
            LEFT JOIN invoices inv ON sj.invoice_id = inv.id
            LEFT JOIN users u ON sj.created_by = u.id
            WHERE sj.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $job = $stmt->fetch();

        if (!$job) {
            return null;
        }

        // Fetch posted expenses linked to this job
        $expStmt = $this->db->prepare("
            SELECT e.*, ec.name AS category_name
            FROM expenses e
            LEFT JOIN expense_categories ec ON e.expense_category_id = ec.id
            WHERE e.service_job_id = :job_id AND e.status = 'posted'
            ORDER BY e.expense_date DESC, e.id DESC
        ");
        $expStmt->execute(['job_id' => $id]);
        $job['expenses'] = $expStmt->fetchAll();

        // Calculate costings
        $totalCost = 0.00;
        foreach ($job['expenses'] as $exp) {
            $totalCost += (float)$exp['amount'];
        }
        $job['total_cost'] = $totalCost;

        // Calculate revenue & profitability
        $revenue = 0.00;
        if ($job['invoice_id'] && $job['invoice_status'] === 'POSTED') {
            $revenue = (float)$job['invoice_total'];
        }
        $job['revenue'] = $revenue;

        $grossProfit = $revenue - $totalCost;
        $job['gross_profit'] = $grossProfit;

        $margin = 0.00;
        if ($revenue > 0) {
            $margin = ($grossProfit / $revenue) * 100;
        }
        $job['margin'] = $margin;

        return $job;
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT sj.*, p.name AS customer_name, p.party_code,
                   s.service_name, s.service_code,
                   inv.invoice_number, inv.total AS invoice_total, inv.status AS invoice_status
            FROM service_jobs sj
            JOIN parties p ON sj.customer_id = p.id
            JOIN services s ON sj.service_id = s.id
            LEFT JOIN invoices inv ON sj.invoice_id = inv.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND sj.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['customer_id'])) {
            $sql .= " AND sj.customer_id = :customer_id";
            $params['customer_id'] = (int)$filters['customer_id'];
        }
        if (!empty($filters['service_id'])) {
            $sql .= " AND sj.service_id = :service_id";
            $params['service_id'] = (int)$filters['service_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (sj.job_number LIKE :search_num OR sj.location LIKE :search_loc OR sj.description LIKE :search_desc)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_loc'] = '%' . $filters['search'] . '%';
            $params['search_desc'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY sj.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $jobs = $stmt->fetchAll();

        // Calculate costs dynamically for list view
        foreach ($jobs as &$job) {
            $cost = (float)$this->db->query("
                SELECT COALESCE(SUM(amount), 0.00) 
                FROM expenses 
                WHERE service_job_id = " . (int)$job['id'] . " AND status = 'posted'
            ")->fetchColumn();
            
            $job['total_cost'] = $cost;
            $revenue = 0.00;
            if ($job['invoice_id'] && $job['invoice_status'] === 'POSTED') {
                $revenue = (float)$job['invoice_total'];
            }
            $job['revenue'] = $revenue;
            $job['profit'] = $revenue - $cost;
        }

        return $jobs;
    }

    public function getCount(array $filters = []): int {
        $sql = "
            SELECT COUNT(*)
            FROM service_jobs sj
            JOIN parties p ON sj.customer_id = p.id
            JOIN services s ON sj.service_id = s.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND sj.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['customer_id'])) {
            $sql .= " AND sj.customer_id = :customer_id";
            $params['customer_id'] = (int)$filters['customer_id'];
        }
        if (!empty($filters['service_id'])) {
            $sql .= " AND sj.service_id = :service_id";
            $params['service_id'] = (int)$filters['service_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (sj.job_number LIKE :search_num OR sj.location LIKE :search_loc OR sj.description LIKE :search_desc)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_loc'] = '%' . $filters['search'] . '%';
            $params['search_desc'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function generateJobNumber(): string {
        $prefix = 'JOB-' . date('Y') . '-';
        $stmt = $this->db->prepare("SELECT job_number FROM service_jobs WHERE job_number LIKE :prefix ORDER BY id DESC LIMIT 1");
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
                UPDATE service_jobs 
                SET customer_id = :customer_id,
                    service_id = :service_id,
                    start_date = :start_date,
                    end_date = :end_date,
                    location = :location,
                    description = :description,
                    assigned_employee = :assigned_employee,
                    status = :status,
                    invoice_id = :invoice_id,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                'customer_id' => (int)$data['customer_id'],
                'service_id' => (int)$data['service_id'],
                'start_date' => $data['start_date'],
                'end_date' => !empty($data['end_date']) ? $data['end_date'] : null,
                'location' => $data['location'] ?? null,
                'description' => $data['description'] ?? null,
                'assigned_employee' => $data['assigned_employee'] ?? null,
                'status' => $data['status'] ?? 'OPEN',
                'invoice_id' => !empty($data['invoice_id']) ? (int)$data['invoice_id'] : null,
                'id' => (int)$data['id']
            ]);
            return (int)$data['id'];
        } else {
            $jobNum = $this->generateJobNumber();
            $stmt = $this->db->prepare("
                INSERT INTO service_jobs 
                (job_number, customer_id, service_id, start_date, end_date, location, description, assigned_employee, status, invoice_id, created_by)
                VALUES 
                (:job_number, :customer_id, :service_id, :start_date, :end_date, :location, :description, :assigned_employee, :status, :invoice_id, :created_by)
            ");
            $stmt->execute([
                'job_number' => $jobNum,
                'customer_id' => (int)$data['customer_id'],
                'service_id' => (int)$data['service_id'],
                'start_date' => $data['start_date'],
                'end_date' => !empty($data['end_date']) ? $data['end_date'] : null,
                'location' => $data['location'] ?? null,
                'description' => $data['description'] ?? null,
                'assigned_employee' => $data['assigned_employee'] ?? null,
                'status' => $data['status'] ?? 'OPEN',
                'invoice_id' => !empty($data['invoice_id']) ? (int)$data['invoice_id'] : null,
                'created_by' => (int)($data['created_by'] ?? 1)
            ]);
            return (int)$this->db->lastInsertId();
        }
    }
}
