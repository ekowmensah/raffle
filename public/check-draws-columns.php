<?php
require_once '../app/config/config.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
echo "Draws table columns:\n";
$result = $db->query('DESCRIBE draws');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . "\n";
}
