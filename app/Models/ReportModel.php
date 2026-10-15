<?php
namespace App\Models;

use Core\Model;
use PDO;

class ReportModel extends Model {

    /**
     * Dashboard & Executive High-Level KPI Aggregates
     */
    public function getSummaryStats(): array {
        // 1. Total Assets
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(jl.debit - jl.credit), 0.00)
            FROM accounts a
            JOIN journal_lines jl ON a.id = jl.account_id
            JOIN journal_entries je ON jl.journal_entry_id = je.id AND je.status = 'posted'
            WHERE a.category = 'Asset' AND a.deleted_at IS NULL
        ");
        $totalAssets = (float)($stmt->fetchColumn() ?: 0.00);

        // 2. Total Liabilities
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(jl.credit - jl.debit), 0.00)
            FROM accounts a
            JOIN journal_lines jl ON a.id = jl.account_id
            JOIN journal_entries je ON jl.journal_entry_id = je.id AND je.status = 'posted'
            WHERE a.category = 'Liability' AND a.deleted_at IS NULL
        ");
        $totalLiabilities = (float)($stmt->fetchColumn() ?: 0.00);

        // 3. Operating Revenue (All time / YTD)
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(jl.credit - jl.debit), 0.00)
            FROM accounts a
            JOIN journal_lines jl ON a.id = jl.account_id
            JOIN journal_entries je ON jl.journal_entry_id = je.id AND je.status = 'posted'
            WHERE a.category = 'Revenue' AND a.deleted_at IS NULL
        ");
        $totalRevenue = (float)($stmt->fetchColumn() ?: 0.00);

        // 4. Operating Expenses + COGS
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(jl.debit - jl.credit), 0.00)
            FROM accounts a
            JOIN journal_lines jl ON a.id = jl.account_id
            JOIN journal_entries je ON jl.journal_entry_id = je.id AND je.status = 'posted'
            WHERE a.category IN ('COGS', 'Expense') AND a.deleted_at IS NULL
        ");
        $totalExpenses = (float)($stmt->fetchColumn() ?: 0.00);

        // 5. Total Liquid Cash & Bank Balances
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(jl.debit - jl.credit), 0.00)
            FROM accounts a
            JOIN journal_lines jl ON a.id = jl.account_id
            JOIN journal_entries je ON jl.journal_entry_id = je.id AND je.status = 'posted'
            WHERE a.account_code IN ('1110', '1120', '1115', '1130') AND a.deleted_at IS NULL
        ");
        $cashBankBalance = (float)($stmt->fetchColumn() ?: 0.00);

        // 6. Total Active Fixed Deposits Principal
        $stmt = $this->db->query("
            SELECT 
                COALESCE(SUM(maturity_amount - expected_interest), 0.00) AS total_principal,
                COUNT(*) AS total_active_count
            FROM member_fixed_deposits
            WHERE status = 'ACTIVE'
        ");
        $fdStats = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_principal' => 0, 'total_active_count' => 0];

        // 7. Total Physical Inventory Valuation
        $stmt = $this->db->query("SELECT COALESCE(SUM(inventory_value), 0.00) FROM inventory_balances");
        $inventoryValue = (float)($stmt->fetchColumn() ?: 0.00);

        // 8. Total Invoiced Sales Count & Sum
        $stmt = $this->db->query("
            SELECT COUNT(*) AS invoice_count, COALESCE(SUM(total), 0.00) AS total_sales 
            FROM invoices 
            WHERE status = 'POSTED'
        ");
        $salesStats = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['invoice_count' => 0, 'total_sales' => 0];

        return [
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => $totalRevenue - $totalExpenses,
            'cash_bank_balance' => $cashBankBalance,
            'active_fd_principal' => (float)$fdStats['total_principal'],
            'active_fd_count' => (int)$fdStats['total_active_count'],
            'inventory_valuation' => $inventoryValue,
            'total_sales' => (float)$salesStats['total_sales'],
            'invoice_count' => (int)$salesStats['invoice_count']
        ];
    }

    /**
     * Profit & Loss Statement (Income Statement)
     */
    public function getProfitAndLoss(?string $fromDate = null, ?string $toDate = null, ?int $costCenterId = null): array {
        $toDate = $toDate ?: date('Y-m-d');
        $params = [':to_date' => $toDate];

        $dateCondition = "je.transaction_date <= :to_date";
        if (!empty($fromDate)) {
            $dateCondition .= " AND je.transaction_date >= :from_date";
            $params[':from_date'] = $fromDate;
        }

        $ccCondition = "";
        if (!empty($costCenterId)) {
            $ccCondition = " AND je.cost_center_id = :cost_center_id";
            $params[':cost_center_id'] = $costCenterId;
        }

        $sql = "
            SELECT 
                a.id,
                a.account_code,
                a.account_name,
                a.category,
                a.normal_balance,
                COALESCE(SUM(jl.debit), 0.00) AS total_debit,
                COALESCE(SUM(jl.credit), 0.00) AS total_credit,
                CASE 
                    WHEN a.category = 'Revenue' THEN COALESCE(SUM(jl.credit - jl.debit), 0.00)
                    ELSE COALESCE(SUM(jl.debit - jl.credit), 0.00)
                END AS net_amount
            FROM accounts a
            JOIN journal_lines jl ON a.id = jl.account_id
            JOIN journal_entries je ON jl.journal_entry_id = je.id AND je.status = 'posted'
            WHERE a.category IN ('Revenue', 'COGS', 'Expense')
              AND a.deleted_at IS NULL
              AND {$dateCondition}
              {$ccCondition}
            GROUP BY a.id, a.account_code, a.account_name, a.category, a.normal_balance
            HAVING net_amount <> 0 OR total_debit <> 0 OR total_credit <> 0
            ORDER BY a.account_code ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $revenues = [];
        $cogs = [];
        $expenses = [];

        $totalRevenue = 0.00;
        $totalCogs = 0.00;
        $totalExpenses = 0.00;

        foreach ($rows as $row) {
            $amount = (float)$row['net_amount'];
            if ($row['category'] === 'Revenue') {
                $revenues[] = $row;
                $totalRevenue += $amount;
            } elseif ($row['category'] === 'COGS') {
                $cogs[] = $row;
                $totalCogs += $amount;
            } elseif ($row['category'] === 'Expense') {
                $expenses[] = $row;
                $totalExpenses += $amount;
            }
        }

        $grossProfit = $totalRevenue - $totalCogs;
        $netProfit = $grossProfit - $totalExpenses;

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'revenues' => $revenues,
            'total_revenue' => $totalRevenue,
            'cogs' => $cogs,
            'total_cogs' => $totalCogs,
            'gross_profit' => $grossProfit,
            'expenses' => $expenses,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit
        ];
    }

    /**
     * Balance Sheet Statement (Financial Position)
     */
    public function getBalanceSheet(?string $asOfDate = null): array {
        $asOfDate = $asOfDate ?: date('Y-m-d');
        $params = [':as_of_date' => $asOfDate];

        $sql = "
            SELECT 
                a.id,
                a.account_code,
                a.account_name,
                a.category,
                a.normal_balance,
                COALESCE(SUM(jl.debit), 0.00) AS total_debit,
                COALESCE(SUM(jl.credit), 0.00) AS total_credit,
                CASE 
                    WHEN a.category = 'Asset' THEN COALESCE(SUM(jl.debit - jl.credit), 0.00)
                    ELSE COALESCE(SUM(jl.credit - jl.debit), 0.00)
                END AS net_balance
            FROM accounts a
            JOIN journal_lines jl ON a.id = jl.account_id
            JOIN journal_entries je ON jl.journal_entry_id = je.id AND je.status = 'posted'
            WHERE a.category IN ('Asset', 'Liability', 'Equity')
              AND a.deleted_at IS NULL
              AND je.transaction_date <= :as_of_date
            GROUP BY a.id, a.account_code, a.account_name, a.category, a.normal_balance
            HAVING net_balance <> 0 OR total_debit <> 0 OR total_credit <> 0
            ORDER BY a.account_code ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $assets = [];
        $liabilities = [];
        $equity = [];

        $totalAssets = 0.00;
        $totalLiabilities = 0.00;
        $totalBaseEquity = 0.00;

        foreach ($rows as $row) {
            $bal = (float)$row['net_balance'];
            if ($row['category'] === 'Asset') {
                $assets[] = $row;
                $totalAssets += $bal;
            } elseif ($row['category'] === 'Liability') {
                $liabilities[] = $row;
                $totalLiabilities += $bal;
            } elseif ($row['category'] === 'Equity') {
                $equity[] = $row;
                $totalBaseEquity += $bal;
            }
        }

        // Calculate Retained Current Year Surplus / Loss up to $asOfDate
        $stmtEarnings = $this->db->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN a.category = 'Revenue' THEN (jl.credit - jl.debit) ELSE 0 END), 0.00) AS total_revenue,
                COALESCE(SUM(CASE WHEN a.category IN ('COGS', 'Expense') THEN (jl.debit - jl.credit) ELSE 0 END), 0.00) AS total_expense
            FROM accounts a
            JOIN journal_lines jl ON a.id = jl.account_id
            JOIN journal_entries je ON jl.journal_entry_id = je.id AND je.status = 'posted'
            WHERE a.category IN ('Revenue', 'COGS', 'Expense')
              AND a.deleted_at IS NULL
              AND je.transaction_date <= :as_of_date
        ");
        $stmtEarnings->execute($params);
        $pnlRow = $stmtEarnings->fetch(PDO::FETCH_ASSOC);
        $currentEarnings = (float)($pnlRow['total_revenue'] ?? 0) - (float)($pnlRow['total_expense'] ?? 0);

        $totalEquity = $totalBaseEquity + $currentEarnings;
        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
        $isBalanced = abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01;

        return [
            'as_of_date' => $asOfDate,
            'assets' => $assets,
            'total_assets' => $totalAssets,
            'liabilities' => $liabilities,
            'total_liabilities' => $totalLiabilities,
            'equity' => $equity,
            'total_base_equity' => $totalBaseEquity,
            'current_earnings' => $currentEarnings,
            'total_equity' => $totalEquity,
            'total_liabilities_and_equity' => $totalLiabilitiesAndEquity,
            'is_balanced' => $isBalanced
        ];
    }

    /**
     * Cash & Bank Book Detailed Ledger
     */
    public function getCashBankBook(?string $fromDate = null, ?string $toDate = null, ?int $accountId = null, int $limit = 100, int $offset = 0): array {
        $params = [];
        $where = "a.account_code IN ('1110', '1120', '1115', '1130') AND je.status = 'posted' AND a.deleted_at IS NULL";

        if (!empty($accountId)) {
            $where .= " AND a.id = :acc_id";
            $params[':acc_id'] = $accountId;
        }

        if (!empty($fromDate)) {
            $where .= " AND je.transaction_date >= :from_date";
            $params[':from_date'] = $fromDate;
        }

        if (!empty($toDate)) {
            $where .= " AND je.transaction_date <= :to_date";
            $params[':to_date'] = $toDate;
        }

        $sql = "
            SELECT 
                jl.id,
                je.transaction_date,
                je.journal_number,
                je.reference,
                je.source_module,
                jl.description,
                a.id AS account_id,
                a.account_code,
                a.account_name,
                jl.debit,
                jl.credit
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            JOIN accounts a ON jl.account_id = a.id
            WHERE {$where}
            ORDER BY je.transaction_date DESC, je.id DESC, jl.id DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCashBankBookCount(?string $fromDate = null, ?string $toDate = null, ?int $accountId = null): int {
        $params = [];
        $where = "a.account_code IN ('1110', '1120', '1115', '1130') AND je.status = 'posted' AND a.deleted_at IS NULL";

        if (!empty($accountId)) {
            $where .= " AND a.id = :acc_id";
            $params[':acc_id'] = $accountId;
        }
        if (!empty($fromDate)) {
            $where .= " AND je.transaction_date >= :from_date";
            $params[':from_date'] = $fromDate;
        }
        if (!empty($toDate)) {
            $where .= " AND je.transaction_date <= :to_date";
            $params[':to_date'] = $toDate;
        }

        $sql = "
            SELECT COUNT(*) 
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            JOIN accounts a ON jl.account_id = a.id
            WHERE {$where}
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Sales Summary Report
     */
    public function getSalesSummary(array $filters = [], int $limit = 50, int $offset = 0): array {
        $params = [];
        $where = "1=1";

        if (!empty($filters['from_date'])) {
            $where .= " AND inv.invoice_date >= :from_date";
            $params[':from_date'] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where .= " AND inv.invoice_date <= :to_date";
            $params[':to_date'] = $filters['to_date'];
        }
        if (!empty($filters['customer_id'])) {
            $where .= " AND inv.customer_id = :customer_id";
            $params[':customer_id'] = (int)$filters['customer_id'];
        }
        if (!empty($filters['payment_type'])) {
            $where .= " AND inv.payment_type = :payment_type";
            $params[':payment_type'] = $filters['payment_type'];
        }
        if (!empty($filters['status'])) {
            $where .= " AND inv.status = :status";
            $params[':status'] = $filters['status'];
        }

        $sql = "
            SELECT 
                inv.id,
                inv.invoice_number,
                inv.invoice_date,
                inv.payment_type,
                inv.subtotal,
                inv.discount,
                inv.total,
                inv.status,
                p.id AS customer_id,
                p.name AS customer_name,
                p.party_code AS customer_code,
                p.phone AS customer_phone
            FROM invoices inv
            LEFT JOIN parties p ON inv.customer_id = p.id
            WHERE {$where}
            ORDER BY inv.invoice_date DESC, inv.id DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSalesSummaryCount(array $filters = []): int {
        $params = [];
        $where = "1=1";

        if (!empty($filters['from_date'])) {
            $where .= " AND inv.invoice_date >= :from_date";
            $params[':from_date'] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where .= " AND inv.invoice_date <= :to_date";
            $params[':to_date'] = $filters['to_date'];
        }
        if (!empty($filters['customer_id'])) {
            $where .= " AND inv.customer_id = :customer_id";
            $params[':customer_id'] = (int)$filters['customer_id'];
        }
        if (!empty($filters['payment_type'])) {
            $where .= " AND inv.payment_type = :payment_type";
            $params[':payment_type'] = $filters['payment_type'];
        }
        if (!empty($filters['status'])) {
            $where .= " AND inv.status = :status";
            $params[':status'] = $filters['status'];
        }

        $sql = "SELECT COUNT(*) FROM invoices inv WHERE {$where}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getSalesSummaryAggregates(array $filters = []): array {
        $params = [];
        $where = "1=1";

        if (!empty($filters['from_date'])) {
            $where .= " AND inv.invoice_date >= :from_date";
            $params[':from_date'] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where .= " AND inv.invoice_date <= :to_date";
            $params[':to_date'] = $filters['to_date'];
        }
        if (!empty($filters['customer_id'])) {
            $where .= " AND inv.customer_id = :customer_id";
            $params[':customer_id'] = (int)$filters['customer_id'];
        }
        if (!empty($filters['payment_type'])) {
            $where .= " AND inv.payment_type = :payment_type";
            $params[':payment_type'] = $filters['payment_type'];
        }
        if (!empty($filters['status'])) {
            $where .= " AND inv.status = :status";
            $params[':status'] = $filters['status'];
        }

        $sql = "
            SELECT 
                COUNT(*) AS total_invoices,
                COALESCE(SUM(inv.subtotal), 0.00) AS total_subtotal,
                COALESCE(SUM(inv.discount), 0.00) AS total_discount,
                COALESCE(SUM(inv.total), 0.00) AS total_sales,
                COALESCE(SUM(CASE WHEN inv.payment_type = 'CASH' THEN inv.total ELSE 0 END), 0.00) AS cash_sales,
                COALESCE(SUM(CASE WHEN inv.payment_type = 'CREDIT' THEN inv.total ELSE 0 END), 0.00) AS credit_sales,
                COALESCE(SUM(CASE WHEN inv.payment_type = 'BANK' THEN inv.total ELSE 0 END), 0.00) AS bank_sales,
                COALESCE(SUM(CASE WHEN inv.payment_type = 'CHEQUE' THEN inv.total ELSE 0 END), 0.00) AS cheque_sales
            FROM invoices inv
            WHERE {$where}
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_invoices' => 0,
            'total_subtotal' => 0,
            'total_discount' => 0,
            'total_sales' => 0,
            'cash_sales' => 0,
            'credit_sales' => 0,
            'bank_sales' => 0,
            'cheque_sales' => 0
        ];
    }

    /**
     * Accounts Receivable (AR) Aging Report
     */
    public function getArAgingReport(array $filters = []): array {
        $params = [];
        $where = "p.party_type IN ('CUSTOMER', 'BOTH') AND p.status = 'active'";

        if (!empty($filters['search'])) {
            $where .= " AND (p.name LIKE :search_name OR p.party_code LIKE :search_code OR p.phone LIKE :search_phone)";
            $term = $filters['search'] . '%';
            $params[':search_name'] = $term;
            $params[':search_code'] = $term;
            $params[':search_phone'] = $term;
        }

        $sql = "
            SELECT 
                p.id AS party_id,
                p.party_code,
                p.name AS customer_name,
                p.phone,
                p.credit_limit,
                COALESCE(SUM(inv.total), 0.00) AS total_receivable,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURDATE(), inv.invoice_date) <= 30 THEN inv.total ELSE 0 END), 0.00) AS current_0_30,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURDATE(), inv.invoice_date) BETWEEN 31 AND 60 THEN inv.total ELSE 0 END), 0.00) AS days_31_60,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURDATE(), inv.invoice_date) BETWEEN 61 AND 90 THEN inv.total ELSE 0 END), 0.00) AS days_61_90,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURDATE(), inv.invoice_date) > 90 THEN inv.total ELSE 0 END), 0.00) AS days_over_90
            FROM parties p
            JOIN invoices inv ON p.id = inv.customer_id AND inv.status = 'POSTED' AND inv.payment_type = 'CREDIT'
            WHERE {$where}
            GROUP BY p.id, p.party_code, p.name, p.phone, p.credit_limit
            HAVING total_receivable > 0
            ORDER BY total_receivable DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Purchases & GRN Summary Report
     */
    public function getPurchasesSummary(array $filters = [], int $limit = 50, int $offset = 0): array {
        $params = [];
        $where = "sl.movement_type = 'GRN'";

        if (!empty($filters['from_date'])) {
            $where .= " AND sl.movement_date >= :from_date";
            $params[':from_date'] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where .= " AND sl.movement_date <= :to_date";
            $params[':to_date'] = $filters['to_date'];
        }
        if (!empty($filters['supplier_id'])) {
            $where .= " AND sl.source_transaction_id = :sup_id";
            $params[':sup_id'] = (int)$filters['supplier_id'];
        }

        $sql = "
            SELECT 
                sl.id,
                sl.movement_date,
                sl.reference_number,
                sl.quantity_in AS quantity,
                sl.unit_cost,
                sl.total_cost,
                p.product_code,
                p.name_en AS product_name,
                loc.name AS warehouse_name,
                COALESCE(sup.name, 'Direct/Internal') AS supplier_name
            FROM stock_ledger sl
            JOIN products p ON sl.product_id = p.id
            JOIN inventory_locations loc ON sl.location_id = loc.id
            LEFT JOIN parties sup ON sl.source_transaction_id = sup.id
            WHERE {$where}
            ORDER BY sl.movement_date DESC, sl.id DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPurchasesSummaryCount(array $filters = []): int {
        $params = [];
        $where = "sl.movement_type = 'GRN'";

        if (!empty($filters['from_date'])) {
            $where .= " AND sl.movement_date >= :from_date";
            $params[':from_date'] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where .= " AND sl.movement_date <= :to_date";
            $params[':to_date'] = $filters['to_date'];
        }
        if (!empty($filters['supplier_id'])) {
            $where .= " AND sl.source_transaction_id = :sup_id";
            $params[':sup_id'] = (int)$filters['supplier_id'];
        }

        $sql = "SELECT COUNT(*) FROM stock_ledger sl WHERE {$where}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Accounts Payable (AP) Aging Report
     */
    public function getApAgingReport(array $filters = []): array {
        $params = [];
        $where = "p.party_type IN ('SUPPLIER', 'BOTH') AND p.status = 'active'";

        if (!empty($filters['search'])) {
            $where .= " AND (p.name LIKE :search_name OR p.party_code LIKE :search_code OR p.phone LIKE :search_phone)";
            $term = $filters['search'] . '%';
            $params[':search_name'] = $term;
            $params[':search_code'] = $term;
            $params[':search_phone'] = $term;
        }

        $sql = "
            SELECT 
                p.id AS party_id,
                p.party_code,
                p.name AS supplier_name,
                p.phone,
                COALESCE(SUM(exp.amount), 0.00) AS total_payable,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURDATE(), exp.expense_date) <= 30 THEN exp.amount ELSE 0 END), 0.00) AS current_0_30,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURDATE(), exp.expense_date) BETWEEN 31 AND 60 THEN exp.amount ELSE 0 END), 0.00) AS days_31_60,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURDATE(), exp.expense_date) BETWEEN 61 AND 90 THEN exp.amount ELSE 0 END), 0.00) AS days_61_90,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURDATE(), exp.expense_date) > 90 THEN exp.amount ELSE 0 END), 0.00) AS days_over_90
            FROM parties p
            JOIN expenses exp ON p.id = exp.supplier_id AND exp.status = 'posted' AND exp.payment_method = 'Credit'
            WHERE {$where}
            GROUP BY p.id, p.party_code, p.name, p.phone
            HAVING total_payable > 0
            ORDER BY total_payable DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Expense Analysis & Category Distribution Report
     */
    public function getExpenseAnalysis(?string $fromDate = null, ?string $toDate = null, ?int $costCenterId = null): array {
        $params = [];
        $where = "e.status = 'posted'";

        if (!empty($fromDate)) {
            $where .= " AND e.expense_date >= :from_date";
            $params[':from_date'] = $fromDate;
        }
        if (!empty($toDate)) {
            $where .= " AND e.expense_date <= :to_date";
            $params[':to_date'] = $toDate;
        }
        if (!empty($costCenterId)) {
            $where .= " AND e.cost_center_id = :cost_center_id";
            $params[':cost_center_id'] = $costCenterId;
        }

        $sql = "
            SELECT 
                ec.id AS category_id,
                ec.name AS category_name,
                COALESCE(a.account_code, '') AS account_code,
                COALESCE(a.account_name, '') AS account_name,
                COUNT(e.id) AS expense_count,
                COALESCE(SUM(e.amount), 0.00) AS total_amount
            FROM expenses e
            JOIN expense_categories ec ON e.expense_category_id = ec.id
            LEFT JOIN accounts a ON ec.linked_account_id = a.id
            WHERE {$where}
            GROUP BY ec.id, ec.name, a.account_code, a.account_name
            ORDER BY total_amount DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalSum = array_sum(array_column($rows, 'total_amount'));
        foreach ($rows as &$r) {
            $r['percentage'] = $totalSum > 0 ? round(($r['total_amount'] / $totalSum) * 100, 2) : 0.00;
        }
        unset($r);

        return [
            'rows' => $rows,
            'total_amount' => $totalSum
        ];
    }

    /**
     * Inventory Valuation Summary
     */
    public function getInventoryValuation(?int $locationId = null, ?int $categoryId = null): array {
        $params = [];
        $where = "1=1";

        if (!empty($locationId)) {
            $where .= " AND ib.location_id = :loc_id";
            $params[':loc_id'] = $locationId;
        }
        if (!empty($categoryId)) {
            $where .= " AND p.category_id = :cat_id";
            $params[':cat_id'] = $categoryId;
        }

        $sql = "
            SELECT 
                p.id AS product_id,
                p.product_code,
                p.name_en AS product_name,
                COALESCE(pc.name, 'Uncategorized') AS category_name,
                loc.name AS warehouse_name,
                ib.quantity_on_hand,
                ib.average_cost,
                ib.inventory_value
            FROM inventory_balances ib
            JOIN products p ON ib.product_id = p.id
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            JOIN inventory_locations loc ON ib.location_id = loc.id
            WHERE {$where}
            ORDER BY p.name_en ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Stock Movement Audit Trail / Stock Ledger
     */
    public function getStockMovementReport(array $filters = [], int $limit = 50, int $offset = 0): array {
        $params = [];
        $where = "1=1";

        if (!empty($filters['from_date'])) {
            $where .= " AND sl.movement_date >= :from_date";
            $params[':from_date'] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where .= " AND sl.movement_date <= :to_date";
            $params[':to_date'] = $filters['to_date'];
        }
        if (!empty($filters['product_id'])) {
            $where .= " AND sl.product_id = :prod_id";
            $params[':prod_id'] = (int)$filters['product_id'];
        }
        if (!empty($filters['location_id'])) {
            $where .= " AND sl.location_id = :loc_id";
            $params[':loc_id'] = (int)$filters['location_id'];
        }
        if (!empty($filters['movement_type'])) {
            $where .= " AND sl.movement_type = :mov_type";
            $params[':mov_type'] = $filters['movement_type'];
        }

        $sql = "
            SELECT 
                sl.id,
                sl.movement_date,
                sl.reference_number,
                sl.movement_type,
                sl.source_module,
                sl.quantity_in,
                sl.quantity_out,
                sl.unit_cost,
                sl.total_cost,
                sl.balance_quantity,
                sl.balance_value,
                p.product_code,
                p.name_en AS product_name,
                loc.name AS warehouse_name,
                COALESCE(u.full_name, 'System') AS user_name
            FROM stock_ledger sl
            JOIN products p ON sl.product_id = p.id
            JOIN inventory_locations loc ON sl.location_id = loc.id
            LEFT JOIN users u ON sl.created_by = u.id
            WHERE {$where}
            ORDER BY sl.movement_date DESC, sl.id DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStockMovementReportCount(array $filters = []): int {
        $params = [];
        $where = "1=1";

        if (!empty($filters['from_date'])) {
            $where .= " AND sl.movement_date >= :from_date";
            $params[':from_date'] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where .= " AND sl.movement_date <= :to_date";
            $params[':to_date'] = $filters['to_date'];
        }
        if (!empty($filters['product_id'])) {
            $where .= " AND sl.product_id = :prod_id";
            $params[':prod_id'] = (int)$filters['product_id'];
        }
        if (!empty($filters['location_id'])) {
            $where .= " AND sl.location_id = :loc_id";
            $params[':loc_id'] = (int)$filters['location_id'];
        }
        if (!empty($filters['movement_type'])) {
            $where .= " AND sl.movement_type = :mov_type";
            $params[':mov_type'] = $filters['movement_type'];
        }

        $sql = "SELECT COUNT(*) FROM stock_ledger sl WHERE {$where}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Fixed Deposits Portfolio Report
     */
    public function getFixedDepositsReport(array $filters = []): array {
        $params = [];
        $where = "1=1";

        if (!empty($filters['status'])) {
            $where .= " AND fd.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['member_id'])) {
            $where .= " AND fd.member_id = :member_id";
            $params[':member_id'] = (int)$filters['member_id'];
        }

        $sql = "
            SELECT 
                fd.id,
                fd.deposit_number,
                m.member_no,
                m.full_name AS member_name,
                m.phone,
                m.nic,
                fd.deposit_date,
                fd.start_date,
                fd.maturity_date,
                fd.term_months,
                fd.interest_rate,
                (fd.maturity_amount - fd.expected_interest) AS principal_amount,
                fd.expected_interest,
                fd.maturity_amount,
                fd.payment_method,
                fd.status,
                DATEDIFF(fd.maturity_date, CURDATE()) AS days_to_maturity
            FROM member_fixed_deposits fd
            JOIN coop_members m ON fd.member_id = m.id
            WHERE {$where}
            ORDER BY fd.start_date DESC, fd.id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Machinery & Equipment Utilization Report
     */
    public function getMachineryUtilizationReport(array $filters = []): array {
        $params = [];
        $where = "1=1";

        if (!empty($filters['status'])) {
            $where .= " AND m.status = :status";
            $params[':status'] = $filters['status'];
        }

        $sql = "
            SELECT 
                m.id,
                m.machinery_code,
                m.machinery_name,
                m.rental_unit,
                m.default_rental_rate,
                m.status,
                COUNT(mr.id) AS total_rentals,
                COALESCE(SUM(mr.quantity), 0.00) AS total_units_rented,
                COALESCE(SUM(mr.total_charge), 0.00) AS total_revenue
            FROM machinery m
            LEFT JOIN machinery_rentals mr ON m.id = mr.machinery_id AND mr.status = 'COMPLETED'
            WHERE {$where}
            GROUP BY m.id, m.machinery_code, m.machinery_name, m.rental_unit, m.default_rental_rate, m.status
            ORDER BY total_revenue DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cost Centers & Operational Divisions Performance
     */
    public function getCostCentersPerformance(?string $fromDate = null, ?string $toDate = null): array {
        $params = [];
        $dateJoin = "";

        if (!empty($fromDate) || !empty($toDate)) {
            if (!empty($fromDate) && !empty($toDate)) {
                $dateJoin = " AND je.transaction_date BETWEEN :from_date AND :to_date";
                $params[':from_date'] = $fromDate;
                $params[':to_date'] = $toDate;
            } elseif (!empty($fromDate)) {
                $dateJoin = " AND je.transaction_date >= :from_date";
                $params[':from_date'] = $fromDate;
            } else {
                $dateJoin = " AND je.transaction_date <= :to_date";
                $params[':to_date'] = $toDate;
            }
        }

        $sql = "
            SELECT 
                cc.id,
                cc.code,
                cc.name,
                COALESCE(SUM(CASE WHEN a.category = 'Revenue' THEN (jl.credit - jl.debit) ELSE 0 END), 0.00) AS total_revenue,
                COALESCE(SUM(CASE WHEN a.category IN ('COGS', 'Expense') THEN (jl.debit - jl.credit) ELSE 0 END), 0.00) AS total_expense,
                COALESCE(SUM(CASE WHEN a.category = 'Revenue' THEN (jl.credit - jl.debit) ELSE 0 END), 0.00) - 
                COALESCE(SUM(CASE WHEN a.category IN ('COGS', 'Expense') THEN (jl.debit - jl.credit) ELSE 0 END), 0.00) AS net_margin
            FROM cost_centers cc
            LEFT JOIN journal_entries je ON cc.id = je.cost_center_id AND je.status = 'posted' {$dateJoin}
            LEFT JOIN journal_lines jl ON je.id = jl.journal_entry_id
            LEFT JOIN accounts a ON jl.account_id = a.id AND a.deleted_at IS NULL
            WHERE cc.is_active = 1
            GROUP BY cc.id, cc.code, cc.name
            ORDER BY total_revenue DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Existing Directory Report Methods
     */
    public function getDirectoryReport(array $filters = []): array {
        $type = strtolower(trim($filters['type'] ?? 'all'));
        $search = trim($filters['search'] ?? '');
        $status = strtolower(trim($filters['status'] ?? ''));

        $results = [];

        if ($type === 'all' || $type === 'director' || $type === 'directors') {
            $directors = $this->fetchDirectors($search, $status);
            $results = array_merge($results, $directors);
        }

        if ($type === 'all' || $type === 'member' || $type === 'members') {
            $members = $this->fetchMembers($search, $status);
            $results = array_merge($results, $members);
        }

        if ($type === 'all' || $type === 'staff') {
            $staff = $this->fetchStaff($search, $status);
            $results = array_merge($results, $staff);
        }

        if ($type === 'all' || $type === 'customer' || $type === 'customers') {
            $customers = $this->fetchCustomers($search, $status);
            $results = array_merge($results, $customers);
        }

        usort($results, function($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        return $results;
    }

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
            $term = $search . '%';
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
            $term = $search . '%';
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
            $term = $search . '%';
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
            $term = $search . '%';
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
