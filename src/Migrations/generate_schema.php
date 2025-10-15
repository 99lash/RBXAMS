<?php
// Using a HEREDOC (<<<SQL) is a clean way to define a long string in PHP.
$sql_schema = <<<SQL
-- Roblox Asset Monitoring System - Database Schema
-- Generated on: {date('Y-m-d H:i:s')}

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for user_roles
-- ----------------------------
DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `user_role_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_role_id` (`user_role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for account_status
-- ----------------------------
DROP TABLE IF EXISTS `account_status`;
CREATE TABLE `account_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT 'sold | pending | unpend | retrieved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for accounts
-- ----------------------------
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` bigint(20) NOT NULL,
  `user_id` char(36) NOT NULL,
  `account_type` varchar(20) DEFAULT NULL COMMENT 'pending | fastflip',
  `account_status_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `cookie_enc` text NOT NULL COMMENT 'encrypted .ROBLOSECURITY cookie',
  `robux` decimal(12,2) DEFAULT NULL,
  `cost_php` decimal(12,2) DEFAULT NULL,
  `price_php` decimal(12,2) DEFAULT NULL,
  `usd_to_php_rate_on_sale` decimal(10,4) DEFAULT NULL,
  `sold_rate_usd` decimal(12,2) DEFAULT NULL,
  `unpend_date` timestamp NULL DEFAULT NULL,
  `sold_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `account_status_id` (`account_status_id`),
  CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `accounts_ibfk_2` FOREIGN KEY (`account_status_id`) REFERENCES `account_status` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for daily_transactions
-- ----------------------------
DROP TABLE IF EXISTS `daily_transactions`;
CREATE TABLE `daily_transactions` (
  `id` char(36) NOT NULL,
  `account_id` bigint(20) DEFAULT NULL,
  `action` varchar(20) DEFAULT NULL COMMENT 'buy | sell',
  `robux_amount` decimal(12,2) DEFAULT NULL,
  `php_amount` decimal(12,2) DEFAULT NULL COMMENT 'Represents cost_php on "buy" and price_php on "sell"',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  CONSTRAINT `daily_transactions_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for daily_summary
-- ----------------------------
DROP TABLE IF EXISTS `daily_summary`;
CREATE TABLE `daily_summary` (
  `summary_date` date NOT NULL,
  `pending_robux_bought` decimal(12,2) DEFAULT 0.00,
  `fastflip_robux_bought` decimal(12,2) DEFAULT 0.00,
  `pending_robux_sold` decimal(12,2) DEFAULT 0.00,
  `fastflip_robux_sold` decimal(12,2) DEFAULT 0.00,
  `pending_expenses_php` decimal(12,2) DEFAULT 0.00,
  `fastflip_expenses_php` decimal(12,2) DEFAULT 0.00,
  `pending_profit_php` decimal(12,2) DEFAULT 0.00,
  `fastflip_profit_php` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`summary_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
SQL;

// Echo the final SQL string
echo $sql_schema;

// Stop the script to prevent any other output
exit();
?>