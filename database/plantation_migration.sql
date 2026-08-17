-- Database Migration for Plantation Operation Stage 1

CREATE TABLE IF NOT EXISTS `plantation_projects` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_name` VARCHAR(150) NOT NULL,
    `location` VARCHAR(150) NOT NULL,
    `start_date` DATE NOT NULL,
    `expected_harvest_date` DATE DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `status` ENUM('ACTIVE', 'COMPLETED', 'CANCELLED') NOT NULL DEFAULT 'ACTIVE',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `plantation_project_crops` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `planned_quantity` DECIMAL(12, 4) NOT NULL DEFAULT 0.0000,
    `unit` VARCHAR(50) NOT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_ppc_project` FOREIGN KEY (`project_id`) REFERENCES `plantation_projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ppc_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
