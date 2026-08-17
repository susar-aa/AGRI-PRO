SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `inventory_locations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `services` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `service_code` VARCHAR(50) NOT NULL UNIQUE,
    `service_name` VARCHAR(150) NOT NULL,
    `description` TEXT,
    `unit` VARCHAR(50) DEFAULT 'Job',
    `default_price` DECIMAL(15,2) DEFAULT 0.00,
    `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `machinery` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `machinery_code` VARCHAR(50) NOT NULL UNIQUE,
    `machinery_name` VARCHAR(150) NOT NULL,
    `serial_number` VARCHAR(100),
    `default_rental_rate` DECIMAL(15,2) DEFAULT 0.00,
    `rental_unit` VARCHAR(50) DEFAULT 'Hour',
    `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `machinery_rentals` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `rental_number` VARCHAR(50) NOT NULL UNIQUE,
    `customer_id` INT UNSIGNED NOT NULL,
    `machinery_id` INT UNSIGNED NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE,
    `total_charge` DECIMAL(15,2) DEFAULT 0.00,
    `status` VARCHAR(50) DEFAULT 'ACTIVE',
    `journal_entry_id` INT UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `invoices` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
    `customer_id` INT UNSIGNED NOT NULL,
    `warehouse_id` INT UNSIGNED DEFAULT NULL,
    `invoice_date` DATE NOT NULL,
    `reference` VARCHAR(100),
    `notes` TEXT,
    `payment_type` VARCHAR(50) DEFAULT 'CASH',
    `discount` DECIMAL(15,2) DEFAULT 0.00,
    `grand_total` DECIMAL(15,2) DEFAULT 0.00,
    `status` VARCHAR(50) DEFAULT 'DRAFT',
    `journal_entry_id` INT UNSIGNED DEFAULT NULL,
    `reversal_journal_entry_id` INT UNSIGNED DEFAULT NULL,
    `cash_account_id` INT UNSIGNED DEFAULT NULL,
    `bank_account_id` INT UNSIGNED DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `invoice_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT UNSIGNED NOT NULL,
    `item_type` VARCHAR(50) DEFAULT 'PRODUCT',
    `product_id` INT UNSIGNED DEFAULT NULL,
    `service_id` INT UNSIGNED DEFAULT NULL,
    `description` VARCHAR(255),
    `quantity` DECIMAL(15,2) DEFAULT 0.00,
    `unit_price` DECIMAL(15,2) DEFAULT 0.00,
    `discount` DECIMAL(15,2) DEFAULT 0.00,
    `line_total` DECIMAL(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payment_receipts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `payment_number` VARCHAR(50) NOT NULL UNIQUE,
    `party_id` INT UNSIGNED NOT NULL,
    `payment_date` DATE NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `payment_method` VARCHAR(50) NOT NULL,
    `reference_number` VARCHAR(100),
    `notes` TEXT,
    `status` VARCHAR(50) DEFAULT 'posted',
    `payment_type` VARCHAR(50) NOT NULL,
    `cash_account_id` INT UNSIGNED DEFAULT NULL,
    `bank_account_id` INT UNSIGNED DEFAULT NULL,
    `journal_entry_id` INT UNSIGNED DEFAULT NULL,
    `reversal_journal_entry_id` INT UNSIGNED DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cheques` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cheque_number` VARCHAR(50) NOT NULL,
    `cheque_type` VARCHAR(50) NOT NULL,
    `party_id` INT UNSIGNED NOT NULL,
    `bank_name` VARCHAR(100) NOT NULL,
    `cheque_date` DATE NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `received_issued_date` DATE NOT NULL,
    `status` VARCHAR(50) DEFAULT 'RECEIVED',
    `created_by` INT UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `service_jobs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `job_number` VARCHAR(50) NOT NULL UNIQUE,
    `customer_id` INT UNSIGNED NOT NULL,
    `status` VARCHAR(50) DEFAULT 'PENDING',
    `invoice_id` INT UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_ledger` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT UNSIGNED NOT NULL,
    `location_id` INT UNSIGNED NOT NULL,
    `transaction_date` DATE NOT NULL,
    `reference` VARCHAR(100),
    `source_module` VARCHAR(50),
    `source_id` INT UNSIGNED,
    `qty_in` DECIMAL(15,4) DEFAULT 0.0000,
    `qty_out` DECIMAL(15,4) DEFAULT 0.0000,
    `unit_cost` DECIMAL(15,4) DEFAULT 0.0000,
    `total_value` DECIMAL(15,4) DEFAULT 0.0000,
    `running_qty` DECIMAL(15,4) DEFAULT 0.0000,
    `running_value` DECIMAL(15,4) DEFAULT 0.0000,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `inventory_balances` (
    `product_id` INT UNSIGNED NOT NULL,
    `location_id` INT UNSIGNED NOT NULL,
    `quantity` DECIMAL(15,4) DEFAULT 0.0000,
    `total_value` DECIMAL(15,4) DEFAULT 0.0000,
    PRIMARY KEY (`product_id`, `location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `inventory_locations` (`code`, `name`, `is_active`) VALUES ('LOC-MAIN', 'Main Store', 1);

-- We also need PTY-WALKIN to exist if it doesn't already
INSERT IGNORE INTO `parties` (`party_code`, `party_type`, `name`, `status`, `credit_limit`, `created_by`) VALUES ('PTY-WALKIN', 'CUSTOMER', 'Walk-in Customer', 'active', 0.00, 1);

SET FOREIGN_KEY_CHECKS = 1;
