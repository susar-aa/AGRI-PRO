<?php
namespace App\Models;

use Core\Model;

class Journal extends Model {

    public function getAll(int $limit = 50, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT je.*, u.full_name AS creator_name, cc.name AS cost_center_name
            FROM journal_entries je
            LEFT JOIN users u ON je.created_by = u.id
            LEFT JOIN cost_centers cc ON je.cost_center_id = cc.id
            ORDER BY je.transaction_date DESC, je.id DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT je.*, u.full_name AS creator_name, cc.name AS cost_center_name
            FROM journal_entries je
            LEFT JOIN users u ON je.created_by = u.id
            LEFT JOIN cost_centers cc ON je.cost_center_id = cc.id
            WHERE je.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $entry = $stmt->fetch();
        if (!$entry) return null;

        // Fetch lines
        $lineStmt = $this->db->prepare("
            SELECT jl.*, a.account_code, a.account_name
            FROM journal_lines jl
            JOIN accounts a ON jl.account_id = a.id
            WHERE jl.journal_entry_id = :je_id
            ORDER BY jl.id ASC
        ");
        $lineStmt->execute(['je_id' => $id]);
        $entry['lines'] = $lineStmt->fetchAll();

        return $entry;
    }

    public function getGeneralLedger(?int $accountId = null, ?string $fromDate = null, ?string $toDate = null, ?int $costCenterId = null): array {
        $sql = "
            SELECT le.*, a.account_code, a.account_name, je.journal_number, je.description AS entry_description, cc.name AS cost_center_name
            FROM ledger_entries le
            JOIN accounts a ON le.account_id = a.id
            JOIN journal_entries je ON le.journal_entry_id = je.id
            LEFT JOIN cost_centers cc ON le.cost_center_id = cc.id
            WHERE je.status = 'posted'
        ";
        $params = [];

        if ($accountId) {
            $sql .= " AND le.account_id = :account_id";
            $params['account_id'] = $accountId;
        }

        if ($fromDate) {
            $sql .= " AND le.transaction_date >= :from_date";
            $params['from_date'] = $fromDate;
        }

        if ($toDate) {
            $sql .= " AND le.transaction_date <= :to_date";
            $params['to_date'] = $toDate;
        }

        if ($costCenterId) {
            $sql .= " AND le.cost_center_id = :cost_center_id";
            $params['cost_center_id'] = $costCenterId;
        }

        $sql .= " ORDER BY le.transaction_date ASC, le.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTrialBalance(?string $toDate = null, ?string $fromDate = null, ?int $costCenterId = null): array {
        $toDate = $toDate ?: date('Y-m-d');
        
        $sql = "
            SELECT 
                a.id AS account_id,
                a.account_code,
                a.account_name,
                a.category,
                a.normal_balance,
                COALESCE(SUM(CASE WHEN je.id IS NOT NULL THEN jl.debit ELSE 0.00 END), 0.00) AS total_debit,
                COALESCE(SUM(CASE WHEN je.id IS NOT NULL THEN jl.credit ELSE 0.00 END), 0.00) AS total_credit
            FROM accounts a
            LEFT JOIN journal_lines jl ON a.id = jl.account_id
            LEFT JOIN journal_entries je ON jl.journal_entry_id = je.id 
                AND je.status = 'posted' 
                AND je.transaction_date <= :to_date
        ";
        $params = ['to_date' => $toDate];

        if ($fromDate) {
            $sql .= " AND je.transaction_date >= :from_date";
            $params['from_date'] = $fromDate;
        }

        if ($costCenterId) {
            $sql .= " AND je.cost_center_id = :cost_center_id";
            $params['cost_center_id'] = $costCenterId;
        }

        $sql .= " WHERE a.deleted_at IS NULL AND a.allow_manual_posting = 1
            GROUP BY a.id, a.account_code, a.account_name, a.category, a.normal_balance
            HAVING total_debit > 0 OR total_credit > 0
            ORDER BY a.account_code ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
