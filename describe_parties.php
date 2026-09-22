<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$stmt = $db->query('DESCRIBE parties');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
