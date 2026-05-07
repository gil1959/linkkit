-- 1. Tabel Shops (Pengaturan Global Toko)
CREATE TABLE `shops` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `name` VARCHAR(256) NOT NULL,
  `description` TEXT,
  `url` VARCHAR(256) NOT NULL, 
  `item_types` TEXT, 
  `cover_image` VARCHAR(256) DEFAULT NULL,
  `logo_image` VARCHAR(256) DEFAULT NULL,
  `is_review_enabled` TINYINT(4) DEFAULT 1,
  `is_active` TINYINT(4) DEFAULT 1,
  `global_webhook_url` VARCHAR(512) DEFAULT NULL,
  `notification_settings` TEXT, -- JSON untuk setting WA, Telegram, Email
  `datetime` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel Listings (Grup Etalase)
CREATE TABLE `shop_listings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `shop_id` INT(11) NOT NULL,
  `name` VARCHAR(256) NOT NULL,
  `description` TEXT,
  `datetime` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel Items (Produk)
CREATE TABLE `shop_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `shop_id` INT(11) NOT NULL,
  `listing_id` INT(11) DEFAULT NULL, 
  `type` VARCHAR(32) DEFAULT 'download_link', -- 'download_link', 'webhook_event', 'random_code', 'manual'
  `download_links` TEXT, -- JSON array of URLs
  `webhook_url` VARCHAR(512) DEFAULT NULL,
  `name` VARCHAR(256) NOT NULL,
  `category` VARCHAR(128) DEFAULT NULL,
  `image` VARCHAR(256) DEFAULT NULL,
  `description` TEXT,
  `is_flexible_amount` TINYINT(4) DEFAULT 0,
  `has_variants` TINYINT(4) DEFAULT 0,
  `price` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `stock` INT(11) DEFAULT NULL, -- NULL = Unlimited
  `qty_per_transaction` INT(11) DEFAULT 0, -- 0 = no limit
  `has_discount` TINYINT(4) DEFAULT 0,
  `discount_price` DECIMAL(10,2) DEFAULT NULL,
  `is_flash_sale` TINYINT(4) DEFAULT 0,
  `flash_sale_end` DATETIME DEFAULT NULL,
  `status` TINYINT(4) DEFAULT 1,
  `sales` INT(11) DEFAULT 0,
  `datetime` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabel Vouchers
CREATE TABLE `shop_vouchers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `shop_id` INT(11) NOT NULL,
  `item_id` INT(11) DEFAULT NULL, -- NULL = Berlaku untuk semua item
  `code` VARCHAR(64) NOT NULL,
  `discount_percentage` INT(11) NOT NULL,
  `quota` INT(11) DEFAULT NULL, 
  `used` INT(11) DEFAULT 0,
  `valid_from` DATETIME DEFAULT NULL,
  `valid_to` DATETIME DEFAULT NULL,
  `is_active` TINYINT(4) DEFAULT 1,
  `datetime` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabel Audience / Customers
CREATE TABLE `shop_customers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `shop_id` INT(11) NOT NULL,
  `email` VARCHAR(256) NOT NULL,
  `full_name` VARCHAR(128) NOT NULL,
  `phone` VARCHAR(32) DEFAULT NULL,
  `total_orders` INT(11) DEFAULT 0,
  `total_spent` DECIMAL(10,2) DEFAULT '0.00',
  `first_purchase` DATETIME DEFAULT NULL,
  `last_purchase` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tabel Transaksi / Orders
CREATE TABLE `shop_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `shop_id` INT(11) NOT NULL,
  `item_id` INT(11) NOT NULL,
  `customer_id` INT(11) NOT NULL,
  `invoice_number` VARCHAR(128) NOT NULL,
  `qty` INT(11) NOT NULL DEFAULT 1,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `service_fee` DECIMAL(10,2) NOT NULL DEFAULT '0.00', -- Fee 5%
  `grand_total` DECIMAL(10,2) NOT NULL,
  `voucher_id` INT(11) DEFAULT NULL,
  `payment_processor` VARCHAR(32) DEFAULT 'tripay',
  `payment_id` VARCHAR(128) DEFAULT NULL, 
  `status` VARCHAR(32) DEFAULT 'pending', -- pending, paid, failed
  `settle_status` VARCHAR(32) DEFAULT 'unsettled', -- unsettled, settled (bisa diwithdraw)
  `paid_date` DATETIME DEFAULT NULL,
  `datetime` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Sistem Saldo, Bank, & Withdrawals
ALTER TABLE `users` ADD `withdrawable_funds` DECIMAL(10,2) DEFAULT '0.00' AFTER `plan_expiration_date`;
ALTER TABLE `users` ADD `pending_funds` DECIMAL(10,2) DEFAULT '0.00' AFTER `withdrawable_funds`;

CREATE TABLE `shop_bank_accounts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `bank_name` VARCHAR(64) NOT NULL,
  `account_number` VARCHAR(64) NOT NULL,
  `account_name` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `shop_withdrawals` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `bank_account_id` INT(11) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `status` VARCHAR(32) DEFAULT 'pending', 
  `datetime` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Reviews
CREATE TABLE `shop_reviews` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `item_id` INT(11) NOT NULL,
  `rating` INT(11) NOT NULL DEFAULT 5,
  `review` TEXT,
  `datetime` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Riwayat Webhook Events
CREATE TABLE `shop_webhook_events` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `shop_id` INT(11) NOT NULL,
  `item_id` INT(11) DEFAULT NULL,
  `webhook_url` VARCHAR(512) NOT NULL,
  `payload` TEXT,
  `status_code` INT(11) DEFAULT NULL,
  `datetime` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
