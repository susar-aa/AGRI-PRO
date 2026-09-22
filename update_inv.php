<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$db->exec("UPDATE invoices SET invoice_number = 'INV - 001' WHERE invoice_number = 'INV-2026-000001'");
echo 'Success';
