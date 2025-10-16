<?php
/**
 * File operations helper functions
 */

class FileHelper {
    /**
     * Handle file write errors
     */
    public static function handleFileError($filepath, $operation = 'write') {
        http_response_code(500);
        echo json_encode([
            'error' => 'Nesprávne oprávnenia priečinka',
            'message' => 'Nedostatočné oprávnenia na komunikáciu so súbormi. Skontrolujte oprávnenia priečinka: ' . dirname($filepath),
            'filepath' => $filepath
        ]);
        exit;
    }

    /**
     * Ensure directory exists and is writable
     */
    public static function ensureDirectoryExists($dir) {
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0755, true)) {
                self::handleFileError($dir, 'create directory');
            }
        }

        if (!is_writable($dir)) {
            self::handleFileError($dir, 'write to directory');
        }
    }

    /**
     * Read JSON file safely
     */
    public static function readJsonFile($filepath, $default = []) {
        if (!file_exists($filepath)) {
            return $default;
        }

        $content = file_get_contents($filepath);
        $data = json_decode($content, true);
        
        return ($data !== null) ? $data : $default;
    }

    /**
     * Write JSON file safely
     */
    public static function writeJsonFile($filepath, $data) {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (@file_put_contents($filepath, $json) === false) {
            self::handleFileError($filepath);
            return false;
        }
        return true;
    }

    /**
     * Initialize file with default data if it doesn't exist
     */
    public static function initializeFile($filepath, $defaultData) {
        if (!file_exists($filepath)) {
            return self::writeJsonFile($filepath, $defaultData);
        }
        return true;
    }

    /**
     * Get schedule file path for a specific month
     */
    public static function getScheduleFile($yearMonth) {
        return DATA_DIR . '/schedule_' . $yearMonth . '.json';
    }
}
