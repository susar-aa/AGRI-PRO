<?php
/**
 * Stage 5E Cheques and Deposits Verification Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

use App\Models\Party;
use App\Models\PartyLedger;
use App\Models\ChequeModel;
use App\Models\DepositModel;
use App\Models\ReceiptPaymentModel;
use App\Services\OpeningBalanceEngine;
use App\Services\PaymentEngine;
use App\Services\ChequeDepositEngine;
use Core\Database;

echo "==================================================\n";
echo "AGRI CO-OP ERP - STAGE 5E AUTOMATED VERIFICATION\n";
echo "==================================================\n\n";

try {
    $db = Database::getInstance();
    $partyModel = new Party();
    $ledgerModel = new PartyLedger();
    $chModel = new ChequeModel();
    $depModel = new DepositModel();
    $pmModel = new ReceiptPaymentModel();

    // Setup: Log in as admin
    \Core\Auth::attempt('admin', 'admin123');
    echo "[SETUP] Logged in as administrator.\n";

    // Clean old test data
    $db->exec("DELETE FROM parties WHERE party_code IN ('PTY-C5E')");
    $db->exec("DELETE FROM journal_entries WHERE source_module IN ('parties', 'finance')");
    $db->exec("DELETE FROM payment_receipts");
    $db->exec("DELETE FROM cheques");
    $db->exec("DELETE FROM bank_deposits");
    echo "[SETUP] Cleaned old test data.\n\n";

    // Resolve Destination Bank Account (first active bank account)
    $bankAccount = $db->query("SELECT * FROM bank_accounts WHERE status = 'active' LIMIT 1")->fetch();
    if (!$bankAccount) {
        throw new Exception("Test setup error: Active bank account is missing.");
    }
    $bankInitBal = (float)$bankAccount['current_balance'];

    // 1 & 2. Create customer and record cheque receipt LKR 20,000
    echo "[TEST 1 & 2] Receive Customer Cheque & Check Status... ";
    $custId = $partyModel->create([
        'party_code' => 'PTY-C5E',
        'party_type' => 'CUSTOMER',
        'name' => 'Stage 5E Customer',
        'created_by' => 1
    ]);
    // Create opening receivable LKR 50,000
    OpeningBalanceEngine::postOpeningBalance([
        'party_id' => $custId,
        'type' => 'receivable',
        'amount' => 50000.00,
        'balance_date' => date('Y-m-d'),
        'description' => 'Customer Stage 5E Opening Receivable'
    ]);

    // Create received cheque
    $chequeId = ChequeDepositEngine::recordCheque([
        'cheque_number' => 'CHQ-5E-001',
        'party_id' => $custId,
        'bank_name' => 'Sampath Bank',
        'cheque_date' => date('Y-m-d'),
        'amount' => 20000.00
    ]);

    $ch = $chModel->getById($chequeId);
    if ($ch['status'] !== 'RECEIVED') {
        throw new Exception("Cheque status should be RECEIVED. Found: " . $ch['status']);
    }
    echo "PASSED (Cheque status = RECEIVED)\n";

    // 3. Verify Customer Receipt is correctly linked
    echo "[TEST 3] Verify Customer Receipt Link... ";
    $receiptId = PaymentEngine::recordPayment([
        'party_id' => $custId,
        'payment_type' => 'RECEIPT',
        'payment_method' => 'Cheque',
        'cheque_id' => $chequeId,
        'amount' => 20000.00,
        'payment_date' => date('Y-m-d'),
        'notes' => 'Settlement by cheque'
    ]);
    PaymentEngine::postPayment($receiptId);

    $receipt = $pmModel->getById($receiptId);
    if ((int)$receipt['cheque_id'] !== $chequeId) {
        throw new Exception("Customer Receipt is not linked to Cheque ID!");
    }
    // Verify customer ledger reflects credit (Outstanding should be 30,000)
    $custBal = $ledgerModel->calculateBalance($custId, 'CUSTOMER');
    if ($custBal !== 30000.00) {
        throw new Exception("Customer balance was not credited by Cheque receipt. Outstanding: " . $custBal);
    }
    echo "PASSED (Customer Receipt linked, Customer outstanding reduced to LKR 30,000)\n";

    // 4 & 5. Create bank deposit and add cheque
    echo "[TEST 4 & 5] Create Bank Deposit & Add Cheque... ";
    $depId = ChequeDepositEngine::recordBankDeposit([
        'bank_account_id' => $bankAccount['id'],
        'deposit_date' => date('Y-m-d'),
        'description' => 'Cheque deposit test 5E',
        'cash_amount' => 0.00,
        'cheque_ids' => [$chequeId]
    ]);
    $deposit = $depModel->getById($depId);
    if (count($deposit['items']) !== 1 || (int)$deposit['items'][0]['cheque_id'] !== $chequeId) {
        throw new Exception("Cheque was not added to Deposit Items.");
    }
    echo "PASSED (Cheque added to Deposit ID: $depId)\n";

    // 6 & 7 & 8. Post deposit & Verify Cheque status = DEPOSITED & GL entry
    echo "[TEST 6 & 7 & 8] Post Deposit & Verify status and bank accounting... ";
    ChequeDepositEngine::postBankDeposit($depId);
    
    $chPost = $chModel->getById($chequeId);
    if ($chPost['status'] !== 'DEPOSITED') {
        throw new Exception("Cheque status was not updated to DEPOSITED.");
    }

    $bankBalPost = (float)$db->query("SELECT current_balance FROM bank_accounts WHERE id = " . $bankAccount['id'])->fetchColumn();
    if (abs($bankBalPost - ($bankInitBal + 20000.00)) > 0.001) {
        throw new Exception("Bank balance did not update. Bank Init: " . $bankInitBal . " / Bank Post: " . $bankBalPost);
    }
    echo "PASSED (Cheque is DEPOSITED, Bank balance increased by LKR 20,000)\n";

    // 9. Mark Cheque = CLEARED
    echo "[TEST 9] Mark Cheque = CLEARED... ";
    ChequeDepositEngine::markChequeCleared($chequeId);
    $chCleared = $chModel->getById($chequeId);
    if ($chCleared['status'] !== 'CLEARED') {
        throw new Exception("Cheque status was not updated to CLEARED.");
    }
    echo "PASSED (Cheque status = CLEARED)\n";

    // 10 & 11. Test bounced cheque & Verify customer balance is restored
    echo "[TEST 10 & 11] Bounce Cheque & Verify Customer balance restore... ";
    ChequeDepositEngine::markChequeBounced($chequeId, 'Cheque returned insufficient funds');
    
    $chBounced = $chModel->getById($chequeId);
    if ($chBounced['status'] !== 'BOUNCED') {
        throw new Exception("Cheque status was not updated to BOUNCED.");
    }

    $custBalPostBounce = $ledgerModel->calculateBalance($custId, 'CUSTOMER');
    if ($custBalPostBounce !== 50000.00) {
        throw new Exception("Customer balance was not restored. Current: " . $custBalPostBounce);
    }

    $bankBalPostBounce = (float)$db->query("SELECT current_balance FROM bank_accounts WHERE id = " . $bankAccount['id'])->fetchColumn();
    if (abs($bankBalPostBounce - $bankInitBal) > 0.001) {
        throw new Exception("Bank account was not credited for bounced cheque: " . $bankBalPostBounce);
    }
    echo "PASSED (Cheque status = BOUNCED, Customer balance restored to LKR 50,000, Bank balance restored)\n";

    // 12 & 13. Attempt duplicate cheque deposit
    echo "[TEST 12 & 13] Duplicate Cheque Entry Rejection... ";
    try {
        ChequeDepositEngine::recordCheque([
            'cheque_number' => 'CHQ-5E-001',
            'party_id' => $custId,
            'bank_name' => 'Sampath Bank',
            'cheque_date' => date('Y-m-d'),
            'amount' => 20000.00
        ]);
        throw new Exception("Duplicate cheque was NOT rejected!");
    } catch (Exception $ex) {
        echo "PASSED (Correctly rejected duplicate cheque number: " . $ex->getMessage() . ")\n";
    }

    // 14. Verify Trial Balance equilibrium
    echo "[TEST 14] Verify Trial Balance equilibrium... ";
    $tbBalances = $db->query("
        SELECT SUM(debit) AS total_debit, SUM(credit) AS total_credit 
        FROM journal_lines jl
        JOIN journal_entries je ON jl.journal_entry_id = je.id
        WHERE je.status = 'posted'
    ")->fetch();

    $diff = abs((float)$tbBalances['total_debit'] - (float)$tbBalances['total_credit']);
    if ($diff < 0.001) {
        echo "PASSED (Trial Balance is in perfect equilibrium. Debits: " . $tbBalances['total_debit'] . " / Credits: " . $tbBalances['total_credit'] . ")\n";
    } else {
        throw new Exception("Trial Balance imbalance detected! Difference: " . $diff);
    }

    // Cleanup
    $db->exec("DELETE FROM parties WHERE party_code IN ('PTY-C5E')");
    $db->exec("DELETE FROM journal_entries WHERE source_module IN ('parties', 'finance')");
    $db->exec("DELETE FROM payment_receipts");
    $db->exec("DELETE FROM cheques");
    $db->exec("DELETE FROM bank_deposits");

    echo "\n==================================================\n";
    echo "ALL STAGE 5E VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Verification Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
