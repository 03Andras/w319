<?php
/**
 * Bootstrap file
 * Initializes the backend application
 */

// Load configuration
require_once __DIR__ . '/config/constants.php';

// Load helpers
require_once __DIR__ . '/helpers/FileHelper.php';
require_once __DIR__ . '/helpers/AuditHelper.php';
require_once __DIR__ . '/helpers/SessionHelper.php';

// Load models
require_once __DIR__ . '/models/SettingsModel.php';
require_once __DIR__ . '/models/ScheduleModel.php';

// Load controllers
require_once __DIR__ . '/controllers/ScheduleController.php';
require_once __DIR__ . '/controllers/SettingsController.php';
require_once __DIR__ . '/controllers/SessionController.php';
require_once __DIR__ . '/controllers/AuditController.php';

// Load router
require_once __DIR__ . '/Router.php';

/**
 * Initialize the application
 */
function initializeApplication() {
    // Ensure data directory exists
    FileHelper::ensureDirectoryExists(DATA_DIR);

    // Initialize required files with defaults
    SettingsModel::initializeSettings();
    FileHelper::initializeFile(AUDIT_LOG_FILE, []);
    FileHelper::initializeFile(SESSIONS_FILE, []);
}
