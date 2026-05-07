<?php
/*
 * Migration: Add missing columns for shop features
 * Run once: php migrate_shop_features.php
 */

require_once __DIR__ . '/app/init.php';

$db = database();
$ok = [];
$err = [];

function try_query($db, $sql, $label) {
    global $ok, $err;
    if($db->query($sql)) {
        $ok[] = "OK: $label";
    } else {
        // Ignore "duplicate column" errors (1060)
        if(strpos($db->error, '1060') !== false || strpos($db->error, 'Duplicate column') !== false) {
            $ok[] = "SKIP (already exists): $label";
        } else {
            $err[] = "FAIL: $label — {$db->error}";
        }
    }
}

// shop_orders: add fulfilled_content
try_query($db, "ALTER TABLE `shop_orders` ADD COLUMN `fulfilled_content` TEXT DEFAULT NULL AFTER `payment_id`", "shop_orders.fulfilled_content");

// shop_orders: add proof_image
try_query($db, "ALTER TABLE `shop_orders` ADD COLUMN `proof_image` VARCHAR(256) DEFAULT NULL AFTER `fulfilled_content`", "shop_orders.proof_image");

// shop_vouchers: add is_unlimited
try_query($db, "ALTER TABLE `shop_vouchers` ADD COLUMN `is_unlimited` TINYINT(4) NOT NULL DEFAULT 0 AFTER `quota`", "shop_vouchers.is_unlimited");

// shop_reviews: make sure order_id column exists
try_query($db, "ALTER TABLE `shop_reviews` ADD COLUMN `order_id` INT(11) NOT NULL DEFAULT 0 AFTER `id`", "shop_reviews.order_id");

// shop_customers: add last_purchase tracking columns if missing
try_query($db, "ALTER TABLE `shop_customers` ADD COLUMN `first_purchase` DATETIME DEFAULT NULL", "shop_customers.first_purchase");
try_query($db, "ALTER TABLE `shop_customers` ADD COLUMN `last_purchase` DATETIME DEFAULT NULL", "shop_customers.last_purchase");

echo "=== Migration Results ===\n";
foreach($ok  as $m) echo "✓ $m\n";
foreach($err as $m) echo "✗ $m\n";
echo "\nDone.\n";
