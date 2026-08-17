<?php
require_once __DIR__ . '/../core/bootstrap.php';
$db = \Core\Database::getInstance();
try {
    $db->query("SELECT 1 FROM expense_categories LIMIT 1");
    echo "expense_categories exists.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
try {
    $db->query("SELECT 1 FROM expenses LIMIT 1");
    echo "expenses exists.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
