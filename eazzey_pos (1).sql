-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2022 at 03:27 PM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 7.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eazzey_pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_code_name` varchar(255) NOT NULL,
  `account_type` int(11) NOT NULL,
  `account_group` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `account_name`, `account_code_name`, `account_type`, `account_group`) VALUES
(1, 'Cash Account', 'cash_account', 1, 1),
(2, 'Inventory Account', 'inventory', 1, 1),
(3, 'Revenue Account', 'revenue', 2, 6),
(4, 'Capital (Owners Equity)', 'capital', 2, 5),
(5, 'Creditors Account (Accounts Payable)', 'creditors', 2, 4),
(6, 'Debtors Account (Accounts Receivable)', 'debtors', 1, 1),
(7, 'Expenses (Cost Of Goods)', 'expenses', 1, 3),
(8, 'Returns / Earnings (Net Income)', 'returns', 2, 5),
(9, 'Sales Returns account (Returned Items)', 'sales_returns', 2, 6);

-- --------------------------------------------------------

--
-- Table structure for table `account_groups`
--

CREATE TABLE `account_groups` (
  `id` int(11) NOT NULL,
  `account_group_name` varchar(255) NOT NULL,
  `account_group_code_name` varchar(255) NOT NULL,
  `account_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `account_groups`
--

INSERT INTO `account_groups` (`id`, `account_group_name`, `account_group_code_name`, `account_type_id`) VALUES
(1, 'Assets', 'assets', 1),
(2, 'Dividends(Drawings or withdraws)', 'dividends', 1),
(3, 'Expenses', 'expenses', 1),
(4, 'Liabilities', 'liabilities', 2),
(5, 'Equity (Owners Capital)', 'equity', 2),
(6, 'Revenue', 'revenue', 2);

-- --------------------------------------------------------

--
-- Table structure for table `account_types`
--

CREATE TABLE `account_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `type_code_name` varchar(255) NOT NULL,
  `increasing` varchar(255) NOT NULL,
  `decreasing` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `account_types`
--

INSERT INTO `account_types` (`id`, `type_name`, `type_code_name`, `increasing`, `decreasing`) VALUES
(1, 'ADEX (Dr)', 'ADEX', 'DR', 'CR'),
(2, 'LER (Cr)', 'LER', 'CR', 'DR');

-- --------------------------------------------------------

--
-- Table structure for table `creditor_transactions`
--

CREATE TABLE `creditor_transactions` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `amount_paid` double NOT NULL,
  `narration` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone_no` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `credit_limit` double NOT NULL,
  `isUsed` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `phone_no`, `email`, `credit_limit`, `isUsed`, `created`) VALUES
(1, 'walulya', 'francis', '0756743152', 'walulyafrancis@gmail.com', 0, 1, '2022-04-16 14:50:14');

-- --------------------------------------------------------

--
-- Table structure for table `debtor_transactions`
--

CREATE TABLE `debtor_transactions` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount_recieved` double NOT NULL,
  `narration` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fiscal_year`
--

CREATE TABLE `fiscal_year` (
  `id` int(11) NOT NULL,
  `year_index` int(11) NOT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `closed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fiscal_year`
--

INSERT INTO `fiscal_year` (`id`, `year_index`, `start`, `end`, `closed`) VALUES
(1, 0, '2022-04-09', '2022-12-31', 0);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `trx_type_id` int(11) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `qty` double NOT NULL,
  `unit_cost_price` double NOT NULL,
  `unit_selling_price` int(11) NOT NULL,
  `units_id` int(11) NOT NULL,
  `inventory_category` int(11) NOT NULL,
  `has_expirly_date` tinyint(1) NOT NULL,
  `expires` date NOT NULL,
  `currency_code` varchar(255) NOT NULL,
  `narration` varchar(255) NOT NULL,
  `account_posting` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `supplier_id`, `trx_type_id`, `item_code`, `barcode`, `item_name`, `qty`, `unit_cost_price`, `unit_selling_price`, `units_id`, `inventory_category`, `has_expirly_date`, `expires`, `currency_code`, `narration`, `account_posting`, `created`) VALUES
(1, 1, 1, 'ITM001', '4353535', 'Plastic plates', 100, 1, 5, 3, 1, 0, '0000-00-00', 'EUR', '', 1, '2022-04-11 13:47:52'),
(2, 2, 2, 'ITM002', '7667364', 'Blue band Margarine', 10, 1, 2, 3, 1, 0, '0000-00-00', 'EUR', '', 1, '2022-04-16 13:47:52');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_category`
--

CREATE TABLE `inventory_category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_code_name` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inventory_category`
--

