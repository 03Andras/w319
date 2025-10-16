<?php
/**
 * Audit log API controller
 */

class AuditController {
    /**
     * Get audit log
     */
    public static function getAuditLog() {
        $logs = AuditHelper::getLogs();
        echo json_encode($logs);
    }
}
