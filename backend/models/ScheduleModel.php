<?php
/**
 * Schedule data model
 */

class ScheduleModel {
    /**
     * Get schedule for a specific month
     */
    public static function getSchedule($yearMonth) {
        $scheduleFile = FileHelper::getScheduleFile($yearMonth);
        return FileHelper::readJsonFile($scheduleFile, []);
    }

    /**
     * Save schedule for a specific month
     */
    public static function saveSchedule($yearMonth, $scheduleData, $user, $changeDetails = []) {
        // Validate booking time range (3 months ahead)
        if (!self::validateBookingTimeRange($yearMonth)) {
            return [
                'error' => true,
                'message' => self::getAvailabilityMessage($yearMonth)
            ];
        }

        $scheduleFile = FileHelper::getScheduleFile($yearMonth);
        
        // Save schedule
        if (!FileHelper::writeJsonFile($scheduleFile, $scheduleData)) {
            return ['error' => true, 'message' => 'Failed to save schedule'];
        }

        // Add detailed audit log
        $logDetails = array_merge($changeDetails, ['yearMonth' => $yearMonth]);
        AuditHelper::addLog($user, 'schedule_update', $logDetails);

        return ['success' => true];
    }

    /**
     * Validate if booking is within allowed time range (3 months ahead)
     */
    private static function validateBookingTimeRange($yearMonth) {
        $currentDate = new DateTime();
        $currentYearMonth = $currentDate->format('Ym');
        
        // Parse target year and month
        $targetYear = (int)substr($yearMonth, 0, 4);
        $targetMonth = (int)substr($yearMonth, 4, 2);
        
        // Calculate the first of next month, then add 2 more months (total 3 ahead)
        $threeMonthsAhead = new DateTime();
        $threeMonthsAhead->setDate($currentDate->format('Y'), $currentDate->format('n') + 1, 1);
        $threeMonthsAhead->modify('+2 months');
        $threeMonthsAhead->setTime(0, 0, 0);
        
        $targetDate = new DateTime();
        $targetDate->setDate($targetYear, $targetMonth, 1);
        $targetDate->setTime(0, 0, 0);
        
        // Check if target month is too far in the future
        return $targetDate < $threeMonthsAhead;
    }

    /**
     * Get availability message for a specific month
     */
    private static function getAvailabilityMessage($yearMonth) {
        $targetYear = (int)substr($yearMonth, 0, 4);
        $targetMonth = (int)substr($yearMonth, 4, 2);
        
        $targetDate = new DateTime();
        $targetDate->setDate($targetYear, $targetMonth, 1);
        
        // Calculate when this specific month becomes available
        $availableDate = clone $targetDate;
        $availableDate->modify('-3 months');
        $availableFromDate = $availableDate->format('d.m.Y');
        
        return 'Rezervácia na tento mesiac bude dostupná od ' . $availableFromDate;
    }
}
