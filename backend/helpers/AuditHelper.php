<?php
/**
 * Audit log helper functions
 */

class AuditHelper {
    /**
     * Add entry to audit log
     */
    public static function addLog($user, $action, $details = []) {
        $logs = FileHelper::readJsonFile(AUDIT_LOG_FILE, []);
        
        $logs[] = [
            'timestamp' => date('Y-m-d H:i:s'),
            'user' => $user,
            'action' => $action,
            'details' => $details
        ];
        
        FileHelper::writeJsonFile(AUDIT_LOG_FILE, $logs);
    }

    /**
     * Get all audit logs
     */
    public static function getLogs() {
        return FileHelper::readJsonFile(AUDIT_LOG_FILE, []);
    }
}
