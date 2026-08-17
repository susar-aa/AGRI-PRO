<?php
require_once __DIR__ . '/../core/bootstrap.php';
$db = \Core\Database::getInstance();
try {
    $db->exec("
    ALTER TABLE `expenses` 
    ADD COLUMN `source_module` VARCHAR(50) DEFAULT 'GENERAL' AFTER `machinery_rental_id`,
    ADD COLUMN `source_type` VARCHAR(50) DEFAULT 'GENERAL_EXPENSE' AFTER `source_module`,
    ADD COLUMN `source_transaction_id` INT(11) DEFAULT NULL AFTER `source_type`;
    ");
    echo "Added source_module, source_type, source_transaction_id to expenses.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
