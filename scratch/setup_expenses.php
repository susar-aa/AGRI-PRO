<?php
require_once __DIR__ . '/../core/bootstrap.php';
$db = \Core\Database::getInstance();

try {
    $db->beginTransaction();

    // Create expense_categories table
    $db->exec("
    CREATE TABLE IF NOT EXISTS `expense_categories` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `linked_account_id` int(11) DEFAULT NULL,
      `is_active` tinyint(1) DEFAULT 1,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Create expenses table
    $db->exec("
    CREATE TABLE IF NOT EXISTS `expenses` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `expense_number` varchar(50) NOT NULL,
      `expense_date` date NOT NULL,
      `expense_category_id` int(11) NOT NULL,
      `cost_center_id` int(11) DEFAULT NULL,
      `payee` varchar(255) NOT NULL,
      `amount` decimal(15,2) NOT NULL,
      `description` text DEFAULT NULL,
      `notes` text DEFAULT NULL,
      `payment_method` varchar(50) NOT NULL,
      `cash_account_id` int(11) DEFAULT NULL,
      `bank_account_id` int(11) DEFAULT NULL,
      `supplier_id` int(11) DEFAULT NULL,
      `project_id` int(11) DEFAULT NULL,
      `batch_id` int(11) DEFAULT NULL,
      `service_job_id` int(11) DEFAULT NULL,
      `machinery_id` int(11) DEFAULT NULL,
      `machinery_rental_id` int(11) DEFAULT NULL,
      `expense_account_id` int(11) DEFAULT NULL,
      `accounts_payable_account_id` int(11) DEFAULT NULL,
      `status` enum('draft','pending_approval','posted','cancelled','reversed') DEFAULT 'draft',
      `journal_entry_id` int(11) DEFAULT NULL,
      `reversal_journal_entry_id` int(11) DEFAULT NULL,
      `created_by` int(11) DEFAULT NULL,
      `approved_by` int(11) DEFAULT NULL,
      `posted_by` int(11) DEFAULT NULL,
      `reversed_by` int(11) DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Create expense_attachments table
    $db->exec("
    CREATE TABLE IF NOT EXISTS `expense_attachments` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `expense_id` int(11) NOT NULL,
      `file_path` varchar(255) NOT NULL,
      `file_name` varchar(255) NOT NULL,
      `uploaded_by` int(11) DEFAULT NULL,
      `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Insert default expense categories
    // We need a generic expense account from `accounts` table. Let's find one or default to 1.
    $accStmt = $db->query("SELECT id FROM accounts WHERE account_type = 'Expense' LIMIT 1");
    $acc = $accStmt->fetchColumn();
    $accId = $acc ? (int)$acc : 1;

    $categories = [
        'Labour charges',
        'Fertilizers & Chemicals',
        'Seeds & Plants',
        'Machinery & Equipment',
        'Fuel & Transport',
        'Irrigation & Water',
        'Maintenance & Repairs',
        'Miscellaneous'
    ];

    $insertStmt = $db->prepare("INSERT IGNORE INTO expense_categories (name, linked_account_id, is_active) VALUES (?, ?, 1)");
    foreach ($categories as $cat) {
        $insertStmt->execute([$cat, $accId]);
    }

    $db->commit();
    echo "Successfully created expenses tables and inserted default categories.\n";

} catch (Exception $e) {
    $db->rollBack();
    echo "Failed: " . $e->getMessage() . "\n";
}
