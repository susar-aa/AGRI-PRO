<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$stmt = $db->query('SELECT id, account_code, account_name FROM accounts WHERE account_name LIKE "%cash%" OR account_code LIKE "%100%"');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
