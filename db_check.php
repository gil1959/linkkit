<?php
require 'config.php';
$db = new mysqli(DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
$res = $db->query('SELECT * FROM payments ORDER BY id DESC LIMIT 5');
while($row = $res->fetch_assoc()) {
    print_r($row);
}
