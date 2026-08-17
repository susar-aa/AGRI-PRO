<?php
namespace App\Models;

use Core\Model;
use Exception;

class MemberModel extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT m.*, p.name AS customer_name, p.party_code
            FROM members m
            LEFT JOIN parties p ON m.party_id = p.id
            WHERE m.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT m.*, p.name AS customer_name, p.party_code
            FROM members m
            LEFT JOIN parties p ON m.party_id = p.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (m.full_name LIKE :search_name OR m.membership_no LIKE :search_no OR m.nic LIKE :search_nic OR m.phone LIKE :search_phone)";
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
        $sql = "SELECT COUNT(*) FROM members m WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (m.full_name LIKE :search_name OR m.membership_no LIKE :search_no OR m.nic LIKE :search_nic OR m.phone LIKE :search_phone)";
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
        $data['membership_no'] = $data['membership_no'] ?? $this->generateMembershipNumber();

        $stmt = $this->db->prepare("
            INSERT INTO members 
            (membership_no, party_id, full_name, nic, dob, gender, occupation, phone, heir_name, heir_address, heir_nic, heir_contact_number, address, city, 
             registration_date, membership_type, status, registration_fee, shares_fee, payment_method, payment_status, notes, journal_entry_id)
            VALUES 
            (:membership_no, :party_id, :full_name, :nic, :dob, :gender, :occupation, :phone, :heir_name, :heir_address, :heir_nic, :heir_contact_number, :address, :city, 
             :registration_date, :membership_type, :status, :registration_fee, :shares_fee, :payment_method, :payment_status, :notes, :journal_entry_id)
        ");

        $stmt->execute([
            'membership_no' => $data['membership_no'],
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
            'membership_type' => $data['membership_type'] ?? 'Ordinary',
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
            UPDATE members 
            SET full_name = :full_name, nic = :nic, dob = :dob, gender = :gender, occupation = :occupation, phone = :phone, 
                heir_name = :heir_name, heir_address = :heir_address, heir_nic = :heir_nic, heir_contact_number = :heir_contact_number, 
                address = :address, city = :city, status = :status, notes = :notes, 
                party_id = :party_id
            WHERE id = :id
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

    public function generateMembershipNumber(): string {
        $prefix = 'MEM-' . date('Y') . '-';
        $stmt = $this->db->prepare("SELECT membership_no FROM members WHERE membership_no LIKE :prefix ORDER BY id DESC LIMIT 1");
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

    public function getFixedDepositsByMember(int $memberId): array {
        $stmt = $this->db->prepare("SELECT * FROM member_fixed_deposits WHERE member_id = :member_id ORDER BY id DESC");
        $stmt->execute(['member_id' => $memberId]);
        return $stmt->fetchAll();
    }
}
