<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'linkkit_BiolinkPr0');
$res = $mysqli->query('SHOW TABLES');
$tables = [];
while ($row = $res->fetch_array()) {
    $tables[] = $row[0];
}
$schema = [];
foreach ($tables as $t) {
    $r = $mysqli->query("SHOW CREATE TABLE `$t`");
    $schema[$t] = $r->fetch_assoc()['Create Table'];
}
file_put_contents('schema_dump.json', json_encode($schema, JSON_PRETTY_PRINT));
echo 'Done';
