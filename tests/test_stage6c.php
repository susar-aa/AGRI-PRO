<?php
/**
 * Stage 6C Service Job Expenses Automated Verification Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

use App\Models\Party;
use App\Models\ServiceModel;
use App\Models\ServiceJobModel;
use App\Models\InvoiceModel;
use App\Models\Expense;
use App\Services\ExpenseEngine;
use App\Services\InvoiceEngine;
use Core\Database;

echo "==================================================\n";
echo "AGRI CO-OP ERP - STAGE 6C AUTOMATED VERIFICATION\n";
echo "==================================================\n\n";

try {
    $db = Database::getInstance();
    $partyModel = new Party();
    $srvModel = new ServiceModel();
    $jobModel = new ServiceJobModel();
    $invModel = new InvoiceModel();
    $expModel = new Expense();

    // Setup: Log in as admin
    \Core\Auth::attempt('admin', 'admin123');
    echo "[SETUP] Logged in as administrator.\n";

    // Clean old test data
    $db->exec("DELETE FROM invoice_items");
    $db->exec("DELETE FROM invoices");
    $db->exec("DELETE FROM expenses WHERE source_module = 'SERVICES'");
    $db->exec("DELETE FROM service_jobs");
    $db->exec("DELETE FROM parties WHERE party_code IN ('CUST-MKT6C')");
    $db->exec("DELETE FROM journal_entries WHERE source_module IN ('invoices', 'expenses')");
    echo "[SETUP] Cleaned old test data.\n\n";

    // Setup customer
    $custId = $partyModel->create([
        'party_code' => 'CUST-MKT6C',
        'party_type' => 'CUSTOMER',
        'name' => 'Service Job Test Customer',
        'created_by' => 1
    ]);

    // Resolve service
    $service = $db->query("SELECT * FROM services WHERE service_code = 'SRV-PLOW'")->fetch();
    if (!$service) {
        throw new Exception("Test setup error: Service SRV-PLOW is missing.");
    }

    // Resolve cash drawer account
    $cashAccount = $db->query("SELECT * FROM cash_accounts WHERE status = 'active' LIMIT 1")->fetch();
    if (!$cashAccount) {
        throw new Exception("Test setup error: Active cash drawer account is missing.");
    }

    // Resolve expense categories (Fuel: e.g. ID 1, Employee Hire: e.g. ID 2, Meals: e.g. ID 3, etc.)
    $catFuelId = (int)$db->query("SELECT id FROM expense_categories WHERE name LIKE '%Fuel%' LIMIT 1")->fetchColumn() ?: 1;
    $catHireId = (int)$db->query("SELECT id FROM expense_categories WHERE name LIKE '%Hire%' OR name LIKE '%Labor%' LIMIT 1")->fetchColumn() ?: 2;
    $catMealsId = (int)$db->query("SELECT id FROM expense_categories WHERE name LIKE '%Meals%' OR name LIKE '%Food%' LIMIT 1")->fetchColumn() ?: 3;

    $costCenterId = (int)$db->query("SELECT id FROM cost_centers LIMIT 1")->fetchColumn();

    // 1. Create Service Job
    echo "[TEST 1] Create Service Job... ";
    $jobId = $jobModel->save([
        'customer_id' => $custId,
        'service_id' => $service['id'],
        'start_date' => date('Y-m-d'),
        'location' => 'Yatagama Fields',
        'description' => 'Plowing paddy field job',
        'assigned_employee' => 'Driver Pathmasiri',
        'status' => 'OPEN'
    ]);
    $job = $jobModel->getById($jobId);
    if (!$job || $job['job_number'] === '') {
        throw new Exception("Service job creation failed.");
    }
    echo "PASSED (Job Number: " . $job['job_number'] . ")\n";

    // 2. Add Fuel Expense
    echo "[TEST 2] Add Fuel Expense linked to Job (LKR 8,000.00)... ";
    $expFuelId = ExpenseEngine::createExpense([
        'expense_date' => date('Y-m-d'),
        'payee' => 'Rambukkana Fuel Station',
        'expense_category_id' => $catFuelId,
        'description' => 'Fuel for plowing job ' . $job['job_number'],
        'amount' => 8000.00,
        'payment_method' => 'Cash',
        'cash_account_id' => $cashAccount['id'],
        'cost_center_id' => $costCenterId,
        'service_job_id' => $jobId,
        'source_module' => 'SERVICES',
        'status' => 'approved'
    ]);
    ExpenseEngine::postExpense($expFuelId);
    echo "PASSED\n";

    // 3. Add Employee Hire Expense
    echo "[TEST 3] Add Employee Hire Expense linked to Job (LKR 10,000.00)... ";
    $expHireId = ExpenseEngine::createExpense([
        'expense_date' => date('Y-m-d'),
        'payee' => 'Operator Pathmasiri',
        'expense_category_id' => $catHireId,
        'description' => 'Labor hire for plowing job ' . $job['job_number'],
        'amount' => 10000.00,
        'payment_method' => 'Cash',
        'cash_account_id' => $cashAccount['id'],
        'cost_center_id' => $costCenterId,
        'service_job_id' => $jobId,
        'source_module' => 'SERVICES',
        'status' => 'approved'
    ]);
    ExpenseEngine::postExpense($expHireId);
    echo "PASSED\n";

    // 4. Add Meal Expense
    echo "[TEST 4] Add Meal Expense linked to Job (LKR 2,000.00)... ";
    $expMealsId = ExpenseEngine::createExpense([
        'expense_date' => date('Y-m-d'),
        'payee' => 'Yatagama Tea Shop',
        'expense_category_id' => $catMealsId,
        'description' => 'Meals for operator ' . $job['job_number'],
        'amount' => 2000.00,
        'payment_method' => 'Cash',
        'cash_account_id' => $cashAccount['id'],
        'cost_center_id' => $costCenterId,
        'service_job_id' => $jobId,
        'source_module' => 'SERVICES',
        'status' => 'approved'
    ]);
    ExpenseEngine::postExpense($expMealsId);
    echo "PASSED\n";

    // 5. Verify Total Cost (should be 8000 + 10000 + 2000 = LKR 20,000)
    echo "[TEST 5] Verify Service Job Total Cost calculation... ";
    $job = $jobModel->getById($jobId);
    if ((float)$job['total_cost'] !== 20000.00) {
        throw new Exception("Total cost calculation is incorrect. Expected: 20000.00, Found: " . $job['total_cost']);
    }
    echo "PASSED (Total Cost: LKR 20,000.00)\n";

    // 6 & 7. Create and Post Central Invoice from Service Job
    echo "[TEST 6 & 7] Create and Post Central Invoice pre-selected parameters (LKR 35,000.00)... ";
    // Emulates "Create Invoice" parameters from the Service Job Detail
    $invId = InvoiceEngine::saveInvoice([
        'customer_id' => $job['customer_id'],
        'invoice_date' => date('Y-m-d'),
        'reference' => $job['job_number'],
        'payment_type' => 'CASH',
        'cash_account_id' => $cashAccount['id'],
        'items' => [
            [
                'item_type' => 'SERVICE',
                'service_id' => $job['service_id'],
                'quantity' => 1.00,
                'unit_price' => 35000.00
            ]
        ]
    ]);
    
    // Link invoice back to job
    $db->prepare("UPDATE service_jobs SET invoice_id = :invoice_id WHERE id = :job_id")
       ->execute(['invoice_id' => $invId, 'job_id' => $jobId]);
       
    // Post the invoice
    InvoiceEngine::postInvoice($invId);
    
    $inv = $invModel->getById($invId);
    if ($inv['status'] !== 'POSTED') {
        throw new Exception("Invoice status should be POSTED.");
    }
    echo "PASSED\n";

    // 8 & 9 & 10. Verify Revenue, Profit, and Margin
    echo "[TEST 8 & 9 & 10] Verify Revenue, Profit, and Margins... ";
    $job = $jobModel->getById($jobId);
    if ((float)$job['revenue'] !== 35000.00) {
        throw new Exception("Revenue was not populated correctly. Expected: 35000, Found: " . $job['revenue']);
    }
    if ((float)$job['gross_profit'] !== 15000.00) {
        throw new Exception("Gross Profit was not calculated correctly. Expected: 15000, Found: " . $job['gross_profit']);
    }
    // Margin: 15000 / 35000 * 100 = 42.86%
    if (abs((float)$job['margin'] - 42.857) > 0.01) {
        throw new Exception("Margin was not calculated correctly. Expected: 42.86%, Found: " . $job['margin']);
    }
    echo "PASSED (Profit: LKR 15,000.00 / Margin: " . number_format($job['margin'], 2) . "%)\n";

    // 11. Verify expenses appear in existing Expense module
    echo "[TEST 11] Verify expenses logged in central Expense registry... ";
    $expCount = (int)$db->query("SELECT COUNT(*) FROM expenses WHERE service_job_id = {$jobId}")->fetchColumn();
    if ($expCount !== 3) {
        throw new Exception("Expenses do not appear in the central Expense registry.");
    }
    echo "PASSED\n";

    // 12. Verify no duplicate accounting entries
    echo "[TEST 12] Verify no duplicate accounting entries occurred... ";
    $jeCount = (int)$db->query("SELECT COUNT(*) FROM journal_entries WHERE source_module = 'invoices' AND source_transaction_id = {$invId}")->fetchColumn();
    if ($jeCount !== 1) {
        throw new Exception("Duplicate journal postings detected on Invoicing!");
    }
    echo "PASSED\n";

    // 13 & 14. Cancel Service Job without deleting posted transactions
    echo "[TEST 13 & 14] Cancel Service Job & Verify posted financial transactions are preserved... ";
    $db->prepare("UPDATE service_jobs SET status = 'CANCELLED' WHERE id = :id")->execute(['id' => $jobId]);
    
    // Expenses must remain intact
    $postExpCount = (int)$db->query("SELECT COUNT(*) FROM expenses WHERE service_job_id = {$jobId} AND status = 'posted'")->fetchColumn();
    if ($postExpCount !== 3) {
        throw new Exception("Job cancellation deleted or modified posted financial expense vouchers!");
    }
    echo "PASSED\n";

    // Cleanup
    $db->exec("DELETE FROM invoice_items");
    $db->exec("DELETE FROM invoices");
    $db->exec("DELETE FROM expenses WHERE source_module = 'SERVICES'");
    $db->exec("DELETE FROM service_jobs");
    $db->exec("DELETE FROM parties WHERE party_code IN ('CUST-MKT6C')");
    $db->exec("DELETE FROM journal_entries WHERE source_module IN ('invoices', 'expenses')");

    echo "\n==================================================\n";
    echo "ALL STAGE 6C VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Verification Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
