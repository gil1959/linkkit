<?php
require 'config.php';
$db = new mysqli(DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
$res = $db->query('ALTER TABLE shop_orders ADD discount_amount DECIMAL(10,2) NULL DEFAULT 0 AFTER grand_total');
if($db->error) echo $db->error; else echo "Success";
