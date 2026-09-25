<?php
namespace App\Models;

use Core\Model;
use PDO;

class ReportModel extends Model {

    /**
     * Fetch directory report records filtered by entity type, search query, and status
     *
     * @param array $filters ['type' => 'all'|'director'|'member'|'staff'|'customer', 'search' => string, 'status' => string]
     * @return array
     */
    public function getDirectoryReport(array $filters = []): array {
        $type = strtolower(trim($filters['type'] ?? 'all'));
        $search = trim($filters['search'] ?? '');
        $status = strtolower(trim($filters['status'] ?? ''));

        $results = [];

        // 1. DIRECTORS
        if ($type === 'all' || $type === 'director' || $type === 'directors') {
            $directors = $this->fetchDirectors($search, $status);
            $results = array_merge($results, $directors);
        }

        // 2. MEMBERS
        if ($type === 'all' || $type === 'member' || $type === 'members') {
            $members = $this->fetchMembers($search, $status);
            $results = array_merge($results, $members);
        }

        // 3. STAFF (Parties with party_type='EMPLOYEE' + System Users)
        if ($type === 'all' || $type === 'staff') {
            $staff = $this->fetchStaff($search, $status);
            $results = array_merge($results, $staff);
        }

        // 4. CUSTOMERS (Parties with party_type IN ('CUSTOMER', 'BOTH'))
        if ($type === 'all' || $type === 'customer' || $type === 'customers') {
            $customers = $this->fetchCustomers($search, $status);
            $results = array_merge($results, $customers);
        }

        // Sort results by Name ascending
        usort($results, function($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        return $results;
    }

    /**
     * Get directory breakdown counts matching current search/status filters
     */
    public function getDirectoryCounts(array $filters = []): array {
        $search = trim($filters['search'] ?? '');
        $status = strtolower(trim($filters['status'] ?? ''));

        $directors = count($this->fetchDirectors($search, $status));
        $members = count($this->fetchMembers($search, $status));
        $staff = count($this->fetchStaff($search, $status));
        $customers = count($this->fetchCustomers($search, $status));

        return [
            'total' => $directors + $members + $staff + $customers,
            'directors' => $directors,
            'members' => $members,
            'staff' => $staff,
            'customers' => $customers
        ];
    }

    private function fetchDirectors(string $search, string $status): array {
        $sql = "
            SELECT 
                'Director' AS entity_type,
                m.id,
                m.member_no AS code,
                m.full_name AS name,
                '' AS username,
                m.phone,
                m.nic,
                m.address,
                m.city,
                m.status,
                m.registration_date AS created_at
            FROM coop_members m
            WHERE m.member_type = 'DIRECTOR'
        ";
        $params = [];

        if (!empty($status) && $status !== 'all') {
            $sql .= " AND LOWER(m.status) = :status";
            $params['status'] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (
                m.full_name LIKE :search_name
                OR m.member_no LIKE :search_code
                OR m.phone LIKE :search_phone
                OR m.nic LIKE :search_nic
                OR m.address LIKE :search_addr
                OR m.city LIKE :search_city
            )";
            $term = '%' . $search . '%';
            $params['search_name'] = $term;
            $params['search_code'] = $term;
            $params['search_phone'] = $term;
            $params['search_nic'] = $term;
            $params['search_addr'] = $term;
            $params['search_city'] = $term;
        }

        $sql .= " ORDER BY m.full_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function fetchMembers(string $search, string $status): array {
        $sql = "
            SELECT 
                'Member' AS entity_type,
                m.id,
                m.member_no AS code,
                m.full_name AS name,
                '' AS username,
                m.phone,
                m.nic,
                m.address,
                m.city,
                m.status,
                m.registration_date AS created_at
            FROM coop_members m
            WHERE m.member_type = 'MEMBER'
        ";
        $params = [];

        if (!empty($status) && $status !== 'all') {
            $sql .= " AND LOWER(m.status) = :status";
            $params['status'] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (
                m.full_name LIKE :search_name
                OR m.member_no LIKE :search_code
                OR m.phone LIKE :search_phone
                OR m.nic LIKE :search_nic
                OR m.address LIKE :search_addr
                OR m.city LIKE :search_city
            )";
            $term = '%' . $search . '%';
            $params['search_name'] = $term;
            $params['search_code'] = $term;
            $params['search_phone'] = $term;
            $params['search_nic'] = $term;
            $params['search_addr'] = $term;
            $params['search_city'] = $term;
        }

        $sql .= " ORDER BY m.full_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function fetchCustomers(string $search, string $status): array {
        $sql = "
            SELECT 
                'Customer' AS entity_type,
                p.id,
                p.party_code AS code,
                p.name AS name,
                '' AS username,
                p.phone,
                p.nic_reg_no AS nic,
                p.address,
                p.city,
                p.status,
                p.created_at
            FROM parties p
            WHERE p.party_type IN ('CUSTOMER', 'BOTH')
        ";
        $params = [];

        if (!empty($status) && $status !== 'all') {
            $sql .= " AND LOWER(p.status) = :status";
            $params['status'] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (
                p.name LIKE :search_name
                OR p.party_code LIKE :search_code
                OR p.phone LIKE :search_phone
                OR p.nic_reg_no LIKE :search_nic
                OR p.address LIKE :search_addr
                OR p.city LIKE :search_city
            )";
            $term = '%' . $search . '%';
            $params['search_name'] = $term;
            $params['search_code'] = $term;
            $params['search_phone'] = $term;
            $params['search_nic'] = $term;
            $params['search_addr'] = $term;
            $params['search_city'] = $term;
        }

        $sql .= " ORDER BY p.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function fetchStaff(string $search, string $status): array {
        $sql = "
            SELECT 
                'Staff' AS entity_type,
                p.id,
                p.party_code AS code,
                p.name AS name,
                COALESCE(u.username, '') AS username,
                p.phone,
                p.nic_reg_no AS nic,
                p.address,
                p.city,
                p.status,
                p.created_at
            FROM parties p
            LEFT JOIN users u ON p.created_by = u.id
            WHERE p.party_type = 'EMPLOYEE'
        ";
        $params = [];

        if (!empty($status) && $status !== 'all') {
            $sql .= " AND LOWER(p.status) = :status";
            $params['status'] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (
                p.name LIKE :search_name
                OR p.party_code LIKE :search_code
                OR p.phone LIKE :search_phone
                OR p.nic_reg_no LIKE :search_nic
                OR p.address LIKE :search_addr
                OR p.city LIKE :search_city
            )";
            $term = '%' . $search . '%';
            $params['search_name'] = $term;
            $params['search_code'] = $term;
            $params['search_phone'] = $term;
            $params['search_nic'] = $term;
            $params['search_addr'] = $term;
            $params['search_city'] = $term;
        }

        $sql .= " ORDER BY p.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
