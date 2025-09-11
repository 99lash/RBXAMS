CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(20),
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `users` (
  `id` CHAR(36) PRIMARY KEY,
  `user_role_id` INT,
  `username` VARCHAR(255),
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_role_id`) REFERENCES `user_roles` (`id`)
);

CREATE TABLE IF NOT EXISTS `account_status` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(50) COMMENT 'sold | pending | unpend | retrieved',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` CHAR(36) PRIMARY KEY,
  `user_id` CHAR(36),
  `account_type` VARCHAR(20) COMMENT 'pending | fastflip',
  `account_status_id` INT,
  `name` VARCHAR(255),
  `robux` DECIMAL(12,2),
  `cost_php` DECIMAL(12,2),
  `price_php` DECIMAL(12,2),
  `profit_php` DECIMAL(12,2),
  `sold_rate_usd` DECIMAL(12,2),
  `sold_date` TIMESTAMP,
  `is_deleted` BOOLEAN,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  FOREIGN KEY (`account_status_id`) REFERENCES `account_status` (`id`)
);

CREATE TABLE IF NOT EXISTS `daily_transactions` (
  `id` CHAR(36) PRIMARY KEY,
  `account_id` CHAR(36),
  `account_type` VARCHAR(20) COMMENT 'pending | fastflip',
  `action` VARCHAR(20) COMMENT 'buy | sell',
  `robux_amount` DECIMAL(12,2),
  `cost_php` DECIMAL(12,2),
  `profit_php` DECIMAL(12,2),
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`)
);

CREATE TABLE IF NOT EXISTS `daily_summary` (
  `id` CHAR(36) PRIMARY KEY,
  `account_type` VARCHAR(20) COMMENT 'pending | fastflip',
  `total_robux_invested` DECIMAL(12,2),
  `total_robux_sold` DECIMAL(12,2),
  `total_cost_php` DECIMAL(12,2),
  `total_profit_php` DECIMAL(12,2),
  `summary_date` DATE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);