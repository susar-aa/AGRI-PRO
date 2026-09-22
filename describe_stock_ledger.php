<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$stmt = $db->query('DESCRIBE stock_ledger');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
