<?php
require_once 'app/config/config.php';
require_once 'app/core/Database.php';

$db = new App\Core\Database();

$db->query('SELECT id, name, status FROM raffle_campaigns ORDER BY id');
$campaigns = $db->resultSet();

echo "Existing campaigns:\n";
echo "==================\n";

if (empty($campaigns)) {
    echo "❌ No campaigns found.\n";
    echo "You need to create a new campaign first.\n";
} else {
    foreach($campaigns as $c) {
        echo "- ID: {$c->id} - {$c->name} (Status: {$c->status})\n";
    }
}
