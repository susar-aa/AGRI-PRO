<?php
namespace App\Models;

use Core\Model;
use Exception;

class Party extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT p.*, u.full_name AS creator_name, ca.name AS customer_activity_name
            FROM parties p
            LEFT JOIN users u ON p.created_by = u.id
            LEFT JOIN cost_centers ca ON p.customer_activity_id = ca.id
            WHERE p.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getByCode(string $code): ?array {
        $stmt = $this->db->prepare("SELECT * FROM parties WHERE party_code = :code LIMIT 1");
        $stmt->execute(['code' => $code]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "SELECT p.*, u.full_name AS creator_name, ca.name AS customer_activity_name FROM parties p LEFT JOIN users u ON p.created_by = u.id LEFT JOIN cost_centers ca ON p.customer_activity_id = ca.id WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search_name OR p.party_code LIKE :search_code OR p.phone LIKE :search_phone OR p.nic_reg_no LIKE :search_nic)";
            $params['search_name'] = '%' . $filters['search'] . '%';
            $params['search_code'] = '%' . $filters['search'] . '%';
            $params['search_phone'] = '%' . $filters['search'] . '%';
            $params['search_nic'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['party_type'])) {
            $sql .= " AND p.party_type = :party_type";
            $params['party_type'] = $filters['party_type'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY p.name ASC LIMIT :limit OFFSET :offset";
        
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
        $sql = "SELECT COUNT(*) FROM parties p WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search_name OR p.party_code LIKE :search_code OR p.phone LIKE :search_phone OR p.nic_reg_no LIKE :search_nic)";
            $params['search_name'] = '%' . $filters['search'] . '%';
            $params['search_code'] = '%' . $filters['search'] . '%';
            $params['search_phone'] = '%' . $filters['search'] . '%';
            $params['search_nic'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['party_type'])) {
            $sql .= " AND p.party_type = :party_type";
            $params['party_type'] = $filters['party_type'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getCustomers(array $filters = [], int $limit = 50, int $offset = 0): array {
        $filters['party_type_in'] = ['CUSTOMER', 'BOTH'];
        return $this->getByTypeArray($filters, $limit, $offset);
    }

    public function getCustomersCount(array $filters = []): int {
        $filters['party_type_in'] = ['CUSTOMER', 'BOTH'];
        return $this->getTypeArrayCount($filters);
    }

    public function getSuppliers(array $filters = [], int $limit = 50, int $offset = 0): array {
        $filters['party_type_in'] = ['SUPPLIER', 'BOTH'];
        return $this->getByTypeArray($filters, $limit, $offset);
    }

    public function getSuppliersCount(array $filters = []): int {
        $filters['party_type_in'] = ['SUPPLIER', 'BOTH'];
        return $this->getTypeArrayCount($filters);
    }

    public function getStaff(array $filters = [], int $limit = 50, int $offset = 0): array {
        $filters['party_type_in'] = ['EMPLOYEE'];
        return $this->getByTypeArray($filters, $limit, $offset);
    }

    public function getStaffCount(array $filters = []): int {
        $filters['party_type_in'] = ['EMPLOYEE'];
        return $this->getTypeArrayCount($filters);
    }

    private function getByTypeArray(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "SELECT p.*, u.full_name AS creator_name FROM parties p LEFT JOIN users u ON p.created_by = u.id WHERE p.party_type IN ('CUSTOMER', 'BOTH')";
        if (isset($filters['party_type_in']) && in_array('SUPPLIER', $filters['party_type_in'])) {
            $sql = "SELECT p.*, u.full_name AS creator_name FROM parties p LEFT JOIN users u ON p.created_by = u.id WHERE p.party_type IN ('SUPPLIER', 'BOTH')";
        } elseif (isset($filters['party_type_in']) && in_array('EMPLOYEE', $filters['party_type_in'])) {
            $sql = "SELECT p.*, u.full_name AS creator_name FROM parties p LEFT JOIN users u ON p.created_by = u.id WHERE p.party_type = 'EMPLOYEE'";
        }
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search_name OR p.party_code LIKE :search_code OR p.phone LIKE :search_phone)";
            $params['search_name'] = '%' . $filters['search'] . '%';
            $params['search_code'] = '%' . $filters['search'] . '%';
            $params['search_phone'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY p.name ASC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function getTypeArrayCount(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM parties p WHERE p.party_type IN ('CUSTOMER', 'BOTH')";
        if (isset($filters['party_type_in']) && in_array('SUPPLIER', $filters['party_type_in'])) {
            $sql = "SELECT COUNT(*) FROM parties p WHERE p.party_type IN ('SUPPLIER', 'BOTH')";
        } elseif (isset($filters['party_type_in']) && in_array('EMPLOYEE', $filters['party_type_in'])) {
            $sql = "SELECT COUNT(*) FROM parties p WHERE p.party_type = 'EMPLOYEE'";
        }
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search_name OR p.party_code LIKE :search_code OR p.phone LIKE :search_phone)";
            $params['search_name'] = '%' . $filters['search'] . '%';
            $params['search_code'] = '%' . $filters['search'] . '%';
            $params['search_phone'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function create(array $data): int {
        // Generate unique code if not explicitly given
        $data['party_code'] = $data['party_code'] ?? self::generatePartyCode();

        // Validate formats
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address format.");
        }

        $stmt = $this->db->prepare("
            INSERT INTO parties 
            (party_code, party_type, name, contact_person, nic_reg_no, phone, whatsapp_number, email, address, city, district, 
             credit_limit, credit_days, payment_terms, status, notes, customer_type, supplier_type, customer_activity_id, created_by)
            VALUES 
            (:party_code, :party_type, :name, :contact_person, :nic_reg_no, :phone, :whatsapp_number, :email, :address, :city, :district, 
             :credit_limit, :credit_days, :payment_terms, :status, :notes, :customer_type, :supplier_type, :customer_activity_id, :created_by)
        ");

        $stmt->execute([
            'party_code' => $data['party_code'],
            'party_type' => $data['party_type'],
            'name' => $data['name'],
            'contact_person' => $data['contact_person'] ?? null,
            'nic_reg_no' => $data['nic_reg_no'] ?? null,
            'phone' => $data['phone'] ?? null,
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'district' => $data['district'] ?? null,
            'credit_limit' => round((float)($data['credit_limit'] ?? 0), 2),
            'credit_days' => (int)($data['credit_days'] ?? 0),
            'payment_terms' => $data['payment_terms'] ?? null,
            'status' => $data['status'] ?? 'active',
            'notes' => $data['notes'] ?? null,
            'customer_type' => $data['customer_type'] ?? null,
            'supplier_type' => $data['supplier_type'] ?? null,
            'customer_activity_id' => !empty($data['customer_activity_id']) ? (int)$data['customer_activity_id'] : null,
            'created_by' => $data['created_by']
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address format.");
        }

        $stmt = $this->db->prepare("
            UPDATE parties 
            SET name = :name, 
                party_type = :party_type,
                contact_person = :contact_person, 
                nic_reg_no = :nic_reg_no, 
                phone = :phone, 
                whatsapp_number = :whatsapp_number,
                email = :email, 
                address = :address, 
                city = :city, 
                district = :district, 
                credit_limit = :credit_limit, 
                credit_days = :credit_days, 
                payment_terms = :payment_terms, 
                status = :status, 
                notes = :notes, 
                customer_type = :customer_type, 
                supplier_type = :supplier_type,
                customer_activity_id = :customer_activity_id
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'party_type' => $data['party_type'],
            'name' => $data['name'],
            'contact_person' => $data['contact_person'] ?? null,
            'nic_reg_no' => $data['nic_reg_no'] ?? null,
            'phone' => $data['phone'] ?? null,
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'district' => $data['district'] ?? null,
            'credit_limit' => round((float)($data['credit_limit'] ?? 0), 2),
            'credit_days' => (int)($data['credit_days'] ?? 0),
            'payment_terms' => $data['payment_terms'] ?? null,
            'status' => $data['status'] ?? 'active',
            'notes' => $data['notes'] ?? null,
            'customer_type' => $data['customer_type'] ?? null,
            'supplier_type' => $data['supplier_type'] ?? null,
            'customer_activity_id' => !empty($data['customer_activity_id']) ? (int)$data['customer_activity_id'] : null
        ]);
    }

    public function deactivate(int $id): bool {
        $stmt = $this->db->prepare("UPDATE parties SET status = 'inactive' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function generatePartyCode(): string {
        $prefix = 'PTY-';
        $stmt = $this->db->prepare("SELECT party_code FROM parties WHERE party_code LIKE :prefix ORDER BY id DESC LIMIT 1");
        $stmt->execute(['prefix' => $prefix . '%']);
        $lastCode = $stmt->fetchColumn();

        if ($lastCode) {
            $seq = (int)substr($lastCode, -5);
            $newSeq = str_pad($seq + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '00001';
        }

        return $prefix . $newSeq;
    }
}
