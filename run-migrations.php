<?php
/**
 * Simple Migration Runner
 * Run: php run-migrations.php
 */

require_once 'app/config/config.php';
require_once 'app/core/Database.php';

$db = new App\Core\Database();

echo "🚀 Running Migrations...\n\n";

$migrations = [
    'migrations/create_ussd_sessions_table.sql',
    'migrations/create_sms_logs_table.sql',
    'migrations/create_api_tables.sql'
];

foreach ($migrations as $migration) {
    if (!file_exists($migration)) {
        echo "❌ Migration file not found: {$migration}\n";
        continue;
    }
    
    echo "📝 Running: {$migration}\n";
    
    $sql = file_get_contents($migration);
    
    // Split by semicolon to handle multiple statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        try {
            $db->query($statement);
            $db->execute();
        } catch (Exception $e) {
            echo "   ⚠️  Warning: " . $e->getMessage() . "\n";
        }
    }
    
    echo "   ✅ Completed\n\n";
}

echo "🎉 All migrations completed!\n";