INSERT INTO `inventory_category` (`id`, `category_name`, `category_code_name`, `created`) VALUES
(1, 'Single Category', 'single_category', '2022-04-13 20:54:16');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_units`
--

CREATE TABLE `inventory_units` (
  `id` int(11) NOT NULL,
  `unit_name` varchar(255) NOT NULL,
  `unit_symbol` varchar(255) NOT NULL,
  `used` tinyint(1) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inventory_units`
--

INSERT INTO `inventory_units` (`id`, `unit_name`, `unit_symbol`, `used`, `created`) VALUES
(1, 'Kilograms', 'kgs', 1, '2022-04-15 16:40:16'),
(2, 'Grams', 'g', 1, '2022-04-15 16:40:16'),
(3, 'pieces', 'pcs', 1, '2022-04-15 16:41:25'),
(4, 'bags', 'bgs', 1, '2022-04-15 16:41:25');

-- --------------------------------------------------------

--
-- Table structure for table `journal`
--

CREATE TABLE `journal` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `journal_cat_id` int(11) NOT NULL,
  `fiscal_year_id` int(11) NOT NULL,
  `dr` double NOT NULL,
  `cr` double NOT NULL,
  `journal_no` varchar(255) NOT NULL,
  `narration` varchar(255) NOT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT '1',
  `journal_date` date NOT NULL,
  `post_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `journal`
--

INSERT INTO `journal` (`id`, `account_id`, `journal_cat_id`, `fiscal_year_id`, `dr`, `cr`, `journal_no`, `narration`, `is_open`, `journal_date`, `post_date`) VALUES
(244, 1, 5, 1, 100, 0, 'JRN267164', 'Deposit of 100 in cash to capital account', 1, '2022-04-11', '2022-04-15 02:30:55'),
(245, 4, 5, 1, 0, 100, 'JRN267164', 'Deposit of 100 in cash to capital account', 1, '2022-04-11', '2022-04-15 02:30:55'),
(246, 2, 1, 1, 100, 0, 'JRN27840', 'Cash purchase of 100 Plastic plates', 1, '2022-04-11', '2022-04-15 02:30:56'),
(247, 1, 1, 1, 0, 100, 'JRN27840', 'Cash purchase of 100 Plastic plates', 1, '2022-04-11', '2022-04-15 02:30:56'),
(248, 7, 1, 1, 2, 0, 'JRN970758', 'Cost of goods for 2 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:31'),
(249, 2, 1, 1, 0, 2, 'JRN970758', 'Cost of goods for 2 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:31'),
(250, 1, 2, 1, 10, 0, 'JRN472129', 'Cash sale of 2 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:31'),
(251, 3, 2, 1, 0, 10, 'JRN472129', 'Cash sale of 2 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:31'),
(252, 7, 1, 1, 10, 0, 'JRN449051', 'Cost of goods for 10 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:31'),
(253, 2, 1, 1, 0, 10, 'JRN449051', 'Cost of goods for 10 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:31'),
(254, 1, 2, 1, 50, 0, 'JRN187542', 'Cash sale of 10 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:31'),
(255, 3, 2, 1, 0, 50, 'JRN187542', 'Cash sale of 10 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(256, 7, 1, 1, 15, 0, 'JRN903706', 'Cost of goods for 15 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(257, 2, 1, 1, 0, 15, 'JRN903706', 'Cost of goods for 15 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(258, 1, 2, 1, 75, 0, 'JRN474527', 'Cash sale of 15 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(259, 3, 2, 1, 0, 75, 'JRN474527', 'Cash sale of 15 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(260, 7, 1, 1, 10, 0, 'JRN221324', 'Cost of goods for 10 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(261, 2, 1, 1, 0, 10, 'JRN221324', 'Cost of goods for 10 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(262, 1, 2, 1, 50, 0, 'JRN433755', 'Cash sale of 10 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(263, 3, 2, 1, 0, 50, 'JRN433755', 'Cash sale of 10 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(264, 7, 1, 1, 20, 0, 'JRN269916', 'Cost of goods for 20 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(265, 2, 1, 1, 0, 20, 'JRN269916', 'Cost of goods for 20 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(266, 1, 2, 1, 100, 0, 'JRN452146', 'Cash sale of 20 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(267, 3, 2, 1, 0, 100, 'JRN452146', 'Cash sale of 20 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(268, 7, 1, 1, 8, 0, 'JRN553226', 'Cost of goods for 8 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(269, 2, 1, 1, 0, 8, 'JRN553226', 'Cost of goods for 8 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(270, 1, 2, 1, 40, 0, 'JRN970269', 'Cash sale of 8 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(271, 3, 2, 1, 0, 40, 'JRN970269', 'Cash sale of 8 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(272, 7, 1, 1, 30, 0, 'JRN815916', 'Cost of goods for 30 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(273, 2, 1, 1, 0, 30, 'JRN815916', 'Cost of goods for 30 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(274, 1, 2, 1, 150, 0, 'JRN822541', 'Cash sale of 30 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(275, 3, 2, 1, 0, 150, 'JRN822541', 'Cash sale of 30 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(276, 7, 1, 1, 5, 0, 'JRN704954', 'Cost of goods for 5 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(277, 2, 1, 1, 0, 5, 'JRN704954', 'Cost of goods for 5 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(278, 1, 2, 1, 25, 0, 'JRN105060', 'Cash sale of 5 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:32'),
(279, 3, 2, 1, 0, 25, 'JRN105060', 'Cash sale of 5 Plastic plates', 1, '2022-04-11', '2022-04-15 02:31:33'),
(290, 7, 1, 1, 0, 1, 'JRN326882', 'Sale in excess of 1 plate', 1, '2022-04-15', '2022-04-15 22:27:04'),
(291, 2, 1, 1, 1, 0, 'JRN326882', 'Sale in excess of 1 plate', 1, '2022-04-15', '2022-04-15 22:27:04'),
(292, 9, 2, 1, 5, 0, 'JRN302466', 'Sale in excess of 1 plate', 1, '2022-04-15', '2022-04-15 22:27:05'),
(293, 1, 2, 1, 0, 5, 'JRN302466', 'Sale in excess of 1 plate', 1, '2022-04-15', '2022-04-15 22:27:05'),
(294, 7, 1, 1, 1, 0, 'JRN31691', 'Cost of goods for 1 Plastic plates', 1, '2022-04-11', '2022-04-15 22:53:39'),
(295, 2, 1, 1, 0, 1, 'JRN31691', 'Cost of goods for 1 Plastic plates', 1, '2022-04-11', '2022-04-15 22:53:39'),
(296, 1, 2, 1, 5, 0, 'JRN155872', 'Cash sale of 1 Plastic plates', 1, '2022-04-11', '2022-04-15 22:53:39'),
(297, 3, 2, 1, 0, 5, 'JRN155872', 'Cash sale of 1 Plastic plates', 1, '2022-04-11', '2022-04-15 22:53:39'),
(298, 2, 1, 1, 10, 0, 'JRN431660', 'Credit purchase of 10 Blue band Margarine from Blue Band Ltd', 1, '2022-04-16', '2022-04-16 16:25:12'),
(299, 5, 1, 1, 0, 10, 'JRN431660', 'Credit purchase of 10 Blue band Margarine from Blue Band Ltd', 1, '2022-04-16', '2022-04-16 16:25:12');

-- --------------------------------------------------------

--
-- Table structure for table `journal_categories`
--

CREATE TABLE `journal_categories` (
  `id` int(11) NOT NULL,
  `journal_cat_name` varchar(255) NOT NULL,
  `journal_cat_code_name` varchar(255) NOT NULL,
  `jounal_cat_no` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `journal_categories`
--

INSERT INTO `journal_categories` (`id`, `journal_cat_name`, `journal_cat_code_name`, `jounal_cat_no`) VALUES
(1, 'Inventory(purchases)', 'inventory', 'JNI0001'),
(2, 'Sales', 'sales', 'JNS0002'),
(3, 'Expenses', 'expenses', 'JNEXPS003'),
(4, 'Assets', 'assets', 'JNASTS004'),
(5, 'Capital & Cash', 'capital_and_cash', 'JNASTS005');

-- --------------------------------------------------------

--
-- Table structure for table `journal_old`
--

CREATE TABLE `journal_old` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `journal_cat_id` int(11) NOT NULL,
  `fiscal_year_id` int(11) NOT NULL,
  `dr` double NOT NULL,
  `cr` double NOT NULL,
  `journal_no` varchar(255) NOT NULL,
  `narration` varchar(255) NOT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT '1',
  `journal_date` date NOT NULL,
  `post_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `journal_old`
