<?php
require_once __DIR__ . '/../core/bootstrap.php';
$db = \Core\Database::getInstance();
try {
    $db->exec("
    ALTER TABLE `expenses` 
    MODIFY COLUMN `status` enum('draft','pending_approval','approved','posted','cancelled','reversed') DEFAULT 'draft';
    ");
    echo "Fixed status enum in expenses.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
