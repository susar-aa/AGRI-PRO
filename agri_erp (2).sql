-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 28, 2026 at 01:30 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 8.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `agri_erp`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `account_code` varchar(30) NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `account_type_id` int(10) UNSIGNED NOT NULL,
  `category` enum('Asset','Liability','Equity','Revenue','COGS','Expense') NOT NULL,
  `normal_balance` enum('debit','credit') NOT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `allow_manual_posting` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `account_code`, `account_name`, `parent_id`, `account_type_id`, `category`, `normal_balance`, `is_system`, `is_active`, `allow_manual_posting`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '1000', 'Assets', NULL, 1, 'Asset', 'debit', 1, 1, 0, 'Header account for all Assets', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(2, '2000', 'Liabilities', NULL, 2, 'Liability', 'credit', 1, 1, 0, 'Header account for all Liabilities', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(3, '3000', 'Equity', NULL, 3, 'Equity', 'credit', 1, 1, 0, 'Header account for Equity', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(4, '4000', 'Revenue', NULL, 4, 'Revenue', 'credit', 1, 1, 0, 'Header account for Revenue', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(5, '5000', 'Cost of Goods Sold', NULL, 5, 'COGS', 'debit', 1, 1, 0, 'Header account for Cost of Goods Sold', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(6, '6000', 'Expenses', NULL, 6, 'Expense', 'debit', 1, 1, 0, 'Header account for Operating Expenses', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(7, '1100', 'Current Assets', 1, 1, 'Asset', 'debit', 1, 1, 0, 'Current liquid assets', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(8, '1200', 'Non-Current Assets (PPE)', 1, 1, 'Asset', 'debit', 1, 1, 0, 'Property, Plant & Equipment', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(9, '1110', 'Cash in Hand', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Main Cash Account', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(10, '1120', 'Bank Accounts', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Bank Operating Accounts', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(11, '1130', 'Petty Cash', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Petty Cash Account', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(12, '1140', 'Accounts Receivable', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Customer Receivables Ledger', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(13, '1150', 'Inventory - Marketplace', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Marketplace Trading Inventory', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(14, '1160', 'Inventory - Raw Materials', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Production Raw Materials Inventory', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(15, '1170', 'Inventory - Finished Goods', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Manufactured Finished Goods Inventory', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(16, '1210', 'Machinery & Equipment', 8, 1, 'Asset', 'debit', 1, 1, 1, 'Plowing, Washing & Production Machinery', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(17, '1220', 'Land & Buildings', 8, 1, 'Asset', 'debit', 1, 1, 1, 'Society Land & Real Estate Assets', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(18, '1230', 'Vehicles & Transport', 8, 1, 'Asset', 'debit', 1, 1, 1, 'Transport & Operational Vehicles', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(19, '2100', 'Current Liabilities', 2, 2, 'Liability', 'credit', 1, 1, 0, 'Short term obligations', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(20, '2110', 'Accounts Payable', 19, 2, 'Liability', 'credit', 1, 1, 1, 'Supplier Payables Ledger', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(21, '2120', 'Accrued Expenses', 19, 2, 'Liability', 'credit', 1, 1, 1, 'Accrued Operational Liabilities', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(22, '2130', 'Customer Advance Payments', 19, 2, 'Liability', 'credit', 1, 1, 1, 'Advances received from customers', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(23, '2200', 'Non-Current Liabilities', 2, 2, 'Liability', 'credit', 1, 1, 0, 'Long term debt', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(24, '2210', 'Long Term Loans', 23, 2, 'Liability', 'credit', 1, 1, 1, 'Bank & Financial Loans', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(25, '3100', 'Member Share Capital', 3, 3, 'Equity', 'credit', 1, 1, 1, 'Cooperative Member Contributions', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(26, '3200', 'Retained Earnings', 3, 3, 'Equity', 'credit', 1, 1, 1, 'Accumulated Surplus/Profit', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(27, '3300', 'General Reserves', 3, 3, 'Equity', 'credit', 1, 1, 1, 'Statutory Statutory Reserves', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(28, '4100', 'Agricultural Services Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Field Plowing & Ag Service Revenue', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(29, '4200', 'Machinery Rental Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Pressure Washers, Generators & Rental Revenue', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(30, '4300', 'Marketplace Sales Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Fertilizer, Oil & Trading Product Sales', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(31, '4400', 'Plantation Sales Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Tomato, Chili & Harvest Crop Sales', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(32, '4500', 'Brick Sales Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Manufactured Brick Sales', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(33, '4600', 'Fruit Packing Sales Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Packed & Processed Fruit Sales', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(34, '4700', 'Construction Contract Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Road & Construction Project Billings', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(35, '4800', 'Grinding Service Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Custom Customer Grinding Service Income', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(36, '4900', 'Grinding Product Sales Revenue', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Own Packaged Ground Product Sales', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(37, '4990', 'Other Income', 4, 4, 'Revenue', 'credit', 1, 1, 1, 'Miscellaneous Income', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(38, '5100', 'COGS - Marketplace Products', 5, 5, 'COGS', 'debit', 1, 1, 1, 'Cost of Goods Sold for Marketplace', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(39, '5200', 'COGS - Plantation Harvest', 5, 5, 'COGS', 'debit', 1, 1, 1, 'Cost of Harvested & Transferred Crops', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(40, '5300', 'COGS - Brick Manufacturing', 5, 5, 'COGS', 'debit', 1, 1, 1, 'Cost of Goods Sold for Bricks', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(41, '5400', 'COGS - Fruit Packing', 5, 5, 'COGS', 'debit', 1, 1, 1, 'Cost of Goods Sold for Packed Fruits', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(42, '5500', 'COGS - Grinding Products', 5, 5, 'COGS', 'debit', 1, 1, 1, 'Cost of Goods Sold for Grinding Line', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(43, '5600', 'Direct Construction Costs', 5, 5, 'COGS', 'debit', 1, 1, 1, 'Direct Subcontracting & Contract Materials', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(44, '6100', 'Fuel Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Fuel costs for machinery, tractors & transport', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(45, '6200', 'Labour Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Direct labor & operational wages', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(46, '6300', 'Employee Hire Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Temporary employee & hire charges', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(47, '6400', 'Meals Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Field worker & operational meals', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(48, '6500', 'Transport Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Freight, cartage & field transport', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(49, '6600', 'Machinery Maintenance', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Equipment servicing & routine maintenance', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(50, '6700', 'Electricity Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Utility electricity costs', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(51, '6800', 'Water Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Utility water & irrigation charges', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(52, '6900', 'Packaging Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Bags, bottles & packaging materials', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(53, '6910', 'Seeds & Seedlings Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Seeds, plants & nursery stock', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(54, '6920', 'Fertilizer Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Chemical & organic fertilizer inputs', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(55, '6930', 'Pesticides & Agrochemicals', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Pest control & crop protection', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(56, '6940', 'Agricultural Inputs Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'General field & farm input materials', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(57, '6950', 'Construction Materials', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Cement, sand, gravel & building materials', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(58, '6960', 'Raw Materials Expense', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Brick clay, whole spices & fruit raw inputs', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(59, '6970', 'Repairs & Overhauls', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Asset repair & major maintenance', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(60, '6980', 'Administrative Expenses', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Office supplies & management expense', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(61, '6990', 'Other Operating Expenses', 6, 6, 'Expense', 'debit', 1, 1, 1, 'Sundry operational expenses', '2026-08-14 21:24:52', '2026-08-14 21:24:52', NULL),
(62, '4250', 'Membership Registration Revenue', 28, 4, 'Revenue', 'credit', 1, 1, 1, 'Cooperative Society Membership Registration Fee Income', '2026-08-14 21:24:53', '2026-08-14 21:24:53', NULL),
(64, '1115', 'Undeposited Cheques', 7, 1, 'Asset', 'debit', 1, 1, 1, 'Cheques received but not yet banked/cleared', '2026-08-16 23:46:12', '2026-08-16 23:46:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `account_types`
--

CREATE TABLE `account_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `category` enum('Asset','Liability','Equity','Revenue','COGS','Expense') NOT NULL,
  `normal_balance` enum('debit','credit') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_types`
--

INSERT INTO `account_types` (`id`, `code`, `name`, `category`, `normal_balance`, `created_at`) VALUES
(1, 'ASSET', 'Assets', 'Asset', 'debit', '2026-08-14 21:24:52'),
(2, 'LIAB', 'Liabilities', 'Liability', 'credit', '2026-08-14 21:24:52'),
(3, 'EQUITY', 'Equity', 'Equity', 'credit', '2026-08-14 21:24:52'),
(4, 'REV', 'Revenue', 'Revenue', 'credit', '2026-08-14 21:24:52'),
(5, 'COGS', 'Cost of Goods Sold', 'COGS', 'debit', '2026-08-14 21:24:52'),
(6, 'EXP', 'Expenses', 'Expense', 'debit', '2026-08-14 21:24:52');

-- --------------------------------------------------------

--
-- Table structure for table `agricultural_sectors`
--

CREATE TABLE `agricultural_sectors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agricultural_sectors`
--

INSERT INTO `agricultural_sectors` (`id`, `name`) VALUES
(141, 'Animal Husbandry'),
(66, 'Areca nut'),
(1, 'Banana'),
(184, 'Black pepper'),
(41, 'Bulath'),
(392, 'Chili'),
(65, 'Cinnamon'),
(5, 'Coconut'),
(159, 'Cow management'),
(362, 'fish'),
(103, 'Floriculture'),
(16, 'Fruit'),
(125, 'Grass cultivation'),
(273, 'Home Gardening'),
(6, 'Paddy Cultivation'),
(120, 'Potato'),
(330, 'puwak'),
(34, 'Rubber'),
(209, 'Trade'),
(346, 'Turmeric'),
(2, 'Vanila'),
(7, 'Vegetable');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `record_id` int(10) UNSIGNED DEFAULT NULL,
  `old_values` longtext DEFAULT NULL,
  `new_values` longtext DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:01:43'),
(2, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.14.169', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-14 06:07:52'),
(3, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:43:35'),
(4, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:43:48'),
(5, 1, 'logout', 'auth', 1, NULL, NULL, '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:44:35'),
(6, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:44:38'),
(7, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '111.223.191.122', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-14 06:46:52'),
(8, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:48:40'),
(9, 1, 'logout', 'auth', 1, NULL, NULL, '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:48:43'),
(10, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:48:46'),
(11, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:48:53'),
(12, 1, 'logout', 'auth', 1, NULL, NULL, '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:49:15'),
(13, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:49:17'),
(14, 6, 'logout', 'auth', 6, NULL, NULL, '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:49:18'),
(15, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:49:22'),
(16, 6, 'logout', 'auth', 6, NULL, NULL, '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:49:32'),
(17, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 06:49:35'),
(18, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-14 16:09:31'),
(19, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-15 07:12:55'),
(20, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.12.113', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-15 07:15:50'),
(21, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '123.231.121.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-15 08:44:45'),
(22, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-15 08:58:19'),
(23, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-15 10:34:28'),
(24, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '123.231.121.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-15 11:32:01'),
(25, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-15 15:45:50'),
(26, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '111.223.184.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 03:14:31'),
(27, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-16 04:46:49'),
(28, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '111.223.184.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:28:49'),
(29, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-16 08:19:17'),
(30, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '111.223.184.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 10:12:10'),
(31, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '111.223.178.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 03:21:40'),
(32, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.27.128', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 09:44:56'),
(33, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.27.128', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 10:36:10'),
(34, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.178.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-17 14:34:39'),
(35, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.13.240', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 03:29:32'),
(36, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.46.162', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 07:18:13'),
(37, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.46.162', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 09:09:50'),
(38, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.25.42', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 03:43:20'),
(39, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.25.42', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 06:27:36'),
(40, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.180.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-21 04:31:36'),
(41, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 06:23:18'),
(42, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '123.231.85.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 06:25:32'),
(43, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.25.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 09:46:40'),
(44, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 09:51:23'),
(45, 1, 'logout', 'auth', 1, NULL, NULL, '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 10:35:32'),
(46, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 10:35:35'),
(47, 1, 'create_journal', 'accounting', 1, NULL, '{\"journal_number\":\"JV-202609-0001\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 10:54:51'),
(48, 1, 'post_central_invoice', 'finance', 1, NULL, '{\"invoice_number\":\"INV - 001\",\"total\":\"2000.00\",\"journal_entry_id\":1}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 10:54:51'),
(49, 1, 'create_journal', 'accounting', 2, NULL, '{\"journal_number\":\"JV-202609-0002\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 11:17:06'),
(50, 1, 'post_central_invoice', 'finance', 2, NULL, '{\"invoice_number\":\"INV - 002\",\"total\":\"2000.00\",\"journal_entry_id\":2}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 11:17:06'),
(51, 1, 'create_journal', 'accounting', 3, NULL, '{\"journal_number\":\"JV-202609-0003\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 11:20:43'),
(52, 1, 'post_central_invoice', 'finance', 3, NULL, '{\"invoice_number\":\"INV - 003\",\"total\":\"2000.00\",\"journal_entry_id\":3}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 11:20:43'),
(53, 6, 'create_journal', 'accounting', 4, NULL, '{\"journal_number\":\"JV-202609-0004\",\"status\":\"posted\"}', '175.157.25.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:23:17'),
(54, 6, 'post_central_invoice', 'finance', 4, NULL, '{\"invoice_number\":\"INV - 004\",\"total\":\"2000.00\",\"journal_entry_id\":4}', '175.157.25.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:23:17'),
(55, 6, 'create_journal', 'accounting', 5, NULL, '{\"journal_number\":\"JV-202609-0005\",\"status\":\"posted\"}', '175.157.25.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:27:36'),
(56, 6, 'post_central_invoice', 'finance', 5, NULL, '{\"invoice_number\":\"INV - 005\",\"total\":\"2000.00\",\"journal_entry_id\":5}', '175.157.25.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:27:36'),
(57, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 11:51:37'),
(58, 1, 'create_journal', 'accounting', 6, NULL, '{\"journal_number\":\"JV-202609-0006\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 12:08:45'),
(59, 1, 'post_central_invoice', 'finance', 2, NULL, '{\"invoice_number\":\"INV - 002\",\"total\":\"2000.00\",\"journal_entry_id\":6}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 12:08:45'),
(60, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-22 15:15:02'),
(61, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.43.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 03:19:05'),
(62, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.10.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 08:39:07'),
(63, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.10.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 09:33:06'),
(64, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 04:18:23'),
(65, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 04:59:30'),
(66, 1, 'create_journal', 'accounting', 7, NULL, '{\"journal_number\":\"JV-202609-0007\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:02:58'),
(67, 1, 'post_central_invoice', 'finance', 8, NULL, '{\"invoice_number\":\"INV - 006\",\"total\":\"2000.00\",\"journal_entry_id\":7}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:02:58'),
(71, 1, 'reverse_journal', 'accounting', 3, NULL, '{\"original_number\":\"JV-202609-0003\",\"reversal_number\":\"JV-202609-0008\",\"reason\":\"Reversal of Invoice INV - 003: Invoice cancelled\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:22:05'),
(72, 1, 'cancel_central_invoice', 'finance', 3, NULL, '{\"invoice_number\":\"INV - 003\",\"reason\":\"Invoice cancelled\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:22:05'),
(73, 1, 'create_journal', 'accounting', 12, NULL, '{\"journal_number\":\"JV-202609-0009\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:23:48'),
(74, 1, 'post_central_invoice', 'finance', 1, NULL, '{\"invoice_number\":\"INV - 001\",\"total\":\"2000.00\",\"journal_entry_id\":12}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:23:48'),
(75, 1, 'create_journal', 'accounting', 13, NULL, '{\"journal_number\":\"JV-202609-0009\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:24:04'),
(76, 1, 'post_central_invoice', 'finance', 1, NULL, '{\"invoice_number\":\"INV - 001\",\"total\":\"2000.00\",\"journal_entry_id\":13}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:24:04'),
(77, 6, 'create_journal', 'accounting', 14, NULL, '{\"journal_number\":\"JV-202609-0010\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:40:06'),
(78, 6, 'post_central_invoice', 'finance', 9, NULL, '{\"invoice_number\":\"INV - 007\",\"total\":\"2000.00\",\"journal_entry_id\":14}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:40:06'),
(79, 1, 'create_journal', 'accounting', 15, NULL, '{\"journal_number\":\"JV-202609-0011\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:40:46'),
(80, 1, 'post_central_invoice', 'finance', 1, NULL, '{\"invoice_number\":\"INV - 001\",\"total\":\"2000.00\",\"journal_entry_id\":15}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:40:46'),
(81, 1, 'create_journal', 'accounting', 16, NULL, '{\"journal_number\":\"JV-202609-0012\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:41:23'),
(82, 1, 'post_central_invoice', 'finance', 4, NULL, '{\"invoice_number\":\"INV - 004\",\"total\":\"2000.00\",\"journal_entry_id\":16}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:41:23'),
(83, 1, 'create_journal', 'accounting', 17, NULL, '{\"journal_number\":\"JV-202609-0013\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:41:41'),
(84, 1, 'post_central_invoice', 'finance', 5, NULL, '{\"invoice_number\":\"INV - 005\",\"total\":\"2000.00\",\"journal_entry_id\":17}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:41:41'),
(85, 6, 'create_journal', 'accounting', 18, NULL, '{\"journal_number\":\"JV-202609-0014\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:42:10'),
(86, 6, 'post_central_invoice', 'finance', 10, NULL, '{\"invoice_number\":\"INV - 008\",\"total\":\"2000.00\",\"journal_entry_id\":18}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:42:10'),
(87, 1, 'create_journal', 'accounting', 19, NULL, '{\"journal_number\":\"JV-202609-0015\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:42:13'),
(88, 1, 'post_central_invoice', 'finance', 8, NULL, '{\"invoice_number\":\"INV - 006\",\"total\":\"2000.00\",\"journal_entry_id\":19}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:42:13'),
(89, 1, 'create_journal', 'accounting', 20, NULL, '{\"journal_number\":\"JV-202609-0016\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:42:44'),
(90, 1, 'post_central_invoice', 'finance', 9, NULL, '{\"invoice_number\":\"INV - 007\",\"total\":\"2000.00\",\"journal_entry_id\":20}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 05:42:44'),
(91, 6, 'create_journal', 'accounting', 21, NULL, '{\"journal_number\":\"JV-202609-0017\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:45:46'),
(92, 6, 'post_central_invoice', 'finance', 11, NULL, '{\"invoice_number\":\"INV - 009\",\"total\":\"2000.00\",\"journal_entry_id\":21}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:45:46'),
(93, 6, 'reverse_journal', 'accounting', 21, NULL, '{\"original_number\":\"JV-202609-0017\",\"reversal_number\":\"JV-202609-0018\",\"reason\":\"Reversal of Invoice INV - 009: Invoice cancelled\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:13:09'),
(94, 6, 'cancel_central_invoice', 'finance', 11, NULL, '{\"invoice_number\":\"INV - 009\",\"reason\":\"Invoice cancelled\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:13:09'),
(95, 6, 'create_journal', 'accounting', 23, NULL, '{\"journal_number\":\"JV-202609-0019\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:14:03'),
(96, 6, 'post_central_invoice', 'finance', 12, NULL, '{\"invoice_number\":\"INV - 010\",\"total\":\"2000.00\",\"journal_entry_id\":23}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:14:03'),
(97, 6, 'create_journal', 'accounting', 24, NULL, '{\"journal_number\":\"JV-202609-0020\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:14:42'),
(98, 6, 'post_central_invoice', 'finance', 13, NULL, '{\"invoice_number\":\"INV - 011\",\"total\":\"2000.00\",\"journal_entry_id\":24}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:14:42'),
(99, 6, 'create_journal', 'accounting', 25, NULL, '{\"journal_number\":\"JV-202609-0020\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:14:59'),
(100, 6, 'post_central_invoice', 'finance', 13, NULL, '{\"invoice_number\":\"INV - 011\",\"total\":\"2000.00\",\"journal_entry_id\":25}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:14:59'),
(101, 6, 'create_journal', 'accounting', 26, NULL, '{\"journal_number\":\"JV-202609-0021\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:15:49'),
(102, 6, 'post_central_invoice', 'finance', 14, NULL, '{\"invoice_number\":\"INV - 012\",\"total\":\"2000.00\",\"journal_entry_id\":26}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:15:49'),
(103, 6, 'create_journal', 'accounting', 27, NULL, '{\"journal_number\":\"JV-202609-0022\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:20:17'),
(104, 6, 'post_central_invoice', 'finance', 15, NULL, '{\"invoice_number\":\"INV - 013\",\"total\":\"2000.00\",\"journal_entry_id\":27}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:20:17'),
(105, 6, 'reverse_journal', 'accounting', 27, NULL, '{\"original_number\":\"JV-202609-0022\",\"reversal_number\":\"JV-202609-0023\",\"reason\":\"Reversal of Invoice INV - 013: Invoice cancelled\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:20:28'),
(106, 6, 'cancel_central_invoice', 'finance', 15, NULL, '{\"invoice_number\":\"INV - 013\",\"reason\":\"Invoice cancelled\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 06:20:28'),
(107, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 07:13:24'),
(108, 6, 'create_journal', 'accounting', 29, NULL, '{\"journal_number\":\"JV-202609-0024\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:22:10'),
(109, 6, 'post_central_invoice', 'finance', 17, NULL, '{\"invoice_number\":\"INV - 015\",\"total\":\"2000.00\",\"journal_entry_id\":29}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:22:10'),
(110, 6, 'reverse_journal', 'accounting', 29, NULL, '{\"original_number\":\"JV-202609-0024\",\"reversal_number\":\"JV-202609-0025\",\"reason\":\"Reversal of Invoice INV - 015: Cancel in Invoice Box\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:22:48'),
(111, 6, 'cancel_central_invoice', 'finance', 17, NULL, '{\"invoice_number\":\"INV - 015\",\"reason\":\"Cancel in Invoice Box\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:22:48'),
(112, 6, 'create_journal', 'accounting', 31, NULL, '{\"journal_number\":\"JV-202609-0026\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:23:49'),
(113, 6, 'post_central_invoice', 'finance', 18, NULL, '{\"invoice_number\":\"INV - 016\",\"total\":\"2000.00\",\"journal_entry_id\":31}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:23:49'),
(114, 6, 'reverse_journal', 'accounting', 31, NULL, '{\"original_number\":\"JV-202609-0026\",\"reversal_number\":\"JV-202609-0027\",\"reason\":\"Reversal of Invoice INV - 016: In Invoice Book\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:24:01'),
(115, 6, 'cancel_central_invoice', 'finance', 18, NULL, '{\"invoice_number\":\"INV - 016\",\"reason\":\"In Invoice Book\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:24:01'),
(116, 6, 'create_journal', 'accounting', 33, NULL, '{\"journal_number\":\"JV-202609-0028\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:26:31'),
(117, 6, 'post_central_invoice', 'finance', 20, NULL, '{\"invoice_number\":\"INV - 018\",\"total\":\"2000.00\",\"journal_entry_id\":33}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:26:31'),
(118, 6, 'create_journal', 'accounting', 34, NULL, '{\"journal_number\":\"JV-202609-0029\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:31:43'),
(119, 6, 'post_central_invoice', 'finance', 21, NULL, '{\"invoice_number\":\"INV - 019\",\"total\":\"2000.00\",\"journal_entry_id\":34}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:31:43'),
(120, 6, 'create_journal', 'accounting', 35, NULL, '{\"journal_number\":\"JV-202609-0030\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:33:33'),
(121, 6, 'post_central_invoice', 'finance', 22, NULL, '{\"invoice_number\":\"INV - 020\",\"total\":\"2000.00\",\"journal_entry_id\":35}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:33:33'),
(122, 6, 'create_journal', 'accounting', 36, NULL, '{\"journal_number\":\"JV-202609-0031\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:34:12'),
(123, 6, 'post_central_invoice', 'finance', 23, NULL, '{\"invoice_number\":\"INV - 021\",\"total\":\"2000.00\",\"journal_entry_id\":36}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:34:12'),
(124, 6, 'create_journal', 'accounting', 37, NULL, '{\"journal_number\":\"JV-202609-0032\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:35:13'),
(125, 6, 'post_central_invoice', 'finance', 24, NULL, '{\"invoice_number\":\"INV - 022\",\"total\":\"2000.00\",\"journal_entry_id\":37}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:35:13'),
(126, 6, 'create_journal', 'accounting', 38, NULL, '{\"journal_number\":\"JV-202609-0033\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:36:06'),
(127, 6, 'post_central_invoice', 'finance', 25, NULL, '{\"invoice_number\":\"INV - 023\",\"total\":\"2000.00\",\"journal_entry_id\":38}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:36:06'),
(128, 6, 'create_journal', 'accounting', 39, NULL, '{\"journal_number\":\"JV-202609-0034\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:42:57'),
(129, 6, 'post_central_invoice', 'finance', 26, NULL, '{\"invoice_number\":\"INV - 024\",\"total\":\"2000.00\",\"journal_entry_id\":39}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:42:57'),
(130, 6, 'create_journal', 'accounting', 40, NULL, '{\"journal_number\":\"JV-202609-0035\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:43:47'),
(131, 6, 'post_central_invoice', 'finance', 27, NULL, '{\"invoice_number\":\"INV - 025\",\"total\":\"2000.00\",\"journal_entry_id\":40}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:43:47'),
(132, 6, 'create_journal', 'accounting', 41, NULL, '{\"journal_number\":\"JV-202609-0036\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:44:36'),
(133, 6, 'post_central_invoice', 'finance', 28, NULL, '{\"invoice_number\":\"INV - 026\",\"total\":\"2000.00\",\"journal_entry_id\":41}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:44:36'),
(134, 6, 'create_journal', 'accounting', 42, NULL, '{\"journal_number\":\"JV-202609-0037\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:45:25'),
(135, 6, 'post_central_invoice', 'finance', 29, NULL, '{\"invoice_number\":\"INV - 027\",\"total\":\"2000.00\",\"journal_entry_id\":42}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:45:25'),
(136, 6, 'create_journal', 'accounting', 43, NULL, '{\"journal_number\":\"JV-202609-0038\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:46:08'),
(137, 6, 'post_central_invoice', 'finance', 30, NULL, '{\"invoice_number\":\"INV - 028\",\"total\":\"2000.00\",\"journal_entry_id\":43}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:46:08'),
(138, 6, 'create_journal', 'accounting', 44, NULL, '{\"journal_number\":\"JV-202609-0039\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:46:42'),
(139, 6, 'post_central_invoice', 'finance', 31, NULL, '{\"invoice_number\":\"INV - 029\",\"total\":\"2000.00\",\"journal_entry_id\":44}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 07:46:42'),
(140, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:28:26'),
(141, 6, 'create_journal', 'accounting', 45, NULL, '{\"journal_number\":\"JV-202609-0040\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:34:47'),
(142, 6, 'post_central_invoice', 'finance', 32, NULL, '{\"invoice_number\":\"INV - 030\",\"total\":\"2000.00\",\"journal_entry_id\":45}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:34:47'),
(143, 6, 'create_journal', 'accounting', 46, NULL, '{\"journal_number\":\"JV-202609-0041\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:35:34'),
(144, 6, 'post_central_invoice', 'finance', 33, NULL, '{\"invoice_number\":\"INV - 031\",\"total\":\"2000.00\",\"journal_entry_id\":46}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:35:34'),
(145, 6, 'create_journal', 'accounting', 47, NULL, '{\"journal_number\":\"JV-202609-0042\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:36:25'),
(146, 6, 'post_central_invoice', 'finance', 34, NULL, '{\"invoice_number\":\"INV - 032\",\"total\":\"2000.00\",\"journal_entry_id\":47}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:36:25'),
(147, 6, 'create_journal', 'accounting', 48, NULL, '{\"journal_number\":\"JV-202609-0043\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:37:14'),
(148, 6, 'post_central_invoice', 'finance', 35, NULL, '{\"invoice_number\":\"INV - 033\",\"total\":\"2000.00\",\"journal_entry_id\":48}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:37:14'),
(149, 6, 'create_journal', 'accounting', 49, NULL, '{\"journal_number\":\"JV-202609-0044\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:41:45'),
(150, 6, 'post_central_invoice', 'finance', 36, NULL, '{\"invoice_number\":\"INV - 034\",\"total\":\"2000.00\",\"journal_entry_id\":49}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:41:45'),
(151, 6, 'create_journal', 'accounting', 50, NULL, '{\"journal_number\":\"JV-202609-0045\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:51:18'),
(152, 6, 'post_central_invoice', 'finance', 37, NULL, '{\"invoice_number\":\"INV - 035\",\"total\":\"2000.00\",\"journal_entry_id\":50}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:51:18'),
(153, 6, 'create_journal', 'accounting', 51, NULL, '{\"journal_number\":\"JV-202609-0046\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:51:54'),
(154, 6, 'post_central_invoice', 'finance', 38, NULL, '{\"invoice_number\":\"INV - 036\",\"total\":\"2000.00\",\"journal_entry_id\":51}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:51:54'),
(155, 6, 'create_journal', 'accounting', 52, NULL, '{\"journal_number\":\"JV-202609-0047\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:52:22'),
(156, 6, 'post_central_invoice', 'finance', 39, NULL, '{\"invoice_number\":\"INV - 037\",\"total\":\"2000.00\",\"journal_entry_id\":52}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:52:22'),
(157, 6, 'create_journal', 'accounting', 53, NULL, '{\"journal_number\":\"JV-202609-0048\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:53:04'),
(158, 6, 'post_central_invoice', 'finance', 40, NULL, '{\"invoice_number\":\"INV - 038\",\"total\":\"2000.00\",\"journal_entry_id\":53}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:53:04'),
(159, 6, 'create_journal', 'accounting', 54, NULL, '{\"journal_number\":\"JV-202609-0049\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:53:36'),
(160, 6, 'post_central_invoice', 'finance', 41, NULL, '{\"invoice_number\":\"INV - 039\",\"total\":\"2000.00\",\"journal_entry_id\":54}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:53:36'),
(161, 6, 'create_journal', 'accounting', 55, NULL, '{\"journal_number\":\"JV-202609-0050\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:54:03'),
(162, 6, 'post_central_invoice', 'finance', 42, NULL, '{\"invoice_number\":\"INV - 040\",\"total\":\"2000.00\",\"journal_entry_id\":55}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:54:03'),
(163, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 09:56:25'),
(164, 6, 'create_journal', 'accounting', 56, NULL, '{\"journal_number\":\"JV-202609-0051\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:58:48'),
(165, 6, 'post_central_invoice', 'finance', 43, NULL, '{\"invoice_number\":\"INV - 041\",\"total\":\"2000.00\",\"journal_entry_id\":56}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:58:48'),
(166, 6, 'create_journal', 'accounting', 57, NULL, '{\"journal_number\":\"JV-202609-0052\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:05:04'),
(167, 6, 'post_central_invoice', 'finance', 44, NULL, '{\"invoice_number\":\"INV - 042\",\"total\":\"2000.00\",\"journal_entry_id\":57}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:05:04'),
(168, 6, 'create_journal', 'accounting', 58, NULL, '{\"journal_number\":\"JV-202609-0053\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:11:29'),
(169, 6, 'post_central_invoice', 'finance', 45, NULL, '{\"invoice_number\":\"INV - 043\",\"total\":\"2000.00\",\"journal_entry_id\":58}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:11:29'),
(170, 6, 'create_journal', 'accounting', 59, NULL, '{\"journal_number\":\"JV-202609-0054\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:12:51'),
(171, 6, 'post_central_invoice', 'finance', 46, NULL, '{\"invoice_number\":\"INV - 044\",\"total\":\"2000.00\",\"journal_entry_id\":59}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:12:51'),
(172, 6, 'create_journal', 'accounting', 60, NULL, '{\"journal_number\":\"JV-202609-0055\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:13:26'),
(173, 6, 'post_central_invoice', 'finance', 47, NULL, '{\"invoice_number\":\"INV - 045\",\"total\":\"2000.00\",\"journal_entry_id\":60}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:13:26'),
(174, 6, 'create_journal', 'accounting', 61, NULL, '{\"journal_number\":\"JV-202609-0056\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:13:58'),
(175, 6, 'post_central_invoice', 'finance', 48, NULL, '{\"invoice_number\":\"INV - 046\",\"total\":\"2000.00\",\"journal_entry_id\":61}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:13:58'),
(176, 6, 'create_journal', 'accounting', 62, NULL, '{\"journal_number\":\"JV-202609-0057\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:14:28'),
(177, 6, 'post_central_invoice', 'finance', 49, NULL, '{\"invoice_number\":\"INV - 047\",\"total\":\"2000.00\",\"journal_entry_id\":62}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:14:28'),
(178, 6, 'create_journal', 'accounting', 63, NULL, '{\"journal_number\":\"JV-202609-0058\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:15:01'),
(179, 6, 'post_central_invoice', 'finance', 50, NULL, '{\"invoice_number\":\"INV - 048\",\"total\":\"2000.00\",\"journal_entry_id\":63}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:15:01'),
(180, 6, 'create_journal', 'accounting', 64, NULL, '{\"journal_number\":\"JV-202609-0059\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:15:30'),
(181, 6, 'post_central_invoice', 'finance', 51, NULL, '{\"invoice_number\":\"INV - 049\",\"total\":\"2000.00\",\"journal_entry_id\":64}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:15:30'),
(182, 6, 'create_journal', 'accounting', 65, NULL, '{\"journal_number\":\"JV-202609-0060\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:15:59'),
(183, 6, 'post_central_invoice', 'finance', 52, NULL, '{\"invoice_number\":\"INV - 050\",\"total\":\"1000.00\",\"journal_entry_id\":65}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:15:59'),
(184, 6, 'create_journal', 'accounting', 66, NULL, '{\"journal_number\":\"JV-202609-0060\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:16:11'),
(185, 6, 'post_central_invoice', 'finance', 52, NULL, '{\"invoice_number\":\"INV - 050\",\"total\":\"2000.00\",\"journal_entry_id\":66}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:16:11'),
(186, 6, 'create_journal', 'accounting', 67, NULL, '{\"journal_number\":\"JV-202609-0061\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:16:45'),
(187, 6, 'post_central_invoice', 'finance', 53, NULL, '{\"invoice_number\":\"INV - 051\",\"total\":\"2000.00\",\"journal_entry_id\":67}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:16:45'),
(188, 6, 'create_journal', 'accounting', 68, NULL, '{\"journal_number\":\"JV-202609-0062\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:17:14'),
(189, 6, 'post_central_invoice', 'finance', 54, NULL, '{\"invoice_number\":\"INV - 052\",\"total\":\"2000.00\",\"journal_entry_id\":68}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:17:14');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(190, 6, 'create_journal', 'accounting', 69, NULL, '{\"journal_number\":\"JV-202609-0063\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:17:46'),
(191, 6, 'post_central_invoice', 'finance', 55, NULL, '{\"invoice_number\":\"INV - 053\",\"total\":\"2000.00\",\"journal_entry_id\":69}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:17:46'),
(192, 6, 'create_journal', 'accounting', 70, NULL, '{\"journal_number\":\"JV-202609-0064\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:18:21'),
(193, 6, 'post_central_invoice', 'finance', 56, NULL, '{\"invoice_number\":\"INV - 054\",\"total\":\"2000.00\",\"journal_entry_id\":70}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 10:18:21'),
(194, 1, 'create_journal', 'accounting', 71, NULL, '{\"journal_number\":\"JV-202609-0065\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 11:08:47'),
(195, 1, 'post_central_invoice', 'finance', 57, NULL, '{\"invoice_number\":\"INV - 055\",\"total\":\"25000.00\",\"journal_entry_id\":71}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-24 11:08:47'),
(196, 6, 'create_journal', 'accounting', 72, NULL, '{\"journal_number\":\"JV-202609-0066\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 11:15:32'),
(197, 6, 'post_central_invoice', 'finance', 58, NULL, '{\"invoice_number\":\"INV - 056\",\"total\":\"1000.00\",\"journal_entry_id\":72}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 11:15:32'),
(198, 6, 'create_journal', 'accounting', 73, NULL, '{\"journal_number\":\"JV-202609-0067\",\"status\":\"posted\"}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 11:16:35'),
(199, 6, 'post_central_invoice', 'finance', 59, NULL, '{\"invoice_number\":\"INV - 057\",\"total\":\"1000.00\",\"journal_entry_id\":73}', '175.157.29.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 11:16:35'),
(200, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-25 05:17:58'),
(201, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 05:31:01'),
(202, 1, 'update', 'company_settings', 0, NULL, '{\"company_name_en\":\"Agri Co-Op Cooperative Society Limited\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-25 05:32:58'),
(203, 6, 'create_journal', 'accounting', 74, NULL, '{\"journal_number\":\"JV-202609-0068\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 05:33:34'),
(204, 6, 'post_central_invoice', 'finance', 60, NULL, '{\"invoice_number\":\"INV - 058\",\"total\":\"2000.00\",\"journal_entry_id\":74}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 05:33:34'),
(205, 6, 'create_journal', 'accounting', 75, NULL, '{\"journal_number\":\"JV-202609-0069\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 05:34:17'),
(206, 6, 'post_central_invoice', 'finance', 61, NULL, '{\"invoice_number\":\"INV - 059\",\"total\":\"1000.00\",\"journal_entry_id\":75}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 05:34:17'),
(207, 6, 'create_journal', 'accounting', 76, NULL, '{\"journal_number\":\"JV-202609-0070\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 05:34:52'),
(208, 6, 'post_central_invoice', 'finance', 62, NULL, '{\"invoice_number\":\"INV - 060\",\"total\":\"2000.00\",\"journal_entry_id\":76}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 05:34:52'),
(209, 6, 'create_journal', 'accounting', 77, NULL, '{\"journal_number\":\"JV-202609-0071\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 05:35:23'),
(210, 6, 'post_central_invoice', 'finance', 63, NULL, '{\"invoice_number\":\"INV - 061\",\"total\":\"2000.00\",\"journal_entry_id\":77}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 05:35:23'),
(211, 6, 'create_journal', 'accounting', 78, NULL, '{\"journal_number\":\"JV-202609-0072\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 05:35:56'),
(212, 6, 'post_central_invoice', 'finance', 64, NULL, '{\"invoice_number\":\"INV - 062\",\"total\":\"2000.00\",\"journal_entry_id\":78}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 05:35:56'),
(213, 6, 'create_journal', 'accounting', 79, NULL, '{\"journal_number\":\"JV-202609-0073\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 05:36:29'),
(214, 6, 'post_central_invoice', 'finance', 65, NULL, '{\"invoice_number\":\"INV - 063\",\"total\":\"2000.00\",\"journal_entry_id\":79}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 05:36:29'),
(215, 1, 'create_journal', 'accounting', 80, NULL, '{\"journal_number\":\"JV-202609-0074\",\"status\":\"posted\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-25 06:03:02'),
(216, 1, 'post_central_invoice', 'finance', 66, NULL, '{\"invoice_number\":\"INV - 064\",\"total\":\"14000.00\",\"journal_entry_id\":80}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-25 06:03:02'),
(217, 6, 'create_journal', 'accounting', 81, NULL, '{\"journal_number\":\"JV-202609-0075\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:16:18'),
(218, 6, 'post_central_invoice', 'finance', 67, NULL, '{\"invoice_number\":\"INV - 065\",\"total\":\"32000.00\",\"journal_entry_id\":81}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:16:18'),
(219, 6, 'create_journal', 'accounting', 82, NULL, '{\"journal_number\":\"JV-202609-0076\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:16:53'),
(220, 6, 'post_central_invoice', 'finance', 68, NULL, '{\"invoice_number\":\"INV - 066\",\"total\":\"2000.00\",\"journal_entry_id\":82}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:16:53'),
(221, 6, 'create_journal', 'accounting', 83, NULL, '{\"journal_number\":\"JV-202609-0077\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:17:23'),
(222, 6, 'post_central_invoice', 'finance', 69, NULL, '{\"invoice_number\":\"INV - 067\",\"total\":\"2000.00\",\"journal_entry_id\":83}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:17:23'),
(223, 6, 'create_journal', 'accounting', 84, NULL, '{\"journal_number\":\"JV-202609-0078\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:17:52'),
(224, 6, 'post_central_invoice', 'finance', 70, NULL, '{\"invoice_number\":\"INV - 068\",\"total\":\"2000.00\",\"journal_entry_id\":84}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:17:52'),
(225, 6, 'create_journal', 'accounting', 85, NULL, '{\"journal_number\":\"JV-202609-0079\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:18:48'),
(226, 6, 'post_central_invoice', 'finance', 71, NULL, '{\"invoice_number\":\"INV - 069\",\"total\":\"2000.00\",\"journal_entry_id\":85}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:18:48'),
(227, 6, 'create_journal', 'accounting', 86, NULL, '{\"journal_number\":\"JV-202609-0080\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:19:18'),
(228, 6, 'post_central_invoice', 'finance', 72, NULL, '{\"invoice_number\":\"INV - 070\",\"total\":\"2000.00\",\"journal_entry_id\":86}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:19:18'),
(229, 6, 'create_journal', 'accounting', 87, NULL, '{\"journal_number\":\"JV-202609-0081\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:19:55'),
(230, 6, 'post_central_invoice', 'finance', 73, NULL, '{\"invoice_number\":\"INV - 071\",\"total\":\"2000.00\",\"journal_entry_id\":87}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:19:55'),
(231, 6, 'create_journal', 'accounting', 88, NULL, '{\"journal_number\":\"JV-202609-0082\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:20:32'),
(232, 6, 'post_central_invoice', 'finance', 74, NULL, '{\"invoice_number\":\"INV - 072\",\"total\":\"2000.00\",\"journal_entry_id\":88}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:20:32'),
(233, 6, 'create_journal', 'accounting', 89, NULL, '{\"journal_number\":\"JV-202609-0083\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:21:05'),
(234, 6, 'post_central_invoice', 'finance', 75, NULL, '{\"invoice_number\":\"INV - 073\",\"total\":\"2000.00\",\"journal_entry_id\":89}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:21:05'),
(235, 6, 'create_journal', 'accounting', 90, NULL, '{\"journal_number\":\"JV-202609-0084\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:21:31'),
(236, 6, 'post_central_invoice', 'finance', 76, NULL, '{\"invoice_number\":\"INV - 074\",\"total\":\"2000.00\",\"journal_entry_id\":90}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:21:31'),
(237, 6, 'create_journal', 'accounting', 91, NULL, '{\"journal_number\":\"JV-202609-0085\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:22:08'),
(238, 6, 'post_central_invoice', 'finance', 77, NULL, '{\"invoice_number\":\"INV - 075\",\"total\":\"2000.00\",\"journal_entry_id\":91}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:22:08'),
(239, 6, 'create_journal', 'accounting', 92, NULL, '{\"journal_number\":\"JV-202609-0086\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:22:35'),
(240, 6, 'post_central_invoice', 'finance', 78, NULL, '{\"invoice_number\":\"INV - 076\",\"total\":\"2000.00\",\"journal_entry_id\":92}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:22:35'),
(241, 6, 'create_journal', 'accounting', 93, NULL, '{\"journal_number\":\"JV-202609-0087\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:23:04'),
(242, 6, 'post_central_invoice', 'finance', 79, NULL, '{\"invoice_number\":\"INV - 077\",\"total\":\"2000.00\",\"journal_entry_id\":93}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:23:04'),
(243, 6, 'create_journal', 'accounting', 94, NULL, '{\"journal_number\":\"JV-202609-0088\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:23:31'),
(244, 6, 'post_central_invoice', 'finance', 80, NULL, '{\"invoice_number\":\"INV - 078\",\"total\":\"2000.00\",\"journal_entry_id\":94}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:23:31'),
(245, 6, 'create_journal', 'accounting', 95, NULL, '{\"journal_number\":\"JV-202609-0089\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:23:57'),
(246, 6, 'post_central_invoice', 'finance', 81, NULL, '{\"invoice_number\":\"INV - 079\",\"total\":\"2000.00\",\"journal_entry_id\":95}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:23:57'),
(247, 6, 'create_journal', 'accounting', 96, NULL, '{\"journal_number\":\"JV-202609-0090\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:24:21'),
(248, 6, 'post_central_invoice', 'finance', 82, NULL, '{\"invoice_number\":\"INV - 080\",\"total\":\"2000.00\",\"journal_entry_id\":96}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:24:21'),
(249, 6, 'create_journal', 'accounting', 97, NULL, '{\"journal_number\":\"JV-202609-0091\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:24:48'),
(250, 6, 'post_central_invoice', 'finance', 83, NULL, '{\"invoice_number\":\"INV - 081\",\"total\":\"2000.00\",\"journal_entry_id\":97}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:24:48'),
(251, 6, 'create_journal', 'accounting', 98, NULL, '{\"journal_number\":\"JV-202609-0092\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:25:14'),
(252, 6, 'post_central_invoice', 'finance', 84, NULL, '{\"invoice_number\":\"INV - 082\",\"total\":\"2000.00\",\"journal_entry_id\":98}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:25:14'),
(253, 6, 'create_journal', 'accounting', 99, NULL, '{\"journal_number\":\"JV-202609-0093\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:25:36'),
(254, 6, 'post_central_invoice', 'finance', 85, NULL, '{\"invoice_number\":\"INV - 083\",\"total\":\"2000.00\",\"journal_entry_id\":99}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:25:36'),
(255, 6, 'create_journal', 'accounting', 100, NULL, '{\"journal_number\":\"JV-202609-0094\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:26:02'),
(256, 6, 'post_central_invoice', 'finance', 86, NULL, '{\"invoice_number\":\"INV - 084\",\"total\":\"2000.00\",\"journal_entry_id\":100}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:26:02'),
(257, 6, 'create_journal', 'accounting', 101, NULL, '{\"journal_number\":\"JV-202609-0095\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:26:31'),
(258, 6, 'post_central_invoice', 'finance', 87, NULL, '{\"invoice_number\":\"INV - 085\",\"total\":\"2000.00\",\"journal_entry_id\":101}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:26:31'),
(259, 6, 'create_journal', 'accounting', 102, NULL, '{\"journal_number\":\"JV-202609-0096\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:26:54'),
(260, 6, 'post_central_invoice', 'finance', 88, NULL, '{\"invoice_number\":\"INV - 086\",\"total\":\"2000.00\",\"journal_entry_id\":102}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:26:54'),
(261, 6, 'create_journal', 'accounting', 103, NULL, '{\"journal_number\":\"JV-202609-0097\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:27:25'),
(262, 6, 'post_central_invoice', 'finance', 89, NULL, '{\"invoice_number\":\"INV - 087\",\"total\":\"2000.00\",\"journal_entry_id\":103}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:27:25'),
(263, 6, 'create_journal', 'accounting', 104, NULL, '{\"journal_number\":\"JV-202609-0098\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:28:06'),
(264, 6, 'post_central_invoice', 'finance', 90, NULL, '{\"invoice_number\":\"INV - 088\",\"total\":\"2000.00\",\"journal_entry_id\":104}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:28:06'),
(265, 6, 'create_journal', 'accounting', 105, NULL, '{\"journal_number\":\"JV-202609-0099\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:28:28'),
(266, 6, 'post_central_invoice', 'finance', 91, NULL, '{\"invoice_number\":\"INV - 089\",\"total\":\"2000.00\",\"journal_entry_id\":105}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:28:28'),
(267, 6, 'create_journal', 'accounting', 106, NULL, '{\"journal_number\":\"JV-202609-0100\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:31:58'),
(268, 6, 'post_central_invoice', 'finance', 92, NULL, '{\"invoice_number\":\"INV - 090\",\"total\":\"2000.00\",\"journal_entry_id\":106}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:31:58'),
(269, 6, 'create_journal', 'accounting', 107, NULL, '{\"journal_number\":\"JV-202609-0100\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:32:10'),
(270, 6, 'post_central_invoice', 'finance', 92, NULL, '{\"invoice_number\":\"INV - 090\",\"total\":\"2000.00\",\"journal_entry_id\":107}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:32:10'),
(271, 6, 'create_journal', 'accounting', 108, NULL, '{\"journal_number\":\"JV-202609-0101\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:32:33'),
(272, 6, 'post_central_invoice', 'finance', 93, NULL, '{\"invoice_number\":\"INV - 091\",\"total\":\"2000.00\",\"journal_entry_id\":108}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:32:33'),
(273, 6, 'create_journal', 'accounting', 109, NULL, '{\"journal_number\":\"JV-202609-0102\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:32:57'),
(274, 6, 'post_central_invoice', 'finance', 94, NULL, '{\"invoice_number\":\"INV - 092\",\"total\":\"2000.00\",\"journal_entry_id\":109}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:32:57'),
(275, 6, 'create_journal', 'accounting', 110, NULL, '{\"journal_number\":\"JV-202609-0103\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:33:21'),
(276, 6, 'post_central_invoice', 'finance', 95, NULL, '{\"invoice_number\":\"INV - 093\",\"total\":\"2000.00\",\"journal_entry_id\":110}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:33:21'),
(277, 6, 'create_journal', 'accounting', 111, NULL, '{\"journal_number\":\"JV-202609-0104\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:33:43'),
(278, 6, 'post_central_invoice', 'finance', 96, NULL, '{\"invoice_number\":\"INV - 094\",\"total\":\"2000.00\",\"journal_entry_id\":111}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:33:43'),
(279, 6, 'create_journal', 'accounting', 112, NULL, '{\"journal_number\":\"JV-202609-0105\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:34:12'),
(280, 6, 'post_central_invoice', 'finance', 97, NULL, '{\"invoice_number\":\"INV - 095\",\"total\":\"2000.00\",\"journal_entry_id\":112}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:34:12'),
(281, 6, 'create_journal', 'accounting', 113, NULL, '{\"journal_number\":\"JV-202609-0106\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:34:36'),
(282, 6, 'post_central_invoice', 'finance', 98, NULL, '{\"invoice_number\":\"INV - 096\",\"total\":\"2000.00\",\"journal_entry_id\":113}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:34:36'),
(283, 6, 'create_journal', 'accounting', 114, NULL, '{\"journal_number\":\"JV-202609-0107\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:34:59'),
(284, 6, 'post_central_invoice', 'finance', 99, NULL, '{\"invoice_number\":\"INV - 097\",\"total\":\"2000.00\",\"journal_entry_id\":114}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:34:59'),
(285, 6, 'create_journal', 'accounting', 115, NULL, '{\"journal_number\":\"JV-202609-0108\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:35:22'),
(286, 6, 'post_central_invoice', 'finance', 100, NULL, '{\"invoice_number\":\"INV - 098\",\"total\":\"2000.00\",\"journal_entry_id\":115}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:35:22'),
(287, 6, 'create_journal', 'accounting', 116, NULL, '{\"journal_number\":\"JV-202609-0109\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:35:56'),
(288, 6, 'post_central_invoice', 'finance', 101, NULL, '{\"invoice_number\":\"INV - 099\",\"total\":\"2000.00\",\"journal_entry_id\":116}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:35:56'),
(289, 6, 'create_journal', 'accounting', 117, NULL, '{\"journal_number\":\"JV-202609-0110\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:36:20'),
(290, 6, 'post_central_invoice', 'finance', 102, NULL, '{\"invoice_number\":\"INV - 100\",\"total\":\"2000.00\",\"journal_entry_id\":117}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:36:20'),
(291, 6, 'create_journal', 'accounting', 118, NULL, '{\"journal_number\":\"JV-202609-0111\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:47:04'),
(292, 6, 'post_central_invoice', 'finance', 103, NULL, '{\"invoice_number\":\"INV - 101\",\"total\":\"2000.00\",\"journal_entry_id\":118}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:47:04'),
(293, 6, 'create_journal', 'accounting', 119, NULL, '{\"journal_number\":\"JV-202609-0112\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:47:31'),
(294, 6, 'post_central_invoice', 'finance', 104, NULL, '{\"invoice_number\":\"INV - 102\",\"total\":\"2000.00\",\"journal_entry_id\":119}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:47:31'),
(295, 6, 'create_journal', 'accounting', 120, NULL, '{\"journal_number\":\"JV-202609-0113\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:47:59'),
(296, 6, 'post_central_invoice', 'finance', 105, NULL, '{\"invoice_number\":\"INV - 103\",\"total\":\"2000.00\",\"journal_entry_id\":120}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:47:59'),
(297, 6, 'create_journal', 'accounting', 121, NULL, '{\"journal_number\":\"JV-202609-0114\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:48:29'),
(298, 6, 'post_central_invoice', 'finance', 106, NULL, '{\"invoice_number\":\"INV - 104\",\"total\":\"2000.00\",\"journal_entry_id\":121}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:48:29'),
(299, 6, 'create_journal', 'accounting', 122, NULL, '{\"journal_number\":\"JV-202609-0115\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:48:54'),
(300, 6, 'post_central_invoice', 'finance', 107, NULL, '{\"invoice_number\":\"INV - 105\",\"total\":\"2000.00\",\"journal_entry_id\":122}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:48:54'),
(301, 6, 'create_journal', 'accounting', 123, NULL, '{\"journal_number\":\"JV-202609-0116\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:51:03'),
(302, 6, 'post_central_invoice', 'finance', 108, NULL, '{\"invoice_number\":\"INV - 106\",\"total\":\"2000.00\",\"journal_entry_id\":123}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:51:03'),
(303, 6, 'create_journal', 'accounting', 124, NULL, '{\"journal_number\":\"JV-202609-0117\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:51:33'),
(304, 6, 'post_central_invoice', 'finance', 109, NULL, '{\"invoice_number\":\"INV - 107\",\"total\":\"2000.00\",\"journal_entry_id\":124}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:51:33'),
(305, 6, 'create_journal', 'accounting', 125, NULL, '{\"journal_number\":\"JV-202609-0118\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:51:57'),
(306, 6, 'post_central_invoice', 'finance', 110, NULL, '{\"invoice_number\":\"INV - 108\",\"total\":\"2000.00\",\"journal_entry_id\":125}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:51:57'),
(307, 6, 'create_journal', 'accounting', 126, NULL, '{\"journal_number\":\"JV-202609-0119\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:53:15'),
(308, 6, 'post_central_invoice', 'finance', 111, NULL, '{\"invoice_number\":\"INV - 109\",\"total\":\"2000.00\",\"journal_entry_id\":126}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:53:15'),
(309, 6, 'create_journal', 'accounting', 127, NULL, '{\"journal_number\":\"JV-202609-0120\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:53:42'),
(310, 6, 'post_central_invoice', 'finance', 112, NULL, '{\"invoice_number\":\"INV - 110\",\"total\":\"2000.00\",\"journal_entry_id\":127}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:53:42'),
(311, 6, 'create_journal', 'accounting', 128, NULL, '{\"journal_number\":\"JV-202609-0121\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:54:10'),
(312, 6, 'post_central_invoice', 'finance', 113, NULL, '{\"invoice_number\":\"INV - 111\",\"total\":\"2000.00\",\"journal_entry_id\":128}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:54:10'),
(313, 6, 'create_journal', 'accounting', 129, NULL, '{\"journal_number\":\"JV-202609-0122\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:54:42'),
(314, 6, 'post_central_invoice', 'finance', 114, NULL, '{\"invoice_number\":\"INV - 112\",\"total\":\"2000.00\",\"journal_entry_id\":129}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:54:42'),
(315, 6, 'create_journal', 'accounting', 130, NULL, '{\"journal_number\":\"JV-202609-0123\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:55:07'),
(316, 6, 'post_central_invoice', 'finance', 115, NULL, '{\"invoice_number\":\"INV - 113\",\"total\":\"2000.00\",\"journal_entry_id\":130}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:55:07'),
(317, 6, 'create_journal', 'accounting', 131, NULL, '{\"journal_number\":\"JV-202609-0124\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:55:30'),
(318, 6, 'post_central_invoice', 'finance', 116, NULL, '{\"invoice_number\":\"INV - 114\",\"total\":\"2000.00\",\"journal_entry_id\":131}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:55:30'),
(319, 6, 'create_journal', 'accounting', 132, NULL, '{\"journal_number\":\"JV-202609-0125\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:55:52'),
(320, 6, 'post_central_invoice', 'finance', 117, NULL, '{\"invoice_number\":\"INV - 115\",\"total\":\"2000.00\",\"journal_entry_id\":132}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:55:52'),
(321, 6, 'create_journal', 'accounting', 133, NULL, '{\"journal_number\":\"JV-202609-0126\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:56:19'),
(322, 6, 'post_central_invoice', 'finance', 118, NULL, '{\"invoice_number\":\"INV - 116\",\"total\":\"2000.00\",\"journal_entry_id\":133}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:56:19'),
(323, 6, 'create_journal', 'accounting', 134, NULL, '{\"journal_number\":\"JV-202609-0127\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:56:41'),
(324, 6, 'post_central_invoice', 'finance', 119, NULL, '{\"invoice_number\":\"INV - 117\",\"total\":\"2000.00\",\"journal_entry_id\":134}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:56:41'),
(325, 6, 'create_journal', 'accounting', 135, NULL, '{\"journal_number\":\"JV-202609-0128\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:57:08'),
(326, 6, 'post_central_invoice', 'finance', 120, NULL, '{\"invoice_number\":\"INV - 118\",\"total\":\"2000.00\",\"journal_entry_id\":135}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:57:08'),
(327, 6, 'create_journal', 'accounting', 136, NULL, '{\"journal_number\":\"JV-202609-0129\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:57:38'),
(328, 6, 'post_central_invoice', 'finance', 121, NULL, '{\"invoice_number\":\"INV - 119\",\"total\":\"2000.00\",\"journal_entry_id\":136}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:57:38'),
(329, 6, 'create_journal', 'accounting', 137, NULL, '{\"journal_number\":\"JV-202609-0130\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:59:25'),
(330, 6, 'post_central_invoice', 'finance', 122, NULL, '{\"invoice_number\":\"INV - 120\",\"total\":\"2000.00\",\"journal_entry_id\":137}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:59:25'),
(331, 6, 'create_journal', 'accounting', 138, NULL, '{\"journal_number\":\"JV-202609-0131\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:59:56'),
(332, 6, 'post_central_invoice', 'finance', 123, NULL, '{\"invoice_number\":\"INV - 121\",\"total\":\"2000.00\",\"journal_entry_id\":138}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 06:59:56'),
(333, 6, 'create_journal', 'accounting', 139, NULL, '{\"journal_number\":\"JV-202609-0132\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:00:24'),
(334, 6, 'post_central_invoice', 'finance', 124, NULL, '{\"invoice_number\":\"INV - 122\",\"total\":\"2000.00\",\"journal_entry_id\":139}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:00:24'),
(335, 6, 'create_journal', 'accounting', 140, NULL, '{\"journal_number\":\"JV-202609-0133\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:00:49'),
(336, 6, 'post_central_invoice', 'finance', 125, NULL, '{\"invoice_number\":\"INV - 123\",\"total\":\"2000.00\",\"journal_entry_id\":140}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:00:49'),
(337, 6, 'create_journal', 'accounting', 141, NULL, '{\"journal_number\":\"JV-202609-0134\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:01:25'),
(338, 6, 'post_central_invoice', 'finance', 126, NULL, '{\"invoice_number\":\"INV - 124\",\"total\":\"2000.00\",\"journal_entry_id\":141}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:01:25'),
(339, 6, 'create_journal', 'accounting', 142, NULL, '{\"journal_number\":\"JV-202609-0135\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:05:15'),
(340, 6, 'post_central_invoice', 'finance', 127, NULL, '{\"invoice_number\":\"INV - 125\",\"total\":\"2000.00\",\"journal_entry_id\":142}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:05:15'),
(341, 6, 'create_journal', 'accounting', 143, NULL, '{\"journal_number\":\"JV-202609-0136\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:05:38'),
(342, 6, 'post_central_invoice', 'finance', 128, NULL, '{\"invoice_number\":\"INV - 126\",\"total\":\"2000.00\",\"journal_entry_id\":143}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:05:38'),
(343, 6, 'create_journal', 'accounting', 144, NULL, '{\"journal_number\":\"JV-202609-0137\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:06:01'),
(344, 6, 'post_central_invoice', 'finance', 129, NULL, '{\"invoice_number\":\"INV - 127\",\"total\":\"2000.00\",\"journal_entry_id\":144}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:06:01'),
(345, 6, 'create_journal', 'accounting', 145, NULL, '{\"journal_number\":\"JV-202609-0138\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:07:10'),
(346, 6, 'post_central_invoice', 'finance', 130, NULL, '{\"invoice_number\":\"INV - 128\",\"total\":\"5400.00\",\"journal_entry_id\":145}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:07:10'),
(347, 6, 'create_journal', 'accounting', 146, NULL, '{\"journal_number\":\"JV-202609-0138\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:07:24'),
(348, 6, 'post_central_invoice', 'finance', 130, NULL, '{\"invoice_number\":\"INV - 128\",\"total\":\"5400.00\",\"journal_entry_id\":146}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:07:24'),
(349, 6, 'create_journal', 'accounting', 147, NULL, '{\"journal_number\":\"JV-202609-0139\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:08:27'),
(350, 6, 'post_central_invoice', 'finance', 131, NULL, '{\"invoice_number\":\"INV - 129\",\"total\":\"5400.00\",\"journal_entry_id\":147}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:08:27'),
(351, 6, 'create_journal', 'accounting', 148, NULL, '{\"journal_number\":\"JV-202609-0140\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:12:00'),
(352, 6, 'post_central_invoice', 'finance', 132, NULL, '{\"invoice_number\":\"INV - 130\",\"total\":\"15120.00\",\"journal_entry_id\":148}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:12:00'),
(353, 6, 'create_journal', 'accounting', 149, NULL, '{\"journal_number\":\"JV-202609-0141\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:15:16'),
(354, 6, 'post_central_invoice', 'finance', 133, NULL, '{\"invoice_number\":\"INV - 131\",\"total\":\"12960.00\",\"journal_entry_id\":149}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:15:16'),
(355, 6, 'create_journal', 'accounting', 150, NULL, '{\"journal_number\":\"JV-202609-0142\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:16:05'),
(356, 6, 'post_central_invoice', 'finance', 134, NULL, '{\"invoice_number\":\"INV - 132\",\"total\":\"3240.00\",\"journal_entry_id\":150}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:16:05'),
(357, 6, 'create_journal', 'accounting', 151, NULL, '{\"journal_number\":\"JV-202609-0143\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:16:30'),
(358, 6, 'post_central_invoice', 'finance', 135, NULL, '{\"invoice_number\":\"INV - 133\",\"total\":\"2000.00\",\"journal_entry_id\":151}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:16:30'),
(359, 6, 'create_journal', 'accounting', 152, NULL, '{\"journal_number\":\"JV-202609-0144\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:17:49'),
(360, 6, 'post_central_invoice', 'finance', 136, NULL, '{\"invoice_number\":\"INV - 134\",\"total\":\"30240.00\",\"journal_entry_id\":152}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:17:49'),
(361, 6, 'create_journal', 'accounting', 153, NULL, '{\"journal_number\":\"JV-202609-0145\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:18:36'),
(362, 6, 'post_central_invoice', 'finance', 137, NULL, '{\"invoice_number\":\"INV - 135\",\"total\":\"21600.00\",\"journal_entry_id\":153}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:18:36'),
(363, 6, 'create_journal', 'accounting', 154, NULL, '{\"journal_number\":\"JV-202609-0146\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:19:20'),
(364, 6, 'post_central_invoice', 'finance', 138, NULL, '{\"invoice_number\":\"INV - 136\",\"total\":\"10800.00\",\"journal_entry_id\":154}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:19:20');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(365, 6, 'create_journal', 'accounting', 155, NULL, '{\"journal_number\":\"JV-202609-0147\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:20:14'),
(366, 6, 'post_central_invoice', 'finance', 139, NULL, '{\"invoice_number\":\"INV - 137\",\"total\":\"4320.00\",\"journal_entry_id\":155}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 07:20:14'),
(367, 6, 'create_journal', 'accounting', 156, NULL, '{\"journal_number\":\"JV-202609-0148\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:42:28'),
(368, 6, 'post_central_invoice', 'finance', 141, NULL, '{\"invoice_number\":\"INV - 139\",\"total\":\"6480.00\",\"journal_entry_id\":156}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:42:28'),
(369, 6, 'create_journal', 'accounting', 157, NULL, '{\"journal_number\":\"JV-202609-0149\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:43:21'),
(370, 6, 'post_central_invoice', 'finance', 142, NULL, '{\"invoice_number\":\"INV - 140\",\"total\":\"5400.00\",\"journal_entry_id\":157}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:43:21'),
(371, 6, 'create_journal', 'accounting', 158, NULL, '{\"journal_number\":\"JV-202609-0150\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:44:05'),
(372, 6, 'post_central_invoice', 'finance', 143, NULL, '{\"invoice_number\":\"INV - 141\",\"total\":\"5940.00\",\"journal_entry_id\":158}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:44:05'),
(373, 6, 'create_journal', 'accounting', 159, NULL, '{\"journal_number\":\"JV-202609-0151\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:44:38'),
(374, 6, 'post_central_invoice', 'finance', 144, NULL, '{\"invoice_number\":\"INV - 142\",\"total\":\"2700.00\",\"journal_entry_id\":159}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:44:38'),
(375, 6, 'create_journal', 'accounting', 160, NULL, '{\"journal_number\":\"JV-202609-0152\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:45:29'),
(376, 6, 'post_central_invoice', 'finance', 145, NULL, '{\"invoice_number\":\"INV - 143\",\"total\":\"4800.00\",\"journal_entry_id\":160}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:45:29'),
(377, 6, 'create_journal', 'accounting', 161, NULL, '{\"journal_number\":\"JV-202609-0153\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:46:55'),
(378, 6, 'post_central_invoice', 'finance', 146, NULL, '{\"invoice_number\":\"INV - 144\",\"total\":\"3000.00\",\"journal_entry_id\":161}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:46:55'),
(379, 6, 'create_journal', 'accounting', 162, NULL, '{\"journal_number\":\"JV-202609-0154\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:47:42'),
(380, 6, 'post_central_invoice', 'finance', 147, NULL, '{\"invoice_number\":\"INV - 145\",\"total\":\"5400.00\",\"journal_entry_id\":162}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:47:42'),
(381, 6, 'create_journal', 'accounting', 163, NULL, '{\"journal_number\":\"JV-202609-0155\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:48:26'),
(382, 6, 'post_central_invoice', 'finance', 148, NULL, '{\"invoice_number\":\"INV - 146\",\"total\":\"8100.00\",\"journal_entry_id\":163}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:48:26'),
(383, 6, 'create_journal', 'accounting', 164, NULL, '{\"journal_number\":\"JV-202609-0156\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:49:42'),
(384, 6, 'post_central_invoice', 'finance', 149, NULL, '{\"invoice_number\":\"INV - 147\",\"total\":\"2400.00\",\"journal_entry_id\":164}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:49:42'),
(385, 6, 'create_journal', 'accounting', 165, NULL, '{\"journal_number\":\"JV-202609-0157\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:50:16'),
(386, 6, 'post_central_invoice', 'finance', 150, NULL, '{\"invoice_number\":\"INV - 148\",\"total\":\"11340.00\",\"journal_entry_id\":165}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:50:16'),
(387, 6, 'create_journal', 'accounting', 166, NULL, '{\"journal_number\":\"JV-202609-0158\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:51:00'),
(388, 6, 'post_central_invoice', 'finance', 151, NULL, '{\"invoice_number\":\"INV - 149\",\"total\":\"4860.00\",\"journal_entry_id\":166}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:51:00'),
(389, 6, 'create_journal', 'accounting', 167, NULL, '{\"journal_number\":\"JV-202609-0159\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:52:03'),
(390, 6, 'post_central_invoice', 'finance', 152, NULL, '{\"invoice_number\":\"INV - 150\",\"total\":\"2000.00\",\"journal_entry_id\":167}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:52:03'),
(391, 6, 'create_journal', 'accounting', 168, NULL, '{\"journal_number\":\"JV-202609-0160\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:54:09'),
(392, 6, 'post_central_invoice', 'finance', 153, NULL, '{\"invoice_number\":\"INV - 151\",\"total\":\"15120.00\",\"journal_entry_id\":168}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:54:09'),
(393, 6, 'create_journal', 'accounting', 169, NULL, '{\"journal_number\":\"JV-202609-0161\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:55:48'),
(394, 6, 'post_central_invoice', 'finance', 154, NULL, '{\"invoice_number\":\"INV - 152\",\"total\":\"2000.00\",\"journal_entry_id\":169}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:55:48'),
(395, 6, 'create_journal', 'accounting', 170, NULL, '{\"journal_number\":\"JV-202609-0162\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:56:17'),
(396, 6, 'post_central_invoice', 'finance', 155, NULL, '{\"invoice_number\":\"INV - 153\",\"total\":\"2000.00\",\"journal_entry_id\":170}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:56:17'),
(397, 6, 'create_journal', 'accounting', 171, NULL, '{\"journal_number\":\"JV-202609-0163\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:56:56'),
(398, 6, 'post_central_invoice', 'finance', 156, NULL, '{\"invoice_number\":\"INV - 154\",\"total\":\"2000.00\",\"journal_entry_id\":171}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 08:56:56'),
(399, 6, 'create_journal', 'accounting', 172, NULL, '{\"journal_number\":\"JV-202609-0164\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:00:23'),
(400, 6, 'post_central_invoice', 'finance', 157, NULL, '{\"invoice_number\":\"INV - 155\",\"total\":\"2000.00\",\"journal_entry_id\":172}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:00:23'),
(401, 6, 'create_journal', 'accounting', 173, NULL, '{\"journal_number\":\"JV-202609-0165\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:01:02'),
(402, 6, 'post_central_invoice', 'finance', 158, NULL, '{\"invoice_number\":\"INV - 156\",\"total\":\"2000.00\",\"journal_entry_id\":173}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:01:02'),
(403, 6, 'create_journal', 'accounting', 174, NULL, '{\"journal_number\":\"JV-202609-0166\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:02:02'),
(404, 6, 'post_central_invoice', 'finance', 159, NULL, '{\"invoice_number\":\"INV - 157\",\"total\":\"2000.00\",\"journal_entry_id\":174}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:02:02'),
(405, 6, 'create_journal', 'accounting', 175, NULL, '{\"journal_number\":\"JV-202609-0167\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:02:36'),
(406, 6, 'post_central_invoice', 'finance', 160, NULL, '{\"invoice_number\":\"INV - 158\",\"total\":\"2000.00\",\"journal_entry_id\":175}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:02:36'),
(407, 6, 'create_journal', 'accounting', 176, NULL, '{\"journal_number\":\"JV-202609-0168\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:03:03'),
(408, 6, 'post_central_invoice', 'finance', 161, NULL, '{\"invoice_number\":\"INV - 159\",\"total\":\"2000.00\",\"journal_entry_id\":176}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:03:03'),
(409, 6, 'create_journal', 'accounting', 177, NULL, '{\"journal_number\":\"JV-202609-0169\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:03:34'),
(410, 6, 'post_central_invoice', 'finance', 162, NULL, '{\"invoice_number\":\"INV - 160\",\"total\":\"2000.00\",\"journal_entry_id\":177}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:03:34'),
(411, 6, 'create_journal', 'accounting', 178, NULL, '{\"journal_number\":\"JV-202609-0170\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:04:00'),
(412, 6, 'post_central_invoice', 'finance', 163, NULL, '{\"invoice_number\":\"INV - 161\",\"total\":\"2000.00\",\"journal_entry_id\":178}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:04:00'),
(413, 6, 'create_journal', 'accounting', 179, NULL, '{\"journal_number\":\"JV-202609-0171\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:05:40'),
(414, 6, 'post_central_invoice', 'finance', 164, NULL, '{\"invoice_number\":\"INV - 162\",\"total\":\"2000.00\",\"journal_entry_id\":179}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:05:40'),
(415, 6, 'create_journal', 'accounting', 180, NULL, '{\"journal_number\":\"JV-202609-0172\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:06:05'),
(416, 6, 'post_central_invoice', 'finance', 165, NULL, '{\"invoice_number\":\"INV - 163\",\"total\":\"2000.00\",\"journal_entry_id\":180}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:06:05'),
(417, 6, 'create_journal', 'accounting', 181, NULL, '{\"journal_number\":\"JV-202609-0173\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:06:44'),
(418, 6, 'post_central_invoice', 'finance', 166, NULL, '{\"invoice_number\":\"INV - 164\",\"total\":\"2000.00\",\"journal_entry_id\":181}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:06:44'),
(419, 6, 'create_journal', 'accounting', 182, NULL, '{\"journal_number\":\"JV-202609-0174\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:07:10'),
(420, 6, 'post_central_invoice', 'finance', 167, NULL, '{\"invoice_number\":\"INV - 165\",\"total\":\"2000.00\",\"journal_entry_id\":182}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:07:10'),
(421, 6, 'create_journal', 'accounting', 183, NULL, '{\"journal_number\":\"JV-202609-0175\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:07:34'),
(422, 6, 'post_central_invoice', 'finance', 168, NULL, '{\"invoice_number\":\"INV - 166\",\"total\":\"2000.00\",\"journal_entry_id\":183}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:07:34'),
(423, 6, 'create_journal', 'accounting', 184, NULL, '{\"journal_number\":\"JV-202609-0176\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:08:01'),
(424, 6, 'post_central_invoice', 'finance', 169, NULL, '{\"invoice_number\":\"INV - 167\",\"total\":\"2000.00\",\"journal_entry_id\":184}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:08:01'),
(425, 6, 'create_journal', 'accounting', 185, NULL, '{\"journal_number\":\"JV-202609-0177\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:08:27'),
(426, 6, 'post_central_invoice', 'finance', 170, NULL, '{\"invoice_number\":\"INV - 168\",\"total\":\"2000.00\",\"journal_entry_id\":185}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:08:27'),
(427, 6, 'create_journal', 'accounting', 186, NULL, '{\"journal_number\":\"JV-202609-0178\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:08:53'),
(428, 6, 'post_central_invoice', 'finance', 171, NULL, '{\"invoice_number\":\"INV - 169\",\"total\":\"2000.00\",\"journal_entry_id\":186}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:08:53'),
(429, 6, 'create_journal', 'accounting', 187, NULL, '{\"journal_number\":\"JV-202609-0179\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:09:16'),
(430, 6, 'post_central_invoice', 'finance', 172, NULL, '{\"invoice_number\":\"INV - 170\",\"total\":\"2000.00\",\"journal_entry_id\":187}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:09:16'),
(431, 6, 'create_journal', 'accounting', 188, NULL, '{\"journal_number\":\"JV-202609-0180\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:09:58'),
(432, 6, 'post_central_invoice', 'finance', 173, NULL, '{\"invoice_number\":\"INV - 171\",\"total\":\"4320.00\",\"journal_entry_id\":188}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:09:58'),
(433, 6, 'create_journal', 'accounting', 189, NULL, '{\"journal_number\":\"JV-202609-0181\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:10:28'),
(434, 6, 'post_central_invoice', 'finance', 174, NULL, '{\"invoice_number\":\"INV - 172\",\"total\":\"4320.00\",\"journal_entry_id\":189}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:10:28'),
(435, 6, 'create_journal', 'accounting', 190, NULL, '{\"journal_number\":\"JV-202609-0182\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:12:06'),
(436, 6, 'post_central_invoice', 'finance', 176, NULL, '{\"invoice_number\":\"INV - 174\",\"total\":\"1800.00\",\"journal_entry_id\":190}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:12:06'),
(437, 6, 'create_journal', 'accounting', 191, NULL, '{\"journal_number\":\"JV-202609-0183\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:13:41'),
(438, 6, 'post_central_invoice', 'finance', 177, NULL, '{\"invoice_number\":\"INV - 175\",\"total\":\"6480.00\",\"journal_entry_id\":191}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:13:41'),
(439, 6, 'create_journal', 'accounting', 192, NULL, '{\"journal_number\":\"JV-202609-0184\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:17:30'),
(440, 6, 'post_central_invoice', 'finance', 178, NULL, '{\"invoice_number\":\"INV - 176\",\"total\":\"6480.00\",\"journal_entry_id\":192}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:17:30'),
(441, 6, 'create_journal', 'accounting', 193, NULL, '{\"journal_number\":\"JV-202609-0185\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:18:14'),
(442, 6, 'post_central_invoice', 'finance', 180, NULL, '{\"invoice_number\":\"INV - 178\",\"total\":\"2700.00\",\"journal_entry_id\":193}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:18:14'),
(443, 6, 'create_journal', 'accounting', 194, NULL, '{\"journal_number\":\"JV-202609-0186\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:18:45'),
(444, 6, 'post_central_invoice', 'finance', 181, NULL, '{\"invoice_number\":\"INV - 179\",\"total\":\"3780.00\",\"journal_entry_id\":194}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:18:45'),
(445, 6, 'create_journal', 'accounting', 195, NULL, '{\"journal_number\":\"JV-202609-0187\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:19:12'),
(446, 6, 'post_central_invoice', 'finance', 182, NULL, '{\"invoice_number\":\"INV - 180\",\"total\":\"27000.00\",\"journal_entry_id\":195}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:19:12'),
(447, 6, 'create_journal', 'accounting', 196, NULL, '{\"journal_number\":\"JV-202609-0188\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:19:41'),
(448, 6, 'post_central_invoice', 'finance', 183, NULL, '{\"invoice_number\":\"INV - 181\",\"total\":\"1080.00\",\"journal_entry_id\":196}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:19:41'),
(449, 6, 'create_journal', 'accounting', 197, NULL, '{\"journal_number\":\"JV-202609-0189\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:20:10'),
(450, 6, 'post_central_invoice', 'finance', 184, NULL, '{\"invoice_number\":\"INV - 182\",\"total\":\"5400.00\",\"journal_entry_id\":197}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:20:10'),
(451, 6, 'create_journal', 'accounting', 198, NULL, '{\"journal_number\":\"JV-202609-0190\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:21:11'),
(452, 6, 'post_central_invoice', 'finance', 185, NULL, '{\"invoice_number\":\"INV - 183\",\"total\":\"9000.00\",\"journal_entry_id\":198}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:21:11'),
(453, 6, 'create_journal', 'accounting', 199, NULL, '{\"journal_number\":\"JV-202609-0191\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:21:43'),
(454, 6, 'post_central_invoice', 'finance', 186, NULL, '{\"invoice_number\":\"INV - 184\",\"total\":\"10800.00\",\"journal_entry_id\":199}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:21:43'),
(455, 6, 'create_journal', 'accounting', 200, NULL, '{\"journal_number\":\"JV-202609-0192\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:23:06'),
(456, 6, 'post_central_invoice', 'finance', 188, NULL, '{\"invoice_number\":\"INV - 186\",\"total\":\"2000.00\",\"journal_entry_id\":200}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:23:06'),
(457, 6, 'create_journal', 'accounting', 201, NULL, '{\"journal_number\":\"JV-202609-0193\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:23:34'),
(458, 6, 'post_central_invoice', 'finance', 189, NULL, '{\"invoice_number\":\"INV - 187\",\"total\":\"2000.00\",\"journal_entry_id\":201}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:23:34'),
(459, 6, 'create_journal', 'accounting', 202, NULL, '{\"journal_number\":\"JV-202609-0194\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:24:08'),
(460, 6, 'post_central_invoice', 'finance', 190, NULL, '{\"invoice_number\":\"INV - 188\",\"total\":\"12250.00\",\"journal_entry_id\":202}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:24:08'),
(461, 6, 'create_journal', 'accounting', 203, NULL, '{\"journal_number\":\"JV-202609-0195\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:24:45'),
(462, 6, 'post_central_invoice', 'finance', 191, NULL, '{\"invoice_number\":\"INV - 189\",\"total\":\"9000.00\",\"journal_entry_id\":203}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:24:45'),
(463, 6, 'create_journal', 'accounting', 204, NULL, '{\"journal_number\":\"JV-202609-0196\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:25:32'),
(464, 6, 'post_central_invoice', 'finance', 192, NULL, '{\"invoice_number\":\"INV - 190\",\"total\":\"12000.00\",\"journal_entry_id\":204}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:25:32'),
(465, 6, 'create_journal', 'accounting', 205, NULL, '{\"journal_number\":\"JV-202609-0197\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:26:07'),
(466, 6, 'post_central_invoice', 'finance', 193, NULL, '{\"invoice_number\":\"INV - 191\",\"total\":\"3600.00\",\"journal_entry_id\":205}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:26:07'),
(467, 6, 'create_journal', 'accounting', 206, NULL, '{\"journal_number\":\"JV-202609-0198\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:26:34'),
(468, 6, 'post_central_invoice', 'finance', 194, NULL, '{\"invoice_number\":\"INV - 192\",\"total\":\"2400.00\",\"journal_entry_id\":206}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:26:34'),
(469, 6, 'create_journal', 'accounting', 207, NULL, '{\"journal_number\":\"JV-202609-0199\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:27:02'),
(470, 6, 'post_central_invoice', 'finance', 195, NULL, '{\"invoice_number\":\"INV - 193\",\"total\":\"7500.00\",\"journal_entry_id\":207}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:27:02'),
(471, 6, 'create_journal', 'accounting', 208, NULL, '{\"journal_number\":\"JV-202609-0200\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:27:55'),
(472, 6, 'post_central_invoice', 'finance', 196, NULL, '{\"invoice_number\":\"INV - 194\",\"total\":\"3000.00\",\"journal_entry_id\":208}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:27:55'),
(473, 6, 'create_journal', 'accounting', 209, NULL, '{\"journal_number\":\"JV-202609-0201\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:28:41'),
(474, 6, 'post_central_invoice', 'finance', 197, NULL, '{\"invoice_number\":\"INV - 195\",\"total\":\"7200.00\",\"journal_entry_id\":209}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:28:41'),
(475, 6, 'create_journal', 'accounting', 210, NULL, '{\"journal_number\":\"JV-202609-0202\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:29:34'),
(476, 6, 'post_central_invoice', 'finance', 198, NULL, '{\"invoice_number\":\"INV - 196\",\"total\":\"3600.00\",\"journal_entry_id\":210}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:29:34'),
(477, 6, 'create_journal', 'accounting', 211, NULL, '{\"journal_number\":\"JV-202609-0203\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:30:15'),
(478, 6, 'post_central_invoice', 'finance', 199, NULL, '{\"invoice_number\":\"INV - 197\",\"total\":\"4800.00\",\"journal_entry_id\":211}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:30:15'),
(479, 6, 'create_journal', 'accounting', 212, NULL, '{\"journal_number\":\"JV-202609-0204\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:31:00'),
(480, 6, 'post_central_invoice', 'finance', 200, NULL, '{\"invoice_number\":\"INV - 198\",\"total\":\"3600.00\",\"journal_entry_id\":212}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:31:00'),
(481, 6, 'create_journal', 'accounting', 213, NULL, '{\"journal_number\":\"JV-202609-0205\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:31:44'),
(482, 6, 'post_central_invoice', 'finance', 201, NULL, '{\"invoice_number\":\"INV - 199\",\"total\":\"3600.00\",\"journal_entry_id\":213}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:31:44'),
(483, 6, 'create_journal', 'accounting', 214, NULL, '{\"journal_number\":\"JV-202609-0206\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:32:24'),
(484, 6, 'post_central_invoice', 'finance', 202, NULL, '{\"invoice_number\":\"INV - 200\",\"total\":\"6000.00\",\"journal_entry_id\":214}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 09:32:24'),
(485, 6, 'create_journal', 'accounting', 215, NULL, '{\"journal_number\":\"JV-202609-0207\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 10:29:20'),
(486, 6, 'post_central_invoice', 'finance', 203, NULL, '{\"invoice_number\":\"INV - 201\",\"total\":\"2000.00\",\"journal_entry_id\":215}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 10:29:20'),
(487, 6, 'create_journal', 'accounting', 216, NULL, '{\"journal_number\":\"JV-202609-0208\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 10:44:45'),
(488, 6, 'post_central_invoice', 'finance', 204, NULL, '{\"invoice_number\":\"INV - 202\",\"total\":\"2000.00\",\"journal_entry_id\":216}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 10:44:45'),
(489, 6, 'create_journal', 'accounting', 217, NULL, '{\"journal_number\":\"JV-202609-0209\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 10:46:09'),
(490, 6, 'post_central_invoice', 'finance', 205, NULL, '{\"invoice_number\":\"INV - 203\",\"total\":\"2000.00\",\"journal_entry_id\":217}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 10:46:09'),
(491, 6, 'create_journal', 'accounting', 218, NULL, '{\"journal_number\":\"JV-202609-0210\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 10:57:40'),
(492, 6, 'post_central_invoice', 'finance', 206, NULL, '{\"invoice_number\":\"INV - 204\",\"total\":\"10070.00\",\"journal_entry_id\":218}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 10:57:40'),
(493, 6, 'create_journal', 'accounting', 219, NULL, '{\"journal_number\":\"JV-202609-0211\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:38:48'),
(494, 6, 'post_central_invoice', 'finance', 207, NULL, '{\"invoice_number\":\"INV - 205\",\"total\":\"17120.00\",\"journal_entry_id\":219}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:38:48'),
(495, 6, 'create_journal', 'accounting', 220, NULL, '{\"journal_number\":\"JV-202609-0212\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:41:04'),
(496, 6, 'post_central_invoice', 'finance', 208, NULL, '{\"invoice_number\":\"INV - 206\",\"total\":\"11480.00\",\"journal_entry_id\":220}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:41:04'),
(497, 6, 'create_journal', 'accounting', 221, NULL, '{\"journal_number\":\"JV-202609-0213\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:45:54'),
(498, 6, 'post_central_invoice', 'finance', 209, NULL, '{\"invoice_number\":\"INV - 207\",\"total\":\"15850.00\",\"journal_entry_id\":221}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:45:54'),
(499, 6, 'create_journal', 'accounting', 222, NULL, '{\"journal_number\":\"JV-202609-0214\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:46:32'),
(500, 6, 'post_central_invoice', 'finance', 210, NULL, '{\"invoice_number\":\"INV - 208\",\"total\":\"15850.00\",\"journal_entry_id\":222}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:46:32'),
(501, 6, 'create_journal', 'accounting', 223, NULL, '{\"journal_number\":\"JV-202609-0215\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:48:59'),
(502, 6, 'post_central_invoice', 'finance', 211, NULL, '{\"invoice_number\":\"INV - 209\",\"total\":\"5400.00\",\"journal_entry_id\":223}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:48:59'),
(503, 6, 'create_journal', 'accounting', 224, NULL, '{\"journal_number\":\"JV-202609-0216\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:52:11'),
(504, 6, 'post_central_invoice', 'finance', 212, NULL, '{\"invoice_number\":\"INV - 210\",\"total\":\"12420.00\",\"journal_entry_id\":224}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:52:11'),
(505, 6, 'create_journal', 'accounting', 225, NULL, '{\"journal_number\":\"JV-202609-0217\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:52:46'),
(506, 6, 'post_central_invoice', 'finance', 213, NULL, '{\"invoice_number\":\"INV - 211\",\"total\":\"4320.00\",\"journal_entry_id\":225}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:52:46'),
(507, 6, 'create_journal', 'accounting', 226, NULL, '{\"journal_number\":\"JV-202609-0218\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:53:15'),
(508, 6, 'post_central_invoice', 'finance', 214, NULL, '{\"invoice_number\":\"INV - 212\",\"total\":\"7560.00\",\"journal_entry_id\":226}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:53:15'),
(509, 6, 'create_journal', 'accounting', 227, NULL, '{\"journal_number\":\"JV-202609-0219\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:53:54'),
(510, 6, 'post_central_invoice', 'finance', 215, NULL, '{\"invoice_number\":\"INV - 213\",\"total\":\"8400.00\",\"journal_entry_id\":227}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:53:54'),
(511, 6, 'create_journal', 'accounting', 228, NULL, '{\"journal_number\":\"JV-202609-0220\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:55:21'),
(512, 6, 'post_central_invoice', 'finance', 216, NULL, '{\"invoice_number\":\"INV - 214\",\"total\":\"8100.00\",\"journal_entry_id\":228}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:55:21'),
(513, 6, 'create_journal', 'accounting', 229, NULL, '{\"journal_number\":\"JV-202609-0221\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:56:04'),
(514, 6, 'post_central_invoice', 'finance', 217, NULL, '{\"invoice_number\":\"INV - 215\",\"total\":\"2400.00\",\"journal_entry_id\":229}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:56:04'),
(515, 6, 'create_journal', 'accounting', 230, NULL, '{\"journal_number\":\"JV-202609-0222\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:57:08'),
(516, 6, 'post_central_invoice', 'finance', 216, NULL, '{\"invoice_number\":\"INV - 214\",\"total\":\"8640.00\",\"journal_entry_id\":230}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:57:08'),
(517, 6, 'create_journal', 'accounting', 231, NULL, '{\"journal_number\":\"JV-202609-0223\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:57:42'),
(518, 6, 'post_central_invoice', 'finance', 217, NULL, '{\"invoice_number\":\"INV - 215\",\"total\":\"8100.00\",\"journal_entry_id\":231}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:57:42'),
(519, 6, 'create_journal', 'accounting', 232, NULL, '{\"journal_number\":\"JV-202609-0224\",\"status\":\"posted\"}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:58:33'),
(520, 6, 'post_central_invoice', 'finance', 218, NULL, '{\"invoice_number\":\"INV - 216\",\"total\":\"2400.00\",\"journal_entry_id\":232}', '111.223.179.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 11:58:33'),
(521, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-26 09:46:45'),
(522, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '123.231.85.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-27 03:37:09'),
(523, 6, 'create_journal', 'accounting', 233, NULL, '{\"journal_number\":\"JV-202609-0225\",\"status\":\"posted\"}', '123.231.85.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-27 03:55:06'),
(524, 6, 'post_central_invoice', 'finance', 219, NULL, '{\"invoice_number\":\"INV - 217\",\"total\":\"5400.00\",\"journal_entry_id\":233}', '123.231.85.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-27 03:55:06'),
(525, 6, 'create_journal', 'accounting', 234, NULL, '{\"journal_number\":\"JV-202609-0226\",\"status\":\"posted\"}', '123.231.85.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-27 03:59:16'),
(526, 6, 'post_central_invoice', 'finance', 220, NULL, '{\"invoice_number\":\"INV - 218\",\"total\":\"7020.00\",\"journal_entry_id\":234}', '123.231.85.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-27 03:59:16'),
(527, 6, 'create_journal', 'accounting', 235, NULL, '{\"journal_number\":\"JV-202609-0227\",\"status\":\"posted\"}', '123.231.85.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-27 03:59:54'),
(528, 6, 'post_central_invoice', 'finance', 221, NULL, '{\"invoice_number\":\"INV - 219\",\"total\":\"13500.00\",\"journal_entry_id\":235}', '123.231.85.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-27 03:59:54'),
(529, 6, 'create_journal', 'accounting', 236, NULL, '{\"journal_number\":\"JV-202609-0228\",\"status\":\"posted\"}', '123.231.85.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-27 04:51:12'),
(530, 6, 'post_central_invoice', 'finance', 222, NULL, '{\"invoice_number\":\"INV - 220\",\"total\":\"3240.00\",\"journal_entry_id\":236}', '123.231.85.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-27 04:51:12'),
(531, 6, 'create_journal', 'accounting', 237, NULL, '{\"journal_number\":\"JV-202609-0229\",\"status\":\"posted\"}', '123.231.85.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-27 04:52:01'),
(532, 6, 'post_central_invoice', 'finance', 223, NULL, '{\"invoice_number\":\"INV - 221\",\"total\":\"1620.00\",\"journal_entry_id\":237}', '123.231.85.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-27 04:52:01'),
(533, 6, 'create_journal', 'accounting', 238, NULL, '{\"journal_number\":\"JV-202609-0230\",\"status\":\"posted\"}', '123.231.85.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-27 04:52:44'),
(534, 6, 'post_central_invoice', 'finance', 224, NULL, '{\"invoice_number\":\"INV - 222\",\"total\":\"6480.00\",\"journal_entry_id\":238}', '123.231.85.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-27 04:52:44'),
(535, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-27 09:43:06'),
(536, 6, 'login', 'auth', 6, NULL, '{\"username\":\"nethmaj\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 03:59:01'),
(537, 6, 'create_journal', 'accounting', 239, NULL, '{\"journal_number\":\"JV-202609-0231\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:00:36'),
(538, 6, 'post_central_invoice', 'finance', 225, NULL, '{\"invoice_number\":\"INV - 223\",\"total\":\"8100.00\",\"journal_entry_id\":239}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:00:36'),
(539, 6, 'create_journal', 'accounting', 240, NULL, '{\"journal_number\":\"JV-202609-0232\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:01:13'),
(540, 6, 'post_central_invoice', 'finance', 226, NULL, '{\"invoice_number\":\"INV - 224\",\"total\":\"5400.00\",\"journal_entry_id\":240}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:01:13');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(541, 6, 'create_journal', 'accounting', 241, NULL, '{\"journal_number\":\"JV-202609-0233\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:02:44'),
(542, 6, 'post_central_invoice', 'finance', 227, NULL, '{\"invoice_number\":\"INV - 225\",\"total\":\"4860.00\",\"journal_entry_id\":241}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:02:44'),
(543, 6, 'create_journal', 'accounting', 242, NULL, '{\"journal_number\":\"JV-202609-0234\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:03:13'),
(544, 6, 'post_central_invoice', 'finance', 228, NULL, '{\"invoice_number\":\"INV - 226\",\"total\":\"2160.00\",\"journal_entry_id\":242}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:03:13'),
(545, 6, 'create_journal', 'accounting', 243, NULL, '{\"journal_number\":\"JV-202609-0235\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:03:44'),
(546, 6, 'post_central_invoice', 'finance', 229, NULL, '{\"invoice_number\":\"INV - 227\",\"total\":\"4320.00\",\"journal_entry_id\":243}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:03:44'),
(547, 6, 'create_journal', 'accounting', 244, NULL, '{\"journal_number\":\"JV-202609-0236\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:06:20'),
(548, 6, 'post_central_invoice', 'finance', 230, NULL, '{\"invoice_number\":\"INV - 228\",\"total\":\"6480.00\",\"journal_entry_id\":244}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:06:20'),
(549, 6, 'create_journal', 'accounting', 245, NULL, '{\"journal_number\":\"JV-202609-0237\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:07:09'),
(550, 6, 'post_central_invoice', 'finance', 231, NULL, '{\"invoice_number\":\"INV - 229\",\"total\":\"1200.00\",\"journal_entry_id\":245}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:07:09'),
(551, 6, 'create_journal', 'accounting', 246, NULL, '{\"journal_number\":\"JV-202609-0238\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:07:42'),
(552, 6, 'post_central_invoice', 'finance', 232, NULL, '{\"invoice_number\":\"INV - 230\",\"total\":\"3780.00\",\"journal_entry_id\":246}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:07:42'),
(553, 6, 'create_journal', 'accounting', 247, NULL, '{\"journal_number\":\"JV-202609-0239\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:08:19'),
(554, 6, 'post_central_invoice', 'finance', 233, NULL, '{\"invoice_number\":\"INV - 231\",\"total\":\"14580.00\",\"journal_entry_id\":247}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:08:19'),
(555, 6, 'create_journal', 'accounting', 248, NULL, '{\"journal_number\":\"JV-202609-0240\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:09:02'),
(556, 6, 'post_central_invoice', 'finance', 234, NULL, '{\"invoice_number\":\"INV - 232\",\"total\":\"6000.00\",\"journal_entry_id\":248}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:09:02'),
(557, 6, 'create_journal', 'accounting', 249, NULL, '{\"journal_number\":\"JV-202609-0241\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:09:39'),
(558, 6, 'post_central_invoice', 'finance', 235, NULL, '{\"invoice_number\":\"INV - 233\",\"total\":\"6000.00\",\"journal_entry_id\":249}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:09:39'),
(559, 6, 'create_journal', 'accounting', 250, NULL, '{\"journal_number\":\"JV-202609-0242\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:10:37'),
(560, 6, 'post_central_invoice', 'finance', 236, NULL, '{\"invoice_number\":\"INV - 234\",\"total\":\"6480.00\",\"journal_entry_id\":250}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:10:37'),
(561, 6, 'create_journal', 'accounting', 251, NULL, '{\"journal_number\":\"JV-202609-0243\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:11:33'),
(562, 6, 'post_central_invoice', 'finance', 239, NULL, '{\"invoice_number\":\"INV - 237\",\"total\":\"3240.00\",\"journal_entry_id\":251}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:11:33'),
(563, 6, 'create_journal', 'accounting', 252, NULL, '{\"journal_number\":\"JV-202609-0244\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:12:36'),
(564, 6, 'post_central_invoice', 'finance', 240, NULL, '{\"invoice_number\":\"INV - 238\",\"total\":\"6480.00\",\"journal_entry_id\":252}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:12:36'),
(565, 6, 'create_journal', 'accounting', 253, NULL, '{\"journal_number\":\"JV-202609-0245\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:13:18'),
(566, 6, 'post_central_invoice', 'finance', 242, NULL, '{\"invoice_number\":\"INV - 240\",\"total\":\"4320.00\",\"journal_entry_id\":253}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:13:18'),
(567, 6, 'create_journal', 'accounting', 254, NULL, '{\"journal_number\":\"JV-202609-0246\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:13:45'),
(568, 6, 'post_central_invoice', 'finance', 243, NULL, '{\"invoice_number\":\"INV - 241\",\"total\":\"8640.00\",\"journal_entry_id\":254}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:13:45'),
(569, 6, 'create_journal', 'accounting', 255, NULL, '{\"journal_number\":\"JV-202609-0247\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:14:26'),
(570, 6, 'post_central_invoice', 'finance', 244, NULL, '{\"invoice_number\":\"INV - 242\",\"total\":\"19440.00\",\"journal_entry_id\":255}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:14:26'),
(571, 6, 'create_journal', 'accounting', 256, NULL, '{\"journal_number\":\"JV-202609-0248\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:15:03'),
(572, 6, 'post_central_invoice', 'finance', 245, NULL, '{\"invoice_number\":\"INV - 243\",\"total\":\"28080.00\",\"journal_entry_id\":256}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:15:03'),
(573, 6, 'create_journal', 'accounting', 257, NULL, '{\"journal_number\":\"JV-202609-0249\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:15:32'),
(574, 6, 'post_central_invoice', 'finance', 246, NULL, '{\"invoice_number\":\"INV - 244\",\"total\":\"2400.00\",\"journal_entry_id\":257}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:15:32'),
(575, 6, 'create_journal', 'accounting', 258, NULL, '{\"journal_number\":\"JV-202609-0250\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:16:08'),
(576, 6, 'post_central_invoice', 'finance', 247, NULL, '{\"invoice_number\":\"INV - 245\",\"total\":\"2400.00\",\"journal_entry_id\":258}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:16:08'),
(577, 6, 'create_journal', 'accounting', 259, NULL, '{\"journal_number\":\"JV-202609-0251\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:16:46'),
(578, 6, 'post_central_invoice', 'finance', 248, NULL, '{\"invoice_number\":\"INV - 246\",\"total\":\"12000.00\",\"journal_entry_id\":259}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:16:46'),
(579, 6, 'create_journal', 'accounting', 260, NULL, '{\"journal_number\":\"JV-202609-0252\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:17:19'),
(580, 6, 'post_central_invoice', 'finance', 249, NULL, '{\"invoice_number\":\"INV - 247\",\"total\":\"16800.00\",\"journal_entry_id\":260}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:17:19'),
(581, 6, 'create_journal', 'accounting', 261, NULL, '{\"journal_number\":\"JV-202609-0253\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:17:51'),
(582, 6, 'post_central_invoice', 'finance', 250, NULL, '{\"invoice_number\":\"INV - 248\",\"total\":\"12000.00\",\"journal_entry_id\":261}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:17:51'),
(583, 6, 'create_journal', 'accounting', 262, NULL, '{\"journal_number\":\"JV-202609-0254\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:18:21'),
(584, 6, 'post_central_invoice', 'finance', 251, NULL, '{\"invoice_number\":\"INV - 249\",\"total\":\"4800.00\",\"journal_entry_id\":262}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:18:21'),
(585, 6, 'create_journal', 'accounting', 263, NULL, '{\"journal_number\":\"JV-202609-0255\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:19:14'),
(586, 6, 'post_central_invoice', 'finance', 252, NULL, '{\"invoice_number\":\"INV - 250\",\"total\":\"3600.00\",\"journal_entry_id\":263}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:19:14'),
(587, 6, 'create_journal', 'accounting', 264, NULL, '{\"journal_number\":\"JV-202609-0256\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:20:10'),
(588, 6, 'post_central_invoice', 'finance', 254, NULL, '{\"invoice_number\":\"INV - 252\",\"total\":\"10800.00\",\"journal_entry_id\":264}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:20:10'),
(589, 6, 'create_journal', 'accounting', 265, NULL, '{\"journal_number\":\"JV-202609-0257\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:20:45'),
(590, 6, 'post_central_invoice', 'finance', 255, NULL, '{\"invoice_number\":\"INV - 253\",\"total\":\"6480.00\",\"journal_entry_id\":265}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:20:45'),
(591, 6, 'create_journal', 'accounting', 266, NULL, '{\"journal_number\":\"JV-202609-0258\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:21:31'),
(592, 6, 'post_central_invoice', 'finance', 256, NULL, '{\"invoice_number\":\"INV - 254\",\"total\":\"10800.00\",\"journal_entry_id\":266}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:21:31'),
(593, 6, 'create_journal', 'accounting', 267, NULL, '{\"journal_number\":\"JV-202609-0259\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:23:51'),
(594, 6, 'post_central_invoice', 'finance', 257, NULL, '{\"invoice_number\":\"INV - 255\",\"total\":\"7560.00\",\"journal_entry_id\":267}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:23:51'),
(595, 6, 'create_journal', 'accounting', 268, NULL, '{\"journal_number\":\"JV-202609-0260\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:24:34'),
(596, 6, 'post_central_invoice', 'finance', 259, NULL, '{\"invoice_number\":\"INV - 257\",\"total\":\"10800.00\",\"journal_entry_id\":268}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:24:34'),
(597, 6, 'create_journal', 'accounting', 269, NULL, '{\"journal_number\":\"JV-202609-0261\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:24:55'),
(598, 6, 'post_central_invoice', 'finance', 260, NULL, '{\"invoice_number\":\"INV - 258\",\"total\":\"5400.00\",\"journal_entry_id\":269}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:24:55'),
(599, 6, 'create_journal', 'accounting', 270, NULL, '{\"journal_number\":\"JV-202609-0262\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:25:25'),
(600, 6, 'post_central_invoice', 'finance', 261, NULL, '{\"invoice_number\":\"INV - 259\",\"total\":\"30240.00\",\"journal_entry_id\":270}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:25:25'),
(601, 6, 'create_journal', 'accounting', 271, NULL, '{\"journal_number\":\"JV-202609-0263\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:26:12'),
(602, 6, 'post_central_invoice', 'finance', 263, NULL, '{\"invoice_number\":\"INV - 261\",\"total\":\"2000.00\",\"journal_entry_id\":271}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:26:12'),
(603, 6, 'create_journal', 'accounting', 272, NULL, '{\"journal_number\":\"JV-202609-0264\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:26:46'),
(604, 6, 'post_central_invoice', 'finance', 264, NULL, '{\"invoice_number\":\"INV - 262\",\"total\":\"2000.00\",\"journal_entry_id\":272}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:26:46'),
(605, 6, 'create_journal', 'accounting', 273, NULL, '{\"journal_number\":\"JV-202609-0265\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:34:38'),
(606, 6, 'post_central_invoice', 'finance', 265, NULL, '{\"invoice_number\":\"INV - 263\",\"total\":\"2000.00\",\"journal_entry_id\":273}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:34:38'),
(607, 6, 'create_journal', 'accounting', 274, NULL, '{\"journal_number\":\"JV-202609-0266\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:35:07'),
(608, 6, 'post_central_invoice', 'finance', 266, NULL, '{\"invoice_number\":\"INV - 264\",\"total\":\"2000.00\",\"journal_entry_id\":274}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:35:07'),
(609, 6, 'create_journal', 'accounting', 275, NULL, '{\"journal_number\":\"JV-202609-0267\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:35:32'),
(610, 6, 'post_central_invoice', 'finance', 267, NULL, '{\"invoice_number\":\"INV - 265\",\"total\":\"2000.00\",\"journal_entry_id\":275}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:35:32'),
(611, 6, 'create_journal', 'accounting', 276, NULL, '{\"journal_number\":\"JV-202609-0268\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:36:03'),
(612, 6, 'post_central_invoice', 'finance', 268, NULL, '{\"invoice_number\":\"INV - 266\",\"total\":\"2000.00\",\"journal_entry_id\":276}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:36:03'),
(613, 6, 'create_journal', 'accounting', 277, NULL, '{\"journal_number\":\"JV-202609-0269\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:36:35'),
(614, 6, 'post_central_invoice', 'finance', 269, NULL, '{\"invoice_number\":\"INV - 267\",\"total\":\"2000.00\",\"journal_entry_id\":277}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:36:35'),
(615, 6, 'create_journal', 'accounting', 278, NULL, '{\"journal_number\":\"JV-202609-0270\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:37:11'),
(616, 6, 'post_central_invoice', 'finance', 270, NULL, '{\"invoice_number\":\"INV - 268\",\"total\":\"2000.00\",\"journal_entry_id\":278}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:37:11'),
(617, 6, 'create_journal', 'accounting', 279, NULL, '{\"journal_number\":\"JV-202609-0271\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:37:40'),
(618, 6, 'post_central_invoice', 'finance', 271, NULL, '{\"invoice_number\":\"INV - 269\",\"total\":\"2000.00\",\"journal_entry_id\":279}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:37:40'),
(619, 6, 'create_journal', 'accounting', 280, NULL, '{\"journal_number\":\"JV-202609-0272\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:38:08'),
(620, 6, 'post_central_invoice', 'finance', 272, NULL, '{\"invoice_number\":\"INV - 270\",\"total\":\"2000.00\",\"journal_entry_id\":280}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:38:08'),
(621, 6, 'create_journal', 'accounting', 281, NULL, '{\"journal_number\":\"JV-202609-0273\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:38:47'),
(622, 6, 'post_central_invoice', 'finance', 273, NULL, '{\"invoice_number\":\"INV - 271\",\"total\":\"2000.00\",\"journal_entry_id\":281}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:38:47'),
(623, 6, 'create_journal', 'accounting', 282, NULL, '{\"journal_number\":\"JV-202609-0274\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:39:12'),
(624, 6, 'post_central_invoice', 'finance', 274, NULL, '{\"invoice_number\":\"INV - 272\",\"total\":\"2000.00\",\"journal_entry_id\":282}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:39:12'),
(625, 6, 'create_journal', 'accounting', 283, NULL, '{\"journal_number\":\"JV-202609-0275\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:39:57'),
(626, 6, 'post_central_invoice', 'finance', 275, NULL, '{\"invoice_number\":\"INV - 273\",\"total\":\"52300.00\",\"journal_entry_id\":283}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:39:57'),
(627, 6, 'create_journal', 'accounting', 284, NULL, '{\"journal_number\":\"JV-202609-0275\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:40:10'),
(628, 6, 'post_central_invoice', 'finance', 275, NULL, '{\"invoice_number\":\"INV - 273\",\"total\":\"52380.00\",\"journal_entry_id\":284}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:40:10'),
(629, 6, 'create_journal', 'accounting', 285, NULL, '{\"journal_number\":\"JV-202609-0276\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:40:29'),
(630, 6, 'post_central_invoice', 'finance', 276, NULL, '{\"invoice_number\":\"INV - 274\",\"total\":\"4320.00\",\"journal_entry_id\":285}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:40:29'),
(631, 6, 'create_journal', 'accounting', 286, NULL, '{\"journal_number\":\"JV-202609-0277\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:40:52'),
(632, 6, 'post_central_invoice', 'finance', 277, NULL, '{\"invoice_number\":\"INV - 275\",\"total\":\"2000.00\",\"journal_entry_id\":286}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:40:52'),
(633, 6, 'create_journal', 'accounting', 287, NULL, '{\"journal_number\":\"JV-202609-0278\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:41:18'),
(634, 6, 'post_central_invoice', 'finance', 278, NULL, '{\"invoice_number\":\"INV - 276\",\"total\":\"2000.00\",\"journal_entry_id\":287}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:41:18'),
(635, 6, 'create_journal', 'accounting', 288, NULL, '{\"journal_number\":\"JV-202609-0279\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:41:46'),
(636, 6, 'post_central_invoice', 'finance', 279, NULL, '{\"invoice_number\":\"INV - 277\",\"total\":\"2000.00\",\"journal_entry_id\":288}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:41:46'),
(637, 6, 'create_journal', 'accounting', 289, NULL, '{\"journal_number\":\"JV-202609-0280\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:42:17'),
(638, 6, 'post_central_invoice', 'finance', 280, NULL, '{\"invoice_number\":\"INV - 278\",\"total\":\"2000.00\",\"journal_entry_id\":289}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:42:17'),
(639, 6, 'create_journal', 'accounting', 290, NULL, '{\"journal_number\":\"JV-202609-0281\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:42:42'),
(640, 6, 'post_central_invoice', 'finance', 281, NULL, '{\"invoice_number\":\"INV - 279\",\"total\":\"2000.00\",\"journal_entry_id\":290}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:42:42'),
(641, 6, 'create_journal', 'accounting', 291, NULL, '{\"journal_number\":\"JV-202609-0282\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:43:31'),
(642, 6, 'post_central_invoice', 'finance', 282, NULL, '{\"invoice_number\":\"INV - 280\",\"total\":\"2000.00\",\"journal_entry_id\":291}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:43:31'),
(643, 6, 'create_journal', 'accounting', 292, NULL, '{\"journal_number\":\"JV-202609-0283\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:44:00'),
(644, 6, 'post_central_invoice', 'finance', 283, NULL, '{\"invoice_number\":\"INV - 281\",\"total\":\"2000.00\",\"journal_entry_id\":292}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:44:00'),
(645, 6, 'create_journal', 'accounting', 293, NULL, '{\"journal_number\":\"JV-202609-0284\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:44:29'),
(646, 6, 'post_central_invoice', 'finance', 284, NULL, '{\"invoice_number\":\"INV - 282\",\"total\":\"2000.00\",\"journal_entry_id\":293}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:44:29'),
(647, 6, 'create_journal', 'accounting', 294, NULL, '{\"journal_number\":\"JV-202609-0285\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:44:54'),
(648, 6, 'post_central_invoice', 'finance', 285, NULL, '{\"invoice_number\":\"INV - 283\",\"total\":\"4320.00\",\"journal_entry_id\":294}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:44:54'),
(649, 6, 'create_journal', 'accounting', 295, NULL, '{\"journal_number\":\"JV-202609-0286\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:45:19'),
(650, 6, 'post_central_invoice', 'finance', 286, NULL, '{\"invoice_number\":\"INV - 284\",\"total\":\"7020.00\",\"journal_entry_id\":295}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:45:19'),
(651, 6, 'create_journal', 'accounting', 296, NULL, '{\"journal_number\":\"JV-202609-0287\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:45:44'),
(652, 6, 'post_central_invoice', 'finance', 287, NULL, '{\"invoice_number\":\"INV - 285\",\"total\":\"2700.00\",\"journal_entry_id\":296}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:45:44'),
(653, 6, 'create_journal', 'accounting', 297, NULL, '{\"journal_number\":\"JV-202609-0288\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:47:33'),
(654, 6, 'post_central_invoice', 'finance', 288, NULL, '{\"invoice_number\":\"INV - 286\",\"total\":\"8640.00\",\"journal_entry_id\":297}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:47:33'),
(655, 6, 'create_journal', 'accounting', 298, NULL, '{\"journal_number\":\"JV-202609-0289\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:47:58'),
(656, 6, 'post_central_invoice', 'finance', 289, NULL, '{\"invoice_number\":\"INV - 287\",\"total\":\"4200.00\",\"journal_entry_id\":298}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:47:58'),
(657, 6, 'create_journal', 'accounting', 299, NULL, '{\"journal_number\":\"JV-202609-0290\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:48:27'),
(658, 6, 'post_central_invoice', 'finance', 290, NULL, '{\"invoice_number\":\"INV - 288\",\"total\":\"4800.00\",\"journal_entry_id\":299}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:48:27'),
(659, 6, 'create_journal', 'accounting', 300, NULL, '{\"journal_number\":\"JV-202609-0291\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:49:14'),
(660, 6, 'post_central_invoice', 'finance', 292, NULL, '{\"invoice_number\":\"INV - 290\",\"total\":\"5940.00\",\"journal_entry_id\":300}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:49:14'),
(661, 6, 'create_journal', 'accounting', 301, NULL, '{\"journal_number\":\"JV-202609-0292\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:49:41'),
(662, 6, 'post_central_invoice', 'finance', 293, NULL, '{\"invoice_number\":\"INV - 291\",\"total\":\"4800.00\",\"journal_entry_id\":301}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:49:41'),
(663, 6, 'create_journal', 'accounting', 302, NULL, '{\"journal_number\":\"JV-202609-0293\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:50:09'),
(664, 6, 'post_central_invoice', 'finance', 294, NULL, '{\"invoice_number\":\"INV - 292\",\"total\":\"1200.00\",\"journal_entry_id\":302}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:50:09'),
(665, 6, 'create_journal', 'accounting', 303, NULL, '{\"journal_number\":\"JV-202609-0294\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:50:33'),
(666, 6, 'post_central_invoice', 'finance', 295, NULL, '{\"invoice_number\":\"INV - 293\",\"total\":\"4320.00\",\"journal_entry_id\":303}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:50:33'),
(667, 6, 'create_journal', 'accounting', 304, NULL, '{\"journal_number\":\"JV-202609-0295\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:51:03'),
(668, 6, 'post_central_invoice', 'finance', 296, NULL, '{\"invoice_number\":\"INV - 294\",\"total\":\"14400.00\",\"journal_entry_id\":304}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:51:03'),
(669, 6, 'create_journal', 'accounting', 305, NULL, '{\"journal_number\":\"JV-202609-0296\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:51:31'),
(670, 6, 'post_central_invoice', 'finance', 297, NULL, '{\"invoice_number\":\"INV - 295\",\"total\":\"4200.00\",\"journal_entry_id\":305}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:51:31'),
(671, 6, 'create_journal', 'accounting', 306, NULL, '{\"journal_number\":\"JV-202609-0297\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:52:02'),
(672, 6, 'post_central_invoice', 'finance', 298, NULL, '{\"invoice_number\":\"INV - 296\",\"total\":\"6000.00\",\"journal_entry_id\":306}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:52:02'),
(673, 6, 'create_journal', 'accounting', 307, NULL, '{\"journal_number\":\"JV-202609-0298\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:52:24'),
(674, 6, 'post_central_invoice', 'finance', 299, NULL, '{\"invoice_number\":\"INV - 297\",\"total\":\"4860.00\",\"journal_entry_id\":307}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:52:24'),
(675, 6, 'create_journal', 'accounting', 308, NULL, '{\"journal_number\":\"JV-202609-0299\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:54:38'),
(676, 6, 'post_central_invoice', 'finance', 301, NULL, '{\"invoice_number\":\"INV - 299\",\"total\":\"8400.00\",\"journal_entry_id\":308}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:54:38'),
(677, 6, 'create_journal', 'accounting', 309, NULL, '{\"journal_number\":\"JV-202609-0300\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:55:11'),
(678, 6, 'post_central_invoice', 'finance', 302, NULL, '{\"invoice_number\":\"INV - 300\",\"total\":\"1080.00\",\"journal_entry_id\":309}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:55:11'),
(679, 6, 'create_journal', 'accounting', 310, NULL, '{\"journal_number\":\"JV-202609-0301\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:58:39'),
(680, 6, 'post_central_invoice', 'finance', 303, NULL, '{\"invoice_number\":\"INV - 301\",\"total\":\"2160.00\",\"journal_entry_id\":310}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:58:39'),
(681, 6, 'create_journal', 'accounting', 311, NULL, '{\"journal_number\":\"JV-202609-0302\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:59:08'),
(682, 6, 'post_central_invoice', 'finance', 304, NULL, '{\"invoice_number\":\"INV - 302\",\"total\":\"18000.00\",\"journal_entry_id\":311}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:59:08'),
(683, 6, 'create_journal', 'accounting', 312, NULL, '{\"journal_number\":\"JV-202609-0303\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:59:36'),
(684, 6, 'post_central_invoice', 'finance', 305, NULL, '{\"invoice_number\":\"INV - 303\",\"total\":\"4320.00\",\"journal_entry_id\":312}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:59:36'),
(685, 6, 'create_journal', 'accounting', 313, NULL, '{\"journal_number\":\"JV-202609-0304\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:00:00'),
(686, 6, 'post_central_invoice', 'finance', 306, NULL, '{\"invoice_number\":\"INV - 304\",\"total\":\"8640.00\",\"journal_entry_id\":313}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:00:00'),
(687, 6, 'create_journal', 'accounting', 314, NULL, '{\"journal_number\":\"JV-202609-0305\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:00:35'),
(688, 6, 'post_central_invoice', 'finance', 307, NULL, '{\"invoice_number\":\"INV - 305\",\"total\":\"7200.00\",\"journal_entry_id\":314}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:00:35'),
(689, 6, 'create_journal', 'accounting', 315, NULL, '{\"journal_number\":\"JV-202609-0306\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:01:07'),
(690, 6, 'post_central_invoice', 'finance', 308, NULL, '{\"invoice_number\":\"INV - 306\",\"total\":\"3600.00\",\"journal_entry_id\":315}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:01:07'),
(691, 6, 'create_journal', 'accounting', 316, NULL, '{\"journal_number\":\"JV-202609-0307\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:02:05'),
(692, 6, 'post_central_invoice', 'finance', 309, NULL, '{\"invoice_number\":\"INV - 307\",\"total\":\"7200.00\",\"journal_entry_id\":316}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:02:05'),
(693, 6, 'create_journal', 'accounting', 317, NULL, '{\"journal_number\":\"JV-202609-0308\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:12:19'),
(694, 6, 'post_central_invoice', 'finance', 310, NULL, '{\"invoice_number\":\"INV - 308\",\"total\":\"1800.00\",\"journal_entry_id\":317}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:12:19'),
(695, 6, 'create_journal', 'accounting', 318, NULL, '{\"journal_number\":\"JV-202609-0309\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:13:20'),
(696, 6, 'post_central_invoice', 'finance', 311, NULL, '{\"invoice_number\":\"INV - 309\",\"total\":\"4800.00\",\"journal_entry_id\":318}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:13:20'),
(697, 1, 'login', 'auth', 1, NULL, '{\"username\":\"admin\"}', '112.134.181.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-28 05:14:39'),
(698, 6, 'create_journal', 'accounting', 319, NULL, '{\"journal_number\":\"JV-202609-0310\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:15:30'),
(699, 6, 'post_central_invoice', 'finance', 312, NULL, '{\"invoice_number\":\"INV - 310\",\"total\":\"7560.00\",\"journal_entry_id\":319}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:15:30'),
(700, 6, 'create_journal', 'accounting', 320, NULL, '{\"journal_number\":\"JV-202609-0311\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:16:16'),
(701, 6, 'post_central_invoice', 'finance', 304, NULL, '{\"invoice_number\":\"INV - 302\",\"total\":\"18000.00\",\"journal_entry_id\":320}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:16:16'),
(702, 6, 'create_journal', 'accounting', 321, NULL, '{\"journal_number\":\"JV-202609-0312\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:18:29'),
(703, 6, 'post_central_invoice', 'finance', 313, NULL, '{\"invoice_number\":\"INV - 311\",\"total\":\"2400.00\",\"journal_entry_id\":321}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:18:29'),
(704, 6, 'create_journal', 'accounting', 322, NULL, '{\"journal_number\":\"JV-202609-0313\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:19:37'),
(705, 6, 'post_central_invoice', 'finance', 314, NULL, '{\"invoice_number\":\"INV - 312\",\"total\":\"4320.00\",\"journal_entry_id\":322}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:19:37'),
(706, 6, 'create_journal', 'accounting', 323, NULL, '{\"journal_number\":\"JV-202609-0314\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:20:32'),
(707, 6, 'post_central_invoice', 'finance', 316, NULL, '{\"invoice_number\":\"INV - 314\",\"total\":\"4200.00\",\"journal_entry_id\":323}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:20:32'),
(708, 6, 'create_journal', 'accounting', 324, NULL, '{\"journal_number\":\"JV-202609-0315\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:21:05'),
(709, 6, 'post_central_invoice', 'finance', 317, NULL, '{\"invoice_number\":\"INV - 315\",\"total\":\"1800.00\",\"journal_entry_id\":324}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:21:05'),
(710, 6, 'create_journal', 'accounting', 325, NULL, '{\"journal_number\":\"JV-202609-0315\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:21:24'),
(711, 6, 'post_central_invoice', 'finance', 317, NULL, '{\"invoice_number\":\"INV - 315\",\"total\":\"1800.00\",\"journal_entry_id\":325}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:21:24'),
(712, 6, 'create_journal', 'accounting', 326, NULL, '{\"journal_number\":\"JV-202609-0315\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:21:44'),
(713, 6, 'post_central_invoice', 'finance', 317, NULL, '{\"invoice_number\":\"INV - 315\",\"total\":\"1800.00\",\"journal_entry_id\":326}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:21:44'),
(714, 6, 'create_journal', 'accounting', 327, NULL, '{\"journal_number\":\"JV-202609-0315\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:22:28'),
(715, 6, 'post_central_invoice', 'finance', 317, NULL, '{\"invoice_number\":\"INV - 315\",\"total\":\"1800.00\",\"journal_entry_id\":327}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:22:28'),
(716, 6, 'create_journal', 'accounting', 328, NULL, '{\"journal_number\":\"JV-202609-0315\",\"status\":\"posted\"}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:23:21'),
(717, 6, 'post_central_invoice', 'finance', 317, NULL, '{\"invoice_number\":\"INV - 315\",\"total\":\"1800.00\",\"journal_entry_id\":328}', '175.157.8.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 05:23:21');

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) NOT NULL,
  `account_name` varchar(100) NOT NULL,
  `swift_code` varchar(20) DEFAULT NULL,
  `current_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_deposits`
--

CREATE TABLE `bank_deposits` (
  `id` int(11) NOT NULL,
  `deposit_number` varchar(50) NOT NULL,
  `deposit_date` date NOT NULL,
  `bank_account_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'DRAFT',
  `created_by` int(11) DEFAULT NULL,
  `journal_entry_id` int(11) DEFAULT NULL,
  `reversal_journal_entry_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_reconciliations`
--

CREATE TABLE `bank_reconciliations` (
  `id` int(10) UNSIGNED NOT NULL,
  `bank_account_id` int(10) UNSIGNED NOT NULL,
  `statement_date` date NOT NULL,
  `ending_balance` decimal(15,2) NOT NULL,
  `book_balance` decimal(15,2) NOT NULL,
  `difference` decimal(15,2) NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brick_production_projects`
--

CREATE TABLE `brick_production_projects` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_name` varchar(150) NOT NULL,
  `location` varchar(150) NOT NULL,
  `start_date` date NOT NULL,
  `expected_completion_date` date DEFAULT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `planned_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(50) NOT NULL DEFAULT 'Pieces',
  `status` enum('ACTIVE','COMPLETED','CANCELLED') NOT NULL DEFAULT 'ACTIVE',
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brick_production_records`
--

CREATE TABLE `brick_production_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `production_date` date NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(50) NOT NULL DEFAULT 'Pieces',
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brick_transfers`
--

CREATE TABLE `brick_transfers` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `production_record_id` int(10) UNSIGNED NOT NULL,
  `transfer_date` date NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cost_price_per_unit` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `selling_price_per_unit` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_accounts`
--

CREATE TABLE `cash_accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `current_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cash_accounts`
--

INSERT INTO `cash_accounts` (`id`, `account_id`, `code`, `name`, `current_balance`, `status`, `created_at`, `updated_at`) VALUES
(1, 9, 'CASH-MAIN', 'Cash in Hand', 1402420.00, 'active', '2026-09-22 10:42:26', '2026-09-28 05:23:21');

-- --------------------------------------------------------

--
-- Table structure for table `cheques`
--

CREATE TABLE `cheques` (
  `id` int(10) UNSIGNED NOT NULL,
  `cheque_number` varchar(50) NOT NULL,
  `cheque_type` varchar(50) NOT NULL,
  `party_id` int(10) UNSIGNED NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `cheque_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `received_issued_date` date NOT NULL,
  `status` varchar(50) DEFAULT 'RECEIVED',
  `created_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coop_members`
--

CREATE TABLE `coop_members` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_type` enum('MEMBER','DIRECTOR') DEFAULT 'MEMBER',
  `member_no` varchar(50) NOT NULL,
  `party_id` int(10) UNSIGNED DEFAULT NULL,
  `full_name` varchar(150) NOT NULL,
  `nic` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `occupation` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `agricultural_sector` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `membership_type` varchar(100) DEFAULT 'Ordinary',
  `status` enum('ACTIVE','INACTIVE','SUSPENDED','RESIGNED') NOT NULL DEFAULT 'ACTIVE',
  `registration_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `shares_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('Unpaid','Cash','Bank Transfer','Cheque') NOT NULL DEFAULT 'Unpaid',
  `payment_status` enum('UNPAID','PAID') NOT NULL DEFAULT 'UNPAID',
  `notes` text DEFAULT NULL,
  `heir_name` varchar(255) DEFAULT NULL,
  `heir_address` text DEFAULT NULL,
  `heir_nic` varchar(100) DEFAULT NULL,
  `heir_contact_number` varchar(50) DEFAULT NULL,
  `journal_entry_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coop_members`
--

INSERT INTO `coop_members` (`id`, `member_type`, `member_no`, `party_id`, `full_name`, `nic`, `dob`, `gender`, `occupation`, `phone`, `email`, `whatsapp`, `agricultural_sector`, `address`, `city`, `registration_date`, `membership_type`, `status`, `registration_fee`, `shares_fee`, `payment_method`, `payment_status`, `notes`, `heir_name`, `heir_address`, `heir_nic`, `heir_contact_number`, `journal_entry_id`, `created_at`) VALUES
(1, 'DIRECTOR', 'AGC/25/001', 1, 'Thennakoon Mudiyanselage Kumarasiri Bandara Thennakoon', '703101968V', '1970-05-11', 'Male', 'Statistical Officer', '0718211010', 'tmkbtennakoon@gmail.com', '0718211010', 'Banana, Vanila', 'Asiri, Werellapana, Rambukkana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 08:08:39'),
(2, 'DIRECTOR', 'AGC/25/002', 2, 'P L G Wimalarathne', '19570760556', '1957-03-16', 'Male', 'Retired Government Service', '0718460172', '', '0718460172', 'Coconut', 'Kotawella, Rambukkana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 09:10:25'),
(3, 'DIRECTOR', 'AGC/25/003', 3, 'Akranuge Manjula Udayananda Pinnalanda', '640401648v', '1964-09-02', 'Male', 'Retired', '0718028774', '', '0718028774', 'Paddy Cultivation, Vegetable', 'Mawathahena, Thalgama, Beligala', 'Warakapola', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 09:20:06'),
(4, 'DIRECTOR', 'AGC/25/004', 4, 'Ranhotige Ajith Wasantha Kumara', '197612303386', '1976-05-02', 'Male', 'Development Officer', '0711296150', 'dilshanchaa58@gmail.com', '0711296150', 'Paddy cultivation', 'Wasantha Kudagama ,Dombemaada, Rambukkana', 'Rambukkana', '2025-10-10', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 11:37:49'),
(5, 'MEMBER', 'AGC/25/005', 6, 'Muththettuwaththe Jayarathna', '196136204872', '1961-12-27', 'Male', 'Retired Teacher', '0722152510', '', '', 'Banana, Vegetable, Coconut, Fruit', 'Muththetuwaththa , Gangoda, Maakehelmala', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 11:43:21'),
(6, 'DIRECTOR', 'AGC/25/006', 7, 'Weda Gedara Sudath Priyankara Manukularathna', '633190380v', '1963-11-14', 'Male', '', '0714501397', 'Priyankaramanukularathana@gmail.com', '0714501397', '', '5/3 , Mihidu Mawatha, Rambukkana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 12:45:15'),
(7, 'MEMBER', 'AGC/25/007', 8, 'Kaluarachchilage Vijith Kumara Kaluarachchi', '643481090v', '1964-12-13', 'Male', 'Air Force Retired', '0714377139', 'wijithkslu@gmail.com', '0714377139', '', '1/12 Daheenpaduwa , Yatagaha, Rambukkana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 12:50:03'),
(8, 'DIRECTOR', 'AGC/25/008', 9, 'Dasanayaka Mudiyanselage Kalana Bandara Dasanayaka', '200318312819', '2003-07-01', 'Male', '', '0762714771', 'kalanadassanayaka22@gmail.com', '0762714771', 'Vegetable', 'E/56/1 , Udugama, Pathtampitiya, Rambukkana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 12:54:03'),
(9, 'MEMBER', 'AGC/25/009', 10, 'Rathnayaka Mudiyanselage Samantha Ranasingha', '772822294v', '1977-10-08', 'Male', '', '0718431807', '', '071431807', 'Banana, Vegetable', 'Paaluwaththa, Udugama , Paththampitiya, Rambukkana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 13:00:25'),
(10, 'MEMBER', 'AGC/25/010', 12, 'Pinnawalayaa Gedara Upul Jaanaka Pinnawala', '198325204093', '1983-08-09', 'Male', 'Development Officer', '0777585314', '', '0704162944', 'Vegetable', 'D/44/B/1 , Katulanda, Kotawellla, Rambukana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 13:24:49'),
(11, 'MEMBER', 'AGC/25/011', 13, 'Agampodi Dewayaalage Deepika Priyadarshani Premachandra', '876420562v', '1987-05-21', 'Female', 'Development Officer', '0778934213', 'deepikakatulanda@gmail.com', '0762040562', 'Vegetable', 'D/44/B/1 , Katulanda, Kotawellla, Rambukana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 13:30:30'),
(12, 'MEMBER', 'AGC/25/012', 14, 'Peramune Gamlath Raallaagee Aasiri Samantha Bandara', '850780463', '1985-03-18', 'Male', 'businessman', '0702821000', '', '0775564587', 'Fruit, Vegetable, Banana, Coconut, Paddy Cultivation', 'Udahawalawwa waththa, kempitiya, paththampitiya', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 13:35:57'),
(13, 'MEMBER', 'AGC/25/013', 15, 'Pushpa Kumara Siyambalapitiya', '703303382v', '1970-11-25', 'Male', '', '0753770145', 'kumarasiyambalapitya8@gmail.com', '0753770145', 'Banana, Coconut, Vegetable', 'c/14 Godawela, daliwala, Rambukkana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 13:46:55'),
(14, 'MEMBER', 'AGC/25/014', 16, 'Edirisingha Dewayalaage Ashan Shanika', '921301405v', '1992-05-09', 'Male', 'Vice Chairman, Pradeshiya Sabha', '0713604001', '', '0713604001', '', 'Ilukthanna, Gangekumbura, Rambukkana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 13:51:02'),
(15, 'MEMBER', 'AGC/25/015', 17, 'Kulasekara Mudiyanselage Ruupasingha', '196027704007', '1960-10-03', 'Male', 'Retired', '0352265915', '', '0705285600', 'Banana, Vegetable, Fruit, Rubber, Coconut', 'E/92, Puwakmote , Yatagama, Rambukkana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 13:55:26'),
(16, 'MEMBER', 'AGC/25/016', 18, 'Yatanwala Gamaralalage Jayaweera', '531823230v', '1953-06-30', 'Male', 'Retired Teacher', '0714413618', '', '', 'Paddy Cultivation', 'D/ 80/2 , Diwlawaththa, Keselwathugoda, Dewalagama', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 13:58:17'),
(17, 'MEMBER', 'AGC/25/017', 19, 'Haththaagodaylaa Imalsha Dileeka Haththagoda', '200519501676', '2005-07-13', 'Male', 'businessman', '0704670703', 'imalshadileeka@gmail.com', '0704670703', 'Banana, Rubber', '67/3 , Pansala Handiya, Gammala, Kotawella , Rambukkana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 14:02:26'),
(18, 'MEMBER', 'AGC/25/018', 20, 'Aathawuda Gedara Kulathunga Bandara', '562893725v', '1956-10-15', 'Male', 'Retired Teacher', '0713979934', '', '0713979886', 'Banana, Coconut, Bulath', 'A/66 Walgama, Yatagama, Rambukkana', 'Rambukkana', '2025-06-21', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 14:06:20'),
(19, 'MEMBER', 'AGC/25/019', 21, 'Ekanayaka Mudiyanselage Samantha Bandara Ekanayaka', '970401172v', '1997-02-09', 'Male', 'post', '0760002015', 'emsbekanayaka@gmail.com', '0760002015', 'Vegetable, Coconut', 'A 52/2 Godagathdeniya, Rambukkana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 14:17:04'),
(20, 'MEMBER', 'AGC/25/020', 22, 'Samarakon Mudiyanselage Gamini Samarakon', '196434404291', '1964-12-09', 'Male', 'Retired', '0702018313', '', '0702018313', 'Coconut, Fruit, Vegetable, Paddy Cultivation, Banana', 'B4 Gangoda, Mirihagoda, Hendiwela', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 14:21:02'),
(21, 'MEMBER', 'AGC/25/021', 23, 'Widana Ralalage Erandi Nisansala Weerabandara', '945721707v', '1994-03-12', 'Female', 'Pradeshiya Sabha Member', '0710618141', 'bandaranisansala@gmail.com', '0760734044', 'Paddy Cultivation', '66/1 A, Kudagama , Dombemada, Rambukkana', 'Rambukkana', '2025-06-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 14:31:01'),
(22, 'MEMBER', 'AGC/25/022', 24, 'Rampatidewage Sarath Ananda Premasiri', '740582810v', '1974-02-27', 'Male', '', '0713982748', '', '0713982748', 'Paddy Cultivation, Rubber, Banana, Fruit, Vegetable, Coconut', '2/1 , Deliwala, Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 14:38:00'),
(23, 'MEMBER', 'AGC/25/023', 25, 'Udagama Liyanalage Shantha Damsiri', '195436201536', '1954-12-27', 'Male', 'Retired Electricity Board', '0718408751', '', '077063851', 'Vegetable, Rubber', '87/14 A , Aladeniya Mawatha , Rohala Road, Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 14:42:36'),
(24, 'MEMBER', 'AGC/25/024', 26, 'J.G Omilaa Wasanthi Edirisingha', '19776232122v', '1977-05-02', 'Male', 'Self-employment', '0715288769', '', '0715288769', 'Paddy Cultivation, Banana, Vegetable, Fruit, Rubber, Coconut, Vanila, Cinnamon, Areca nut', '290/1 Shanthi Gbbala, Kotawella, Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 14:51:04'),
(25, 'MEMBER', 'AGC/25/025', 27, 'Dewaylaa Gedara Manjula Prabath Hemachnadra', '812285033v', '1981-08-15', 'Male', 'Farming', '0774745524', '', '0788485731', 'Paddy Cultivation, Coconut, Rubber', 'E 28/3 koswaththa , Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 14:54:36'),
(26, 'MEMBER', 'AGC/25/026', 28, 'Agampodi Dewayaage Manoj Nilantha Gunasekara', '19863391326v', '1986-12-04', 'Male', '', '0712196131', '', '0712196131', 'Paddy Cultivation, Vegetable, Fruit, Coconut, Rubber, Vanila, Cinnamon, Areca nut', 'Gabbala, Kotawella , Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 15:35:50'),
(27, 'MEMBER', 'AGC/25/027', 29, 'Ampalegedara Samarasingha Bandara', '672382041v', '1967-05-08', 'Male', 'Teacher', '0775299287', '', '0775299287', 'Banana, Coconut, Cinnamon', 'Wallagoda', 'Rambukkana', '2025-10-24', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 15:38:09'),
(28, 'MEMBER', 'AGC/25/028', 30, 'Ranathunga Arachchilage Pradip Shantha Kumara', '810570644v', '1981-02-26', 'Male', 'Teacher', '0717873993', 'pradeepranathunga@gmail.com', '0717873993', 'Cinnamon', '17, Mahipala Herath Road, Anwataya, Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 15:45:27'),
(29, 'MEMBER', 'AGC/25/029', 31, 'G.R.R.S Gamlath', '867171100v', '1986-08-04', 'Male', 'Development Officer', '0778044921', 'gamlath519@gmail.com', '0778044921', 'Paddy Cultivation, Coconut, Cinnamon', 'F/94 Weligamuwa, Kotawella, Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 15:50:14'),
(30, 'MEMBER', 'AGC/25/030', 32, 'Udugoda Gedara Somarathna', '470923547v', '1947-04-01', 'Male', 'Retired', '0352264020', '', '', 'Paddy Cultivation, Coconut, Rubber, Vegetable, Banana', 'Puwakmote,Yatagama ,Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 15:52:31'),
(31, 'MEMBER', 'AGC/25/031', 33, 'Weligamage Thillak Weligama', '197011404597', '1970-04-23', 'Male', 'businessman', '0770866528', 'weligamabakers@gmail.com', '0770866528', 'Paddy Cultivation, Banana, Vegetable, Fruit, Coconut, Rubber', 'Mirihagoda, Hewadiwela, Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 15:55:14'),
(32, 'MEMBER', 'AGC/25/032', 34, 'R.Nimal Weerasingha', '195921210103', '1959-07-30', 'Male', 'Retired', '0779064289', '', '0779064289', 'Paddy Cultivation, Banana, Vegetable, Fruit, Coconut', 'Sirisewana Aadaweta, Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-15 15:58:11'),
(33, 'MEMBER', 'AGC/25/033', 35, 'I.P Champikaa Damayanthi', '666320255v', '1966-05-11', 'Female', 'Farming', '0776236998', '', '0776236998', 'Paddy Cultivation, Vegetable, Floriculture', '68, Bandarawaththa mahawa, Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 03:20:47'),
(34, 'MEMBER', 'AGC/26/001', 36, 'Wijesuriya Mudiyanselage Buddika Gamunu Bandara', '197914700757', '1979-05-26', 'Male', 'electrician', '0776235213', '', '0776235213', 'Paddy Cultivation, Banana, Vegetable, Fruit, Coconut, Rubber', 'A/83, Daliwala, Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 03:26:29'),
(35, 'MEMBER', 'AGC/25/035', 37, 'Ambekoan Senawirathna Panditha Wasala Mudiyanse Ralahamilage Aruna Senawirathna', '772313012v', '1977-08-18', 'Male', 'Quantity Surveyor', '0702874881', 'aruna1808@gmail.com', '0702874881', 'Paddy Cultivation, Banana, Coconut', 'A/69, Dalimala,Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 03:33:29'),
(36, 'MEMBER', 'AGC/25/036', 38, 'Edirisuriya Mudiyanselage Priyantha Bandara Edirisuriya', '198014503252', '1980-05-24', 'Male', 'Management Services Officer', '0712006999', 'priyanthaedirisuriya@gmail.com', '0712006999', 'Coconut', 'Kundagollawaththa, Pinnawala, Rambukkana', 'Rambukkana', '2025-10-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 03:57:00'),
(37, 'MEMBER', 'AGC/25/037', 39, 'Warnakulasuriya Arachchilage Jayalath Perera', '692532503v', '1969-09-09', 'Male', 'Estate Superintendent', '0779503669', 'Pererajayalath0909@gmail.com', '0702166623', 'Paddy Cultivation, Banana, Coconut, Fruit', 'Walawwa, Walgama, Yatagama, Rambukkana', 'Rambukkana', '2025-10-18', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 04:04:11'),
(38, 'MEMBER', 'AGC/25/038', 40, 'Thennakoan Mudiyanselage Sarath Jayathissa', '630355060v', '1963-02-04', 'Male', 'Retired Army personnel', '0711367327', '', '', 'Banana, Vegetable, Potato', '116/B , Malberiwaththa, Keselwathugoda, Dewalagama', 'Rambukkana', '2025-10-18', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 04:16:07'),
(39, 'MEMBER', 'AGC/25/039', 41, 'Sinhalage Gunasiri Gunathilaka', '571833786v', '1957-07-01', 'Male', 'Retired', '0718084972', '', '0718084972', 'Paddy Cultivation', 'Duminda, Polaththaapitiya, Udadeniya, Rambukkana', 'Rambukkana', '2025-10-19', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 04:18:25'),
(40, 'MEMBER', 'AGC/25/040', 42, 'Yatiwaldeniya Gedara Gunarathna Weerasena', '702420253v', '1970-08-29', 'Male', '', '0707475192', '', '0715796072', '', '10 A/127 , Diyasinatha , Rambukkana', 'Rambukkana', '2025-10-19', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 05:33:24'),
(41, 'MEMBER', 'AGC/25/041', 43, 'Marawala Ralalage Indika Saman Kumara', '820982231v', '1982-04-07', 'Male', 'Driver', '0714951490', '', '0714951490', 'Paddy Cultivation, Coconut, Banana, Grass cultivation', 'A 87/1 , Walgama ,Yatagama, Rambukkana', 'Rambukkana', '2025-10-19', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 05:40:24'),
(42, 'MEMBER', 'AGC/25/042', 44, 'Upali Gunawardana Ilangakoan', '530051811v', '1953-01-05', 'Male', 'Farming', '0712025376', '', '072208915667', 'Coconut, Banana, Vegetable', 'Malwaththa Road, Kehelwathugoda, Dewalagama', 'Rambukkana', '2025-11-05', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 05:42:32'),
(43, 'MEMBER', 'AGC/25/043', 45, 'Sohani Chamilika Kumari Ilangakoan', '198368403118', '1983-07-02', 'Male', '', '0720891567', 'sohaniillangakoon@gmail.com', '0712025376', 'Banana, Vegetable, Fruit, Coconut', 'D 3611, Malwaththa Walawwa, Keselwathugoda, Dewalagama', 'Rambukkana', '2025-11-05', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 05:48:55'),
(44, 'MEMBER', 'AGC/25/044', 46, 'Dasanayaka Mudiyanselage Jaaliya Dasanayaka', '631832059v', '1963-07-01', 'Male', 'businessman', '0777974011', '', '0777974011', 'Paddy Cultivation, Banana, Vegetable, Coconut, Cinnamon', 'Ruksewana, Pohorache, Dewalagama', 'Rambukkana', '2025-11-06', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 05:51:16'),
(45, 'MEMBER', 'AGC/25/045', 47, 'W.Chaminda Sugath', '197423002717', '1974-08-17', 'Male', 'Animal Husbandry', '0372051710', '', '0712313337', 'Paddy Cultivation, Banana, Vegetable, Animal Husbandry, Coconut', 'C 18/2 Ambuwangala , Imbulgasdeniya', 'Rambukkana', '2025-11-07', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 05:56:50'),
(46, 'MEMBER', 'AGC/25/046', 48, 'Waasitiyalaage Sunil', '633132682v', '1963-11-08', 'Male', 'Retired', '0372053503', '', '0762451657', 'Paddy Cultivation, Coconut, Animal Husbandry', 'Kumbuwangala, Imbulgasdeniya', 'Rambukkana', '2025-11-09', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 05:59:13'),
(47, 'MEMBER', 'AGC/25/047', 49, 'Mutha Merachcha Ranil Chandrathilaka', '197915101259', '1979-05-30', 'Male', 'Air Force Retired', '0772657482', '', '0772657482', 'Coconut, Banana', '22, Mihidu mawatha, Eriyawa, Rambukkana', 'Rambukkana', '2026-01-01', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'B.S.S Damayanthi', '22, Mihidu Mawatha, Eriyawa, Rambukkana', '198083801664', '', NULL, '2026-09-16 06:04:21'),
(48, 'MEMBER', 'AGC/26/048', 50, 'Hengaha Rallage Dingiri Banda', '490394206v', '1949-02-08', 'Male', 'Retired', '0740511949', '', '', 'Paddy Cultivation, Banana, Vegetable, Fruit, Coconut', 'C 949, Puwakmote, Yatagama, Rambukkana', 'Rambukkana', '2026-02-02', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'R.M Dayawthi Manike', 'Puwakmote, Yatagama, Rambukkana', '468500230v', '', NULL, '2026-09-16 06:10:14'),
(49, 'MEMBER', 'AGC/26/049', 51, 'Nawarathna Mudiyanselage Senawirathna', '194728400967', '1947-10-10', 'Male', 'Retired', '0716291422', '', '0716291422', 'Paddy Cultivation, Rubber, Vegetable', 'B 35, Mirihagoda, Hewadiwela', 'Rambukkana', '2026-02-21', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'H.M.T Hemalatha Hewadiwela', 'B 35 , Mirihagoda, Hewadiwela', '497931223v', '', NULL, '2026-09-16 06:12:45'),
(50, 'MEMBER', 'AGC/26/050', 52, 'J.M Thilak Champika Jayasingha', '196102910054', '1961-01-29', 'Male', 'Farming', '0704912620', '', '0704912620', 'Paddy Cultivation, Vegetable, Coconut, Cow management', 'Walgama, Yatagama, Rambukkana', 'Rambukkana', '2025-10-27', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-16 06:16:25'),
(51, 'MEMBER', 'AGC/26/051', 53, 'Katukurunda Gamage Dedunu Sithari Katukurunda', '197762602951', '1996-05-05', 'Female', 'Floriculture', '0717282226', '', '0704264172', 'Paddy Cultivation, Vegetable, Rubber, Fruit, Floriculture', 'Dahenpathuwa , Yatagama, Rambukkana', 'Rambukkana', '2026-03-03', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'R.M.T.C Rathnayaka', 'Dahenpathuwa , Yatagama, Rambukkana', '703263194v', '', NULL, '2026-09-16 06:19:29'),
(52, 'MEMBER', 'AGC/26/052', 54, 'Edirisingha Arachchige Gamini Vijerathna', '521752777v', '1952-06-20', 'Male', 'Retired Principle', '0777106892', '', '0777106892', 'Paddy Cultivation, Banana, Coconut, Fruit, Black pepper', 'Walalgoda, Rambukkana', 'Rambukkana', '2026-03-03', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'R.D Chintha Rani', 'Walalgoda, Rambukkana', '538258727v', '', NULL, '2026-09-16 06:55:46'),
(53, 'MEMBER', 'AGC/26/053', 55, 'Athugalge Dewadasa', '570311441v', '1957-01-31', 'Male', 'Farming', '0703344038', '', '', 'Paddy Cultivation, Vegetable', '75, Alagolla, Hewadiwela', 'Rambukkana', '2026-02-03', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Seetha Malkanthi', '75, Alagolla, Hewadiwela', '', '', NULL, '2026-09-16 06:58:59'),
(54, 'MEMBER', 'AGC/26/054', 56, 'Jayasundara  Mudiyanselage Herathbanda Jayasundara', '450118560', '1945-01-11', 'Male', 'Retired', '0741645056', '', '', '', 'C 48, Hinabowa, Rambukana', 'Rambukkana', '2026-03-03', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'J.M.S.D Jayasundara', 'Hinabowa, Rambukana', '19790729', '', NULL, '2026-09-16 07:10:45'),
(55, 'MEMBER', 'AGC/26/055', 57, 'Kalaochiyla Gedara Ranjani Shriyalatha', '695552092v', '1969-02-24', 'Female', 'Self-employment', '0702261095', '', '0702261095', 'Floriculture', 'Middeniyawaththa, Parape, Rambukkana', 'Rambukkana', '2026-03-27', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'R.D Shalitha Dilshan Vimalasena', 'Middeniyawaththa, Parape, Rambukkana', '950022744v', '', NULL, '2026-09-16 07:15:11'),
(56, 'MEMBER', 'AGC/26/056', 58, 'Suduhakurala Renuka Nandani Edirisingha', '708082457v', '1970-11-03', 'Female', 'Farming', '0718694082', '', '0703322031', 'Paddy Cultivation, Banana, Vegetable, Fruit, Coconut', 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', '2026-03-27', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'D.D Gagana Achintha Jayaweera', 'Pahalagama ,Parape ,Rambukkana', '', '', NULL, '2026-09-16 07:17:51'),
(57, 'MEMBER', 'AGC/26/057', 59, 'Kosgollalaa Gedara Kumuduni Samankumari', '765272262v', '1976-01-27', 'Female', 'Farming', '0761550261', '', '0761550261', '', 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', '2026-03-27', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'B.G Pabodaa Maduwanthi Liyanage', 'A 41/2 Pahalagama , Parape, Rambukkana', '200550102992', '', NULL, '2026-09-16 07:37:03'),
(58, 'MEMBER', 'AGC/26/058', 60, 'Pihille Gedara Jayarathna', '195913900414', '1959-05-18', 'Male', 'Farming', '0714985750', '', '', 'Paddy Cultivation', 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', '2026-03-27', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'P.G Lakshitha Ishan Jayarathna', 'Pahalagama ,Parape ,Rambukkana', '', '', NULL, '2026-09-16 07:54:03'),
(59, 'MEMBER', 'AGC/26/059', 61, 'Edirisingha Dewayalaage Priyangaa Kumuduni Jayasingha', '197168401057', '1971-07-02', 'Female', 'Farming', '0769607060', '', '0704656471', 'Coconut, Paddy Cultivation, Banana, Cow management', 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', '2026-03-27', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'R.D Roshan Madushanka Samaraweera', 'Pahalagama ,Parape ,Rambukkana', '', '', NULL, '2026-09-16 07:56:40'),
(60, 'MEMBER', 'AGC/26/060', 62, 'Ranathunga Dewayalaa Sunil Suwinitha Mallika', '195784900248', '1957-10-14', 'Female', 'Rolling beedis', '0772198342', '', '', 'Coconut, Banana', 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', '2026-03-27', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Renuka Handari Edirisinga', '', '', '', NULL, '2026-09-16 10:21:20'),
(61, 'MEMBER', 'AGC/26/061', 63, 'Werawellegedara Manel Rukmani Latha', '197164101311', '1971-05-20', 'Female', '', '0712917280', '', '0716355530', 'Paddy Cultivation, Banana, Vegetable', 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', '2026-03-27', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'I.P Ishani Sithara Udayasiri', 'Pahalagama ,Parape ,Rambukkana', '', '', NULL, '2026-09-16 10:23:46'),
(62, 'MEMBER', 'AGC/26/062', 64, 'Edirimuni Dewayalage Karunawathi', '195782100237', '1957-11-16', 'Female', '', '0706546055', '', '', 'Vegetable', 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', '2026-03-26', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'E.D Karunawathi', 'Pahalagama ,Parape ,Rambukkana', '195782100237', '', NULL, '2026-09-16 10:33:48'),
(63, 'MEMBER', 'AGC/26/063', 65, 'Suduhakurala Upali Senarath Edirisingha', '670260380v', '1967-01-26', 'Male', 'Farming', '0761626408', '', '', 'Paddy Cultivation, Banana, Vegetable, Coconut, Fruit, Trade', 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', '2026-03-27', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Priyanthaa Ranjani  Edirisingha', 'Pahalagama ,Parape ,Rambukkana', '', '', NULL, '2026-09-16 10:37:25'),
(64, 'MEMBER', 'AGC/26/064', 66, 'W.G.T Gamini', '195730701204', '1957-11-02', 'Male', '', '0715674358', '', '', 'Coconut, Vegetable, Banana, Paddy Cultivation', 'Yawanakanda , parape , Rambukkana', 'Rambukkana', '2026-03-27', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'W.G Nuwan Dayaweera', 'Yawanakanda , parape , Rambukkana', '', '', NULL, '2026-09-16 10:47:12'),
(65, 'MEMBER', 'AGC/26/065', 67, 'Dukganna Walawwe Madduma Bandara', '461662544v', '1946-06-14', 'Male', 'Farming', '0372391761', '', '', 'Paddy Cultivation, Coconut, Banana', 'Amuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'D.W Ranjan Kumara', 'Amuwangala, Imbulgasdeniya', '198307700593', '', NULL, '2026-09-16 10:49:52'),
(66, 'MEMBER', 'AGC/26/066', 68, 'Kainankada Edirimuni Suriyage Sunil Saanthi Gunasekara', '196122701216', '1961-08-14', 'Male', 'Retired', '0776615854', '', '0776615854', 'Paddy Cultivation, Coconut', 'Munamale , Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-21', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'K.D.C Pathmini', 'Munamale , Ambuwangala, Imbulgasdeniya', '', '', NULL, '2026-09-16 10:58:08'),
(67, 'MEMBER', 'AGC/26/067', 69, 'Wannaku Waththe Waduge Don Anura Ranjith Perera', '195932800977', '1959-11-23', 'Male', 'Retired', '0717271831', '', '0717271831', 'Paddy Cultivation, Banana, Coconut', 'C.4 Abuwangala , Imbulgasdeniya', 'Rambukkana', '2026-03-21', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'J.A.A.S Wijekoan', 'C.4 Abuwangala , Imbulgasdeniya', '685651661v', '', NULL, '2026-09-16 11:02:18'),
(68, 'MEMBER', 'AGC/26/068', 70, 'Pallewelayage Ariyadasa', '4815112559v', '1948-05-30', 'Male', 'Retired Justice of the Peace', '0354395300', '', '', 'Paddy Cultivation, Coconut, Banana', 'Rambukkana Road , Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-28', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'P.G.A Bandara', 'Rambukkana Road , Ambuwangala, Imbulgasdeniya', '198909101129', '', NULL, '2026-09-17 03:31:55'),
(69, 'MEMBER', 'AGC/26/069', 71, 'Suduhakuralage Chamara Sunil Kumara', '852703067v', '1985-09-20', 'Male', 'Farming', '0774730344', '', '', 'Paddy Cultivation', 'Marukwathura, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'S.H Gayandi Nethsara Kumari', '', '', '', NULL, '2026-09-17 03:35:04'),
(70, 'MEMBER', 'AGC/26/070', 72, 'Pallewelage Mahasen Karunarathna', '1962400318', '1962-11-09', 'Male', 'Farming', '071939509', '', '0711939509', 'Paddy Cultivation, Coconut', 'ShanthiSewana, Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-23', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'P.A.P.D Karunarathna', 'Amuwangala, Imbulgasdeniya', '940064660v', '', NULL, '2026-09-17 03:43:15'),
(71, 'MEMBER', 'AGC/26/071', 73, 'Koskotuwe Gedara Chandani Dhammikaa Koskotuwa', '196485601032', '1964-12-21', 'Female', 'Retired', '0766795044', '', '0766795044', 'Paddy Cultivation, Coconut', 'Munamale , Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-21', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'N.E.S.D.J Gunasekara', 'Munamale , Ambuwangala, Imbulgasdeniya', '200420102876', '', NULL, '2026-09-17 04:00:10'),
(72, 'MEMBER', 'AGC/26/072', 74, 'Manikkum Pedige Indrani Kusumlatha', '097162503198', '1971-05-04', 'Female', '', '0762960283', '', '', 'Paddy Cultivation', 'Kandiwaththa, Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'W. Kasun Lakmal Wapitiya', 'Kandiwaththa, Ambuwangala, Imbulgasdeniya', '46011820', '', NULL, '2026-09-17 04:03:23'),
(73, 'MEMBER', 'AGC/26/073', 75, 'Gaspe Ralalage Wijewardana', '194518603662', '1945-05-15', 'Male', 'Farming', '0714133846', '', '', 'Paddy Cultivation, Vegetable', 'Amuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-23', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-17 04:08:52'),
(74, 'MEMBER', 'AGC/26/074', 76, 'Thibbotuge Sirisena', '413291283v', '1941-11-24', 'Male', 'Retired', '0764107119', '', '', 'Paddy Cultivation, Coconut', 'Suranga Niwasa , Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'P.D Sirisena', 'Suranga Niwasa , Imbulgasdeniya', '413291283', '', NULL, '2026-09-17 04:16:29'),
(75, 'MEMBER', 'AGC/26/075', 77, 'Henaka Ralalage Nalin Prabath Amarasingha', '730560656v', '1973-02-25', 'Male', 'Retired', '0718441128', '', '', 'Paddy Cultivation, Coconut, Banana', 'Marukwathura, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Gamaralalage Yatuna Pramodini Kumari', 'Marukwathura, Imbulgasdeniya', '', '', NULL, '2026-09-17 04:35:52'),
(76, 'MEMBER', 'AGC/26/076', 78, 'Jayaweera Arachchige Asanka Bandara Jayaweera', '198813800757', '1988-05-17', 'Male', 'Workplace inspector', '0712063651', '', '07620633651', 'Paddy Cultivation, Coconut, Banana, Fruit', 'Kondostharawaththa , Marukwathura , Imbulgasdeniya', 'Rambukkana', '2026-03-23', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'M.R.H Manike', 'Kondostharawaththa , Marukwathura , Imbulgasdeniya', '548304350v', '', NULL, '2026-09-17 11:08:56'),
(77, 'MEMBER', 'AGC/26/077', 79, 'Hewawasam Gallage Nandawathi', '496343468v', '1949-05-13', 'Female', '', '0372053587', '', '0701674028', 'Paddy Cultivation', 'Udakothuwawaththa, Aduwangala, Iwulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Pallegamage Paltha Nihal Samarathunga', 'Udakothuwawaththa, Aduwangala, Iwulgasdeniya', '197619602531', '', NULL, '2026-09-17 11:11:41'),
(78, 'MEMBER', 'AGC/26/078', 80, 'Henaka Ralalage Askoaa', '647700799v', '1964-09-26', 'Female', 'Farming', '0711646879', '', '0711646879', 'Paddy Cultivation', 'Sigiri , Wttarak Thanna, Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'S.G Ruchini Kawshalya Jayawardana', 'Sigiri , Wttarak Thanna, Ambuwangala, Imbulgasdeniya', '985482926v', '', NULL, '2026-09-17 11:14:33'),
(79, 'MEMBER', 'AGC/26/080', 81, 'Polgampala Arachilage Rukmani Wasantha Kumari', '197384100734', '1973-12-06', 'Female', '', '03720551669', '', '', 'Paddy Cultivation, Coconut', 'C/76/5 , Abuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'M.L.M Karunawathi', 'C/76/5 , Abuwangala, Imbulgasdeniya', '971580232v', '', NULL, '2026-09-17 11:21:23'),
(80, 'MEMBER', 'AGC/26/081', 83, 'Jayasuriya Arachchilage Karunarathna', '482173233v', '1984-08-04', 'Male', '', '0776605410', '', '', 'Paddy Cultivation', 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-24', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Jayasuriyaa Arachilage Sardha Sripali Jayasuriya', 'Ambuwangala, Imbulgasdeniya', '778023164v', '', NULL, '2026-09-17 11:38:48'),
(81, 'MEMBER', 'AGC/26/082', 84, 'Herathmudiyanselage Somasiri Manike', '197081400155', '1970-11-09', 'Male', '', '0772804505', '', '0772804505', 'Paddy Cultivation', 'Ihalaabuwangala, Imbuldeniya', 'Rambukkana', '2026-03-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'P.R Kosala Gayashan Hemarathna', 'Ihalaabuwangala, Imbuldeniya', '970780718v', '', NULL, '2026-09-17 11:45:11'),
(82, 'MEMBER', 'AGC/26/083', 85, 'Polgampala Arachilage Sisira Hemachandra', '1967736002452', '1967-12-25', 'Male', 'Retired Military', '07100000000', '', '', '', 'C/76/3 Ambuwangala ,Imbuldeniya', 'Rambukkana', '2026-03-24', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'G.H.D Dilrukshi', 'C/76/3 Ambuwangala ,Imbuldeniya', '197354201743', '', NULL, '2026-09-17 11:48:12'),
(83, 'MEMBER', 'AGC/26/084', 86, 'Wannakuwaththe Waduge Don Jayathilaka', '4705421524', '1947-02-23', 'Male', '', '0711858079', '', '', 'Paddy Cultivation', 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-17 11:58:14'),
(84, 'MEMBER', 'AGC/26/085', 87, 'Athukoralage Tikiribanda', '482492788v', '1948-09-05', 'Male', 'Farming', '07199336500', '', '', 'Paddy Cultivation, Vegetable', 'Egodagedara, Imbulgaldeniya', 'Rambukkana', '2026-03-26', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Wasulaa Samanthi Athukorala', 'Egodagedara, Imbulgaldeniya', '', '', NULL, '2026-09-17 12:00:49'),
(85, 'MEMBER', 'AGC/26/086', 88, 'Wijesuriya Basnayaka Mudiyanselage Chandrani Nawarathna Manike', '196973101157', '1969-08-18', 'Female', 'Farming', '0718993292', '', '0718993292', 'Paddy Cultivation', 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-09-18', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'K.R Weerarathna', 'Ambuwangala, Imbulgasdeniya', '195936001234', '', NULL, '2026-09-18 03:37:19'),
(86, 'MEMBER', 'AGC/26/087', 89, 'Meegalle Gamlath Ralalage Anusha Hemali Gamlath', '747040060v', '1974-07-22', 'Female', '', '0702542462', '', '0702542462', 'Paddy Cultivation', 'Kurunduwaththa, Imbulgasdeniya', 'Rambukkana', '2026-04-03', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'G.P Malshani Wasana', 'Kurunduwaththa, Imbulgasdeniya', '200654701568', '', NULL, '2026-09-18 03:40:45'),
(87, 'MEMBER', 'AGC/26/088', 90, 'Senanayaka Mudiyanselage Gunarathna', '540391297v', '1954-02-08', 'Male', 'Retired Grama Niladhari', '0775318160', '', '', 'Paddy Cultivation, Vegetable, Coconut, Cow management', '75, Kurunduwaththa, Imbulgasdeniya', 'Rambukkana', '2026-02-28', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'S.A Jayampath Senanayaka', '75, Kurunduwaththa, Imbulgasdeniya', '', '', NULL, '2026-09-18 04:33:04'),
(88, 'MEMBER', 'AGC/26/089', 91, 'Athukoralage Podimahaththaya', '420803184v', '1941-11-01', 'Male', 'Retired', '0718740167', '', '', 'Coconut, Paddy Cultivation, Banana', 'Rambukkana Road, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'A.S.B Athukorala', 'Rambukkana Road, Imbulgasdeniya', '', '', NULL, '2026-09-18 04:39:20'),
(89, 'MEMBER', 'AGC/26/090', 92, 'Kuruppu Arachchilage Podinilame', '593411486v', '1959-12-06', 'Male', 'Farming', '0779922295', '', '0701050459', 'Paddy Cultivation, Rubber', 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'K.A Mangala Priyadarshana Thurunu Arachchi', 'Ambuwangala, Imbulgasdeniya', '833180630v', '', NULL, '2026-09-18 04:43:47'),
(90, 'MEMBER', 'AGC/26/091', 93, 'Pathiranahalage Gunarath Manike', '195976500213', '1959-09-21', 'Female', '', '0772654301', '', '', 'Paddy Cultivation', 'Paranawaththa, Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-26', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'P. Mallika Pathirana', 'Paranawaththa, Ambuwangala, Imbulgasdeniya', '196550400706', '', NULL, '2026-09-18 04:46:13'),
(91, 'MEMBER', 'AGC/26/092', 94, 'Pathiranahalage Premawathi', '196186300030', '1961-10-28', 'Female', '', '0778181431', '', '', 'Paddy Cultivation', 'C 33/2 Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'P. Mallika Pathirana', 'C 33/2 Ambuwangala, Imbulgasdeniya', '196550400706', '', NULL, '2026-09-18 04:48:23'),
(92, 'MEMBER', 'AGC/26/093', 95, 'Rankonde Mudiyanselage Ranjani Wayalat Kumarihami Wallawa', '195355400586', '1953-02-23', 'Female', '', '0372243483', '', '0718578637', '', 'Deiyanwala walawwa, Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'R.W.D.W.C.A.B Rajakaruna', 'Deiyanwala walawwa, Ambuwangala, Imbulgasdeniya', '', '', NULL, '2026-09-18 05:09:12'),
(93, 'MEMBER', 'AGC/26/094', 96, 'Ranaweera Kaluarachi Mudiyanselage Don Mahesu Sunil Ranaweera', '613430210', '1961-10-08', 'Male', 'Retired Teacher', '0717207798', '', '0719001467', 'Paddy Cultivation, Banana, Vegetable, Fruit, Home Gardening', 'Sirangawitiya, Imbulgasdeniya', 'Rambukkana', '2026-03-23', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'T.M.S.J Bandara', 'Sirangawitiya, Imbulgasdeniya', '196479804223', '', NULL, '2026-09-18 05:14:44'),
(94, 'MEMBER', 'AGC/26/095', 97, 'Pathirathna Mudiyanselage Indika Pathirathna', '770683351v', '1977-03-08', 'Male', 'businessman', '0785000687', '', '0785000687', 'Paddy Cultivation', 'A/8 Ambuwangala Handiya, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'M.A.S.D.R Muhandiram', 'A/8 Ambuwangala Handiya, Imbulgasdeniya', '878192494v', '', NULL, '2026-09-18 05:17:54'),
(95, 'MEMBER', 'AGC/26/096', 98, 'Herath Ralalage Nandumani Godagama', '195054000070', '1950-02-09', 'Female', '', '0372243588', '', '', 'Paddy Cultivation', 'Ambuwangala,Sirangepitiya, Imbulgasdeniya', 'Rambukkana', '2026-03-26', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Anoja Priyangi Godagama', 'Imbulgasdeniya, Kegalla Para', '', '', NULL, '2026-09-18 05:25:02'),
(96, 'MEMBER', 'AGC/26/097', 99, 'Dugganna Walawwe Iranga Nishantha Bandara Ambuwangala', '790880943v', '1979-03-28', 'Male', 'Tailer', '0776343278', '', '0776343278', 'Paddy Cultivation', 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'A.M.M Menaka Kumari', 'Ambuwangala, Imbulgasdeniya', '198751001159', '', NULL, '2026-09-18 05:40:58'),
(97, 'MEMBER', 'AGC/26/098', 100, 'Herath Mudiyanselage Gunarathna Banda', '195511110144', '1955-04-20', 'Male', 'Retired Postmaster', '0718402015', '', '0704444523', 'Paddy Cultivation', 'Sisil Thangodawaththa , Sirangapitiya, Pitawala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'A.K.D Gunarathna', 'Sisil Thangodawaththa , Sirangapitiya, Pitawala, Imbulgasdeniya', '567502813v', '', NULL, '2026-09-18 05:51:14'),
(98, 'MEMBER', 'AGC/26/099', 101, 'D.N.H Madawala', '488293710v', '1948-11-24', 'Male', '', '0767673298', '', '0767673298', 'Paddy Cultivation', 'C 69, Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Sanjaya Chaminda Karunarathna', 'Ambuwangala, Imbulgasdeniya', '', '', NULL, '2026-09-18 05:53:10'),
(99, 'MEMBER', 'AGC/26/100', 102, 'Kande Ralalage Karunasena', '541250387v', '1954-05-05', 'Male', 'Farming', '0777135050', '', '', 'Paddy Cultivation, Banana, Coconut', 'Wattaram Thanna, Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-26', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'K.R Manjuli Ruwan Karunasena', 'Wattaram Thanna, Ambuwangala, Imbulgasdeniya', '860273101v', '', NULL, '2026-09-18 05:55:31'),
(100, 'MEMBER', 'AGC/26/101', 103, 'R.W.D.W Erangaa Krishanthi Rajakaruna', '198850502312', '1988-01-05', 'Female', '', '0779406732', '', '0779406732', 'Paddy Cultivation', 'C 44, Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'P.K Asanka Nuwan Senawirathna', '', '', '', NULL, '2026-09-18 05:58:58'),
(101, 'MEMBER', 'AGC/26/102', 104, 'Athukorala Raalalage Wijewardana', '196308701476', '1963-03-21', 'Male', 'Farming', '0725832020', '', '', 'Paddy Cultivation, Coconut', 'Palapolwaththa, Pitawala, Imbulgasdeniya', 'Rambukkana', '2026-03-27', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-18 06:01:06'),
(102, 'MEMBER', 'AGC/26/103', 105, 'Wallawathage Karunarathna', '551730760v', '1955-06-21', 'Male', 'Farming', '0372244516', '', '0778705141', 'Paddy Cultivation, Banana, Coconut', 'Marukwatura, Imbulgasdeniya', 'Rambukkana', '2026-03-26', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'G. Karunawathi', 'Marukwatura, Imbulgasdeniya', '', '', NULL, '2026-09-18 06:05:49'),
(103, 'MEMBER', 'AGC/26/104', 106, 'Molgampala Arachilage Upul Nishantha', '19801026', '1980-10-26', 'Male', 'Sri Lanka Army', '0717909571', '', '0717909571', 'Paddy Cultivation', 'C/76/1 , Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-24', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'H.R Neluka Saman Kumari', 'C/76/1 , Ambuwangala, Imbulgasdeniya', '848273546', '', NULL, '2026-09-18 06:08:55'),
(104, 'MEMBER', 'AGC/26/105', 107, 'Pathirannahalage Nishanthi Kumari Pathirana', '855411539v', '1985-02-10', 'Female', '', '0701116002', '', '0776226820', 'Paddy Cultivation, Cow management', 'C 52/1 Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'T.Kawshalya Suwahas Thennakoan', 'C 52/1 Ambuwangala, Imbulgasdeniya', '', '', NULL, '2026-09-18 06:17:31'),
(105, 'MEMBER', 'AGC/26/106', 108, 'W.A Nilangani Nawarathna', '196681501477v', '1966-11-10', 'Male', '', '0761874628', '', '0761874628', 'Paddy Cultivation, Banana', 'C 59/1 , Ihala Ambuwamgala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'K.R Indika Ruwan Siriwardana', 'C 59/1 , Ihala Ambuwamgala, Imbulgasdeniya', '', '', NULL, '2026-09-18 06:25:50'),
(106, 'MEMBER', 'AGC/26/107', 109, 'wijesingha Arachilage Sandyaa Nawarathna', '196363101472', '1963-05-10', 'Female', '', '0763712407', '', '0763712407', 'Coconut, Banana', 'C/34/5 , Nalawilla Para, Ihala Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'H.R Nimal Premasiri Karunarathna', 'C/52/3 , Nalawilla Para, Ihala Ambuwangala, Imbulgasdeniya', '196363101472', '', NULL, '2026-09-18 06:29:13'),
(107, 'MEMBER', 'AGC/26/108', 110, 'K.R Kumarasingha', '61355080v', '1961-12-20', 'Male', 'Mason', '0779309021', 'Kumarasinghekr@gmail.com', '', 'Paddy Cultivation, Banana', 'C/59, Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-03-25', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'W.M. Dhammika Priyanthi Weerasuriya', 'C/59, Ambuwangala, Imbulgasdeniya', '697202943v', '', NULL, '2026-09-18 06:37:24'),
(108, 'MEMBER', 'AGC/26/109', 111, 'W.E.W.M.R Sunil Bandara', '5935822485v', '1959-12-23', 'Male', 'Retired', '0711896377', '', '', 'Paddy Cultivation, Rubber, Coconut, Vegetable, Banana', 'Hapugoda, Halpitiya, Hiriwadunna', 'Rambukkana', '2026-04-03', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'W.E.W.M.R Isura Bandara', 'Hapugoda, Halpitiya, Hiriwadunna', '952202146v', '', NULL, '2026-09-18 07:28:33'),
(109, 'MEMBER', 'AGC/26/110', 112, 'W.A Chandra Weerasingha', '617262339', '1961-08-13', 'Female', '', '0778872458', '', '0778872458', '', 'Nagayawaththa, Nwagamuwa, Rambukkana', 'Rambukkana', '2026-04-03', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'I.H.M Saubagya Semini Gunarathna', 'Nagayawaththa , Nawagamuwa', '', '', NULL, '2026-09-18 07:33:49'),
(110, 'MEMBER', 'AGC/26/111', 113, 'Disanayaka Mudiyanselage Dingiribanda Disanayaka', '194403301521', '1944-02-02', 'Male', 'Farming', '0765664291', '', '', 'Paddy Cultivation', 'Nagayawaththa, Nwagamuwa, Rambukkana', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'K.R. Bandara Manike', 'Nagayawaththa, Nwagamuwa, Rambukkana', '', '', NULL, '2026-09-18 07:38:29'),
(111, 'MEMBER', 'AGC/26/112', 114, 'Munasingha Arachchige Mangalika Munasingha', '528452442v', '1952-10-10', 'Female', 'Retired', '0716490385', '', '', 'Paddy Cultivation, Banana, Coconut', 'Nuwan, Galwala Road, Rambukkana', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'H.M Nuwan Prasanna Herath', 'Nuwan, Galwala Road, Rambukkana', '', '', NULL, '2026-09-18 09:20:57'),
(112, 'MEMBER', 'AGC/26/113', 115, 'Hitihawellage Sandasiri Mallika', '525833267v', '1952-03-23', 'Female', '', '0718662847', '', '', 'Paddy Cultivation', 'Sandalla, Handagama, Rambukkana', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'H.M Kosala Amarasingha', 'Sandalla, Handagama, Rambukkana', '', '', NULL, '2026-09-18 09:28:46'),
(113, 'MEMBER', 'AGC/26/114', 116, 'Herathmudiyanselage Wasanthaa Herath', '196553800728', '1965-02-07', 'Female', '', '0740710182', '', '', 'Paddy Cultivation', 'D/132, Diganawaththa, Molagoda', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'H.M Ranjith Jayantha', 'D/132, Diganawaththa, Molagoda', '', '', NULL, '2026-09-18 11:19:52'),
(114, 'MEMBER', 'AGC/26/115', 117, 'Herath Mudiyanselage Jayantha Bandara', '196203303964', '1962-02-02', 'Male', 'Farming', '0769005696', '', '', 'Paddy Cultivation', 'Galwala Para, Bathabhuraya, Rambukkana', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'H.M Gayan Kawindha Bandara', 'Galwala Para, Bathabhuraya, Rambukkana', '', '', NULL, '2026-09-18 11:22:00'),
(115, 'MEMBER', 'AGC/26/116', 118, 'Hadagama Mudaligedara Mudalige Ananda Handagama', '592170922v', '1959-08-04', 'Male', 'Air Force Retired', '0718499751', '', '0718499751', '', 'B/5/2 , Maduwasala , Nagamuwa, Rambukkana', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Pasindu Handagama', 'B/5/2 , Maduwasala , Nagamuwa, Rambukkana', '', '', NULL, '2026-09-18 11:26:12'),
(116, 'MEMBER', 'AGC/26/117', 119, 'Mudali Mahipala Appuhamilage Yamuna Ariyalatha', '608303898v', '1960-11-25', 'Female', 'Manual labor / Wage labor', '0787833515', '', '', 'Paddy Cultivation', 'C/92, Nawagamuwa, Rambukkana', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-19 04:18:21'),
(117, 'MEMBER', 'AGC/26/118', 120, 'Weragodayalage Kularathna', '593624706v', '1959-10-27', 'Male', 'Farming', '0740046250', '', '', '', 'Parape, Rambukkana', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-19 04:20:59'),
(118, 'MEMBER', 'AGC/26/119', 121, 'Henaka Ralalage Amarasingha', '563273950', '1956-11-22', 'Male', 'Farming', '0710437980', '', '', 'Paddy Cultivation', 'Kagalla para, Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-09-19', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Maduranga Pradeep Amarasingha', 'Kagalla para, Ambuwangala, Imbulgasdeniya', '', '', NULL, '2026-09-19 04:23:23'),
(119, 'MEMBER', 'AGC/26/120', 122, 'Ilangakoan Mudiyanselage Jayasuriya', '6022900260', '1960-08-16', 'Male', 'Retired', '0771377950', '', '0771377950', 'Paddy Cultivation, Coconut, Cow management, Banana', 'Warukwathura, Imbulgasdeniya', 'Rambukkana', '2026-04-05', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'I.M.A.L Ilangakoan', 'Warukwathura, Imbulgasdeniya', '200524400519', '', NULL, '2026-09-19 04:53:52'),
(120, 'MEMBER', 'AGC/26/121', 123, 'Hatan Arachilage Ranjani Sriyanjali Hatanarachchi', '936172032v', '1993-04-26', 'Female', 'School Development Officer', '0703609634', '', '0703609634', 'Paddy Cultivation, Rubber, Coconut, Banana', 'Gunasiri, Marukwathura, Imbulgasdeniya', 'Rambukkana', '2026-04-05', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'W.A Nalinda Prasad Withanarachchi', 'Gunasiri, Marukwathura, Imbulgasdeniya', '199436102544', '', NULL, '2026-09-19 04:59:07'),
(121, 'MEMBER', 'AGC/26/122', 124, 'G.R.D Manike', '196858800300', '1968-03-28', 'Male', 'Assistant Director', '0718318021', '', '0718318021', 'Paddy Cultivation, Coconut', 'Thuthotawaththa, Halpitiya, Hiriwadunna', 'Rambukkana', '2026-03-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'W.H Piyathissa', 'Thuthotawaththa, Halpitiya, Hiriwadunna', '690230887v', '', NULL, '2026-09-19 05:07:55'),
(122, 'MEMBER', 'AGC/26/123', 125, 'N.M Nawarathna Sarath Bandara', '752921415v', '1975-10-18', 'Male', 'Security sector', '0704691930', '', '', 'Paddy Cultivation', 'Halpitiya , Hiriwadunna', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'G.R Priyantha Chandrakanthi', 'Halpitiya , Hiriwadunna', '197779501980', '', NULL, '2026-09-19 05:11:22'),
(123, 'MEMBER', 'AGC/26/124', 126, 'A.D Ralahami', '662090913v', '1966-07-27', 'Male', 'Carpenter', '0768301447', '', '0702909026', 'Paddy Cultivation, Coconut', 'Naathagahamulla, Halpitiya, Hiriwadunna', 'Rambukkana', '2026-04-05', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'A.S.S Disanayaka', '', 'Naathagahamulla, Halpitiya, Hiriwadunna', '', NULL, '2026-09-19 05:14:25'),
(124, 'MEMBER', 'AGC/26/125', 127, 'Herath Mudiyanselage Ralahamilage Jayasiri Bandara', '603384148v', '1960-12-03', 'Male', 'Farming', '0767164276', '', '0768555534', 'Paddy Cultivation', 'Sirisewana, Halpitiya , Hiriwadunna', 'Rambukkana', '2026-04-03', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'H.M.R Rajitha Madusanka Bandara', 'Sirisewana, Halpitiya , Hiriwadunna', '89324181v', '', NULL, '2026-09-19 05:21:37'),
(125, 'MEMBER', 'AGC/26/126', 128, 'Hallama Appuhamilage Chandrarathna Piyasiri', '74278165v', '1974-10-04', 'Male', 'Farming', '0718124574', '', '0718124574', 'Paddy Cultivation, Banana, Coconut, Cow management, Cinnamon, puwak, Black pepper', 'Halpitiya , Hiriwadunna', 'Rambukkana', '2026-04-05', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'M.C.S Piyasena', 'Halpitiya , Hiriwadunna', '742781515v', '', NULL, '2026-09-19 05:26:41'),
(126, 'MEMBER', 'AGC/26/127', 129, 'M.R Kamani Pushpakumari', '197480800783', '1974-11-03', 'Male', '', '0725311907', '', '', 'Paddy Cultivation', 'Hapugoda, Halpitiya', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-19 05:48:27');
INSERT INTO `coop_members` (`id`, `member_type`, `member_no`, `party_id`, `full_name`, `nic`, `dob`, `gender`, `occupation`, `phone`, `email`, `whatsapp`, `agricultural_sector`, `address`, `city`, `registration_date`, `membership_type`, `status`, `registration_fee`, `shares_fee`, `payment_method`, `payment_status`, `notes`, `heir_name`, `heir_address`, `heir_nic`, `heir_contact_number`, `journal_entry_id`, `created_at`) VALUES
(127, 'MEMBER', 'AGC/26/128', 130, 'Chandrasekara Mudiyanselage Dhammika Jayathilaka', '801220614v', '1980-05-01', 'Male', 'Farming', '0776049103', '', '', '', 'Hapugoda, Halpitiya, Hiriwadunna', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Pediththa Siriyaananda', 'Hapugoda, Halpitiya, Hiriwadunna', '198071103429', '', NULL, '2026-09-19 05:50:38'),
(128, 'MEMBER', 'AGC/26/129', 131, 'Upasaka Raalalage Renuka Jayasundara', '626410529v', '1962-02-20', 'Male', '', '0713068573', '', '', 'Paddy Cultivation', 'Halpitiya , Hiriwadunna', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'U.R Tikirimanike', 'Halpitiya , Hiriwadunna', '516833750v', '', NULL, '2026-09-19 06:30:11'),
(129, 'MEMBER', 'AGC/26/130', 132, 'Arachchilage Ranbanda', '194916404680', '1949-06-12', 'Male', 'Carpenter', '0741872685', '', '', 'Paddy Cultivation', 'D108, Hapugoda, Halpitiya, Hiriwadunna', 'Rambukkana', '2026-04-03', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Arachchilage Saman Kumara', 'D108, Hapugoda, Halpitiya, Hiriwadunna', '781222267v', '', NULL, '2026-09-19 06:33:05'),
(130, 'MEMBER', 'AGC/26/131', 133, 'Malawi Arachilage Podibanda', '530212947v', '1953-01-21', 'Male', 'Farming', '0703005030', '', '', 'Paddy Cultivation', 'D.62, Hapugoda, Hiriwadunna', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Malawi Arachilage Chamara Pushpakumara', 'D.62, Hapugoda, Hiriwadunna', '893292837v', '', NULL, '2026-09-19 06:40:32'),
(131, 'MEMBER', 'AGC/26/132', 134, 'Pahala Kangarage Chathuranga Aberathna', '950852070v', '1995-03-25', 'Male', '', '0789890896', '', '0789890896', 'Paddy Cultivation, Coconut', '79, Karandagasthanna, Hiriwadunna', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-19 06:46:00'),
(132, 'MEMBER', 'AGC/26/133', 135, 'Liyanaralalage Punchimahaththaya', '586431226v', '1958-05-22', 'Male', '', '0767191651', '', '', 'Paddy Cultivation', 'Hapugoda, Halpitiya, Hiriwadunna', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'P.V.R Anjana Madushanka Gunarathna', 'Hapugoda, Halpitiya, Hiriwadunna', '', '', NULL, '2026-09-19 06:49:56'),
(133, 'MEMBER', 'AGC/26/134', 136, 'H.Gunathilaka', '563360666v', '1956-12-01', 'Male', 'Farming', '0711902586', '', '', 'Paddy Cultivation', 'Thunpala Waththa, Hapugoda, Hiriwadunna', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Marasingha Arachchilage Seetha Manike', 'Thunpala Waththa, Hapugoda, Hiriwadunna', '648212372v', '', NULL, '2026-09-19 06:53:25'),
(134, 'MEMBER', 'AGC/26/135', 137, 'Hitihamillage Duminda Sampath Aberathna', '198224902053', '1982-09-05', 'Male', 'Air Force Officer', '0767774039', '', '0713930331', 'Paddy Cultivation, Rubber, Fruit, Banana, Cinnamon, Black pepper, Turmeric, Coconut', 'D/73/1 , Hapugoda , Halpitiya, Hiriwadunna', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Hitihamillage Sasadu Minoj', 'D/73/1, Hapugoda , Halpitiya', '', '', NULL, '2026-09-19 08:00:14'),
(135, 'MEMBER', 'AGC/26/136', 138, 'Siman Hewage Sunil Shantha', '197429102361', '1974-10-17', 'Male', 'Farming', '0775876101', '', '', 'Paddy Cultivation', 'pillawa, Hapugoda, Halpitiya', 'Rambukkana', '2026-04-05', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-19 08:02:01'),
(136, 'MEMBER', 'AGC/26/137', 139, 'Widana Pathirana Nihal Priyantha Dharmadasa', '196807302515', '1968-03-13', 'Male', 'Army Retired', '0701288136', '', '0704957014', 'Paddy Cultivation', 'Kotuwakale, Kapugoda, Halpitiya, Hiriwadunna', 'Rambukkana', '2026-04-05', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'W.P Kanishka Chathurangani Dharmadasa', 'Kotuwakale, Kapugoda, Halpitiya, Hiriwadunna', '', '', NULL, '2026-09-19 08:04:50'),
(137, 'MEMBER', 'AGC/26/138', 140, 'Heralibadage Dilip Ashoka Karunathna', '673342841v', '1967-11-29', 'Male', 'Farming', '0777646951', '', '0777646951', 'Paddy Cultivation, Banana, Rubber, Cow management, Fruit, Vegetable', 'Ajantha Niwasa, Thalakolayaya, Hewadiwela', 'Rambukkana', '2026-03-20', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Manjula Niroshani Gunarathna', 'Ajantha Niwasa, Thalakolayaya, Hewadiwela', '755920274v', '', NULL, '2026-09-19 08:08:56'),
(138, 'MEMBER', 'AGC/26/139', 141, 'S.L.B.K Wikramarathna', '832690236v', '1983-09-25', 'Male', 'businessman', '0713792238', '', '0713792238', 'Paddy Cultivation, Coconut, Vegetable, Fruit', 'Pahala Higapitiya, Hewadiwela, Rambukkana', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'P.K.A.A.D Gunathiaka', 'Pahala Higapitiya, Hewadiwela, Rambukkana', '865431473v', '', NULL, '2026-09-19 08:13:38'),
(139, 'MEMBER', 'AGC/26/140', 142, 'Senakaralalage Egodagedara Nirosh Dhammika Abaya Bandara', '197234804006', '1972-10-13', 'Male', 'Farming', '0711711786', '', '0770160183', 'Paddy Cultivation, Coconut, fish, Banana', 'Waligamuwa, Kotawella', 'Rambukkana', '2026-03-31', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'H.M Nimesha Janadhi Herath', 'Waligamuwa, Kotawella', '855390604v', '', NULL, '2026-09-19 08:16:35'),
(140, 'MEMBER', 'AGC/26/141', 143, 'Muthugamaraalalage Upali Chandrasena', '553313244v', '1955-11-26', 'Male', 'Farming', '0776236987', '', '', 'Paddy Cultivation, Banana, Vegetable, Fruit, Coconut', 'F/89, Weligamuwa , Kotawella', 'Rambukkana', '2026-04-07', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'T.M.K.N Thennakoan', 'F/89, Weligamuwa , Kotawella', '605921779v', '', NULL, '2026-09-19 08:18:46'),
(141, 'MEMBER', 'AGC/26/142', 144, 'Kanaththegedara , Podiralahami', '19491521848v', '1949-05-31', 'Male', 'Retired', '0764825342', '', '', 'Paddy Cultivation, Fruit, Banana, Vegetable, Coconut', 'Weligamuwa, Kotawella, Rambukkana', 'Rambukkana', '2026-04-10', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'M.P. Nandawathi Manike', 'Weligamuwa, Kotawella, Rambukkana', '', '', NULL, '2026-09-19 10:30:33'),
(142, 'MEMBER', 'AGC/26/143', 145, 'Hewapathirana Padma Weerasingha', '5382249v', '1949-02-07', 'Male', 'Retired Principle', '0743802767', '', '', 'Paddy Cultivation, Coconut, Cow management', '4th lane, sathiamte waththa, Imbulgasdeniya', 'Rambukkana', '2026-04-07', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'B.D.C.S.K Somadasa', '4th lane, sathiamte waththa, Imbulgasdeniya', '412312556v', '', NULL, '2026-09-19 10:34:10'),
(143, 'MEMBER', 'AGC/26/144', 146, 'D.G.Anar Perera', '510494580v', '1951-02-18', 'Male', 'Retired', '0714238502', '', '', 'Paddy Cultivation, Coconut', '50/C Sentimant Waththa, Imbulgasdeniya', 'Rambukkana', '2026-05-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'D.G.S Kawshalya', '50/C Sentimant Waththa, Imbulgasdeniya', '887302898v', '', NULL, '2026-09-19 10:41:52'),
(144, 'MEMBER', 'AGC/26/145', 147, 'Sacrage Reeta Priyamani', '195654201182', '1956-02-11', 'Male', '', '0763811723', '', '', 'Paddy Cultivation', 'Sillugala,Kudagama,Dodamemada', 'Rambukkana', '2026-04-10', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Supun Somasiri', 'Sillugala,Kudagama,Dodamemada', '', '', NULL, '2026-09-19 10:46:15'),
(145, 'MEMBER', 'AGC/26/146', 148, 'Arachchilage Thilakarathna', '195834602667', '1958-12-11', 'Male', 'Farming', '0779910197', '', '', 'Paddy Cultivation', 'D/89, Hapugoda, Halpitiya, Hiriwatunna', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'W.L.Silawathi', 'D/89, Hapugoda, Halpitiya, Hiriwatunna', '687043251v', '', NULL, '2026-09-19 10:49:20'),
(146, 'MEMBER', 'AGC/26/147', 149, 'R.A Sarath Chandrasiri', '613353917v', '1961-11-30', 'Male', 'Industrial', '0775506205', '', '', 'Paddy Cultivation', 'D.24. Kotakanda, Halpitiya, Hiriwadunna', 'Rambukkana', '2026-04-04', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'R.A Damith Kumara Chandasiri', 'D.24. Kotakanda, Halpitiya, Hiriwadunna', '871780196v', '', NULL, '2026-09-19 10:52:03'),
(147, 'MEMBER', 'AGC/26/148', 150, 'Muhandiram Ralalage Pradeep Nishantha Bandara', '852884363', '1985-10-14', 'Male', 'Driver', '0778306572', '', '', 'Paddy Cultivation, Coconut, Banana', 'D/3 , Kotakanda , Halpitiya, Hiriwadunna', 'Rambukkana', '2026-04-06', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-19 10:54:11'),
(148, 'MEMBER', 'AGC/26/149', 151, 'Herath Mudiyanselage Podomanike', '608241957v', '1960-11-19', 'Male', '', '0353221111', '', '0716682559', '', 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-04-26', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'K.R Danushka Suraj Bandara Kulathunga', 'Ambuwangala, Imbulgasdeniya', '903082968v', '', NULL, '2026-09-19 10:56:28'),
(149, 'MEMBER', 'AGC/26/150', 152, 'Kutihoyalaa Gedara Nihal Rupasingha', '730372914v', '1973-02-06', 'Male', 'Agricultural Research Officer', '0703817354', '', '0702825460', '', 'Hadagama, Pinnawala', 'Rambukkana', '2026-04-28', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'T.G.T.S Rupasinga', 'Hadagama, Pinnawala, Rambukkana', '730372914v', '0703817354', NULL, '2026-09-19 11:00:46'),
(150, 'MEMBER', 'AGC/26/151', 153, 'G.R Damayanthi', '858444527v', '1985-11-09', 'Male', 'Garment Service', '0768836925', '', '0768836925', 'Paddy Cultivation', 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-04-10', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-19 11:02:39'),
(151, 'MEMBER', 'AGC/26/152', 154, 'Kosgollalaa Gedara Sumith Vimalasiri', '710943583v', '1971-04-03', 'Male', 'Manual labor / Wage labor', '0703137123', '', '', 'Paddy Cultivation', 'Boralla, Pahalagama, Parape, Rambukkana', 'Rambukkana', '2026-04-28', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'K.G Premalatha', 'Boralla, Pahalagama', '725241542', '', NULL, '2026-09-19 11:07:13'),
(152, 'MEMBER', 'AGC/26/153', 155, 'Ranathunga Dewayalage Weerasingha', '195000700587', '1950-01-07', 'Male', 'Farming', '0716366818', '', '', 'Paddy Cultivation', 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', '2026-05-09', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'M.D.N.C.K Weerasingha', 'Pahalagama ,Parape ,Rambukkana', '80142351v', '', NULL, '2026-09-19 11:09:10'),
(153, 'MEMBER', 'AGC/26/154', 156, 'Mahathunga Dewayalage Nirosh Saman Kumara Weerasingha', '821751357v', '1982-06-23', 'Male', 'Farming', '0716366818', '', '0716366818', 'Paddy Cultivation', 'A/57/6, Pahalagama, Parape, Rambukkana', 'Rambukkana', '2026-05-11', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'W.O. Sunanda Minilawathi Ariyarathna', 'A/57/6, Pahalagama, Parape, Rambukkana', '848520691v', '', NULL, '2026-09-19 11:12:26'),
(154, 'MEMBER', 'AGC/26/155', 161, 'W.G.B Chandrasiri', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 06:53:57'),
(155, 'MEMBER', 'AGC/26/156', 162, 'G.P Sirimanna', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:15:05'),
(156, 'MEMBER', 'AGC/26/157', 163, 'G.R Asela', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:15:29'),
(157, 'MEMBER', 'AGC/26/158', 164, 'G.G Karunarathna', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:15:48'),
(158, 'MEMBER', 'AGC/26/159', 165, 'G.R Damayanthi', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:16:28'),
(159, 'MEMBER', 'AGC/26/160', 166, 'A.Ariyapala', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:16:45'),
(160, 'MEMBER', 'AGC/26/161', 167, 'S.H Karunawathi', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:17:05'),
(161, 'MEMBER', 'AGC/26/162', 168, 'H.R Somadasa', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:17:37'),
(162, 'MEMBER', 'AGC/26/163', 169, 'S.H Piyaseeli', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:18:05'),
(163, 'MEMBER', 'AGC/26/164', 170, 'P.G Chithtra Airangani', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:18:34'),
(164, 'MEMBER', 'AGC/26/165', 171, 'K.G Sumith Wimalasiri', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-04-28', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:19:54'),
(165, 'MEMBER', 'AGC/26/166', 172, 'M.D Saman Kumara Weerasingha', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:20:27'),
(166, 'MEMBER', 'AGC/26/167', 173, 'M.D Weerasingha', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-04-28', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:24:55'),
(167, 'MEMBER', 'AGC/26/168', 174, 'K.M Shriyani Premalatha', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-04-28', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:25:26'),
(168, 'MEMBER', 'AGC/26/169', 175, 'D.Edirimuni Dewayalage Manel Swarnalatha', '197064301039', '1970-05-22', 'Female', '', '0772883490', '', '', 'Paddy Cultivation, Rubber, Chili', 'Imbulthanna, Pahalagama, Parape', 'Rambukkana', '2026-04-28', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:39:36'),
(169, 'MEMBER', 'AGC/26/170', 176, 'N.D.N.P Edirisingha', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:40:53'),
(170, 'MEMBER', 'AGC/26/171', 177, 'Kamala Wijesundara', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:41:17'),
(171, 'MEMBER', 'AGC/26/172', 178, 'Lakmal Kumara Rathnayaka', NULL, NULL, 'Male', '', '', '', '', '', 'Parape', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:41:39'),
(172, 'MEMBER', 'AGC/26/173', 179, 'Ajanthaa Malawita', NULL, NULL, 'Male', '', '', '', '', '', '', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:41:59'),
(173, 'MEMBER', 'AGC/26/174', 180, 'W.W Gunathilaka', NULL, NULL, 'Male', '', '', '', '', '', 'Thithmalpola', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:42:32'),
(174, 'MEMBER', 'AGC/26/175', 181, 'H.W Chandrawathi', NULL, NULL, 'Male', '', '', '', '', '', 'Thithmalpola', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:42:54'),
(175, 'MEMBER', 'AGC/26/176', 182, 'Asanka Dilanka Karunarathna', NULL, NULL, 'Male', '', '', '', '', '', 'Thithmalpola', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:43:16'),
(176, 'MEMBER', 'AGC/26/177', 183, 'Malani Jayalathaa', NULL, NULL, 'Male', '', '', '', '', '', 'Thithmalpola', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:43:37'),
(177, 'MEMBER', 'AGC/26/178', 184, 'Gamini Thennakoan', NULL, NULL, 'Male', '', '', '', '', '', 'Thithmalpola', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:44:00'),
(178, 'MEMBER', 'AGC/26/179', 185, 'Anulaa Kumarihami', NULL, NULL, 'Female', '', '', '', '', '', 'Thithmalpola', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:44:41'),
(179, 'MEMBER', 'AGC/26/180', 186, 'H.M Gihan Madushanka', NULL, NULL, 'Male', '', '', '', '', '', 'Thithmalpola', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:45:25'),
(180, 'MEMBER', 'AGC/26/181', 187, 'N.P Wijesingha', NULL, NULL, 'Male', '', '', '', '', '', 'Kudagama', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:45:48'),
(181, 'MEMBER', 'AGC/26/182', 188, 'Herath Mudiyanselage Ranbanda', '510691016v', '1951-03-09', 'Male', 'Retired', '0352266850', '', '', 'Banana, Coconut, Paddy Cultivation', 'Ihalawalpola, Kotawella, Rambukkana', 'Rambukkana', '2026-06-10', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Oruthota Arachchige Lalitha Karunanayaka', 'Ihalawalpola, Kotawella, Rambukkana', '535920295v', '', NULL, '2026-09-22 07:48:20'),
(182, 'MEMBER', 'AGC/26/183', 189, 'B.A Wedhani Ibekaa', NULL, NULL, 'Male', '', '', '', '', '', 'Ambuwangala, Imbulgasdeniya', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:50:48'),
(183, 'MEMBER', 'AGC/26/184', 190, 'H.A DayawathiManike', NULL, NULL, 'Male', '', '', '', '', '', 'Ambuwangala, Imbulgasdeniya', '', '2026-09-22', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 07:51:06'),
(184, 'MEMBER', 'AGC/26/185', 191, 'Udakarandupana Dewapurayalage Kusumawathi', '547023218v', '1954-07-20', 'Female', '', '0773888256', '', '', 'Paddy Cultivation', 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', '2026-04-10', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Weerapurayalage Asanka Iroshan Gunathilaka', 'C/29/11 Ambuwangala, Imbulgaspitiya', '198333902549', '', NULL, '2026-09-22 07:58:59'),
(185, 'MEMBER', 'AGC/26/186', 192, 'Kotamagala Gedara Premadasa', '5233733311', '1952-12-02', 'Male', '', '0352265410', '', '0718241186', '', '87/29 Isuru Pedesa, Rohala para, Rambukkana', 'Rambukkana', '2026-04-07', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'K.G.S.I Kobbagala', '87/29 Isura Pokuna, Rohala Para, Rambukkana', '997463820v', '', NULL, '2026-09-22 08:12:35'),
(186, 'MEMBER', 'AGC/26/187', 193, 'Jayasingha Mudiyanselage Nithaa Mangalika', '69630170v', '1969-04-12', 'Female', '', '0776284324', '', '0776284324', '', '22/72 A Mihidu Mawatha, Rambukkana', 'Rambukkana', '2026-04-27', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', '', '', '', '', NULL, '2026-09-22 08:16:27'),
(187, 'MEMBER', 'AGC/26/188', 194, 'A.A Sarath Amarasingha', '602140105v', '1960-08-01', 'Male', '', '074475290', '', '', '', 'Aludeniya Mawatha, Rohala Para', 'Rambukkana', '2026-04-18', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'A.A Adithya Nihara Amarasingha', 'Aludeniya Mawatha, Rohala Para', '', '', NULL, '2026-09-22 08:18:32'),
(188, 'MEMBER', 'AGC/26/189', 195, 'Vikter Vikramasingha', '592530929v', '1959-09-09', 'Male', 'Retired', '0352265546', 'apvw59@gmail.com', '0718203853', '', '28, Mihidu Mawatha, Rambukkana', 'Rambukkana', '2026-03-03', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Vikramasuriyage Geethani Kaanthi Latha Dharmasiri', '28, Mihidu Mawatha, Rambukkana', '608230670v', '', NULL, '2026-09-22 08:26:59'),
(189, 'MEMBER', 'AGC/26/190', 196, 'Hemba Githiyanage Gamara Priyanthi Silwa', '677790881v', '1967-10-05', 'Female', 'Nurse', '0712495365', '', '0712495365', '', 'No 29/2 , Mihidu Mawatha , Rambukkana', 'Rambukkana', '2026-04-02', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'W.P Shiwaththi Tharushika', 'No 29/2 , Mihidu Mawatha , Rambukkana', '199874100060', '', NULL, '2026-09-22 08:33:16'),
(190, 'MEMBER', 'AGC/26/191', 197, 'Rangallalage Umesh Imathka Rangalla', '200200701728', '2002-01-04', 'Male', '', '0760720522', 'umeshimantha360@gmail.com', '0760720522', 'Rubber, Paddy Cultivation', 'Godagampala, Daliwala, Rambukkana', 'Rambukkana', '2026-08-09', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'D. Pathmalatha', 'Godagampala, Daliwala, Rambukkana', '687973860v', '', NULL, '2026-09-22 08:38:36'),
(191, 'MEMBER', 'AGC/26/192', 198, 'Aluthwaththe Gedara Salikaa Dilrukshi Wijewardhana', '966382201v', '1996-05-17', 'Female', '', '0711820946', 'shalikadilrukshi290@gmail.com', '0711820946', 'Paddy Cultivation', 'Godagampala, Daliwala, Rambukkana', 'Rambukkana', '2026-08-09', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'R.Dhanuka Shehan', 'Godagampala, Daliwala, Rambukkana', '912191290v', '', NULL, '2026-09-22 08:41:07'),
(192, 'MEMBER', 'AGC/26/193', 199, 'Ranjakanthe Gedara Pushpakumara Wijesingha', '197325903250', '1973-09-15', 'Male', 'Retired Army personnel', '0701218051', '', '070121851', 'Banana, Coconut, Fruit', 'Madagama ,Parape', 'Rambukkana', '2026-08-09', 'Ordinary', 'ACTIVE', 0.00, 0.00, 'Unpaid', 'UNPAID', '', 'Dbhagaha Gedara Salmini Priyangika', 'Madagama ,Parape', '', '', NULL, '2026-09-22 08:45:09');

-- --------------------------------------------------------

--
-- Table structure for table `cost_centers`
--

CREATE TABLE `cost_centers` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cost_centers`
--

INSERT INTO `cost_centers` (`id`, `code`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'CC-001', 'Agricultural Services', 'Plowing & Agricultural Field Services', 1, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(2, 'CC-002', 'Machinery Rental', 'Rental of Washers, Generators, Grills & Equipment', 1, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(3, 'CC-003', 'Marketplace', 'Buying & Selling of Agricultural & Trading Products', 1, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(4, 'CC-004', 'Plantation', 'Crop Growing & Agricultural Production Projects', 1, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(5, 'CC-005', 'Brick Manufacturing', 'Brick Production & Manufacturing Unit', 1, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(6, 'CC-006', 'Fruit Packing', 'Fruit Procurement, Packing & Processing', 1, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(7, 'CC-007', 'Construction', 'Construction Contracts & Engineering Projects', 1, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(8, 'CC-008', 'Grinding Mill', 'Grinding Service & Packaged Product Production', 1, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(9, 'CC-009', 'Administration', 'General Administration, Overhead & Management', 1, '2026-08-14 21:24:52', '2026-08-14 21:24:52');

-- --------------------------------------------------------

--
-- Table structure for table `customer_activities`
--

CREATE TABLE `customer_activities` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `status` varchar(50) DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deposit_items`
--

CREATE TABLE `deposit_items` (
  `id` int(11) NOT NULL,
  `deposit_id` int(11) NOT NULL,
  `cheque_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `item_type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `expense_number` varchar(50) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `expense_date` date NOT NULL,
  `expense_category_id` int(11) NOT NULL,
  `cost_center_id` int(11) DEFAULT NULL,
  `payee` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `cash_account_id` int(11) DEFAULT NULL,
  `bank_account_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `service_job_id` int(11) DEFAULT NULL,
  `machinery_id` int(11) DEFAULT NULL,
  `machinery_rental_id` int(11) DEFAULT NULL,
  `source_module` varchar(50) DEFAULT 'GENERAL',
  `source_type` varchar(50) DEFAULT 'GENERAL_EXPENSE',
  `source_transaction_id` int(11) DEFAULT NULL,
  `expense_account_id` int(11) DEFAULT NULL,
  `accounts_payable_account_id` int(11) DEFAULT NULL,
  `status` enum('draft','pending_approval','approved','posted','cancelled','reversed') DEFAULT 'draft',
  `journal_entry_id` int(11) DEFAULT NULL,
  `reversal_journal_entry_id` int(11) DEFAULT NULL,
  `reversal_reason` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `posted_by` int(11) DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `reversed_by` int(11) DEFAULT NULL,
  `reversed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_attachments`
--

CREATE TABLE `expense_attachments` (
  `id` int(11) NOT NULL,
  `expense_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `linked_account_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `linked_account_id`, `is_active`) VALUES
(1, 'Labour charges', 1, 1),
(2, 'Fertilizers & Chemicals', 1, 1),
(3, 'Seeds & Plants', 1, 1),
(4, 'Machinery & Equipment', 1, 1),
(5, 'Fuel & Transport', 1, 1),
(6, 'Irrigation & Water', 1, 1),
(7, 'Maintenance & Repairs', 1, 1),
(8, 'Miscellaneous', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_balances`
--

CREATE TABLE `inventory_balances` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `quantity_on_hand` decimal(15,4) DEFAULT 0.0000,
  `inventory_value` decimal(15,4) DEFAULT 0.0000,
  `average_cost` decimal(15,4) DEFAULT 0.0000,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_locations`
--

CREATE TABLE `inventory_locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_locations`
--

INSERT INTO `inventory_locations` (`id`, `code`, `name`, `is_active`, `created_at`) VALUES
(1, 'LOC-MAIN', 'Main Store', 1, '2026-08-14 23:28:07');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `warehouse_id` int(10) UNSIGNED DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `reversal_reason` text DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT 'CASH',
  `discount` decimal(15,2) DEFAULT 0.00,
  `total` decimal(15,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'DRAFT',
  `journal_entry_id` int(10) UNSIGNED DEFAULT NULL,
  `reversal_journal_entry_id` int(10) UNSIGNED DEFAULT NULL,
  `cash_account_id` int(10) UNSIGNED DEFAULT NULL,
  `bank_account_id` int(10) UNSIGNED DEFAULT NULL,
  `cheque_id` int(10) UNSIGNED DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `customer_id`, `warehouse_id`, `invoice_date`, `reference`, `notes`, `reversal_reason`, `payment_type`, `discount`, `total`, `status`, `journal_entry_id`, `reversal_journal_entry_id`, `cash_account_id`, `bank_account_id`, `cheque_id`, `subtotal`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'INV - 001', 7, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 15, NULL, 1, NULL, NULL, 2000.00, 1, '2026-09-22 10:38:33', '2026-09-24 05:40:46'),
(2, 'INV - 002', 9, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 6, NULL, 1, NULL, NULL, 2000.00, 1, '2026-09-22 11:17:06', '2026-09-22 12:08:45'),
(3, 'INV - 003', 33, 1, '2025-06-22', '', '', 'Invoice cancelled', 'CASH', 0.00, 2000.00, 'CANCELLED', 3, 11, 1, NULL, NULL, 2000.00, 1, '2026-09-22 11:20:43', '2026-09-24 05:43:14'),
(4, 'INV - 004', 10, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 16, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-22 11:23:17', '2026-09-24 05:41:23'),
(5, 'INV - 005', 1, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 17, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-22 11:27:36', '2026-09-24 05:41:41'),
(8, 'INV - 006', 4, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 19, NULL, 1, NULL, NULL, 2000.00, 1, '2026-09-24 05:02:58', '2026-09-24 05:42:13'),
(9, 'INV - 007', 2, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 20, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 05:40:06', '2026-09-24 05:42:44'),
(10, 'INV - 008', 12, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 18, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 05:42:10', '2026-09-24 05:42:10'),
(11, 'INV - 009', 34, 1, '2025-06-22', '', '', 'Invoice cancelled', 'CASH', 0.00, 2000.00, 'CANCELLED', 21, 22, 1, NULL, NULL, 2000.00, 6, '2026-09-24 05:45:46', '2026-09-24 06:13:09'),
(12, 'INV - 010', 15, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 23, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 06:14:03', '2026-09-24 06:14:03'),
(13, 'INV - 011', 8, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 25, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 06:14:42', '2026-09-24 06:14:59'),
(14, 'INV - 012', 14, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 26, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 06:15:49', '2026-09-24 06:15:49'),
(15, 'INV - 013', 32, 1, '2025-09-24', '', '', 'Invoice cancelled', 'CASH', 0.00, 2000.00, 'CANCELLED', 27, 28, 1, NULL, NULL, 2000.00, 6, '2026-09-24 06:20:17', '2026-09-24 06:20:28'),
(16, 'INV - 014', 200, NULL, '2026-09-24', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 1, '2026-09-24 07:18:22', '2026-09-24 07:18:22'),
(17, 'INV - 015', 35, 1, '2026-09-24', '', '', 'Cancel in Invoice Box', 'CASH', 0.00, 2000.00, 'CANCELLED', 29, 30, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:22:10', '2026-09-24 07:22:48'),
(18, 'INV - 016', 36, 1, '2026-09-24', '', '', 'In Invoice Book', 'CASH', 0.00, 2000.00, 'CANCELLED', 31, 32, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:23:49', '2026-09-24 07:24:01'),
(19, 'INV - 017', 200, NULL, '2026-09-24', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-24 07:25:10', '2026-09-24 07:25:10'),
(20, 'INV - 018', 3, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 33, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:26:31', '2026-09-24 07:26:31'),
(21, 'INV - 019', 6, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 34, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:31:43', '2026-09-24 07:31:43'),
(22, 'INV - 020', 13, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 35, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:33:33', '2026-09-24 07:33:33'),
(23, 'INV - 021', 17, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 36, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:34:12', '2026-09-24 07:34:12'),
(24, 'INV - 022', 19, 1, '2026-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 37, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:35:13', '2026-09-24 07:35:13'),
(25, 'INV - 023', 20, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 38, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:36:06', '2026-09-24 07:36:06'),
(26, 'INV - 024', 16, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 39, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:42:57', '2026-09-24 07:42:57'),
(27, 'INV - 025', 18, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 40, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:43:47', '2026-09-24 07:43:47'),
(28, 'INV - 026', 21, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 41, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:44:36', '2026-09-24 07:44:36'),
(29, 'INV - 027', 22, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 42, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:45:25', '2026-09-24 07:45:25'),
(30, 'INV - 028', 23, 1, '2025-06-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 43, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:46:08', '2026-09-24 07:46:08'),
(31, 'INV - 029', 24, 1, '2026-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 44, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 07:46:42', '2026-09-24 07:46:42'),
(32, 'INV - 030', 25, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 45, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 09:34:47', '2026-09-24 09:34:47'),
(33, 'INV - 031', 26, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 46, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 09:35:34', '2026-09-24 09:35:34'),
(34, 'INV - 032', 27, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 47, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 09:36:25', '2026-09-24 09:36:25'),
(35, 'INV - 033', 28, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 48, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 09:37:14', '2026-09-24 09:37:14'),
(36, 'INV - 034', 29, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 49, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 09:41:45', '2026-09-24 09:41:45'),
(37, 'INV - 035', 30, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 50, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 09:51:18', '2026-09-24 09:51:18'),
(38, 'INV - 036', 31, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 51, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 09:51:54', '2026-09-24 09:51:54'),
(39, 'INV - 037', 32, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 52, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 09:52:22', '2026-09-24 09:52:22'),
(40, 'INV - 038', 33, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 53, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 09:53:04', '2026-09-24 09:53:04'),
(41, 'INV - 039', 34, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 54, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 09:53:36', '2026-09-24 09:53:36'),
(42, 'INV - 040', 35, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 55, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 09:54:03', '2026-09-24 09:54:03'),
(43, 'INV - 041', 36, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 56, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 09:58:48', '2026-09-24 09:58:48'),
(44, 'INV - 042', 37, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 57, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 10:05:04', '2026-09-24 10:05:04'),
(45, 'INV - 043', 38, 1, '2025-10-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 58, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 10:11:29', '2026-09-24 10:11:29'),
(46, 'INV - 044', 39, 1, '2025-10-18', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 59, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 10:12:51', '2026-09-24 10:12:51'),
(47, 'INV - 045', 40, 1, '2025-10-18', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 60, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 10:13:26', '2026-09-24 10:13:26'),
(48, 'INV - 046', 41, 1, '2025-10-19', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 61, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 10:13:58', '2026-09-24 10:13:58'),
(49, 'INV - 047', 42, 1, '2025-10-19', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 62, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 10:14:28', '2026-09-24 10:14:28'),
(50, 'INV - 048', 43, 1, '2025-10-19', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 63, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 10:15:01', '2026-09-24 10:15:01'),
(51, 'INV - 049', 44, 1, '2025-11-05', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 64, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 10:15:30', '2026-09-24 10:15:30'),
(52, 'INV - 050', 45, 1, '2025-11-05', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 66, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 10:15:59', '2026-09-24 10:16:11'),
(53, 'INV - 051', 46, 1, '2025-11-06', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 67, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 10:16:45', '2026-09-24 10:16:45'),
(54, 'INV - 052', 47, 1, '2025-11-07', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 68, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 10:17:14', '2026-09-24 10:17:14'),
(55, 'INV - 053', 48, 1, '2025-11-09', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 69, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 10:17:46', '2026-09-24 10:17:46'),
(56, 'INV - 054', 49, 1, '2025-12-27', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 70, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-24 10:18:21', '2026-09-24 10:18:21'),
(57, 'INV - 055', 200, 1, '2025-12-28', '', '', NULL, 'CASH', 0.00, 25000.00, 'POSTED', 71, NULL, 1, NULL, NULL, 25000.00, 1, '2026-09-24 11:08:47', '2026-09-24 11:08:47'),
(58, 'INV - 056', 200, 1, '2026-09-24', '', '', NULL, 'CASH', 0.00, 1000.00, 'POSTED', 72, NULL, 1, NULL, NULL, 1000.00, 6, '2026-09-24 11:15:32', '2026-09-24 11:15:32'),
(59, 'INV - 057', 50, 1, '2026-01-02', '', '', NULL, 'CASH', 0.00, 1000.00, 'POSTED', 73, NULL, 1, NULL, NULL, 1000.00, 6, '2026-09-24 11:16:35', '2026-09-24 11:16:35'),
(60, 'INV - 058', 51, 1, '2026-02-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 74, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 05:33:34', '2026-09-25 05:33:34'),
(61, 'INV - 059', 52, 1, '2026-02-24', '', '', NULL, 'CASH', 0.00, 1000.00, 'POSTED', 75, NULL, 1, NULL, NULL, 1000.00, 6, '2026-09-25 05:34:17', '2026-09-25 05:34:17'),
(62, 'INV - 060', 53, 1, '2026-03-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 76, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 05:34:52', '2026-09-25 05:34:52'),
(63, 'INV - 061', 54, 1, '2026-03-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 77, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 05:35:23', '2026-09-25 05:35:23'),
(64, 'INV - 062', 55, 1, '2026-03-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 78, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 05:35:56', '2026-09-25 05:35:56'),
(65, 'INV - 063', 56, 1, '2026-03-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 79, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 05:36:29', '2026-09-25 05:36:29'),
(66, 'INV - 064', 200, 1, '2026-03-19', '', '', NULL, 'CASH', 0.00, 14000.00, 'POSTED', 80, NULL, 1, NULL, NULL, 14000.00, 1, '2026-09-25 06:03:02', '2026-09-25 06:03:02'),
(67, 'INV - 065', 200, 1, '2026-03-19', '', '', NULL, 'CASH', 0.00, 32000.00, 'POSTED', 81, NULL, 1, NULL, NULL, 32000.00, 6, '2026-09-25 06:16:18', '2026-09-25 06:16:18'),
(68, 'INV - 066', 57, 1, '2026-03-27', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 82, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:16:53', '2026-09-25 06:16:53'),
(69, 'INV - 067', 58, 1, '2026-03-27', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 83, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:17:23', '2026-09-25 06:17:23'),
(70, 'INV - 068', 59, 1, '2026-03-27', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 84, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:17:52', '2026-09-25 06:17:52'),
(71, 'INV - 069', 60, 1, '2026-03-27', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 85, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:18:48', '2026-09-25 06:18:48'),
(72, 'INV - 070', 61, 1, '2026-03-27', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 86, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:19:18', '2026-09-25 06:19:18'),
(73, 'INV - 071', 62, 1, '2026-03-27', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 87, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:19:55', '2026-09-25 06:19:55'),
(74, 'INV - 072', 63, 1, '2026-03-27', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 88, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:20:32', '2026-09-25 06:20:32'),
(75, 'INV - 073', 64, 1, '2026-03-27', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 89, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:21:05', '2026-09-25 06:21:05'),
(76, 'INV - 074', 65, 1, '2026-03-27', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 90, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:21:31', '2026-09-25 06:21:31'),
(77, 'INV - 075', 66, 1, '2026-03-27', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 91, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:22:08', '2026-09-25 06:22:08'),
(78, 'INV - 076', 67, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 92, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:22:35', '2026-09-25 06:22:35'),
(79, 'INV - 077', 68, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 93, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:23:04', '2026-09-25 06:23:04'),
(80, 'INV - 078', 69, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 94, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:23:31', '2026-09-25 06:23:31'),
(81, 'INV - 079', 70, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 95, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:23:57', '2026-09-25 06:23:57'),
(82, 'INV - 080', 71, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 96, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:24:21', '2026-09-25 06:24:21'),
(83, 'INV - 081', 72, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 97, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:24:48', '2026-09-25 06:24:48'),
(84, 'INV - 082', 73, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 98, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:25:14', '2026-09-25 06:25:14'),
(85, 'INV - 083', 74, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 99, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:25:36', '2026-09-25 06:25:36'),
(86, 'INV - 084', 75, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 100, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:26:02', '2026-09-25 06:26:02'),
(87, 'INV - 085', 76, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 101, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:26:31', '2026-09-25 06:26:31'),
(88, 'INV - 086', 77, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 102, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:26:54', '2026-09-25 06:26:54'),
(89, 'INV - 087', 78, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 103, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:27:25', '2026-09-25 06:27:25'),
(90, 'INV - 088', 79, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 104, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:28:06', '2026-09-25 06:28:06'),
(91, 'INV - 089', 80, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 105, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:28:28', '2026-09-25 06:28:28'),
(92, 'INV - 090', 200, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 107, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:31:58', '2026-09-25 06:32:10'),
(93, 'INV - 091', 81, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 108, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:32:33', '2026-09-25 06:32:33'),
(94, 'INV - 092', 83, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 109, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:32:57', '2026-09-25 06:32:57'),
(95, 'INV - 093', 84, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 110, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:33:21', '2026-09-25 06:33:21'),
(96, 'INV - 094', 85, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 111, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:33:43', '2026-09-25 06:33:43'),
(97, 'INV - 095', 86, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 112, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:34:12', '2026-09-25 06:34:12'),
(98, 'INV - 096', 87, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 113, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:34:36', '2026-09-25 06:34:36'),
(99, 'INV - 097', 88, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 114, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:34:59', '2026-09-25 06:34:59'),
(100, 'INV - 098', 89, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 115, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:35:22', '2026-09-25 06:35:22'),
(101, 'INV - 099', 90, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 116, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:35:56', '2026-09-25 06:35:56'),
(102, 'INV - 100', 91, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 117, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:36:20', '2026-09-25 06:36:20'),
(103, 'INV - 101', 92, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 118, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:47:04', '2026-09-25 06:47:04'),
(104, 'INV - 102', 93, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 119, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:47:31', '2026-09-25 06:47:31'),
(105, 'INV - 103', 94, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 120, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:47:59', '2026-09-25 06:47:59'),
(106, 'INV - 104', 95, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 121, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:48:29', '2026-09-25 06:48:29'),
(107, 'INV - 105', 96, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 122, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:48:54', '2026-09-25 06:48:54'),
(108, 'INV - 106', 97, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 123, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:51:03', '2026-09-25 06:51:03'),
(109, 'INV - 107', 98, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 124, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:51:33', '2026-09-25 06:51:33'),
(110, 'INV - 108', 99, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 125, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:51:57', '2026-09-25 06:51:57'),
(111, 'INV - 109', 100, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 126, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:53:15', '2026-09-25 06:53:15'),
(112, 'INV - 110', 101, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 127, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:53:42', '2026-09-25 06:53:42'),
(113, 'INV - 111', 102, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 128, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:54:10', '2026-09-25 06:54:10'),
(114, 'INV - 112', 103, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 129, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:54:42', '2026-09-25 06:54:42'),
(115, 'INV - 113', 104, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 130, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:55:07', '2026-09-25 06:55:07'),
(116, 'INV - 114', 105, 1, '2026-09-25', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 131, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:55:30', '2026-09-25 06:55:30'),
(117, 'INV - 115', 106, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 132, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:55:52', '2026-09-25 06:55:52'),
(118, 'INV - 116', 107, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 133, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:56:19', '2026-09-25 06:56:19'),
(119, 'INV - 117', 108, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 134, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:56:41', '2026-09-25 06:56:41'),
(120, 'INV - 118', 109, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 135, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:57:08', '2026-09-25 06:57:08'),
(121, 'INV - 119', 110, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 136, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:57:38', '2026-09-25 06:57:38'),
(122, 'INV - 120', 111, 1, '2026-04-03', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 137, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:59:25', '2026-09-25 06:59:25'),
(123, 'INV - 121', 112, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 138, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 06:59:56', '2026-09-25 06:59:56'),
(124, 'INV - 122', 113, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 139, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 07:00:23', '2026-09-25 07:00:24'),
(125, 'INV - 123', 114, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 140, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 07:00:49', '2026-09-25 07:00:49'),
(126, 'INV - 124', 115, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 141, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 07:01:25', '2026-09-25 07:01:25'),
(127, 'INV - 125', 116, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 142, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 07:05:14', '2026-09-25 07:05:15'),
(128, 'INV - 126', 117, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 143, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 07:05:38', '2026-09-25 07:05:38'),
(129, 'INV - 127', 118, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 144, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 07:06:01', '2026-09-25 07:06:01'),
(130, 'INV - 128', 112, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 5400.00, 'POSTED', 146, NULL, 1, NULL, NULL, 5400.00, 6, '2026-09-25 07:07:10', '2026-09-25 07:07:24'),
(131, 'INV - 129', 114, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 5400.00, 'POSTED', 147, NULL, 1, NULL, NULL, 5400.00, 6, '2026-09-25 07:08:27', '2026-09-25 07:08:27'),
(132, 'INV - 130', 113, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 15120.00, 'POSTED', 148, NULL, 1, NULL, NULL, 15120.00, 6, '2026-09-25 07:12:00', '2026-09-25 07:12:00'),
(133, 'INV - 131', 115, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 12960.00, 'POSTED', 149, NULL, 1, NULL, NULL, 12960.00, 6, '2026-09-25 07:15:16', '2026-09-25 07:15:16'),
(134, 'INV - 132', 116, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 3240.00, 'POSTED', 150, NULL, 1, NULL, NULL, 3240.00, 6, '2026-09-25 07:16:04', '2026-09-25 07:16:05'),
(135, 'INV - 133', 119, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 151, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 07:16:30', '2026-09-25 07:16:30'),
(136, 'INV - 134', 118, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 30240.00, 'POSTED', 152, NULL, 1, NULL, NULL, 30240.00, 6, '2026-09-25 07:17:49', '2026-09-25 07:17:49'),
(137, 'INV - 135', 67, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 21600.00, 'POSTED', 153, NULL, 1, NULL, NULL, 21600.00, 6, '2026-09-25 07:18:36', '2026-09-25 07:18:36'),
(138, 'INV - 136', 70, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 10800.00, 'POSTED', 154, NULL, 1, NULL, NULL, 10800.00, 6, '2026-09-25 07:19:20', '2026-09-25 07:19:20'),
(139, 'INV - 137', 72, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 4320.00, 'POSTED', 155, NULL, 1, NULL, NULL, 4320.00, 6, '2026-09-25 07:20:14', '2026-09-25 07:20:14'),
(140, 'INV - 138', 200, NULL, '2026-09-25', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-25 08:38:46', '2026-09-25 08:38:46'),
(141, 'INV - 139', 73, 1, '2026-04-05', '', '', NULL, 'CASH', 0.00, 6480.00, 'POSTED', 156, NULL, 1, NULL, NULL, 6480.00, 6, '2026-09-25 08:42:28', '2026-09-25 08:42:28'),
(142, 'INV - 140', 74, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 5400.00, 'POSTED', 157, NULL, 1, NULL, NULL, 5400.00, 6, '2026-09-25 08:43:21', '2026-09-25 08:43:21'),
(143, 'INV - 141', 75, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 5940.00, 'POSTED', 158, NULL, 1, NULL, NULL, 5940.00, 6, '2026-09-25 08:44:05', '2026-09-25 08:44:05'),
(144, 'INV - 142', 76, 1, '2026-04-09', '', '', NULL, 'CASH', 0.00, 2700.00, 'POSTED', 159, NULL, 1, NULL, NULL, 2700.00, 6, '2026-09-25 08:44:38', '2026-09-25 08:44:38'),
(145, 'INV - 143', 191, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 4800.00, 'POSTED', 160, NULL, 1, NULL, NULL, 4800.00, 6, '2026-09-25 08:45:29', '2026-09-25 08:45:29'),
(146, 'INV - 144', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 3000.00, 'POSTED', 161, NULL, 1, NULL, NULL, 3000.00, 6, '2026-09-25 08:46:55', '2026-09-25 08:46:55'),
(147, 'INV - 145', 77, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 5400.00, 'POSTED', 162, NULL, 1, NULL, NULL, 5400.00, 6, '2026-09-25 08:47:42', '2026-09-25 08:47:42'),
(148, 'INV - 146', 47, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 8100.00, 'POSTED', 163, NULL, 1, NULL, NULL, 8100.00, 6, '2026-09-25 08:48:26', '2026-09-25 08:48:26'),
(149, 'INV - 147', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2400.00, 'POSTED', 164, NULL, 1, NULL, NULL, 2400.00, 6, '2026-09-25 08:49:42', '2026-09-25 08:49:42'),
(150, 'INV - 148', 120, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 11340.00, 'POSTED', 165, NULL, 1, NULL, NULL, 11340.00, 6, '2026-09-25 08:50:16', '2026-09-25 08:50:16'),
(151, 'INV - 149', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 4860.00, 'POSTED', 166, NULL, 1, NULL, NULL, 4860.00, 6, '2026-09-25 08:51:00', '2026-09-25 08:51:00'),
(152, 'INV - 150', 120, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 167, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 08:52:03', '2026-09-25 08:52:03'),
(153, 'INV - 151', 68, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 15120.00, 'POSTED', 168, NULL, 1, NULL, NULL, 15120.00, 6, '2026-09-25 08:54:09', '2026-09-25 08:54:09'),
(154, 'INV - 152', 121, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 169, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 08:55:48', '2026-09-25 08:55:48'),
(155, 'INV - 153', 122, 1, '2026-04-05', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 170, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 08:56:17', '2026-09-25 08:56:17'),
(156, 'INV - 154', 123, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 171, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 08:56:56', '2026-09-25 08:56:56'),
(157, 'INV - 155', 124, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 172, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:00:23', '2026-09-25 09:00:23'),
(158, 'INV - 156', 125, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 173, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:01:02', '2026-09-25 09:01:02'),
(159, 'INV - 157', 126, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 174, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:02:02', '2026-09-25 09:02:02'),
(160, 'INV - 158', 127, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 175, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:02:36', '2026-09-25 09:02:36'),
(161, 'INV - 159', 128, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 176, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:03:03', '2026-09-25 09:03:03'),
(162, 'INV - 160', 129, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 177, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:03:34', '2026-09-25 09:03:34'),
(163, 'INV - 161', 130, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 178, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:04:00', '2026-09-25 09:04:00'),
(164, 'INV - 162', 131, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 179, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:05:40', '2026-09-25 09:05:40'),
(165, 'INV - 163', 132, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 180, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:06:05', '2026-09-25 09:06:05'),
(166, 'INV - 164', 133, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 181, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:06:44', '2026-09-25 09:06:44'),
(167, 'INV - 165', 134, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 182, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:07:10', '2026-09-25 09:07:10'),
(168, 'INV - 166', 135, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 183, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:07:34', '2026-09-25 09:07:34'),
(169, 'INV - 167', 136, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 184, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:08:01', '2026-09-25 09:08:01'),
(170, 'INV - 168', 137, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 185, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:08:27', '2026-09-25 09:08:27'),
(171, 'INV - 169', 138, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 186, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:08:53', '2026-09-25 09:08:53'),
(172, 'INV - 170', 139, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 187, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:09:16', '2026-09-25 09:09:16'),
(173, 'INV - 171', 94, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 4320.00, 'POSTED', 188, NULL, 1, NULL, NULL, 4320.00, 6, '2026-09-25 09:09:58', '2026-09-25 09:09:58'),
(174, 'INV - 172', 200, 1, '2026-09-25', '', '', NULL, 'CASH', 0.00, 4320.00, 'POSTED', 189, NULL, 1, NULL, NULL, 4320.00, 6, '2026-09-25 09:10:28', '2026-09-25 09:10:28'),
(175, 'INV - 173', 200, NULL, '2026-09-25', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-25 09:11:33', '2026-09-25 09:11:33'),
(176, 'INV - 174', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 1800.00, 'POSTED', 190, NULL, 1, NULL, NULL, 1800.00, 6, '2026-09-25 09:12:06', '2026-09-25 09:12:06'),
(177, 'INV - 175', 100, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 6480.00, 'POSTED', 191, NULL, 1, NULL, NULL, 6480.00, 6, '2026-09-25 09:13:41', '2026-09-25 09:13:41'),
(178, 'INV - 176', 104, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 6480.00, 'POSTED', 192, NULL, 1, NULL, NULL, 6480.00, 6, '2026-09-25 09:17:30', '2026-09-25 09:17:30'),
(179, 'INV - 177', 200, NULL, '2026-09-25', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-25 09:17:43', '2026-09-25 09:17:43'),
(180, 'INV - 178', 78, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2700.00, 'POSTED', 193, NULL, 1, NULL, NULL, 2700.00, 6, '2026-09-25 09:18:14', '2026-09-25 09:18:14'),
(181, 'INV - 179', 93, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 3780.00, 'POSTED', 194, NULL, 1, NULL, NULL, 3780.00, 6, '2026-09-25 09:18:45', '2026-09-25 09:18:45'),
(182, 'INV - 180', 83, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 27000.00, 'POSTED', 195, NULL, 1, NULL, NULL, 27000.00, 6, '2026-09-25 09:19:12', '2026-09-25 09:19:12'),
(183, 'INV - 181', 102, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 1080.00, 'POSTED', 196, NULL, 1, NULL, NULL, 1080.00, 6, '2026-09-25 09:19:41', '2026-09-25 09:19:41'),
(184, 'INV - 182', 88, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 5400.00, 'POSTED', 197, NULL, 1, NULL, NULL, 5400.00, 6, '2026-09-25 09:20:10', '2026-09-25 09:20:10'),
(185, 'INV - 183', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 9000.00, 'POSTED', 198, NULL, 1, NULL, NULL, 9000.00, 6, '2026-09-25 09:21:11', '2026-09-25 09:21:11'),
(186, 'INV - 184', 95, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 10800.00, 'POSTED', 199, NULL, 1, NULL, NULL, 10800.00, 6, '2026-09-25 09:21:43', '2026-09-25 09:21:43'),
(187, 'INV - 185', 200, NULL, '2026-09-25', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-25 09:21:51', '2026-09-25 09:21:51'),
(188, 'INV - 186', 140, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 200, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:23:06', '2026-09-25 09:23:06'),
(189, 'INV - 187', 141, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 201, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 09:23:34', '2026-09-25 09:23:34'),
(190, 'INV - 188', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 12250.00, 'POSTED', 202, NULL, 1, NULL, NULL, 12250.00, 6, '2026-09-25 09:24:08', '2026-09-25 09:24:08'),
(191, 'INV - 189', 122, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 9000.00, 'POSTED', 203, NULL, 1, NULL, NULL, 9000.00, 6, '2026-09-25 09:24:45', '2026-09-25 09:24:45'),
(192, 'INV - 190', 200, 1, '2026-09-25', '', '', NULL, 'CASH', 0.00, 12000.00, 'POSTED', 204, NULL, 1, NULL, NULL, 12000.00, 6, '2026-09-25 09:25:32', '2026-09-25 09:25:32'),
(193, 'INV - 191', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 3600.00, 'POSTED', 205, NULL, 1, NULL, NULL, 3600.00, 6, '2026-09-25 09:26:07', '2026-09-25 09:26:07'),
(194, 'INV - 192', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 2400.00, 'POSTED', 206, NULL, 1, NULL, NULL, 2400.00, 6, '2026-09-25 09:26:34', '2026-09-25 09:26:34'),
(195, 'INV - 193', 123, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 7500.00, 'POSTED', 207, NULL, 1, NULL, NULL, 7500.00, 6, '2026-09-25 09:27:02', '2026-09-25 09:27:02'),
(196, 'INV - 194', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 3000.00, 'POSTED', 208, NULL, 1, NULL, NULL, 3000.00, 6, '2026-09-25 09:27:55', '2026-09-25 09:27:55'),
(197, 'INV - 195', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 7200.00, 'POSTED', 209, NULL, 1, NULL, NULL, 7200.00, 6, '2026-09-25 09:28:41', '2026-09-25 09:28:41'),
(198, 'INV - 196', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 3600.00, 'POSTED', 210, NULL, 1, NULL, NULL, 3600.00, 6, '2026-09-25 09:29:34', '2026-09-25 09:29:34'),
(199, 'INV - 197', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 4800.00, 'POSTED', 211, NULL, 1, NULL, NULL, 4800.00, 6, '2026-09-25 09:30:15', '2026-09-25 09:30:15'),
(200, 'INV - 198', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 3600.00, 'POSTED', 212, NULL, 1, NULL, NULL, 3600.00, 6, '2026-09-25 09:31:00', '2026-09-25 09:31:00'),
(201, 'INV - 199', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 3600.00, 'POSTED', 213, NULL, 1, NULL, NULL, 3600.00, 6, '2026-09-25 09:31:44', '2026-09-25 09:31:44'),
(202, 'INV - 200', 200, 1, '2026-04-04', '', '', NULL, 'CASH', 0.00, 6000.00, 'POSTED', 214, NULL, 1, NULL, NULL, 6000.00, 6, '2026-09-25 09:32:24', '2026-09-25 09:32:24'),
(203, 'INV - 201', 142, 1, '2026-04-09', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 215, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 10:29:20', '2026-09-25 10:29:20'),
(204, 'INV - 202', 143, 1, '2026-09-25', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 216, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 10:44:45', '2026-09-25 10:44:45'),
(205, 'INV - 203', 144, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 217, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-25 10:46:09', '2026-09-25 10:46:09'),
(206, 'INV - 204', 142, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 10070.00, 'POSTED', 218, NULL, 1, NULL, NULL, 10070.00, 6, '2026-09-25 10:57:40', '2026-09-25 10:57:40'),
(207, 'INV - 205', 143, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 17120.00, 'POSTED', 219, NULL, 1, NULL, NULL, 17120.00, 6, '2026-09-25 11:38:48', '2026-09-25 11:38:48'),
(208, 'INV - 206', 144, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 11480.00, 'POSTED', 220, NULL, 1, NULL, NULL, 11480.00, 6, '2026-09-25 11:41:04', '2026-09-25 11:41:04'),
(209, 'INV - 207', 200, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 15850.00, 'POSTED', 221, NULL, 1, NULL, NULL, 15850.00, 6, '2026-09-25 11:45:54', '2026-09-25 11:45:54'),
(210, 'INV - 208', 200, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 15850.00, 'POSTED', 222, NULL, 1, NULL, NULL, 15850.00, 6, '2026-09-25 11:46:32', '2026-09-25 11:46:32'),
(211, 'INV - 209', 86, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 5400.00, 'POSTED', 223, NULL, 1, NULL, NULL, 5400.00, 6, '2026-09-25 11:48:59', '2026-09-25 11:48:59'),
(212, 'INV - 210', 90, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 12420.00, 'POSTED', 224, NULL, 1, NULL, NULL, 12420.00, 6, '2026-09-25 11:52:11', '2026-09-25 11:52:11'),
(213, 'INV - 211', 89, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 4320.00, 'POSTED', 225, NULL, 1, NULL, NULL, 4320.00, 6, '2026-09-25 11:52:46', '2026-09-25 11:52:46'),
(214, 'INV - 212', 92, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 7560.00, 'POSTED', 226, NULL, 1, NULL, NULL, 7560.00, 6, '2026-09-25 11:53:14', '2026-09-25 11:53:15'),
(215, 'INV - 213', 200, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 8400.00, 'POSTED', 227, NULL, 1, NULL, NULL, 8400.00, 6, '2026-09-25 11:53:54', '2026-09-25 11:53:54'),
(216, 'INV - 214', 98, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 8640.00, 'POSTED', 230, NULL, 1, NULL, NULL, 8640.00, 6, '2026-09-25 11:55:21', '2026-09-25 11:57:08'),
(217, 'INV - 215', 105, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 8100.00, 'POSTED', 231, NULL, 1, NULL, NULL, 8100.00, 6, '2026-09-25 11:56:04', '2026-09-25 11:57:42'),
(218, 'INV - 216', 200, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 2400.00, 'POSTED', 232, NULL, 1, NULL, NULL, 2400.00, 6, '2026-09-25 11:58:33', '2026-09-25 11:58:33'),
(219, 'INV - 217', 85, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 5400.00, 'POSTED', 233, NULL, 1, NULL, NULL, 5400.00, 6, '2026-09-27 03:55:06', '2026-09-27 03:55:06'),
(220, 'INV - 218', 103, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 7020.00, 'POSTED', 234, NULL, 1, NULL, NULL, 7020.00, 6, '2026-09-27 03:59:16', '2026-09-27 03:59:16'),
(221, 'INV - 219', 81, 1, '2026-09-27', '', '', NULL, 'CASH', 0.00, 13500.00, 'POSTED', 235, NULL, 1, NULL, NULL, 13500.00, 6, '2026-09-27 03:59:54', '2026-09-27 03:59:54'),
(222, 'INV - 220', 84, 1, '2026-04-26', '', '', NULL, 'CASH', 0.00, 3240.00, 'POSTED', 236, NULL, 1, NULL, NULL, 3240.00, 6, '2026-09-27 04:51:12', '2026-09-27 04:51:12'),
(223, 'INV - 221', 99, 1, '2026-04-26', '', '', NULL, 'CASH', 0.00, 1620.00, 'POSTED', 237, NULL, 1, NULL, NULL, 1620.00, 6, '2026-09-27 04:52:01', '2026-09-27 04:52:01'),
(224, 'INV - 222', 96, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 6480.00, 'POSTED', 238, NULL, 1, NULL, NULL, 6480.00, 6, '2026-09-27 04:52:44', '2026-09-27 04:52:44'),
(225, 'INV - 223', 200, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 8100.00, 'POSTED', 239, NULL, 1, NULL, NULL, 8100.00, 6, '2026-09-28 04:00:36', '2026-09-28 04:00:36'),
(226, 'INV - 224', 79, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 5400.00, 'POSTED', 240, NULL, 1, NULL, NULL, 5400.00, 6, '2026-09-28 04:01:13', '2026-09-28 04:01:13'),
(227, 'INV - 225', 104, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 4860.00, 'POSTED', 241, NULL, 1, NULL, NULL, 4860.00, 6, '2026-09-28 04:02:44', '2026-09-28 04:02:44'),
(228, 'INV - 226', 80, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 2160.00, 'POSTED', 242, NULL, 1, NULL, NULL, 2160.00, 6, '2026-09-28 04:03:13', '2026-09-28 04:03:13'),
(229, 'INV - 227', 97, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 4320.00, 'POSTED', 243, NULL, 1, NULL, NULL, 4320.00, 6, '2026-09-28 04:03:44', '2026-09-28 04:03:44'),
(230, 'INV - 228', 69, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 6480.00, 'POSTED', 244, NULL, 1, NULL, NULL, 6480.00, 6, '2026-09-28 04:06:20', '2026-09-28 04:06:20'),
(231, 'INV - 229', 200, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 1200.00, 'POSTED', 245, NULL, 1, NULL, NULL, 1200.00, 6, '2026-09-28 04:07:09', '2026-09-28 04:07:09'),
(232, 'INV - 230', 91, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 3780.00, 'POSTED', 246, NULL, 1, NULL, NULL, 3780.00, 6, '2026-09-28 04:07:42', '2026-09-28 04:07:42'),
(233, 'INV - 231', 87, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 14580.00, 'POSTED', 247, NULL, 1, NULL, NULL, 14580.00, 6, '2026-09-28 04:08:19', '2026-09-28 04:08:19'),
(234, 'INV - 232', 200, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 6000.00, 'POSTED', 248, NULL, 1, NULL, NULL, 6000.00, 6, '2026-09-28 04:09:02', '2026-09-28 04:09:02'),
(235, 'INV - 233', 200, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 6000.00, 'POSTED', 249, NULL, 1, NULL, NULL, 6000.00, 6, '2026-09-28 04:09:39', '2026-09-28 04:09:39'),
(236, 'INV - 234', 104, 1, '2026-04-10', '', '', NULL, 'CASH', 0.00, 6480.00, 'POSTED', 250, NULL, 1, NULL, NULL, 6480.00, 6, '2026-09-28 04:10:37', '2026-09-28 04:10:37'),
(237, 'INV - 235', 200, NULL, '2026-09-28', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-28 04:11:00', '2026-09-28 04:11:00'),
(238, 'INV - 236', 200, NULL, '2026-09-28', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-28 04:11:05', '2026-09-28 04:11:05'),
(239, 'INV - 237', 133, 1, '2026-04-11', '', '', NULL, 'CASH', 0.00, 3240.00, 'POSTED', 251, NULL, 1, NULL, NULL, 3240.00, 6, '2026-09-28 04:11:33', '2026-09-28 04:11:33'),
(240, 'INV - 238', 134, 1, '2026-04-11', '', '', NULL, 'CASH', 0.00, 6480.00, 'POSTED', 252, NULL, 1, NULL, NULL, 6480.00, 6, '2026-09-28 04:12:36', '2026-09-28 04:12:36'),
(241, 'INV - 239', 200, NULL, '2026-09-28', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-28 04:12:53', '2026-09-28 04:12:53'),
(242, 'INV - 240', 135, 1, '2026-04-11', '', '', NULL, 'CASH', 0.00, 4320.00, 'POSTED', 253, NULL, 1, NULL, NULL, 4320.00, 6, '2026-09-28 04:13:18', '2026-09-28 04:13:18'),
(243, 'INV - 241', 136, 1, '2026-04-11', '', '', NULL, 'CASH', 0.00, 8640.00, 'POSTED', 254, NULL, 1, NULL, NULL, 8640.00, 6, '2026-09-28 04:13:45', '2026-09-28 04:13:45'),
(244, 'INV - 242', 130, 1, '2026-04-11', '', '', NULL, 'CASH', 0.00, 19440.00, 'POSTED', 255, NULL, 1, NULL, NULL, 19440.00, 6, '2026-09-28 04:14:26', '2026-09-28 04:14:26'),
(245, 'INV - 243', 138, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 28080.00, 'POSTED', 256, NULL, 1, NULL, NULL, 28080.00, 6, '2026-09-28 04:15:03', '2026-09-28 04:15:03'),
(246, 'INV - 244', 200, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 2400.00, 'POSTED', 257, NULL, 1, NULL, NULL, 2400.00, 6, '2026-09-28 04:15:32', '2026-09-28 04:15:32'),
(247, 'INV - 245', 200, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 2400.00, 'POSTED', 258, NULL, 1, NULL, NULL, 2400.00, 6, '2026-09-28 04:16:08', '2026-09-28 04:16:08'),
(248, 'INV - 246', 200, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 12000.00, 'POSTED', 259, NULL, 1, NULL, NULL, 12000.00, 6, '2026-09-28 04:16:46', '2026-09-28 04:16:46'),
(249, 'INV - 247', 200, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 16800.00, 'POSTED', 260, NULL, 1, NULL, NULL, 16800.00, 6, '2026-09-28 04:17:19', '2026-09-28 04:17:19'),
(250, 'INV - 248', 200, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 12000.00, 'POSTED', 261, NULL, 1, NULL, NULL, 12000.00, 6, '2026-09-28 04:17:51', '2026-09-28 04:17:51'),
(251, 'INV - 249', 200, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 4800.00, 'POSTED', 262, NULL, 1, NULL, NULL, 4800.00, 6, '2026-09-28 04:18:21', '2026-09-28 04:18:21'),
(252, 'INV - 250', 200, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 3600.00, 'POSTED', 263, NULL, 1, NULL, NULL, 3600.00, 6, '2026-09-28 04:19:14', '2026-09-28 04:19:14'),
(253, 'INV - 251', 200, NULL, '2026-09-28', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-28 04:19:38', '2026-09-28 04:19:38'),
(254, 'INV - 252', 200, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 10800.00, 'POSTED', 264, NULL, 1, NULL, NULL, 10800.00, 6, '2026-09-28 04:20:10', '2026-09-28 04:20:10'),
(255, 'INV - 253', 137, 1, '2026-09-28', '', '', NULL, 'CASH', 0.00, 6480.00, 'POSTED', 265, NULL, 1, NULL, NULL, 6480.00, 6, '2026-09-28 04:20:45', '2026-09-28 04:20:45'),
(256, 'INV - 254', 200, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 10800.00, 'POSTED', 266, NULL, 1, NULL, NULL, 10800.00, 6, '2026-09-28 04:21:31', '2026-09-28 04:21:31'),
(257, 'INV - 255', 139, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 7560.00, 'POSTED', 267, NULL, 1, NULL, NULL, 7560.00, 6, '2026-09-28 04:23:51', '2026-09-28 04:23:51'),
(258, 'INV - 256', 200, NULL, '2026-09-28', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-28 04:24:10', '2026-09-28 04:24:10'),
(259, 'INV - 257', 126, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 10800.00, 'POSTED', 268, NULL, 1, NULL, NULL, 10800.00, 6, '2026-09-28 04:24:34', '2026-09-28 04:24:34'),
(260, 'INV - 258', 125, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 5400.00, 'POSTED', 269, NULL, 1, NULL, NULL, 5400.00, 6, '2026-09-28 04:24:55', '2026-09-28 04:24:55'),
(261, 'INV - 259', 124, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 30240.00, 'POSTED', 270, NULL, 1, NULL, NULL, 30240.00, 6, '2026-09-28 04:25:25', '2026-09-28 04:25:25'),
(262, 'INV - 260', 200, NULL, '2026-09-28', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-28 04:25:39', '2026-09-28 04:25:39'),
(263, 'INV - 261', 145, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 271, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:26:12', '2026-09-28 04:26:12'),
(264, 'INV - 262', 146, 1, '2026-04-12', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 272, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:26:46', '2026-09-28 04:26:46'),
(265, 'INV - 263', 147, 1, '2026-04-18', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 273, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:34:38', '2026-09-28 04:34:38'),
(266, 'INV - 264', 148, 1, '2026-04-18', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 274, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:35:07', '2026-09-28 04:35:07'),
(267, 'INV - 265', 149, 1, '2026-04-18', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 275, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:35:32', '2026-09-28 04:35:32'),
(268, 'INV - 266', 150, 1, '2026-04-18', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 276, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:36:03', '2026-09-28 04:36:03'),
(269, 'INV - 267', 151, 1, '2026-04-22', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 277, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:36:35', '2026-09-28 04:36:35'),
(270, 'INV - 268', 152, 1, '2026-04-23', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 278, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:37:11', '2026-09-28 04:37:11'),
(271, 'INV - 269', 153, 1, '2026-04-23', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 279, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:37:40', '2026-09-28 04:37:40'),
(272, 'INV - 270', 154, 1, '2026-04-23', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 280, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:38:08', '2026-09-28 04:38:08'),
(273, 'INV - 271', 155, 1, '2026-04-24', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 281, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:38:47', '2026-09-28 04:38:47'),
(274, 'INV - 272', 156, 1, '2026-04-23', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 282, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:39:12', '2026-09-28 04:39:12'),
(275, 'INV - 273', 101, 1, '2026-04-18', '', '', NULL, 'CASH', 0.00, 52380.00, 'POSTED', 284, NULL, 1, NULL, NULL, 52380.00, 6, '2026-09-28 04:39:57', '2026-09-28 04:40:10'),
(276, 'INV - 274', 200, 1, '2026-09-28', '', '', NULL, 'CASH', 0.00, 4320.00, 'POSTED', 285, NULL, 1, NULL, NULL, 4320.00, 6, '2026-09-28 04:40:29', '2026-09-28 04:40:29'),
(277, 'INV - 275', 161, 1, '2026-04-20', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 286, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:40:52', '2026-09-28 04:40:52'),
(278, 'INV - 276', 162, 1, '2026-04-20', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 287, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:41:18', '2026-09-28 04:41:18'),
(279, 'INV - 277', 163, 1, '2026-04-20', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 288, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:41:46', '2026-09-28 04:41:46'),
(280, 'INV - 278', 164, 1, '2026-04-20', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 289, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:42:17', '2026-09-28 04:42:17'),
(281, 'INV - 279', 165, 1, '2026-04-20', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 290, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:42:42', '2026-09-28 04:42:42'),
(282, 'INV - 280', 166, 1, '2026-04-20', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 291, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:43:31', '2026-09-28 04:43:31'),
(283, 'INV - 281', 167, 1, '2026-04-20', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 292, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:44:00', '2026-09-28 04:44:00'),
(284, 'INV - 282', 168, 1, '2026-04-20', '', '', NULL, 'CASH', 0.00, 2000.00, 'POSTED', 293, NULL, 1, NULL, NULL, 2000.00, 6, '2026-09-28 04:44:29', '2026-09-28 04:44:29'),
(285, 'INV - 283', 166, 1, '2026-04-20', '', '', NULL, 'CASH', 0.00, 4320.00, 'POSTED', 294, NULL, 1, NULL, NULL, 4320.00, 6, '2026-09-28 04:44:54', '2026-09-28 04:44:54'),
(286, 'INV - 284', 168, 1, '2026-04-20', '', '', NULL, 'CASH', 0.00, 7020.00, 'POSTED', 295, NULL, 1, NULL, NULL, 7020.00, 6, '2026-09-28 04:45:19', '2026-09-28 04:45:19'),
(287, 'INV - 285', 161, 1, '2026-04-21', '', '', NULL, 'CASH', 0.00, 2700.00, 'POSTED', 296, NULL, 1, NULL, NULL, 2700.00, 6, '2026-09-28 04:45:44', '2026-09-28 04:45:44'),
(288, 'INV - 286', 162, 1, '2026-04-21', '', '', NULL, 'CASH', 0.00, 8640.00, 'POSTED', 297, NULL, 1, NULL, NULL, 8640.00, 6, '2026-09-28 04:47:33', '2026-09-28 04:47:33'),
(289, 'INV - 287', 200, 1, '2026-04-21', '', '', NULL, 'CASH', 0.00, 4200.00, 'POSTED', 298, NULL, 1, NULL, NULL, 4200.00, 6, '2026-09-28 04:47:58', '2026-09-28 04:47:58'),
(290, 'INV - 288', 200, 1, '2026-04-21', '', '', NULL, 'CASH', 0.00, 4800.00, 'POSTED', 299, NULL, 1, NULL, NULL, 4800.00, 6, '2026-09-28 04:48:27', '2026-09-28 04:48:27');
INSERT INTO `invoices` (`id`, `invoice_number`, `customer_id`, `warehouse_id`, `invoice_date`, `reference`, `notes`, `reversal_reason`, `payment_type`, `discount`, `total`, `status`, `journal_entry_id`, `reversal_journal_entry_id`, `cash_account_id`, `bank_account_id`, `cheque_id`, `subtotal`, `created_by`, `created_at`, `updated_at`) VALUES
(291, 'INV - 289', 200, NULL, '2026-09-28', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-28 04:48:41', '2026-09-28 04:48:41'),
(292, 'INV - 290', 165, 1, '2026-04-21', '', '', NULL, 'CASH', 0.00, 5940.00, 'POSTED', 300, NULL, 1, NULL, NULL, 5940.00, 6, '2026-09-28 04:49:14', '2026-09-28 04:49:14'),
(293, 'INV - 291', 200, 1, '2026-04-21', '', '', NULL, 'CASH', 0.00, 4800.00, 'POSTED', 301, NULL, 1, NULL, NULL, 4800.00, 6, '2026-09-28 04:49:41', '2026-09-28 04:49:41'),
(294, 'INV - 292', 200, 1, '2026-04-21', '', '', NULL, 'CASH', 0.00, 1200.00, 'POSTED', 302, NULL, 1, NULL, NULL, 1200.00, 6, '2026-09-28 04:50:09', '2026-09-28 04:50:09'),
(295, 'INV - 293', 167, 1, '2026-04-21', '', '', NULL, 'CASH', 0.00, 4320.00, 'POSTED', 303, NULL, 1, NULL, NULL, 4320.00, 6, '2026-09-28 04:50:33', '2026-09-28 04:50:33'),
(296, 'INV - 294', 200, 1, '2026-05-01', '', '', NULL, 'CASH', 0.00, 14400.00, 'POSTED', 304, NULL, 1, NULL, NULL, 14400.00, 6, '2026-09-28 04:51:03', '2026-09-28 04:51:03'),
(297, 'INV - 295', 200, 1, '2026-05-01', '', '', NULL, 'CASH', 0.00, 4200.00, 'POSTED', 305, NULL, 1, NULL, NULL, 4200.00, 6, '2026-09-28 04:51:31', '2026-09-28 04:51:31'),
(298, 'INV - 296', 200, 1, '2026-05-02', '', '', NULL, 'CASH', 0.00, 6000.00, 'POSTED', 306, NULL, 1, NULL, NULL, 6000.00, 6, '2026-09-28 04:52:02', '2026-09-28 04:52:02'),
(299, 'INV - 297', 164, 1, '2026-05-02', '', '', NULL, 'CASH', 0.00, 4860.00, 'POSTED', 307, NULL, 1, NULL, NULL, 4860.00, 6, '2026-09-28 04:52:24', '2026-09-28 04:52:24'),
(300, 'INV - 298', 200, NULL, '2026-09-28', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-28 04:54:08', '2026-09-28 04:54:08'),
(301, 'INV - 299', 200, 1, '2026-05-05', '', '', NULL, 'CASH', 0.00, 8400.00, 'POSTED', 308, NULL, 1, NULL, NULL, 8400.00, 6, '2026-09-28 04:54:38', '2026-09-28 04:54:38'),
(302, 'INV - 300', 131, 1, '2026-05-03', '', '', NULL, 'CASH', 0.00, 1080.00, 'POSTED', 309, NULL, 1, NULL, NULL, 1080.00, 6, '2026-09-28 04:55:11', '2026-09-28 04:55:11'),
(303, 'INV - 301', 132, 1, '2026-05-03', '', '', NULL, 'CASH', 0.00, 2160.00, 'POSTED', 310, NULL, 1, NULL, NULL, 2160.00, 6, '2026-09-28 04:58:39', '2026-09-28 04:58:39'),
(304, 'INV - 302', 200, 1, '2026-05-06', '', '', NULL, 'CASH', 0.00, 18000.00, 'POSTED', 320, NULL, 1, NULL, NULL, 18000.00, 6, '2026-09-28 04:59:08', '2026-09-28 05:16:16'),
(305, 'INV - 303', 149, 1, '2026-05-06', '', '', NULL, 'CASH', 0.00, 4320.00, 'POSTED', 312, NULL, 1, NULL, NULL, 4320.00, 6, '2026-09-28 04:59:36', '2026-09-28 04:59:36'),
(306, 'INV - 304', 150, 1, '2026-05-06', '', '', NULL, 'CASH', 0.00, 8640.00, 'POSTED', 313, NULL, 1, NULL, NULL, 8640.00, 6, '2026-09-28 05:00:00', '2026-09-28 05:00:00'),
(307, 'INV - 305', 200, 1, '2026-05-06', '', '', NULL, 'CASH', 0.00, 7200.00, 'POSTED', 314, NULL, 1, NULL, NULL, 7200.00, 6, '2026-09-28 05:00:35', '2026-09-28 05:00:35'),
(308, 'INV - 306', 200, 1, '2026-05-06', '', '', NULL, 'CASH', 0.00, 3600.00, 'POSTED', 315, NULL, 1, NULL, NULL, 3600.00, 6, '2026-09-28 05:01:07', '2026-09-28 05:01:07'),
(309, 'INV - 307', 200, 1, '2026-05-06', '', '', NULL, 'CASH', 0.00, 7200.00, 'POSTED', 316, NULL, 1, NULL, NULL, 7200.00, 6, '2026-09-28 05:02:05', '2026-09-28 05:02:05'),
(310, 'INV - 308', 200, 1, '2026-05-06', '', '', NULL, 'CASH', 0.00, 1800.00, 'POSTED', 317, NULL, 1, NULL, NULL, 1800.00, 6, '2026-09-28 05:12:19', '2026-09-28 05:12:19'),
(311, 'INV - 309', 200, 1, '2026-05-06', '', '', NULL, 'CASH', 0.00, 4800.00, 'POSTED', 318, NULL, 1, NULL, NULL, 4800.00, 6, '2026-09-28 05:13:20', '2026-09-28 05:13:20'),
(312, 'INV - 310', 129, 1, '2026-05-03', '', '', NULL, 'CASH', 0.00, 7560.00, 'POSTED', 319, NULL, 1, NULL, NULL, 7560.00, 6, '2026-09-28 05:15:30', '2026-09-28 05:15:30'),
(313, 'INV - 311', 200, 1, '2026-05-06', '', '', NULL, 'CASH', 0.00, 2400.00, 'POSTED', 321, NULL, 1, NULL, NULL, 2400.00, 6, '2026-09-28 05:18:29', '2026-09-28 05:18:29'),
(314, 'INV - 312', 200, 1, '2026-05-06', '', '', NULL, 'CASH', 0.00, 4320.00, 'POSTED', 322, NULL, 1, NULL, NULL, 4320.00, 6, '2026-09-28 05:19:37', '2026-09-28 05:19:37'),
(315, 'INV - 313', 200, NULL, '2026-09-28', NULL, 'Physically voided in bill book', NULL, 'CASH', 0.00, 0.00, 'CANCELLED', NULL, NULL, NULL, NULL, NULL, 0.00, 6, '2026-09-28 05:19:53', '2026-09-28 05:19:53'),
(316, 'INV - 314', 200, 1, '2026-05-06', '', '', NULL, 'CASH', 0.00, 4200.00, 'POSTED', 323, NULL, 1, NULL, NULL, 4200.00, 6, '2026-09-28 05:20:32', '2026-09-28 05:20:32'),
(317, 'INV - 315', 200, 1, '2026-05-06', '', '', NULL, 'CASH', 0.00, 1800.00, 'POSTED', 328, NULL, 1, NULL, NULL, 1800.00, 6, '2026-09-28 05:21:05', '2026-09-28 05:23:21');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_id` int(10) UNSIGNED NOT NULL,
  `item_type` varchar(50) DEFAULT 'PRODUCT',
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `service_id` int(10) UNSIGNED DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `quantity` decimal(15,2) DEFAULT 0.00,
  `unit_price` decimal(15,2) DEFAULT 0.00,
  `discount` decimal(15,2) DEFAULT 0.00,
  `total` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_type`, `product_id`, `service_id`, `description`, `quantity`, `unit_price`, `discount`, `total`) VALUES
(7, 3, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(8, 3, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(13, 2, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(14, 2, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(23, 1, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(24, 1, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(25, 4, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(26, 4, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(27, 5, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(28, 5, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(29, 10, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(30, 10, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(31, 8, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(32, 8, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(33, 9, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(34, 9, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(35, 11, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(36, 11, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(37, 12, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(38, 12, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(41, 13, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(42, 13, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(43, 14, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(44, 14, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(45, 15, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(46, 15, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(47, 17, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(48, 17, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(49, 18, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(50, 18, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(51, 20, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(52, 20, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(53, 21, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(54, 21, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(55, 22, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(56, 22, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(57, 23, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(58, 23, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(59, 24, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(60, 24, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(61, 25, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(62, 25, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(63, 26, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(64, 26, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(65, 27, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(66, 27, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(67, 28, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(68, 28, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(69, 29, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(70, 29, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(71, 30, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(72, 30, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(73, 31, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(74, 31, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(75, 32, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(76, 32, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(77, 33, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(78, 33, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(79, 34, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(80, 34, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(81, 35, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(82, 35, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(83, 36, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(84, 36, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(85, 37, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(86, 37, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(87, 38, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(88, 38, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(89, 39, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(90, 39, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(91, 40, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(92, 40, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(93, 41, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(94, 41, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(95, 42, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(96, 42, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(97, 43, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(98, 43, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(99, 44, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(100, 44, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(101, 45, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(102, 45, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(103, 46, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(104, 46, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(105, 47, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(106, 47, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(107, 48, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(108, 48, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(109, 49, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(110, 49, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(111, 50, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(112, 50, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(113, 51, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(114, 51, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(116, 52, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(117, 52, 'SHARE_CAPITAL', NULL, NULL, 'Share Capital', 1.00, 1000.00, 0.00, 1000.00),
(118, 53, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(119, 53, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(120, 54, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(121, 54, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(122, 55, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(123, 55, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(124, 56, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(125, 56, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(126, 57, 'DONATION', NULL, NULL, '', 1.00, 25000.00, 0.00, 25000.00),
(127, 58, 'DONATION', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(128, 59, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(129, 60, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(130, 60, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(131, 61, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(132, 62, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(133, 62, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(134, 63, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(135, 63, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(136, 64, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(137, 64, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(138, 65, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(139, 65, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(140, 66, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 7000.00, 0.00, 7000.00),
(141, 66, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 7000.00, 0.00, 7000.00),
(142, 67, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 16000.00, 0.00, 16000.00),
(143, 67, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 16000.00, 0.00, 16000.00),
(144, 68, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(145, 68, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(146, 69, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(147, 69, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(148, 70, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(149, 70, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(150, 71, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(151, 71, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(152, 72, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(153, 72, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(154, 73, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(155, 73, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(156, 74, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(157, 74, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(158, 75, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(159, 75, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(160, 76, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(161, 76, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(162, 77, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(163, 77, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(164, 78, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(165, 78, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(166, 79, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(167, 79, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(168, 80, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(169, 80, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(170, 81, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(171, 81, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(172, 82, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(173, 82, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(174, 83, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(175, 83, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(176, 84, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(177, 84, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(178, 85, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(179, 85, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(180, 86, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(181, 86, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(182, 87, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(183, 87, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(184, 88, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(185, 88, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(186, 89, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(187, 89, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(188, 90, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(189, 90, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(190, 91, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(191, 91, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(194, 92, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(195, 92, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(196, 93, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(197, 93, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(198, 94, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(199, 94, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(200, 95, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(201, 95, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(202, 96, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(203, 96, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(204, 97, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(205, 97, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(206, 98, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(207, 98, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(208, 99, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(209, 99, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(210, 100, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(211, 100, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(212, 101, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(213, 101, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(214, 102, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(215, 102, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(216, 103, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(217, 103, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(218, 104, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(219, 104, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(220, 105, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(221, 105, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(222, 106, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(223, 106, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(224, 107, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(225, 107, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(226, 108, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(227, 108, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(228, 109, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(229, 109, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(230, 110, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(231, 110, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(232, 111, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(233, 111, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(234, 112, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(235, 112, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(236, 113, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(237, 113, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(238, 114, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(239, 114, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(240, 115, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(241, 115, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(242, 116, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(243, 116, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(244, 117, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(245, 117, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(246, 118, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(247, 118, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(248, 119, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(249, 119, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(250, 120, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(251, 120, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(252, 121, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(253, 121, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(254, 122, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(255, 122, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(256, 123, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(257, 123, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(258, 124, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(259, 124, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(260, 125, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(261, 125, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(262, 126, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(263, 126, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(264, 127, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(265, 127, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(266, 128, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(267, 128, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(268, 129, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(269, 129, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(272, 130, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2700.00, 0.00, 2700.00),
(273, 130, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2700.00, 0.00, 2700.00),
(274, 131, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2700.00, 0.00, 2700.00),
(275, 131, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2700.00, 0.00, 2700.00),
(276, 132, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 7560.00, 0.00, 7560.00),
(277, 132, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 7560.00, 0.00, 7560.00),
(278, 133, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 6480.00, 0.00, 6480.00),
(279, 133, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 6480.00, 0.00, 6480.00),
(280, 134, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 1620.00, 0.00, 1620.00),
(281, 134, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 1620.00, 0.00, 1620.00),
(282, 135, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(283, 135, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(284, 136, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 15120.00, 0.00, 15120.00),
(285, 136, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 15120.00, 0.00, 15120.00),
(286, 137, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 10800.00, 0.00, 10800.00),
(287, 137, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 10800.00, 0.00, 10800.00),
(288, 138, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2700.00, 0.00, 2700.00),
(289, 138, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 8100.00, 0.00, 8100.00),
(290, 139, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2160.00, 0.00, 2160.00),
(291, 139, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2160.00, 0.00, 2160.00),
(292, 141, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3240.00, 0.00, 3240.00),
(293, 141, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3240.00, 0.00, 3240.00),
(294, 142, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2700.00, 0.00, 2700.00),
(295, 142, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2700.00, 0.00, 2700.00),
(296, 143, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3240.00, 0.00, 3240.00),
(297, 143, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2700.00, 0.00, 2700.00),
(298, 144, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2700.00, 0.00, 2700.00),
(299, 145, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2400.00, 0.00, 2400.00),
(300, 145, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2400.00, 0.00, 2400.00),
(301, 146, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3000.00, 0.00, 3000.00),
(302, 147, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 5400.00, 0.00, 5400.00),
(303, 148, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 8100.00, 0.00, 8100.00),
(304, 149, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2400.00, 0.00, 2400.00),
(305, 150, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 11340.00, 0.00, 11340.00),
(306, 151, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 4860.00, 0.00, 4860.00),
(307, 152, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(308, 152, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(309, 153, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 7560.00, 0.00, 7560.00),
(310, 153, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 7560.00, 0.00, 7560.00),
(311, 154, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(312, 154, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(313, 155, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(314, 155, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(315, 156, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(316, 156, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(317, 157, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(318, 157, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(319, 158, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(320, 158, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(321, 159, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(322, 159, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(323, 160, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(324, 160, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(325, 161, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(326, 161, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(327, 162, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(328, 162, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(329, 163, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(330, 163, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(331, 164, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(332, 164, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(333, 165, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(334, 165, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(335, 166, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(336, 166, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(337, 167, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(338, 167, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(339, 168, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(340, 168, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(341, 169, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(342, 169, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(343, 170, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(344, 170, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(345, 171, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(346, 171, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(347, 172, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(348, 172, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(349, 173, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 4320.00, 0.00, 4320.00),
(350, 174, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 4320.00, 0.00, 4320.00),
(351, 176, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 1800.00, 0.00, 1800.00),
(352, 177, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3240.00, 0.00, 3240.00),
(353, 177, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3240.00, 0.00, 3240.00),
(354, 178, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3240.00, 0.00, 3240.00),
(355, 178, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3240.00, 0.00, 3240.00),
(356, 180, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2700.00, 0.00, 2700.00),
(357, 181, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3780.00, 0.00, 3780.00),
(358, 182, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 27000.00, 0.00, 27000.00),
(359, 183, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 1080.00, 0.00, 1080.00),
(360, 184, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 5400.00, 0.00, 5400.00),
(361, 185, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 6000.00, 0.00, 6000.00),
(362, 185, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3000.00, 0.00, 3000.00),
(363, 186, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 10800.00, 0.00, 10800.00),
(364, 188, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(365, 188, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(366, 189, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(367, 189, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(368, 190, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 12250.00, 0.00, 12250.00),
(369, 191, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 4500.00, 0.00, 4500.00),
(370, 191, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 4500.00, 0.00, 4500.00),
(371, 192, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 12000.00, 0.00, 12000.00),
(372, 193, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3600.00, 0.00, 3600.00),
(373, 194, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2400.00, 0.00, 2400.00),
(374, 195, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 7500.00, 0.00, 7500.00),
(375, 196, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3000.00, 0.00, 3000.00),
(376, 197, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 7200.00, 0.00, 7200.00),
(377, 198, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 1800.00, 0.00, 1800.00),
(378, 198, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 1800.00, 0.00, 1800.00),
(379, 199, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2400.00, 0.00, 2400.00),
(380, 199, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 2400.00, 0.00, 2400.00),
(381, 200, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 1800.00, 0.00, 1800.00),
(382, 200, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 1800.00, 0.00, 1800.00),
(383, 201, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 1800.00, 0.00, 1800.00),
(384, 201, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 1800.00, 0.00, 1800.00),
(385, 202, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3000.00, 0.00, 3000.00),
(386, 202, 'SERVICE', NULL, 1, 'Machinery Rental Billing', 1.00, 3000.00, 0.00, 3000.00),
(387, 203, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(388, 203, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(389, 204, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(390, 204, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(391, 205, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(392, 205, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(393, 206, 'SERVICE', NULL, 2, '', 1.00, 10070.00, 0.00, 10070.00),
(394, 207, 'SERVICE', NULL, 2, '', 1.00, 17120.00, 0.00, 17120.00),
(395, 208, 'SERVICE', NULL, 2, '', 1.00, 11480.00, 0.00, 11480.00),
(396, 209, 'SERVICE', NULL, 2, '', 1.00, 15850.00, 0.00, 15850.00),
(397, 210, 'SERVICE', NULL, 2, '', 1.00, 15850.00, 0.00, 15850.00),
(398, 211, 'SERVICE', NULL, 2, '', 1.00, 5400.00, 0.00, 5400.00),
(399, 212, 'SERVICE', NULL, 2, '', 1.00, 12420.00, 0.00, 12420.00),
(400, 213, 'SERVICE', NULL, 2, '', 1.00, 4320.00, 0.00, 4320.00),
(401, 214, 'SERVICE', NULL, 2, '', 1.00, 7560.00, 0.00, 7560.00),
(402, 215, 'SERVICE', NULL, 2, '', 1.00, 8400.00, 0.00, 8400.00),
(405, 216, 'SERVICE', NULL, 2, '', 1.00, 8640.00, 0.00, 8640.00),
(406, 217, 'SERVICE', NULL, 2, '', 1.00, 8100.00, 0.00, 8100.00),
(407, 218, 'SERVICE', NULL, 2, '', 1.00, 2400.00, 0.00, 2400.00),
(408, 219, 'SERVICE', NULL, 2, '', 1.00, 5400.00, 0.00, 5400.00),
(409, 220, 'SERVICE', NULL, 2, '', 1.00, 7020.00, 0.00, 7020.00),
(410, 221, 'SERVICE', NULL, 2, '', 1.00, 13500.00, 0.00, 13500.00),
(411, 222, 'SERVICE', NULL, 2, '', 1.00, 3240.00, 0.00, 3240.00),
(412, 223, 'SERVICE', NULL, 2, '', 1.00, 1620.00, 0.00, 1620.00),
(413, 224, 'SERVICE', NULL, 2, '', 1.00, 6480.00, 0.00, 6480.00),
(414, 225, 'SERVICE', NULL, 2, '', 1.00, 8100.00, 0.00, 8100.00),
(415, 226, 'SERVICE', NULL, 2, '', 1.00, 5400.00, 0.00, 5400.00),
(416, 227, 'SERVICE', NULL, 2, '', 1.00, 4860.00, 0.00, 4860.00),
(417, 228, 'SERVICE', NULL, 2, '', 1.00, 2160.00, 0.00, 2160.00),
(418, 229, 'SERVICE', NULL, 2, '', 1.00, 4320.00, 0.00, 4320.00),
(419, 230, 'SERVICE', NULL, 2, '', 1.00, 6480.00, 0.00, 6480.00),
(420, 231, 'SERVICE', NULL, 2, '', 1.00, 1200.00, 0.00, 1200.00),
(421, 232, 'SERVICE', NULL, 2, '', 1.00, 3780.00, 0.00, 3780.00),
(422, 233, 'SERVICE', NULL, 2, '', 1.00, 14580.00, 0.00, 14580.00),
(423, 234, 'SERVICE', NULL, 2, '', 1.00, 6000.00, 0.00, 6000.00),
(424, 235, 'SERVICE', NULL, 2, '', 1.00, 6000.00, 0.00, 6000.00),
(425, 236, 'SERVICE', NULL, 2, '', 1.00, 6480.00, 0.00, 6480.00),
(426, 239, 'SERVICE', NULL, 2, '', 1.00, 3240.00, 0.00, 3240.00),
(427, 240, 'SERVICE', NULL, 2, '', 1.00, 6480.00, 0.00, 6480.00),
(428, 242, 'SERVICE', NULL, 2, '', 1.00, 4320.00, 0.00, 4320.00),
(429, 243, 'SERVICE', NULL, 2, '', 1.00, 8640.00, 0.00, 8640.00),
(430, 244, 'SERVICE', NULL, 2, '', 1.00, 19440.00, 0.00, 19440.00),
(431, 245, 'SERVICE', NULL, 2, '', 1.00, 28080.00, 0.00, 28080.00),
(432, 246, 'SERVICE', NULL, 2, '', 1.00, 2400.00, 0.00, 2400.00),
(433, 247, 'SERVICE', NULL, 2, '', 1.00, 2400.00, 0.00, 2400.00),
(434, 248, 'SERVICE', NULL, 2, '', 1.00, 12000.00, 0.00, 12000.00),
(435, 249, 'SERVICE', NULL, 2, '', 1.00, 16800.00, 0.00, 16800.00),
(436, 250, 'SERVICE', NULL, 2, '', 1.00, 12000.00, 0.00, 12000.00),
(437, 251, 'SERVICE', NULL, 2, '', 1.00, 4800.00, 0.00, 4800.00),
(438, 252, 'SERVICE', NULL, 2, '', 1.00, 3600.00, 0.00, 3600.00),
(439, 254, 'SERVICE', NULL, 2, '', 1.00, 10800.00, 0.00, 10800.00),
(440, 255, 'SERVICE', NULL, 2, '', 1.00, 6480.00, 0.00, 6480.00),
(441, 256, 'SERVICE', NULL, 2, '', 1.00, 10800.00, 0.00, 10800.00),
(442, 257, 'SERVICE', NULL, 2, '', 1.00, 7560.00, 0.00, 7560.00),
(443, 259, 'SERVICE', NULL, 2, '', 1.00, 10800.00, 0.00, 10800.00),
(444, 260, 'SERVICE', NULL, 2, '', 1.00, 5400.00, 0.00, 5400.00),
(445, 261, 'SERVICE', NULL, 2, '', 1.00, 30240.00, 0.00, 30240.00),
(446, 263, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(447, 263, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(448, 264, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(449, 264, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(450, 265, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(451, 265, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(452, 266, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(453, 266, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(454, 267, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(455, 267, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(456, 268, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(457, 268, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(458, 269, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(459, 269, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(460, 270, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(461, 270, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(462, 271, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(463, 271, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(464, 272, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(465, 272, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(466, 273, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(467, 273, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(468, 274, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(469, 274, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(471, 275, 'SERVICE', NULL, 2, '', 1.00, 52380.00, 0.00, 52380.00),
(472, 276, 'SERVICE', NULL, 2, '', 1.00, 4320.00, 0.00, 4320.00),
(473, 277, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(474, 277, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(475, 278, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(476, 278, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(477, 279, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(478, 279, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(479, 280, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(480, 280, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(481, 281, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(482, 281, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(483, 282, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(484, 282, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(485, 283, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(486, 283, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(487, 284, 'MEMBER_FEE', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(488, 284, 'SHARE_CAPITAL', NULL, NULL, '', 1.00, 1000.00, 0.00, 1000.00),
(489, 285, 'SERVICE', NULL, 2, '', 1.00, 4320.00, 0.00, 4320.00),
(490, 286, 'SERVICE', NULL, 2, '', 1.00, 7020.00, 0.00, 7020.00),
(491, 287, 'SERVICE', NULL, 2, '', 1.00, 2700.00, 0.00, 2700.00),
(492, 288, 'SERVICE', NULL, 2, '', 1.00, 8640.00, 0.00, 8640.00),
(493, 289, 'SERVICE', NULL, 2, '', 1.00, 4200.00, 0.00, 4200.00),
(494, 290, 'SERVICE', NULL, 2, '', 1.00, 4800.00, 0.00, 4800.00),
(495, 292, 'SERVICE', NULL, 2, '', 1.00, 5940.00, 0.00, 5940.00),
(496, 293, 'SERVICE', NULL, 2, '', 1.00, 4800.00, 0.00, 4800.00),
(497, 294, 'SERVICE', NULL, 2, '', 1.00, 1200.00, 0.00, 1200.00),
(498, 295, 'SERVICE', NULL, 2, '', 1.00, 4320.00, 0.00, 4320.00),
(499, 296, 'SERVICE', NULL, 2, '', 1.00, 14400.00, 0.00, 14400.00),
(500, 297, 'SERVICE', NULL, 2, '', 1.00, 4200.00, 0.00, 4200.00),
(501, 298, 'SERVICE', NULL, 2, '', 1.00, 6000.00, 0.00, 6000.00),
(502, 299, 'SERVICE', NULL, 2, '', 1.00, 4860.00, 0.00, 4860.00),
(503, 301, 'SERVICE', NULL, 2, '', 1.00, 8400.00, 0.00, 8400.00),
(504, 302, 'SERVICE', NULL, 2, '', 1.00, 1080.00, 0.00, 1080.00),
(505, 303, 'SERVICE', NULL, 2, '', 1.00, 2160.00, 0.00, 2160.00),
(507, 305, 'SERVICE', NULL, 2, '', 1.00, 4320.00, 0.00, 4320.00),
(508, 306, 'SERVICE', NULL, 2, '', 1.00, 8640.00, 0.00, 8640.00),
(509, 307, 'SERVICE', NULL, 2, '', 1.00, 7200.00, 0.00, 7200.00),
(510, 308, 'SERVICE', NULL, 2, '', 1.00, 3600.00, 0.00, 3600.00),
(511, 309, 'SERVICE', NULL, 2, '', 1.00, 7200.00, 0.00, 7200.00),
(512, 310, 'SERVICE', NULL, 2, '', 1.00, 1800.00, 0.00, 1800.00),
(513, 311, 'SERVICE', NULL, 2, '', 1.00, 4800.00, 0.00, 4800.00),
(514, 312, 'SERVICE', NULL, 2, '', 1.00, 7560.00, 0.00, 7560.00),
(515, 304, 'SERVICE', NULL, 2, '', 1.00, 18000.00, 0.00, 18000.00),
(516, 313, 'SERVICE', NULL, 2, '', 1.00, 2400.00, 0.00, 2400.00),
(517, 314, 'SERVICE', NULL, 2, '', 1.00, 4320.00, 0.00, 4320.00),
(518, 316, 'SERVICE', NULL, 2, '', 1.00, 4200.00, 0.00, 4200.00),
(523, 317, 'SERVICE', NULL, 2, '', 1.00, 1800.00, 0.00, 1800.00);

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` int(10) UNSIGNED NOT NULL,
  `journal_number` varchar(50) NOT NULL,
  `transaction_date` date NOT NULL,
  `posting_date` date DEFAULT NULL,
  `description` text NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `source_module` varchar(50) NOT NULL DEFAULT 'manual',
  `source_transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `cost_center_id` int(10) UNSIGNED DEFAULT NULL,
  `project_id` int(10) UNSIGNED DEFAULT NULL,
  `batch_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('draft','posted','cancelled') NOT NULL DEFAULT 'posted',
  `total_debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_by` int(10) UNSIGNED NOT NULL,
  `posted_by` int(10) UNSIGNED DEFAULT NULL,
  `posted_at` datetime DEFAULT NULL,
  `reversal_of_journal_id` int(10) UNSIGNED DEFAULT NULL,
  `reversal_reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journal_entries`
--

INSERT INTO `journal_entries` (`id`, `journal_number`, `transaction_date`, `posting_date`, `description`, `reference`, `source_module`, `source_transaction_id`, `cost_center_id`, `project_id`, `batch_id`, `status`, `total_debit`, `total_credit`, `created_by`, `posted_by`, `posted_at`, `reversal_of_journal_id`, `reversal_reason`, `created_at`, `updated_at`) VALUES
(6, 'JV-202609-0006', '2025-06-22', NULL, 'Central Invoice (INV - 002)', 'INV - 002', 'invoices', 2, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 1, NULL, NULL, NULL, NULL, '2026-09-22 12:08:45', '2026-09-22 12:08:45'),
(15, 'JV-202609-0011', '2025-06-22', NULL, 'Central Invoice (INV - 001)', 'INV - 001', 'invoices', 1, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 1, NULL, NULL, NULL, NULL, '2026-09-24 05:40:46', '2026-09-24 05:40:46'),
(16, 'JV-202609-0012', '2025-06-22', NULL, 'Central Invoice (INV - 004)', 'INV - 004', 'invoices', 4, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 1, NULL, NULL, NULL, NULL, '2026-09-24 05:41:23', '2026-09-24 05:41:23'),
(17, 'JV-202609-0013', '2025-06-22', NULL, 'Central Invoice (INV - 005)', 'INV - 005', 'invoices', 5, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 1, NULL, NULL, NULL, NULL, '2026-09-24 05:41:41', '2026-09-24 05:41:41'),
(18, 'JV-202609-0014', '2025-06-22', NULL, 'Central Invoice (INV - 008)', 'INV - 008', 'invoices', 10, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 05:42:10', '2026-09-24 05:42:10'),
(19, 'JV-202609-0015', '2025-06-22', NULL, 'Central Invoice (INV - 006)', 'INV - 006', 'invoices', 8, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 1, NULL, NULL, NULL, NULL, '2026-09-24 05:42:13', '2026-09-24 05:42:13'),
(20, 'JV-202609-0016', '2025-06-22', NULL, 'Central Invoice (INV - 007)', 'INV - 007', 'invoices', 9, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 1, NULL, NULL, NULL, NULL, '2026-09-24 05:42:44', '2026-09-24 05:42:44'),
(23, 'JV-202609-0019', '2025-06-22', NULL, 'Central Invoice (INV - 010)', 'INV - 010', 'invoices', 12, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 06:14:03', '2026-09-24 06:14:03'),
(25, 'JV-202609-0020', '2025-06-22', NULL, 'Central Invoice (INV - 011)', 'INV - 011', 'invoices', 13, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 06:14:59', '2026-09-24 06:14:59'),
(26, 'JV-202609-0021', '2025-06-22', NULL, 'Central Invoice (INV - 012)', 'INV - 012', 'invoices', 14, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 06:15:49', '2026-09-24 06:15:49'),
(33, 'JV-202609-0028', '2025-06-22', NULL, 'Central Invoice (INV - 018)', 'INV - 018', 'invoices', 20, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 07:26:31', '2026-09-24 07:26:31'),
(34, 'JV-202609-0029', '2025-06-22', NULL, 'Central Invoice (INV - 019)', 'INV - 019', 'invoices', 21, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 07:31:43', '2026-09-24 07:31:43'),
(35, 'JV-202609-0030', '2025-06-22', NULL, 'Central Invoice (INV - 020)', 'INV - 020', 'invoices', 22, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 07:33:33', '2026-09-24 07:33:33'),
(36, 'JV-202609-0031', '2025-06-22', NULL, 'Central Invoice (INV - 021)', 'INV - 021', 'invoices', 23, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 07:34:12', '2026-09-24 07:34:12'),
(37, 'JV-202609-0032', '2026-06-22', NULL, 'Central Invoice (INV - 022)', 'INV - 022', 'invoices', 24, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 07:35:13', '2026-09-24 07:35:13'),
(38, 'JV-202609-0033', '2025-06-22', NULL, 'Central Invoice (INV - 023)', 'INV - 023', 'invoices', 25, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 07:36:06', '2026-09-24 07:36:06'),
(39, 'JV-202609-0034', '2025-06-22', NULL, 'Central Invoice (INV - 024)', 'INV - 024', 'invoices', 26, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 07:42:57', '2026-09-24 07:42:57'),
(40, 'JV-202609-0035', '2025-06-22', NULL, 'Central Invoice (INV - 025)', 'INV - 025', 'invoices', 27, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 07:43:47', '2026-09-24 07:43:47'),
(41, 'JV-202609-0036', '2025-06-22', NULL, 'Central Invoice (INV - 026)', 'INV - 026', 'invoices', 28, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 07:44:36', '2026-09-24 07:44:36'),
(42, 'JV-202609-0037', '2025-06-22', NULL, 'Central Invoice (INV - 027)', 'INV - 027', 'invoices', 29, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 07:45:25', '2026-09-24 07:45:25'),
(43, 'JV-202609-0038', '2025-06-22', NULL, 'Central Invoice (INV - 028)', 'INV - 028', 'invoices', 30, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 07:46:08', '2026-09-24 07:46:08'),
(44, 'JV-202609-0039', '2026-10-04', NULL, 'Central Invoice (INV - 029)', 'INV - 029', 'invoices', 31, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 07:46:42', '2026-09-24 07:46:42'),
(45, 'JV-202609-0040', '2025-10-04', NULL, 'Central Invoice (INV - 030)', 'INV - 030', 'invoices', 32, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 09:34:47', '2026-09-24 09:34:47'),
(46, 'JV-202609-0041', '2025-10-04', NULL, 'Central Invoice (INV - 031)', 'INV - 031', 'invoices', 33, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 09:35:34', '2026-09-24 09:35:34'),
(47, 'JV-202609-0042', '2025-10-04', NULL, 'Central Invoice (INV - 032)', 'INV - 032', 'invoices', 34, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 09:36:25', '2026-09-24 09:36:25'),
(48, 'JV-202609-0043', '2025-10-04', NULL, 'Central Invoice (INV - 033)', 'INV - 033', 'invoices', 35, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 09:37:14', '2026-09-24 09:37:14'),
(49, 'JV-202609-0044', '2025-10-04', NULL, 'Central Invoice (INV - 034)', 'INV - 034', 'invoices', 36, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 09:41:45', '2026-09-24 09:41:45'),
(50, 'JV-202609-0045', '2025-10-04', NULL, 'Central Invoice (INV - 035)', 'INV - 035', 'invoices', 37, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 09:51:18', '2026-09-24 09:51:18'),
(51, 'JV-202609-0046', '2025-10-04', NULL, 'Central Invoice (INV - 036)', 'INV - 036', 'invoices', 38, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 09:51:54', '2026-09-24 09:51:54'),
(52, 'JV-202609-0047', '2025-10-04', NULL, 'Central Invoice (INV - 037)', 'INV - 037', 'invoices', 39, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 09:52:22', '2026-09-24 09:52:22'),
(53, 'JV-202609-0048', '2025-10-04', NULL, 'Central Invoice (INV - 038)', 'INV - 038', 'invoices', 40, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 09:53:04', '2026-09-24 09:53:04'),
(54, 'JV-202609-0049', '2025-10-04', NULL, 'Central Invoice (INV - 039)', 'INV - 039', 'invoices', 41, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 09:53:36', '2026-09-24 09:53:36'),
(55, 'JV-202609-0050', '2025-10-04', NULL, 'Central Invoice (INV - 040)', 'INV - 040', 'invoices', 42, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 09:54:03', '2026-09-24 09:54:03'),
(56, 'JV-202609-0051', '2025-10-04', NULL, 'Central Invoice (INV - 041)', 'INV - 041', 'invoices', 43, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 09:58:48', '2026-09-24 09:58:48'),
(57, 'JV-202609-0052', '2025-10-04', NULL, 'Central Invoice (INV - 042)', 'INV - 042', 'invoices', 44, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 10:05:04', '2026-09-24 10:05:04'),
(58, 'JV-202609-0053', '2025-10-04', NULL, 'Central Invoice (INV - 043)', 'INV - 043', 'invoices', 45, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 10:11:29', '2026-09-24 10:11:29'),
(59, 'JV-202609-0054', '2025-10-18', NULL, 'Central Invoice (INV - 044)', 'INV - 044', 'invoices', 46, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 10:12:51', '2026-09-24 10:12:51'),
(60, 'JV-202609-0055', '2025-10-18', NULL, 'Central Invoice (INV - 045)', 'INV - 045', 'invoices', 47, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 10:13:26', '2026-09-24 10:13:26'),
(61, 'JV-202609-0056', '2025-10-19', NULL, 'Central Invoice (INV - 046)', 'INV - 046', 'invoices', 48, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 10:13:58', '2026-09-24 10:13:58'),
(62, 'JV-202609-0057', '2025-10-19', NULL, 'Central Invoice (INV - 047)', 'INV - 047', 'invoices', 49, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 10:14:28', '2026-09-24 10:14:28'),
(63, 'JV-202609-0058', '2025-10-19', NULL, 'Central Invoice (INV - 048)', 'INV - 048', 'invoices', 50, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 10:15:01', '2026-09-24 10:15:01'),
(64, 'JV-202609-0059', '2025-11-05', NULL, 'Central Invoice (INV - 049)', 'INV - 049', 'invoices', 51, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 10:15:30', '2026-09-24 10:15:30'),
(66, 'JV-202609-0060', '2025-11-05', NULL, 'Central Invoice (INV - 050)', 'INV - 050', 'invoices', 52, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 10:16:11', '2026-09-24 10:16:11'),
(67, 'JV-202609-0061', '2025-11-06', NULL, 'Central Invoice (INV - 051)', 'INV - 051', 'invoices', 53, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 10:16:45', '2026-09-24 10:16:45'),
(68, 'JV-202609-0062', '2025-11-07', NULL, 'Central Invoice (INV - 052)', 'INV - 052', 'invoices', 54, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 10:17:14', '2026-09-24 10:17:14'),
(69, 'JV-202609-0063', '2025-11-09', NULL, 'Central Invoice (INV - 053)', 'INV - 053', 'invoices', 55, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 10:17:46', '2026-09-24 10:17:46'),
(70, 'JV-202609-0064', '2025-12-27', NULL, 'Central Invoice (INV - 054)', 'INV - 054', 'invoices', 56, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 10:18:21', '2026-09-24 10:18:21'),
(71, 'JV-202609-0065', '2025-12-28', NULL, 'Central Invoice (INV - 055)', 'INV - 055', 'invoices', 57, NULL, NULL, NULL, 'posted', 25000.00, 25000.00, 1, NULL, NULL, NULL, NULL, '2026-09-24 11:08:47', '2026-09-24 11:08:47'),
(72, 'JV-202609-0066', '2026-09-24', NULL, 'Central Invoice (INV - 056)', 'INV - 056', 'invoices', 58, NULL, NULL, NULL, 'posted', 1000.00, 1000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 11:15:32', '2026-09-24 11:15:32'),
(73, 'JV-202609-0067', '2026-01-02', NULL, 'Central Invoice (INV - 057)', 'INV - 057', 'invoices', 59, NULL, NULL, NULL, 'posted', 1000.00, 1000.00, 6, NULL, NULL, NULL, NULL, '2026-09-24 11:16:35', '2026-09-24 11:16:35'),
(74, 'JV-202609-0068', '2026-02-04', NULL, 'Central Invoice (INV - 058)', 'INV - 058', 'invoices', 60, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 05:33:34', '2026-09-25 05:33:34'),
(75, 'JV-202609-0069', '2026-02-24', NULL, 'Central Invoice (INV - 059)', 'INV - 059', 'invoices', 61, NULL, NULL, NULL, 'posted', 1000.00, 1000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 05:34:17', '2026-09-25 05:34:17'),
(76, 'JV-202609-0070', '2026-03-03', NULL, 'Central Invoice (INV - 060)', 'INV - 060', 'invoices', 62, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 05:34:52', '2026-09-25 05:34:52'),
(77, 'JV-202609-0071', '2026-03-03', NULL, 'Central Invoice (INV - 061)', 'INV - 061', 'invoices', 63, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 05:35:23', '2026-09-25 05:35:23'),
(78, 'JV-202609-0072', '2026-03-03', NULL, 'Central Invoice (INV - 062)', 'INV - 062', 'invoices', 64, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 05:35:56', '2026-09-25 05:35:56'),
(79, 'JV-202609-0073', '2026-03-03', NULL, 'Central Invoice (INV - 063)', 'INV - 063', 'invoices', 65, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 05:36:29', '2026-09-25 05:36:29'),
(80, 'JV-202609-0074', '2026-03-19', NULL, 'Central Invoice (INV - 064)', 'INV - 064', 'invoices', 66, NULL, NULL, NULL, 'posted', 14000.00, 14000.00, 1, NULL, NULL, NULL, NULL, '2026-09-25 06:03:02', '2026-09-25 06:03:02'),
(81, 'JV-202609-0075', '2026-03-19', NULL, 'Central Invoice (INV - 065)', 'INV - 065', 'invoices', 67, NULL, NULL, NULL, 'posted', 32000.00, 32000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:16:18', '2026-09-25 06:16:18'),
(82, 'JV-202609-0076', '2026-03-27', NULL, 'Central Invoice (INV - 066)', 'INV - 066', 'invoices', 68, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:16:53', '2026-09-25 06:16:53'),
(83, 'JV-202609-0077', '2026-03-27', NULL, 'Central Invoice (INV - 067)', 'INV - 067', 'invoices', 69, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:17:23', '2026-09-25 06:17:23'),
(84, 'JV-202609-0078', '2026-03-27', NULL, 'Central Invoice (INV - 068)', 'INV - 068', 'invoices', 70, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:17:52', '2026-09-25 06:17:52'),
(85, 'JV-202609-0079', '2026-03-27', NULL, 'Central Invoice (INV - 069)', 'INV - 069', 'invoices', 71, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:18:48', '2026-09-25 06:18:48'),
(86, 'JV-202609-0080', '2026-03-27', NULL, 'Central Invoice (INV - 070)', 'INV - 070', 'invoices', 72, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:19:18', '2026-09-25 06:19:18'),
(87, 'JV-202609-0081', '2026-03-27', NULL, 'Central Invoice (INV - 071)', 'INV - 071', 'invoices', 73, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:19:55', '2026-09-25 06:19:55'),
(88, 'JV-202609-0082', '2026-03-27', NULL, 'Central Invoice (INV - 072)', 'INV - 072', 'invoices', 74, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:20:32', '2026-09-25 06:20:32'),
(89, 'JV-202609-0083', '2026-03-27', NULL, 'Central Invoice (INV - 073)', 'INV - 073', 'invoices', 75, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:21:05', '2026-09-25 06:21:05'),
(90, 'JV-202609-0084', '2026-03-27', NULL, 'Central Invoice (INV - 074)', 'INV - 074', 'invoices', 76, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:21:31', '2026-09-25 06:21:31'),
(91, 'JV-202609-0085', '2026-03-27', NULL, 'Central Invoice (INV - 075)', 'INV - 075', 'invoices', 77, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:22:08', '2026-09-25 06:22:08'),
(92, 'JV-202609-0086', '2026-04-03', NULL, 'Central Invoice (INV - 076)', 'INV - 076', 'invoices', 78, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:22:35', '2026-09-25 06:22:35'),
(93, 'JV-202609-0087', '2026-04-03', NULL, 'Central Invoice (INV - 077)', 'INV - 077', 'invoices', 79, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:23:04', '2026-09-25 06:23:04'),
(94, 'JV-202609-0088', '2026-04-03', NULL, 'Central Invoice (INV - 078)', 'INV - 078', 'invoices', 80, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:23:31', '2026-09-25 06:23:31'),
(95, 'JV-202609-0089', '2026-04-03', NULL, 'Central Invoice (INV - 079)', 'INV - 079', 'invoices', 81, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:23:57', '2026-09-25 06:23:57'),
(96, 'JV-202609-0090', '2026-04-03', NULL, 'Central Invoice (INV - 080)', 'INV - 080', 'invoices', 82, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:24:21', '2026-09-25 06:24:21'),
(97, 'JV-202609-0091', '2026-04-03', NULL, 'Central Invoice (INV - 081)', 'INV - 081', 'invoices', 83, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:24:48', '2026-09-25 06:24:48'),
(98, 'JV-202609-0092', '2026-04-03', NULL, 'Central Invoice (INV - 082)', 'INV - 082', 'invoices', 84, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:25:14', '2026-09-25 06:25:14'),
(99, 'JV-202609-0093', '2026-04-03', NULL, 'Central Invoice (INV - 083)', 'INV - 083', 'invoices', 85, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:25:36', '2026-09-25 06:25:36'),
(100, 'JV-202609-0094', '2026-04-03', NULL, 'Central Invoice (INV - 084)', 'INV - 084', 'invoices', 86, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:26:02', '2026-09-25 06:26:02'),
(101, 'JV-202609-0095', '2026-04-03', NULL, 'Central Invoice (INV - 085)', 'INV - 085', 'invoices', 87, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:26:31', '2026-09-25 06:26:31'),
(102, 'JV-202609-0096', '2026-04-03', NULL, 'Central Invoice (INV - 086)', 'INV - 086', 'invoices', 88, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:26:54', '2026-09-25 06:26:54'),
(103, 'JV-202609-0097', '2026-04-03', NULL, 'Central Invoice (INV - 087)', 'INV - 087', 'invoices', 89, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:27:25', '2026-09-25 06:27:25'),
(104, 'JV-202609-0098', '2026-04-03', NULL, 'Central Invoice (INV - 088)', 'INV - 088', 'invoices', 90, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:28:06', '2026-09-25 06:28:06'),
(105, 'JV-202609-0099', '2026-04-03', NULL, 'Central Invoice (INV - 089)', 'INV - 089', 'invoices', 91, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:28:28', '2026-09-25 06:28:28'),
(107, 'JV-202609-0100', '2026-04-03', NULL, 'Central Invoice (INV - 090)', 'INV - 090', 'invoices', 92, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:32:10', '2026-09-25 06:32:10'),
(108, 'JV-202609-0101', '2026-04-03', NULL, 'Central Invoice (INV - 091)', 'INV - 091', 'invoices', 93, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:32:33', '2026-09-25 06:32:33'),
(109, 'JV-202609-0102', '2026-04-03', NULL, 'Central Invoice (INV - 092)', 'INV - 092', 'invoices', 94, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:32:57', '2026-09-25 06:32:57'),
(110, 'JV-202609-0103', '2026-04-03', NULL, 'Central Invoice (INV - 093)', 'INV - 093', 'invoices', 95, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:33:21', '2026-09-25 06:33:21'),
(111, 'JV-202609-0104', '2026-04-03', NULL, 'Central Invoice (INV - 094)', 'INV - 094', 'invoices', 96, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:33:43', '2026-09-25 06:33:43'),
(112, 'JV-202609-0105', '2026-04-03', NULL, 'Central Invoice (INV - 095)', 'INV - 095', 'invoices', 97, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:34:12', '2026-09-25 06:34:12'),
(113, 'JV-202609-0106', '2026-04-03', NULL, 'Central Invoice (INV - 096)', 'INV - 096', 'invoices', 98, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:34:36', '2026-09-25 06:34:36'),
(114, 'JV-202609-0107', '2026-04-03', NULL, 'Central Invoice (INV - 097)', 'INV - 097', 'invoices', 99, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:34:59', '2026-09-25 06:34:59'),
(115, 'JV-202609-0108', '2026-04-03', NULL, 'Central Invoice (INV - 098)', 'INV - 098', 'invoices', 100, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:35:22', '2026-09-25 06:35:22'),
(116, 'JV-202609-0109', '2026-04-03', NULL, 'Central Invoice (INV - 099)', 'INV - 099', 'invoices', 101, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:35:56', '2026-09-25 06:35:56'),
(117, 'JV-202609-0110', '2026-04-03', NULL, 'Central Invoice (INV - 100)', 'INV - 100', 'invoices', 102, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:36:20', '2026-09-25 06:36:20'),
(118, 'JV-202609-0111', '2026-04-03', NULL, 'Central Invoice (INV - 101)', 'INV - 101', 'invoices', 103, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:47:04', '2026-09-25 06:47:04'),
(119, 'JV-202609-0112', '2026-04-03', NULL, 'Central Invoice (INV - 102)', 'INV - 102', 'invoices', 104, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:47:31', '2026-09-25 06:47:31'),
(120, 'JV-202609-0113', '2026-04-03', NULL, 'Central Invoice (INV - 103)', 'INV - 103', 'invoices', 105, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:47:59', '2026-09-25 06:47:59'),
(121, 'JV-202609-0114', '2026-04-03', NULL, 'Central Invoice (INV - 104)', 'INV - 104', 'invoices', 106, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:48:29', '2026-09-25 06:48:29'),
(122, 'JV-202609-0115', '2026-04-03', NULL, 'Central Invoice (INV - 105)', 'INV - 105', 'invoices', 107, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:48:54', '2026-09-25 06:48:54'),
(123, 'JV-202609-0116', '2026-04-03', NULL, 'Central Invoice (INV - 106)', 'INV - 106', 'invoices', 108, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:51:03', '2026-09-25 06:51:03'),
(124, 'JV-202609-0117', '2026-04-03', NULL, 'Central Invoice (INV - 107)', 'INV - 107', 'invoices', 109, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:51:33', '2026-09-25 06:51:33'),
(125, 'JV-202609-0118', '2026-04-03', NULL, 'Central Invoice (INV - 108)', 'INV - 108', 'invoices', 110, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:51:57', '2026-09-25 06:51:57'),
(126, 'JV-202609-0119', '2026-04-03', NULL, 'Central Invoice (INV - 109)', 'INV - 109', 'invoices', 111, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:53:15', '2026-09-25 06:53:15'),
(127, 'JV-202609-0120', '2026-04-03', NULL, 'Central Invoice (INV - 110)', 'INV - 110', 'invoices', 112, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:53:42', '2026-09-25 06:53:42'),
(128, 'JV-202609-0121', '2026-04-03', NULL, 'Central Invoice (INV - 111)', 'INV - 111', 'invoices', 113, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:54:10', '2026-09-25 06:54:10'),
(129, 'JV-202609-0122', '2026-04-03', NULL, 'Central Invoice (INV - 112)', 'INV - 112', 'invoices', 114, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:54:42', '2026-09-25 06:54:42'),
(130, 'JV-202609-0123', '2026-04-03', NULL, 'Central Invoice (INV - 113)', 'INV - 113', 'invoices', 115, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:55:07', '2026-09-25 06:55:07'),
(131, 'JV-202609-0124', '2026-09-25', NULL, 'Central Invoice (INV - 114)', 'INV - 114', 'invoices', 116, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:55:30', '2026-09-25 06:55:30'),
(132, 'JV-202609-0125', '2026-04-03', NULL, 'Central Invoice (INV - 115)', 'INV - 115', 'invoices', 117, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:55:52', '2026-09-25 06:55:52'),
(133, 'JV-202609-0126', '2026-04-03', NULL, 'Central Invoice (INV - 116)', 'INV - 116', 'invoices', 118, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:56:19', '2026-09-25 06:56:19'),
(134, 'JV-202609-0127', '2026-04-03', NULL, 'Central Invoice (INV - 117)', 'INV - 117', 'invoices', 119, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:56:41', '2026-09-25 06:56:41'),
(135, 'JV-202609-0128', '2026-04-03', NULL, 'Central Invoice (INV - 118)', 'INV - 118', 'invoices', 120, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:57:08', '2026-09-25 06:57:08'),
(136, 'JV-202609-0129', '2026-04-03', NULL, 'Central Invoice (INV - 119)', 'INV - 119', 'invoices', 121, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:57:38', '2026-09-25 06:57:38'),
(137, 'JV-202609-0130', '2026-04-03', NULL, 'Central Invoice (INV - 120)', 'INV - 120', 'invoices', 122, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:59:25', '2026-09-25 06:59:25'),
(138, 'JV-202609-0131', '2026-04-04', NULL, 'Central Invoice (INV - 121)', 'INV - 121', 'invoices', 123, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 06:59:56', '2026-09-25 06:59:56'),
(139, 'JV-202609-0132', '2026-04-04', NULL, 'Central Invoice (INV - 122)', 'INV - 122', 'invoices', 124, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:00:24', '2026-09-25 07:00:24'),
(140, 'JV-202609-0133', '2026-04-04', NULL, 'Central Invoice (INV - 123)', 'INV - 123', 'invoices', 125, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:00:49', '2026-09-25 07:00:49'),
(141, 'JV-202609-0134', '2026-04-04', NULL, 'Central Invoice (INV - 124)', 'INV - 124', 'invoices', 126, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:01:25', '2026-09-25 07:01:25'),
(142, 'JV-202609-0135', '2026-04-04', NULL, 'Central Invoice (INV - 125)', 'INV - 125', 'invoices', 127, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:05:14', '2026-09-25 07:05:14'),
(143, 'JV-202609-0136', '2026-04-04', NULL, 'Central Invoice (INV - 126)', 'INV - 126', 'invoices', 128, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:05:38', '2026-09-25 07:05:38'),
(144, 'JV-202609-0137', '2026-04-04', NULL, 'Central Invoice (INV - 127)', 'INV - 127', 'invoices', 129, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:06:01', '2026-09-25 07:06:01'),
(146, 'JV-202609-0138', '2026-04-04', NULL, 'Central Invoice (INV - 128)', 'INV - 128', 'invoices', 130, NULL, NULL, NULL, 'posted', 5400.00, 5400.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:07:24', '2026-09-25 07:07:24'),
(147, 'JV-202609-0139', '2026-04-04', NULL, 'Central Invoice (INV - 129)', 'INV - 129', 'invoices', 131, NULL, NULL, NULL, 'posted', 5400.00, 5400.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:08:27', '2026-09-25 07:08:27'),
(148, 'JV-202609-0140', '2026-04-04', NULL, 'Central Invoice (INV - 130)', 'INV - 130', 'invoices', 132, NULL, NULL, NULL, 'posted', 15120.00, 15120.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:12:00', '2026-09-25 07:12:00'),
(149, 'JV-202609-0141', '2026-04-04', NULL, 'Central Invoice (INV - 131)', 'INV - 131', 'invoices', 133, NULL, NULL, NULL, 'posted', 12960.00, 12960.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:15:16', '2026-09-25 07:15:16'),
(150, 'JV-202609-0142', '2026-04-04', NULL, 'Central Invoice (INV - 132)', 'INV - 132', 'invoices', 134, NULL, NULL, NULL, 'posted', 3240.00, 3240.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:16:05', '2026-09-25 07:16:05'),
(151, 'JV-202609-0143', '2026-04-04', NULL, 'Central Invoice (INV - 133)', 'INV - 133', 'invoices', 135, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:16:30', '2026-09-25 07:16:30'),
(152, 'JV-202609-0144', '2026-04-04', NULL, 'Central Invoice (INV - 134)', 'INV - 134', 'invoices', 136, NULL, NULL, NULL, 'posted', 30240.00, 30240.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:17:49', '2026-09-25 07:17:49'),
(153, 'JV-202609-0145', '2026-04-04', NULL, 'Central Invoice (INV - 135)', 'INV - 135', 'invoices', 137, NULL, NULL, NULL, 'posted', 21600.00, 21600.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:18:36', '2026-09-25 07:18:36'),
(154, 'JV-202609-0146', '2026-04-04', NULL, 'Central Invoice (INV - 136)', 'INV - 136', 'invoices', 138, NULL, NULL, NULL, 'posted', 10800.00, 10800.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:19:20', '2026-09-25 07:19:20'),
(155, 'JV-202609-0147', '2026-04-04', NULL, 'Central Invoice (INV - 137)', 'INV - 137', 'invoices', 139, NULL, NULL, NULL, 'posted', 4320.00, 4320.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 07:20:14', '2026-09-25 07:20:14'),
(156, 'JV-202609-0148', '2026-04-05', NULL, 'Central Invoice (INV - 139)', 'INV - 139', 'invoices', 141, NULL, NULL, NULL, 'posted', 6480.00, 6480.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:42:28', '2026-09-25 08:42:28'),
(157, 'JV-202609-0149', '2026-04-04', NULL, 'Central Invoice (INV - 140)', 'INV - 140', 'invoices', 142, NULL, NULL, NULL, 'posted', 5400.00, 5400.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:43:21', '2026-09-25 08:43:21'),
(158, 'JV-202609-0150', '2026-04-04', NULL, 'Central Invoice (INV - 141)', 'INV - 141', 'invoices', 143, NULL, NULL, NULL, 'posted', 5940.00, 5940.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:44:05', '2026-09-25 08:44:05'),
(159, 'JV-202609-0151', '2026-04-09', NULL, 'Central Invoice (INV - 142)', 'INV - 142', 'invoices', 144, NULL, NULL, NULL, 'posted', 2700.00, 2700.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:44:38', '2026-09-25 08:44:38'),
(160, 'JV-202609-0152', '2026-04-04', NULL, 'Central Invoice (INV - 143)', 'INV - 143', 'invoices', 145, NULL, NULL, NULL, 'posted', 4800.00, 4800.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:45:29', '2026-09-25 08:45:29'),
(161, 'JV-202609-0153', '2026-04-04', NULL, 'Central Invoice (INV - 144)', 'INV - 144', 'invoices', 146, NULL, NULL, NULL, 'posted', 3000.00, 3000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:46:55', '2026-09-25 08:46:55'),
(162, 'JV-202609-0154', '2026-04-04', NULL, 'Central Invoice (INV - 145)', 'INV - 145', 'invoices', 147, NULL, NULL, NULL, 'posted', 5400.00, 5400.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:47:42', '2026-09-25 08:47:42'),
(163, 'JV-202609-0155', '2026-04-04', NULL, 'Central Invoice (INV - 146)', 'INV - 146', 'invoices', 148, NULL, NULL, NULL, 'posted', 8100.00, 8100.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:48:26', '2026-09-25 08:48:26'),
(164, 'JV-202609-0156', '2026-04-04', NULL, 'Central Invoice (INV - 147)', 'INV - 147', 'invoices', 149, NULL, NULL, NULL, 'posted', 2400.00, 2400.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:49:42', '2026-09-25 08:49:42'),
(165, 'JV-202609-0157', '2026-04-04', NULL, 'Central Invoice (INV - 148)', 'INV - 148', 'invoices', 150, NULL, NULL, NULL, 'posted', 11340.00, 11340.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:50:16', '2026-09-25 08:50:16'),
(166, 'JV-202609-0158', '2026-04-04', NULL, 'Central Invoice (INV - 149)', 'INV - 149', 'invoices', 151, NULL, NULL, NULL, 'posted', 4860.00, 4860.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:51:00', '2026-09-25 08:51:00'),
(167, 'JV-202609-0159', '2026-04-04', NULL, 'Central Invoice (INV - 150)', 'INV - 150', 'invoices', 152, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:52:03', '2026-09-25 08:52:03'),
(168, 'JV-202609-0160', '2026-04-04', NULL, 'Central Invoice (INV - 151)', 'INV - 151', 'invoices', 153, NULL, NULL, NULL, 'posted', 15120.00, 15120.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:54:09', '2026-09-25 08:54:09'),
(169, 'JV-202609-0161', '2026-04-04', NULL, 'Central Invoice (INV - 152)', 'INV - 152', 'invoices', 154, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:55:48', '2026-09-25 08:55:48'),
(170, 'JV-202609-0162', '2026-04-05', NULL, 'Central Invoice (INV - 153)', 'INV - 153', 'invoices', 155, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:56:17', '2026-09-25 08:56:17'),
(171, 'JV-202609-0163', '2026-04-04', NULL, 'Central Invoice (INV - 154)', 'INV - 154', 'invoices', 156, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 08:56:56', '2026-09-25 08:56:56'),
(172, 'JV-202609-0164', '2026-04-04', NULL, 'Central Invoice (INV - 155)', 'INV - 155', 'invoices', 157, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:00:23', '2026-09-25 09:00:23'),
(173, 'JV-202609-0165', '2026-04-04', NULL, 'Central Invoice (INV - 156)', 'INV - 156', 'invoices', 158, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:01:02', '2026-09-25 09:01:02'),
(174, 'JV-202609-0166', '2026-04-04', NULL, 'Central Invoice (INV - 157)', 'INV - 157', 'invoices', 159, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:02:02', '2026-09-25 09:02:02'),
(175, 'JV-202609-0167', '2026-04-04', NULL, 'Central Invoice (INV - 158)', 'INV - 158', 'invoices', 160, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:02:36', '2026-09-25 09:02:36'),
(176, 'JV-202609-0168', '2026-04-04', NULL, 'Central Invoice (INV - 159)', 'INV - 159', 'invoices', 161, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:03:03', '2026-09-25 09:03:03'),
(177, 'JV-202609-0169', '2026-04-04', NULL, 'Central Invoice (INV - 160)', 'INV - 160', 'invoices', 162, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:03:34', '2026-09-25 09:03:34'),
(178, 'JV-202609-0170', '2026-04-04', NULL, 'Central Invoice (INV - 161)', 'INV - 161', 'invoices', 163, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:04:00', '2026-09-25 09:04:00'),
(179, 'JV-202609-0171', '2026-04-04', NULL, 'Central Invoice (INV - 162)', 'INV - 162', 'invoices', 164, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:05:40', '2026-09-25 09:05:40'),
(180, 'JV-202609-0172', '2026-04-04', NULL, 'Central Invoice (INV - 163)', 'INV - 163', 'invoices', 165, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:06:05', '2026-09-25 09:06:05'),
(181, 'JV-202609-0173', '2026-04-04', NULL, 'Central Invoice (INV - 164)', 'INV - 164', 'invoices', 166, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:06:44', '2026-09-25 09:06:44'),
(182, 'JV-202609-0174', '2026-04-04', NULL, 'Central Invoice (INV - 165)', 'INV - 165', 'invoices', 167, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:07:10', '2026-09-25 09:07:10'),
(183, 'JV-202609-0175', '2026-04-04', NULL, 'Central Invoice (INV - 166)', 'INV - 166', 'invoices', 168, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:07:34', '2026-09-25 09:07:34'),
(184, 'JV-202609-0176', '2026-04-04', NULL, 'Central Invoice (INV - 167)', 'INV - 167', 'invoices', 169, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:08:01', '2026-09-25 09:08:01'),
(185, 'JV-202609-0177', '2026-04-04', NULL, 'Central Invoice (INV - 168)', 'INV - 168', 'invoices', 170, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:08:27', '2026-09-25 09:08:27'),
(186, 'JV-202609-0178', '2026-04-04', NULL, 'Central Invoice (INV - 169)', 'INV - 169', 'invoices', 171, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:08:53', '2026-09-25 09:08:53'),
(187, 'JV-202609-0179', '2026-04-04', NULL, 'Central Invoice (INV - 170)', 'INV - 170', 'invoices', 172, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:09:16', '2026-09-25 09:09:16'),
(188, 'JV-202609-0180', '2026-04-04', NULL, 'Central Invoice (INV - 171)', 'INV - 171', 'invoices', 173, NULL, NULL, NULL, 'posted', 4320.00, 4320.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:09:58', '2026-09-25 09:09:58'),
(189, 'JV-202609-0181', '2026-09-25', NULL, 'Central Invoice (INV - 172)', 'INV - 172', 'invoices', 174, NULL, NULL, NULL, 'posted', 4320.00, 4320.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:10:28', '2026-09-25 09:10:28'),
(190, 'JV-202609-0182', '2026-04-04', NULL, 'Central Invoice (INV - 174)', 'INV - 174', 'invoices', 176, NULL, NULL, NULL, 'posted', 1800.00, 1800.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:12:06', '2026-09-25 09:12:06'),
(191, 'JV-202609-0183', '2026-04-04', NULL, 'Central Invoice (INV - 175)', 'INV - 175', 'invoices', 177, NULL, NULL, NULL, 'posted', 6480.00, 6480.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:13:41', '2026-09-25 09:13:41'),
(192, 'JV-202609-0184', '2026-04-04', NULL, 'Central Invoice (INV - 176)', 'INV - 176', 'invoices', 178, NULL, NULL, NULL, 'posted', 6480.00, 6480.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:17:30', '2026-09-25 09:17:30'),
(193, 'JV-202609-0185', '2026-04-04', NULL, 'Central Invoice (INV - 178)', 'INV - 178', 'invoices', 180, NULL, NULL, NULL, 'posted', 2700.00, 2700.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:18:14', '2026-09-25 09:18:14'),
(194, 'JV-202609-0186', '2026-04-04', NULL, 'Central Invoice (INV - 179)', 'INV - 179', 'invoices', 181, NULL, NULL, NULL, 'posted', 3780.00, 3780.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:18:45', '2026-09-25 09:18:45'),
(195, 'JV-202609-0187', '2026-04-04', NULL, 'Central Invoice (INV - 180)', 'INV - 180', 'invoices', 182, NULL, NULL, NULL, 'posted', 27000.00, 27000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:19:12', '2026-09-25 09:19:12'),
(196, 'JV-202609-0188', '2026-04-04', NULL, 'Central Invoice (INV - 181)', 'INV - 181', 'invoices', 183, NULL, NULL, NULL, 'posted', 1080.00, 1080.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:19:41', '2026-09-25 09:19:41'),
(197, 'JV-202609-0189', '2026-04-04', NULL, 'Central Invoice (INV - 182)', 'INV - 182', 'invoices', 184, NULL, NULL, NULL, 'posted', 5400.00, 5400.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:20:10', '2026-09-25 09:20:10'),
(198, 'JV-202609-0190', '2026-04-04', NULL, 'Central Invoice (INV - 183)', 'INV - 183', 'invoices', 185, NULL, NULL, NULL, 'posted', 9000.00, 9000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:21:11', '2026-09-25 09:21:11'),
(199, 'JV-202609-0191', '2026-04-04', NULL, 'Central Invoice (INV - 184)', 'INV - 184', 'invoices', 186, NULL, NULL, NULL, 'posted', 10800.00, 10800.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:21:43', '2026-09-25 09:21:43'),
(200, 'JV-202609-0192', '2026-04-04', NULL, 'Central Invoice (INV - 186)', 'INV - 186', 'invoices', 188, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:23:06', '2026-09-25 09:23:06'),
(201, 'JV-202609-0193', '2026-04-04', NULL, 'Central Invoice (INV - 187)', 'INV - 187', 'invoices', 189, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:23:34', '2026-09-25 09:23:34'),
(202, 'JV-202609-0194', '2026-04-04', NULL, 'Central Invoice (INV - 188)', 'INV - 188', 'invoices', 190, NULL, NULL, NULL, 'posted', 12250.00, 12250.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:24:08', '2026-09-25 09:24:08'),
(203, 'JV-202609-0195', '2026-04-04', NULL, 'Central Invoice (INV - 189)', 'INV - 189', 'invoices', 191, NULL, NULL, NULL, 'posted', 9000.00, 9000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:24:45', '2026-09-25 09:24:45'),
(204, 'JV-202609-0196', '2026-09-25', NULL, 'Central Invoice (INV - 190)', 'INV - 190', 'invoices', 192, NULL, NULL, NULL, 'posted', 12000.00, 12000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:25:32', '2026-09-25 09:25:32'),
(205, 'JV-202609-0197', '2026-04-04', NULL, 'Central Invoice (INV - 191)', 'INV - 191', 'invoices', 193, NULL, NULL, NULL, 'posted', 3600.00, 3600.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:26:07', '2026-09-25 09:26:07'),
(206, 'JV-202609-0198', '2026-04-04', NULL, 'Central Invoice (INV - 192)', 'INV - 192', 'invoices', 194, NULL, NULL, NULL, 'posted', 2400.00, 2400.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:26:34', '2026-09-25 09:26:34'),
(207, 'JV-202609-0199', '2026-04-04', NULL, 'Central Invoice (INV - 193)', 'INV - 193', 'invoices', 195, NULL, NULL, NULL, 'posted', 7500.00, 7500.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:27:02', '2026-09-25 09:27:02'),
(208, 'JV-202609-0200', '2026-04-04', NULL, 'Central Invoice (INV - 194)', 'INV - 194', 'invoices', 196, NULL, NULL, NULL, 'posted', 3000.00, 3000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:27:55', '2026-09-25 09:27:55'),
(209, 'JV-202609-0201', '2026-04-04', NULL, 'Central Invoice (INV - 195)', 'INV - 195', 'invoices', 197, NULL, NULL, NULL, 'posted', 7200.00, 7200.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:28:41', '2026-09-25 09:28:41'),
(210, 'JV-202609-0202', '2026-04-04', NULL, 'Central Invoice (INV - 196)', 'INV - 196', 'invoices', 198, NULL, NULL, NULL, 'posted', 3600.00, 3600.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:29:34', '2026-09-25 09:29:34'),
(211, 'JV-202609-0203', '2026-04-04', NULL, 'Central Invoice (INV - 197)', 'INV - 197', 'invoices', 199, NULL, NULL, NULL, 'posted', 4800.00, 4800.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:30:15', '2026-09-25 09:30:15'),
(212, 'JV-202609-0204', '2026-04-04', NULL, 'Central Invoice (INV - 198)', 'INV - 198', 'invoices', 200, NULL, NULL, NULL, 'posted', 3600.00, 3600.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:31:00', '2026-09-25 09:31:00'),
(213, 'JV-202609-0205', '2026-04-04', NULL, 'Central Invoice (INV - 199)', 'INV - 199', 'invoices', 201, NULL, NULL, NULL, 'posted', 3600.00, 3600.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:31:44', '2026-09-25 09:31:44'),
(214, 'JV-202609-0206', '2026-04-04', NULL, 'Central Invoice (INV - 200)', 'INV - 200', 'invoices', 202, NULL, NULL, NULL, 'posted', 6000.00, 6000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 09:32:24', '2026-09-25 09:32:24'),
(215, 'JV-202609-0207', '2026-04-09', NULL, 'Central Invoice (INV - 201)', 'INV - 201', 'invoices', 203, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 10:29:20', '2026-09-25 10:29:20'),
(216, 'JV-202609-0208', '2026-09-25', NULL, 'Central Invoice (INV - 202)', 'INV - 202', 'invoices', 204, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 10:44:45', '2026-09-25 10:44:45'),
(217, 'JV-202609-0209', '2026-04-10', NULL, 'Central Invoice (INV - 203)', 'INV - 203', 'invoices', 205, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 10:46:09', '2026-09-25 10:46:09'),
(218, 'JV-202609-0210', '2026-04-10', NULL, 'Central Invoice (INV - 204)', 'INV - 204', 'invoices', 206, NULL, NULL, NULL, 'posted', 10070.00, 10070.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 10:57:40', '2026-09-25 10:57:40'),
(219, 'JV-202609-0211', '2026-04-10', NULL, 'Central Invoice (INV - 205)', 'INV - 205', 'invoices', 207, NULL, NULL, NULL, 'posted', 17120.00, 17120.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 11:38:48', '2026-09-25 11:38:48'),
(220, 'JV-202609-0212', '2026-04-10', NULL, 'Central Invoice (INV - 206)', 'INV - 206', 'invoices', 208, NULL, NULL, NULL, 'posted', 11480.00, 11480.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 11:41:04', '2026-09-25 11:41:04'),
(221, 'JV-202609-0213', '2026-04-10', NULL, 'Central Invoice (INV - 207)', 'INV - 207', 'invoices', 209, NULL, NULL, NULL, 'posted', 15850.00, 15850.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 11:45:54', '2026-09-25 11:45:54'),
(222, 'JV-202609-0214', '2026-04-10', NULL, 'Central Invoice (INV - 208)', 'INV - 208', 'invoices', 210, NULL, NULL, NULL, 'posted', 15850.00, 15850.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 11:46:32', '2026-09-25 11:46:32'),
(223, 'JV-202609-0215', '2026-04-10', NULL, 'Central Invoice (INV - 209)', 'INV - 209', 'invoices', 211, NULL, NULL, NULL, 'posted', 5400.00, 5400.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 11:48:59', '2026-09-25 11:48:59'),
(224, 'JV-202609-0216', '2026-04-10', NULL, 'Central Invoice (INV - 210)', 'INV - 210', 'invoices', 212, NULL, NULL, NULL, 'posted', 12420.00, 12420.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 11:52:11', '2026-09-25 11:52:11'),
(225, 'JV-202609-0217', '2026-04-10', NULL, 'Central Invoice (INV - 211)', 'INV - 211', 'invoices', 213, NULL, NULL, NULL, 'posted', 4320.00, 4320.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 11:52:46', '2026-09-25 11:52:46'),
(226, 'JV-202609-0218', '2026-04-10', NULL, 'Central Invoice (INV - 212)', 'INV - 212', 'invoices', 214, NULL, NULL, NULL, 'posted', 7560.00, 7560.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 11:53:15', '2026-09-25 11:53:15'),
(227, 'JV-202609-0219', '2026-04-10', NULL, 'Central Invoice (INV - 213)', 'INV - 213', 'invoices', 215, NULL, NULL, NULL, 'posted', 8400.00, 8400.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 11:53:54', '2026-09-25 11:53:54'),
(230, 'JV-202609-0222', '2026-04-10', NULL, 'Central Invoice (INV - 214)', 'INV - 214', 'invoices', 216, NULL, NULL, NULL, 'posted', 8640.00, 8640.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 11:57:08', '2026-09-25 11:57:08'),
(231, 'JV-202609-0223', '2026-04-10', NULL, 'Central Invoice (INV - 215)', 'INV - 215', 'invoices', 217, NULL, NULL, NULL, 'posted', 8100.00, 8100.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 11:57:42', '2026-09-25 11:57:42'),
(232, 'JV-202609-0224', '2026-04-10', NULL, 'Central Invoice (INV - 216)', 'INV - 216', 'invoices', 218, NULL, NULL, NULL, 'posted', 2400.00, 2400.00, 6, NULL, NULL, NULL, NULL, '2026-09-25 11:58:33', '2026-09-25 11:58:33'),
(233, 'JV-202609-0225', '2026-04-10', NULL, 'Central Invoice (INV - 217)', 'INV - 217', 'invoices', 219, NULL, NULL, NULL, 'posted', 5400.00, 5400.00, 6, NULL, NULL, NULL, NULL, '2026-09-27 03:55:06', '2026-09-27 03:55:06'),
(234, 'JV-202609-0226', '2026-04-10', NULL, 'Central Invoice (INV - 218)', 'INV - 218', 'invoices', 220, NULL, NULL, NULL, 'posted', 7020.00, 7020.00, 6, NULL, NULL, NULL, NULL, '2026-09-27 03:59:16', '2026-09-27 03:59:16'),
(235, 'JV-202609-0227', '2026-09-27', NULL, 'Central Invoice (INV - 219)', 'INV - 219', 'invoices', 221, NULL, NULL, NULL, 'posted', 13500.00, 13500.00, 6, NULL, NULL, NULL, NULL, '2026-09-27 03:59:54', '2026-09-27 03:59:54'),
(236, 'JV-202609-0228', '2026-04-26', NULL, 'Central Invoice (INV - 220)', 'INV - 220', 'invoices', 222, NULL, NULL, NULL, 'posted', 3240.00, 3240.00, 6, NULL, NULL, NULL, NULL, '2026-09-27 04:51:12', '2026-09-27 04:51:12'),
(237, 'JV-202609-0229', '2026-04-26', NULL, 'Central Invoice (INV - 221)', 'INV - 221', 'invoices', 223, NULL, NULL, NULL, 'posted', 1620.00, 1620.00, 6, NULL, NULL, NULL, NULL, '2026-09-27 04:52:01', '2026-09-27 04:52:01'),
(238, 'JV-202609-0230', '2026-04-10', NULL, 'Central Invoice (INV - 222)', 'INV - 222', 'invoices', 224, NULL, NULL, NULL, 'posted', 6480.00, 6480.00, 6, NULL, NULL, NULL, NULL, '2026-09-27 04:52:44', '2026-09-27 04:52:44'),
(239, 'JV-202609-0231', '2026-04-10', NULL, 'Central Invoice (INV - 223)', 'INV - 223', 'invoices', 225, NULL, NULL, NULL, 'posted', 8100.00, 8100.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:00:36', '2026-09-28 04:00:36'),
(240, 'JV-202609-0232', '2026-04-10', NULL, 'Central Invoice (INV - 224)', 'INV - 224', 'invoices', 226, NULL, NULL, NULL, 'posted', 5400.00, 5400.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:01:13', '2026-09-28 04:01:13'),
(241, 'JV-202609-0233', '2026-04-10', NULL, 'Central Invoice (INV - 225)', 'INV - 225', 'invoices', 227, NULL, NULL, NULL, 'posted', 4860.00, 4860.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:02:44', '2026-09-28 04:02:44'),
(242, 'JV-202609-0234', '2026-04-10', NULL, 'Central Invoice (INV - 226)', 'INV - 226', 'invoices', 228, NULL, NULL, NULL, 'posted', 2160.00, 2160.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:03:13', '2026-09-28 04:03:13'),
(243, 'JV-202609-0235', '2026-04-10', NULL, 'Central Invoice (INV - 227)', 'INV - 227', 'invoices', 229, NULL, NULL, NULL, 'posted', 4320.00, 4320.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:03:44', '2026-09-28 04:03:44'),
(244, 'JV-202609-0236', '2026-04-10', NULL, 'Central Invoice (INV - 228)', 'INV - 228', 'invoices', 230, NULL, NULL, NULL, 'posted', 6480.00, 6480.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:06:20', '2026-09-28 04:06:20'),
(245, 'JV-202609-0237', '2026-04-10', NULL, 'Central Invoice (INV - 229)', 'INV - 229', 'invoices', 231, NULL, NULL, NULL, 'posted', 1200.00, 1200.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:07:09', '2026-09-28 04:07:09'),
(246, 'JV-202609-0238', '2026-04-10', NULL, 'Central Invoice (INV - 230)', 'INV - 230', 'invoices', 232, NULL, NULL, NULL, 'posted', 3780.00, 3780.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:07:42', '2026-09-28 04:07:42'),
(247, 'JV-202609-0239', '2026-04-10', NULL, 'Central Invoice (INV - 231)', 'INV - 231', 'invoices', 233, NULL, NULL, NULL, 'posted', 14580.00, 14580.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:08:19', '2026-09-28 04:08:19'),
(248, 'JV-202609-0240', '2026-04-10', NULL, 'Central Invoice (INV - 232)', 'INV - 232', 'invoices', 234, NULL, NULL, NULL, 'posted', 6000.00, 6000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:09:02', '2026-09-28 04:09:02'),
(249, 'JV-202609-0241', '2026-04-10', NULL, 'Central Invoice (INV - 233)', 'INV - 233', 'invoices', 235, NULL, NULL, NULL, 'posted', 6000.00, 6000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:09:39', '2026-09-28 04:09:39'),
(250, 'JV-202609-0242', '2026-04-10', NULL, 'Central Invoice (INV - 234)', 'INV - 234', 'invoices', 236, NULL, NULL, NULL, 'posted', 6480.00, 6480.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:10:37', '2026-09-28 04:10:37');
INSERT INTO `journal_entries` (`id`, `journal_number`, `transaction_date`, `posting_date`, `description`, `reference`, `source_module`, `source_transaction_id`, `cost_center_id`, `project_id`, `batch_id`, `status`, `total_debit`, `total_credit`, `created_by`, `posted_by`, `posted_at`, `reversal_of_journal_id`, `reversal_reason`, `created_at`, `updated_at`) VALUES
(251, 'JV-202609-0243', '2026-04-11', NULL, 'Central Invoice (INV - 237)', 'INV - 237', 'invoices', 239, NULL, NULL, NULL, 'posted', 3240.00, 3240.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:11:33', '2026-09-28 04:11:33'),
(252, 'JV-202609-0244', '2026-04-11', NULL, 'Central Invoice (INV - 238)', 'INV - 238', 'invoices', 240, NULL, NULL, NULL, 'posted', 6480.00, 6480.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:12:36', '2026-09-28 04:12:36'),
(253, 'JV-202609-0245', '2026-04-11', NULL, 'Central Invoice (INV - 240)', 'INV - 240', 'invoices', 242, NULL, NULL, NULL, 'posted', 4320.00, 4320.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:13:18', '2026-09-28 04:13:18'),
(254, 'JV-202609-0246', '2026-04-11', NULL, 'Central Invoice (INV - 241)', 'INV - 241', 'invoices', 243, NULL, NULL, NULL, 'posted', 8640.00, 8640.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:13:45', '2026-09-28 04:13:45'),
(255, 'JV-202609-0247', '2026-04-11', NULL, 'Central Invoice (INV - 242)', 'INV - 242', 'invoices', 244, NULL, NULL, NULL, 'posted', 19440.00, 19440.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:14:26', '2026-09-28 04:14:26'),
(256, 'JV-202609-0248', '2026-04-12', NULL, 'Central Invoice (INV - 243)', 'INV - 243', 'invoices', 245, NULL, NULL, NULL, 'posted', 28080.00, 28080.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:15:03', '2026-09-28 04:15:03'),
(257, 'JV-202609-0249', '2026-04-12', NULL, 'Central Invoice (INV - 244)', 'INV - 244', 'invoices', 246, NULL, NULL, NULL, 'posted', 2400.00, 2400.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:15:32', '2026-09-28 04:15:32'),
(258, 'JV-202609-0250', '2026-04-12', NULL, 'Central Invoice (INV - 245)', 'INV - 245', 'invoices', 247, NULL, NULL, NULL, 'posted', 2400.00, 2400.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:16:08', '2026-09-28 04:16:08'),
(259, 'JV-202609-0251', '2026-04-12', NULL, 'Central Invoice (INV - 246)', 'INV - 246', 'invoices', 248, NULL, NULL, NULL, 'posted', 12000.00, 12000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:16:46', '2026-09-28 04:16:46'),
(260, 'JV-202609-0252', '2026-04-12', NULL, 'Central Invoice (INV - 247)', 'INV - 247', 'invoices', 249, NULL, NULL, NULL, 'posted', 16800.00, 16800.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:17:19', '2026-09-28 04:17:19'),
(261, 'JV-202609-0253', '2026-04-12', NULL, 'Central Invoice (INV - 248)', 'INV - 248', 'invoices', 250, NULL, NULL, NULL, 'posted', 12000.00, 12000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:17:51', '2026-09-28 04:17:51'),
(262, 'JV-202609-0254', '2026-04-12', NULL, 'Central Invoice (INV - 249)', 'INV - 249', 'invoices', 251, NULL, NULL, NULL, 'posted', 4800.00, 4800.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:18:21', '2026-09-28 04:18:21'),
(263, 'JV-202609-0255', '2026-04-12', NULL, 'Central Invoice (INV - 250)', 'INV - 250', 'invoices', 252, NULL, NULL, NULL, 'posted', 3600.00, 3600.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:19:14', '2026-09-28 04:19:14'),
(264, 'JV-202609-0256', '2026-04-12', NULL, 'Central Invoice (INV - 252)', 'INV - 252', 'invoices', 254, NULL, NULL, NULL, 'posted', 10800.00, 10800.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:20:10', '2026-09-28 04:20:10'),
(265, 'JV-202609-0257', '2026-09-28', NULL, 'Central Invoice (INV - 253)', 'INV - 253', 'invoices', 255, NULL, NULL, NULL, 'posted', 6480.00, 6480.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:20:45', '2026-09-28 04:20:45'),
(266, 'JV-202609-0258', '2026-04-12', NULL, 'Central Invoice (INV - 254)', 'INV - 254', 'invoices', 256, NULL, NULL, NULL, 'posted', 10800.00, 10800.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:21:31', '2026-09-28 04:21:31'),
(267, 'JV-202609-0259', '2026-04-12', NULL, 'Central Invoice (INV - 255)', 'INV - 255', 'invoices', 257, NULL, NULL, NULL, 'posted', 7560.00, 7560.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:23:51', '2026-09-28 04:23:51'),
(268, 'JV-202609-0260', '2026-04-12', NULL, 'Central Invoice (INV - 257)', 'INV - 257', 'invoices', 259, NULL, NULL, NULL, 'posted', 10800.00, 10800.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:24:34', '2026-09-28 04:24:34'),
(269, 'JV-202609-0261', '2026-04-12', NULL, 'Central Invoice (INV - 258)', 'INV - 258', 'invoices', 260, NULL, NULL, NULL, 'posted', 5400.00, 5400.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:24:55', '2026-09-28 04:24:55'),
(270, 'JV-202609-0262', '2026-04-12', NULL, 'Central Invoice (INV - 259)', 'INV - 259', 'invoices', 261, NULL, NULL, NULL, 'posted', 30240.00, 30240.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:25:25', '2026-09-28 04:25:25'),
(271, 'JV-202609-0263', '2026-04-12', NULL, 'Central Invoice (INV - 261)', 'INV - 261', 'invoices', 263, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:26:12', '2026-09-28 04:26:12'),
(272, 'JV-202609-0264', '2026-04-12', NULL, 'Central Invoice (INV - 262)', 'INV - 262', 'invoices', 264, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:26:46', '2026-09-28 04:26:46'),
(273, 'JV-202609-0265', '2026-04-18', NULL, 'Central Invoice (INV - 263)', 'INV - 263', 'invoices', 265, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:34:38', '2026-09-28 04:34:38'),
(274, 'JV-202609-0266', '2026-04-18', NULL, 'Central Invoice (INV - 264)', 'INV - 264', 'invoices', 266, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:35:07', '2026-09-28 04:35:07'),
(275, 'JV-202609-0267', '2026-04-18', NULL, 'Central Invoice (INV - 265)', 'INV - 265', 'invoices', 267, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:35:32', '2026-09-28 04:35:32'),
(276, 'JV-202609-0268', '2026-04-18', NULL, 'Central Invoice (INV - 266)', 'INV - 266', 'invoices', 268, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:36:03', '2026-09-28 04:36:03'),
(277, 'JV-202609-0269', '2026-04-22', NULL, 'Central Invoice (INV - 267)', 'INV - 267', 'invoices', 269, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:36:35', '2026-09-28 04:36:35'),
(278, 'JV-202609-0270', '2026-04-23', NULL, 'Central Invoice (INV - 268)', 'INV - 268', 'invoices', 270, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:37:11', '2026-09-28 04:37:11'),
(279, 'JV-202609-0271', '2026-04-23', NULL, 'Central Invoice (INV - 269)', 'INV - 269', 'invoices', 271, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:37:40', '2026-09-28 04:37:40'),
(280, 'JV-202609-0272', '2026-04-23', NULL, 'Central Invoice (INV - 270)', 'INV - 270', 'invoices', 272, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:38:08', '2026-09-28 04:38:08'),
(281, 'JV-202609-0273', '2026-04-24', NULL, 'Central Invoice (INV - 271)', 'INV - 271', 'invoices', 273, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:38:47', '2026-09-28 04:38:47'),
(282, 'JV-202609-0274', '2026-04-23', NULL, 'Central Invoice (INV - 272)', 'INV - 272', 'invoices', 274, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:39:12', '2026-09-28 04:39:12'),
(284, 'JV-202609-0275', '2026-04-18', NULL, 'Central Invoice (INV - 273)', 'INV - 273', 'invoices', 275, NULL, NULL, NULL, 'posted', 52380.00, 52380.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:40:10', '2026-09-28 04:40:10'),
(285, 'JV-202609-0276', '2026-09-28', NULL, 'Central Invoice (INV - 274)', 'INV - 274', 'invoices', 276, NULL, NULL, NULL, 'posted', 4320.00, 4320.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:40:29', '2026-09-28 04:40:29'),
(286, 'JV-202609-0277', '2026-04-20', NULL, 'Central Invoice (INV - 275)', 'INV - 275', 'invoices', 277, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:40:52', '2026-09-28 04:40:52'),
(287, 'JV-202609-0278', '2026-04-20', NULL, 'Central Invoice (INV - 276)', 'INV - 276', 'invoices', 278, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:41:18', '2026-09-28 04:41:18'),
(288, 'JV-202609-0279', '2026-04-20', NULL, 'Central Invoice (INV - 277)', 'INV - 277', 'invoices', 279, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:41:46', '2026-09-28 04:41:46'),
(289, 'JV-202609-0280', '2026-04-20', NULL, 'Central Invoice (INV - 278)', 'INV - 278', 'invoices', 280, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:42:17', '2026-09-28 04:42:17'),
(290, 'JV-202609-0281', '2026-04-20', NULL, 'Central Invoice (INV - 279)', 'INV - 279', 'invoices', 281, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:42:42', '2026-09-28 04:42:42'),
(291, 'JV-202609-0282', '2026-04-20', NULL, 'Central Invoice (INV - 280)', 'INV - 280', 'invoices', 282, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:43:31', '2026-09-28 04:43:31'),
(292, 'JV-202609-0283', '2026-04-20', NULL, 'Central Invoice (INV - 281)', 'INV - 281', 'invoices', 283, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:44:00', '2026-09-28 04:44:00'),
(293, 'JV-202609-0284', '2026-04-20', NULL, 'Central Invoice (INV - 282)', 'INV - 282', 'invoices', 284, NULL, NULL, NULL, 'posted', 2000.00, 2000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:44:29', '2026-09-28 04:44:29'),
(294, 'JV-202609-0285', '2026-04-20', NULL, 'Central Invoice (INV - 283)', 'INV - 283', 'invoices', 285, NULL, NULL, NULL, 'posted', 4320.00, 4320.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:44:54', '2026-09-28 04:44:54'),
(295, 'JV-202609-0286', '2026-04-20', NULL, 'Central Invoice (INV - 284)', 'INV - 284', 'invoices', 286, NULL, NULL, NULL, 'posted', 7020.00, 7020.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:45:19', '2026-09-28 04:45:19'),
(296, 'JV-202609-0287', '2026-04-21', NULL, 'Central Invoice (INV - 285)', 'INV - 285', 'invoices', 287, NULL, NULL, NULL, 'posted', 2700.00, 2700.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:45:44', '2026-09-28 04:45:44'),
(297, 'JV-202609-0288', '2026-04-21', NULL, 'Central Invoice (INV - 286)', 'INV - 286', 'invoices', 288, NULL, NULL, NULL, 'posted', 8640.00, 8640.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:47:33', '2026-09-28 04:47:33'),
(298, 'JV-202609-0289', '2026-04-21', NULL, 'Central Invoice (INV - 287)', 'INV - 287', 'invoices', 289, NULL, NULL, NULL, 'posted', 4200.00, 4200.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:47:58', '2026-09-28 04:47:58'),
(299, 'JV-202609-0290', '2026-04-21', NULL, 'Central Invoice (INV - 288)', 'INV - 288', 'invoices', 290, NULL, NULL, NULL, 'posted', 4800.00, 4800.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:48:27', '2026-09-28 04:48:27'),
(300, 'JV-202609-0291', '2026-04-21', NULL, 'Central Invoice (INV - 290)', 'INV - 290', 'invoices', 292, NULL, NULL, NULL, 'posted', 5940.00, 5940.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:49:14', '2026-09-28 04:49:14'),
(301, 'JV-202609-0292', '2026-04-21', NULL, 'Central Invoice (INV - 291)', 'INV - 291', 'invoices', 293, NULL, NULL, NULL, 'posted', 4800.00, 4800.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:49:41', '2026-09-28 04:49:41'),
(302, 'JV-202609-0293', '2026-04-21', NULL, 'Central Invoice (INV - 292)', 'INV - 292', 'invoices', 294, NULL, NULL, NULL, 'posted', 1200.00, 1200.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:50:09', '2026-09-28 04:50:09'),
(303, 'JV-202609-0294', '2026-04-21', NULL, 'Central Invoice (INV - 293)', 'INV - 293', 'invoices', 295, NULL, NULL, NULL, 'posted', 4320.00, 4320.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:50:33', '2026-09-28 04:50:33'),
(304, 'JV-202609-0295', '2026-05-01', NULL, 'Central Invoice (INV - 294)', 'INV - 294', 'invoices', 296, NULL, NULL, NULL, 'posted', 14400.00, 14400.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:51:03', '2026-09-28 04:51:03'),
(305, 'JV-202609-0296', '2026-05-01', NULL, 'Central Invoice (INV - 295)', 'INV - 295', 'invoices', 297, NULL, NULL, NULL, 'posted', 4200.00, 4200.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:51:31', '2026-09-28 04:51:31'),
(306, 'JV-202609-0297', '2026-05-02', NULL, 'Central Invoice (INV - 296)', 'INV - 296', 'invoices', 298, NULL, NULL, NULL, 'posted', 6000.00, 6000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:52:02', '2026-09-28 04:52:02'),
(307, 'JV-202609-0298', '2026-05-02', NULL, 'Central Invoice (INV - 297)', 'INV - 297', 'invoices', 299, NULL, NULL, NULL, 'posted', 4860.00, 4860.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:52:24', '2026-09-28 04:52:24'),
(308, 'JV-202609-0299', '2026-05-05', NULL, 'Central Invoice (INV - 299)', 'INV - 299', 'invoices', 301, NULL, NULL, NULL, 'posted', 8400.00, 8400.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:54:38', '2026-09-28 04:54:38'),
(309, 'JV-202609-0300', '2026-05-03', NULL, 'Central Invoice (INV - 300)', 'INV - 300', 'invoices', 302, NULL, NULL, NULL, 'posted', 1080.00, 1080.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:55:11', '2026-09-28 04:55:11'),
(310, 'JV-202609-0301', '2026-05-03', NULL, 'Central Invoice (INV - 301)', 'INV - 301', 'invoices', 303, NULL, NULL, NULL, 'posted', 2160.00, 2160.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:58:39', '2026-09-28 04:58:39'),
(312, 'JV-202609-0303', '2026-05-06', NULL, 'Central Invoice (INV - 303)', 'INV - 303', 'invoices', 305, NULL, NULL, NULL, 'posted', 4320.00, 4320.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 04:59:36', '2026-09-28 04:59:36'),
(313, 'JV-202609-0304', '2026-05-06', NULL, 'Central Invoice (INV - 304)', 'INV - 304', 'invoices', 306, NULL, NULL, NULL, 'posted', 8640.00, 8640.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 05:00:00', '2026-09-28 05:00:00'),
(314, 'JV-202609-0305', '2026-05-06', NULL, 'Central Invoice (INV - 305)', 'INV - 305', 'invoices', 307, NULL, NULL, NULL, 'posted', 7200.00, 7200.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 05:00:35', '2026-09-28 05:00:35'),
(315, 'JV-202609-0306', '2026-05-06', NULL, 'Central Invoice (INV - 306)', 'INV - 306', 'invoices', 308, NULL, NULL, NULL, 'posted', 3600.00, 3600.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 05:01:07', '2026-09-28 05:01:07'),
(316, 'JV-202609-0307', '2026-05-06', NULL, 'Central Invoice (INV - 307)', 'INV - 307', 'invoices', 309, NULL, NULL, NULL, 'posted', 7200.00, 7200.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 05:02:05', '2026-09-28 05:02:05'),
(317, 'JV-202609-0308', '2026-05-06', NULL, 'Central Invoice (INV - 308)', 'INV - 308', 'invoices', 310, NULL, NULL, NULL, 'posted', 1800.00, 1800.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 05:12:19', '2026-09-28 05:12:19'),
(318, 'JV-202609-0309', '2026-05-06', NULL, 'Central Invoice (INV - 309)', 'INV - 309', 'invoices', 311, NULL, NULL, NULL, 'posted', 4800.00, 4800.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 05:13:20', '2026-09-28 05:13:20'),
(319, 'JV-202609-0310', '2026-05-03', NULL, 'Central Invoice (INV - 310)', 'INV - 310', 'invoices', 312, NULL, NULL, NULL, 'posted', 7560.00, 7560.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 05:15:30', '2026-09-28 05:15:30'),
(320, 'JV-202609-0311', '2026-05-06', NULL, 'Central Invoice (INV - 302)', 'INV - 302', 'invoices', 304, NULL, NULL, NULL, 'posted', 18000.00, 18000.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 05:16:16', '2026-09-28 05:16:16'),
(321, 'JV-202609-0312', '2026-05-06', NULL, 'Central Invoice (INV - 311)', 'INV - 311', 'invoices', 313, NULL, NULL, NULL, 'posted', 2400.00, 2400.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 05:18:29', '2026-09-28 05:18:29'),
(322, 'JV-202609-0313', '2026-05-06', NULL, 'Central Invoice (INV - 312)', 'INV - 312', 'invoices', 314, NULL, NULL, NULL, 'posted', 4320.00, 4320.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 05:19:37', '2026-09-28 05:19:37'),
(323, 'JV-202609-0314', '2026-05-06', NULL, 'Central Invoice (INV - 314)', 'INV - 314', 'invoices', 316, NULL, NULL, NULL, 'posted', 4200.00, 4200.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 05:20:32', '2026-09-28 05:20:32'),
(328, 'JV-202609-0315', '2026-05-06', NULL, 'Central Invoice (INV - 315)', 'INV - 315', 'invoices', 317, NULL, NULL, NULL, 'posted', 1800.00, 1800.00, 6, NULL, NULL, NULL, NULL, '2026-09-28 05:23:21', '2026-09-28 05:23:21');

-- --------------------------------------------------------

--
-- Table structure for table `journal_lines`
--

CREATE TABLE `journal_lines` (
  `id` int(10) UNSIGNED NOT NULL,
  `journal_entry_id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `reconciled` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journal_lines`
--

INSERT INTO `journal_lines` (`id`, `journal_entry_id`, `account_id`, `debit`, `credit`, `description`, `created_at`, `reconciled`) VALUES
(16, 6, 9, 2000.00, 0.00, 'Invoice INV - 002', '2026-09-22 12:08:45', 0),
(17, 6, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 002', '2026-09-22 12:08:45', 0),
(18, 6, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 002', '2026-09-22 12:08:45', 0),
(43, 15, 9, 2000.00, 0.00, 'Invoice INV - 001', '2026-09-24 05:40:46', 0),
(44, 15, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 001', '2026-09-24 05:40:46', 0),
(45, 15, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 001', '2026-09-24 05:40:46', 0),
(46, 16, 9, 2000.00, 0.00, 'Invoice INV - 004', '2026-09-24 05:41:23', 0),
(47, 16, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 004', '2026-09-24 05:41:23', 0),
(48, 16, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 004', '2026-09-24 05:41:23', 0),
(49, 17, 9, 2000.00, 0.00, 'Invoice INV - 005', '2026-09-24 05:41:41', 0),
(50, 17, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 005', '2026-09-24 05:41:41', 0),
(51, 17, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 005', '2026-09-24 05:41:41', 0),
(52, 18, 9, 2000.00, 0.00, 'Invoice INV - 008', '2026-09-24 05:42:10', 0),
(53, 18, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 008', '2026-09-24 05:42:10', 0),
(54, 18, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 008', '2026-09-24 05:42:10', 0),
(55, 19, 9, 2000.00, 0.00, 'Invoice INV - 006', '2026-09-24 05:42:13', 0),
(56, 19, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 006', '2026-09-24 05:42:13', 0),
(57, 19, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 006', '2026-09-24 05:42:13', 0),
(58, 20, 9, 2000.00, 0.00, 'Invoice INV - 007', '2026-09-24 05:42:44', 0),
(59, 20, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 007', '2026-09-24 05:42:44', 0),
(60, 20, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 007', '2026-09-24 05:42:44', 0),
(67, 23, 9, 2000.00, 0.00, 'Invoice INV - 010', '2026-09-24 06:14:03', 0),
(68, 23, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 010', '2026-09-24 06:14:03', 0),
(69, 23, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 010', '2026-09-24 06:14:03', 0),
(73, 25, 9, 2000.00, 0.00, 'Invoice INV - 011', '2026-09-24 06:14:59', 0),
(74, 25, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 011', '2026-09-24 06:14:59', 0),
(75, 25, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 011', '2026-09-24 06:14:59', 0),
(76, 26, 9, 2000.00, 0.00, 'Invoice INV - 012', '2026-09-24 06:15:49', 0),
(77, 26, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 012', '2026-09-24 06:15:49', 0),
(78, 26, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 012', '2026-09-24 06:15:49', 0),
(97, 33, 9, 2000.00, 0.00, 'Invoice INV - 018', '2026-09-24 07:26:31', 0),
(98, 33, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 018', '2026-09-24 07:26:31', 0),
(99, 33, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 018', '2026-09-24 07:26:31', 0),
(100, 34, 9, 2000.00, 0.00, 'Invoice INV - 019', '2026-09-24 07:31:43', 0),
(101, 34, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 019', '2026-09-24 07:31:43', 0),
(102, 34, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 019', '2026-09-24 07:31:43', 0),
(103, 35, 9, 2000.00, 0.00, 'Invoice INV - 020', '2026-09-24 07:33:33', 0),
(104, 35, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 020', '2026-09-24 07:33:33', 0),
(105, 35, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 020', '2026-09-24 07:33:33', 0),
(106, 36, 9, 2000.00, 0.00, 'Invoice INV - 021', '2026-09-24 07:34:12', 0),
(107, 36, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 021', '2026-09-24 07:34:12', 0),
(108, 36, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 021', '2026-09-24 07:34:12', 0),
(109, 37, 9, 2000.00, 0.00, 'Invoice INV - 022', '2026-09-24 07:35:13', 0),
(110, 37, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 022', '2026-09-24 07:35:13', 0),
(111, 37, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 022', '2026-09-24 07:35:13', 0),
(112, 38, 9, 2000.00, 0.00, 'Invoice INV - 023', '2026-09-24 07:36:06', 0),
(113, 38, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 023', '2026-09-24 07:36:06', 0),
(114, 38, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 023', '2026-09-24 07:36:06', 0),
(115, 39, 9, 2000.00, 0.00, 'Invoice INV - 024', '2026-09-24 07:42:57', 0),
(116, 39, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 024', '2026-09-24 07:42:57', 0),
(117, 39, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 024', '2026-09-24 07:42:57', 0),
(118, 40, 9, 2000.00, 0.00, 'Invoice INV - 025', '2026-09-24 07:43:47', 0),
(119, 40, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 025', '2026-09-24 07:43:47', 0),
(120, 40, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 025', '2026-09-24 07:43:47', 0),
(121, 41, 9, 2000.00, 0.00, 'Invoice INV - 026', '2026-09-24 07:44:36', 0),
(122, 41, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 026', '2026-09-24 07:44:36', 0),
(123, 41, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 026', '2026-09-24 07:44:36', 0),
(124, 42, 9, 2000.00, 0.00, 'Invoice INV - 027', '2026-09-24 07:45:25', 0),
(125, 42, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 027', '2026-09-24 07:45:25', 0),
(126, 42, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 027', '2026-09-24 07:45:25', 0),
(127, 43, 9, 2000.00, 0.00, 'Invoice INV - 028', '2026-09-24 07:46:08', 0),
(128, 43, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 028', '2026-09-24 07:46:08', 0),
(129, 43, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 028', '2026-09-24 07:46:08', 0),
(130, 44, 9, 2000.00, 0.00, 'Invoice INV - 029', '2026-09-24 07:46:42', 0),
(131, 44, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 029', '2026-09-24 07:46:42', 0),
(132, 44, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 029', '2026-09-24 07:46:42', 0),
(133, 45, 9, 2000.00, 0.00, 'Invoice INV - 030', '2026-09-24 09:34:47', 0),
(134, 45, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 030', '2026-09-24 09:34:47', 0),
(135, 45, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 030', '2026-09-24 09:34:47', 0),
(136, 46, 9, 2000.00, 0.00, 'Invoice INV - 031', '2026-09-24 09:35:34', 0),
(137, 46, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 031', '2026-09-24 09:35:34', 0),
(138, 46, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 031', '2026-09-24 09:35:34', 0),
(139, 47, 9, 2000.00, 0.00, 'Invoice INV - 032', '2026-09-24 09:36:25', 0),
(140, 47, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 032', '2026-09-24 09:36:25', 0),
(141, 47, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 032', '2026-09-24 09:36:25', 0),
(142, 48, 9, 2000.00, 0.00, 'Invoice INV - 033', '2026-09-24 09:37:14', 0),
(143, 48, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 033', '2026-09-24 09:37:14', 0),
(144, 48, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 033', '2026-09-24 09:37:14', 0),
(145, 49, 9, 2000.00, 0.00, 'Invoice INV - 034', '2026-09-24 09:41:45', 0),
(146, 49, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 034', '2026-09-24 09:41:45', 0),
(147, 49, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 034', '2026-09-24 09:41:45', 0),
(148, 50, 9, 2000.00, 0.00, 'Invoice INV - 035', '2026-09-24 09:51:18', 0),
(149, 50, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 035', '2026-09-24 09:51:18', 0),
(150, 50, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 035', '2026-09-24 09:51:18', 0),
(151, 51, 9, 2000.00, 0.00, 'Invoice INV - 036', '2026-09-24 09:51:54', 0),
(152, 51, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 036', '2026-09-24 09:51:54', 0),
(153, 51, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 036', '2026-09-24 09:51:54', 0),
(154, 52, 9, 2000.00, 0.00, 'Invoice INV - 037', '2026-09-24 09:52:22', 0),
(155, 52, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 037', '2026-09-24 09:52:22', 0),
(156, 52, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 037', '2026-09-24 09:52:22', 0),
(157, 53, 9, 2000.00, 0.00, 'Invoice INV - 038', '2026-09-24 09:53:04', 0),
(158, 53, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 038', '2026-09-24 09:53:04', 0),
(159, 53, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 038', '2026-09-24 09:53:04', 0),
(160, 54, 9, 2000.00, 0.00, 'Invoice INV - 039', '2026-09-24 09:53:36', 0),
(161, 54, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 039', '2026-09-24 09:53:36', 0),
(162, 54, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 039', '2026-09-24 09:53:36', 0),
(163, 55, 9, 2000.00, 0.00, 'Invoice INV - 040', '2026-09-24 09:54:03', 0),
(164, 55, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 040', '2026-09-24 09:54:03', 0),
(165, 55, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 040', '2026-09-24 09:54:03', 0),
(166, 56, 9, 2000.00, 0.00, 'Invoice INV - 041', '2026-09-24 09:58:48', 0),
(167, 56, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 041', '2026-09-24 09:58:48', 0),
(168, 56, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 041', '2026-09-24 09:58:48', 0),
(169, 57, 9, 2000.00, 0.00, 'Invoice INV - 042', '2026-09-24 10:05:04', 0),
(170, 57, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 042', '2026-09-24 10:05:04', 0),
(171, 57, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 042', '2026-09-24 10:05:04', 0),
(172, 58, 9, 2000.00, 0.00, 'Invoice INV - 043', '2026-09-24 10:11:29', 0),
(173, 58, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 043', '2026-09-24 10:11:29', 0),
(174, 58, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 043', '2026-09-24 10:11:29', 0),
(175, 59, 9, 2000.00, 0.00, 'Invoice INV - 044', '2026-09-24 10:12:51', 0),
(176, 59, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 044', '2026-09-24 10:12:51', 0),
(177, 59, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 044', '2026-09-24 10:12:51', 0),
(178, 60, 9, 2000.00, 0.00, 'Invoice INV - 045', '2026-09-24 10:13:26', 0),
(179, 60, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 045', '2026-09-24 10:13:26', 0),
(180, 60, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 045', '2026-09-24 10:13:26', 0),
(181, 61, 9, 2000.00, 0.00, 'Invoice INV - 046', '2026-09-24 10:13:58', 0),
(182, 61, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 046', '2026-09-24 10:13:58', 0),
(183, 61, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 046', '2026-09-24 10:13:58', 0),
(184, 62, 9, 2000.00, 0.00, 'Invoice INV - 047', '2026-09-24 10:14:28', 0),
(185, 62, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 047', '2026-09-24 10:14:28', 0),
(186, 62, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 047', '2026-09-24 10:14:28', 0),
(187, 63, 9, 2000.00, 0.00, 'Invoice INV - 048', '2026-09-24 10:15:01', 0),
(188, 63, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 048', '2026-09-24 10:15:01', 0),
(189, 63, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 048', '2026-09-24 10:15:01', 0),
(190, 64, 9, 2000.00, 0.00, 'Invoice INV - 049', '2026-09-24 10:15:30', 0),
(191, 64, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 049', '2026-09-24 10:15:30', 0),
(192, 64, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 049', '2026-09-24 10:15:30', 0),
(195, 66, 9, 2000.00, 0.00, 'Invoice INV - 050', '2026-09-24 10:16:11', 0),
(196, 66, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 050', '2026-09-24 10:16:11', 0),
(197, 66, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 050', '2026-09-24 10:16:11', 0),
(198, 67, 9, 2000.00, 0.00, 'Invoice INV - 051', '2026-09-24 10:16:45', 0),
(199, 67, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 051', '2026-09-24 10:16:45', 0),
(200, 67, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 051', '2026-09-24 10:16:45', 0),
(201, 68, 9, 2000.00, 0.00, 'Invoice INV - 052', '2026-09-24 10:17:14', 0),
(202, 68, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 052', '2026-09-24 10:17:14', 0),
(203, 68, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 052', '2026-09-24 10:17:14', 0),
(204, 69, 9, 2000.00, 0.00, 'Invoice INV - 053', '2026-09-24 10:17:46', 0),
(205, 69, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 053', '2026-09-24 10:17:46', 0),
(206, 69, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 053', '2026-09-24 10:17:46', 0),
(207, 70, 9, 2000.00, 0.00, 'Invoice INV - 054', '2026-09-24 10:18:21', 0),
(208, 70, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 054', '2026-09-24 10:18:21', 0),
(209, 70, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 054', '2026-09-24 10:18:21', 0),
(210, 71, 9, 25000.00, 0.00, 'Invoice INV - 055', '2026-09-24 11:08:47', 0),
(211, 71, 37, 0.00, 25000.00, 'Service Revenue: Invoice INV - 055', '2026-09-24 11:08:47', 0),
(212, 72, 9, 1000.00, 0.00, 'Invoice INV - 056', '2026-09-24 11:15:32', 0),
(213, 72, 37, 0.00, 1000.00, 'Service Revenue: Invoice INV - 056', '2026-09-24 11:15:32', 0),
(214, 73, 9, 1000.00, 0.00, 'Invoice INV - 057', '2026-09-24 11:16:35', 0),
(215, 73, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 057', '2026-09-24 11:16:35', 0),
(216, 74, 9, 2000.00, 0.00, 'Invoice INV - 058', '2026-09-25 05:33:34', 0),
(217, 74, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 058', '2026-09-25 05:33:34', 0),
(218, 74, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 058', '2026-09-25 05:33:34', 0),
(219, 75, 9, 1000.00, 0.00, 'Invoice INV - 059', '2026-09-25 05:34:17', 0),
(220, 75, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 059', '2026-09-25 05:34:17', 0),
(221, 76, 9, 2000.00, 0.00, 'Invoice INV - 060', '2026-09-25 05:34:52', 0),
(222, 76, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 060', '2026-09-25 05:34:52', 0),
(223, 76, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 060', '2026-09-25 05:34:52', 0),
(224, 77, 9, 2000.00, 0.00, 'Invoice INV - 061', '2026-09-25 05:35:23', 0),
(225, 77, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 061', '2026-09-25 05:35:23', 0),
(226, 77, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 061', '2026-09-25 05:35:23', 0),
(227, 78, 9, 2000.00, 0.00, 'Invoice INV - 062', '2026-09-25 05:35:56', 0),
(228, 78, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 062', '2026-09-25 05:35:56', 0),
(229, 78, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 062', '2026-09-25 05:35:56', 0),
(230, 79, 9, 2000.00, 0.00, 'Invoice INV - 063', '2026-09-25 05:36:29', 0),
(231, 79, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 063', '2026-09-25 05:36:29', 0),
(232, 79, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 063', '2026-09-25 05:36:29', 0),
(233, 80, 9, 14000.00, 0.00, 'Invoice INV - 064', '2026-09-25 06:03:02', 0),
(234, 80, 4, 0.00, 14000.00, 'Service Revenue: Invoice INV - 064', '2026-09-25 06:03:02', 0),
(235, 81, 9, 32000.00, 0.00, 'Invoice INV - 065', '2026-09-25 06:16:18', 0),
(236, 81, 4, 0.00, 32000.00, 'Service Revenue: Invoice INV - 065', '2026-09-25 06:16:18', 0),
(237, 82, 9, 2000.00, 0.00, 'Invoice INV - 066', '2026-09-25 06:16:53', 0),
(238, 82, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 066', '2026-09-25 06:16:53', 0),
(239, 82, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 066', '2026-09-25 06:16:53', 0),
(240, 83, 9, 2000.00, 0.00, 'Invoice INV - 067', '2026-09-25 06:17:23', 0),
(241, 83, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 067', '2026-09-25 06:17:23', 0),
(242, 83, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 067', '2026-09-25 06:17:23', 0),
(243, 84, 9, 2000.00, 0.00, 'Invoice INV - 068', '2026-09-25 06:17:52', 0),
(244, 84, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 068', '2026-09-25 06:17:52', 0),
(245, 84, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 068', '2026-09-25 06:17:52', 0),
(246, 85, 9, 2000.00, 0.00, 'Invoice INV - 069', '2026-09-25 06:18:48', 0),
(247, 85, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 069', '2026-09-25 06:18:48', 0),
(248, 85, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 069', '2026-09-25 06:18:48', 0),
(249, 86, 9, 2000.00, 0.00, 'Invoice INV - 070', '2026-09-25 06:19:18', 0),
(250, 86, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 070', '2026-09-25 06:19:18', 0),
(251, 86, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 070', '2026-09-25 06:19:18', 0),
(252, 87, 9, 2000.00, 0.00, 'Invoice INV - 071', '2026-09-25 06:19:55', 0),
(253, 87, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 071', '2026-09-25 06:19:55', 0),
(254, 87, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 071', '2026-09-25 06:19:55', 0),
(255, 88, 9, 2000.00, 0.00, 'Invoice INV - 072', '2026-09-25 06:20:32', 0),
(256, 88, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 072', '2026-09-25 06:20:32', 0),
(257, 88, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 072', '2026-09-25 06:20:32', 0),
(258, 89, 9, 2000.00, 0.00, 'Invoice INV - 073', '2026-09-25 06:21:05', 0),
(259, 89, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 073', '2026-09-25 06:21:05', 0),
(260, 89, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 073', '2026-09-25 06:21:05', 0),
(261, 90, 9, 2000.00, 0.00, 'Invoice INV - 074', '2026-09-25 06:21:31', 0),
(262, 90, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 074', '2026-09-25 06:21:31', 0),
(263, 90, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 074', '2026-09-25 06:21:31', 0),
(264, 91, 9, 2000.00, 0.00, 'Invoice INV - 075', '2026-09-25 06:22:08', 0),
(265, 91, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 075', '2026-09-25 06:22:08', 0),
(266, 91, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 075', '2026-09-25 06:22:08', 0),
(267, 92, 9, 2000.00, 0.00, 'Invoice INV - 076', '2026-09-25 06:22:35', 0),
(268, 92, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 076', '2026-09-25 06:22:35', 0),
(269, 92, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 076', '2026-09-25 06:22:35', 0),
(270, 93, 9, 2000.00, 0.00, 'Invoice INV - 077', '2026-09-25 06:23:04', 0),
(271, 93, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 077', '2026-09-25 06:23:04', 0),
(272, 93, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 077', '2026-09-25 06:23:04', 0),
(273, 94, 9, 2000.00, 0.00, 'Invoice INV - 078', '2026-09-25 06:23:31', 0),
(274, 94, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 078', '2026-09-25 06:23:31', 0),
(275, 94, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 078', '2026-09-25 06:23:31', 0),
(276, 95, 9, 2000.00, 0.00, 'Invoice INV - 079', '2026-09-25 06:23:57', 0),
(277, 95, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 079', '2026-09-25 06:23:57', 0),
(278, 95, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 079', '2026-09-25 06:23:57', 0),
(279, 96, 9, 2000.00, 0.00, 'Invoice INV - 080', '2026-09-25 06:24:21', 0),
(280, 96, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 080', '2026-09-25 06:24:21', 0),
(281, 96, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 080', '2026-09-25 06:24:21', 0),
(282, 97, 9, 2000.00, 0.00, 'Invoice INV - 081', '2026-09-25 06:24:48', 0),
(283, 97, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 081', '2026-09-25 06:24:48', 0),
(284, 97, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 081', '2026-09-25 06:24:48', 0),
(285, 98, 9, 2000.00, 0.00, 'Invoice INV - 082', '2026-09-25 06:25:14', 0),
(286, 98, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 082', '2026-09-25 06:25:14', 0),
(287, 98, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 082', '2026-09-25 06:25:14', 0),
(288, 99, 9, 2000.00, 0.00, 'Invoice INV - 083', '2026-09-25 06:25:36', 0),
(289, 99, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 083', '2026-09-25 06:25:36', 0),
(290, 99, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 083', '2026-09-25 06:25:36', 0),
(291, 100, 9, 2000.00, 0.00, 'Invoice INV - 084', '2026-09-25 06:26:02', 0),
(292, 100, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 084', '2026-09-25 06:26:02', 0),
(293, 100, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 084', '2026-09-25 06:26:02', 0),
(294, 101, 9, 2000.00, 0.00, 'Invoice INV - 085', '2026-09-25 06:26:31', 0),
(295, 101, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 085', '2026-09-25 06:26:31', 0),
(296, 101, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 085', '2026-09-25 06:26:31', 0),
(297, 102, 9, 2000.00, 0.00, 'Invoice INV - 086', '2026-09-25 06:26:54', 0),
(298, 102, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 086', '2026-09-25 06:26:54', 0),
(299, 102, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 086', '2026-09-25 06:26:54', 0),
(300, 103, 9, 2000.00, 0.00, 'Invoice INV - 087', '2026-09-25 06:27:25', 0),
(301, 103, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 087', '2026-09-25 06:27:25', 0),
(302, 103, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 087', '2026-09-25 06:27:25', 0),
(303, 104, 9, 2000.00, 0.00, 'Invoice INV - 088', '2026-09-25 06:28:06', 0),
(304, 104, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 088', '2026-09-25 06:28:06', 0),
(305, 104, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 088', '2026-09-25 06:28:06', 0),
(306, 105, 9, 2000.00, 0.00, 'Invoice INV - 089', '2026-09-25 06:28:28', 0),
(307, 105, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 089', '2026-09-25 06:28:28', 0),
(308, 105, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 089', '2026-09-25 06:28:28', 0),
(312, 107, 9, 2000.00, 0.00, 'Invoice INV - 090', '2026-09-25 06:32:10', 0),
(313, 107, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 090', '2026-09-25 06:32:10', 0),
(314, 107, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 090', '2026-09-25 06:32:10', 0),
(315, 108, 9, 2000.00, 0.00, 'Invoice INV - 091', '2026-09-25 06:32:33', 0),
(316, 108, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 091', '2026-09-25 06:32:33', 0),
(317, 108, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 091', '2026-09-25 06:32:33', 0),
(318, 109, 9, 2000.00, 0.00, 'Invoice INV - 092', '2026-09-25 06:32:57', 0),
(319, 109, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 092', '2026-09-25 06:32:57', 0),
(320, 109, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 092', '2026-09-25 06:32:57', 0),
(321, 110, 9, 2000.00, 0.00, 'Invoice INV - 093', '2026-09-25 06:33:21', 0),
(322, 110, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 093', '2026-09-25 06:33:21', 0),
(323, 110, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 093', '2026-09-25 06:33:21', 0),
(324, 111, 9, 2000.00, 0.00, 'Invoice INV - 094', '2026-09-25 06:33:43', 0),
(325, 111, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 094', '2026-09-25 06:33:43', 0),
(326, 111, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 094', '2026-09-25 06:33:43', 0),
(327, 112, 9, 2000.00, 0.00, 'Invoice INV - 095', '2026-09-25 06:34:12', 0),
(328, 112, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 095', '2026-09-25 06:34:12', 0),
(329, 112, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 095', '2026-09-25 06:34:12', 0),
(330, 113, 9, 2000.00, 0.00, 'Invoice INV - 096', '2026-09-25 06:34:36', 0),
(331, 113, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 096', '2026-09-25 06:34:36', 0),
(332, 113, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 096', '2026-09-25 06:34:36', 0),
(333, 114, 9, 2000.00, 0.00, 'Invoice INV - 097', '2026-09-25 06:34:59', 0),
(334, 114, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 097', '2026-09-25 06:34:59', 0),
(335, 114, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 097', '2026-09-25 06:34:59', 0),
(336, 115, 9, 2000.00, 0.00, 'Invoice INV - 098', '2026-09-25 06:35:22', 0),
(337, 115, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 098', '2026-09-25 06:35:22', 0),
(338, 115, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 098', '2026-09-25 06:35:22', 0),
(339, 116, 9, 2000.00, 0.00, 'Invoice INV - 099', '2026-09-25 06:35:56', 0),
(340, 116, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 099', '2026-09-25 06:35:56', 0),
(341, 116, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 099', '2026-09-25 06:35:56', 0),
(342, 117, 9, 2000.00, 0.00, 'Invoice INV - 100', '2026-09-25 06:36:20', 0),
(343, 117, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 100', '2026-09-25 06:36:20', 0),
(344, 117, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 100', '2026-09-25 06:36:20', 0),
(345, 118, 9, 2000.00, 0.00, 'Invoice INV - 101', '2026-09-25 06:47:04', 0),
(346, 118, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 101', '2026-09-25 06:47:04', 0),
(347, 118, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 101', '2026-09-25 06:47:04', 0),
(348, 119, 9, 2000.00, 0.00, 'Invoice INV - 102', '2026-09-25 06:47:31', 0),
(349, 119, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 102', '2026-09-25 06:47:31', 0),
(350, 119, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 102', '2026-09-25 06:47:31', 0),
(351, 120, 9, 2000.00, 0.00, 'Invoice INV - 103', '2026-09-25 06:47:59', 0),
(352, 120, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 103', '2026-09-25 06:47:59', 0),
(353, 120, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 103', '2026-09-25 06:47:59', 0),
(354, 121, 9, 2000.00, 0.00, 'Invoice INV - 104', '2026-09-25 06:48:29', 0),
(355, 121, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 104', '2026-09-25 06:48:29', 0),
(356, 121, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 104', '2026-09-25 06:48:29', 0),
(357, 122, 9, 2000.00, 0.00, 'Invoice INV - 105', '2026-09-25 06:48:54', 0),
(358, 122, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 105', '2026-09-25 06:48:54', 0),
(359, 122, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 105', '2026-09-25 06:48:54', 0),
(360, 123, 9, 2000.00, 0.00, 'Invoice INV - 106', '2026-09-25 06:51:03', 0),
(361, 123, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 106', '2026-09-25 06:51:03', 0),
(362, 123, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 106', '2026-09-25 06:51:03', 0),
(363, 124, 9, 2000.00, 0.00, 'Invoice INV - 107', '2026-09-25 06:51:33', 0),
(364, 124, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 107', '2026-09-25 06:51:33', 0),
(365, 124, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 107', '2026-09-25 06:51:33', 0),
(366, 125, 9, 2000.00, 0.00, 'Invoice INV - 108', '2026-09-25 06:51:57', 0),
(367, 125, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 108', '2026-09-25 06:51:57', 0),
(368, 125, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 108', '2026-09-25 06:51:57', 0),
(369, 126, 9, 2000.00, 0.00, 'Invoice INV - 109', '2026-09-25 06:53:15', 0),
(370, 126, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 109', '2026-09-25 06:53:15', 0),
(371, 126, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 109', '2026-09-25 06:53:15', 0),
(372, 127, 9, 2000.00, 0.00, 'Invoice INV - 110', '2026-09-25 06:53:42', 0),
(373, 127, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 110', '2026-09-25 06:53:42', 0),
(374, 127, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 110', '2026-09-25 06:53:42', 0),
(375, 128, 9, 2000.00, 0.00, 'Invoice INV - 111', '2026-09-25 06:54:10', 0),
(376, 128, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 111', '2026-09-25 06:54:10', 0),
(377, 128, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 111', '2026-09-25 06:54:10', 0),
(378, 129, 9, 2000.00, 0.00, 'Invoice INV - 112', '2026-09-25 06:54:42', 0),
(379, 129, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 112', '2026-09-25 06:54:42', 0),
(380, 129, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 112', '2026-09-25 06:54:42', 0),
(381, 130, 9, 2000.00, 0.00, 'Invoice INV - 113', '2026-09-25 06:55:07', 0),
(382, 130, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 113', '2026-09-25 06:55:07', 0),
(383, 130, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 113', '2026-09-25 06:55:07', 0),
(384, 131, 9, 2000.00, 0.00, 'Invoice INV - 114', '2026-09-25 06:55:30', 0),
(385, 131, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 114', '2026-09-25 06:55:30', 0),
(386, 131, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 114', '2026-09-25 06:55:30', 0),
(387, 132, 9, 2000.00, 0.00, 'Invoice INV - 115', '2026-09-25 06:55:52', 0),
(388, 132, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 115', '2026-09-25 06:55:52', 0),
(389, 132, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 115', '2026-09-25 06:55:52', 0),
(390, 133, 9, 2000.00, 0.00, 'Invoice INV - 116', '2026-09-25 06:56:19', 0),
(391, 133, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 116', '2026-09-25 06:56:19', 0),
(392, 133, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 116', '2026-09-25 06:56:19', 0),
(393, 134, 9, 2000.00, 0.00, 'Invoice INV - 117', '2026-09-25 06:56:41', 0),
(394, 134, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 117', '2026-09-25 06:56:41', 0),
(395, 134, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 117', '2026-09-25 06:56:41', 0),
(396, 135, 9, 2000.00, 0.00, 'Invoice INV - 118', '2026-09-25 06:57:08', 0),
(397, 135, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 118', '2026-09-25 06:57:08', 0),
(398, 135, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 118', '2026-09-25 06:57:08', 0),
(399, 136, 9, 2000.00, 0.00, 'Invoice INV - 119', '2026-09-25 06:57:38', 0),
(400, 136, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 119', '2026-09-25 06:57:38', 0),
(401, 136, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 119', '2026-09-25 06:57:38', 0),
(402, 137, 9, 2000.00, 0.00, 'Invoice INV - 120', '2026-09-25 06:59:25', 0),
(403, 137, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 120', '2026-09-25 06:59:25', 0),
(404, 137, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 120', '2026-09-25 06:59:25', 0),
(405, 138, 9, 2000.00, 0.00, 'Invoice INV - 121', '2026-09-25 06:59:56', 0),
(406, 138, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 121', '2026-09-25 06:59:56', 0),
(407, 138, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 121', '2026-09-25 06:59:56', 0),
(408, 139, 9, 2000.00, 0.00, 'Invoice INV - 122', '2026-09-25 07:00:24', 0),
(409, 139, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 122', '2026-09-25 07:00:24', 0),
(410, 139, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 122', '2026-09-25 07:00:24', 0),
(411, 140, 9, 2000.00, 0.00, 'Invoice INV - 123', '2026-09-25 07:00:49', 0),
(412, 140, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 123', '2026-09-25 07:00:49', 0),
(413, 140, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 123', '2026-09-25 07:00:49', 0),
(414, 141, 9, 2000.00, 0.00, 'Invoice INV - 124', '2026-09-25 07:01:25', 0),
(415, 141, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 124', '2026-09-25 07:01:25', 0),
(416, 141, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 124', '2026-09-25 07:01:25', 0),
(417, 142, 9, 2000.00, 0.00, 'Invoice INV - 125', '2026-09-25 07:05:15', 0),
(418, 142, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 125', '2026-09-25 07:05:15', 0),
(419, 142, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 125', '2026-09-25 07:05:15', 0),
(420, 143, 9, 2000.00, 0.00, 'Invoice INV - 126', '2026-09-25 07:05:38', 0),
(421, 143, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 126', '2026-09-25 07:05:38', 0),
(422, 143, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 126', '2026-09-25 07:05:38', 0),
(423, 144, 9, 2000.00, 0.00, 'Invoice INV - 127', '2026-09-25 07:06:01', 0),
(424, 144, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 127', '2026-09-25 07:06:01', 0),
(425, 144, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 127', '2026-09-25 07:06:01', 0),
(428, 146, 9, 5400.00, 0.00, 'Invoice INV - 128', '2026-09-25 07:07:24', 0),
(429, 146, 4, 0.00, 5400.00, 'Service Revenue: Invoice INV - 128', '2026-09-25 07:07:24', 0),
(430, 147, 9, 5400.00, 0.00, 'Invoice INV - 129', '2026-09-25 07:08:27', 0),
(431, 147, 4, 0.00, 5400.00, 'Service Revenue: Invoice INV - 129', '2026-09-25 07:08:27', 0),
(432, 148, 9, 15120.00, 0.00, 'Invoice INV - 130', '2026-09-25 07:12:00', 0),
(433, 148, 4, 0.00, 15120.00, 'Service Revenue: Invoice INV - 130', '2026-09-25 07:12:00', 0),
(434, 149, 9, 12960.00, 0.00, 'Invoice INV - 131', '2026-09-25 07:15:16', 0),
(435, 149, 4, 0.00, 12960.00, 'Service Revenue: Invoice INV - 131', '2026-09-25 07:15:16', 0),
(436, 150, 9, 3240.00, 0.00, 'Invoice INV - 132', '2026-09-25 07:16:05', 0),
(437, 150, 4, 0.00, 3240.00, 'Service Revenue: Invoice INV - 132', '2026-09-25 07:16:05', 0),
(438, 151, 9, 2000.00, 0.00, 'Invoice INV - 133', '2026-09-25 07:16:30', 0),
(439, 151, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 133', '2026-09-25 07:16:30', 0),
(440, 151, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 133', '2026-09-25 07:16:30', 0),
(441, 152, 9, 30240.00, 0.00, 'Invoice INV - 134', '2026-09-25 07:17:49', 0),
(442, 152, 4, 0.00, 30240.00, 'Service Revenue: Invoice INV - 134', '2026-09-25 07:17:49', 0),
(443, 153, 9, 21600.00, 0.00, 'Invoice INV - 135', '2026-09-25 07:18:36', 0),
(444, 153, 4, 0.00, 21600.00, 'Service Revenue: Invoice INV - 135', '2026-09-25 07:18:36', 0),
(445, 154, 9, 10800.00, 0.00, 'Invoice INV - 136', '2026-09-25 07:19:20', 0),
(446, 154, 4, 0.00, 10800.00, 'Service Revenue: Invoice INV - 136', '2026-09-25 07:19:20', 0),
(447, 155, 9, 4320.00, 0.00, 'Invoice INV - 137', '2026-09-25 07:20:14', 0),
(448, 155, 4, 0.00, 4320.00, 'Service Revenue: Invoice INV - 137', '2026-09-25 07:20:14', 0),
(449, 156, 9, 6480.00, 0.00, 'Invoice INV - 139', '2026-09-25 08:42:28', 0),
(450, 156, 4, 0.00, 6480.00, 'Service Revenue: Invoice INV - 139', '2026-09-25 08:42:28', 0),
(451, 157, 9, 5400.00, 0.00, 'Invoice INV - 140', '2026-09-25 08:43:21', 0),
(452, 157, 4, 0.00, 5400.00, 'Service Revenue: Invoice INV - 140', '2026-09-25 08:43:21', 0),
(453, 158, 9, 5940.00, 0.00, 'Invoice INV - 141', '2026-09-25 08:44:05', 0),
(454, 158, 4, 0.00, 5940.00, 'Service Revenue: Invoice INV - 141', '2026-09-25 08:44:05', 0),
(455, 159, 9, 2700.00, 0.00, 'Invoice INV - 142', '2026-09-25 08:44:38', 0),
(456, 159, 4, 0.00, 2700.00, 'Service Revenue: Invoice INV - 142', '2026-09-25 08:44:38', 0),
(457, 160, 9, 4800.00, 0.00, 'Invoice INV - 143', '2026-09-25 08:45:29', 0),
(458, 160, 4, 0.00, 4800.00, 'Service Revenue: Invoice INV - 143', '2026-09-25 08:45:29', 0),
(459, 161, 9, 3000.00, 0.00, 'Invoice INV - 144', '2026-09-25 08:46:55', 0),
(460, 161, 4, 0.00, 3000.00, 'Service Revenue: Invoice INV - 144', '2026-09-25 08:46:55', 0),
(461, 162, 9, 5400.00, 0.00, 'Invoice INV - 145', '2026-09-25 08:47:42', 0),
(462, 162, 4, 0.00, 5400.00, 'Service Revenue: Invoice INV - 145', '2026-09-25 08:47:42', 0),
(463, 163, 9, 8100.00, 0.00, 'Invoice INV - 146', '2026-09-25 08:48:26', 0),
(464, 163, 4, 0.00, 8100.00, 'Service Revenue: Invoice INV - 146', '2026-09-25 08:48:26', 0),
(465, 164, 9, 2400.00, 0.00, 'Invoice INV - 147', '2026-09-25 08:49:42', 0),
(466, 164, 4, 0.00, 2400.00, 'Service Revenue: Invoice INV - 147', '2026-09-25 08:49:42', 0),
(467, 165, 9, 11340.00, 0.00, 'Invoice INV - 148', '2026-09-25 08:50:16', 0),
(468, 165, 4, 0.00, 11340.00, 'Service Revenue: Invoice INV - 148', '2026-09-25 08:50:16', 0),
(469, 166, 9, 4860.00, 0.00, 'Invoice INV - 149', '2026-09-25 08:51:00', 0),
(470, 166, 4, 0.00, 4860.00, 'Service Revenue: Invoice INV - 149', '2026-09-25 08:51:00', 0),
(471, 167, 9, 2000.00, 0.00, 'Invoice INV - 150', '2026-09-25 08:52:03', 0),
(472, 167, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 150', '2026-09-25 08:52:03', 0),
(473, 167, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 150', '2026-09-25 08:52:03', 0),
(474, 168, 9, 15120.00, 0.00, 'Invoice INV - 151', '2026-09-25 08:54:09', 0),
(475, 168, 4, 0.00, 15120.00, 'Service Revenue: Invoice INV - 151', '2026-09-25 08:54:09', 0),
(476, 169, 9, 2000.00, 0.00, 'Invoice INV - 152', '2026-09-25 08:55:48', 0),
(477, 169, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 152', '2026-09-25 08:55:48', 0),
(478, 169, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 152', '2026-09-25 08:55:48', 0),
(479, 170, 9, 2000.00, 0.00, 'Invoice INV - 153', '2026-09-25 08:56:17', 0),
(480, 170, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 153', '2026-09-25 08:56:17', 0),
(481, 170, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 153', '2026-09-25 08:56:17', 0),
(482, 171, 9, 2000.00, 0.00, 'Invoice INV - 154', '2026-09-25 08:56:56', 0),
(483, 171, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 154', '2026-09-25 08:56:56', 0),
(484, 171, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 154', '2026-09-25 08:56:56', 0),
(485, 172, 9, 2000.00, 0.00, 'Invoice INV - 155', '2026-09-25 09:00:23', 0),
(486, 172, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 155', '2026-09-25 09:00:23', 0),
(487, 172, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 155', '2026-09-25 09:00:23', 0),
(488, 173, 9, 2000.00, 0.00, 'Invoice INV - 156', '2026-09-25 09:01:02', 0),
(489, 173, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 156', '2026-09-25 09:01:02', 0),
(490, 173, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 156', '2026-09-25 09:01:02', 0),
(491, 174, 9, 2000.00, 0.00, 'Invoice INV - 157', '2026-09-25 09:02:02', 0),
(492, 174, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 157', '2026-09-25 09:02:02', 0),
(493, 174, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 157', '2026-09-25 09:02:02', 0),
(494, 175, 9, 2000.00, 0.00, 'Invoice INV - 158', '2026-09-25 09:02:36', 0),
(495, 175, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 158', '2026-09-25 09:02:36', 0),
(496, 175, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 158', '2026-09-25 09:02:36', 0),
(497, 176, 9, 2000.00, 0.00, 'Invoice INV - 159', '2026-09-25 09:03:03', 0),
(498, 176, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 159', '2026-09-25 09:03:03', 0),
(499, 176, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 159', '2026-09-25 09:03:03', 0),
(500, 177, 9, 2000.00, 0.00, 'Invoice INV - 160', '2026-09-25 09:03:34', 0),
(501, 177, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 160', '2026-09-25 09:03:34', 0),
(502, 177, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 160', '2026-09-25 09:03:34', 0),
(503, 178, 9, 2000.00, 0.00, 'Invoice INV - 161', '2026-09-25 09:04:00', 0),
(504, 178, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 161', '2026-09-25 09:04:00', 0),
(505, 178, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 161', '2026-09-25 09:04:00', 0),
(506, 179, 9, 2000.00, 0.00, 'Invoice INV - 162', '2026-09-25 09:05:40', 0),
(507, 179, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 162', '2026-09-25 09:05:40', 0),
(508, 179, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 162', '2026-09-25 09:05:40', 0),
(509, 180, 9, 2000.00, 0.00, 'Invoice INV - 163', '2026-09-25 09:06:05', 0),
(510, 180, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 163', '2026-09-25 09:06:05', 0),
(511, 180, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 163', '2026-09-25 09:06:05', 0),
(512, 181, 9, 2000.00, 0.00, 'Invoice INV - 164', '2026-09-25 09:06:44', 0),
(513, 181, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 164', '2026-09-25 09:06:44', 0),
(514, 181, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 164', '2026-09-25 09:06:44', 0),
(515, 182, 9, 2000.00, 0.00, 'Invoice INV - 165', '2026-09-25 09:07:10', 0),
(516, 182, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 165', '2026-09-25 09:07:10', 0),
(517, 182, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 165', '2026-09-25 09:07:10', 0),
(518, 183, 9, 2000.00, 0.00, 'Invoice INV - 166', '2026-09-25 09:07:34', 0),
(519, 183, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 166', '2026-09-25 09:07:34', 0),
(520, 183, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 166', '2026-09-25 09:07:34', 0),
(521, 184, 9, 2000.00, 0.00, 'Invoice INV - 167', '2026-09-25 09:08:01', 0),
(522, 184, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 167', '2026-09-25 09:08:01', 0),
(523, 184, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 167', '2026-09-25 09:08:01', 0),
(524, 185, 9, 2000.00, 0.00, 'Invoice INV - 168', '2026-09-25 09:08:27', 0),
(525, 185, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 168', '2026-09-25 09:08:27', 0),
(526, 185, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 168', '2026-09-25 09:08:27', 0),
(527, 186, 9, 2000.00, 0.00, 'Invoice INV - 169', '2026-09-25 09:08:53', 0),
(528, 186, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 169', '2026-09-25 09:08:53', 0),
(529, 186, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 169', '2026-09-25 09:08:53', 0),
(530, 187, 9, 2000.00, 0.00, 'Invoice INV - 170', '2026-09-25 09:09:16', 0),
(531, 187, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 170', '2026-09-25 09:09:16', 0),
(532, 187, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 170', '2026-09-25 09:09:16', 0),
(533, 188, 9, 4320.00, 0.00, 'Invoice INV - 171', '2026-09-25 09:09:58', 0),
(534, 188, 4, 0.00, 4320.00, 'Service Revenue: Invoice INV - 171', '2026-09-25 09:09:58', 0),
(535, 189, 9, 4320.00, 0.00, 'Invoice INV - 172', '2026-09-25 09:10:28', 0),
(536, 189, 4, 0.00, 4320.00, 'Service Revenue: Invoice INV - 172', '2026-09-25 09:10:28', 0),
(537, 190, 9, 1800.00, 0.00, 'Invoice INV - 174', '2026-09-25 09:12:06', 0),
(538, 190, 4, 0.00, 1800.00, 'Service Revenue: Invoice INV - 174', '2026-09-25 09:12:06', 0),
(539, 191, 9, 6480.00, 0.00, 'Invoice INV - 175', '2026-09-25 09:13:41', 0),
(540, 191, 4, 0.00, 6480.00, 'Service Revenue: Invoice INV - 175', '2026-09-25 09:13:41', 0),
(541, 192, 9, 6480.00, 0.00, 'Invoice INV - 176', '2026-09-25 09:17:30', 0),
(542, 192, 4, 0.00, 6480.00, 'Service Revenue: Invoice INV - 176', '2026-09-25 09:17:30', 0),
(543, 193, 9, 2700.00, 0.00, 'Invoice INV - 178', '2026-09-25 09:18:14', 0),
(544, 193, 4, 0.00, 2700.00, 'Service Revenue: Invoice INV - 178', '2026-09-25 09:18:14', 0),
(545, 194, 9, 3780.00, 0.00, 'Invoice INV - 179', '2026-09-25 09:18:45', 0),
(546, 194, 4, 0.00, 3780.00, 'Service Revenue: Invoice INV - 179', '2026-09-25 09:18:45', 0),
(547, 195, 9, 27000.00, 0.00, 'Invoice INV - 180', '2026-09-25 09:19:12', 0),
(548, 195, 4, 0.00, 27000.00, 'Service Revenue: Invoice INV - 180', '2026-09-25 09:19:12', 0),
(549, 196, 9, 1080.00, 0.00, 'Invoice INV - 181', '2026-09-25 09:19:41', 0),
(550, 196, 4, 0.00, 1080.00, 'Service Revenue: Invoice INV - 181', '2026-09-25 09:19:41', 0),
(551, 197, 9, 5400.00, 0.00, 'Invoice INV - 182', '2026-09-25 09:20:10', 0),
(552, 197, 4, 0.00, 5400.00, 'Service Revenue: Invoice INV - 182', '2026-09-25 09:20:10', 0),
(553, 198, 9, 9000.00, 0.00, 'Invoice INV - 183', '2026-09-25 09:21:11', 0),
(554, 198, 4, 0.00, 9000.00, 'Service Revenue: Invoice INV - 183', '2026-09-25 09:21:11', 0),
(555, 199, 9, 10800.00, 0.00, 'Invoice INV - 184', '2026-09-25 09:21:43', 0),
(556, 199, 4, 0.00, 10800.00, 'Service Revenue: Invoice INV - 184', '2026-09-25 09:21:43', 0),
(557, 200, 9, 2000.00, 0.00, 'Invoice INV - 186', '2026-09-25 09:23:06', 0),
(558, 200, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 186', '2026-09-25 09:23:06', 0),
(559, 200, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 186', '2026-09-25 09:23:06', 0),
(560, 201, 9, 2000.00, 0.00, 'Invoice INV - 187', '2026-09-25 09:23:34', 0),
(561, 201, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 187', '2026-09-25 09:23:34', 0),
(562, 201, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 187', '2026-09-25 09:23:34', 0),
(563, 202, 9, 12250.00, 0.00, 'Invoice INV - 188', '2026-09-25 09:24:08', 0),
(564, 202, 4, 0.00, 12250.00, 'Service Revenue: Invoice INV - 188', '2026-09-25 09:24:08', 0),
(565, 203, 9, 9000.00, 0.00, 'Invoice INV - 189', '2026-09-25 09:24:45', 0),
(566, 203, 4, 0.00, 9000.00, 'Service Revenue: Invoice INV - 189', '2026-09-25 09:24:45', 0),
(567, 204, 9, 12000.00, 0.00, 'Invoice INV - 190', '2026-09-25 09:25:32', 0),
(568, 204, 4, 0.00, 12000.00, 'Service Revenue: Invoice INV - 190', '2026-09-25 09:25:32', 0),
(569, 205, 9, 3600.00, 0.00, 'Invoice INV - 191', '2026-09-25 09:26:07', 0),
(570, 205, 4, 0.00, 3600.00, 'Service Revenue: Invoice INV - 191', '2026-09-25 09:26:07', 0),
(571, 206, 9, 2400.00, 0.00, 'Invoice INV - 192', '2026-09-25 09:26:34', 0),
(572, 206, 4, 0.00, 2400.00, 'Service Revenue: Invoice INV - 192', '2026-09-25 09:26:34', 0),
(573, 207, 9, 7500.00, 0.00, 'Invoice INV - 193', '2026-09-25 09:27:02', 0),
(574, 207, 4, 0.00, 7500.00, 'Service Revenue: Invoice INV - 193', '2026-09-25 09:27:02', 0),
(575, 208, 9, 3000.00, 0.00, 'Invoice INV - 194', '2026-09-25 09:27:55', 0),
(576, 208, 4, 0.00, 3000.00, 'Service Revenue: Invoice INV - 194', '2026-09-25 09:27:55', 0),
(577, 209, 9, 7200.00, 0.00, 'Invoice INV - 195', '2026-09-25 09:28:41', 0),
(578, 209, 4, 0.00, 7200.00, 'Service Revenue: Invoice INV - 195', '2026-09-25 09:28:41', 0),
(579, 210, 9, 3600.00, 0.00, 'Invoice INV - 196', '2026-09-25 09:29:34', 0),
(580, 210, 4, 0.00, 3600.00, 'Service Revenue: Invoice INV - 196', '2026-09-25 09:29:34', 0),
(581, 211, 9, 4800.00, 0.00, 'Invoice INV - 197', '2026-09-25 09:30:15', 0),
(582, 211, 4, 0.00, 4800.00, 'Service Revenue: Invoice INV - 197', '2026-09-25 09:30:15', 0),
(583, 212, 9, 3600.00, 0.00, 'Invoice INV - 198', '2026-09-25 09:31:00', 0),
(584, 212, 4, 0.00, 3600.00, 'Service Revenue: Invoice INV - 198', '2026-09-25 09:31:00', 0),
(585, 213, 9, 3600.00, 0.00, 'Invoice INV - 199', '2026-09-25 09:31:44', 0),
(586, 213, 4, 0.00, 3600.00, 'Service Revenue: Invoice INV - 199', '2026-09-25 09:31:44', 0),
(587, 214, 9, 6000.00, 0.00, 'Invoice INV - 200', '2026-09-25 09:32:24', 0),
(588, 214, 4, 0.00, 6000.00, 'Service Revenue: Invoice INV - 200', '2026-09-25 09:32:24', 0),
(589, 215, 9, 2000.00, 0.00, 'Invoice INV - 201', '2026-09-25 10:29:20', 0),
(590, 215, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 201', '2026-09-25 10:29:20', 0),
(591, 215, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 201', '2026-09-25 10:29:20', 0),
(592, 216, 9, 2000.00, 0.00, 'Invoice INV - 202', '2026-09-25 10:44:45', 0),
(593, 216, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 202', '2026-09-25 10:44:45', 0),
(594, 216, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 202', '2026-09-25 10:44:45', 0),
(595, 217, 9, 2000.00, 0.00, 'Invoice INV - 203', '2026-09-25 10:46:09', 0),
(596, 217, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 203', '2026-09-25 10:46:09', 0),
(597, 217, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 203', '2026-09-25 10:46:09', 0),
(598, 218, 9, 10070.00, 0.00, 'Invoice INV - 204', '2026-09-25 10:57:40', 0),
(599, 218, 4, 0.00, 10070.00, 'Service Revenue: Invoice INV - 204', '2026-09-25 10:57:40', 0),
(600, 219, 9, 17120.00, 0.00, 'Invoice INV - 205', '2026-09-25 11:38:48', 0),
(601, 219, 4, 0.00, 17120.00, 'Service Revenue: Invoice INV - 205', '2026-09-25 11:38:48', 0),
(602, 220, 9, 11480.00, 0.00, 'Invoice INV - 206', '2026-09-25 11:41:04', 0),
(603, 220, 4, 0.00, 11480.00, 'Service Revenue: Invoice INV - 206', '2026-09-25 11:41:04', 0),
(604, 221, 9, 15850.00, 0.00, 'Invoice INV - 207', '2026-09-25 11:45:54', 0),
(605, 221, 4, 0.00, 15850.00, 'Service Revenue: Invoice INV - 207', '2026-09-25 11:45:54', 0),
(606, 222, 9, 15850.00, 0.00, 'Invoice INV - 208', '2026-09-25 11:46:32', 0),
(607, 222, 4, 0.00, 15850.00, 'Service Revenue: Invoice INV - 208', '2026-09-25 11:46:32', 0),
(608, 223, 9, 5400.00, 0.00, 'Invoice INV - 209', '2026-09-25 11:48:59', 0),
(609, 223, 4, 0.00, 5400.00, 'Service Revenue: Invoice INV - 209', '2026-09-25 11:48:59', 0),
(610, 224, 9, 12420.00, 0.00, 'Invoice INV - 210', '2026-09-25 11:52:11', 0),
(611, 224, 4, 0.00, 12420.00, 'Service Revenue: Invoice INV - 210', '2026-09-25 11:52:11', 0),
(612, 225, 9, 4320.00, 0.00, 'Invoice INV - 211', '2026-09-25 11:52:46', 0),
(613, 225, 4, 0.00, 4320.00, 'Service Revenue: Invoice INV - 211', '2026-09-25 11:52:46', 0),
(614, 226, 9, 7560.00, 0.00, 'Invoice INV - 212', '2026-09-25 11:53:15', 0),
(615, 226, 4, 0.00, 7560.00, 'Service Revenue: Invoice INV - 212', '2026-09-25 11:53:15', 0),
(616, 227, 9, 8400.00, 0.00, 'Invoice INV - 213', '2026-09-25 11:53:54', 0),
(617, 227, 4, 0.00, 8400.00, 'Service Revenue: Invoice INV - 213', '2026-09-25 11:53:54', 0),
(622, 230, 9, 8640.00, 0.00, 'Invoice INV - 214', '2026-09-25 11:57:08', 0),
(623, 230, 4, 0.00, 8640.00, 'Service Revenue: Invoice INV - 214', '2026-09-25 11:57:08', 0),
(624, 231, 9, 8100.00, 0.00, 'Invoice INV - 215', '2026-09-25 11:57:42', 0),
(625, 231, 4, 0.00, 8100.00, 'Service Revenue: Invoice INV - 215', '2026-09-25 11:57:42', 0),
(626, 232, 9, 2400.00, 0.00, 'Invoice INV - 216', '2026-09-25 11:58:33', 0),
(627, 232, 4, 0.00, 2400.00, 'Service Revenue: Invoice INV - 216', '2026-09-25 11:58:33', 0),
(628, 233, 9, 5400.00, 0.00, 'Invoice INV - 217', '2026-09-27 03:55:06', 0),
(629, 233, 4, 0.00, 5400.00, 'Service Revenue: Invoice INV - 217', '2026-09-27 03:55:06', 0),
(630, 234, 9, 7020.00, 0.00, 'Invoice INV - 218', '2026-09-27 03:59:16', 0),
(631, 234, 4, 0.00, 7020.00, 'Service Revenue: Invoice INV - 218', '2026-09-27 03:59:16', 0),
(632, 235, 9, 13500.00, 0.00, 'Invoice INV - 219', '2026-09-27 03:59:54', 0),
(633, 235, 4, 0.00, 13500.00, 'Service Revenue: Invoice INV - 219', '2026-09-27 03:59:54', 0),
(634, 236, 9, 3240.00, 0.00, 'Invoice INV - 220', '2026-09-27 04:51:12', 0),
(635, 236, 4, 0.00, 3240.00, 'Service Revenue: Invoice INV - 220', '2026-09-27 04:51:12', 0),
(636, 237, 9, 1620.00, 0.00, 'Invoice INV - 221', '2026-09-27 04:52:01', 0),
(637, 237, 4, 0.00, 1620.00, 'Service Revenue: Invoice INV - 221', '2026-09-27 04:52:01', 0),
(638, 238, 9, 6480.00, 0.00, 'Invoice INV - 222', '2026-09-27 04:52:44', 0),
(639, 238, 4, 0.00, 6480.00, 'Service Revenue: Invoice INV - 222', '2026-09-27 04:52:44', 0),
(640, 239, 9, 8100.00, 0.00, 'Invoice INV - 223', '2026-09-28 04:00:36', 0),
(641, 239, 4, 0.00, 8100.00, 'Service Revenue: Invoice INV - 223', '2026-09-28 04:00:36', 0),
(642, 240, 9, 5400.00, 0.00, 'Invoice INV - 224', '2026-09-28 04:01:13', 0),
(643, 240, 4, 0.00, 5400.00, 'Service Revenue: Invoice INV - 224', '2026-09-28 04:01:13', 0),
(644, 241, 9, 4860.00, 0.00, 'Invoice INV - 225', '2026-09-28 04:02:44', 0),
(645, 241, 4, 0.00, 4860.00, 'Service Revenue: Invoice INV - 225', '2026-09-28 04:02:44', 0),
(646, 242, 9, 2160.00, 0.00, 'Invoice INV - 226', '2026-09-28 04:03:13', 0),
(647, 242, 4, 0.00, 2160.00, 'Service Revenue: Invoice INV - 226', '2026-09-28 04:03:13', 0),
(648, 243, 9, 4320.00, 0.00, 'Invoice INV - 227', '2026-09-28 04:03:44', 0),
(649, 243, 4, 0.00, 4320.00, 'Service Revenue: Invoice INV - 227', '2026-09-28 04:03:44', 0),
(650, 244, 9, 6480.00, 0.00, 'Invoice INV - 228', '2026-09-28 04:06:20', 0),
(651, 244, 4, 0.00, 6480.00, 'Service Revenue: Invoice INV - 228', '2026-09-28 04:06:20', 0),
(652, 245, 9, 1200.00, 0.00, 'Invoice INV - 229', '2026-09-28 04:07:09', 0),
(653, 245, 4, 0.00, 1200.00, 'Service Revenue: Invoice INV - 229', '2026-09-28 04:07:09', 0),
(654, 246, 9, 3780.00, 0.00, 'Invoice INV - 230', '2026-09-28 04:07:42', 0),
(655, 246, 4, 0.00, 3780.00, 'Service Revenue: Invoice INV - 230', '2026-09-28 04:07:42', 0),
(656, 247, 9, 14580.00, 0.00, 'Invoice INV - 231', '2026-09-28 04:08:19', 0),
(657, 247, 4, 0.00, 14580.00, 'Service Revenue: Invoice INV - 231', '2026-09-28 04:08:19', 0),
(658, 248, 9, 6000.00, 0.00, 'Invoice INV - 232', '2026-09-28 04:09:02', 0),
(659, 248, 4, 0.00, 6000.00, 'Service Revenue: Invoice INV - 232', '2026-09-28 04:09:02', 0);
INSERT INTO `journal_lines` (`id`, `journal_entry_id`, `account_id`, `debit`, `credit`, `description`, `created_at`, `reconciled`) VALUES
(660, 249, 9, 6000.00, 0.00, 'Invoice INV - 233', '2026-09-28 04:09:39', 0),
(661, 249, 4, 0.00, 6000.00, 'Service Revenue: Invoice INV - 233', '2026-09-28 04:09:39', 0),
(662, 250, 9, 6480.00, 0.00, 'Invoice INV - 234', '2026-09-28 04:10:37', 0),
(663, 250, 4, 0.00, 6480.00, 'Service Revenue: Invoice INV - 234', '2026-09-28 04:10:37', 0),
(664, 251, 9, 3240.00, 0.00, 'Invoice INV - 237', '2026-09-28 04:11:33', 0),
(665, 251, 4, 0.00, 3240.00, 'Service Revenue: Invoice INV - 237', '2026-09-28 04:11:33', 0),
(666, 252, 9, 6480.00, 0.00, 'Invoice INV - 238', '2026-09-28 04:12:36', 0),
(667, 252, 4, 0.00, 6480.00, 'Service Revenue: Invoice INV - 238', '2026-09-28 04:12:36', 0),
(668, 253, 9, 4320.00, 0.00, 'Invoice INV - 240', '2026-09-28 04:13:18', 0),
(669, 253, 4, 0.00, 4320.00, 'Service Revenue: Invoice INV - 240', '2026-09-28 04:13:18', 0),
(670, 254, 9, 8640.00, 0.00, 'Invoice INV - 241', '2026-09-28 04:13:45', 0),
(671, 254, 4, 0.00, 8640.00, 'Service Revenue: Invoice INV - 241', '2026-09-28 04:13:45', 0),
(672, 255, 9, 19440.00, 0.00, 'Invoice INV - 242', '2026-09-28 04:14:26', 0),
(673, 255, 4, 0.00, 19440.00, 'Service Revenue: Invoice INV - 242', '2026-09-28 04:14:26', 0),
(674, 256, 9, 28080.00, 0.00, 'Invoice INV - 243', '2026-09-28 04:15:03', 0),
(675, 256, 4, 0.00, 28080.00, 'Service Revenue: Invoice INV - 243', '2026-09-28 04:15:03', 0),
(676, 257, 9, 2400.00, 0.00, 'Invoice INV - 244', '2026-09-28 04:15:32', 0),
(677, 257, 4, 0.00, 2400.00, 'Service Revenue: Invoice INV - 244', '2026-09-28 04:15:32', 0),
(678, 258, 9, 2400.00, 0.00, 'Invoice INV - 245', '2026-09-28 04:16:08', 0),
(679, 258, 4, 0.00, 2400.00, 'Service Revenue: Invoice INV - 245', '2026-09-28 04:16:08', 0),
(680, 259, 9, 12000.00, 0.00, 'Invoice INV - 246', '2026-09-28 04:16:46', 0),
(681, 259, 4, 0.00, 12000.00, 'Service Revenue: Invoice INV - 246', '2026-09-28 04:16:46', 0),
(682, 260, 9, 16800.00, 0.00, 'Invoice INV - 247', '2026-09-28 04:17:19', 0),
(683, 260, 4, 0.00, 16800.00, 'Service Revenue: Invoice INV - 247', '2026-09-28 04:17:19', 0),
(684, 261, 9, 12000.00, 0.00, 'Invoice INV - 248', '2026-09-28 04:17:51', 0),
(685, 261, 4, 0.00, 12000.00, 'Service Revenue: Invoice INV - 248', '2026-09-28 04:17:51', 0),
(686, 262, 9, 4800.00, 0.00, 'Invoice INV - 249', '2026-09-28 04:18:21', 0),
(687, 262, 4, 0.00, 4800.00, 'Service Revenue: Invoice INV - 249', '2026-09-28 04:18:21', 0),
(688, 263, 9, 3600.00, 0.00, 'Invoice INV - 250', '2026-09-28 04:19:14', 0),
(689, 263, 4, 0.00, 3600.00, 'Service Revenue: Invoice INV - 250', '2026-09-28 04:19:14', 0),
(690, 264, 9, 10800.00, 0.00, 'Invoice INV - 252', '2026-09-28 04:20:10', 0),
(691, 264, 4, 0.00, 10800.00, 'Service Revenue: Invoice INV - 252', '2026-09-28 04:20:10', 0),
(692, 265, 9, 6480.00, 0.00, 'Invoice INV - 253', '2026-09-28 04:20:45', 0),
(693, 265, 4, 0.00, 6480.00, 'Service Revenue: Invoice INV - 253', '2026-09-28 04:20:45', 0),
(694, 266, 9, 10800.00, 0.00, 'Invoice INV - 254', '2026-09-28 04:21:31', 0),
(695, 266, 4, 0.00, 10800.00, 'Service Revenue: Invoice INV - 254', '2026-09-28 04:21:31', 0),
(696, 267, 9, 7560.00, 0.00, 'Invoice INV - 255', '2026-09-28 04:23:51', 0),
(697, 267, 4, 0.00, 7560.00, 'Service Revenue: Invoice INV - 255', '2026-09-28 04:23:51', 0),
(698, 268, 9, 10800.00, 0.00, 'Invoice INV - 257', '2026-09-28 04:24:34', 0),
(699, 268, 4, 0.00, 10800.00, 'Service Revenue: Invoice INV - 257', '2026-09-28 04:24:34', 0),
(700, 269, 9, 5400.00, 0.00, 'Invoice INV - 258', '2026-09-28 04:24:55', 0),
(701, 269, 4, 0.00, 5400.00, 'Service Revenue: Invoice INV - 258', '2026-09-28 04:24:55', 0),
(702, 270, 9, 30240.00, 0.00, 'Invoice INV - 259', '2026-09-28 04:25:25', 0),
(703, 270, 4, 0.00, 30240.00, 'Service Revenue: Invoice INV - 259', '2026-09-28 04:25:25', 0),
(704, 271, 9, 2000.00, 0.00, 'Invoice INV - 261', '2026-09-28 04:26:12', 0),
(705, 271, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 261', '2026-09-28 04:26:12', 0),
(706, 271, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 261', '2026-09-28 04:26:12', 0),
(707, 272, 9, 2000.00, 0.00, 'Invoice INV - 262', '2026-09-28 04:26:46', 0),
(708, 272, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 262', '2026-09-28 04:26:46', 0),
(709, 272, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 262', '2026-09-28 04:26:46', 0),
(710, 273, 9, 2000.00, 0.00, 'Invoice INV - 263', '2026-09-28 04:34:38', 0),
(711, 273, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 263', '2026-09-28 04:34:38', 0),
(712, 273, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 263', '2026-09-28 04:34:38', 0),
(713, 274, 9, 2000.00, 0.00, 'Invoice INV - 264', '2026-09-28 04:35:07', 0),
(714, 274, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 264', '2026-09-28 04:35:07', 0),
(715, 274, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 264', '2026-09-28 04:35:07', 0),
(716, 275, 9, 2000.00, 0.00, 'Invoice INV - 265', '2026-09-28 04:35:32', 0),
(717, 275, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 265', '2026-09-28 04:35:32', 0),
(718, 275, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 265', '2026-09-28 04:35:32', 0),
(719, 276, 9, 2000.00, 0.00, 'Invoice INV - 266', '2026-09-28 04:36:03', 0),
(720, 276, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 266', '2026-09-28 04:36:03', 0),
(721, 276, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 266', '2026-09-28 04:36:03', 0),
(722, 277, 9, 2000.00, 0.00, 'Invoice INV - 267', '2026-09-28 04:36:35', 0),
(723, 277, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 267', '2026-09-28 04:36:35', 0),
(724, 277, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 267', '2026-09-28 04:36:35', 0),
(725, 278, 9, 2000.00, 0.00, 'Invoice INV - 268', '2026-09-28 04:37:11', 0),
(726, 278, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 268', '2026-09-28 04:37:11', 0),
(727, 278, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 268', '2026-09-28 04:37:11', 0),
(728, 279, 9, 2000.00, 0.00, 'Invoice INV - 269', '2026-09-28 04:37:40', 0),
(729, 279, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 269', '2026-09-28 04:37:40', 0),
(730, 279, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 269', '2026-09-28 04:37:40', 0),
(731, 280, 9, 2000.00, 0.00, 'Invoice INV - 270', '2026-09-28 04:38:08', 0),
(732, 280, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 270', '2026-09-28 04:38:08', 0),
(733, 280, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 270', '2026-09-28 04:38:08', 0),
(734, 281, 9, 2000.00, 0.00, 'Invoice INV - 271', '2026-09-28 04:38:47', 0),
(735, 281, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 271', '2026-09-28 04:38:47', 0),
(736, 281, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 271', '2026-09-28 04:38:47', 0),
(737, 282, 9, 2000.00, 0.00, 'Invoice INV - 272', '2026-09-28 04:39:12', 0),
(738, 282, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 272', '2026-09-28 04:39:12', 0),
(739, 282, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 272', '2026-09-28 04:39:12', 0),
(742, 284, 9, 52380.00, 0.00, 'Invoice INV - 273', '2026-09-28 04:40:10', 0),
(743, 284, 4, 0.00, 52380.00, 'Service Revenue: Invoice INV - 273', '2026-09-28 04:40:10', 0),
(744, 285, 9, 4320.00, 0.00, 'Invoice INV - 274', '2026-09-28 04:40:29', 0),
(745, 285, 4, 0.00, 4320.00, 'Service Revenue: Invoice INV - 274', '2026-09-28 04:40:29', 0),
(746, 286, 9, 2000.00, 0.00, 'Invoice INV - 275', '2026-09-28 04:40:52', 0),
(747, 286, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 275', '2026-09-28 04:40:52', 0),
(748, 286, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 275', '2026-09-28 04:40:52', 0),
(749, 287, 9, 2000.00, 0.00, 'Invoice INV - 276', '2026-09-28 04:41:18', 0),
(750, 287, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 276', '2026-09-28 04:41:18', 0),
(751, 287, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 276', '2026-09-28 04:41:18', 0),
(752, 288, 9, 2000.00, 0.00, 'Invoice INV - 277', '2026-09-28 04:41:46', 0),
(753, 288, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 277', '2026-09-28 04:41:46', 0),
(754, 288, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 277', '2026-09-28 04:41:46', 0),
(755, 289, 9, 2000.00, 0.00, 'Invoice INV - 278', '2026-09-28 04:42:17', 0),
(756, 289, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 278', '2026-09-28 04:42:17', 0),
(757, 289, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 278', '2026-09-28 04:42:17', 0),
(758, 290, 9, 2000.00, 0.00, 'Invoice INV - 279', '2026-09-28 04:42:42', 0),
(759, 290, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 279', '2026-09-28 04:42:42', 0),
(760, 290, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 279', '2026-09-28 04:42:42', 0),
(761, 291, 9, 2000.00, 0.00, 'Invoice INV - 280', '2026-09-28 04:43:31', 0),
(762, 291, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 280', '2026-09-28 04:43:31', 0),
(763, 291, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 280', '2026-09-28 04:43:31', 0),
(764, 292, 9, 2000.00, 0.00, 'Invoice INV - 281', '2026-09-28 04:44:00', 0),
(765, 292, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 281', '2026-09-28 04:44:00', 0),
(766, 292, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 281', '2026-09-28 04:44:00', 0),
(767, 293, 9, 2000.00, 0.00, 'Invoice INV - 282', '2026-09-28 04:44:29', 0),
(768, 293, 27, 0.00, 1000.00, 'Service Revenue: Invoice INV - 282', '2026-09-28 04:44:29', 0),
(769, 293, 25, 0.00, 1000.00, 'Service Revenue: Invoice INV - 282', '2026-09-28 04:44:29', 0),
(770, 294, 9, 4320.00, 0.00, 'Invoice INV - 283', '2026-09-28 04:44:54', 0),
(771, 294, 4, 0.00, 4320.00, 'Service Revenue: Invoice INV - 283', '2026-09-28 04:44:54', 0),
(772, 295, 9, 7020.00, 0.00, 'Invoice INV - 284', '2026-09-28 04:45:19', 0),
(773, 295, 4, 0.00, 7020.00, 'Service Revenue: Invoice INV - 284', '2026-09-28 04:45:19', 0),
(774, 296, 9, 2700.00, 0.00, 'Invoice INV - 285', '2026-09-28 04:45:44', 0),
(775, 296, 4, 0.00, 2700.00, 'Service Revenue: Invoice INV - 285', '2026-09-28 04:45:44', 0),
(776, 297, 9, 8640.00, 0.00, 'Invoice INV - 286', '2026-09-28 04:47:33', 0),
(777, 297, 4, 0.00, 8640.00, 'Service Revenue: Invoice INV - 286', '2026-09-28 04:47:33', 0),
(778, 298, 9, 4200.00, 0.00, 'Invoice INV - 287', '2026-09-28 04:47:58', 0),
(779, 298, 4, 0.00, 4200.00, 'Service Revenue: Invoice INV - 287', '2026-09-28 04:47:58', 0),
(780, 299, 9, 4800.00, 0.00, 'Invoice INV - 288', '2026-09-28 04:48:27', 0),
(781, 299, 4, 0.00, 4800.00, 'Service Revenue: Invoice INV - 288', '2026-09-28 04:48:27', 0),
(782, 300, 9, 5940.00, 0.00, 'Invoice INV - 290', '2026-09-28 04:49:14', 0),
(783, 300, 4, 0.00, 5940.00, 'Service Revenue: Invoice INV - 290', '2026-09-28 04:49:14', 0),
(784, 301, 9, 4800.00, 0.00, 'Invoice INV - 291', '2026-09-28 04:49:41', 0),
(785, 301, 4, 0.00, 4800.00, 'Service Revenue: Invoice INV - 291', '2026-09-28 04:49:41', 0),
(786, 302, 9, 1200.00, 0.00, 'Invoice INV - 292', '2026-09-28 04:50:09', 0),
(787, 302, 4, 0.00, 1200.00, 'Service Revenue: Invoice INV - 292', '2026-09-28 04:50:09', 0),
(788, 303, 9, 4320.00, 0.00, 'Invoice INV - 293', '2026-09-28 04:50:33', 0),
(789, 303, 4, 0.00, 4320.00, 'Service Revenue: Invoice INV - 293', '2026-09-28 04:50:33', 0),
(790, 304, 9, 14400.00, 0.00, 'Invoice INV - 294', '2026-09-28 04:51:03', 0),
(791, 304, 4, 0.00, 14400.00, 'Service Revenue: Invoice INV - 294', '2026-09-28 04:51:03', 0),
(792, 305, 9, 4200.00, 0.00, 'Invoice INV - 295', '2026-09-28 04:51:31', 0),
(793, 305, 4, 0.00, 4200.00, 'Service Revenue: Invoice INV - 295', '2026-09-28 04:51:31', 0),
(794, 306, 9, 6000.00, 0.00, 'Invoice INV - 296', '2026-09-28 04:52:02', 0),
(795, 306, 4, 0.00, 6000.00, 'Service Revenue: Invoice INV - 296', '2026-09-28 04:52:02', 0),
(796, 307, 9, 4860.00, 0.00, 'Invoice INV - 297', '2026-09-28 04:52:24', 0),
(797, 307, 4, 0.00, 4860.00, 'Service Revenue: Invoice INV - 297', '2026-09-28 04:52:24', 0),
(798, 308, 9, 8400.00, 0.00, 'Invoice INV - 299', '2026-09-28 04:54:38', 0),
(799, 308, 4, 0.00, 8400.00, 'Service Revenue: Invoice INV - 299', '2026-09-28 04:54:38', 0),
(800, 309, 9, 1080.00, 0.00, 'Invoice INV - 300', '2026-09-28 04:55:11', 0),
(801, 309, 4, 0.00, 1080.00, 'Service Revenue: Invoice INV - 300', '2026-09-28 04:55:11', 0),
(802, 310, 9, 2160.00, 0.00, 'Invoice INV - 301', '2026-09-28 04:58:39', 0),
(803, 310, 4, 0.00, 2160.00, 'Service Revenue: Invoice INV - 301', '2026-09-28 04:58:39', 0),
(806, 312, 9, 4320.00, 0.00, 'Invoice INV - 303', '2026-09-28 04:59:36', 0),
(807, 312, 4, 0.00, 4320.00, 'Service Revenue: Invoice INV - 303', '2026-09-28 04:59:36', 0),
(808, 313, 9, 8640.00, 0.00, 'Invoice INV - 304', '2026-09-28 05:00:00', 0),
(809, 313, 4, 0.00, 8640.00, 'Service Revenue: Invoice INV - 304', '2026-09-28 05:00:00', 0),
(810, 314, 9, 7200.00, 0.00, 'Invoice INV - 305', '2026-09-28 05:00:35', 0),
(811, 314, 4, 0.00, 7200.00, 'Service Revenue: Invoice INV - 305', '2026-09-28 05:00:35', 0),
(812, 315, 9, 3600.00, 0.00, 'Invoice INV - 306', '2026-09-28 05:01:07', 0),
(813, 315, 4, 0.00, 3600.00, 'Service Revenue: Invoice INV - 306', '2026-09-28 05:01:07', 0),
(814, 316, 9, 7200.00, 0.00, 'Invoice INV - 307', '2026-09-28 05:02:05', 0),
(815, 316, 4, 0.00, 7200.00, 'Service Revenue: Invoice INV - 307', '2026-09-28 05:02:05', 0),
(816, 317, 9, 1800.00, 0.00, 'Invoice INV - 308', '2026-09-28 05:12:19', 0),
(817, 317, 4, 0.00, 1800.00, 'Service Revenue: Invoice INV - 308', '2026-09-28 05:12:19', 0),
(818, 318, 9, 4800.00, 0.00, 'Invoice INV - 309', '2026-09-28 05:13:20', 0),
(819, 318, 4, 0.00, 4800.00, 'Service Revenue: Invoice INV - 309', '2026-09-28 05:13:20', 0),
(820, 319, 9, 7560.00, 0.00, 'Invoice INV - 310', '2026-09-28 05:15:30', 0),
(821, 319, 4, 0.00, 7560.00, 'Service Revenue: Invoice INV - 310', '2026-09-28 05:15:30', 0),
(822, 320, 9, 18000.00, 0.00, 'Invoice INV - 302', '2026-09-28 05:16:16', 0),
(823, 320, 4, 0.00, 18000.00, 'Service Revenue: Invoice INV - 302', '2026-09-28 05:16:16', 0),
(824, 321, 9, 2400.00, 0.00, 'Invoice INV - 311', '2026-09-28 05:18:29', 0),
(825, 321, 4, 0.00, 2400.00, 'Service Revenue: Invoice INV - 311', '2026-09-28 05:18:29', 0),
(826, 322, 9, 4320.00, 0.00, 'Invoice INV - 312', '2026-09-28 05:19:37', 0),
(827, 322, 4, 0.00, 4320.00, 'Service Revenue: Invoice INV - 312', '2026-09-28 05:19:37', 0),
(828, 323, 9, 4200.00, 0.00, 'Invoice INV - 314', '2026-09-28 05:20:32', 0),
(829, 323, 4, 0.00, 4200.00, 'Service Revenue: Invoice INV - 314', '2026-09-28 05:20:32', 0),
(838, 328, 9, 1800.00, 0.00, 'Invoice INV - 315', '2026-09-28 05:23:21', 0),
(839, 328, 4, 0.00, 1800.00, 'Service Revenue: Invoice INV - 315', '2026-09-28 05:23:21', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_entries`
--

CREATE TABLE `ledger_entries` (
  `id` int(10) UNSIGNED NOT NULL,
  `journal_entry_id` int(10) UNSIGNED NOT NULL,
  `journal_line_id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `transaction_date` date NOT NULL,
  `cost_center_id` int(10) UNSIGNED DEFAULT NULL,
  `project_id` int(10) UNSIGNED DEFAULT NULL,
  `batch_id` int(10) UNSIGNED DEFAULT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `running_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `machinery`
--

CREATE TABLE `machinery` (
  `id` int(10) UNSIGNED NOT NULL,
  `machinery_code` varchar(50) NOT NULL,
  `machinery_name` varchar(150) NOT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `default_rental_rate` decimal(15,2) DEFAULT 0.00,
  `rental_unit` varchar(50) DEFAULT 'Hour',
  `is_active` tinyint(1) DEFAULT 1,
  `status` varchar(50) DEFAULT 'AVAILABLE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `machinery`
--

INSERT INTO `machinery` (`id`, `machinery_code`, `machinery_name`, `serial_number`, `default_rental_rate`, `rental_unit`, `is_active`, `status`) VALUES
(1, 'MAC001', 'Tractor ( RI - 7640 )', NULL, 7000.00, 'Job', 1, 'AVAILABLE'),
(2, 'MAC002', 'Tractor ( RI - 7642 )', NULL, 7000.00, 'Job', 1, 'AVAILABLE');

-- --------------------------------------------------------

--
-- Table structure for table `machinery_rentals`
--

CREATE TABLE `machinery_rentals` (
  `id` int(10) UNSIGNED NOT NULL,
  `rental_number` varchar(50) NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `machinery_id` int(10) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `rental_unit` varchar(50) DEFAULT NULL,
  `quantity` decimal(15,2) DEFAULT 0.00,
  `rental_rate` decimal(15,2) DEFAULT 0.00,
  `total_charge` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'ACTIVE',
  `journal_entry_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `invoice_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_fixed_deposits`
--

CREATE TABLE `member_fixed_deposits` (
  `id` int(10) UNSIGNED NOT NULL,
  `deposit_number` varchar(50) NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `deposit_date` date NOT NULL,
  `start_date` date NOT NULL,
  `term_months` int(10) UNSIGNED NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `expected_interest` decimal(15,2) NOT NULL DEFAULT 0.00,
  `maturity_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `maturity_date` date NOT NULL,
  `payment_method` enum('Cash','Bank Transfer','Cheque') NOT NULL DEFAULT 'Cash',
  `status` enum('ACTIVE','MATURED','CLOSED','PREMATURELY_CLOSED','CANCELLED') NOT NULL DEFAULT 'ACTIVE',
  `notes` text DEFAULT NULL,
  `journal_entry_id` int(10) UNSIGNED DEFAULT NULL,
  `maturity_journal_entry_id` int(10) UNSIGNED DEFAULT NULL,
  `closure_date` date DEFAULT NULL,
  `closure_reason` text DEFAULT NULL,
  `interest_adjustment` decimal(15,2) DEFAULT NULL,
  `final_payable_amount` decimal(15,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parties`
--

CREATE TABLE `parties` (
  `id` int(10) UNSIGNED NOT NULL,
  `party_code` varchar(50) NOT NULL,
  `party_type` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `nic_reg_no` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `whatsapp_number` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit_days` int(11) NOT NULL DEFAULT 0,
  `payment_terms` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `customer_type` varchar(50) DEFAULT NULL,
  `supplier_type` varchar(50) DEFAULT NULL,
  `customer_activity_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parties`
--

INSERT INTO `parties` (`id`, `party_code`, `party_type`, `name`, `contact_person`, `nic_reg_no`, `phone`, `whatsapp_number`, `email`, `address`, `city`, `district`, `credit_limit`, `credit_days`, `payment_terms`, `status`, `notes`, `customer_type`, `supplier_type`, `customer_activity_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'AGC/25/001', 'DIRECTOR', 'Thennakoon Mudiyanselage Kumarasiri Bandara Thennakoon', NULL, '703101968V', '0718211010', NULL, NULL, 'Asiri, Werellapana, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 1, '2026-09-15 08:08:39', '2026-09-15 08:08:39'),
(2, 'AGC/25/002', 'DIRECTOR', 'P L G Wimalarathne', NULL, '19570760556', '0718460172', NULL, NULL, 'Kotawella, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 1, '2026-09-15 09:10:25', '2026-09-15 09:10:25'),
(3, 'AGC/25/003', 'DIRECTOR', 'Akranuge Manjula Udayananda Pinnalanda', NULL, '640401648v', '0718028774', NULL, NULL, 'Mawathahena, Thalgama, Beligala', 'Warakapola', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 09:20:06', '2026-09-15 09:20:06'),
(4, 'AGC/25/004', 'DIRECTOR', 'Ranhotige Ajith Wasantha Kumara', NULL, '197612303386', '0711296150', NULL, NULL, 'Wasantha Kudagama ,Dombemaada, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 11:37:49', '2026-09-15 11:37:49'),
(6, 'AGC/25/005', 'MEMBER', 'Muththettuwaththe Jayarathna', NULL, '196136204872', '0722152510', NULL, NULL, 'Muththetuwaththa , Gangoda, Maakehelmala', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 11:43:21', '2026-09-15 11:43:21'),
(7, 'AGC/25/006', 'DIRECTOR', 'Weda Gedara Sudath Proyankara Manukularathna', NULL, '633190380v', '0714501397', NULL, NULL, '5/3 , Mihidu Mawatha, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 12:45:15', '2026-09-15 12:45:15'),
(8, 'AGC/25/007', 'MEMBER', 'Kaluarachchilage Vijith Kumara Kaluarachchi', NULL, '643481090v', '0714377139', NULL, NULL, '1/12 Daheenpaduwa , Yatagaha, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 12:50:03', '2026-09-15 12:50:03'),
(9, 'AGC/25/008', 'DIRECTOR', 'Dasanayaka Mudiyanselage Kalana Bandara Dasanayaka', NULL, '200318312819', '0762714771', NULL, NULL, 'E/56/1 , Udugama, Pathtampitiya, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 12:54:03', '2026-09-15 12:54:03'),
(10, 'AGC/25/009', 'MEMBER', 'Rathnayaka Mudiyanselage Samantha Ranasingha', NULL, '772822294v', '0718431807', NULL, NULL, 'Paaluwaththa, Udugama , Paththampitiya, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 13:00:25', '2026-09-15 13:00:25'),
(12, 'AGC/25/010', 'MEMBER', 'Pinnawalayaa Gedara Upul Jaanaka Pinnawala', NULL, '198325204093', '0777585314', NULL, NULL, 'D/44/B/1 , Katulanda, Kotawellla, Rambukana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 13:24:49', '2026-09-15 13:24:49'),
(13, 'AGC/25/011', 'MEMBER', 'Agampodi Dewayaalage Deepika Priyadarshani Premachandra', NULL, '876420562v', '0778934213', NULL, NULL, 'D/44/B/1 , Katulanda, Kotawellla, Rambukana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 13:30:30', '2026-09-15 13:30:30'),
(14, 'AGC/25/012', 'MEMBER', 'Peramune Gamlath Raallaagee Aasiri Samantha Bandara', NULL, '850780463', '0702821000', NULL, NULL, 'Udahawalawwa waththa, kempitiya, paththampitiya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 13:35:57', '2026-09-15 13:35:57'),
(15, 'AGC/25/013', 'MEMBER', 'Pushpa Kumara Siyambalapitiya', NULL, '703303382v', '0753770145', NULL, NULL, 'c/14 Godawela, daliwala, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 13:46:55', '2026-09-15 13:46:55'),
(16, 'AGC/25/014', 'MEMBER', 'Edirisingha Dewayalaage Ashan Shanika', NULL, '921301405v', '0713604001', NULL, NULL, 'Ilukthanna, Gangekumbura, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 13:51:02', '2026-09-15 13:51:02'),
(17, 'AGC/25/015', 'MEMBER', 'Kulasekara Mudiyanselage Ruupasingha', NULL, '196027704007', '0352265915', NULL, NULL, 'E/92, Puwakmote , Yatagama, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 13:55:26', '2026-09-15 13:55:26'),
(18, 'AGC/25/016', 'MEMBER', 'Yatanwala Gamaralalage Jayaweera', NULL, '531823230v', '0714413618', NULL, NULL, 'D/ 80/2 , Diwlawaththa, Keselwathugoda, Dewalagama', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 13:58:17', '2026-09-15 13:58:17'),
(19, 'AGC/25/017', 'MEMBER', 'Haththaagodaylaa Imalsha Dileeka Haththagoda', NULL, '200519501676', '0704670703', NULL, NULL, '67/3 , Pansala Handiya, Gammala, Kotawella , Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 14:02:26', '2026-09-15 14:02:26'),
(20, 'AGC/25/018', 'MEMBER', 'Aathawuda Gedara Kulathunga Bandara', NULL, '562893725v', '0713979934', NULL, NULL, 'A/66 Walgama, Yatagama, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 14:06:20', '2026-09-15 14:06:20'),
(21, 'AGC/25/019', 'MEMBER', 'Ekanayaka Mudiyanselage Samantha Bandara Ekanayaka', NULL, '970401172v', '0760002015', NULL, NULL, 'A 52/2 Godagathdeniya, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 14:17:04', '2026-09-15 14:17:04'),
(22, 'AGC/25/020', 'MEMBER', 'Samarakon Mudiyanselage Gamini Samarakon', NULL, '196434404291', '0702018313', NULL, NULL, 'B4 Gangoda, Mirihagoda, Hendiwela', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 14:21:02', '2026-09-15 14:21:02'),
(23, 'AGC/25/021', 'MEMBER', 'Widana Ralalage Erandi Nisansala Weerabandara', NULL, '945721707v', '0710618141', NULL, NULL, '66/1 A, Kudagama , Dombemada, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 14:31:01', '2026-09-15 14:31:01'),
(24, 'AGC/25/022', 'MEMBER', 'Rampatidewage Sarath Ananda Premasiri', NULL, '740582810v', '0713982748', NULL, NULL, '2/1 , Deliwala, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 14:38:00', '2026-09-15 14:38:00'),
(25, 'AGC/25/023', 'MEMBER', 'Udagama Liyanalage Shantha Damsiri', NULL, '195436201536', '0718408751', NULL, NULL, '87/14 A , Aladeniya Mawatha , Rohala Road, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 14:42:36', '2026-09-15 14:42:36'),
(26, 'AGC/25/024', 'MEMBER', 'J.G Omilaa Wasanthi Edirisingha', NULL, '19776232122v', '0715288769', NULL, NULL, '290/1 Shanthi Gbbala, Kotawella, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 14:51:04', '2026-09-15 14:51:04'),
(27, 'AGC/25/025', 'MEMBER', 'Dewaylaa Gedara Manjula Prabath Hemachnadra', NULL, '812285033v', '0774745524', NULL, NULL, 'E 28/3 koswaththa , Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 14:54:36', '2026-09-15 14:54:36'),
(28, 'AGC/25/026', 'MEMBER', 'Agampodi Dewayaage Manoj Nilantha Gunasekara', NULL, '19863391326v', '0712196131', NULL, NULL, 'Gabbala, Kotawella , Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 15:35:50', '2026-09-15 15:35:50'),
(29, 'AGC/25/027', 'MEMBER', 'Ampalegedara Samarasingha Bandara', NULL, '672382041v', '0775299287', NULL, NULL, 'Wallagoda', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 15:38:09', '2026-09-15 15:38:09'),
(30, 'AGC/25/028', 'MEMBER', 'Ranathunga Arachchilage Pradip Shantha Kumara', NULL, '810570644v', '0717873993', NULL, NULL, '17, Mahipala Herath Road, Anwataya, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 15:45:27', '2026-09-15 15:45:27'),
(31, 'AGC/25/029', 'MEMBER', 'G.R.R.S Gamlath', NULL, '867171100v', '0778044921', NULL, NULL, 'F/94 Weligamuwa, Kotawella, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 15:50:14', '2026-09-15 15:50:14'),
(32, 'AGC/25/030', 'MEMBER', 'Udugoda Gedara Somarathna', NULL, '470923547v', '0352264020', NULL, NULL, 'Puwakmote,Yatagama ,Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 15:52:31', '2026-09-15 15:52:31'),
(33, 'AGC/25/031', 'MEMBER', 'Weligamage Thillak Weligama', NULL, '197011404597', '0770866528', NULL, NULL, 'Mirihagoda, Hewadiwela, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 15:55:14', '2026-09-15 15:55:14'),
(34, 'AGC/25/032', 'MEMBER', 'R.Nimal Weerasingha', NULL, '195921210103', '0779064289', NULL, NULL, 'Sirisewana Aadaweta, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-15 15:58:11', '2026-09-15 15:58:11'),
(35, 'AGC/25/033', 'MEMBER', 'I.P Champikaa Damayanthi', NULL, '666320255v', '0776236998', NULL, NULL, '68, Bandarawaththa mahawa, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 03:20:47', '2026-09-16 03:20:47'),
(36, 'AGC/26/001', 'MEMBER', 'Wijesuriya Mudiyanselage Buddika Gamunu Bandara', NULL, '197914700757', '0776235213', NULL, NULL, 'A/83, Daliwala, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 03:26:29', '2026-09-16 03:26:29'),
(37, 'AGC/25/035', 'MEMBER', 'Ambekoan Senawirathna Panditha Wasala Mudiyanse Ralahamilage Aruna Senawirathna', NULL, '772313012v', '0702874881', NULL, NULL, 'A/69, Dalimala,Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 03:33:29', '2026-09-16 03:33:29'),
(38, 'AGC/25/036', 'MEMBER', 'Edirisuriya Mudiyanselage Priyantha Bandara Edirisuriya', NULL, '198014503252', '0712006999', NULL, NULL, 'Kundagollawaththa, Pinnawala, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 03:57:00', '2026-09-16 03:57:00'),
(39, 'AGC/25/037', 'MEMBER', 'Warnakulasuriya Arachchilage Jayalath Perera', NULL, '692532503v', '0779503669', NULL, NULL, 'Walawwa, Walgama, Yatagama, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 04:04:11', '2026-09-16 04:04:11'),
(40, 'AGC/25/038', 'MEMBER', 'Thennakoan Mudiyanselage Sarath Jayathissa', NULL, '630355060v', '0711367327', NULL, NULL, '116/B , Malberiwaththa, Keselwathugoda, Dewalagama', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 04:16:07', '2026-09-16 04:16:07'),
(41, 'AGC/25/039', 'MEMBER', 'Sinhalage Gunasiri Gunathilaka', NULL, '571833786v', '0718084972', NULL, NULL, 'Duminda, Polaththaapitiya, Udadeniya, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 04:18:25', '2026-09-16 04:18:25'),
(42, 'AGC/25/040', 'MEMBER', 'Yatiwaldeniya Gedara Gunarathna Weerasena', NULL, '702420253v', '0707475192', NULL, NULL, '10 A/127 , Diyasinatha , Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 05:33:24', '2026-09-16 05:33:24'),
(43, 'AGC/25/041', 'MEMBER', 'Marawala Ralalage Indika Saman Kumara', NULL, '820982231v', '0714951490', NULL, NULL, 'A 87/1 , Walgama ,Yatagama, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 05:40:24', '2026-09-16 05:40:24'),
(44, 'AGC/25/042', 'MEMBER', 'Upali Gunawardana Ilangakoan', NULL, '530051811v', '0712025376', NULL, NULL, 'Malwaththa Road, Kehelwathugoda, Dewalagama', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 05:42:32', '2026-09-16 05:42:32'),
(45, 'AGC/25/043', 'MEMBER', 'Sohani Chamilika Kumari Ilangakoan', NULL, '198368403118', '0720891567', NULL, NULL, 'D 3611, Malwaththa Walawwa, Keselwathugoda, Dewalagama', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 05:48:55', '2026-09-16 05:48:55'),
(46, 'AGC/25/044', 'MEMBER', 'Dasanayaka Mudiyanselage Jaaliya Dasanayaka', NULL, '631832059v', '0777974011', NULL, NULL, 'Ruksewana, Pohorache, Dewalagama', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 05:51:16', '2026-09-16 05:51:16'),
(47, 'AGC/25/045', 'MEMBER', 'W.Chaminda Sugath', NULL, '197423002717', '0372051710', NULL, NULL, 'C 18/2 Ambuwangala , Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 05:56:50', '2026-09-16 05:56:50'),
(48, 'AGC/25/046', 'MEMBER', 'Waasitiyalaage Sunil', NULL, '633132682v', '0372053503', NULL, NULL, 'Kumbuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 05:59:13', '2026-09-16 05:59:13'),
(49, 'AGC/25/047', 'MEMBER', 'Mutha Merachcha Ranil Chandrathilaka', NULL, '197915101259', '0772657482', NULL, NULL, '22, Mihidu mawatha, Eriyawa, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 06:04:21', '2026-09-16 06:04:21'),
(50, 'AGC/26/048', 'MEMBER', 'Hengaha Rallage Dingiri Banda', NULL, '490394206v', '0740511949', NULL, NULL, 'C 949, Puwakmote, Yatagama, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 06:10:14', '2026-09-16 06:10:14'),
(51, 'AGC/26/049', 'MEMBER', 'Nawarathna Mudiyanselage Senawirathna', NULL, '194728400967', '0716291422', NULL, NULL, 'B 35, Mirihagoda, Hewadiwela', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 06:12:45', '2026-09-16 06:12:45'),
(52, 'AGC/26/050', 'MEMBER', 'J.M Thilak Champika Jayasingha', NULL, '196102910054', '0704912620', NULL, NULL, 'Walgama, Yatagama, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 06:16:25', '2026-09-16 06:16:25'),
(53, 'AGC/26/051', 'MEMBER', 'Katukurunda Gamage Dedunu Sithari Katukurunda', NULL, '197762602951', '0717282226', NULL, NULL, 'Dahenpathuwa , Yatagama, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 06:19:29', '2026-09-16 06:19:29'),
(54, 'AGC/26/052', 'MEMBER', 'Edirisingha Arachchige Gamini Vijerathna', NULL, '521752777v', '0777106892', NULL, NULL, 'Walalgoda, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 06:55:46', '2026-09-16 06:55:46'),
(55, 'AGC/26/053', 'MEMBER', 'Athugalge Dewadasa', NULL, '570311441v', '0703344038', NULL, NULL, '75, Alagolla, Hewadiwela', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 06:58:59', '2026-09-16 06:58:59'),
(56, 'AGC/26/054', 'MEMBER', 'Jayasundara  Mudiyanselage Herathbanda Jayasundara', NULL, '450118560', '0741645056', NULL, NULL, 'C 48, Hinabowa, Rambukana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 07:10:45', '2026-09-16 07:10:45'),
(57, 'AGC/26/055', 'MEMBER', 'Kalaochiyla Gedara Ranjani Shriyalatha', NULL, '695552092v', '0702261095', NULL, NULL, 'Middeniyawaththa, Parape, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 07:15:11', '2026-09-16 07:15:11'),
(58, 'AGC/26/056', 'MEMBER', 'Suduhakurala Renuka Nandani Edirisingha', NULL, '708082457v', '0718694082', NULL, NULL, 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 07:17:51', '2026-09-16 07:17:51'),
(59, 'AGC/26/057', 'MEMBER', 'Kosgollalaa Gedara Kumuduni Samankumari', NULL, '765272262v', '0761550261', NULL, NULL, 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 07:37:03', '2026-09-16 07:37:03'),
(60, 'AGC/26/058', 'MEMBER', 'Pihille Gedara Jayarathna', NULL, '195913900414', '0714985750', NULL, NULL, 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 07:54:03', '2026-09-16 07:54:03'),
(61, 'AGC/26/059', 'MEMBER', 'Edirisingha Dewayalaage Priyangaa Kumuduni Jayasingha', NULL, '197168401057', '0769607060', NULL, NULL, 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 07:56:40', '2026-09-16 07:56:40'),
(62, 'AGC/26/060', 'MEMBER', 'Ranathunga Dewayalaa Sunil Suwinitha Mallika', NULL, '195784900248', '0772198342', NULL, NULL, 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 10:21:20', '2026-09-16 10:21:20'),
(63, 'AGC/26/061', 'MEMBER', 'Werawellegedara Manel Rukmani Latha', NULL, '197164101311', '0712917280', NULL, NULL, 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 10:23:46', '2026-09-16 10:23:46'),
(64, 'AGC/26/062', 'MEMBER', 'Edirimuni Dewayalage Karunawathi', NULL, '195782100237', '0706546055', NULL, NULL, 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 10:33:48', '2026-09-16 10:33:48'),
(65, 'AGC/26/063', 'MEMBER', 'Suduhakurala Upali Senarath Edirisingha', NULL, '670260380v', '0761626408', NULL, NULL, 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 10:37:25', '2026-09-16 10:37:25'),
(66, 'AGC/26/064', 'MEMBER', 'W.G.T Gamini', NULL, '195730701204', '0715674358', NULL, NULL, 'Yawanakanda , parape , Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 10:47:12', '2026-09-16 10:47:12'),
(67, 'AGC/26/065', 'MEMBER', 'Dukganna Walawwe Madduma Bandara', NULL, '461662544v', '0372391761', NULL, NULL, 'Amuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 10:49:52', '2026-09-16 10:49:52'),
(68, 'AGC/26/066', 'MEMBER', 'Kainankada Edirimuni Suriyage Sunil Saanthi Gunasekara', NULL, '196122701216', '0776615854', NULL, NULL, 'Munamale , Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 10:58:08', '2026-09-16 10:58:08'),
(69, 'AGC/26/067', 'MEMBER', 'Wannaku Waththe Waduge Don Anura Ranjith Perera', NULL, '195932800977', '0717271831', NULL, NULL, 'C.4 Abuwangala , Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-16 11:02:18', '2026-09-16 11:02:18'),
(70, 'AGC/26/068', 'MEMBER', 'Pallewelayage Ariyadasa', NULL, '4815112559v', '0354395300', NULL, NULL, 'Rambukkana Road , Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 03:31:55', '2026-09-17 03:31:55'),
(71, 'AGC/26/069', 'MEMBER', 'Suduhakuralage Chamara Sunil Kumara', NULL, '852703067v', '0774730344', NULL, NULL, 'Marukwathura, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 03:35:04', '2026-09-17 03:35:04'),
(72, 'AGC/26/070', 'MEMBER', 'Pallewelage Mahasen Karunarathna', NULL, '1962400318', '071939509', NULL, NULL, 'ShanthiSewana, Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 03:43:15', '2026-09-17 03:43:15'),
(73, 'AGC/26/071', 'MEMBER', 'Koskotuwe Gedara Chandani Dhammikaa Koskotuwa', NULL, '196485601032', '0766795044', NULL, NULL, 'Munamale , Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 04:00:10', '2026-09-17 04:00:10'),
(74, 'AGC/26/072', 'MEMBER', 'Manikkum Pedige Indrani Kusumlatha', NULL, '097162503198', '0762960283', NULL, NULL, 'Kandiwaththa, Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 04:03:23', '2026-09-17 04:03:23'),
(75, 'AGC/26/073', 'MEMBER', 'Gaspe Ralalage Wijewardana', NULL, '194518603662', '0714133846', NULL, NULL, 'Amuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 04:08:52', '2026-09-17 04:08:52'),
(76, 'AGC/26/074', 'MEMBER', 'Thibbotuge Sirisena', NULL, '413291283v', '0764107119', NULL, NULL, 'Suranga Niwasa , Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 04:16:29', '2026-09-17 04:16:29'),
(77, 'AGC/26/075', 'MEMBER', 'Henaka Ralalage Nalin Prabath Amarasingha', NULL, '730560656v', '0718441128', NULL, NULL, 'Marukwathura, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 04:35:52', '2026-09-17 04:35:52'),
(78, 'AGC/26/076', 'MEMBER', 'Jayaweera Arachchige Asanka Bandara Jayaweera', NULL, '198813800757', '0712063651', NULL, NULL, 'Kondostharawaththa , Marukwathura , Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 11:08:56', '2026-09-17 11:08:56'),
(79, 'AGC/26/077', 'MEMBER', 'Hewawasam Gallage Nandawathi', NULL, '496343468v', '0372053587', NULL, NULL, 'Udakothuwawaththa, Aduwangala, Iwulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 11:11:41', '2026-09-17 11:11:41'),
(80, 'AGC/26/078', 'MEMBER', 'Henaka Rala;age Askoaa', NULL, '647700799v', '0711646879', NULL, NULL, 'Sigiri , Wttarak Thanna, Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 11:14:33', '2026-09-17 11:14:33'),
(81, 'AGC/26/079', 'MEMBER', 'Polgampala Arachilage Rukmani Wasantha Kumari', NULL, '197384100734', '03720551669', NULL, NULL, 'C/76/5 , Abuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 11:21:23', '2026-09-17 11:21:23'),
(83, 'AGC/26/081', 'MEMBER', 'Jayasuriya Arachchilage Karunarathna', NULL, '482173233v', '0776605410', NULL, NULL, 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 11:38:48', '2026-09-17 11:38:48'),
(84, 'AGC/26/082', 'MEMBER', 'Herathmudiyanselage Somasiri Manike', NULL, '197081400155', '0772804505', NULL, NULL, 'Ihalaabuwangala, Imbuldeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 11:45:11', '2026-09-17 11:45:11'),
(85, 'AGC/26/083', 'MEMBER', 'Polgampala Arachilage Sisira Hemachandra', NULL, '1967736002452', '07100000000', NULL, NULL, 'C/76/3 Ambuwangala ,Imbuldeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 11:48:12', '2026-09-17 11:48:12'),
(86, 'AGC/26/084', 'MEMBER', 'Wannakuwaththe Waduge Don Jayathilaka', NULL, '4705421524', '0711858079', NULL, NULL, 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 11:58:14', '2026-09-17 11:58:14'),
(87, 'AGC/26/085', 'MEMBER', 'Athukoralage Tikiribanda', NULL, '482492788v', '07199336500', NULL, NULL, 'Egodagedara, Imbulgaldeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-17 12:00:49', '2026-09-17 12:00:49'),
(88, 'AGC/26/086', 'MEMBER', 'Wijesuriya Basnayaka Mudiyanselage Chandrani Nawarathna Manike', NULL, '196973101157', '0718993292', NULL, NULL, 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 03:37:19', '2026-09-18 03:37:19'),
(89, 'AGC/26/087', 'MEMBER', 'Meegalle Gamlath Ralalage Anusha Hemali Gamlath', NULL, '747040060v', '0702542462', NULL, NULL, 'Kurunduwaththa, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 03:40:45', '2026-09-18 03:40:45'),
(90, 'AGC/26/088', 'MEMBER', 'Senanayaka Mudiyanselage Gunarathna', NULL, '540391297v', '0775318160', NULL, NULL, '75, Kurunduwaththa, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 04:33:04', '2026-09-18 04:33:04'),
(91, 'AGC/26/089', 'MEMBER', 'Athukoralage Podimahaththaya', NULL, '420803184v', '0718740167', NULL, NULL, 'Rambukkana Road, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 04:39:20', '2026-09-18 04:39:20'),
(92, 'AGC/26/090', 'MEMBER', 'Kuruppu Arachchilage Podinilame', NULL, '593411486v', '0779922295', NULL, NULL, 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 04:43:47', '2026-09-18 04:43:47'),
(93, 'AGC/26/091', 'MEMBER', 'Pathiranahalage Gunarath Manike', NULL, '195976500213', '0772654301', NULL, NULL, 'Paranawaththa, Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 04:46:13', '2026-09-18 04:46:13'),
(94, 'AGC/26/092', 'MEMBER', 'Pathiranahalage Premawathi', NULL, '196186300030', '0778181431', NULL, NULL, 'C 33/2 Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 04:48:23', '2026-09-18 04:48:23'),
(95, 'AGC/26/093', 'MEMBER', 'Rankonde Mudiyanselage Ranjani Wayalat Kumarihami Wallawa', NULL, '195355400586', '0372243483', NULL, NULL, 'Deiyanwala walawwa, Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 05:09:12', '2026-09-18 05:09:12'),
(96, 'AGC/26/094', 'MEMBER', 'Ranaweera Kaluarachi Mudiyanselage Don Mahesu Sunil Ranaweera', NULL, '613430210', '0717207798', NULL, NULL, 'Sirangawitiya, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 05:14:44', '2026-09-18 05:14:44'),
(97, 'AGC/26/095', 'MEMBER', 'Pathirathna Mudiyanselage Indika Pathirathna', NULL, '770683351v', '0785000687', NULL, NULL, 'A/8 Ambuwangala Handiya, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 05:17:54', '2026-09-18 05:17:54'),
(98, 'AGC/26/096', 'MEMBER', 'Herath Ralalage Nandumani Godagama', NULL, '195054000070', '0372243588', NULL, NULL, 'Ambuwangala,Sirangepitiya, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 05:25:02', '2026-09-18 05:25:02'),
(99, 'AGC/26/097', 'MEMBER', 'Dugganna Walawwe Iranga Nishntha Bandara Ambuwangala', NULL, '790880943v', '0776343278', NULL, NULL, 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 05:40:58', '2026-09-18 05:40:58'),
(100, 'AGC/26/098', 'MEMBER', 'Herath Mudiyanselage Gunarathna Banda', NULL, '195511110144', '0718402015', NULL, NULL, 'Sisil Thangodawaththa , Sirangapitiya, Pitawala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 05:51:14', '2026-09-18 05:51:14'),
(101, 'AGC/26/099', 'MEMBER', 'D.N.H Madawala', NULL, '488293710v', '0767673298', NULL, NULL, 'C 69, Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 05:53:10', '2026-09-18 05:53:10'),
(102, 'AGC/26/100', 'MEMBER', 'Kande Ralalage Karunasena', NULL, '541250387v', '0777135050', NULL, NULL, 'Wattaram Thanna, Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 05:55:31', '2026-09-18 05:55:31'),
(103, 'AGC/26/101', 'MEMBER', 'R.W.D.W Erangaa Krishanthi Rajakaruna', NULL, '198850502312', '0779406732', NULL, NULL, 'C 44, Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 05:58:58', '2026-09-18 05:58:58'),
(104, 'AGC/26/102', 'MEMBER', 'Athukorala Raalalage Wijewardana', NULL, '196308701476', '0725832020', NULL, NULL, 'Palapolwaththa, Pitawala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 06:01:06', '2026-09-18 06:01:06'),
(105, 'AGC/26/103', 'MEMBER', 'Wallawathage Karunarathna', NULL, '551730760v', '0372244516', NULL, NULL, 'Marukwatura, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 06:05:49', '2026-09-18 06:05:49'),
(106, 'AGC/26/104', 'MEMBER', 'Molgampala Arachilage Upul Nishantha', NULL, '19801026', '0717909571', NULL, NULL, 'C/76/1 , Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 06:08:55', '2026-09-18 06:08:55'),
(107, 'AGC/26/105', 'MEMBER', 'Pathirannahalage Nishanthi Kumari Pathirana', NULL, '855411539v', '0701116002', NULL, NULL, 'C 52/1 Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 06:17:31', '2026-09-18 06:17:31'),
(108, 'AGC/26/106', 'MEMBER', 'W.A Nilangani Nawarathna', NULL, '196681501477v', '0761874628', NULL, NULL, 'C 59/1 , Ihala Ambuwamgala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 06:25:50', '2026-09-18 06:25:50'),
(109, 'AGC/26/107', 'MEMBER', 'wijesingha Arachilage Sandyaa Nawarathna', NULL, '196363101472', '0763712407', NULL, NULL, 'C/34/5 , Nalawilla Para, Ihala Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 06:29:13', '2026-09-18 06:29:13'),
(110, 'AGC/26/108', 'MEMBER', 'K.R Kumarasingha', NULL, '61355080v', '0779309021', NULL, NULL, 'C/59, Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 06:37:24', '2026-09-18 06:37:24'),
(111, 'AGC/26/109', 'MEMBER', 'W.E.W.M.R Sunil Bandara', NULL, '5935822485v', '0711896377', NULL, NULL, 'Hapugoda, Halpitiya, Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 07:28:33', '2026-09-18 07:28:33'),
(112, 'AGC/26/110', 'MEMBER', 'W.A Chandra Weerasingha', NULL, '617262339', '0778872458', NULL, NULL, 'Nagayawaththa, Nwagamuwa, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 07:33:49', '2026-09-18 07:33:49'),
(113, 'AGC/26/111', 'MEMBER', 'Disanayaka Mudiyanselage Dingiribanda Disanayaka', NULL, '194403301521', '0765664291', NULL, NULL, 'Nagayawaththa, Nwagamuwa, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 07:38:29', '2026-09-18 07:38:29'),
(114, 'AGC/26/112', 'MEMBER', 'Munasingha Arachchige Mangalika Munasingha', NULL, '528452442v', '0716490385', NULL, NULL, 'Nuwan, Galwala Road, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 09:20:57', '2026-09-18 09:20:57'),
(115, 'AGC/26/113', 'MEMBER', 'Hitihawellage Sandasiri Mallika', NULL, '525833267v', '0718662847', NULL, NULL, 'Sandalla, Handagama, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 09:28:46', '2026-09-18 09:28:46'),
(116, 'AGC/26/114', 'MEMBER', 'Herathmudiyanselage Wasanthaa Herath', NULL, '196553800728', '0740710182', NULL, NULL, 'D/132, Diganawaththa, Molagoda', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 11:19:52', '2026-09-18 11:19:52'),
(117, 'AGC/26/115', 'MEMBER', 'Herath Mudiyanselage Jayantha Bandara', NULL, '196203303964', '0769005696', NULL, NULL, 'Galwala Para, Bathabhuraya, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 11:22:00', '2026-09-18 11:22:00'),
(118, 'AGC/26/116', 'MEMBER', 'Hadagama Mudaligedara Mudalige Ananda Handagama', NULL, '592170922v', '0718499751', NULL, NULL, 'B/5/2 , Maduwasala , Nagamuwa, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-18 11:26:12', '2026-09-18 11:26:12'),
(119, 'AGC/26/117', 'MEMBER', 'Mudali Mahipala Appuhamilage Yamuna Ariyalatha', NULL, '608303898v', '0787833515', NULL, NULL, 'C/92, Nawagamuwa, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 04:18:21', '2026-09-19 04:18:21'),
(120, 'AGC/26/118', 'MEMBER', 'Weragodayalage Kularathna', NULL, '593624706v', '0740046250', NULL, NULL, 'Parape, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 04:20:59', '2026-09-19 04:20:59'),
(121, 'AGC/26/119', 'MEMBER', 'Henaka Ralalage Amarasingha', NULL, '563273950', '0710437980', NULL, NULL, 'Kagalla para, Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 04:23:23', '2026-09-19 04:23:23'),
(122, 'AGC/26/120', 'MEMBER', 'Ilangakoan Mudiyanselage Jayasuriya', NULL, '6022900260', '0771377950', NULL, NULL, 'Warukwathura, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 04:53:52', '2026-09-19 04:53:52'),
(123, 'AGC/26/121', 'MEMBER', 'Hatan Arachilage Ranjani Sriyanjali Hatanarachchi', NULL, '936172032v', '0703609634', NULL, NULL, 'Gunasiri, Marukwathura, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 04:59:07', '2026-09-19 04:59:07'),
(124, 'AGC/26/122', 'MEMBER', 'G.R.D Manike', NULL, '196858800300', '0718318021', NULL, NULL, 'Thuthotawaththa, Halpitiya, Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 05:07:55', '2026-09-19 05:07:55'),
(125, 'AGC/26/123', 'MEMBER', 'N.M Nawarathna Sarath Bandara', NULL, '752921415v', '0704691930', NULL, NULL, 'Halpitiya , Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 05:11:22', '2026-09-19 05:11:22'),
(126, 'AGC/26/124', 'MEMBER', 'A.D Ralahami', NULL, '662090913v', '0768301447', NULL, NULL, 'Naathagahamulla, Halpitiya, Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 05:14:25', '2026-09-19 05:14:25'),
(127, 'AGC/26/125', 'MEMBER', 'Herath Mudiyanselage Ralahamilage Jayasiri Bandara', NULL, '603384148v', '0767164276', NULL, NULL, 'Sirisewana, Halpitiya , Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 05:21:37', '2026-09-19 05:21:37'),
(128, 'AGC/26/126', 'MEMBER', 'Hallama Appuhamilage Chandrarathna Piyasiri', NULL, '74278165v', '0718124574', NULL, NULL, 'Halpitiya , Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 05:26:41', '2026-09-19 05:26:41'),
(129, 'AGC/26/127', 'MEMBER', 'M.R Kamani Pushpakumari', NULL, '197480800783', '0725311907', NULL, NULL, 'Hapugoda, Halpitiya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 05:48:27', '2026-09-19 05:48:27'),
(130, 'AGC/26/128', 'MEMBER', 'Chandrasekara Mudiyanselage Dhammika Jayathilaka', NULL, '801220614v', '0776049103', NULL, NULL, 'Hapugoda, Halpitiya, Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 05:50:38', '2026-09-19 05:50:38'),
(131, 'AGC/26/129', 'MEMBER', 'Upasaka Raalalage Renuka Jayasundara', NULL, '626410529v', '0713068573', NULL, NULL, 'Halpitiya , Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 06:30:11', '2026-09-19 06:30:11'),
(132, 'AGC/26/130', 'MEMBER', 'Arachchilage Ranbanda', NULL, '194916404680', '0741872685', NULL, NULL, 'D108, Hapugoda, Halpitiya, Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 06:33:05', '2026-09-19 06:33:05'),
(133, 'AGC/26/131', 'MEMBER', 'Malawi Arachilage Podibanda', NULL, '530212947v', '0703005030', NULL, NULL, 'D.62, Hapugoda, Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 06:40:32', '2026-09-19 06:40:32'),
(134, 'AGC/26/132', 'MEMBER', 'Pahala Kangarage Chathuranga Aberathna', NULL, '950852070v', '0789890896', NULL, NULL, '79, Karandagasthanna, Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 06:46:00', '2026-09-19 06:46:00'),
(135, 'AGC/26/133', 'MEMBER', 'Liyanaralalage Punchimahaththaya', NULL, '586431226v', '0767191651', NULL, NULL, 'Hapugoda, Halpitiya, Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 06:49:56', '2026-09-19 06:49:56'),
(136, 'AGC/26/134', 'MEMBER', 'H.Gunathilaka', NULL, '563360666v', '0711902586', NULL, NULL, 'Thunpala Waththa, Hapugoda, Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 06:53:25', '2026-09-19 06:53:25'),
(137, 'AGC/26/135', 'MEMBER', 'Hitihamillage Duminda Sampath Aberathna', NULL, '198224902053', '0767774039', NULL, NULL, 'D/73/1 , Hapugoda , Halpitiya, Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 08:00:14', '2026-09-19 08:00:14'),
(138, 'AGC/26/136', 'MEMBER', 'Siman Hewage Sunil Shantha', NULL, '197429102361', '0775876101', NULL, NULL, 'pillawa, Hapugoda, Halpitiya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 08:02:01', '2026-09-19 08:02:01'),
(139, 'AGC/26/137', 'MEMBER', 'Widana Pathirana Nihal Priyantha Dharmadasa', NULL, '196807302515', '0701288136', NULL, NULL, 'Kotuwakale, Kapugoda, Halpitiya, Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 08:04:50', '2026-09-19 08:04:50'),
(140, 'AGC/26/138', 'MEMBER', 'Heralibadage Dilip Ashoka Karunathna', NULL, '673342841v', '0777646951', NULL, NULL, 'Ajantha Niwasa, Thalakolayaya, Hewadiwela', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 08:08:56', '2026-09-19 08:08:56'),
(141, 'AGC/26/139', 'MEMBER', 'S.L.B.K Wikramarathna', NULL, '832690236v', '0713792238', NULL, NULL, 'Pahala Higapitiya, Hewadiwela, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 08:13:38', '2026-09-19 08:13:38'),
(142, 'AGC/26/140', 'MEMBER', 'Senakaralalage Egodagedara Nirosh Dhammika Abaya Bandara', NULL, '197234804006', '0711711786', NULL, NULL, 'Waligamuwa, Kotawella', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 08:16:35', '2026-09-19 08:16:35'),
(143, 'AGC/26/141', 'MEMBER', 'Muthugamaraalalage Upali Chandrasena', NULL, '553313244v', '0776236987', NULL, NULL, 'F/89, Weligamuwa , Kotawella', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 08:18:46', '2026-09-19 08:18:46'),
(144, 'AGC/26/142', 'MEMBER', 'Kanaththegedara , Podiralahami', NULL, '19491521848v', '0764825342', NULL, NULL, 'Weligamuwa, Kotawella, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 10:30:33', '2026-09-19 10:30:33'),
(145, 'AGC/26/143', 'MEMBER', 'Hewapathirana Padma Weerasingha', NULL, '5382249v', '0743802767', NULL, NULL, '4th lane, sathiamte waththa, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 10:34:10', '2026-09-19 10:34:10'),
(146, 'AGC/26/144', 'MEMBER', 'D.G.Anar Perera', NULL, '510494580v', '0714238502', NULL, NULL, '50/C Sentimant Waththa, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 10:41:52', '2026-09-19 10:41:52'),
(147, 'AGC/26/145', 'MEMBER', 'Sacrage Reeta Priyamani', NULL, '195654201182', '0763811723', NULL, NULL, 'Sillugala,Kudagama,Dodamemada', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 10:46:15', '2026-09-19 10:46:15'),
(148, 'AGC/26/146', 'MEMBER', 'Arachchilage Thilakarathna', NULL, '195834602667', '0779910197', NULL, NULL, 'D/89, Hapugoda, Halpitiya, Hiriwatunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 10:49:20', '2026-09-19 10:49:20'),
(149, 'AGC/26/147', 'MEMBER', 'R.A Sarath Chandrasiri', NULL, '613353917v', '0775506205', NULL, NULL, 'D.24. Kotakanda, Halpitiya, Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 10:52:03', '2026-09-19 10:52:03'),
(150, 'AGC/26/148', 'MEMBER', 'Muhandiram Ralalage Pradeep Nishantha Bandara', NULL, '852884363', '0778306572', NULL, NULL, 'D/3 , Kotakanda , Halpitiya, Hiriwadunna', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 10:54:11', '2026-09-19 10:54:11'),
(151, 'AGC/26/149', 'MEMBER', 'Herath Mudiyanselage Podomanike', NULL, '608241957v', '0353221111', NULL, NULL, 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 10:56:28', '2026-09-19 10:56:28'),
(152, 'AGC/26/150', 'MEMBER', 'Kutihoyalaa Gedara Nihal Rupasingha', NULL, '730372914v', '0703817354', NULL, NULL, 'Hadagama, Pinnawala', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 11:00:46', '2026-09-19 11:00:46'),
(153, 'AGC/26/151', 'MEMBER', 'G.R Damayanthi', NULL, '858444527v', '0768836925', NULL, NULL, 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 11:02:39', '2026-09-19 11:02:39'),
(154, 'AGC/26/152', 'MEMBER', 'Kosgollalaa Gedara Sumith Vimalasiri', NULL, '710943583v', '0703137123', NULL, NULL, 'Boralla, Pahalagama, Parape, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 11:07:13', '2026-09-19 11:07:13'),
(155, 'AGC/26/153', 'MEMBER', 'Ranathunga Dewayalage Weerasingha', NULL, '195000700587', '0716366818', NULL, NULL, 'Pahalagama ,Parape ,Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 11:09:10', '2026-09-19 11:09:10'),
(156, 'AGC/26/154', 'MEMBER', 'Mahathunga Dewayalage Nirosh Saman Kumara Weerasingha', NULL, '821751357v', '0716366818', NULL, NULL, 'A/57/6, Pahalagama, Parape, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-19 11:12:26', '2026-09-19 11:12:26'),
(161, 'AGC/26/155', 'MEMBER', 'W.G.B Chandrasiri', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 06:53:57', '2026-09-22 06:53:57'),
(162, 'AGC/26/156', 'MEMBER', 'G.P Sirimanna', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:15:05', '2026-09-22 07:15:05'),
(163, 'AGC/26/157', 'MEMBER', 'G.R Asela', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:15:29', '2026-09-22 07:15:29'),
(164, 'AGC/26/158', 'MEMBER', 'G.G Karunarathna', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:15:48', '2026-09-22 07:15:48'),
(165, 'AGC/26/159', 'MEMBER', 'G.R Damayanthi', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:16:28', '2026-09-22 07:16:28'),
(166, 'AGC/26/160', 'MEMBER', 'A.Ariyapala', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:16:45', '2026-09-22 07:16:45'),
(167, 'AGC/26/161', 'MEMBER', 'S.H Karunawathi', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:17:05', '2026-09-22 07:17:05'),
(168, 'AGC/26/162', 'MEMBER', 'H.R Somadasa', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:17:37', '2026-09-22 07:17:37'),
(169, 'AGC/26/163', 'MEMBER', 'S.H Piyaseeli', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:18:05', '2026-09-22 07:18:05'),
(170, 'AGC/26/164', 'MEMBER', 'P.G Chithtra Airangani', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:18:34', '2026-09-22 07:18:34'),
(171, 'AGC/26/165', 'MEMBER', 'K.G Sumith Wimalasiri', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:19:54', '2026-09-22 07:19:54'),
(172, 'AGC/26/166', 'MEMBER', 'M.D Saman Kumara Weerasingha', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:20:27', '2026-09-22 07:20:27'),
(173, 'AGC/26/167', 'MEMBER', 'M.D Weerasingha', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:24:55', '2026-09-22 07:24:55'),
(174, 'AGC/26/168', 'MEMBER', 'K.M Shriyani Premalatha', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:25:26', '2026-09-22 07:25:26'),
(175, 'AGC/26/169', 'MEMBER', 'D.Edirimuni Dewayalage Manel Swarnalatha', NULL, '197064301039', '0772883490', NULL, NULL, 'Imbulthanna, Pahalagama, Parape', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:39:36', '2026-09-22 07:39:36'),
(176, 'AGC/26/170', 'MEMBER', 'N.D.N.P Edirisingha', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:40:53', '2026-09-22 07:40:53'),
(177, 'AGC/26/171', 'MEMBER', 'Kamala Wijesundara', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:41:17', '2026-09-22 07:41:17'),
(178, 'AGC/26/172', 'MEMBER', 'Lakmal Kumara Rathnayaka', NULL, '', '', NULL, NULL, 'Parape', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:41:39', '2026-09-22 07:41:39'),
(179, 'AGC/26/173', 'MEMBER', 'Ajanthaa Malawita', NULL, '', '', NULL, NULL, '', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:41:59', '2026-09-22 07:41:59'),
(180, 'AGC/26/174', 'MEMBER', 'W.W Gunathilaka', NULL, '', '', NULL, NULL, 'Thithmalpola', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:42:32', '2026-09-22 07:42:32'),
(181, 'AGC/26/175', 'MEMBER', 'H.W Chandrawathi', NULL, '', '', NULL, NULL, 'Thithmalpola', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:42:54', '2026-09-22 07:42:54'),
(182, 'AGC/26/176', 'MEMBER', 'Asanka Dilanka Karunarathna', NULL, '', '', NULL, NULL, 'Thithmalpola', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:43:16', '2026-09-22 07:43:16'),
(183, 'AGC/26/177', 'MEMBER', 'Malani Jayalathaa', NULL, '', '', NULL, NULL, 'Thithmalpola', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:43:37', '2026-09-22 07:43:37'),
(184, 'AGC/26/178', 'MEMBER', 'Gamini Thennakoan', NULL, '', '', NULL, NULL, 'Thithmalpola', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:44:00', '2026-09-22 07:44:00'),
(185, 'AGC/26/179', 'MEMBER', 'Anulaa Kumarihami', NULL, '', '', NULL, NULL, 'Thithmalpola', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:44:41', '2026-09-22 07:44:41'),
(186, 'AGC/26/180', 'MEMBER', 'H.M Gihan Madushanka', NULL, '', '', NULL, NULL, 'Thithmalpola', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:45:25', '2026-09-22 07:45:25'),
(187, 'AGC/26/181', 'MEMBER', 'N.P Wijesingha', NULL, '', '', NULL, NULL, 'Kudagama', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:45:48', '2026-09-22 07:45:48'),
(188, 'AGC/26/182', 'MEMBER', 'Herath Mudiyanselage Ranbanda', NULL, '510691016v', '0352266850', NULL, NULL, 'Ihalawalpola, Kotawella, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:48:20', '2026-09-22 07:48:20'),
(189, 'AGC/26/183', 'MEMBER', 'B.A Wedhani Ibekaa', NULL, '', '', NULL, NULL, 'Ambuwangala, Imbulgasdeniya', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:50:48', '2026-09-22 07:50:48'),
(190, 'AGC/26/184', 'MEMBER', 'H.A DayawathiManike', NULL, '', '', NULL, NULL, 'Ambuwangala, Imbulgasdeniya', '', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:51:06', '2026-09-22 07:51:06'),
(191, 'AGC/26/185', 'MEMBER', 'Udakarandupana Dewapurayalage Kusumawathi', NULL, '547023218v', '0773888256', NULL, NULL, 'Ambuwangala, Imbulgasdeniya', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 07:58:59', '2026-09-22 07:58:59'),
(192, 'AGC/26/186', 'MEMBER', 'Kotamagala Gedara Premadasa', NULL, '5233733311', '0352265410', NULL, NULL, '87/29 Isuru Pedesa, Rohala para, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 08:12:35', '2026-09-22 08:12:35'),
(193, 'AGC/26/187', 'MEMBER', 'Jayasingha Mudiyanselage Nithaa Mangalika', NULL, '69630170v', '0776284324', NULL, NULL, '22/72 A Mihidu Mawatha, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 08:16:27', '2026-09-22 08:16:27'),
(194, 'AGC/26/188', 'MEMBER', 'A.A Sarath Amarasingha', NULL, '602140105v', '074475290', NULL, NULL, 'Aludeniya Mawatha, Rohala Para', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 08:18:32', '2026-09-22 08:18:32'),
(195, 'AGC/26/189', 'MEMBER', 'Vikter Vikramasingha', NULL, '592530929v', '0352265546', NULL, NULL, '28, Mihidu Mawatha, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 08:26:59', '2026-09-22 08:26:59'),
(196, 'AGC/26/190', 'MEMBER', 'Hemba Githiyanage Gamara Priyanthi Silwa', NULL, '677790881v', '0712495365', NULL, NULL, 'No 29/2 , Mihidu Mawatha , Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 08:33:16', '2026-09-22 08:33:16'),
(197, 'AGC/26/191', 'MEMBER', 'Rangallalage Umesh Imathka Rangalla', NULL, '200200701728', '0760720522', NULL, NULL, 'Godagampala, Daliwala, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 08:38:36', '2026-09-22 08:38:36');
INSERT INTO `parties` (`id`, `party_code`, `party_type`, `name`, `contact_person`, `nic_reg_no`, `phone`, `whatsapp_number`, `email`, `address`, `city`, `district`, `credit_limit`, `credit_days`, `payment_terms`, `status`, `notes`, `customer_type`, `supplier_type`, `customer_activity_id`, `created_by`, `created_at`, `updated_at`) VALUES
(198, 'AGC/26/192', 'MEMBER', 'Aluthwaththe Gedara Salikaa Dilrukshi Wijewardhana', NULL, '966382201v', '0711820946', NULL, NULL, 'Godagampala, Daliwala, Rambukkana', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 08:41:07', '2026-09-22 08:41:07'),
(199, 'AGC/26/193', 'MEMBER', 'Ranjakanthe Gedara Pushpakumara Wijesingha', NULL, '197325903250', '0701218051', NULL, NULL, 'Madagama ,Parape', 'Rambukkana', NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 6, '2026-09-22 08:45:09', '2026-09-22 08:45:09'),
(200, 'PTY-WALKIN', 'CUSTOMER', 'Walk-in Customer', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, 'active', NULL, NULL, NULL, NULL, 1, '2026-09-24 07:18:22', '2026-09-24 07:18:22');

-- --------------------------------------------------------

--
-- Table structure for table `party_opening_balances`
--

CREATE TABLE `party_opening_balances` (
  `id` int(11) NOT NULL,
  `party_id` int(11) NOT NULL,
  `type` enum('receivable','payable') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('draft','posted','reversed') DEFAULT 'draft',
  `journal_entry_id` int(11) DEFAULT NULL,
  `reversal_journal_entry_id` int(11) DEFAULT NULL,
  `reversal_reason` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_receipts`
--

CREATE TABLE `payment_receipts` (
  `id` int(10) UNSIGNED NOT NULL,
  `payment_number` varchar(50) NOT NULL,
  `party_id` int(10) UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'posted',
  `payment_type` varchar(50) NOT NULL,
  `income_account_id` int(10) UNSIGNED DEFAULT NULL,
  `cash_account_id` int(10) UNSIGNED DEFAULT NULL,
  `bank_account_id` int(10) UNSIGNED DEFAULT NULL,
  `journal_entry_id` int(10) UNSIGNED DEFAULT NULL,
  `reversal_journal_entry_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(150) NOT NULL,
  `module` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `code`, `name`, `module`, `description`, `created_at`, `updated_at`) VALUES
(1, 'dashboard.view', 'View Dashboard', 'dashboard', 'Access dashboard overview', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(2, 'coa.view', 'View Chart of Accounts', 'accounting', 'View chart of accounts', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(3, 'coa.manage', 'Manage Chart of Accounts', 'accounting', 'Add/Edit accounts', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(4, 'journal.view', 'View Journal Entries', 'accounting', 'View journal vouchers', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(5, 'journal.create', 'Create Journal Entries', 'accounting', 'Create new double-entry journal', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(6, 'ledger.view', 'View General Ledger', 'accounting', 'View ledger accounts', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(7, 'trial_balance.view', 'View Trial Balance', 'accounting', 'View trial balance report', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(8, 'sales.view', 'View Sales & Marketplace', 'sales', 'View sales orders and marketplace', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(9, 'purchases.view', 'View Purchasing & GRN', 'purchasing', 'View purchases and GRN', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(10, 'inventory.view', 'View Inventory', 'inventory', 'View stock balances and ledger', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(11, 'services.view', 'View Services & Rentals', 'services', 'View plowing and machinery rentals', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(12, 'production.view', 'View Production & Plantation', 'production', 'View crops, bricks, packing & mill', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(13, 'projects.view', 'View Construction Projects', 'projects', 'View construction contracts', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(14, 'finance.view', 'View Cash & Bank', 'finance', 'View cash and bank balances', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(15, 'reports.view', 'View Reports', 'reports', 'View financial and business reports', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(16, 'users.manage', 'Manage Users & Roles', 'admin', 'Manage user accounts and permissions', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(17, 'settings.manage', 'Manage Settings', 'admin', 'Manage system & company settings', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(18, 'audit.view', 'View Audit Logs', 'admin', 'View system audit trails', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(19, 'expenses.view', 'View Expenses', 'finance', 'View and search expense vouchers', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(20, 'expenses.create', 'Create Expenses', 'finance', 'Create new draft expenses', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(21, 'expenses.edit', 'Edit Expenses', 'finance', 'Edit draft expense vouchers', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(22, 'expenses.submit', 'Submit Expenses', 'finance', 'Submit draft expenses for approval', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(23, 'expenses.approve', 'Approve Expenses', 'finance', 'Approve pending expense vouchers', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(24, 'expenses.post', 'Post Expenses', 'finance', 'Post approved expenses to ledger', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(25, 'expenses.reverse', 'Reverse Expenses', 'finance', 'Create reversal vouchers for posted expenses', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(26, 'expenses.cancel', 'Cancel Expenses', 'finance', 'Cancel draft or pending expenses', '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(27, 'cheques.view', 'View Cheques', 'cheques', 'Auto-generated permission for cheques.view', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(28, 'cheques.update_status', 'Update_status Cheques', 'cheques', 'Auto-generated permission for cheques.update_status', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(29, 'deposits.view', 'View Deposits', 'deposits', 'Auto-generated permission for deposits.view', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(30, 'deposits.create', 'Create Deposits', 'deposits', 'Auto-generated permission for deposits.create', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(31, 'deposits.post', 'Post Deposits', 'deposits', 'Auto-generated permission for deposits.post', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(32, 'deposits.cancel', 'Cancel Deposits', 'deposits', 'Auto-generated permission for deposits.cancel', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(33, 'parties.view', 'View Parties', 'parties', 'Auto-generated permission for parties.view', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(34, 'parties.create', 'Create Parties', 'parties', 'Auto-generated permission for parties.create', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(35, 'parties.edit', 'Edit Parties', 'parties', 'Auto-generated permission for parties.edit', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(36, 'invoices.view', 'View Invoices', 'invoices', 'Auto-generated permission for invoices.view', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(37, 'invoices.create', 'Create Invoices', 'invoices', 'Auto-generated permission for invoices.create', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(38, 'invoices.post', 'Post Invoices', 'invoices', 'Auto-generated permission for invoices.post', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(39, 'invoices.cancel', 'Cancel Invoices', 'invoices', 'Auto-generated permission for invoices.cancel', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(40, 'journal.post', 'Post Journal', 'journal', 'Auto-generated permission for journal.post', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(41, 'journal.submit', 'Submit Journal', 'journal', 'Auto-generated permission for journal.submit', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(42, 'journal.approve', 'Approve Journal', 'journal', 'Auto-generated permission for journal.approve', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(43, 'journal.cancel', 'Cancel Journal', 'journal', 'Auto-generated permission for journal.cancel', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(44, 'journal.reverse', 'Reverse Journal', 'journal', 'Auto-generated permission for journal.reverse', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(45, 'machinery.view', 'View Machinery', 'machinery', 'Auto-generated permission for machinery.view', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(46, 'machinery.edit', 'Edit Machinery', 'machinery', 'Auto-generated permission for machinery.edit', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(47, 'machinery.create', 'Create Machinery', 'machinery', 'Auto-generated permission for machinery.create', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(48, 'machinery.maintenance', 'Maintenance Machinery', 'machinery', 'Auto-generated permission for machinery.maintenance', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(49, 'machinery.deactivate', 'Deactivate Machinery', 'machinery', 'Auto-generated permission for machinery.deactivate', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(50, 'machinery_rentals.view', 'View Machinery rentals', 'machinery_rentals', 'Auto-generated permission for machinery_rentals.view', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(51, 'machinery_rentals.complete', 'Complete Machinery rentals', 'machinery_rentals', 'Auto-generated permission for machinery_rentals.complete', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(52, 'machinery_rentals.cancel', 'Cancel Machinery rentals', 'machinery_rentals', 'Auto-generated permission for machinery_rentals.cancel', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(53, 'marketplace.view', 'View Marketplace', 'marketplace', 'Auto-generated permission for marketplace.view', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(54, 'marketplace.products', 'Products Marketplace', 'marketplace', 'Auto-generated permission for marketplace.products', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(55, 'marketplace.sales.view', 'Sales Marketplace', 'marketplace', 'Auto-generated permission for marketplace.sales.view', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(56, 'marketplace.sales.create', 'Sales Marketplace', 'marketplace', 'Auto-generated permission for marketplace.sales.create', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(57, 'marketplace.sales.post', 'Sales Marketplace', 'marketplace', 'Auto-generated permission for marketplace.sales.post', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(58, 'marketplace.sales.cancel', 'Sales Marketplace', 'marketplace', 'Auto-generated permission for marketplace.sales.cancel', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(59, 'parties.deactivate', 'Deactivate Parties', 'parties', 'Auto-generated permission for parties.deactivate', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(60, 'customer.opening_balance', 'Opening_balance Customer', 'customer', 'Auto-generated permission for customer.opening_balance', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(61, 'supplier.opening_balance', 'Opening_balance Supplier', 'supplier', 'Auto-generated permission for supplier.opening_balance', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(62, 'customer.opening_balance.reverse', 'Opening_balance Customer', 'customer', 'Auto-generated permission for customer.opening_balance.reverse', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(63, 'supplier.opening_balance.reverse', 'Opening_balance Supplier', 'supplier', 'Auto-generated permission for supplier.opening_balance.reverse', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(64, 'receipts.view', 'View Receipts', 'receipts', 'Auto-generated permission for receipts.view', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(65, 'supplier_payments.view', 'View Supplier payments', 'supplier_payments', 'Auto-generated permission for supplier_payments.view', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(66, 'receipts.create', 'Create Receipts', 'receipts', 'Auto-generated permission for receipts.create', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(67, 'supplier_payments.create', 'Create Supplier payments', 'supplier_payments', 'Auto-generated permission for supplier_payments.create', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(68, 'receipts.post', 'Post Receipts', 'receipts', 'Auto-generated permission for receipts.post', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(69, 'supplier_payments.post', 'Post Supplier payments', 'supplier_payments', 'Auto-generated permission for supplier_payments.post', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(70, 'receipts.reverse', 'Reverse Receipts', 'receipts', 'Auto-generated permission for receipts.reverse', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(71, 'supplier_payments.reverse', 'Reverse Supplier payments', 'supplier_payments', 'Auto-generated permission for supplier_payments.reverse', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(72, 'services.edit', 'Edit Services', 'services', 'Auto-generated permission for services.edit', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(73, 'services.create', 'Create Services', 'services', 'Auto-generated permission for services.create', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(74, 'services.deactivate', 'Deactivate Services', 'services', 'Auto-generated permission for services.deactivate', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(75, 'service_jobs.view', 'View Service jobs', 'service_jobs', 'Auto-generated permission for service_jobs.view', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(76, 'service_jobs.create', 'Create Service jobs', 'service_jobs', 'Auto-generated permission for service_jobs.create', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(77, 'service_jobs.edit', 'Edit Service jobs', 'service_jobs', 'Auto-generated permission for service_jobs.edit', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(78, 'service_jobs.complete', 'Complete Service jobs', 'service_jobs', 'Auto-generated permission for service_jobs.complete', '2026-08-14 21:58:50', '2026-08-14 21:58:50'),
(79, 'service_jobs.cancel', 'Cancel Service jobs', 'service_jobs', 'Auto-generated permission for service_jobs.cancel', '2026-08-14 21:58:50', '2026-08-14 21:58:50');

-- --------------------------------------------------------

--
-- Table structure for table `plantation_harvests`
--

CREATE TABLE `plantation_harvests` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `harvest_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `quality_grade` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plantation_harvest_transfers`
--

CREATE TABLE `plantation_harvest_transfers` (
  `id` int(10) UNSIGNED NOT NULL,
  `harvest_id` int(10) UNSIGNED NOT NULL,
  `transfer_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `cost_price_per_unit` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `selling_price_per_unit` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plantation_projects`
--

CREATE TABLE `plantation_projects` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_name` varchar(150) NOT NULL,
  `location` varchar(150) NOT NULL,
  `start_date` date NOT NULL,
  `expected_harvest_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('ACTIVE','COMPLETED','CANCELLED') NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plantation_project_crops`
--

CREATE TABLE `plantation_project_crops` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `planned_quantity` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `unit` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `product_code` varchar(100) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `supplier_id` int(10) UNSIGNED DEFAULT NULL,
  `product_type` varchar(50) DEFAULT 'TRADING',
  `base_unit_id` int(10) UNSIGNED DEFAULT NULL,
  `purchase_unit_id` int(10) UNSIGNED DEFAULT NULL,
  `sales_unit_id` int(10) UNSIGNED DEFAULT NULL,
  `default_purchase_price` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `default_selling_price` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `inventory_account_id` int(10) UNSIGNED DEFAULT NULL,
  `cogs_account_id` int(10) UNSIGNED DEFAULT NULL,
  `sales_revenue_account_id` int(10) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_marketplace` tinyint(1) NOT NULL DEFAULT 1,
  `source_module` varchar(50) DEFAULT 'PURCHASE',
  `source_transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `code`, `name`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'Super Admin', 'Full unrestricted system administrative access', 1, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(2, 'admin', 'Administrator', 'General administration and user management access', 1, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(3, 'accountant', 'Accountant', 'Full financial accounting, journals, and reports access', 1, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(4, 'manager', 'Manager', 'Operational management and review access', 0, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(5, 'cashier', 'Cashier', 'POS, customer payments, and cash management access', 0, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(6, 'inventory_officer', 'Inventory Officer', 'Stock management, GRN, and inventory access', 0, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(7, 'sales_officer', 'Sales Officer', 'Sales orders, customer management, and billing access', 0, '2026-08-14 21:24:52', '2026-08-14 21:24:52'),
(8, 'production_officer', 'Production Officer', 'Plantation, manufacturing, and processing batch access', 0, '2026-08-14 21:24:52', '2026-08-14 21:24:52');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 42),
(1, 43),
(1, 44),
(1, 45),
(1, 46),
(1, 47),
(1, 48),
(1, 49),
(1, 50),
(1, 51),
(1, 52),
(1, 53),
(1, 54),
(1, 55),
(1, 56),
(1, 57),
(1, 58),
(1, 59),
(1, 60),
(1, 61),
(1, 62),
(1, 63),
(1, 64),
(1, 65),
(1, 66),
(1, 67),
(1, 68),
(1, 69),
(1, 70),
(1, 71),
(1, 72),
(1, 73),
(1, 74),
(1, 75),
(1, 76),
(1, 77),
(1, 78),
(1, 79),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 23),
(2, 24),
(2, 25),
(2, 26),
(2, 27),
(2, 28),
(2, 29),
(2, 30),
(2, 31),
(2, 32),
(2, 33),
(2, 34),
(2, 35),
(2, 36),
(2, 37),
(2, 38),
(2, 39),
(2, 40),
(2, 41),
(2, 42),
(2, 43),
(2, 44),
(2, 45),
(2, 46),
(2, 47),
(2, 48),
(2, 49),
(2, 50),
(2, 51),
(2, 52),
(2, 53),
(2, 54),
(2, 55),
(2, 56),
(2, 57),
(2, 58),
(2, 59),
(2, 60),
(2, 61),
(2, 62),
(2, 63),
(2, 64),
(2, 65),
(2, 66),
(2, 67),
(2, 68),
(2, 69),
(2, 70),
(2, 71),
(2, 72),
(2, 73),
(2, 74),
(2, 75),
(2, 76),
(2, 77),
(2, 78),
(2, 79),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 14),
(3, 15),
(3, 19),
(3, 20),
(3, 21),
(3, 22),
(3, 23),
(3, 24),
(3, 25),
(3, 26),
(4, 19),
(4, 20),
(4, 21),
(4, 22),
(4, 23),
(4, 24),
(4, 25),
(4, 26),
(5, 19),
(5, 20),
(5, 21),
(5, 22),
(5, 23),
(5, 24),
(5, 25),
(5, 26),
(6, 19),
(6, 20),
(6, 21),
(6, 22),
(6, 23),
(6, 24),
(6, 25),
(6, 26),
(7, 19),
(7, 20),
(7, 21),
(7, 22),
(7, 23),
(7, 24),
(7, 25),
(7, 26),
(8, 19),
(8, 20),
(8, 21),
(8, 22),
(8, 23),
(8, 24),
(8, 25),
(8, 26);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(10) UNSIGNED NOT NULL,
  `service_code` varchar(50) NOT NULL,
  `service_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `unit` varchar(50) DEFAULT 'Job',
  `default_price` decimal(15,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `revenue_account_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_code`, `service_name`, `description`, `unit`, `default_price`, `is_active`, `revenue_account_id`) VALUES
(1, 'SRV-MACH-RNT', 'Machinery & Equipment Rental Service', 'Machinery rental billing', 'Hour', 0.00, 1, NULL),
(2, 'SRV-0002', 'Ploughing', '', 'Job', 0.00, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_jobs`
--

CREATE TABLE `service_jobs` (
  `id` int(10) UNSIGNED NOT NULL,
  `job_number` varchar(50) NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'PENDING',
  `invoice_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_ledger`
--

CREATE TABLE `stock_ledger` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `movement_date` date NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `movement_type` varchar(50) DEFAULT NULL,
  `source_module` varchar(50) DEFAULT NULL,
  `source_type` varchar(50) DEFAULT NULL,
  `source_transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `quantity_in` decimal(15,4) DEFAULT 0.0000,
  `quantity_out` decimal(15,4) DEFAULT 0.0000,
  `unit_cost` decimal(15,4) DEFAULT 0.0000,
  `total_cost` decimal(15,4) DEFAULT 0.0000,
  `balance_quantity` decimal(15,4) DEFAULT 0.0000,
  `balance_value` decimal(15,4) DEFAULT 0.0000,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'system',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `description`, `updated_at`) VALUES
(1, 'app_title', 'Agri Co-Op ERP', 'system', 'ERP Application Title', '2026-08-14 21:24:52'),
(2, 'timezone', 'Asia/Colombo', 'system', 'System Timezone', '2026-08-14 21:24:52'),
(3, 'date_format', 'Y-m-d', 'system', 'Default Display Date Format', '2026-08-14 21:24:52'),
(4, 'journal_prefix', 'JV-', 'accounting', 'Journal Voucher Prefix', '2026-08-14 21:24:52'),
(5, 'invoice_prefix', 'INV-', 'sales', 'Invoice Number Prefix', '2026-08-14 21:24:52');

-- --------------------------------------------------------

--
-- Table structure for table `units_of_measure`
--

CREATE TABLE `units_of_measure` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units_of_measure`
--

INSERT INTO `units_of_measure` (`id`, `code`, `name`, `created_at`, `updated_at`) VALUES
(1, 'KG', 'Kilogram', '2026-08-14 23:42:21', '2026-08-14 23:42:21'),
(2, 'L', 'Litre', '2026-08-14 23:42:21', '2026-08-14 23:42:21'),
(3, 'PCS', 'Pieces', '2026-08-14 23:42:21', '2026-08-14 23:42:21'),
(4, 'PKT', 'Packet', '2026-08-14 23:42:21', '2026-08-14 23:42:21'),
(5, 'BOX', 'Box', '2026-08-14 23:42:21', '2026-08-14 23:42:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `party_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `phone`, `party_id`, `status`, `last_login`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', 'admin@agricoop.lk', '$2y$10$/nTxywDUdEYGDvWFWfv2EeHjbWd03P/r50epypAwEmw2gdka4dkEa', 'Super Administrator', '0753770145', NULL, 'active', '2026-09-28 05:14:39', '2026-08-14 21:24:52', '2026-09-28 05:14:39', NULL),
(3, 'kumara', '', '$2y$10$0k/3Vm.mJUfT4WayUOmGZ.N3UMCy2Bax7HupUepFNuMNOJYijiq4u', 'Kumara Siyambalapitiya', '0753770145', NULL, 'active', '2026-08-31 12:25:34', '2026-08-31 12:24:54', '2026-08-31 12:25:34', NULL),
(6, 'nethmaj', NULL, '$2y$10$JWQHEagOiaZYIzWKmVHNueQaHsUEhom87xhGHPNr8bIPVLTRaoUo.', 'Nethma Jayasinghe', '', NULL, 'active', '2026-09-28 03:59:01', '2026-09-14 06:05:20', '2026-09-28 03:59:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(1, 1),
(6, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_code` (`account_code`),
  ADD KEY `fk_acc_parent` (`parent_id`),
  ADD KEY `fk_acc_type` (`account_type_id`),
  ADD KEY `idx_acc_code` (`account_code`),
  ADD KEY `idx_acc_category` (`category`),
  ADD KEY `idx_acc_active` (`is_active`);

--
-- Indexes for table `account_types`
--
ALTER TABLE `account_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `agricultural_sectors`
--
ALTER TABLE `agricultural_sectors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_module` (`module`),
  ADD KEY `idx_audit_created` (`created_at`);

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bank_acc` (`account_id`);

--
-- Indexes for table `bank_deposits`
--
ALTER TABLE `bank_deposits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `deposit_number` (`deposit_number`);

--
-- Indexes for table `bank_reconciliations`
--
ALTER TABLE `bank_reconciliations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bank_account_id` (`bank_account_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `brick_production_projects`
--
ALTER TABLE `brick_production_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bpp_product` (`product_id`);

--
-- Indexes for table `brick_production_records`
--
ALTER TABLE `brick_production_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bpr_project` (`project_id`),
  ADD KEY `fk_bpr_product` (`product_id`);

--
-- Indexes for table `brick_transfers`
--
ALTER TABLE `brick_transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bt_project` (`project_id`),
  ADD KEY `fk_bt_record` (`production_record_id`);

--
-- Indexes for table `cash_accounts`
--
ALTER TABLE `cash_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_cash_acc` (`account_id`);

--
-- Indexes for table `cheques`
--
ALTER TABLE `cheques`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coop_members`
--
ALTER TABLE `coop_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `membership_no` (`member_no`),
  ADD KEY `party_id` (`party_id`),
  ADD KEY `journal_entry_id` (`journal_entry_id`);

--
-- Indexes for table `cost_centers`
--
ALTER TABLE `cost_centers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_cc_code` (`code`),
  ADD KEY `idx_cc_active` (`is_active`);

--
-- Indexes for table `customer_activities`
--
ALTER TABLE `customer_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deposit_items`
--
ALTER TABLE `deposit_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deposit_id` (`deposit_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_attachments`
--
ALTER TABLE `expense_attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_balances`
--
ALTER TABLE `inventory_balances`
  ADD PRIMARY KEY (`product_id`,`location_id`);

--
-- Indexes for table `inventory_locations`
--
ALTER TABLE `inventory_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `journal_number` (`journal_number`),
  ADD KEY `fk_je_cost_center` (`cost_center_id`),
  ADD KEY `fk_je_created_by` (`created_by`),
  ADD KEY `idx_je_number` (`journal_number`),
  ADD KEY `idx_je_date` (`transaction_date`),
  ADD KEY `idx_je_source` (`source_module`,`source_transaction_id`),
  ADD KEY `idx_je_status` (`status`);

--
-- Indexes for table `journal_lines`
--
ALTER TABLE `journal_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_jl_je` (`journal_entry_id`),
  ADD KEY `idx_jl_acc` (`account_id`);

--
-- Indexes for table `ledger_entries`
--
ALTER TABLE `ledger_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_le_je` (`journal_entry_id`),
  ADD KEY `fk_le_jl` (`journal_line_id`),
  ADD KEY `idx_le_acc_date` (`account_id`,`transaction_date`),
  ADD KEY `idx_le_cc` (`cost_center_id`);

--
-- Indexes for table `machinery`
--
ALTER TABLE `machinery`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `machinery_code` (`machinery_code`);

--
-- Indexes for table `machinery_rentals`
--
ALTER TABLE `machinery_rentals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rental_number` (`rental_number`);

--
-- Indexes for table `member_fixed_deposits`
--
ALTER TABLE `member_fixed_deposits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `deposit_number` (`deposit_number`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `journal_entry_id` (`journal_entry_id`),
  ADD KEY `maturity_journal_entry_id` (`maturity_journal_entry_id`);

--
-- Indexes for table `parties`
--
ALTER TABLE `parties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `party_code` (`party_code`),
  ADD KEY `fk_party_creator` (`created_by`);

--
-- Indexes for table `party_opening_balances`
--
ALTER TABLE `party_opening_balances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_receipts`
--
ALTER TABLE `payment_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_number` (`payment_number`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_perm_module` (`module`);

--
-- Indexes for table `plantation_harvests`
--
ALTER TABLE `plantation_harvests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ph_project` (`project_id`),
  ADD KEY `fk_ph_product` (`product_id`),
  ADD KEY `fk_ph_creator` (`created_by`);

--
-- Indexes for table `plantation_harvest_transfers`
--
ALTER TABLE `plantation_harvest_transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pht_harvest` (`harvest_id`),
  ADD KEY `fk_pht_creator` (`created_by`);

--
-- Indexes for table `plantation_projects`
--
ALTER TABLE `plantation_projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plantation_project_crops`
--
ALTER TABLE `plantation_project_crops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ppc_project` (`project_id`),
  ADD KEY `fk_ppc_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_code` (`product_code`),
  ADD KEY `fk_prod_cat` (`category_id`),
  ADD KEY `fk_prod_base_unit` (`base_unit_id`),
  ADD KEY `fk_prod_inv_acc` (`inventory_account_id`),
  ADD KEY `fk_prod_cogs_acc` (`cogs_account_id`),
  ADD KEY `fk_prod_sales_acc` (`sales_revenue_account_id`),
  ADD KEY `fk_prod_creator` (`created_by`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `fk_rp_perm` (`permission_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_code` (`service_code`);

--
-- Indexes for table `service_jobs`
--
ALTER TABLE `service_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_number` (`job_number`);

--
-- Indexes for table `stock_ledger`
--
ALTER TABLE `stock_ledger`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `units_of_measure`
--
ALTER TABLE `units_of_measure`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_username` (`username`),
  ADD KEY `idx_users_status` (`status`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `fk_ur_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `account_types`
--
ALTER TABLE `account_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `agricultural_sectors`
--
ALTER TABLE `agricultural_sectors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=403;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=718;

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_deposits`
--
ALTER TABLE `bank_deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_reconciliations`
--
ALTER TABLE `bank_reconciliations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brick_production_projects`
--
ALTER TABLE `brick_production_projects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brick_production_records`
--
ALTER TABLE `brick_production_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brick_transfers`
--
ALTER TABLE `brick_transfers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_accounts`
--
ALTER TABLE `cash_accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cheques`
--
ALTER TABLE `cheques`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coop_members`
--
ALTER TABLE `coop_members`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `cost_centers`
--
ALTER TABLE `cost_centers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customer_activities`
--
ALTER TABLE `customer_activities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposit_items`
--
ALTER TABLE `deposit_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_attachments`
--
ALTER TABLE `expense_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inventory_locations`
--
ALTER TABLE `inventory_locations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=318;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=524;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=329;

--
-- AUTO_INCREMENT for table `journal_lines`
--
ALTER TABLE `journal_lines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=840;

--
-- AUTO_INCREMENT for table `ledger_entries`
--
ALTER TABLE `ledger_entries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `machinery`
--
ALTER TABLE `machinery`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `machinery_rentals`
--
ALTER TABLE `machinery_rentals`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_fixed_deposits`
--
ALTER TABLE `member_fixed_deposits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parties`
--
ALTER TABLE `parties`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `party_opening_balances`
--
ALTER TABLE `party_opening_balances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_receipts`
--
ALTER TABLE `payment_receipts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `plantation_harvests`
--
ALTER TABLE `plantation_harvests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plantation_harvest_transfers`
--
ALTER TABLE `plantation_harvest_transfers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plantation_projects`
--
ALTER TABLE `plantation_projects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plantation_project_crops`
--
ALTER TABLE `plantation_project_crops`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `service_jobs`
--
ALTER TABLE `service_jobs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_ledger`
--
ALTER TABLE `stock_ledger`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `units_of_measure`
--
ALTER TABLE `units_of_measure`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `fk_acc_parent` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `fk_acc_type` FOREIGN KEY (`account_type_id`) REFERENCES `account_types` (`id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD CONSTRAINT `fk_bank_acc` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `bank_reconciliations`
--
ALTER TABLE `bank_reconciliations`
  ADD CONSTRAINT `bank_reconciliations_ibfk_1` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bank_reconciliations_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `brick_production_projects`
--
ALTER TABLE `brick_production_projects`
  ADD CONSTRAINT `fk_bpp_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `brick_production_records`
--
ALTER TABLE `brick_production_records`
  ADD CONSTRAINT `fk_bpr_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_bpr_project` FOREIGN KEY (`project_id`) REFERENCES `brick_production_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `brick_transfers`
--
ALTER TABLE `brick_transfers`
  ADD CONSTRAINT `fk_bt_project` FOREIGN KEY (`project_id`) REFERENCES `brick_production_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bt_record` FOREIGN KEY (`production_record_id`) REFERENCES `brick_production_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cash_accounts`
--
ALTER TABLE `cash_accounts`
  ADD CONSTRAINT `fk_cash_acc` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `coop_members`
--
ALTER TABLE `coop_members`
  ADD CONSTRAINT `coop_members_ibfk_1` FOREIGN KEY (`party_id`) REFERENCES `parties` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `coop_members_ibfk_2` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `deposit_items`
--
ALTER TABLE `deposit_items`
  ADD CONSTRAINT `deposit_items_ibfk_1` FOREIGN KEY (`deposit_id`) REFERENCES `bank_deposits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `fk_je_cost_center` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_je_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `journal_lines`
--
ALTER TABLE `journal_lines`
  ADD CONSTRAINT `fk_jl_acc` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `fk_jl_je` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ledger_entries`
--
ALTER TABLE `ledger_entries`
  ADD CONSTRAINT `fk_le_acc` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `fk_le_cc` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_le_je` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_le_jl` FOREIGN KEY (`journal_line_id`) REFERENCES `journal_lines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `member_fixed_deposits`
--
ALTER TABLE `member_fixed_deposits`
  ADD CONSTRAINT `member_fixed_deposits_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `coop_members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `member_fixed_deposits_ibfk_2` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `member_fixed_deposits_ibfk_3` FOREIGN KEY (`maturity_journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `parties`
--
ALTER TABLE `parties`
  ADD CONSTRAINT `fk_party_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `plantation_harvests`
--
ALTER TABLE `plantation_harvests`
  ADD CONSTRAINT `fk_ph_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_ph_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_ph_project` FOREIGN KEY (`project_id`) REFERENCES `plantation_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plantation_harvest_transfers`
--
ALTER TABLE `plantation_harvest_transfers`
  ADD CONSTRAINT `fk_pht_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_pht_harvest` FOREIGN KEY (`harvest_id`) REFERENCES `plantation_harvests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plantation_project_crops`
--
ALTER TABLE `plantation_project_crops`
  ADD CONSTRAINT `fk_ppc_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_ppc_project` FOREIGN KEY (`project_id`) REFERENCES `plantation_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_prod_base_unit` FOREIGN KEY (`base_unit_id`) REFERENCES `units_of_measure` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prod_cogs_acc` FOREIGN KEY (`cogs_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prod_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prod_inv_acc` FOREIGN KEY (`inventory_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prod_sales_acc` FOREIGN KEY (`sales_revenue_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
