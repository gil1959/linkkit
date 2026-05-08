-- Jalankan di phpMyAdmin atau cPanel MySQL
-- Tambah kolom checkout_url ke shop_orders

ALTER TABLE `shop_orders` 
ADD COLUMN `checkout_url` TEXT NULL DEFAULT NULL AFTER `payment_id`;
