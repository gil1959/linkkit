<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'linkkit_BiolinkPr0');
$res = $mysqli->query('SELECT COUNT(*) FROM shop_orders');
echo "Count: " . $res->fetch_row()[0];
