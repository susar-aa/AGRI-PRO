<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$stmt = $db->query("SELECT * FROM permissions WHERE name LIKE '%invoice%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
