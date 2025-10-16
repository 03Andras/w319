<?php
/**
 * Settings data model
 */

class SettingsModel {
    /**
     * Get application settings
     */
    public static function getSettings() {
        return FileHelper::readJsonFile(SETTINGS_FILE, self::getDefaultSettings());
    }

    /**
     * Save application settings
     */
    public static function saveSettings($data, $user) {
        // Remove user from settings data if present
        unset($data['user']);
        
        if (!FileHelper::writeJsonFile(SETTINGS_FILE, $data)) {
            return ['error' => true, 'message' => 'Failed to save settings'];
        }

        // Add audit log
        AuditHelper::addLog($user, 'settings_update', ['changes' => 'Settings modified']);

        return ['success' => true];
    }

    /**
     * Get PIN code
     */
    public static function getPinCode() {
        $settings = self::getSettings();
        return $settings['pinCode'] ?? DEFAULT_PIN_CODE;
    }

    /**
     * Get default settings structure
     */
    private static function getDefaultSettings() {
        return [
            'ownerName' => '',
            'labelStyle' => 'surname',
            'adminUser' => '',
            'adminUsers' => ["Eva Mészáros"],
            'adminPassword' => DEFAULT_ADMIN_PASSWORD,
            'pinCode' => DEFAULT_PIN_CODE,
            'connectedUsers' => [],
            'team' => DEFAULT_TEAM
        ];
    }

    /**
     * Initialize settings file with defaults
     */
    public static function initializeSettings() {
        FileHelper::initializeFile(SETTINGS_FILE, self::getDefaultSettings());
    }
}
