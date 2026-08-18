<?php
require 'core/Database.php';
try {
    $db = new PDO('mysql:host=localhost;dbname=agri_erp;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Drop unique index on NIC if it exists
    try {
        $db->exec("ALTER TABLE coop_members DROP INDEX nic");
    } catch (Exception $e) {}

    // 5. Insert directors into coop_members
    $directorsTableExists = $db->query("SHOW TABLES LIKE 'directors'")->rowCount() > 0;
    if ($directorsTableExists) {
        $directors = $db->query("SELECT * FROM directors")->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $db->prepare("
            INSERT INTO coop_members 
            (member_type, member_no, party_id, full_name, nic, dob, gender, occupation, phone, address, city, 
             registration_date, status, registration_fee, shares_fee, payment_method, payment_status, notes, 
             heir_name, heir_address, heir_nic, heir_contact_number, journal_entry_id, created_at)
            VALUES 
            ('DIRECTOR', :member_no, :party_id, :full_name, :nic, :dob, :gender, :occupation, :phone, :address, :city, 
             :registration_date, :status, :registration_fee, :shares_fee, :payment_method, :payment_status, :notes, 
             :heir_name, :heir_address, :heir_nic, :heir_contact_number, :journal_entry_id, :created_at)
        ");
        
        foreach ($directors as $d) {
            $stmt->execute([
                'member_no' => $d['director_no'],
                'party_id' => $d['party_id'],
                'full_name' => $d['full_name'],
                'nic' => $d['nic'],
                'dob' => $d['dob'],
                'gender' => $d['gender'],
                'occupation' => $d['occupation'],
                'phone' => $d['phone'],
                'address' => $d['address'],
                'city' => $d['city'],
                'registration_date' => $d['registration_date'],
                'status' => $d['status'],
                'registration_fee' => $d['registration_fee'],
                'shares_fee' => $d['shares_fee'],
                'payment_method' => $d['payment_method'],
                'payment_status' => $d['payment_status'],
                'notes' => $d['notes'],
                'heir_name' => $d['heir_name'],
                'heir_address' => $d['heir_address'],
                'heir_nic' => $d['heir_nic'],
                'heir_contact_number' => $d['heir_contact_number'],
                'journal_entry_id' => $d['journal_entry_id'],
                'created_at' => $d['created_at']
            ]);
        }

        // 6. Drop directors table
        $db->exec("DROP TABLE directors");
    }

    echo "Migration completed successfully.\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
