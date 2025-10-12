<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$dataDir = __DIR__ . '/data';
$settingsFile = $dataDir . '/settings.json';
$auditLogFile = $dataDir . '/audit_log.json';
$sessionsFile = $dataDir . '/sessions.json';

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
        'adminUsers' => [],
        'adminPassword' => 'Jablko123',
        'connectedUsers' => [],
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

// Initialize sessions file if it doesn't exist
if (!file_exists($sessionsFile)) {
    file_put_contents($sessionsFile, '[]');
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

// Helper function to get client IP address
function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// Helper function to register a session
function registerSession($userName, $sessionId) {
    global $sessionsFile;
    $sessions = json_decode(file_get_contents($sessionsFile), true) ?: [];
    
    $sessions[] = [
        'sessionId' => $sessionId,
        'userName' => $userName,
        'ipAddress' => getClientIP(),
        'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        'loginTime' => date('Y-m-d H:i:s'),
        'lastActivity' => date('Y-m-d H:i:s'),
        'active' => true
    ];
    
    file_put_contents($sessionsFile, json_encode($sessions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Log to audit
    addAuditLog($userName, 'user_login', ['sessionId' => $sessionId, 'ipAddress' => getClientIP()]);
}

// Helper function to check if session is disconnected
function isSessionDisconnected($sessionId) {
    global $sessionsFile;
    $sessions = json_decode(file_get_contents($sessionsFile), true) ?: [];
    
    foreach ($sessions as $session) {
        if ($session['sessionId'] === $sessionId && isset($session['active']) && !$session['active']) {
            return true;
        }
    }
    return false;
}

// Helper function to update session activity
function updateSessionActivity($sessionId) {
    global $sessionsFile;
    $sessions = json_decode(file_get_contents($sessionsFile), true) ?: [];
    
    foreach ($sessions as &$session) {
        if ($session['sessionId'] === $sessionId) {
            $session['lastActivity'] = date('Y-m-d H:i:s');
            break;
        }
    }
    
    file_put_contents($sessionsFile, json_encode($sessions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
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
    
    case 'disconnectUser':
        $input = file_get_contents('php://input');
        $decoded = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $userToDisconnect = $decoded['userToDisconnect'] ?? '';
            $adminUser = $decoded['adminUser'] ?? 'Unknown';
            
            if (empty($userToDisconnect)) {
                http_response_code(400);
                echo json_encode(['error' => 'userToDisconnect required']);
                break;
            }
            
            // Disconnect all sessions for this user
            global $sessionsFile;
            $sessions = json_decode(file_get_contents($sessionsFile), true) ?: [];
            $disconnectedCount = 0;
            
            foreach ($sessions as &$session) {
                if ($session['userName'] === $userToDisconnect && ($session['active'] ?? true)) {
                    $session['active'] = false;
                    $session['disconnectedBy'] = $adminUser;
                    $session['disconnectedAt'] = date('Y-m-d H:i:s');
                    $disconnectedCount++;
                }
            }
            
            file_put_contents($sessionsFile, json_encode($sessions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Add to audit log
            addAuditLog($adminUser, 'user_disconnect', [
                'disconnectedUser' => $userToDisconnect,
                'sessionsDisconnected' => $disconnectedCount
            ]);
            
            echo json_encode(['success' => true, 'sessionsDisconnected' => $disconnectedCount]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
        }
        break;
    
    case 'registerSession':
        $input = file_get_contents('php://input');
        $decoded = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $userName = $decoded['userName'] ?? '';
            $sessionId = $decoded['sessionId'] ?? '';
            
            if (empty($userName) || empty($sessionId)) {
                http_response_code(400);
                echo json_encode(['error' => 'userName and sessionId required']);
                break;
            }
            
            registerSession($userName, $sessionId);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
        }
        break;
    
    case 'checkSession':
        $sessionId = $_GET['sessionId'] ?? '';
        if (empty($sessionId)) {
            http_response_code(400);
            echo json_encode(['error' => 'sessionId required']);
            break;
        }
        
        $isDisconnected = isSessionDisconnected($sessionId);
        if (!$isDisconnected) {
            updateSessionActivity($sessionId);
        }
        
        echo json_encode(['active' => !$isDisconnected]);
        break;
    
    case 'getSessions':
        global $sessionsFile;
        $sessions = json_decode(file_get_contents($sessionsFile), true) ?: [];
        
        // Filter to show only active sessions or recent ones (last 7 days)
        $cutoffDate = date('Y-m-d H:i:s', strtotime('-7 days'));
        $filteredSessions = array_filter($sessions, function($session) use ($cutoffDate) {
            return ($session['active'] ?? true) || ($session['lastActivity'] ?? '2000-01-01') > $cutoffDate;
        });
        
        echo json_encode(array_values($filteredSessions));
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
