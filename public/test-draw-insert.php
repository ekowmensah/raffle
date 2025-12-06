<?php
/**
 * Test draw insertion with winner_count
 */

require_once '../app/config/config.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<h1>Test Draw Insertion</h1>";
    
    // First, verify the column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM draws LIKE 'winner_count'");
    $columnExists = $stmt->rowCount() > 0;
    
    echo "<p><strong>winner_count column exists:</strong> " . ($columnExists ? "✅ YES" : "❌ NO") . "</p>";
    
    if (!$columnExists) {
        echo "<p style='color: red;'>ERROR: Column does not exist! Run migration first.</p>";
        exit;
    }
    
    // Test data
    $testData = [
        'campaign_id' => 1,
        'station_id' => 1,
        'programme_id' => null,
        'draw_type' => 'daily',
        'draw_date' => date('Y-m-d'),
        'status' => 'pending',
        'started_by_user_id' => 1,
        'total_prize_pool' => 100.00,
        'winner_count' => 3
    ];
    
    echo "<h2>Test Data:</h2>";
    echo "<pre>";
    print_r($testData);
    echo "</pre>";
    
    // Build SQL
    $fields = implode(', ', array_keys($testData));
    $placeholders = ':' . implode(', :', array_keys($testData));
    $sql = "INSERT INTO draws ({$fields}) VALUES ({$placeholders})";
    
    echo "<h2>SQL Query:</h2>";
    echo "<pre>" . htmlspecialchars($sql) . "</pre>";
    
    // Try to insert
    echo "<h2>Attempting Insert...</h2>";
    
    try {
        $stmt = $pdo->prepare($sql);
        
        foreach ($testData as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        $stmt->execute();
        $insertId = $pdo->lastInsertId();
        
        echo "<p style='color: green; font-size: 18px;'>✅ <strong>SUCCESS!</strong></p>";
        echo "<p>Draw inserted with ID: <strong>{$insertId}</strong></p>";
        
        // Retrieve the inserted row
        $stmt = $pdo->prepare("SELECT * FROM draws WHERE id = :id");
        $stmt->bindValue(':id', $insertId);
        $stmt->execute();
        $draw = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h3>Inserted Draw:</h3>";
        echo "<pre>";
        print_r($draw);
        echo "</pre>";
        
        // Clean up - delete the test draw
        echo "<h3>Cleanup:</h3>";
        $stmt = $pdo->prepare("DELETE FROM draws WHERE id = :id");
        $stmt->bindValue(':id', $insertId);
        $stmt->execute();
        echo "<p>✅ Test draw deleted (ID: {$insertId})</p>";
        
    } catch (PDOException $e) {
        echo "<p style='color: red; font-size: 18px;'>❌ <strong>INSERT FAILED!</strong></p>";
        echo "<pre style='background: #ffe6e6; padding: 15px; border: 1px solid #ff0000;'>";
        echo "Error: " . htmlspecialchars($e->getMessage());
        echo "\n\nSQL State: " . $e->getCode();
        echo "</pre>";
        
        echo "<h3>Troubleshooting:</h3>";
        echo "<ul>";
        echo "<li>Run: <code>php run-winner-count-migration.php</code></li>";
        echo "<li>Restart Apache and MySQL</li>";
        echo "<li>Check database name in config.php</li>";
        echo "</ul>";
    }
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Database Connection Error:</h2>";
    echo "<pre style='background: #ffe6e6; padding: 15px; border: 1px solid #ff0000;'>";
    echo htmlspecialchars($e->getMessage());
    echo "</pre>";
}
?>
