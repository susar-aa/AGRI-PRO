<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$stmt = $db->query('DESCRIBE inventory_transactions');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
