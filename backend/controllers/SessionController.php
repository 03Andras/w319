<?php
/**
 * Session API controller
 */

class SessionController {
    /**
     * Register a new session
     */
    public static function registerSession() {
        $input = file_get_contents('php://input');
        $decoded = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            return;
        }

        $userName = $decoded['userName'] ?? '';
        $sessionId = $decoded['sessionId'] ?? '';
        
        if (empty($userName) || empty($sessionId)) {
            http_response_code(400);
            echo json_encode(['error' => 'userName and sessionId required']);
            return;
        }
        
        SessionHelper::registerSession($userName, $sessionId);
        echo json_encode(['success' => true]);
    }

    /**
     * Check if session is active
     */
    public static function checkSession() {
        $sessionId = $_GET['sessionId'] ?? '';
        
        if (empty($sessionId)) {
            http_response_code(400);
            echo json_encode(['error' => 'sessionId required']);
            return;
        }
        
        $isDisconnected = SessionHelper::isSessionDisconnected($sessionId);
        if (!$isDisconnected) {
            SessionHelper::updateSessionActivity($sessionId);
        }
        
        echo json_encode(['active' => !$isDisconnected]);
    }

    /**
     * Get all sessions
     */
    public static function getSessions() {
        $sessions = SessionHelper::getSessions();
        echo json_encode($sessions);
    }

    /**
     * Disconnect a user
     */
    public static function disconnectUser() {
        $input = file_get_contents('php://input');
        $decoded = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            return;
        }

        $userToDisconnect = $decoded['userToDisconnect'] ?? '';
        $adminUser = $decoded['adminUser'] ?? 'Unknown';
        
        if (empty($userToDisconnect)) {
            http_response_code(400);
            echo json_encode(['error' => 'userToDisconnect required']);
            return;
        }
        
        $count = SessionHelper::disconnectUser($userToDisconnect, $adminUser);
        echo json_encode(['success' => true, 'sessionsDisconnected' => $count]);
    }
}
