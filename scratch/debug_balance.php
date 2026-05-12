<?php
$m = new mysqli('127.0.0.1','root','','linkkit_BiolinkPr0');
if($m->connect_error) die("Connect error: ".$m->connect_error);

// Check users fund columns
$r = $m->query('SHOW COLUMNS FROM users WHERE Field IN ("withdrawable_funds","pending_funds","verification_status")');
echo "=== USERS COLUMNS ===\n";
while($row = $r->fetch_assoc()) print_r($row);

// Check actual values
$r2 = $m->query('SELECT user_id, name, withdrawable_funds, pending_funds, verification_status FROM users WHERE user_id > 1 LIMIT 10');
echo "\n=== USER BALANCES ===\n";
while($row = $r2->fetch_assoc()) print_r($row);

// Check shop_orders settle_status
$r3 = $m->query('SELECT id, invoice_number, grand_total, status, settle_status FROM shop_orders ORDER BY id DESC LIMIT 5');
echo "\n=== RECENT ORDERS ===\n";
while($row = $r3->fetch_assoc()) print_r($row);
