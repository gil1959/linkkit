<?php
$m = new mysqli('127.0.0.1','root','','linkkit_BiolinkPr0');
$r = $m->query('SELECT * FROM shop_customers WHERE id IN (1, 10)');
while($row = $r->fetch_assoc()) print_r($row);
