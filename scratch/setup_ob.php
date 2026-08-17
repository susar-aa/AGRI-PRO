<?php
require_once __DIR__ . '/../core/bootstrap.php';
$db = \Core\Database::getInstance();

try {
    $db->exec("
    CREATE TABLE IF NOT EXISTS `party_opening_balances` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `party_id` int(11) NOT NULL,
      `type` enum('receivable','payable') NOT NULL,
      `amount` decimal(15,2) NOT NULL,
      `balance_date` date NOT NULL,
      `description` text DEFAULT NULL,
      `status` enum('draft','posted','reversed') DEFAULT 'draft',
      `journal_entry_id` int(11) DEFAULT NULL,
      `reversal_journal_entry_id` int(11) DEFAULT NULL,
      `reversal_reason` text DEFAULT NULL,
      `created_by` int(11) DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Created party_opening_balances.\n";
} catch (Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}
