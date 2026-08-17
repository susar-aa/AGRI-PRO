<?php
require_once __DIR__ . '/../core/bootstrap.php';
$db = \Core\Database::getInstance();
try {
    $db->exec("
    ALTER TABLE `expenses` 
    ADD COLUMN `approved_at` TIMESTAMP NULL DEFAULT NULL AFTER `approved_by`,
    ADD COLUMN `posted_at` TIMESTAMP NULL DEFAULT NULL AFTER `posted_by`,
    ADD COLUMN `reversed_at` TIMESTAMP NULL DEFAULT NULL AFTER `reversed_by`,
    ADD COLUMN `reversal_reason` TEXT DEFAULT NULL AFTER `reversal_journal_entry_id`;
    ");
    echo "Added approved_at, posted_at, reversed_at, and reversal_reason.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
