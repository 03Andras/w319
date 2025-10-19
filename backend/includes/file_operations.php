<?php
/**
 * File operations helper functions
 * Handles all file read/write operations with proper error handling
 */

/**
 * Handle file operation errors
 */
function handleFileError($filepath, $operation = 'write') {
    http_response_code(500);
    echo json_encode([
        'error' => 'Nesprávne oprávnenia priečinka',
        'message' => 'Nedostatočné oprávnenia na komunikáciu so súbormi. Skontrolujte oprávnenia priečinka: ' . dirname($filepath),
        'filepath' => $filepath
    ]);
    exit;
}

/**
 * Ensure data directory exists and is writable
 */
function ensureDataDirectory() {
    if (!is_dir(DATA_DIR)) {
        if (!@mkdir(DATA_DIR, 0755, true)) {
            handleFileError(DATA_DIR, 'create directory');
        }
    }
    
    if (!is_writable(DATA_DIR)) {
        handleFileError(DATA_DIR, 'write to directory');
    }
}

/**
 * Initialize settings file with default values if it doesn't exist
 */
function initializeSettingsFile() {
    global $DEFAULT_TEAM;
    
    if (!file_exists(SETTINGS_FILE)) {
        $defaultSettings = [
            'ownerName' => '',
            'labelStyle' => 'surname',
            'adminUser' => '',
            'adminUsers' => ["Eva Mészáros"],
            'adminPassword' => DEFAULT_ADMIN_PASSWORD,
            'pinCode' => DEFAULT_PIN_CODE,
            'connectedUsers' => [],
            'team' => $DEFAULT_TEAM,
            'workingDayOverrides' => []
        ];
        if (@file_put_contents(SETTINGS_FILE, json_encode($defaultSettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
            handleFileError(SETTINGS_FILE, 'create');
        }
    }
}

/**
 * Initialize audit log file if it doesn't exist
 */
function initializeAuditLogFile() {
    if (!file_exists(AUDIT_LOG_FILE)) {
        if (@file_put_contents(AUDIT_LOG_FILE, '[]') === false) {
            handleFileError(AUDIT_LOG_FILE, 'create');
        }
    }
}

/**
 * Initialize sessions file if it doesn't exist
 */
function initializeSessionsFile() {
    if (!file_exists(SESSIONS_FILE)) {
        if (@file_put_contents(SESSIONS_FILE, '[]') === false) {
            handleFileError(SESSIONS_FILE, 'create');
        }
    }
}

/**
 * Initialize all required files
 */
function initializeFiles() {
    ensureDataDirectory();
    initializeSettingsFile();
    initializeAuditLogFile();
    initializeSessionsFile();
}

/**
 * Get schedule file path for a specific month
 */
function getScheduleFile($yearMonth) {
    return DATA_DIR . '/schedule_' . $yearMonth . '.json';
}

/**
 * Read JSON file and return decoded array
 */
function readJsonFile($filepath, $defaultValue = []) {
    if (!file_exists($filepath)) {
        return $defaultValue;
    }
    
    $content = @file_get_contents($filepath);
    if ($content === false) {
        return $defaultValue;
    }
    
    $decoded = json_decode($content, true);
    return ($decoded !== null) ? $decoded : $defaultValue;
}

/**
 * Write data to JSON file
 */
function writeJsonFile($filepath, $data) {
    if (@file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
        handleFileError($filepath);
        return false;
    }
    return true;
}
