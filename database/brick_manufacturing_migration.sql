-- BRICK MANUFACTURING OPERATIONS DATABASE MIGRATION

CREATE TABLE IF NOT EXISTS `brick_production_projects` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_name` VARCHAR(150) NOT NULL,
    `location` VARCHAR(150) NOT NULL,
    `start_date` DATE NOT NULL,
    `expected_completion_date` DATE DEFAULT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `planned_quantity` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `unit` VARCHAR(50) NOT NULL DEFAULT 'Pieces',
    `status` ENUM('ACTIVE', 'COMPLETED', 'CANCELLED') NOT NULL DEFAULT 'ACTIVE',
    `notes` TEXT DEFAULT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_bpp_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `brick_production_records` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT UNSIGNED NOT NULL,
    `production_date` DATE NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `quantity` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `unit` VARCHAR(50) NOT NULL DEFAULT 'Pieces',
    `notes` TEXT DEFAULT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_bpr_project` FOREIGN KEY (`project_id`) REFERENCES `brick_production_projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bpr_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `brick_transfers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT UNSIGNED NOT NULL,
    `production_record_id` INT UNSIGNED NOT NULL,
    `transfer_date` DATE NOT NULL,
    `quantity` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `cost_price_per_unit` DECIMAL(12, 4) NOT NULL DEFAULT 0.0000,
    `selling_price_per_unit` DECIMAL(12, 4) NOT NULL DEFAULT 0.0000,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_bt_project` FOREIGN KEY (`project_id`) REFERENCES `brick_production_projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bt_record` FOREIGN KEY (`production_record_id`) REFERENCES `brick_production_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
