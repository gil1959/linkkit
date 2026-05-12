<?php
$schema = json_decode(file_get_contents('schema_dump.json'), true);
foreach($schema as $table => $create) {
    if (strpos($table, 'shop') !== false) {
        echo "TABLE: $table\n";
        echo $create . "\n\n";
    }
}
