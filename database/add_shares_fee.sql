USE `agri_erp`;
ALTER TABLE `members`
ADD COLUMN `shares_fee` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `registration_fee`;
