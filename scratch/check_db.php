<?php
$pdo = new PDO('mysql:host=localhost;dbname=linkkit_BiolinkPr0', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "DB Connected OK\n";

$cols = $pdo->query('SHOW COLUMNS FROM shop_items')->fetchAll(PDO::FETCH_COLUMN);
echo "shop_items cols: " . implode(', ', $cols) . "\n";

$cols2 = $pdo->query('SHOW COLUMNS FROM shop_orders')->fetchAll(PDO::FETCH_COLUMN);
echo "shop_orders cols: " . implode(', ', $cols2) . "\n";

$cols3 = $pdo->query('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_COLUMN);
echo "users has verification_status: " . (in_array('verification_status', $cols3) ? 'YA' : 'TIDAK') . "\n";

$tables = $pdo->query("SHOW TABLES LIKE 'shop_verifications'")->fetchAll(PDO::FETCH_COLUMN);
echo "shop_verifications table: " . (count($tables) > 0 ? 'ADA' : 'TIDAK ADA') . "\n";

$tables2 = $pdo->query("SHOW TABLES LIKE 'shop_physical_orders'")->fetchAll(PDO::FETCH_COLUMN);
echo "shop_physical_orders table: " . (count($tables2) > 0 ? 'ADA' : 'TIDAK ADA') . "\n";
