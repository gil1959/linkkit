-- ============================================================
-- MIGRATION: Physical Products + RajaOngkir + KTP Verification
-- ============================================================

-- 1. Produk Fisik: tambah kolom berat & dimensi ke shop_items
ALTER TABLE `shop_items`
  ADD COLUMN IF NOT EXISTS `weight` DECIMAL(8,2) DEFAULT NULL AFTER `has_variants`,
  ADD COLUMN IF NOT EXISTS `length` INT(11) DEFAULT NULL AFTER `weight`,
  ADD COLUMN IF NOT EXISTS `width`  INT(11) DEFAULT NULL AFTER `length`,
  ADD COLUMN IF NOT EXISTS `height` INT(11) DEFAULT NULL AFTER `width`;

-- 2. Pengiriman: tambah kolom ke shop_orders
ALTER TABLE `shop_orders`
  ADD COLUMN IF NOT EXISTS `shipping_address`  TEXT DEFAULT NULL AFTER `voucher_id`,
  ADD COLUMN IF NOT EXISTS `shipping_courier`  VARCHAR(32) DEFAULT NULL AFTER `shipping_address`,
  ADD COLUMN IF NOT EXISTS `shipping_service`  VARCHAR(32) DEFAULT NULL AFTER `shipping_courier`,
  ADD COLUMN IF NOT EXISTS `shipping_cost`     DECIMAL(10,2) DEFAULT 0 AFTER `shipping_service`,
  ADD COLUMN IF NOT EXISTS `tracking_number`   VARCHAR(128) DEFAULT NULL AFTER `shipping_cost`,
  ADD COLUMN IF NOT EXISTS `shipping_status`   VARCHAR(32) DEFAULT NULL AFTER `tracking_number`,
  ADD COLUMN IF NOT EXISTS `checkout_url`      VARCHAR(512) DEFAULT NULL AFTER `payment_id`;

-- 3. Kota asal toko: tambah kolom ke shops
ALTER TABLE `shops`
  ADD COLUMN IF NOT EXISTS `origin_city_id`   INT(11) DEFAULT NULL AFTER `global_webhook_url`,
  ADD COLUMN IF NOT EXISTS `origin_city_name` VARCHAR(128) DEFAULT NULL AFTER `origin_city_id`,
  ADD COLUMN IF NOT EXISTS `origin_province`  VARCHAR(128) DEFAULT NULL AFTER `origin_city_name`;

-- 4. Status verifikasi seller: tambah ke users
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `verification_status` VARCHAR(32) DEFAULT 'unverified' AFTER `withdrawable_funds`;

-- 5. Tabel verifikasi KTP + selfie
CREATE TABLE IF NOT EXISTS `shop_verifications` (
  `id`               INT(11) NOT NULL AUTO_INCREMENT,
  `user_id`          INT(11) NOT NULL,
  `full_name`        VARCHAR(128) NOT NULL,
  `nik`              VARCHAR(16) NOT NULL,
  `ktp_image`        VARCHAR(256) NOT NULL,
  `selfie_image`     VARCHAR(256) NOT NULL,
  `status`           VARCHAR(32) DEFAULT 'pending',
  `rejection_reason` TEXT DEFAULT NULL,
  `submitted_at`     DATETIME NOT NULL,
  `reviewed_at`      DATETIME DEFAULT NULL,
  `reviewed_by`      INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
