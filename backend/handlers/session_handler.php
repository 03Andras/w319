<?php
/**
 * Session handler
 * Handles registerSession, checkSession, getSessions, and disconnectUser actions
 */

/**
 * Handle registerSession action
 */
function handleRegisterSession() {
    $input = file_get_contents('php://input');
    $decoded = validateJsonInput($input);
    
    if ($decoded === null) {
        sendErrorResponse('Invalid JSON');
    }
    
    $userName = $decoded['userName'] ?? '';
    $sessionId = $decoded['sessionId'] ?? '';
    
    if (empty($userName) || empty($sessionId)) {
        sendErrorResponse('userName and sessionId required');
    }
    
    registerSession($userName, $sessionId);
    sendSuccessResponse();
}

/**
 * Handle checkSession action
 */
function handleCheckSession() {
    $sessionId = $_GET['sessionId'] ?? '';
    
    if (empty($sessionId)) {
        sendErrorResponse('sessionId required');
    }
    
    $isDisconnected = isSessionDisconnected($sessionId);
    if (!$isDisconnected) {
        updateSessionActivity($sessionId);
    }
    
    sendSuccessResponse(['active' => !$isDisconnected]);
}

/**
 * Handle getSessions action
 */
function handleGetSessions() {
    $sessions = getSessions();
    echo json_encode($sessions);
}

/**
 * Handle disconnectUser action
 */
function handleDisconnectUser() {
    $input = file_get_contents('php://input');
    $decoded = validateJsonInput($input);
    
    if ($decoded === null) {
        sendErrorResponse('Invalid JSON');
    }
    
    $userToDisconnect = $decoded['userToDisconnect'] ?? '';
    $adminUser = $decoded['adminUser'] ?? 'Unknown';
    
    if (empty($userToDisconnect)) {
        sendErrorResponse('userToDisconnect required');
    }
    
    $disconnectedCount = disconnectUserSessions($userToDisconnect, $adminUser);
    
    sendSuccessResponse(['success' => true, 'sessionsDisconnected' => $disconnectedCount]);
}
