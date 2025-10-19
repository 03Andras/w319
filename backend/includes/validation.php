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
 * Calculate Easter Sunday for a given year (using Computus algorithm)
 */
function calculateEasterSunday($year) {
    $a = $year % 19;
    $b = intdiv($year, 100);
    $c = $year % 100;
    $d = intdiv($b, 4);
    $e = $b % 4;
    $f = intdiv($b + 8, 25);
    $g = intdiv($b - $f + 1, 3);
    $h = (19 * $a + $b - $d - $g + 15) % 30;
    $i = intdiv($c, 4);
    $k = $c % 4;
    $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
    $m = intdiv($a + 11 * $h + 22 * $l, 451);
    $month = intdiv($h + $l - 7 * $m + 114, 31);
    $day = (($h + $l - 7 * $m + 114) % 31) + 1;
    
    $easterDate = new DateTime();
    $easterDate->setDate($year, $month, $day);
    $easterDate->setTime(0, 0, 0);
    
    return $easterDate;
}

/**
 * Check if a date is a Slovak public holiday
 * Uses configurable holidays from settings.json
 */
function isHoliday($dateString, $settings = null) {
    static $cachedSettings = null;
    
    $date = new DateTime($dateString);
    $year = (int)$date->format('Y');
    $month = (int)$date->format('n');
    $day = (int)$date->format('j');
    
    // Load settings if not provided and not cached
    if ($settings === null) {
        if ($cachedSettings === null) {
            $settingsFile = DATA_DIR . '/settings.json';
            $cachedSettings = [];
            if (file_exists($settingsFile)) {
                $settingsJson = @file_get_contents($settingsFile);
                if ($settingsJson !== false) {
                    $decoded = json_decode($settingsJson, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $cachedSettings = $decoded;
                    }
                }
            }
        }
        $settings = $cachedSettings;
    }
    
    // Default fixed public holidays in Slovakia (MM-DD format) - used if settings don't have holidays
    $defaultHolidays = [
        '01-01', '01-06', '05-01', '05-08', '07-05', '08-29',
        '09-01', '09-15', '11-01', '12-24', '12-25', '12-26'
    ];
    
    // Use holidays from settings, or fall back to defaults
    $fixedHolidays = $settings['holidays'] ?? $defaultHolidays;
    
    $dateKey = sprintf('%02d-%02d', $month, $day);
    if (in_array($dateKey, $fixedHolidays)) {
        return true;
    }
    
    // Calculate movable holidays (Easter-based) - always calculated, not configurable
    $easterSunday = calculateEasterSunday($year);
    
    // Good Friday (2 days before Easter)
    $goodFriday = clone $easterSunday;
    $goodFriday->modify('-2 days');
    
    // Easter Monday (1 day after Easter)
    $easterMonday = clone $easterSunday;
    $easterMonday->modify('+1 day');
    
    // Compare dates
    $currentDate = clone $date;
    $currentDate->setTime(0, 0, 0);
    
    if ($currentDate->format('Y-m-d') === $goodFriday->format('Y-m-d')) {
        return true;
    }
    
    if ($currentDate->format('Y-m-d') === $easterMonday->format('Y-m-d')) {
        return true;
    }
    
    return false;
}

/**
 * Check if a date is a non-working day (weekend or holiday), 
 * considering working day overrides (exceptions)
 */
function isNonWorkingDay($dateString, $workingDayOverrides = [], $settings = null) {
    // Check if this day is overridden as a working day
    if (is_array($workingDayOverrides) && in_array($dateString, $workingDayOverrides)) {
        return false; // This day is set as working, even if it's a weekend/holiday
    }
    
    // Otherwise, check if it's a weekend or holiday
    return isWeekend($dateString) || isHoliday($dateString, $settings);
}

/**
 * Validate that schedule data doesn't contain non-working day bookings
 * (weekends and holidays), unless they are explicitly set as working day exceptions
 */
function validateNoNonWorkingDayBookings($scheduleData) {
    // Load settings to get working day overrides and holidays
    $settingsFile = DATA_DIR . '/settings.json';
    $settings = [];
    if (file_exists($settingsFile)) {
        $settingsJson = file_get_contents($settingsFile);
        $settings = json_decode($settingsJson, true) ?? [];
    }
    
    $workingDayOverrides = $settings['workingDayOverrides'] ?? [];
    $nonWorkingBookings = [];
    
    foreach ($scheduleData as $dateString => $seats) {
        // Check if this is a non-working day (weekend/holiday without override)
        if (isNonWorkingDay($dateString, $workingDayOverrides, $settings)) {
            // Check if there are any non-empty bookings on this non-working day
            foreach ($seats as $seatNum => $occupant) {
                if (!empty($occupant) && trim($occupant) !== '') {
                    $nonWorkingBookings[] = $dateString;
                    break; // One is enough per date
                }
            }
        }
    }
    
    if (!empty($nonWorkingBookings)) {
        $dates = array_map(function($d) {
            $date = new DateTime($d);
            return $date->format('d.m.Y');
        }, $nonWorkingBookings);
        
        return [
            'valid' => false,
            'message' => 'Rezervácia nie je možná cez víkend alebo sviatok. Prosím, vyberte pracovný deň. Rezervácie na nepracovné dni: ' . implode(', ', $dates)
        ];
    }
    
    return ['valid' => true];
}

/**
 * Legacy function name for backward compatibility
 */
function validateNoWeekendBookings($scheduleData) {
    return validateNoNonWorkingDayBookings($scheduleData);
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
