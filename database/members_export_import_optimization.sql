-- =========================================================================
-- AGRI PRO: Society Members Export & Import Performance Indexes Migration
-- Target Tables: coop_members, parties
-- Tested on local XAMPP MySQL. Run on live database during deployment.
-- =========================================================================

-- 1. Accelerate duplicate NIC checks and imports for coop_members (member_type, nic)
ALTER TABLE `coop_members` 
    ADD INDEX `idx_cm_type_nic` (`member_type`, `nic`);

-- 2. Accelerate member listing, sorting, and directory searches (member_type, full_name)
ALTER TABLE `coop_members` 
    ADD INDEX `idx_cm_type_name` (`member_type`, `full_name`);

-- 3. Accelerate phone lookups during search and deduplication
ALTER TABLE `coop_members` 
    ADD INDEX `idx_cm_phone` (`phone`);

-- 4. Accelerate linked party lookups by NIC on parties table
ALTER TABLE `parties` 
    ADD INDEX `idx_parties_nic` (`nic_reg_no`);
