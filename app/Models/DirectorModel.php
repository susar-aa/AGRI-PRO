<?php
namespace App\Models;

use Core\Model;
use Exception;

class DirectorModel extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT m.*, p.name AS customer_name, p.party_code
            FROM coop_members m
            LEFT JOIN parties p ON m.party_id = p.id
            WHERE m.id = :id AND m.member_type = 'DIRECTOR' LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT m.*, p.name AS customer_name, p.party_code
            FROM coop_members m
            LEFT JOIN parties p ON m.party_id = p.id
            WHERE m.member_type = 'DIRECTOR'
        ";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (m.full_name LIKE :search_name OR m.member_no LIKE :search_no OR m.nic LIKE :search_nic OR m.phone LIKE :search_phone)";
            $params['search_name'] = '%' . $filters['search'] . '%';
            $params['search_no'] = '%' . $filters['search'] . '%';
            $params['search_nic'] = '%' . $filters['search'] . '%';
            $params['search_phone'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status'])) {
            $sql .= " AND m.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY m.full_name ASC LIMIT :limit OFFSET :offset";

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
        $sql = "SELECT COUNT(*) FROM coop_members m WHERE m.member_type = 'DIRECTOR'";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (m.full_name LIKE :search_name OR m.member_no LIKE :search_no OR m.nic LIKE :search_nic OR m.phone LIKE :search_phone)";
            $params['search_name'] = '%' . $filters['search'] . '%';
            $params['search_no'] = '%' . $filters['search'] . '%';
            $params['search_nic'] = '%' . $filters['search'] . '%';
            $params['search_phone'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status'])) {
            $sql .= " AND m.status = :status";
            $params['status'] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function create(array $data): int {
        $data['member_no'] = $data['member_no'] ?? $this->generateDirectorNumber();

        if (empty($data['party_id'])) {
            $pStmt = $this->db->prepare("INSERT INTO parties (party_code, party_type, name, phone, address, city, nic_reg_no, created_by) VALUES (:code, 'DIRECTOR', :name, :phone, :address, :city, :nic, :by)");
            $pStmt->execute([
                'code' => $data['member_no'],
                'name' => $data['full_name'],
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
                'city' => $data['city'] ?? '',
                'nic' => $data['nic'] ?? '',
                'by' => \Core\Auth::id() ?? 1
            ]);
            $data['party_id'] = $this->db->lastInsertId();
        }

        $stmt = $this->db->prepare("
            INSERT INTO coop_members 
            (member_type, member_no, party_id, full_name, nic, dob, gender, occupation, phone, heir_name, heir_address, heir_nic, heir_contact_number, address, city, 
             registration_date, status, registration_fee, shares_fee, payment_method, payment_status, notes, journal_entry_id)
            VALUES 
            ('DIRECTOR', :member_no, :party_id, :full_name, :nic, :dob, :gender, :occupation, :phone, :heir_name, :heir_address, :heir_nic, :heir_contact_number, :address, :city, 
             :registration_date, :status, :registration_fee, :shares_fee, :payment_method, :payment_status, :notes, :journal_entry_id)
        ");

        $stmt->execute([
            'member_no' => $data['member_no'],
            'party_id' => $data['party_id'] ?? null,
            'full_name' => $data['full_name'],
            'nic' => $data['nic'],
            'dob' => $data['dob'],
            'gender' => $data['gender'],
            'occupation' => $data['occupation'] ?? null,
            'phone' => $data['phone'],
            'heir_name' => $data['heir_name'] ?? null,
            'heir_address' => $data['heir_address'] ?? null,
            'heir_nic' => $data['heir_nic'] ?? null,
            'heir_contact_number' => $data['heir_contact_number'] ?? null,
            'address' => $data['address'],
            'city' => $data['city'],
            'registration_date' => $data['registration_date'],
            'status' => $data['status'] ?? 'ACTIVE',
            'registration_fee' => round((float)($data['registration_fee'] ?? 0), 2),
            'shares_fee' => round((float)($data['shares_fee'] ?? 0), 2),
            'payment_method' => $data['payment_method'] ?? 'Unpaid',
            'payment_status' => $data['payment_status'] ?? 'UNPAID',
            'notes' => $data['notes'] ?? null,
            'journal_entry_id' => $data['journal_entry_id'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE coop_members 
            SET full_name = :full_name, nic = :nic, dob = :dob, gender = :gender, occupation = :occupation, phone = :phone, 
                heir_name = :heir_name, heir_address = :heir_address, heir_nic = :heir_nic, heir_contact_number = :heir_contact_number, 
                address = :address, city = :city, status = :status, notes = :notes, 
                party_id = :party_id
            WHERE id = :id AND member_type = 'DIRECTOR'
        ");

        return $stmt->execute([
            'id' => $id,
            'full_name' => $data['full_name'],
            'nic' => $data['nic'],
            'dob' => $data['dob'],
            'gender' => $data['gender'],
            'occupation' => $data['occupation'] ?? null,
            'phone' => $data['phone'],
            'heir_name' => $data['heir_name'] ?? null,
            'heir_address' => $data['heir_address'] ?? null,
            'heir_nic' => $data['heir_nic'] ?? null,
            'heir_contact_number' => $data['heir_contact_number'] ?? null,
            'address' => $data['address'],
            'city' => $data['city'],
            'status' => $data['status'] ?? 'ACTIVE',
            'notes' => $data['notes'] ?? null,
            'party_id' => $data['party_id'] ?? null
        ]);
    }

    public function generateDirectorNumber(): string {
        $stmtMem = $this->db->query("SELECT MAX(CAST(SUBSTRING(member_no, 5) AS UNSIGNED)) FROM coop_members WHERE member_no LIKE 'AGC %' AND member_type = 'MEMBER'");
        $maxMem = (int)$stmtMem->fetchColumn();

        $stmtDir = $this->db->query("SELECT MAX(CAST(SUBSTRING(member_no, 5) AS UNSIGNED)) FROM coop_members WHERE member_no LIKE 'AGC %' AND member_type = 'DIRECTOR'");
        $maxDir = (int)$stmtDir->fetchColumn();

        $maxVal = max($maxMem, $maxDir);
        $newSeq = $maxVal + 1;

        return 'AGC ' . str_pad($newSeq, 3, '0', STR_PAD_LEFT);
    }
}
