<?php
/**
 * API Entry Point for Desk Reservation System
 * 
 * This is a refactored, modular backend structure that maintains
 * 100% backward compatibility with the original monolithic implementation.
 * 
 * All functionality is organized into separate modules for better
 * maintainability and readability.
 */

// Load configuration
require_once __DIR__ . '/backend/config/config.php';

// Setup response headers
setupHeaders();

// Load helper functions
require_once __DIR__ . '/backend/includes/file_operations.php';
require_once __DIR__ . '/backend/includes/validation.php';
require_once __DIR__ . '/backend/includes/audit_log.php';
require_once __DIR__ . '/backend/includes/session_management.php';

// Load action handlers
require_once __DIR__ . '/backend/handlers/schedule_handler.php';
require_once __DIR__ . '/backend/handlers/settings_handler.php';
require_once __DIR__ . '/backend/handlers/audit_log_handler.php';
require_once __DIR__ . '/backend/handlers/session_handler.php';

// Initialize all required files
initializeFiles();

// Get the requested action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Route to appropriate handler
switch ($action) {
    case 'getSchedule':
        handleGetSchedule();
        break;
    
    case 'saveSchedule':
        handleSaveSchedule();
        break;
    
    case 'getSettings':
        handleGetSettings();
        break;
    
    case 'saveSettings':
        handleSaveSettings();
        break;
    
    case 'getAuditLog':
        handleGetAuditLog();
        break;
    
    case 'disconnectUser':
        handleDisconnectUser();
        break;
    
    case 'registerSession':
        handleRegisterSession();
        break;
    
    case 'checkSession':
        handleCheckSession();
        break;
    
    case 'getSessions':
        handleGetSessions();
        break;
    
    case 'getPinCode':
        handleGetPinCode();
        break;
    
    case 'getVersion':
        handleGetVersion();
        break;
    
    default:
        sendErrorResponse('Invalid action');
        break;
}
