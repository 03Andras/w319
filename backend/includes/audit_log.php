<?php
/**
 * Audit log management functions
 * Handles logging of all system events
 */

/**
 * Add an entry to the audit log
 */
function addAuditLog($user, $action, $details = []) {
    $logs = readJsonFile(AUDIT_LOG_FILE, []);
    
    $logs[] = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user' => $user,
        'action' => $action,
        'details' => $details
    ];
    
    writeJsonFile(AUDIT_LOG_FILE, $logs);
}
