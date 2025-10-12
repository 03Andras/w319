<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$dataDir = __DIR__ . '/data';
$settingsFile = $dataDir . '/settings.json';
$auditLogFile = $dataDir . '/audit_log.json';

// Ensure data directory exists
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// Initialize settings file if it doesn't exist
if (!file_exists($settingsFile)) {
    $defaultSettings = [
        'ownerName' => '',
        'labelStyle' => 'surname',
        'adminUser' => '',
        'team' => [
            "Eva Mészáros","Viera Krajníková","Nikola Oslanská","Soňa Žáková","Roman Blažek",
            "Ján Tóth","Ivo Novysedlák","Kristína Jablonská","Zuzana Špalková","Roman Šajbidor",
            "Margaréta Cifrová","Dávid Jablonický","Peter Marko","Michal Michalec","Ľubica Hadbavná"
        ]
    ];
    file_put_contents($settingsFile, json_encode($defaultSettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Initialize audit log if it doesn't exist
if (!file_exists($auditLogFile)) {
    file_put_contents($auditLogFile, '[]');
}

// Helper function to get schedule file for a specific month
function getScheduleFile($yearMonth) {
    global $dataDir;
    return $dataDir . '/schedule_' . $yearMonth . '.json';
}

// Helper function to add audit log entry
function addAuditLog($user, $action, $details = []) {
    global $auditLogFile;
    $logs = json_decode(file_get_contents($auditLogFile), true) ?: [];
    $logs[] = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user' => $user,
        'action' => $action,
        'details' => $details
    ];
    file_put_contents($auditLogFile, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'getSchedule':
        $yearMonth = $_GET['yearMonth'] ?? '';
        if (empty($yearMonth)) {
            http_response_code(400);
            echo json_encode(['error' => 'yearMonth parameter required']);
            break;
        }
        $scheduleFile = getScheduleFile($yearMonth);
        if (!file_exists($scheduleFile)) {
            echo '{}';
        } else {
            $data = file_get_contents($scheduleFile);
            echo $data;
        }
        break;
    
    case 'saveSchedule':
        $input = file_get_contents('php://input');
        $decoded = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $yearMonth = $decoded['yearMonth'] ?? '';
            $scheduleData = $decoded['data'] ?? [];
            $user = $decoded['user'] ?? 'Unknown';
            $changeDetails = $decoded['changeDetails'] ?? [];
            
            if (empty($yearMonth)) {
                http_response_code(400);
                echo json_encode(['error' => 'yearMonth required']);
                break;
            }
            
            // Load old schedule to compare
            $scheduleFile = getScheduleFile($yearMonth);
            $oldSchedule = [];
            if (file_exists($scheduleFile)) {
                $oldSchedule = json_decode(file_get_contents($scheduleFile), true) ?: [];
            }
            
            // Save new schedule
            file_put_contents($scheduleFile, json_encode($scheduleData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Add detailed audit log
            $logDetails = array_merge($changeDetails, ['yearMonth' => $yearMonth]);
            addAuditLog($user, 'schedule_update', $logDetails);
            
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
            $user = $decoded['user'] ?? 'Unknown';
            unset($decoded['user']); // Remove user from settings data
            
            file_put_contents($settingsFile, json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Add audit log
            addAuditLog($user, 'settings_update', ['changes' => 'Settings modified']);
            
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
        }
        break;
    
    case 'getAuditLog':
        $data = file_get_contents($auditLogFile);
        echo $data;
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
