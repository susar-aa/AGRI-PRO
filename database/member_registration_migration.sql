-- Society Member Registration & Fixed Deposits Schema Updates

CREATE TABLE IF NOT EXISTS `members` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `membership_no` VARCHAR(50) NOT NULL UNIQUE,
    `party_id` INT UNSIGNED NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `nic` VARCHAR(50) NOT NULL UNIQUE,
    `dob` DATE NOT NULL,
    `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `address` TEXT NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `registration_date` DATE NOT NULL,
    `membership_type` VARCHAR(100) NOT NULL DEFAULT 'Ordinary',
    `status` ENUM('ACTIVE', 'INACTIVE', 'SUSPENDED', 'RESIGNED') NOT NULL DEFAULT 'ACTIVE',
    `registration_fee` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `payment_method` ENUM('Unpaid', 'Cash', 'Bank Transfer', 'Cheque') NOT NULL DEFAULT 'Unpaid',
    `payment_status` ENUM('UNPAID', 'PAID') NOT NULL DEFAULT 'UNPAID',
    `notes` TEXT DEFAULT NULL,
    `journal_entry_id` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`party_id`) REFERENCES `parties` (`id`) ON DELETE SET NULL,
    FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `member_fixed_deposits` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `deposit_number` VARCHAR(50) NOT NULL UNIQUE,
    `member_id` INT UNSIGNED NOT NULL,
    `deposit_date` DATE NOT NULL,
    `deposit_amount` DECIMAL(15,2) NOT NULL,
    `interest_rate` DECIMAL(5,2) NOT NULL,
    `maturity_date` DATE NOT NULL,
    `term_months` INT UNSIGNED NOT NULL,
    `status` ENUM('ACTIVE', 'MATURED', 'CLOSED') NOT NULL DEFAULT 'ACTIVE',
    FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Check and insert Membership Registration Income account (Revenue Category, Code: 4250)
INSERT IGNORE INTO accounts (account_code, account_name, parent_id, account_type_id, category, normal_balance, is_system, is_active, allow_manual_posting, description)
VALUES ('4250', 'Membership Registration Revenue', 28, 4, 'Revenue', 'credit', 1, 1, 1, 'Cooperative Society Membership Registration Fee Income');
