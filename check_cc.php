<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$stmt = $db->query('SELECT * FROM cost_centers');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
