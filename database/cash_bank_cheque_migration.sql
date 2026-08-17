-- Cash, Bank, Cheque System Enhancements

CREATE TABLE IF NOT EXISTS `bank_reconciliations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `bank_account_id` INT UNSIGNED NOT NULL,
    `statement_date` DATE NOT NULL,
    `ending_balance` DECIMAL(15,2) NOT NULL,
    `book_balance` DECIMAL(15,2) NOT NULL,
    `difference` DECIMAL(15,2) NOT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add reconciled column to journal_lines if it doesn't exist
SET @dbname = DATABASE();
SET @tablename = 'journal_lines';
SET @columnname = 'reconciled';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE table_name = @tablename
     AND table_schema = @dbname
     AND column_name = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE `journal_lines` ADD COLUMN `reconciled` TINYINT(1) NOT NULL DEFAULT 0'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
