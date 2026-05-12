<?php
$m = new mysqli('127.0.0.1','root','','linkkit_BiolinkPr0');
$r = $m->query('SELECT id, total_amount, grand_total, service_fee, status, settle_status FROM shop_orders');
while($row = $r->fetch_assoc()) print_r($row);
