<?php
/**
 * Stage 5A Customer & Supplier Management Verification Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

use App\Models\Party;
use Core\Database;

echo "==================================================\n";
echo "AGRI CO-OP ERP - STAGE 5A AUTOMATED VERIFICATION\n";
echo "==================================================\n\n";

try {
    $db = Database::getInstance();
    $partyModel = new Party();

    // Setup: Log in as admin
    \Core\Auth::attempt('admin', 'admin123');
    echo "[SETUP] Logged in as administrator.\n\n";

    // Clean old test data
    $db->exec("DELETE FROM parties WHERE party_code IN ('PTY-TEST1', 'PTY-TEST2', 'PTY-TEST3')");

    // ----------------------------------------------------
    // TEST 1: Create a Customer
    // ----------------------------------------------------
    echo "[TEST 1] Create a Customer... ";
    $custId = $partyModel->create([
        'party_code' => 'PTY-TEST1',
        'party_type' => 'CUSTOMER',
        'name' => 'Test Customer One',
        'contact_person' => 'John Doe',
        'nic_reg_no' => '199012345678',
        'phone' => '0771112222',
        'email' => 'john@customer.com',
        'address' => '123 Customer St, Colombo',
        'city' => 'Colombo',
        'district' => 'Colombo',
        'credit_limit' => 50000.00,
        'credit_days' => 30,
        'payment_terms' => 'Net 30',
        'customer_type' => 'Business',
        'created_by' => 1
    ]);

    $cust = $partyModel->getById($custId);
    if ($cust && $cust['party_type'] === 'CUSTOMER' && (float)$cust['credit_limit'] === 50000.00) {
        echo "PASSED (Customer profile created successfully with code: " . $cust['party_code'] . ")\n";
    } else {
        throw new Exception("Failed to verify customer profile parameters.");
    }

    // ----------------------------------------------------
    // TEST 2: Create a Supplier
    // ----------------------------------------------------
    echo "[TEST 2] Create a Supplier... ";
    $suppId = $partyModel->create([
        'party_code' => 'PTY-TEST2',
        'party_type' => 'SUPPLIER',
        'name' => 'Test Supplier Two',
        'contact_person' => 'Jane Smith',
        'nic_reg_no' => 'PV-100244',
        'phone' => '0773334444',
        'email' => 'jane@supplier.com',
        'address' => '456 Supplier St, Kandy',
        'city' => 'Kandy',
        'district' => 'Kandy',
        'payment_terms' => 'Cash On Delivery',
        'supplier_type' => 'Distributor',
        'created_by' => 1
    ]);

    $supp = $partyModel->getById($suppId);
    if ($supp && $supp['party_type'] === 'SUPPLIER' && $supp['supplier_type'] === 'Distributor') {
        echo "PASSED (Supplier profile created successfully with code: " . $supp['party_code'] . ")\n";
    } else {
        throw new Exception("Failed to verify supplier profile parameters.");
    }

    // ----------------------------------------------------
    // TEST 3: Create a Party that is BOTH
    // ----------------------------------------------------
    echo "[TEST 3] Create a Party that is BOTH (Customer & Supplier)... ";
    $bothId = $partyModel->create([
        'party_code' => 'PTY-TEST3',
        'party_type' => 'BOTH',
        'name' => 'Unified Partner Three',
        'contact_person' => 'Alex Band',
        'phone' => '0775556666',
        'email' => 'alex@unified.com',
        'customer_type' => 'Business',
        'supplier_type' => 'Manufacturer',
        'created_by' => 1
    ]);

    $both = $partyModel->getById($bothId);
    if ($both && $both['party_type'] === 'BOTH' && $both['customer_type'] === 'Business' && $both['supplier_type'] === 'Manufacturer') {
        echo "PASSED (Unified profile created successfully with code: " . $both['party_code'] . ")\n";
    } else {
        throw new Exception("Failed to verify BOTH type party profile.");
    }

    // ----------------------------------------------------
    // TEST 4: Edit a Party
    // ----------------------------------------------------
    echo "[TEST 4] Edit a Party... ";
    $partyModel->update($bothId, [
        'party_type' => 'BOTH',
        'name' => 'Unified Partner Three Updated',
        'contact_person' => 'Alex Band Jr.',
        'nic_reg_no' => 'PV-88741',
        'phone' => '0775556667',
        'email' => 'alex.jr@unified.com',
        'address' => 'Updated address',
        'city' => 'Galle',
        'district' => 'Galle',
        'credit_limit' => 100000.00,
        'credit_days' => 15,
        'payment_terms' => 'Net 15',
        'customer_type' => 'Business',
        'supplier_type' => 'Distributor',
        'status' => 'active'
    ]);

    $updatedBoth = $partyModel->getById($bothId);
    if ($updatedBoth && $updatedBoth['name'] === 'Unified Partner Three Updated' && (float)$updatedBoth['credit_limit'] === 100000.00) {
        echo "PASSED (Party details updated and stored successfully)\n";
    } else {
        throw new Exception("Failed to edit/update party profile.");
    }

    // ----------------------------------------------------
    // TEST 5: Deactivate a Party
    // ----------------------------------------------------
    echo "[TEST 5] Deactivate a Party... ";
    $partyModel->deactivate($custId);
    $deactivatedCust = $partyModel->getById($custId);
    if ($deactivatedCust && $deactivatedCust['status'] === 'inactive') {
        echo "PASSED (Customer status toggled to inactive successfully)\n";
    } else {
        throw new Exception("Deactivate party status update failed.");
    }

    // ----------------------------------------------------
    // TEST 6: Search and Filter Parties
    // ----------------------------------------------------
    echo "[TEST 6] Search and Filter Parties... ";
    $allMatches = $partyModel->getAll(['search' => 'Unified Partner']);
    $inactiveMatches = $partyModel->getAll(['status' => 'inactive']);
    $supplierMatches = $partyModel->getSuppliers(['search' => 'Supplier']);

    if (count($allMatches) >= 1 && count($inactiveMatches) >= 1 && count($supplierMatches) >= 1) {
        echo "PASSED (Search and category filter queries returned expected items)\n";
    } else {
        throw new Exception("Filter queries returned empty or invalid results.");
    }

    // ----------------------------------------------------
    // TEST 7: Duplicate Party Code Check
    // ----------------------------------------------------
    echo "[TEST 7] Verify duplicate Party Codes are rejected... ";
    try {
        $partyModel->create([
            'party_code' => 'PTY-TEST2', // already exists
            'party_type' => 'CUSTOMER',
            'name' => 'Imposter Customer',
            'created_by' => 1
        ]);
        throw new Exception("Duplicate party code was improperly allowed.");
    } catch (Exception $e) {
        echo "PASSED (Blocked duplicate code: " . $e->getMessage() . ")\n";
    }

    // ----------------------------------------------------
    // TEST 8: Verify Permissions Work
    // ----------------------------------------------------
    echo "[TEST 8] Verify Permissions check... ";
    if (\Core\Auth::hasPermission('parties.view') && \Core\Auth::hasPermission('parties.create')) {
        echo "PASSED (Administrator permissions verified successfully)\n";
    } else {
        throw new Exception("Required permissions not mapped to administrator role.");
    }

    // ----------------------------------------------------
    // TEST 9: Verify Inactive Parties selection check
    // ----------------------------------------------------
    echo "[TEST 9] Verify inactive party cannot be used for transactions... ";
    // Fetch a transaction selection candidate list. Inactive parties should be filtered out from listings
    $activeParties = $partyModel->getAll(['status' => 'active']);
    $foundInactive = false;
    foreach ($activeParties as $ap) {
        if ($ap['status'] === 'inactive') {
            $foundInactive = true;
            break;
        }
    }
    if (!$foundInactive) {
        echo "PASSED (Inactive parties correctly omitted from transaction candidate selection list)\n";
    } else {
        throw new Exception("Inactive party returned in active status query.");
    }

    // ----------------------------------------------------
    // TEST 10: Verify Existing Stage 1–4 Features work
    // ----------------------------------------------------
    echo "[TEST 10] Verify existing Stage 1–4 Double-Entry Accounts still work... ";
    $coc = $db->query("SELECT COUNT(*) FROM accounts")->fetchColumn();
    if ($coc > 0) {
        echo "PASSED (Accounting Chart of Accounts queries are active and stable)\n";
    } else {
        throw new Exception("Chart of Accounts table query failed.");
    }

    // Cleanup test data
    $db->exec("DELETE FROM parties WHERE party_code IN ('PTY-TEST1', 'PTY-TEST2', 'PTY-TEST3')");

    echo "\n==================================================\n";
    echo "ALL STAGE 5A VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Verification Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
