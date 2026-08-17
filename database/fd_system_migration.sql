-- Complete Fixed Deposit (FD) System Schema

-- Drop old member_fixed_deposits if any
DROP TABLE IF EXISTS `member_fixed_deposits`;

-- Create fresh member_fixed_deposits table
CREATE TABLE `member_fixed_deposits` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `deposit_number` VARCHAR(50) NOT NULL UNIQUE,
    `member_id` INT UNSIGNED NOT NULL,
    `deposit_date` DATE NOT NULL,
    `start_date` DATE NOT NULL,
    `term_months` INT UNSIGNED NOT NULL,
    `interest_rate` DECIMAL(5,2) NOT NULL,
    `expected_interest` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `maturity_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `maturity_date` DATE NOT NULL,
    `payment_method` ENUM('Cash', 'Bank Transfer', 'Cheque') NOT NULL DEFAULT 'Cash',
    `status` ENUM('ACTIVE', 'MATURED', 'CLOSED', 'PREMATURELY_CLOSED', 'CANCELLED') NOT NULL DEFAULT 'ACTIVE',
    `notes` TEXT DEFAULT NULL,
    `journal_entry_id` INT UNSIGNED DEFAULT NULL,
    `maturity_journal_entry_id` INT UNSIGNED DEFAULT NULL,
    `closure_date` DATE DEFAULT NULL,
    `closure_reason` TEXT DEFAULT NULL,
    `interest_adjustment` DECIMAL(15,2) DEFAULT NULL,
    `final_payable_amount` DECIMAL(15,2) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL,
    FOREIGN KEY (`maturity_journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Check and insert Fixed Deposit Asset GL Account (Asset Category, Code: 1150)
INSERT IGNORE INTO accounts (account_code, account_name, parent_id, account_type_id, category, normal_balance, is_system, is_active, allow_manual_posting, description)
VALUES ('1150', 'Fixed Deposits Asset', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Fixed Deposit Investments Held by the Society');
