-- Yield Marketplace Transfers Migration with Costing columns

CREATE TABLE IF NOT EXISTS `plantation_harvest_transfers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `harvest_id` INT UNSIGNED NOT NULL,
    `transfer_date` DATE NOT NULL,
    `quantity` DECIMAL(12, 4) NOT NULL,
    `cost_price_per_unit` DECIMAL(12, 4) NOT NULL DEFAULT 0.0000,
    `selling_price_per_unit` DECIMAL(12, 4) NOT NULL DEFAULT 0.0000,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_pht_harvest` FOREIGN KEY (`harvest_id`) REFERENCES `plantation_harvests` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pht_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
