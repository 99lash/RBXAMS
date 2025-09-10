CREATE TABLE `user_roles` (
  `id` integer PRIMARY KEY,
  `name` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `users` (
  `id` uuid PRIMARY KEY,
  `user_role_id` integer,
  `username` varchar(255),
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `account_status` (
  `id` integer PRIMARY KEY,
  `name` varchar(255) COMMENT 'sold | pending | unpend | retrieved',
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `accounts` (
  `id` uuid PRIMARY KEY,
  `user_id` uuid,
  `account_type` varchar(20) COMMENT 'pending | fastflip',
  `account_status_id` integer,
  `name` varchar(255),
  `robux` double,
  `cost_php` currency,
  `price_php` currency,
  `profit_php` currency,
  `sold_rate_usd` currency,
  `sold_date` timestamp,
  `is_deleted` boolean,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `daily_transactions` (
  `id` uuid PRIMARY KEY,
  `account_id` uuid,
  `account_type` varchar(20) COMMENT 'pending | fastflip',
  `action` varchar(20) COMMENT 'buy | sell',
  `robux_amount` double,
  `cost_php` currency,
  `profit_php` currency,
  `created_at` timestamp
);

CREATE TABLE `daily_summary` (
  `id` uuid PRIMARY KEY,
  `account_type` varchar(20) COMMENT 'pending | fastflip',
  `total_robux_invested` double,
  `total_robux_sold` double,
  `total_cost_php` currency,
  `total_profit_php` currency,
  `summary_date` date,
  `created_at` timestamp,
  `updated_at` timestamp
);

ALTER TABLE `users` ADD FOREIGN KEY (`user_role_id`) REFERENCES `user_roles` (`id`);

ALTER TABLE `accounts` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `accounts` ADD FOREIGN KEY (`account_status_id`) REFERENCES `account_status` (`id`);

ALTER TABLE `daily_transactions` ADD FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`);
