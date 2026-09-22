<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
print_r($db->query('DESCRIBE cash_accounts')->fetchAll(PDO::FETCH_ASSOC));
