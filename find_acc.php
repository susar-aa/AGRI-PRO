<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$stmt = $db->query("SELECT id, account_code, account_name, category FROM accounts WHERE category IN ('Equity', 'Liability')");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
