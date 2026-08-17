<?php
namespace App\Models;

use Core\Model;

class DepositModel extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT bd.*, ba.bank_name, ba.account_number,
                   u.full_name AS creator_name,
                   je.journal_number, rje.journal_number AS reversal_journal_number
            FROM bank_deposits bd
            JOIN bank_accounts ba ON bd.bank_account_id = ba.id
            LEFT JOIN users u ON bd.created_by = u.id
            LEFT JOIN journal_entries je ON bd.journal_entry_id = je.id
            LEFT JOIN journal_entries rje ON bd.reversal_journal_entry_id = rje.id
            WHERE bd.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $deposit = $stmt->fetch();

        if (!$deposit) {
            return null;
        }

        $deposit['items'] = $this->getDepositItems($id);
        return $deposit;
    }

    public function getDepositItems(int $depositId): array {
        $stmt = $this->db->prepare("
            SELECT di.*, c.cheque_number, c.bank_name AS cheque_bank, p.name AS customer_name
            FROM deposit_items di
            LEFT JOIN cheques c ON di.cheque_id = c.id
            LEFT JOIN parties p ON c.party_id = p.id
            WHERE di.deposit_id = :deposit_id
        ");
        $stmt->execute(['deposit_id' => $depositId]);
        return $stmt->fetchAll();
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT bd.*, ba.bank_name, ba.account_number, je.journal_number 
            FROM bank_deposits bd
            JOIN bank_accounts ba ON bd.bank_account_id = ba.id
            LEFT JOIN journal_entries je ON bd.journal_entry_id = je.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND bd.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['bank_account_id'])) {
            $sql .= " AND bd.bank_account_id = :bank_account_id";
            $params['bank_account_id'] = (int)$filters['bank_account_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (bd.deposit_number LIKE :search_num OR bd.description LIKE :search_desc)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_desc'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY bd.deposit_date DESC, bd.id DESC LIMIT :limit OFFSET :offset";

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
            FROM bank_deposits bd
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND bd.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['bank_account_id'])) {
            $sql .= " AND bd.bank_account_id = :bank_account_id";
            $params['bank_account_id'] = (int)$filters['bank_account_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (bd.deposit_number LIKE :search_num OR bd.description LIKE :search_desc)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_desc'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function generateDepositNumber(): string {
        $prefix = 'DEP-' . date('Y') . '-';
        $stmt = $this->db->prepare("SELECT deposit_number FROM bank_deposits WHERE deposit_number LIKE :prefix ORDER BY id DESC LIMIT 1");
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
