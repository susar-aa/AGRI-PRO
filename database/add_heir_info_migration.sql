USE `agri_erp`;

-- Add Heir Information to the members table
ALTER TABLE `members`
ADD COLUMN `heir_name` VARCHAR(255) DEFAULT NULL AFTER `notes`,
ADD COLUMN `heir_address` TEXT DEFAULT NULL AFTER `heir_name`,
ADD COLUMN `heir_nic` VARCHAR(100) DEFAULT NULL AFTER `heir_address`,
ADD COLUMN `heir_contact_number` VARCHAR(50) DEFAULT NULL AFTER `heir_nic`;

-- Remove email column as requested
ALTER TABLE `members` DROP COLUMN `email`;
