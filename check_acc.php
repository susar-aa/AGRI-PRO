<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$stmt = $db->query('SELECT id, account_code, account_name, category FROM accounts WHERE id IN (25, 37)');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
