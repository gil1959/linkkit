<?php
$m = new mysqli('127.0.0.1','root','','linkkit_BiolinkPr0');
if($m->connect_error) { die("Connect error: ".$m->connect_error); }
$r = $m->query('SHOW CREATE TABLE shop_orders');
if(!$r) { die("Error: ".$m->error); }
$row = $r->fetch_assoc();
echo $row['Create Table'];
