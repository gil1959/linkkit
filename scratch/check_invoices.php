<?php
$m = new mysqli('127.0.0.1','root','','linkkit_BiolinkPr0');
$r = $m->query('SELECT id, invoice_number, item_id, customer_id, status FROM shop_orders WHERE id IN (7, 8, 9)');
while($row = $r->fetch_assoc()) print_r($row);
