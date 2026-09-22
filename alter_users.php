<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$db->exec("ALTER TABLE users ADD COLUMN party_id INT(10) UNSIGNED NULL AFTER phone");
echo 'Success';
