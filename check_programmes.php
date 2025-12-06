<?php
require_once 'app/config/config.php';
require_once 'app/core/Database.php';

$db = new App\Core\Database();

$db->query('SELECT p.id, p.name, s.name as station_name 
           FROM programmes p 
           INNER JOIN stations s ON p.station_id = s.id 
           ORDER BY s.name, p.name');
$programmes = $db->resultSet();

echo "Existing programmes:\n";
echo "===================\n";

if (empty($programmes)) {
    echo "❌ No programmes found.\n";
    echo "You need to create stations and programmes first.\n";
} else {
    foreach($programmes as $p) {
        echo "- ID: {$p->id} - {$p->name} (Station: {$p->station_name})\n";
    }
}
