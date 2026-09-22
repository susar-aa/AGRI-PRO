<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$db->exec("INSERT INTO cash_accounts (account_id, code, name, status) VALUES (9, 'CASH-MAIN', 'Cash in Hand', 'active')");
echo 'Success';
