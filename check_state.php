<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
print_r($db->query('SELECT * FROM cash_accounts')->fetchAll(PDO::FETCH_ASSOC));
print_r($db->query('SELECT id, invoice_number, status, payment_type FROM invoices')->fetchAll(PDO::FETCH_ASSOC));
print_r($db->query('SELECT * FROM journal_entries')->fetchAll(PDO::FETCH_ASSOC));
