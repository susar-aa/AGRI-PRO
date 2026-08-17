<?php
require_once __DIR__ . '/../core/bootstrap.php';
$db = \Core\Database::getInstance();
try {
    $db->exec("ALTER TABLE `expenses` ADD COLUMN `reference_number` VARCHAR(100) DEFAULT NULL AFTER `expense_number`;");
    echo "Added reference_number to expenses.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