--

INSERT INTO `journal_old` (`id`, `account_id`, `journal_cat_id`, `fiscal_year_id`, `dr`, `cr`, `journal_no`, `narration`, `is_open`, `journal_date`, `post_date`) VALUES
(45, 1, 2, 1, 120, 0, 'JRN98067', 'Deposit of 100 in cash to capital account', 1, '2022-04-11', '2022-04-13 13:37:41'),
(46, 4, 2, 1, 0, 120, 'JRN98067', 'Deposit of 100 in cash to capital account', 1, '2022-04-11', '2022-04-13 13:37:41'),
(81, 7, 2, 1, 120, 0, 'JRN45634', 'Cost of goods for 100 plates', 1, '2022-04-11', '2022-04-13 13:37:41'),
(82, 2, 2, 1, 0, 120, 'JRN45634', 'Cost of goods for 100 plates', 1, '2022-04-11', '2022-04-13 13:37:41'),
(99, 2, 1, 1, 120, 0, 'JRN458950', 'Cash purchase of 100 Plastic plates', 1, '2022-04-11', '2022-04-13 18:16:56'),
(100, 1, 1, 1, 0, 120, 'JRN458950', 'Cash purchase of 100 Plastic plates', 1, '2022-04-11', '2022-04-13 18:16:56'),
(133, 1, 2, 1, 10, 0, 'JRN71114', 'Cash sale of 2 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:33'),
(134, 3, 2, 1, 0, 10, 'JRN71114', 'Cash sale of 2 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:33'),
(135, 1, 2, 1, 50, 0, 'JRN678197', 'Cash sale of 10 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:33'),
(136, 3, 2, 1, 0, 50, 'JRN678197', 'Cash sale of 10 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:33'),
(137, 1, 2, 1, 75, 0, 'JRN192169', 'Cash sale of 15 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:33'),
(138, 3, 2, 1, 0, 75, 'JRN192169', 'Cash sale of 15 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:33'),
(139, 1, 2, 1, 50, 0, 'JRN65773', 'Cash sale of 10 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:34'),
(140, 3, 2, 1, 0, 50, 'JRN65773', 'Cash sale of 10 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:34'),
(141, 1, 2, 1, 100, 0, 'JRN282890', 'Cash sale of 20 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:34'),
(142, 3, 2, 1, 0, 100, 'JRN282890', 'Cash sale of 20 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:34'),
(143, 1, 2, 1, 40, 0, 'JRN53536', 'Cash sale of 8 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:34'),
(144, 3, 2, 1, 0, 40, 'JRN53536', 'Cash sale of 8 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:34'),
(145, 1, 2, 1, 150, 0, 'JRN311290', 'Cash sale of 30 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:34'),
(146, 3, 2, 1, 0, 150, 'JRN311290', 'Cash sale of 30 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:34'),
(147, 1, 2, 1, 25, 0, 'JRN187554', 'Cash sale of 5 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:34'),
(148, 3, 2, 1, 0, 25, 'JRN187554', 'Cash sale of 5 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:34'),
(149, 1, 2, 1, 100, 0, 'JRN186757', 'Cash sale of 20 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:34'),
(150, 3, 2, 1, 0, 100, 'JRN186757', 'Cash sale of 20 Plastic plates', 1, '2022-04-11', '2022-04-13 18:31:34');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderID` int(11) NOT NULL,
  `orderNo` varchar(255) NOT NULL,
  `debtor_customer` int(11) DEFAULT NULL,
  `orderDate` datetime NOT NULL,
  `transaction_type_id` int(11) NOT NULL,
  `payment_type_id` int(11) NOT NULL,
  `account_posting` tinyint(1) NOT NULL DEFAULT '0',
  `isSettled` tinyint(1) NOT NULL,
  `isOpen` tinyint(1) NOT NULL,
  `isCancelled` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`orderID`, `orderNo`, `debtor_customer`, `orderDate`, `transaction_type_id`, `payment_type_id`, `account_posting`, `isSettled`, `isOpen`, `isCancelled`) VALUES
(1, '112007', NULL, '2022-04-11 00:00:00', 1, 1, 1, 1, 0, 0),
(2, '112008', NULL, '2022-04-11 00:00:00', 1, 1, 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `qty` double NOT NULL,
  `discount` double NOT NULL,
  `has_refund` tinyint(1) NOT NULL,
  `narration` varchar(300) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `item_id`, `orderID`, `qty`, `discount`, `has_refund`, `narration`, `created`) VALUES
(1, 1, 1, 2, 0, 1, '', '2022-04-12 00:00:00'),
(2, 1, 1, 10, 0, 0, '', '2022-04-12 00:00:00'),
(3, 1, 1, 15, 0, 0, '', '2022-04-12 00:00:00'),
(4, 1, 1, 10, 0, 0, '', '2022-04-12 00:00:00'),
(5, 1, 1, 20, 0, 0, '', '2022-04-12 00:00:00'),
(6, 1, 1, 8, 0, 0, '', '2022-04-12 00:00:00'),
(7, 1, 1, 30, 0, 0, '', '2022-04-12 00:00:00'),
(8, 1, 1, 5, 0, 0, '', '2022-04-12 00:00:00'),
(9, 1, 2, 1, 0, 0, '', '2022-04-12 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `payment_types`
--

CREATE TABLE `payment_types` (
  `id` int(11) NOT NULL,
  `trx_type_id` int(11) NOT NULL,
  `payment_name` varchar(255) NOT NULL,
  `payment_code_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payment_types`
--

INSERT INTO `payment_types` (`id`, `trx_type_id`, `payment_name`, `payment_code_name`) VALUES
(1, 1, 'Cash', 'cash'),
(2, 1, 'Electronic(Visa, MasterCard, Unionpay)', 'electronic'),
(3, 2, 'Credit', 'credit_sale');

-- --------------------------------------------------------

--
-- Table structure for table `sales_refunds`
--

CREATE TABLE `sales_refunds` (
  `id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `qty` double NOT NULL,
  `narration` varchar(255) NOT NULL,
  `account_posting` tinyint(1) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sales_refunds`
--

INSERT INTO `sales_refunds` (`id`, `order_item_id`, `qty`, `narration`, `account_posting`, `created`) VALUES
(1, 1, 1, 'Sale in excess of 1 plate', 1, '2022-04-15 18:12:11');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `phone_no` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `credit_limit` double NOT NULL,
  `isUsed` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `supplier_name`, `phone_no`, `email`, `credit_limit`, `isUsed`, `created`) VALUES
(1, 'Lukka Plastics', '0356466434565', 'info@lukkaplastics.com', 10000, 1, '2022-04-11 12:51:09'),
(2, 'Blue Band Ltd', '043565566', 'info@bluebandltd.com', 40000, 1, '2022-04-11 12:51:09');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_types`
--

