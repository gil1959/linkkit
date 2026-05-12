<?php
$mysqli = new mysqli('127.0.0.1', 'root', '');
$res = $mysqli->query('SHOW DATABASES');
while ($row = $res->fetch_array()) {
    echo $row[0] . "\n";
}
