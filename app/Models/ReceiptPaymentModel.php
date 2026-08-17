<?php
namespace App\Models;

use Core\Model;

class ReceiptPaymentModel extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT pr.*, p.name AS party_name, p.party_code, p.party_type,
                   u.full_name AS creator_name,
                   je.journal_number, rje.journal_number AS reversal_journal_number,
                   ca.name AS cash_account_name,
                   ba.bank_name AS bank_account_name, ba.account_number AS bank_account_num
            FROM payment_receipts pr
            JOIN parties p ON pr.party_id = p.id
            LEFT JOIN users u ON pr.created_by = u.id
            LEFT JOIN journal_entries je ON pr.journal_entry_id = je.id
            LEFT JOIN journal_entries rje ON pr.reversal_journal_entry_id = rje.id
            LEFT JOIN cash_accounts ca ON pr.cash_account_id = ca.id
            LEFT JOIN bank_accounts ba ON pr.bank_account_id = ba.id
            WHERE pr.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT pr.*, p.name AS party_name, p.party_code, je.journal_number 
            FROM payment_receipts pr
            JOIN parties p ON pr.party_id = p.id
            LEFT JOIN journal_entries je ON pr.journal_entry_id = je.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['payment_type'])) {
            $sql .= " AND pr.payment_type = :payment_type";
            $params['payment_type'] = $filters['payment_type'];
        }
        if (!empty($filters['party_id'])) {
            $sql .= " AND pr.party_id = :party_id";
            $params['party_id'] = (int)$filters['party_id'];
        }
        if (!empty($filters['payment_method'])) {
            $sql .= " AND pr.payment_method = :payment_method";
            $params['payment_method'] = $filters['payment_method'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND pr.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND pr.payment_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND pr.payment_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (pr.payment_number LIKE :search_num OR p.name LIKE :search_name OR pr.reference_number LIKE :search_ref)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_name'] = '%' . $filters['search'] . '%';
            $params['search_ref'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY pr.payment_date DESC, pr.id DESC LIMIT :limit OFFSET :offset";

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
            FROM payment_receipts pr
            JOIN parties p ON pr.party_id = p.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['payment_type'])) {
            $sql .= " AND pr.payment_type = :payment_type";
            $params['payment_type'] = $filters['payment_type'];
        }
        if (!empty($filters['party_id'])) {
            $sql .= " AND pr.party_id = :party_id";
            $params['party_id'] = (int)$filters['party_id'];
        }
        if (!empty($filters['payment_method'])) {
            $sql .= " AND pr.payment_method = :payment_method";
            $params['payment_method'] = $filters['payment_method'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND pr.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND pr.payment_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND pr.payment_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (pr.payment_number LIKE :search_num OR p.name LIKE :search_name OR pr.reference_number LIKE :search_ref)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_name'] = '%' . $filters['search'] . '%';
            $params['search_ref'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function generatePaymentNumber(string $type): string {
        $db = \Core\Database::getInstance();
        $prefix = ($type === 'RECEIPT') ? 'REC-' . date('Y') . '-' : 'PAY-' . date('Y') . '-';
        $stmt = $db->prepare("SELECT payment_number FROM payment_receipts WHERE payment_number LIKE :prefix ORDER BY id DESC LIMIT 1");
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
