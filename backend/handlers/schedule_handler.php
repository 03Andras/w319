<?php
/**
 * Schedule management handler
 * Handles getSchedule and saveSchedule actions
 */

/**
 * Handle getSchedule action with retry logic for reliability
 */
function handleGetSchedule() {
    $yearMonth = $_GET['yearMonth'] ?? '';
    
    if (!validateYearMonth($yearMonth)) {
        sendErrorResponse('yearMonth parameter required');
    }
    
    $scheduleFile = getScheduleFile($yearMonth);
    
    // Ensure data directory exists and is writable
    ensureDataDirectory();
    
    if (!file_exists($scheduleFile)) {
        // Return empty schedule with verification metadata
        header('Content-Type: application/json');
        echo json_encode([
            'data' => [],
            'verified' => true,
            'empty' => true,
            'yearMonth' => $yearMonth
        ]);
        return;
    }
    
    // Try to read file with retry mechanism (up to 3 attempts)
    $maxRetries = 3;
    $data = null;
    $lastError = '';
    
    for ($i = 0; $i < $maxRetries; $i++) {
        // Check if file still exists (could have been deleted between checks)
        if (!file_exists($scheduleFile)) {
            // File was deleted, return empty schedule
            header('Content-Type: application/json');
            echo json_encode([
                'data' => [],
                'verified' => true,
                'empty' => true,
                'yearMonth' => $yearMonth,
                'note' => 'File was deleted during read'
            ]);
            return;
        }
        
        $data = @file_get_contents($scheduleFile);
        if ($data !== false) {
            // Verify JSON is valid
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE || $data === '{}' || $data === '[]') {
                // Return data with verification metadata
                header('Content-Type: application/json');
                echo json_encode([
                    'data' => $decoded ?? [],
                    'verified' => true,
                    'yearMonth' => $yearMonth,
                    'fileSize' => strlen($data),
                    'retries' => $i
                ]);
                return;
            } else {
                $lastError = 'Invalid JSON data: ' . json_last_error_msg();
                // Try to repair the file by returning empty and logging
                error_log("Schedule file $scheduleFile has invalid JSON: " . json_last_error_msg());
            }
        } else {
            $lastError = 'Failed to read file';
            error_log("Failed to read schedule file $scheduleFile on attempt " . ($i + 1));
        }
        
        // Wait briefly before retry
        if ($i < $maxRetries - 1) {
            usleep(100000); // 100ms
        }
    }
    
    // If all retries failed, return error with Slovak message
    sendErrorResponse('Nepodarilo sa načítať dáta rozvrhu po ' . $maxRetries . ' pokusoch: ' . $lastError, 500);
}

/**
 * Count non-empty bookings in schedule data
 */
function countBookings($scheduleData) {
    $count = 0;
    foreach ($scheduleData as $date => $seats) {
        if (is_array($seats)) {
            foreach ($seats as $seatNum => $occupant) {
                if (!empty($occupant) && trim($occupant) !== '') {
                    $count++;
                }
            }
        }
    }
    return $count;
}

/**
 * Validate that new schedule data doesn't accidentally delete too many bookings
 */
function validateScheduleDataIntegrity($scheduleFile, $newScheduleData) {
    // If file doesn't exist, any data is acceptable (first save)
    if (!file_exists($scheduleFile)) {
        return ['valid' => true];
    }
    
    // Load existing schedule
    $existingData = readJsonFile($scheduleFile, []);
    
    // Count bookings in both datasets
    $existingBookings = countBookings($existingData);
    $newBookings = countBookings($newScheduleData);
    
    // If there are no existing bookings, accept the new data
    if ($existingBookings === 0) {
        return ['valid' => true];
    }
    
    // Calculate how many bookings would be deleted
    $deletedBookings = $existingBookings - $newBookings;
    
    // Define threshold: reject if more than 80% of bookings would be deleted
    // AND at least 5 bookings would be deleted (to avoid false positives on small datasets)
    $deletionPercentage = ($deletedBookings / $existingBookings) * 100;
    
    if ($deletedBookings >= 5 && $deletionPercentage > 80) {
        return [
            'valid' => false,
            'message' => sprintf(
                'Operácia zamietnutá: Pokus o zmazanie príliš veľa rezervácií (%d z %d, %.1f%%). ' .
                'Toto môže byť spôsobené chybou pri načítaní dát. Prosím, obnovte stránku a skúste znova.',
                $deletedBookings,
                $existingBookings,
                $deletionPercentage
            ),
            'existingBookings' => $existingBookings,
            'newBookings' => $newBookings,
            'deletedBookings' => $deletedBookings
        ];
    }
    
    return ['valid' => true];
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
    
    // Validate no weekend bookings
    $weekendValidation = validateNoWeekendBookings($scheduleData);
    if (!$weekendValidation['valid']) {
        // Log the rejected attempt
        $logDetails = array_merge($changeDetails, [
            'yearMonth' => $yearMonth,
            'reason' => 'weekend_booking_attempted'
        ]);
        addAuditLog($user, 'schedule_save_rejected', $logDetails);
        
        sendErrorResponse($weekendValidation['message']);
    }
    
    // Validate data integrity to prevent accidental mass deletion
    $scheduleFile = getScheduleFile($yearMonth);
    $integrityCheck = validateScheduleDataIntegrity($scheduleFile, $scheduleData);
    if (!$integrityCheck['valid']) {
        // Log the rejected attempt for security audit
        $logDetails = array_merge($changeDetails, [
            'yearMonth' => $yearMonth,
            'reason' => 'data_integrity_violation',
            'existingBookings' => $integrityCheck['existingBookings'] ?? 0,
            'newBookings' => $integrityCheck['newBookings'] ?? 0,
            'deletedBookings' => $integrityCheck['deletedBookings'] ?? 0
        ]);
        addAuditLog($user, 'schedule_save_rejected', $logDetails);
        
        sendErrorResponse($integrityCheck['message']);
    }
    
    // Save new schedule
    writeJsonFile($scheduleFile, $scheduleData);
    
    // Add detailed audit log
    $logDetails = array_merge($changeDetails, ['yearMonth' => $yearMonth]);
    addAuditLog($user, 'schedule_update', $logDetails);
    
    sendSuccessResponse();
}
