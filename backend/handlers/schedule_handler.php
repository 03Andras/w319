<?php
/**
 * Schedule management handler
 * Handles getSchedule and saveSchedule actions
 */

/**
 * Handle getSchedule action
 */
function handleGetSchedule() {
    $yearMonth = $_GET['yearMonth'] ?? '';
    
    if (!validateYearMonth($yearMonth)) {
        sendErrorResponse('yearMonth parameter required');
    }
    
    $scheduleFile = getScheduleFile($yearMonth);
    
    if (!file_exists($scheduleFile)) {
        echo '{}';
    } else {
        $data = file_get_contents($scheduleFile);
        echo $data;
    }
}

/**
 * Handle saveSchedule action
 */
function handleSaveSchedule() {
    $input = file_get_contents('php://input');
    $decoded = validateJsonInput($input);
    
    if ($decoded === null) {
        sendErrorResponse('Invalid JSON');
    }
    
    $yearMonth = $decoded['yearMonth'] ?? '';
    $scheduleData = $decoded['data'] ?? [];
    $user = $decoded['user'] ?? 'Unknown';
    $changeDetails = $decoded['changeDetails'] ?? [];
    
    if (!validateYearMonth($yearMonth)) {
        sendErrorResponse('yearMonth required');
    }
    
    // Validate booking time range
    $validation = validateBookingTimeRange($yearMonth);
    if (!$validation['valid']) {
        sendErrorResponse($validation['message']);
    }
    
    // Save new schedule
    $scheduleFile = getScheduleFile($yearMonth);
    writeJsonFile($scheduleFile, $scheduleData);
    
    // Add detailed audit log
    $logDetails = array_merge($changeDetails, ['yearMonth' => $yearMonth]);
    addAuditLog($user, 'schedule_update', $logDetails);
    
    sendSuccessResponse();
}
