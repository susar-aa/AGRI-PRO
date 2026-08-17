-- Missing base tables from initial schema
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `product_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `units_of_measure` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `parties` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `party_code` VARCHAR(50) NOT NULL UNIQUE,
    `party_type` VARCHAR(50) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `contact_person` VARCHAR(255) DEFAULT NULL,
    `nic_reg_no` VARCHAR(100) DEFAULT NULL,
    `phone` VARCHAR(50) DEFAULT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `district` VARCHAR(100) DEFAULT NULL,
    `credit_limit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `credit_days` INT NOT NULL DEFAULT 0,
    `payment_terms` TEXT DEFAULT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'active',
    `notes` TEXT DEFAULT NULL,
    `customer_type` VARCHAR(50) DEFAULT NULL,
    `supplier_type` VARCHAR(50) DEFAULT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_party_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `sku` VARCHAR(100) DEFAULT NULL,
    `product_code` VARCHAR(100) NOT NULL UNIQUE,
    `name_en` VARCHAR(255) NOT NULL,
    `category_id` INT UNSIGNED DEFAULT NULL,
    `product_type` VARCHAR(50) DEFAULT 'TRADING',
    `base_unit_id` INT UNSIGNED DEFAULT NULL,
    `purchase_unit_id` INT UNSIGNED DEFAULT NULL,
    `sales_unit_id` INT UNSIGNED DEFAULT NULL,
    `default_purchase_price` DECIMAL(15,4) NOT NULL DEFAULT 0.0000,
    `default_selling_price` DECIMAL(15,4) NOT NULL DEFAULT 0.0000,
    `inventory_account_id` INT UNSIGNED DEFAULT NULL,
    `cogs_account_id` INT UNSIGNED DEFAULT NULL,
    `sales_revenue_account_id` INT UNSIGNED DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `is_marketplace` TINYINT(1) NOT NULL DEFAULT 1,
    `source_module` VARCHAR(50) DEFAULT 'PURCHASE',
    `source_transaction_id` INT UNSIGNED DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_prod_base_unit` FOREIGN KEY (`base_unit_id`) REFERENCES `units_of_measure` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_prod_inv_acc` FOREIGN KEY (`inventory_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_prod_cogs_acc` FOREIGN KEY (`cogs_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_prod_sales_acc` FOREIGN KEY (`sales_revenue_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_prod_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
