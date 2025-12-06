<?php
require_once 'app/config/config.php';
require_once 'app/core/Database.php';

$db = new App\Core\Database();

echo "Clearing remaining tables...\n";

$db->query("DELETE FROM draws");
$db->execute();
echo "✅ Cleared: draws\n";

$db->query("DELETE FROM raffle_campaigns");
$db->execute();
echo "✅ Cleared: raffle_campaigns\n";

echo "✅ Done!\n";
