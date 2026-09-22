<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$stmt = $db->query("SELECT * FROM accounts WHERE account_code = '3110'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
