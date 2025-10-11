<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$dataDir = __DIR__ . '/data';
$scheduleFile = $dataDir . '/schedule.json';
$settingsFile = $dataDir . '/settings.json';

// Ensure data directory exists
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// Initialize files if they don't exist
if (!file_exists($scheduleFile)) {
    file_put_contents($scheduleFile, '{}');
}
if (!file_exists($settingsFile)) {
    $defaultSettings = [
        'ownerName' => '',
        'labelStyle' => 'surname',
        'team' => [
            "Eva Mészáros","Viera Krajníková","Nikola Oslanská","Soňa Žáková","Roman Blažek",
            "Ján Tóth","Ivo Novysedlák","Kristína Jablonská","Zuzana Špalková","Roman Šajbidor",
            "Margaréta Cifrová","Dávid Jablonický","Peter Marko","Michal Michalec","Ľubica Hadbavná"
        ]
    ];
    file_put_contents($settingsFile, json_encode($defaultSettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'getSchedule':
        $data = file_get_contents($scheduleFile);
        echo $data;
        break;
    
    case 'saveSchedule':
        $input = file_get_contents('php://input');
        $decoded = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            file_put_contents($scheduleFile, json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
        }
        break;
    
    case 'getSettings':
        $data = file_get_contents($settingsFile);
        echo $data;
        break;
    
    case 'saveSettings':
        $input = file_get_contents('php://input');
        $decoded = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            file_put_contents($settingsFile, json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
        }
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
