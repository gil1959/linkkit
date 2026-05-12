<?php
$pdo = new PDO('mysql:host=localhost;dbname=linkkit_BiolinkPr0', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$migrations = [
    // shop_items
    "ALTER TABLE `shop_items` ADD COLUMN `weight` DECIMAL(8,2) DEFAULT NULL",
    "ALTER TABLE `shop_items` ADD COLUMN `length` INT DEFAULT NULL",
    "ALTER TABLE `shop_items` ADD COLUMN `width` INT DEFAULT NULL",
    "ALTER TABLE `shop_items` ADD COLUMN `height` INT DEFAULT NULL",
    // shop_orders
    "ALTER TABLE `shop_orders` ADD COLUMN `shipping_address` TEXT DEFAULT NULL",
    "ALTER TABLE `shop_orders` ADD COLUMN `shipping_courier` VARCHAR(32) DEFAULT NULL",
    "ALTER TABLE `shop_orders` ADD COLUMN `shipping_service` VARCHAR(32) DEFAULT NULL",
    "ALTER TABLE `shop_orders` ADD COLUMN `shipping_cost` DECIMAL(10,2) DEFAULT 0",
    "ALTER TABLE `shop_orders` ADD COLUMN `tracking_number` VARCHAR(128) DEFAULT NULL",
    "ALTER TABLE `shop_orders` ADD COLUMN `shipping_status` VARCHAR(32) DEFAULT NULL",
    "ALTER TABLE `shop_orders` ADD COLUMN `checkout_url` VARCHAR(512) DEFAULT NULL",
    // shops
    "ALTER TABLE `shops` ADD COLUMN `origin_city_id` INT DEFAULT NULL",
    "ALTER TABLE `shops` ADD COLUMN `origin_city_name` VARCHAR(128) DEFAULT NULL",
    "ALTER TABLE `shops` ADD COLUMN `origin_province` VARCHAR(128) DEFAULT NULL",
    // users
    "ALTER TABLE `users` ADD COLUMN `verification_status` VARCHAR(32) DEFAULT 'unverified'",
    // shop_verifications
    "CREATE TABLE IF NOT EXISTS `shop_verifications` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `user_id` INT NOT NULL,
        `full_name` VARCHAR(128) NOT NULL,
        `nik` VARCHAR(16) NOT NULL,
        `ktp_image` VARCHAR(256) NOT NULL,
        `selfie_image` VARCHAR(256) NOT NULL,
        `status` VARCHAR(32) DEFAULT 'pending',
        `rejection_reason` TEXT DEFAULT NULL,
        `submitted_at` DATETIME NOT NULL,
        `reviewed_at` DATETIME DEFAULT NULL,
        `reviewed_by` INT DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

$errors = [];
$success = 0;

foreach ($migrations as $sql) {
    $colName = '';
    if (preg_match('/ADD COLUMN `(\w+)`/', $sql, $m)) $colName = $m[1];
    try {
        $pdo->exec($sql);
        echo "[OK] " . ($colName ?: (strpos($sql, 'CREATE TABLE') !== false ? 'CREATE TABLE shop_verifications' : 'Query')) . "\n";
        $success++;
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false || strpos($e->getMessage(), 'already exists') !== false) {
            echo "[SKIP] $colName sudah ada\n";
        } else {
            echo "[ERROR] " . $e->getMessage() . "\n";
            $errors[] = $e->getMessage();
        }
    }
}

echo "\n=== SELESAI: $success sukses, " . count($errors) . " error ===\n";

// Verifikasi
$cols = $pdo->query('SHOW COLUMNS FROM shop_items')->fetchAll(PDO::FETCH_COLUMN);
echo "shop_items (weight ada): " . (in_array('weight', $cols) ? 'YA' : 'TIDAK') . "\n";
$cols2 = $pdo->query('SHOW COLUMNS FROM shop_orders')->fetchAll(PDO::FETCH_COLUMN);
echo "shop_orders (checkout_url ada): " . (in_array('checkout_url', $cols2) ? 'YA' : 'TIDAK') . "\n";
$cols3 = $pdo->query('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_COLUMN);
echo "users (verification_status ada): " . (in_array('verification_status', $cols3) ? 'YA' : 'TIDAK') . "\n";
$t = $pdo->query("SHOW TABLES LIKE 'shop_verifications'")->fetchAll(PDO::FETCH_COLUMN);
echo "shop_verifications table: " . (count($t) > 0 ? 'ADA' : 'TIDAK ADA') . "\n";
