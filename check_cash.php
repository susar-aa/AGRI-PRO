<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$stmt = $db->query('SELECT * FROM cash_accounts');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
