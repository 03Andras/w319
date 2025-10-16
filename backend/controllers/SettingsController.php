<?php
/**
 * Settings API controller
 */

class SettingsController {
    /**
     * Get application settings
     */
    public static function getSettings() {
        $settings = SettingsModel::getSettings();
        echo json_encode($settings);
    }

    /**
     * Save application settings
     */
    public static function saveSettings() {
        $input = file_get_contents('php://input');
        $decoded = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            return;
        }

        $user = $decoded['user'] ?? 'Unknown';
        $result = SettingsModel::saveSettings($decoded, $user);
        
        if (isset($result['error'])) {
            http_response_code(500);
            echo json_encode($result);
            return;
        }

        echo json_encode($result);
    }

    /**
     * Get PIN code
     */
    public static function getPinCode() {
        $pinCode = SettingsModel::getPinCode();
        echo json_encode(['pinCode' => $pinCode]);
    }
}
