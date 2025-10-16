<?php
/**
 * Session management helper functions
 */

class SessionHelper {
    /**
     * Get client IP address
     */
    public static function getClientIP() {
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
    public static function registerSession($userName, $sessionId) {
        $sessions = FileHelper::readJsonFile(SESSIONS_FILE, []);
        
        $sessions[] = [
            'sessionId' => $sessionId,
            'userName' => $userName,
            'ipAddress' => self::getClientIP(),
            'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'loginTime' => date('Y-m-d H:i:s'),
            'lastActivity' => date('Y-m-d H:i:s'),
            'active' => true
        ];
        
        FileHelper::writeJsonFile(SESSIONS_FILE, $sessions);
        
        // Log to audit
        AuditHelper::addLog($userName, 'user_login', [
            'sessionId' => $sessionId, 
            'ipAddress' => self::getClientIP()
        ]);
    }

    /**
     * Check if session is disconnected
     */
    public static function isSessionDisconnected($sessionId) {
        $sessions = FileHelper::readJsonFile(SESSIONS_FILE, []);
        
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
    public static function updateSessionActivity($sessionId) {
        $sessions = FileHelper::readJsonFile(SESSIONS_FILE, []);
        
        foreach ($sessions as &$session) {
            if ($session['sessionId'] === $sessionId) {
                $session['lastActivity'] = date('Y-m-d H:i:s');
                break;
            }
        }
        
        FileHelper::writeJsonFile(SESSIONS_FILE, $sessions);
    }

    /**
     * Get all sessions (filtered)
     */
    public static function getSessions() {
        $sessions = FileHelper::readJsonFile(SESSIONS_FILE, []);
        
        // Filter to show only active sessions or recent ones (last 7 days)
        $cutoffDate = date('Y-m-d H:i:s', strtotime('-7 days'));
        $filteredSessions = array_filter($sessions, function($session) use ($cutoffDate) {
            return ($session['active'] ?? true) || ($session['lastActivity'] ?? '2000-01-01') > $cutoffDate;
        });
        
        return array_values($filteredSessions);
    }

    /**
     * Disconnect user sessions
     */
    public static function disconnectUser($userToDisconnect, $adminUser) {
        $sessions = FileHelper::readJsonFile(SESSIONS_FILE, []);
        $disconnectedCount = 0;
        
        foreach ($sessions as &$session) {
            if ($session['userName'] === $userToDisconnect && ($session['active'] ?? true)) {
                $session['active'] = false;
                $session['disconnectedBy'] = $adminUser;
                $session['disconnectedAt'] = date('Y-m-d H:i:s');
                $disconnectedCount++;
            }
        }
        
        FileHelper::writeJsonFile(SESSIONS_FILE, $sessions);
        
        // Add to audit log
        AuditHelper::addLog($adminUser, 'user_disconnect', [
            'disconnectedUser' => $userToDisconnect,
            'sessionsDisconnected' => $disconnectedCount
        ]);
        
        return $disconnectedCount;
    }
}
