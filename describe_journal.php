<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$stmt = $db->query('DESCRIBE journal_entries');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
