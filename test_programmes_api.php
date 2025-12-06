<?php
require_once 'app/config/config.php';
require_once 'app/core/Database.php';
require_once 'app/models/Programme.php';

$programmeModel = new App\Models\Programme();

// Test with station ID 1
$stationId = 1;
$programmes = $programmeModel->getByStation($stationId);

echo "Programmes for Station ID {$stationId}:\n";
echo "====================================\n";

if (empty($programmes)) {
    echo "No programmes found.\n";
} else {
    foreach ($programmes as $p) {
        echo "- ID: {$p->id} - {$p->name}\n";
    }
}

echo "\nJSON Output:\n";
echo json_encode(['programmes' => $programmes], JSON_PRETTY_PRINT);
