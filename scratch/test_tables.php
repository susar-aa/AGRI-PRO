<?php
require_once __DIR__ . '/../core/bootstrap.php';
$db = \Core\Database::getInstance();
$tables = ['party_opening_balances', 'payment_receipts', 'invoices'];
foreach ($tables as $t) {
    try {
        $db->query("SELECT 1 FROM $t LIMIT 1");
        echo "$t exists.\n";
    } catch (Exception $e) {
        echo "$t MISSING.\n";
    }
}
