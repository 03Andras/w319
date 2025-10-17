<?php
/**
 * Validation helper functions
 * Handles input validation and date range checks
 */

/**
 * Validate year-month format (YYYYMM)
 */
function validateYearMonth($yearMonth) {
    if (empty($yearMonth)) {
        return false;
    }
    
    // Should be 6 digits
    if (!preg_match('/^\d{6}$/', $yearMonth)) {
        return false;
    }
    
    $year = (int)substr($yearMonth, 0, 4);
    $month = (int)substr($yearMonth, 4, 2);
    
    // Valid year and month ranges
    return $year >= 2000 && $year <= 2100 && $month >= 1 && $month <= 12;
}

/**
 * Check if booking is within allowed time range (3 months ahead, starting from new month)
 */
function validateBookingTimeRange($yearMonth) {
    $currentDate = new DateTime();
    
    // Parse target year and month
    $targetYear = (int)substr($yearMonth, 0, 4);
    $targetMonth = (int)substr($yearMonth, 4, 2);
    
    // Calculate limit: first of next month + 3 months = can book current + 3 future months
    $threeMonthsAhead = new DateTime();
    $threeMonthsAhead->setDate($currentDate->format('Y'), $currentDate->format('n') + 1, 1);
    $threeMonthsAhead->modify('+3 months');
    $threeMonthsAhead->setTime(0, 0, 0);
    
    $targetDate = new DateTime();
    $targetDate->setDate($targetYear, $targetMonth, 1);
    $targetDate->setTime(0, 0, 0);
    
    // Check if target month is too far in the future
    if ($targetDate >= $threeMonthsAhead) {
        // Calculate when this specific month becomes available
        $availableDate = clone $targetDate;
        $availableDate->modify('-3 months');
        $availableFromDate = $availableDate->format('d.m.Y');
        
        return [
            'valid' => false,
            'message' => 'Rezervácia na tento mesiac bude dostupná od ' . $availableFromDate
        ];
    }
    
    return ['valid' => true];
}

/**
 * Check if a date is a weekend (Saturday or Sunday)
 */
function isWeekend($dateString) {
    // Parse date string (format: YYYY-MM-DD)
    $date = new DateTime($dateString);
    $dayOfWeek = (int)$date->format('N'); // 1 (Monday) through 7 (Sunday)
    
    // 6 = Saturday, 7 = Sunday
    return $dayOfWeek === 6 || $dayOfWeek === 7;
}

/**
 * Validate that schedule data doesn't contain weekend bookings
 */
function validateNoWeekendBookings($scheduleData) {
    $weekendBookings = [];
    
    foreach ($scheduleData as $dateString => $seats) {
        if (isWeekend($dateString)) {
            // Check if there are any non-empty bookings on this weekend day
            foreach ($seats as $seatNum => $occupant) {
                if (!empty($occupant) && trim($occupant) !== '') {
                    $weekendBookings[] = $dateString;
                    break; // One is enough per date
                }
            }
        }
    }
    
    if (!empty($weekendBookings)) {
        $dates = array_map(function($d) {
            $date = new DateTime($d);
            return $date->format('d.m.Y');
        }, $weekendBookings);
        
        return [
            'valid' => false,
            'message' => 'Rezervácia nie je možná cez víkend (sobota a nedeľa). Prosím, vyberte pracovný deň. Rezervácie na víkendové dni: ' . implode(', ', $dates)
        ];
    }
    
    return ['valid' => true];
}

/**
 * Validate JSON input
 */
function validateJsonInput($input) {
    if (empty($input)) {
        return null;
    }
    
    $decoded = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }
    
    return $decoded;
}

/**
 * Send JSON error response
 */
function sendErrorResponse($message, $statusCode = 400) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit;
}

/**
 * Send JSON success response
 */
function sendSuccessResponse($data = ['success' => true]) {
    echo json_encode($data);
    exit;
}
