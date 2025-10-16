# Backend Structure Documentation

## Overview

The backend has been refactored from a monolithic `api.php` file into a modular, organized structure. This improves maintainability, readability, and testability while maintaining 100% backward compatibility.

## Directory Structure

```
backend/
├── config/
│   └── config.php              # Configuration constants and settings
├── includes/
│   ├── file_operations.php     # File I/O operations and helpers
│   ├── validation.php          # Input validation and error handling
│   ├── audit_log.php          # Audit logging functionality
│   └── session_management.php  # User session tracking
└── handlers/
    ├── schedule_handler.php    # Schedule GET/POST operations
    ├── settings_handler.php    # Settings management
    ├── audit_log_handler.php   # Audit log retrieval
    └── session_handler.php     # Session management endpoints
```

## Architecture

### Entry Point: `api.php`

The main API file now acts as a router that:
1. Loads configuration
2. Sets up HTTP headers
3. Loads all helper functions and handlers
4. Routes requests to appropriate handlers based on the `action` parameter

### Configuration Layer: `backend/config/config.php`

Contains all system-wide constants and configuration:
- Data directory paths (maintains `root/data` location)
- Default settings values
- Default team members
- Header setup function

### Helper Functions: `backend/includes/`

#### `file_operations.php`
- File read/write operations with error handling
- Directory initialization
- JSON file operations with proper error handling
- Schedule file path generation

#### `validation.php`
- Input validation (JSON, year-month format)
- Booking time range validation
- Response helper functions (error/success)

#### `audit_log.php`
- Single function to add audit log entries
- Maintains consistent logging format

#### `session_management.php`
- User session registration
- Session activity tracking
- IP address detection
- Session disconnect functionality

### Action Handlers: `backend/handlers/`

Each handler file contains functions for specific API actions:

#### `schedule_handler.php`
- `handleGetSchedule()` - Retrieve schedule for a month
- `handleSaveSchedule()` - Save/update schedule with validation

#### `settings_handler.php`
- `handleGetSettings()` - Retrieve system settings
- `handleSaveSettings()` - Update system settings
- `handleGetPinCode()` - Get PIN code for authentication

#### `audit_log_handler.php`
- `handleGetAuditLog()` - Retrieve audit log entries

#### `session_handler.php`
- `handleRegisterSession()` - Register new user session
- `handleCheckSession()` - Check if session is active
- `handleGetSessions()` - List all active sessions
- `handleDisconnectUser()` - Disconnect user sessions (admin)

## Data Storage

All data is stored in the `data/` directory at the project root:
- `settings.json` - System settings and team members
- `audit_log.json` - Audit log of all actions
- `sessions.json` - Active user sessions
- `schedule_YYYYMM.json` - Monthly schedules

The data directory location is configurable in `backend/config/config.php` but defaults to `root/data` as required.

## API Endpoints

All endpoints maintain backward compatibility with the original implementation:

### GET Requests
- `?action=getSchedule&yearMonth=YYYYMM` - Get schedule for a month
- `?action=getSettings` - Get system settings
- `?action=getAuditLog` - Get audit log
- `?action=checkSession&sessionId=xxx` - Check session status
- `?action=getSessions` - Get all sessions
- `?action=getPinCode` - Get PIN code

### POST Requests
All POST requests expect JSON body with appropriate parameters:
- `?action=saveSchedule` - Save schedule
- `?action=saveSettings` - Save settings
- `?action=registerSession` - Register new session
- `?action=disconnectUser` - Disconnect user (admin)

## Error Handling

The refactored backend includes improved error handling:
- File operation errors are caught and reported clearly
- JSON validation ensures data integrity
- HTTP status codes are properly set (400 for bad requests, 500 for server errors)
- All errors return JSON responses with descriptive messages

## Benefits of the Refactored Structure

1. **Modularity**: Each component has a single responsibility
2. **Maintainability**: Easy to locate and modify specific functionality
3. **Testability**: Individual functions can be tested in isolation
4. **Readability**: Clear organization makes code easier to understand
5. **Extensibility**: Adding new features is straightforward
6. **Backward Compatibility**: Existing frontend code works without changes

## Adding New Features

To add a new API action:

1. Create a handler function in the appropriate file (or create a new handler file)
2. Add the action to the switch statement in `api.php`
3. Ensure proper error handling and validation
4. Update this documentation

Example:
```php
// In backend/handlers/my_handler.php
function handleMyNewAction() {
    // Your implementation
    sendSuccessResponse(['data' => 'result']);
}

// In api.php
case 'myNewAction':
    handleMyNewAction();
    break;
```

## Testing

Test individual components using PHP CLI:
```bash
php -r "
\$_GET['action'] = 'getSettings';
include 'api.php';
"
```

## Security Considerations

The refactored backend maintains all original security features:
- PIN code protection
- Admin authentication
- Session tracking
- Audit logging
- Data directory protection via .htaccess

## Migration Notes

The original `api.php` has been preserved as `api_original.php` for reference. The new modular structure is a drop-in replacement with identical functionality.
