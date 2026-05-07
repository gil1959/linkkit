<?php
require_once __DIR__ . '/app/init.php';
$db = database();
$queries = [
    "ALTER TABLE `shop_orders` ADD COLUMN `fulfilled_content` TEXT DEFAULT NULL AFTER `paid_date`",
    "ALTER TABLE `shop_orders` ADD COLUMN `proof_image` VARCHAR(256) DEFAULT NULL AFTER `fulfilled_content`",
];
foreach($queries as $q){
    $result = $db->query($q);
    if($result === false){
        // Check if column already exists (error 1060)
        if(strpos($db->error, 'Duplicate column') !== false){
            echo "SKIP (already exists): $q\n";
        } else {
            echo "ERR [{$db->errno}]: {$db->error}\n  >> $q\n";
        }
    } else {
        echo "OK: $q\n";
    }
}
echo "Done\n";
