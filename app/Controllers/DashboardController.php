<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class DashboardController extends Controller {

    public function index(): void {
        Auth::requirePermission('dashboard.view');

        $db = Database::getInstance();

        // Calculate actual balances from ledger/accounts
        // 1. Total Revenue (Category = 'Revenue')
        $stmt = $db->query("
            SELECT COALESCE(SUM(jl.credit - jl.debit), 0.00) 
            FROM journal_lines jl 
            JOIN accounts a ON jl.account_id = a.id 
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            WHERE a.category = 'Revenue' AND je.status = 'posted'
        ");
        $totalRevenue = (float)$stmt->fetchColumn();

        // 2. Total Expenses (Category = 'Expense' or 'COGS')
        $stmt = $db->query("
            SELECT COALESCE(SUM(jl.debit - jl.credit), 0.00) 
            FROM journal_lines jl 
            JOIN accounts a ON jl.account_id = a.id 
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            WHERE a.category IN ('Expense', 'COGS') AND je.status = 'posted'
        ");
        $totalExpenses = (float)$stmt->fetchColumn();

        $netProfit = $totalRevenue - $totalExpenses;

        // 3. Cash Balance (Account Code 1110 & 1130)
        $stmt = $db->query("
            SELECT COALESCE(SUM(jl.debit - jl.credit), 0.00) 
            FROM journal_lines jl 
            JOIN accounts a ON jl.account_id = a.id 
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            WHERE a.account_code IN ('1110', '1130') AND je.status = 'posted'
        ");
        $cashBalance = (float)$stmt->fetchColumn();

        // 4. Bank Balance (Account Code 1120)
        $stmt = $db->query("
            SELECT COALESCE(SUM(jl.debit - jl.credit), 0.00) 
            FROM journal_lines jl 
            JOIN accounts a ON jl.account_id = a.id 
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            WHERE a.account_code = '1120' AND je.status = 'posted'
        ");
        $bankBalance = (float)$stmt->fetchColumn();

        // 5. Accounts Receivable (1140)
        $stmt = $db->query("
            SELECT COALESCE(SUM(jl.debit - jl.credit), 0.00) 
            FROM journal_lines jl 
            JOIN accounts a ON jl.account_id = a.id 
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            WHERE a.account_code = '1140' AND je.status = 'posted'
        ");
        $accountsReceivable = (float)$stmt->fetchColumn();

        // 6. Accounts Payable (2110)
        $stmt = $db->query("
            SELECT COALESCE(SUM(jl.credit - jl.debit), 0.00) 
            FROM journal_lines jl 
            JOIN accounts a ON jl.account_id = a.id 
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            WHERE a.account_code = '2110' AND je.status = 'posted'
        ");
        $accountsPayable = (float)$stmt->fetchColumn();

        // 7. Inventory Value (1150, 1160, 1170)
        $stmt = $db->query("
            SELECT COALESCE(SUM(jl.debit - jl.credit), 0.00) 
            FROM journal_lines jl 
            JOIN accounts a ON jl.account_id = a.id 
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            WHERE a.account_code IN ('1150', '1160', '1170') AND je.status = 'posted'
        ");
        $inventoryValue = (float)$stmt->fetchColumn();

        // Cost Center Overview
        $ccStmt = $db->query("
            SELECT cc.id, cc.code, cc.name,
                COALESCE(SUM(CASE WHEN a.category = 'Revenue' THEN (jl.credit - jl.debit) ELSE 0 END), 0.00) AS revenue,
                COALESCE(SUM(CASE WHEN a.category IN ('Expense', 'COGS') THEN (jl.debit - jl.credit) ELSE 0 END), 0.00) AS expense
            FROM cost_centers cc
            LEFT JOIN journal_entries je ON je.cost_center_id = cc.id AND je.status = 'posted'
            LEFT JOIN journal_lines jl ON jl.journal_entry_id = je.id
            LEFT JOIN accounts a ON jl.account_id = a.id
            WHERE cc.is_active = 1
            GROUP BY cc.id, cc.code, cc.name
            ORDER BY cc.code ASC
        ");
        $costCenterStats = $ccStmt->fetchAll();

        $this->render('dashboard/index', [
            'pageTitle' => 'ERP Dashboard & Key Metrics',
            'activeNav' => 'dashboard',
            'kpi' => [
                'revenue' => $totalRevenue,
                'expenses' => $totalExpenses,
                'net_profit' => $netProfit,
                'cash' => $cashBalance,
                'bank' => $bankBalance,
                'receivable' => $accountsReceivable,
                'payable' => $accountsPayable,
                'inventory' => $inventoryValue
            ],
            'costCenterStats' => $costCenterStats
        ]);
    }
}
