<?php
/**
 * Schedule API controller
 */

class ScheduleController {
    /**
     * Get schedule for a specific month
     */
    public static function getSchedule() {
        $yearMonth = $_GET['yearMonth'] ?? '';
        
        if (empty($yearMonth)) {
            http_response_code(400);
            echo json_encode(['error' => 'yearMonth parameter required']);
            return;
        }

        $schedule = ScheduleModel::getSchedule($yearMonth);
        echo json_encode($schedule);
    }

    /**
     * Save schedule
     */
    public static function saveSchedule() {
        $input = file_get_contents('php://input');
        $decoded = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            return;
        }

        $yearMonth = $decoded['yearMonth'] ?? '';
        $scheduleData = $decoded['data'] ?? [];
        $user = $decoded['user'] ?? 'Unknown';
        $changeDetails = $decoded['changeDetails'] ?? [];
        
        if (empty($yearMonth)) {
            http_response_code(400);
            echo json_encode(['error' => 'yearMonth required']);
            return;
        }

        $result = ScheduleModel::saveSchedule($yearMonth, $scheduleData, $user, $changeDetails);
        
        if (isset($result['error'])) {
            http_response_code(400);
            echo json_encode([
                'error' => $result['message'],
                'message' => $result['message']
            ]);
            return;
        }

        echo json_encode($result);
    }
}
