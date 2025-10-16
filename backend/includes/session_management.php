<?php
/**
 * Session management functions
 * Handles user session tracking and validation
 */

/**
 * Get client IP address
 */
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

/**
 * Register a new session
 */
function registerSession($userName, $sessionId) {
    $sessions = readJsonFile(SESSIONS_FILE, []);
    
    $sessions[] = [
        'sessionId' => $sessionId,
        'userName' => $userName,
        'ipAddress' => getClientIP(),
        'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        'loginTime' => date('Y-m-d H:i:s'),
        'lastActivity' => date('Y-m-d H:i:s'),
        'active' => true
    ];
    
    writeJsonFile(SESSIONS_FILE, $sessions);
    
    // Log to audit
    addAuditLog($userName, 'user_login', ['sessionId' => $sessionId, 'ipAddress' => getClientIP()]);
}

/**
 * Check if session is disconnected
 */
function isSessionDisconnected($sessionId) {
    $sessions = readJsonFile(SESSIONS_FILE, []);
    
    foreach ($sessions as $session) {
        if ($session['sessionId'] === $sessionId && isset($session['active']) && !$session['active']) {
            return true;
        }
    }
    return false;
}

/**
 * Update session activity timestamp
 */
function updateSessionActivity($sessionId) {
    $sessions = readJsonFile(SESSIONS_FILE, []);
    
    foreach ($sessions as &$session) {
        if ($session['sessionId'] === $sessionId) {
            $session['lastActivity'] = date('Y-m-d H:i:s');
            break;
        }
    }
    
    writeJsonFile(SESSIONS_FILE, $sessions);
}

/**
 * Disconnect all sessions for a user
 */
function disconnectUserSessions($userToDisconnect, $adminUser) {
    $sessions = readJsonFile(SESSIONS_FILE, []);
    $disconnectedCount = 0;
    
    foreach ($sessions as &$session) {
        if ($session['userName'] === $userToDisconnect && ($session['active'] ?? true)) {
            $session['active'] = false;
            $session['disconnectedBy'] = $adminUser;
            $session['disconnectedAt'] = date('Y-m-d H:i:s');
            $disconnectedCount++;
        }
    }
    
    writeJsonFile(SESSIONS_FILE, $sessions);
    
    // Add to audit log
    addAuditLog($adminUser, 'user_disconnect', [
        'disconnectedUser' => $userToDisconnect,
        'sessionsDisconnected' => $disconnectedCount
    ]);
    
    return $disconnectedCount;
}

/**
 * Get all sessions (filtered)
 */
function getSessions() {
    $sessions = readJsonFile(SESSIONS_FILE, []);
    
    // Filter to show only active sessions or recent ones (last 7 days)
    $cutoffDate = date('Y-m-d H:i:s', strtotime('-7 days'));
    $filteredSessions = array_filter($sessions, function($session) use ($cutoffDate) {
        return ($session['active'] ?? true) || ($session['lastActivity'] ?? '2000-01-01') > $cutoffDate;
    });
    
    return array_values($filteredSessions);
}
