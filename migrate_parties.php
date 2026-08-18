<?php
require 'core/Database.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=agri_erp;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Members
    $members = $db->query("SELECT id, full_name, phone, address, city, nic FROM members WHERE party_id IS NULL")->fetchAll(PDO::FETCH_ASSOC);
    foreach($members as $m) {
        $stmt = $db->prepare("INSERT INTO parties (party_code, party_type, name, phone, address, city, nic_reg_no, created_by) VALUES (:code, :type, :name, :phone, :address, :city, :nic, 1)");
        $stmt->execute([
            'code' => 'MEM-' . str_pad($m['id'], 4, '0', STR_PAD_LEFT),
            'type' => 'MEMBER',
            'name' => $m['full_name'],
            'phone' => $m['phone'] ?? '',
            'address' => $m['address'] ?? '',
            'city' => $m['city'] ?? '',
            'nic' => $m['nic'] ?? ''
        ]);
        $partyId = $db->lastInsertId();
        $db->prepare("UPDATE members SET party_id = ? WHERE id = ?")->execute([$partyId, $m['id']]);
    }

    // Directors
    $directors = $db->query("SELECT id, full_name, phone, address, city, nic FROM directors WHERE party_id IS NULL")->fetchAll(PDO::FETCH_ASSOC);
    foreach($directors as $d) {
        $stmt = $db->prepare("INSERT INTO parties (party_code, party_type, name, phone, address, city, nic_reg_no, created_by) VALUES (:code, :type, :name, :phone, :address, :city, :nic, 1)");
        $stmt->execute([
            'code' => 'DIR-' . str_pad($d['id'], 4, '0', STR_PAD_LEFT),
            'type' => 'DIRECTOR',
            'name' => $d['full_name'],
            'phone' => $d['phone'] ?? '',
            'address' => $d['address'] ?? '',
            'city' => $d['city'] ?? '',
            'nic' => $d['nic'] ?? ''
        ]);
        $partyId = $db->lastInsertId();
        $db->prepare("UPDATE directors SET party_id = ? WHERE id = ?")->execute([$partyId, $d['id']]);
    }
    echo "Done migrating existing members/directors.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
