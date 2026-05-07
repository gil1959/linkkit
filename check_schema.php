<?php
define('ALTUMCODE', 1);
require_once __DIR__ . '/app/init.php';
$r = database()->query('DESCRIBE shop_orders');
while($row = $r->fetch_assoc()){
    echo $row['Field'] . ': ' . $row['Type'] . "\n";
}
