<?php
namespace App\Models;

use Core\Model;

class InvoiceModel extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT i.*, p.name AS customer_name, p.party_code,
                   l.name AS warehouse_name, u.full_name AS creator_name,
                   je.journal_number, rje.journal_number AS reversal_journal_number
            FROM invoices i
            JOIN parties p ON i.customer_id = p.id
            LEFT JOIN inventory_locations l ON i.warehouse_id = l.id
            LEFT JOIN users u ON i.created_by = u.id
            LEFT JOIN journal_entries je ON i.journal_entry_id = je.id
            LEFT JOIN journal_entries rje ON i.reversal_journal_entry_id = rje.id
            WHERE i.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $invoice = $stmt->fetch();

        if (!$invoice) {
            return null;
        }

        $invoice['items'] = $this->getInvoiceItems($id);
        return $invoice;
    }

    public function getInvoiceItems(int $invoiceId): array {
        $stmt = $this->db->prepare("
            SELECT ii.*, 
                   p.name_en AS product_name, p.sku, pu.code AS product_unit,
                   s.service_name, s.service_code, s.unit AS service_unit
            FROM invoice_items ii
            LEFT JOIN products p ON ii.product_id = p.id
            LEFT JOIN units_of_measure pu ON p.sales_unit_id = pu.id
            LEFT JOIN services s ON ii.service_id = s.id
            WHERE ii.invoice_id = :invoice_id
            ORDER BY ii.id ASC
        ");
        $stmt->execute(['invoice_id' => $invoiceId]);
        return $stmt->fetchAll();
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT i.*, p.name AS customer_name, p.party_code, je.journal_number
            FROM invoices i
            JOIN parties p ON i.customer_id = p.id
            LEFT JOIN journal_entries je ON i.journal_entry_id = je.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND i.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['customer_id'])) {
            $sql .= " AND i.customer_id = :customer_id";
            $params['customer_id'] = (int)$filters['customer_id'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND i.invoice_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND i.invoice_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (i.invoice_number LIKE :search_num OR i.reference LIKE :search_ref OR i.notes LIKE :search_notes)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_ref'] = '%' . $filters['search'] . '%';
            $params['search_notes'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY i.invoice_date DESC, i.id DESC LIMIT :limit OFFSET :offset";

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
            FROM invoices i
            JOIN parties p ON i.customer_id = p.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND i.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['customer_id'])) {
            $sql .= " AND i.customer_id = :customer_id";
            $params['customer_id'] = (int)$filters['customer_id'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND i.invoice_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND i.invoice_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (i.invoice_number LIKE :search_num OR i.reference LIKE :search_ref OR i.notes LIKE :search_notes)";
            $params['search_num'] = '%' . $filters['search'] . '%';
            $params['search_ref'] = '%' . $filters['search'] . '%';
            $params['search_notes'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function generateInvoiceNumber(): string {
        $prefix = 'INV-' . date('Y') . '-';
        $stmt = $this->db->prepare("SELECT invoice_number FROM invoices WHERE invoice_number LIKE :prefix ORDER BY id DESC LIMIT 1");
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