CREATE TABLE `transaction_types` (
  `id` int(11) NOT NULL,
  `trx_name` varchar(255) NOT NULL,
  `trx_code_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction_types`
--

INSERT INTO `transaction_types` (`id`, `trx_name`, `trx_code_name`) VALUES
(1, 'Cash', 'cash'),
(2, 'Credit', 'credit');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_groups`
--
ALTER TABLE `account_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_types`
--
ALTER TABLE `account_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `creditor_transactions`
--
ALTER TABLE `creditor_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `debtor_transactions`
--
ALTER TABLE `debtor_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fiscal_year`
--
ALTER TABLE `fiscal_year`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_category`
--
ALTER TABLE `inventory_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_units`
--
ALTER TABLE `inventory_units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journal`
--
ALTER TABLE `journal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journal_categories`
--
ALTER TABLE `journal_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journal_old`
--
ALTER TABLE `journal_old`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderID`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_types`
--
ALTER TABLE `payment_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_refunds`
--
ALTER TABLE `sales_refunds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_types`
--
ALTER TABLE `transaction_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `account_groups`
--
ALTER TABLE `account_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `account_types`
--
ALTER TABLE `account_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `creditor_transactions`
--
ALTER TABLE `creditor_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `debtor_transactions`
--
ALTER TABLE `debtor_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fiscal_year`
--
ALTER TABLE `fiscal_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory_category`
--
ALTER TABLE `inventory_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_units`
--
ALTER TABLE `inventory_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `journal`
--
ALTER TABLE `journal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=300;

--
-- AUTO_INCREMENT for table `journal_categories`
--
ALTER TABLE `journal_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `journal_old`
--
ALTER TABLE `journal_old`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `orderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payment_types`
--
ALTER TABLE `payment_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales_refunds`
--
ALTER TABLE `sales_refunds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaction_types`
--
ALTER TABLE `transaction_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
