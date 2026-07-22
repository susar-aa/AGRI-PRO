-- AGRI CO-OP ERP SEED DATA
-- Version: 1.0 (Stage 1 Initial Data)

USE `agri_erp`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. COMPANY SETTINGS SEEDERS
TRUNCATE TABLE `company_settings`;
INSERT INTO `company_settings` (`setting_key`, `setting_value`, `setting_group`, `description`) VALUES
('company_name_si', 'සීමා සහිත ඇග්රි කෝප් සමූපකාර සමිතිය', 'general', 'Company Name in Sinhala'),
('company_name_en', 'Agri Co-Op Cooperative Society Limited', 'general', 'Company Name in English'),
('address_si', 'මීදූම, යටගම, රඹුක්කන', 'general', 'Company Address in Sinhala'),
('address_en', 'Miduma, Yatagama, Rambukkana', 'general', 'Company Address in English'),
('reg_no_si', 'කෑ/1027', 'general', 'Registration Number in Sinhala'),
('reg_no_en', 'KE/1027', 'general', 'Registration Number in English'),
('reg_date', '2025.11.14', 'general', 'Official Registration Date'),
('contact_numbers', '075 377 0 145, 070 629 61 50, 071 82 110 10, 071 846 0 172, 071 80 28 774', 'general', 'Contact Phone Numbers'),
('currency_symbol', 'LKR', 'general', 'System Currency Code'),
('fiscal_year_start', '01-01', 'accounting', 'Start Date of Fiscal Year');

