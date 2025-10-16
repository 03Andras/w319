# Backend Migration Guide

## Overview

This document describes the migration from the monolithic `api.php` to the new modular backend structure.

## What Changed

### File Structure

**Before:**
```
/
├── api.php (393 lines - everything in one file)
├── index.html
└── data/
```

**After:**
```
/
├── api.php (new modular entry point - 86 lines)
├── api_original.php (backup of original)
├── index.html (unchanged)
├── data/ (unchanged)
└── backend/
    ├── README.md
    ├── config/
    │   └── config.php
    ├── includes/
    │   ├── file_operations.php
    │   ├── validation.php
    │   ├── audit_log.php
    │   └── session_management.php
    └── handlers/
        ├── schedule_handler.php
        ├── settings_handler.php
        ├── audit_log_handler.php
        └── session_handler.php
```

## Backward Compatibility

✅ **100% backward compatible** - No changes needed to frontend code or API consumers.

All endpoints work exactly as before:
- Same URLs
- Same parameters
- Same response formats
- Same error handling

## Code Organization

### Configuration (backend/config/)

All constants and settings are now in one place:
- Data directory paths
- Default values
- System-wide configuration

**Example:**
```php
define('DATA_DIR', __DIR__ . '/../../data');
define('SETTINGS_FILE', DATA_DIR . '/settings.json');
```

### Helper Functions (backend/includes/)

Common functionality extracted into reusable modules:

1. **file_operations.php** - File I/O
   - `readJsonFile()` - Safe JSON reading
   - `writeJsonFile()` - Safe JSON writing
   - `initializeFiles()` - Setup data directory

2. **validation.php** - Input validation
   - `validateYearMonth()` - Date format validation
   - `validateBookingTimeRange()` - Business logic validation
   - `sendErrorResponse()` / `sendSuccessResponse()` - Standardized responses

3. **audit_log.php** - Logging
   - `addAuditLog()` - Single function for all logging

4. **session_management.php** - Session handling
   - `registerSession()` - Create new session
   - `isSessionDisconnected()` - Check session status
   - `updateSessionActivity()` - Update timestamp
   - `disconnectUserSessions()` - Admin disconnect

### Action Handlers (backend/handlers/)

Each API action has its own handler function:

**schedule_handler.php:**
- `handleGetSchedule()` - GET schedule
- `handleSaveSchedule()` - POST schedule

**settings_handler.php:**
- `handleGetSettings()` - GET settings
- `handleSaveSettings()` - POST settings
- `handleGetPinCode()` - GET PIN

**audit_log_handler.php:**
- `handleGetAuditLog()` - GET logs

**session_handler.php:**
- `handleRegisterSession()` - POST session
- `handleCheckSession()` - GET session status
- `handleGetSessions()` - GET all sessions
- `handleDisconnectUser()` - POST disconnect

## Benefits

### 1. Maintainability
- Easy to find specific functionality
- Changes are isolated to relevant files
- Clear separation of concerns

### 2. Readability
- Smaller, focused files
- Self-documenting structure
- Consistent patterns throughout

### 3. Testability
- Individual functions can be tested
- Mock dependencies easily
- Clear inputs and outputs

### 4. Extensibility
- Add new handlers without touching existing code
- Reuse helper functions
- Clear patterns to follow

### 5. Debugging
- Errors point to specific files
- Stack traces are more meaningful
- Easier to isolate issues

## Adding New Features

### Example: Add a new API action

**Step 1:** Create handler function
```php
// backend/handlers/my_handler.php
function handleMyAction() {
    $input = file_get_contents('php://input');
    $data = validateJsonInput($input);
    
    if ($data === null) {
        sendErrorResponse('Invalid JSON');
    }
    
    // Your logic here
    
    sendSuccessResponse(['result' => 'success']);
}
```

**Step 2:** Register in api.php
```php
// Load your handler
require_once __DIR__ . '/backend/handlers/my_handler.php';

// Add to switch statement
case 'myAction':
    handleMyAction();
    break;
```

**Step 3:** Test
```bash
curl -X POST "http://localhost:8000/api.php?action=myAction" \
  -H "Content-Type: application/json" \
  -d '{"key": "value"}'
```

## Error Handling Improvements

The new structure provides better error handling:

### Before:
```php
if (!file_exists($file)) {
    echo '{}';
} else {
    echo file_get_contents($file);
}
```

### After:
```php
$data = readJsonFile($file, []);
echo json_encode($data);
```

Benefits:
- Automatic error handling
- Consistent return types
- Proper JSON encoding
- Safe defaults

## Performance

The modular structure has minimal performance impact:
- Files are loaded once per request
- No additional database queries
- Same number of file operations
- Efficient opcode caching

## Testing

All endpoints have been tested and verified:

✅ GET requests
- getSettings
- getSchedule
- getAuditLog
- getSessions
- checkSession
- getPinCode

✅ POST requests
- saveSettings
- saveSchedule
- registerSession
- disconnectUser

✅ Error handling
- Invalid actions
- Missing parameters
- Invalid JSON
- Time range validation

✅ Data persistence
- Settings saved correctly
- Schedules saved correctly
- Audit logs updated
- Sessions tracked

## Rollback

If needed, rolling back is simple:

```bash
# Restore original api.php
cp api_original.php api.php

# Remove backend directory (optional)
rm -rf backend/
```

The `api_original.php` backup ensures you can always revert to the previous version.

## Support

For questions or issues:
1. Check backend/README.md for detailed documentation
2. Review handler files for implementation examples
3. Compare with api_original.php for reference

## Summary

The backend refactoring provides a solid foundation for future development while maintaining complete compatibility with existing code. The modular structure makes the codebase easier to understand, maintain, and extend.
