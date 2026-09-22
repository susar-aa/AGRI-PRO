<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$stmt = $db->query('SHOW TABLES');
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