-- 2. SYSTEM SETTINGS
TRUNCATE TABLE `system_settings`;
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_group`, `description`) VALUES
('app_title', 'Agri Co-Op ERP', 'system', 'ERP Application Title'),
('timezone', 'Asia/Colombo', 'system', 'System Timezone'),
('date_format', 'Y-m-d', 'system', 'Default Display Date Format'),
('journal_prefix', 'JV-', 'accounting', 'Journal Voucher Prefix'),
('invoice_prefix', 'INV-', 'sales', 'Invoice Number Prefix');

-- 3. COST CENTERS SEEDERS
TRUNCATE TABLE `cost_centers`;
INSERT INTO `cost_centers` (`id`, `code`, `name`, `description`, `is_active`) VALUES
(1, 'CC-001', 'Agricultural Services', 'Plowing & Agricultural Field Services', 1),
(2, 'CC-002', 'Machinery Rental', 'Rental of Washers, Generators, Grills & Equipment', 1),
(3, 'CC-003', 'Marketplace', 'Buying & Selling of Agricultural & Trading Products', 1),
(4, 'CC-004', 'Plantation', 'Crop Growing & Agricultural Production Projects', 1),
(5, 'CC-005', 'Brick Manufacturing', 'Brick Production & Manufacturing Unit', 1),
(6, 'CC-006', 'Fruit Packing', 'Fruit Procurement, Packing & Processing', 1),
(7, 'CC-007', 'Construction', 'Construction Contracts & Engineering Projects', 1),
(8, 'CC-008', 'Grinding Mill', 'Grinding Service & Packaged Product Production', 1),
(9, 'CC-009', 'Administration', 'General Administration, Overhead & Management', 1);

-- 4. ROLES SEEDERS
TRUNCATE TABLE `roles`;
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `is_system`) VALUES
(1, 'super_admin', 'Super Admin', 'Full unrestricted system administrative access', 1),
(2, 'admin', 'Administrator', 'General administration and user management access', 1),
(3, 'accountant', 'Accountant', 'Full financial accounting, journals, and reports access', 1),
(4, 'manager', 'Manager', 'Operational management and review access', 0),
(5, 'cashier', 'Cashier', 'POS, customer payments, and cash management access', 0),
(6, 'inventory_officer', 'Inventory Officer', 'Stock management, GRN, and inventory access', 0),
(7, 'sales_officer', 'Sales Officer', 'Sales orders, customer management, and billing access', 0),
(8, 'production_officer', 'Production Officer', 'Plantation, manufacturing, and processing batch access', 0);

-- 5. PERMISSIONS SEEDERS
TRUNCATE TABLE `permissions`;
INSERT INTO `permissions` (`id`, `code`, `name`, `module`, `description`) VALUES
(1, 'dashboard.view', 'View Dashboard', 'dashboard', 'Access dashboard overview'),
(2, 'coa.view', 'View Chart of Accounts', 'accounting', 'View chart of accounts'),
(3, 'coa.manage', 'Manage Chart of Accounts', 'accounting', 'Add/Edit accounts'),
(4, 'journal.view', 'View Journal Entries', 'accounting', 'View journal vouchers'),
(5, 'journal.create', 'Create Journal Entries', 'accounting', 'Create new double-entry journal'),
(6, 'ledger.view', 'View General Ledger', 'accounting', 'View ledger accounts'),
(7, 'trial_balance.view', 'View Trial Balance', 'accounting', 'View trial balance report'),
(8, 'sales.view', 'View Sales & Marketplace', 'sales', 'View sales orders and marketplace'),
(9, 'purchases.view', 'View Purchasing & GRN', 'purchasing', 'View purchases and GRN'),
(10, 'inventory.view', 'View Inventory', 'inventory', 'View stock balances and ledger'),
(11, 'services.view', 'View Services & Rentals', 'services', 'View plowing and machinery rentals'),
(12, 'production.view', 'View Production & Plantation', 'production', 'View crops, bricks, packing & mill'),
(13, 'projects.view', 'View Construction Projects', 'projects', 'View construction contracts'),
(14, 'finance.view', 'View Cash & Bank', 'finance', 'View cash and bank balances'),
(15, 'reports.view', 'View Reports', 'reports', 'View financial and business reports'),
(16, 'users.manage', 'Manage Users & Roles', 'admin', 'Manage user accounts and permissions'),
(17, 'settings.manage', 'Manage Settings', 'admin', 'Manage system & company settings'),
(18, 'audit.view', 'View Audit Logs', 'admin', 'View system audit trails');

-- Assign all permissions to Super Admin & Administrator
TRUNCATE TABLE `role_permissions`;
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM `permissions`;

INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 2, id FROM `permissions`;

-- Assign accounting permissions to Accountant
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5), (3, 6), (3, 7), (3, 14), (3, 15);

-- 6. USERS SEEDERS (Default Password: admin123)
TRUNCATE TABLE `users`;
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `phone`, `status`) VALUES
(1, 'admin', 'admin@agricoop.lk', '$2y$10$gpzCP3usrQ64qeKwUgaT/.uMweoI1nyZklDzhdf7szHLy6hfK4hm6', 'Super Administrator', '0753770145', 'active'),
(2, 'accountant', 'accountant@agricoop.lk', '$2y$10$gpzCP3usrQ64qeKwUgaT/.uMweoI1nyZklDzhdf7szHLy6hfK4hm6', 'Senior Accountant', '0706296150', 'active');

-- Assign Super Admin role to user #1
TRUNCATE TABLE `user_roles`;
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 3);

-- 7. ACCOUNT TYPES SEEDERS
TRUNCATE TABLE `account_types`;
INSERT INTO `account_types` (`id`, `code`, `name`, `category`, `normal_balance`) VALUES
(1, 'ASSET', 'Assets', 'Asset', 'debit'),
(2, 'LIAB', 'Liabilities', 'Liability', 'credit'),
(3, 'EQUITY', 'Equity', 'Equity', 'credit'),
(4, 'REV', 'Revenue', 'Revenue', 'credit'),
(5, 'COGS', 'Cost of Goods Sold', 'COGS', 'debit'),
(6, 'EXP', 'Expenses', 'Expense', 'debit');

-- 8. CHART OF ACCOUNTS SEEDERS
TRUNCATE TABLE `accounts`;

-- Level 1: Header Accounts
INSERT INTO `accounts` (`id`, `account_code`, `account_name`, `parent_id`, `account_type_id`, `category`, `normal_balance`, `is_system`, `is_active`, `allow_manual_posting`, `description`) VALUES
(1, '1000', 'Assets', NULL, 1, 'Asset', 'debit', 1, 1, 0, 'Header account for all Assets'),
(2, '2000', 'Liabilities', NULL, 2, 'Liability', 'credit', 1, 1, 0, 'Header account for all Liabilities'),
(3, '3000', 'Equity', NULL, 3, 'Equity', 'credit', 1, 1, 0, 'Header account for Equity'),
(4, '4000', 'Revenue', NULL, 4, 'Revenue', 'credit', 1, 1, 0, 'Header account for Revenue'),
(5, '5000', 'Cost of Goods Sold', NULL, 5, 'COGS', 'debit', 1, 1, 0, 'Header account for Cost of Goods Sold'),
(6, '6000', 'Expenses', NULL, 6, 'Expense', 'debit', 1, 1, 0, 'Header account for Operating Expenses');

-- Level 2: Assets Sub-headers
INSERT INTO `accounts` (`id`, `account_code`, `account_name`, `parent_id`, `account_type_id`, `category`, `normal_balance`, `is_system`, `is_active`, `allow_manual_posting`, `description`) VALUES
(7, '1100', 'Current Assets', 1, 1, 'Asset', 'debit', 1, 1, 0, 'Current liquid assets'),
(8, '1200', 'Non-Current Assets (PPE)', 1, 1, 'Asset', 'debit', 1, 1, 0, 'Property, Plant & Equipment');

-- Level 3: Current Assets Posting Accounts
INSERT INTO `accounts` (`id`, `account_code`, `account_name`, `parent_id`, `account_type_id`, `category`, `normal_balance`, `is_system`, `is_active`, `allow_manual_posting`, `description`) VALUES
(9, '1110', 'Cash in Hand', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Main Cash Account'),
(10, '1120', 'Bank Accounts', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Bank Operating Accounts'),
(11, '1130', 'Petty Cash', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Petty Cash Account'),
(12, '1140', 'Accounts Receivable', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Customer Receivables Ledger'),
(13, '1150', 'Inventory - Marketplace', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Marketplace Trading Inventory'),
(14, '1160', 'Inventory - Raw Materials', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Production Raw Materials Inventory'),
(15, '1170', 'Inventory - Finished Goods', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Manufactured Finished Goods Inventory');

-- Level 3: Non-Current Assets Posting Accounts
INSERT INTO `accounts` (`id`, `account_code`, `account_name`, `parent_id`, `account_type_id`, `category`, `normal_balance`, `is_system`, `is_active`, `allow_manual_posting`, `description`) VALUES
(16, '1210', 'Machinery & Equipment', 8, 1, 'Asset', 'debit', 1, 1, 1, 'Plowing, Washing & Production Machinery'),
(17, '1220', 'Land & Buildings', 8, 1, 'Asset', 'debit', 1, 1, 1, 'Society Land & Real Estate Assets'),
(18, '1230', 'Vehicles & Transport', 8, 1, 'Asset', 'debit', 1, 1, 1, 'Transport & Operational Vehicles');

-- Level 2 & 3: Liabilities
INSERT INTO `accounts` (`id`, `account_code`, `account_name`, `parent_id`, `account_type_id`, `category`, `normal_balance`, `is_system`, `is_active`, `allow_manual_posting`, `description`) VALUES
(19, '2100', 'Current Liabilities', 2, 2, 'Liability', 'credit', 1, 1, 0, 'Short term obligations'),
(20, '2110', 'Accounts Payable', 19, 2, 'Liability', 'credit', 1, 1, 1, 'Supplier Payables Ledger'),
(21, '2120', 'Accrued Expenses', 19, 2, 'Liability', 'credit', 1, 1, 1, 'Accrued Operational Liabilities'),
(22, '2130', 'Customer Advance Payments', 19, 2, 'Liability', 'credit', 1, 1, 1, 'Advances received from customers'),
(23, '2200', 'Non-Current Liabilities', 2, 2, 'Liability', 'credit', 1, 1, 0, 'Long term debt'),
(24, '2210', 'Long Term Loans', 23, 2, 'Liability', 'credit', 1, 1, 1, 'Bank & Financial Loans');

-- Level 2 & 3: Equity
INSERT INTO `accounts` (`id`, `account_code`, `account_name`, `parent_id`, `account_type_id`, `category`, `normal_balance`, `is_system`, `is_active`, `allow_manual_posting`, `description`) VALUES
(25, '3100', 'Member Share Capital', 3, 3, 'Equity', 'credit', 1, 1, 1, 'Cooperative Member Contributions'),
(26, '3200', 'Retained Earnings', 3, 3, 'Equity', 'credit', 1, 1, 1, 'Accumulated Surplus/Profit'),
(27, '3300', 'General Reserves', 3, 3, 'Equity', 'credit', 1, 1, 1, 'Statutory Statutory Reserves');

-- Level 2 & 3: Revenue Accounts for 8 Business Activities
INSERT INTO `accounts` (`id`, `account_code`, `account_name`, `parent_id`, `account_type_id`, `category`, `normal_balance`, `is_system`, `is_active`, `allow_manual_posting`, `description`) VALUES
(28, '4100', 'Agricultural Services Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Field Plowing & Ag Service Revenue'),
(29, '4200', 'Machinery Rental Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Pressure Washers, Generators & Rental Revenue'),
(30, '4300', 'Marketplace Sales Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Fertilizer, Oil & Trading Product Sales'),
(31, '4400', 'Plantation Sales Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Tomato, Chili & Harvest Crop Sales'),
(32, '4500', 'Brick Sales Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Manufactured Brick Sales'),
(33, '4600', 'Fruit Packing Sales Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Packed & Processed Fruit Sales'),
(34, '4700', 'Construction Contract Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Road & Construction Project Billings'),
(35, '4800', 'Grinding Service Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Custom Customer Grinding Service Income'),
(36, '4900', 'Grinding Product Sales Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Own Packaged Ground Product Sales'),
(37, '4990', 'Other Income', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Miscellaneous Income');

-- Level 2 & 3: COGS Accounts
INSERT INTO `accounts` (`id`, `account_code`, `account_name`, `parent_id`, `account_type_id`, `category`, `normal_balance`, `is_system`, `is_active`, `allow_manual_posting`, `description`) VALUES
(38, '5100', 'COGS - Marketplace Products', 5, 5, 'COGS', 'debit', 1, 1, 1, 'Cost of Goods Sold for Marketplace'),
(39, '5200', 'COGS - Plantation Harvest', 5, 5, 'COGS', 'debit', 1, 1, 1, 'Cost of Harvested & Transferred Crops'),
(40, '5300', 'COGS - Brick Manufacturing', 5, 5, 'COGS', 'debit', 1, 1, 1, 'Cost of Goods Sold for Bricks'),
(41, '5400', 'COGS - Fruit Packing', 5, 5, 'COGS', 'debit', 1, 1, 1, 'Cost of Goods Sold for Packed Fruits'),
(42, '5500', 'COGS - Grinding Products', 5, 5, 'COGS', 'debit', 1, 1, 1, 'Cost of Goods Sold for Grinding Line'),
(43, '5600', 'Direct Construction Costs', 5, 5, 'COGS', 'debit', 1, 1, 1, 'Direct Subcontracting & Contract Materials');

-- Level 2 & 3: Operating Expense Accounts
INSERT INTO `accounts` (`id`, `account_code`, `account_name`, `parent_id`, `account_type_id`, `category`, `normal_balance`, `is_system`, `is_active`, `allow_manual_posting`, `description`) VALUES
(44, '6100', 'Fuel Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Fuel costs for machinery, tractors & transport'),
(45, '6200', 'Labour Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Direct labor & operational wages'),
(46, '6300', 'Employee Hire Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Temporary employee & hire charges'),
(47, '6400', 'Meals Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Field worker & operational meals'),
(48, '6500', 'Transport Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Freight, cartage & field transport'),
(49, '6600', 'Machinery Maintenance', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Equipment servicing & routine maintenance'),
(50, '6700', 'Electricity Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Utility electricity costs'),
(51, '6800', 'Water Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Utility water & irrigation charges'),
(52, '6900', 'Packaging Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Bags, bottles & packaging materials'),
(53, '6910', 'Seeds & Seedlings Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Seeds, plants & nursery stock'),
(54, '6920', 'Fertilizer Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Chemical & organic fertilizer inputs'),
(55, '6930', 'Pesticides & Agrochemicals', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Pest control & crop protection'),
(56, '6940', 'Agricultural Inputs Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'General field & farm input materials'),
(57, '6950', 'Construction Materials', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Cement, sand, gravel & building materials'),
(58, '6960', 'Raw Materials Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Brick clay, whole spices & fruit raw inputs'),
(59, '6970', 'Repairs & Overhauls', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Asset repair & major maintenance'),
(60, '6980', 'Administrative Expenses', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Office supplies & management expense'),
(61, '6990', 'Other Operating Expenses', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Sundry operational expenses');

-- 9. CASH & BANK ACCOUNTS SEEDERS
TRUNCATE TABLE `cash_accounts`;
INSERT INTO `cash_accounts` (`id`, `account_id`, `code`, `name`, `current_balance`, `status`) VALUES
(1, 9, 'CASH-MAIN', 'Main Office Cash Drawer', 0.00, 'active'),
(2, 11, 'CASH-PETTY', 'Petty Cash Fund', 0.00, 'active');

TRUNCATE TABLE `bank_accounts`;
INSERT INTO `bank_accounts` (`id`, `account_id`, `bank_name`, `branch`, `account_number`, `account_name`, `swift_code`, `current_balance`, `status`) VALUES
(1, 10, 'Bank of Ceylon', 'Rambukkana Branch', '875412001', 'Agri Co-Op Cooperative Society', 'BCEYLKLX', 0.00, 'active'),
(2, 10, 'People\'s Bank', 'Rambukkana Branch', '0481001584', 'Agri Co-Op Cooperative Society', 'PSBLKLX', 0.00, 'active');

SET FOREIGN_KEY_CHECKS = 1;
