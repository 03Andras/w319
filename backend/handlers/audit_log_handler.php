<?php
/**
 * Audit log handler
 * Handles getAuditLog action
 */

/**
 * Handle getAuditLog action
 */
function handleGetAuditLog() {
    $data = file_get_contents(AUDIT_LOG_FILE);
    echo $data;
}
