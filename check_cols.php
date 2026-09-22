<?php
require 'core/bootstrap.php'; 
$db = Core\Database::getInstance(); 
$stmt = $db->query('DESCRIBE invoices'); 
print_r(array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field'));

$stmt = $db->query('DESCRIBE invoice_items'); 
print_r(array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field'));
