<?php
/**
 * Settings management handler
 * Handles getSettings and saveSettings actions
 */

/**
 * Handle getSettings action
 */
function handleGetSettings() {
    $data = file_get_contents(SETTINGS_FILE);
    echo $data;
}

/**
 * Handle saveSettings action
 */
function handleSaveSettings() {
    $input = file_get_contents('php://input');
    $decoded = validateJsonInput($input);
    
    if ($decoded === null) {
        sendErrorResponse('Invalid JSON');
    }
    
    $user = $decoded['user'] ?? 'Unknown';
    unset($decoded['user']); // Remove user from settings data
    
    writeJsonFile(SETTINGS_FILE, $decoded);
    
    // Add audit log
    addAuditLog($user, 'settings_update', ['changes' => 'Settings modified']);
    
    sendSuccessResponse();
}

/**
 * Handle getPinCode action
 */
function handleGetPinCode() {
    $settings = readJsonFile(SETTINGS_FILE);
    $pinCode = $settings['pinCode'] ?? DEFAULT_PIN_CODE;
    sendSuccessResponse(['pinCode' => $pinCode]);
}
