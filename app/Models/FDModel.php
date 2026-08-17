<?php
namespace App\Models;

use Core\Model;
use Exception;

class FDModel extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT fd.*, m.full_name AS member_name, m.membership_no, m.nic, m.phone,
                   je.journal_number AS payment_journal, mje.journal_number AS maturity_journal
            FROM member_fixed_deposits fd
            JOIN members m ON fd.member_id = m.id
            LEFT JOIN journal_entries je ON fd.journal_entry_id = je.id
            LEFT JOIN journal_entries mje ON fd.maturity_journal_entry_id = mje.id
            WHERE fd.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT fd.*, m.full_name AS member_name, m.membership_no, m.nic
            FROM member_fixed_deposits fd
            JOIN members m ON fd.member_id = m.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['member_id'])) {
            $sql .= " AND fd.member_id = :member_id";
            $params['member_id'] = (int)$filters['member_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND fd.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['maturity_date'])) {
            $sql .= " AND fd.maturity_date = :maturity_date";
            $params['maturity_date'] = $filters['maturity_date'];
        }

        $sql .= " ORDER BY fd.id DESC LIMIT :limit OFFSET :offset";

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
        $sql = "SELECT COUNT(*) FROM member_fixed_deposits fd WHERE 1=1";
        $params = [];

        if (!empty($filters['member_id'])) {
            $sql .= " AND fd.member_id = :member_id";
            $params['member_id'] = (int)$filters['member_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND fd.status = :status";
            $params['status'] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function create(array $data): int {
        $data['deposit_number'] = $data['deposit_number'] ?? $this->generateFDNumber();

        $stmt = $this->db->prepare("
            INSERT INTO member_fixed_deposits 
            (deposit_number, member_id, deposit_date, start_date, term_months, interest_rate, expected_interest, maturity_amount, maturity_date, payment_method, status, notes, journal_entry_id)
            VALUES 
            (:deposit_number, :member_id, :deposit_date, :start_date, :term_months, :interest_rate, :expected_interest, :maturity_amount, :maturity_date, :payment_method, :status, :notes, :journal_entry_id)
        ");

        $stmt->execute([
            'deposit_number' => $data['deposit_number'],
            'member_id' => (int)$data['member_id'],
            'deposit_date' => $data['deposit_date'],
            'start_date' => $data['start_date'],
            'term_months' => (int)$data['term_months'],
            'interest_rate' => round((float)$data['interest_rate'], 2),
            'expected_interest' => round((float)$data['expected_interest'], 2),
            'maturity_amount' => round((float)$data['maturity_amount'], 2),
            'maturity_date' => $data['maturity_date'],
            'payment_method' => $data['payment_method'] ?? 'Cash',
            'status' => $data['status'] ?? 'ACTIVE',
            'notes' => $data['notes'] ?? null,
            'journal_entry_id' => $data['journal_entry_id'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function generateFDNumber(): string {
        $prefix = 'FD-' . date('Y') . '-';
        $stmt = $this->db->prepare("SELECT deposit_number FROM member_fixed_deposits WHERE deposit_number LIKE :prefix ORDER BY id DESC LIMIT 1");
        $stmt->execute(['prefix' => $prefix . '%']);
        $lastNo = $stmt->fetchColumn();

        if ($lastNo) {
            $seq = (int)substr($lastNo, -5);
            $newSeq = str_pad($seq + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '00001';
        }

        return $prefix . $newSeq;
    }

    public function getActiveStats(): array {
        // Active FDs, Total FD Principal, Total Expected Interest, Outstanding Matured
        $activeCount = (int)$this->db->query("SELECT COUNT(*) FROM member_fixed_deposits WHERE status = 'ACTIVE'")->fetchColumn();
        $totalPrincipal = (float)$this->db->query("SELECT SUM(maturity_amount - expected_interest) FROM member_fixed_deposits WHERE status = 'ACTIVE'")->fetchColumn();
        $totalInterest = (float)$this->db->query("SELECT SUM(expected_interest) FROM member_fixed_deposits WHERE status = 'ACTIVE'")->fetchColumn();
        $maturedCount = (int)$this->db->query("SELECT COUNT(*) FROM member_fixed_deposits WHERE status = 'MATURED'")->fetchColumn();

        return [
            'active_count' => $activeCount,
            'total_principal' => $totalPrincipal,
            'total_interest' => $totalInterest,
            'matured_count' => $maturedCount
        ];
    }
}
