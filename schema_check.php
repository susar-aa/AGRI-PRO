<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$stmt = $db->query('DESCRIBE coop_members');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
