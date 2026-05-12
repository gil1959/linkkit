<?php
$m = new mysqli('127.0.0.1','root','','linkkit_BiolinkPr0');

// Cek seller dari order yang settled
$r = $m->query("SELECT o.id, o.grand_total, o.service_fee, o.settle_status, s.user_id as seller_id, u.name as seller_name
    FROM shop_orders o
    JOIN shops s ON o.shop_id = s.id
    JOIN users u ON s.user_id = u.user_id
    WHERE o.id IN (8,9)");
while($row = $r->fetch_assoc()) print_r($row);

// Check saldo seller langsung
$r2 = $m->query("SELECT user_id, name, withdrawable_funds FROM users WHERE user_id IN (SELECT DISTINCT s.user_id FROM shops s JOIN shop_orders o ON o.shop_id=s.id WHERE o.id IN (8,9))");
echo "\n=== SELLER BALANCE ===\n";
while($row = $r2->fetch_assoc()) print_r($row);
