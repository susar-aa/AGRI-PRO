<?php
namespace App\Models;

use Core\Model;

class Expense extends Model {

    public function getAllCategories(): array {
        $stmt = $this->db->prepare("
            SELECT ec.*, a.account_code, a.account_name 
            FROM expense_categories ec
            JOIN accounts a ON ec.linked_account_id = a.id
            WHERE ec.is_active = 1
            ORDER BY ec.name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCategoryById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT ec.*, a.account_code, a.account_name 
            FROM expense_categories ec
            JOIN accounts a ON ec.linked_account_id = a.id
            WHERE ec.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getCashAccounts(): array {
        $stmt = $this->db->prepare("SELECT * FROM cash_accounts WHERE status = 'active'");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBankAccounts(): array {
        $stmt = $this->db->prepare("SELECT * FROM bank_accounts WHERE status = 'active'");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT e.*, ec.name AS category_name, cc.name AS cost_center_name, u.full_name AS creator_name,
                   je.journal_number, rje.journal_number AS reversal_journal_number
            FROM expenses e
            JOIN expense_categories ec ON e.expense_category_id = ec.id
            JOIN cost_centers cc ON e.cost_center_id = cc.id
            LEFT JOIN users u ON e.created_by = u.id
            LEFT JOIN journal_entries je ON e.journal_entry_id = je.id
            LEFT JOIN journal_entries rje ON e.reversal_journal_entry_id = rje.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['date_from'])) {
            $sql .= " AND e.expense_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND e.expense_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        if (!empty($filters['expense_number'])) {
            $sql .= " AND e.expense_number LIKE :expense_number";
            $params['expense_number'] = '%' . $filters['expense_number'] . '%';
        }
        if (!empty($filters['category_id'])) {
            $sql .= " AND e.expense_category_id = :category_id";
            $params['category_id'] = (int)$filters['category_id'];
        }
        if (!empty($filters['payee'])) {
            $sql .= " AND e.payee LIKE :payee";
            $params['payee'] = '%' . $filters['payee'] . '%';
        }
        if (!empty($filters['payment_method'])) {
            $sql .= " AND e.payment_method = :payment_method";
            $params['payment_method'] = $filters['payment_method'];
        }
        if (!empty($filters['cost_center_id'])) {
            $sql .= " AND e.cost_center_id = :cost_center_id";
            $params['cost_center_id'] = (int)$filters['cost_center_id'];
        }
        if (!empty($filters['source_module'])) {
            $sql .= " AND e.source_module = :source_module";
            $params['source_module'] = $filters['source_module'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY e.expense_date DESC, e.id DESC LIMIT :limit OFFSET :offset";
        
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
            FROM expenses e
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['date_from'])) {
            $sql .= " AND e.expense_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND e.expense_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        if (!empty($filters['expense_number'])) {
            $sql .= " AND e.expense_number LIKE :expense_number";
            $params['expense_number'] = '%' . $filters['expense_number'] . '%';
        }
        if (!empty($filters['category_id'])) {
            $sql .= " AND e.expense_category_id = :category_id";
            $params['category_id'] = (int)$filters['category_id'];
        }
        if (!empty($filters['payee'])) {
            $sql .= " AND e.payee LIKE :payee";
            $params['payee'] = '%' . $filters['payee'] . '%';
        }
        if (!empty($filters['payment_method'])) {
            $sql .= " AND e.payment_method = :payment_method";
            $params['payment_method'] = $filters['payment_method'];
        }
        if (!empty($filters['cost_center_id'])) {
            $sql .= " AND e.cost_center_id = :cost_center_id";
            $params['cost_center_id'] = (int)$filters['cost_center_id'];
        }
        if (!empty($filters['source_module'])) {
            $sql .= " AND e.source_module = :source_module";
            $params['source_module'] = $filters['source_module'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params['status'] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT e.*, ec.name AS category_name, cc.name AS cost_center_name, 
                   u.full_name AS creator_name, app.full_name AS approver_name, pst.full_name AS poster_name, rev.full_name AS reverser_name,
                   je.journal_number, rje.journal_number AS reversal_journal_number,
                   supp.name AS supplier_name,
                   ca.name AS cash_account_name,
                   ba.bank_name AS bank_account_name, ba.account_number AS bank_account_num,
                   ea.account_name AS expense_account_name, ea.account_code AS expense_account_code,
                   pa.account_name AS ap_account_name, pa.account_code AS ap_account_code
            FROM expenses e
            JOIN expense_categories ec ON e.expense_category_id = ec.id
            JOIN cost_centers cc ON e.cost_center_id = cc.id
            LEFT JOIN users u ON e.created_by = u.id
            LEFT JOIN users app ON e.approved_by = app.id
            LEFT JOIN users pst ON e.posted_by = pst.id
            LEFT JOIN users rev ON e.reversed_by = rev.id
            LEFT JOIN journal_entries je ON e.journal_entry_id = je.id
            LEFT JOIN journal_entries rje ON e.reversal_journal_entry_id = rje.id
            LEFT JOIN parties supp ON e.supplier_id = supp.id
            LEFT JOIN cash_accounts ca ON e.cash_account_id = ca.id
            LEFT JOIN bank_accounts ba ON e.bank_account_id = ba.id
            LEFT JOIN accounts ea ON e.expense_account_id = ea.id
            LEFT JOIN accounts pa ON e.accounts_payable_account_id = pa.id
            WHERE e.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $expense = $stmt->fetch();
        if (!$expense) return null;

        // Fetch attachments
        $expense['attachments'] = $this->getAttachments($id);
        return $expense;
    }

    public function getAttachments(int $expenseId): array {
        $stmt = $this->db->prepare("
            SELECT ea.*, u.full_name AS uploader_name 
            FROM expense_attachments ea
            LEFT JOIN users u ON ea.uploaded_by = u.id
            WHERE ea.expense_id = :expense_id
            ORDER BY ea.id ASC
        ");
        $stmt->execute(['expense_id' => $expenseId]);
        return $stmt->fetchAll();
    }

    /**
     * Compile reporting statistics and aggregates for Dashboard / Reporting view.
     */
    public function getExpenseReports(array $filters = []): array {
        $whereSql = " WHERE e.status = 'posted'";
        $params = [];

        if (!empty($filters['date_from'])) {
            $whereSql .= " AND e.expense_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $whereSql .= " AND e.expense_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        if (!empty($filters['cost_center_id'])) {
            $whereSql .= " AND e.cost_center_id = :cost_center_id";
            $params['cost_center_id'] = (int)$filters['cost_center_id'];
        }

        // 1. Total Posted Expense Amount
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount), 0.00) FROM expenses e " . $whereSql);
        $stmt->execute($params);
        $totalExpenses = (float)$stmt->fetchColumn();

        // 2. Expenses by Category
        $stmt = $this->db->prepare("
            SELECT ec.name AS category_name, SUM(e.amount) AS total_amount
            FROM expenses e
            JOIN expense_categories ec ON e.expense_category_id = ec.id
            " . $whereSql . "
            GROUP BY ec.id, ec.name
            ORDER BY total_amount DESC
        ");
        $stmt->execute($params);
        $byCategory = $stmt->fetchAll();

        // 3. Expenses by Cost Center
        $stmt = $this->db->prepare("
            SELECT cc.name AS cost_center_name, SUM(e.amount) AS total_amount
            FROM expenses e
            JOIN cost_centers cc ON e.cost_center_id = cc.id
            " . $whereSql . "
            GROUP BY cc.id, cc.name
            ORDER BY total_amount DESC
        ");
        $stmt->execute($params);
        $byCostCenter = $stmt->fetchAll();

        // 4. Expenses by Payment Method
        $stmt = $this->db->prepare("
            SELECT e.payment_method, SUM(e.amount) AS total_amount
            FROM expenses e
            " . $whereSql . "
            GROUP BY e.payment_method
            ORDER BY total_amount DESC
        ");
        $stmt->execute($params);
        $byPaymentMethod = $stmt->fetchAll();

        // 5. Monthly Trend (last 12 months)
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(e.expense_date, '%Y-%m') AS month, SUM(e.amount) AS total_amount
            FROM expenses e
            " . $whereSql . "
            GROUP BY month
            ORDER BY month ASC
            LIMIT 12
        ");
        $stmt->execute($params);
        $monthlyTrend = $stmt->fetchAll();

        // 6. Expenses by Source Module
        $stmt = $this->db->prepare("
            SELECT e.source_module, SUM(e.amount) AS total_amount
            FROM expenses e
            " . $whereSql . "
            GROUP BY e.source_module
            ORDER BY total_amount DESC
        ");
        $stmt->execute($params);
        $bySource = $stmt->fetchAll();

        // 7. Expenses by Supplier
        $stmt = $this->db->prepare("
            SELECT COALESCE(s.name, 'Non-Credit / Others') AS supplier_name, SUM(e.amount) AS total_amount
            FROM expenses e
            LEFT JOIN parties s ON e.supplier_id = s.id
            " . $whereSql . "
            GROUP BY s.id, s.name
            ORDER BY total_amount DESC
        ");
        $stmt->execute($params);
        $bySupplier = $stmt->fetchAll();

        return [
            'total_expenses' => $totalExpenses,
            'by_category' => $byCategory,
            'by_cost_center' => $byCostCenter,
            'by_payment_method' => $byPaymentMethod,
            'monthly_trend' => $monthlyTrend,
            'by_source' => $bySource,
            'by_supplier' => $bySupplier
        ];
    }
}
