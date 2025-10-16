# Backend Architecture Visualization

## Before Refactoring

```
┌─────────────────────────────────────────────────────────────┐
│                         api.php                              │
│                      (393 lines)                             │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ Headers Setup                                         │  │
│  ├──────────────────────────────────────────────────────┤  │
│  │ Configuration Constants                               │  │
│  ├──────────────────────────────────────────────────────┤  │
│  │ Helper Functions:                                     │  │
│  │  • handleFileError()                                  │  │
│  │  • getScheduleFile()                                  │  │
│  │  • addAuditLog()                                      │  │
│  │  • getClientIP()                                      │  │
│  │  • registerSession()                                  │  │
│  │  • isSessionDisconnected()                            │  │
│  │  • updateSessionActivity()                            │  │
│  ├──────────────────────────────────────────────────────┤  │
│  │ File Initialization:                                  │  │
│  │  • Ensure data directory exists                       │  │
│  │  • Create settings.json                               │  │
│  │  • Create audit_log.json                              │  │
│  │  • Create sessions.json                               │  │
│  ├──────────────────────────────────────────────────────┤  │
│  │ Action Router (switch statement):                     │  │
│  │  • case 'getSchedule'        [inline code]           │  │
│  │  • case 'saveSchedule'       [inline code]           │  │
│  │  • case 'getSettings'        [inline code]           │  │
│  │  • case 'saveSettings'       [inline code]           │  │
│  │  • case 'getAuditLog'        [inline code]           │  │
│  │  • case 'disconnectUser'     [inline code]           │  │
│  │  • case 'registerSession'    [inline code]           │  │
│  │  • case 'checkSession'       [inline code]           │  │
│  │  • case 'getSessions'        [inline code]           │  │
│  │  • case 'getPinCode'         [inline code]           │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘

Problems:
❌ Hard to navigate (393 lines)
❌ All code in one file
❌ Mixed concerns
❌ Difficult to test individual functions
❌ Hard to extend
```

## After Refactoring

```
┌─────────────────────────────────────────────────────────────┐
│                       api.php (Entry Point)                  │
│                         (86 lines)                           │
│                                                              │
│  1. Load Configuration                                       │
│  2. Setup Headers                                            │
│  3. Load Helper Functions                                    │
│  4. Load Action Handlers                                     │
│  5. Initialize Files                                         │
│  6. Route to Handler                                         │
└────────────────┬───────────────────────────────┬─────────────┘
                 │                               │
                 ▼                               ▼
    ┌────────────────────────┐      ┌────────────────────────┐
    │   backend/config/      │      │   backend/includes/    │
    │                        │      │                        │
    │  ┌──────────────────┐ │      │  ┌──────────────────┐ │
    │  │  config.php      │ │      │  │ file_operations  │ │
    │  │                  │ │      │  │     .php         │ │
    │  │ • DATA_DIR       │ │      │  │                  │ │
    │  │ • SETTINGS_FILE  │ │      │  │ • readJsonFile() │ │
    │  │ • AUDIT_LOG_FILE │ │      │  │ • writeJsonFile()│ │
    │  │ • SESSIONS_FILE  │ │      │  │ • initFiles()    │ │
    │  │ • Default values │ │      │  └──────────────────┘ │
    │  │ • setupHeaders() │ │      │                        │
    │  └──────────────────┘ │      │  ┌──────────────────┐ │
    └────────────────────────┘      │  │  validation.php  │ │
                                    │  │                  │ │
                                    │  │ • validate*()    │ │
                                    │  │ • sendError()    │ │
                                    │  │ • sendSuccess()  │ │
                                    │  └──────────────────┘ │
                                    │                        │
                                    │  ┌──────────────────┐ │
                                    │  │  audit_log.php   │ │
                                    │  │                  │ │
                                    │  │ • addAuditLog()  │ │
                                    │  └──────────────────┘ │
                                    │                        │
                                    │  ┌──────────────────┐ │
                                    │  │ session_mgmt.php │ │
                                    │  │                  │ │
                                    │  │ • getClientIP()  │ │
                                    │  │ • registerSess() │ │
                                    │  │ • isDisconnect() │ │
                                    │  │ • updateSess()   │ │
                                    │  └──────────────────┘ │
                                    └────────────────────────┘
                 
                 ┌────────────────────────────────────────────┐
                 │         backend/handlers/                  │
                 │                                            │
                 │  ┌──────────────────────────────────────┐ │
                 │  │  schedule_handler.php                 │ │
                 │  │  • handleGetSchedule()                │ │
                 │  │  • handleSaveSchedule()               │ │
                 │  └──────────────────────────────────────┘ │
                 │                                            │
                 │  ┌──────────────────────────────────────┐ │
                 │  │  settings_handler.php                 │ │
                 │  │  • handleGetSettings()                │ │
                 │  │  • handleSaveSettings()               │ │
                 │  │  • handleGetPinCode()                 │ │
                 │  └──────────────────────────────────────┘ │
                 │                                            │
                 │  ┌──────────────────────────────────────┐ │
                 │  │  audit_log_handler.php                │ │
                 │  │  • handleGetAuditLog()                │ │
                 │  └──────────────────────────────────────┘ │
                 │                                            │
                 │  ┌──────────────────────────────────────┐ │
                 │  │  session_handler.php                  │ │
                 │  │  • handleRegisterSession()            │ │
                 │  │  • handleCheckSession()               │ │
                 │  │  • handleGetSessions()                │ │
                 │  │  • handleDisconnectUser()             │ │
                 │  └──────────────────────────────────────┘ │
                 └────────────────────────────────────────────┘

Benefits:
✅ Easy to navigate (8-12 files, each <150 lines)
✅ Clear separation of concerns
✅ Testable components
✅ Reusable helper functions
✅ Easy to extend
✅ Self-documenting structure
```

## File Size Comparison

| File                                 | Lines | Purpose                    |
|--------------------------------------|-------|----------------------------|
| **Original:**                        |       |                            |
| `api.php`                            | 393   | Everything                 |
| **Refactored:**                      |       |                            |
| `api.php`                            | 86    | Entry point & router       |
| `backend/config/config.php`          | 38    | Configuration              |
| `backend/includes/file_operations.php` | 121 | File I/O helpers           |
| `backend/includes/validation.php`    | 94    | Validation & responses     |
| `backend/includes/audit_log.php`     | 16    | Audit logging              |
| `backend/includes/session_management.php` | 124 | Session management     |
| `backend/handlers/schedule_handler.php` | 59 | Schedule operations        |
| `backend/handlers/settings_handler.php` | 37 | Settings operations        |
| `backend/handlers/audit_log_handler.php` | 9 | Audit log retrieval        |
| `backend/handlers/session_handler.php` | 73 | Session endpoints          |
| **Total (new):**                     | 657   | Organized in 10 files      |

**Note:** While the total line count increased (due to better documentation, spacing, and error handling), the code is now:
- Much more maintainable
- Easier to understand
- Better documented
- More robust
- Easier to extend

## Request Flow

### Before:
```
HTTP Request
    ↓
api.php (393 lines)
    ↓
switch($action)
    ↓
Inline code (30-60 lines each case)
    ↓
HTTP Response
```

### After:
```
HTTP Request
    ↓
api.php (entry point)
    ↓
Load config & helpers
    ↓
switch($action)
    ↓
Call handler function
    ↓
    ├─→ Use validation helpers
    ├─→ Use file operation helpers
    ├─→ Use session helpers
    └─→ Use audit log helpers
    ↓
HTTP Response
```

## Module Dependencies

```
api.php (entry point)
    │
    ├─→ backend/config/config.php
    │       (no dependencies)
    │
    ├─→ backend/includes/file_operations.php
    │       └─→ config.php
    │
    ├─→ backend/includes/validation.php
    │       └─→ config.php
    │
    ├─→ backend/includes/audit_log.php
    │       ├─→ config.php
    │       └─→ file_operations.php
    │
    ├─→ backend/includes/session_management.php
    │       ├─→ config.php
    │       ├─→ file_operations.php
    │       └─→ audit_log.php
    │
    └─→ backend/handlers/*.php
            ├─→ config.php
            ├─→ file_operations.php
            ├─→ validation.php
            ├─→ audit_log.php
            └─→ session_management.php
```

## Key Improvements

1. **Separation of Concerns**
   - Configuration separate from logic
   - Helpers separate from handlers
   - Each file has one responsibility

2. **Reusability**
   - Helper functions used by multiple handlers
   - Consistent validation patterns
   - Shared error handling

3. **Maintainability**
   - Find code quickly
   - Change one thing in one place
   - Clear dependencies

4. **Testability**
   - Test individual functions
   - Mock dependencies easily
   - Clear inputs/outputs

5. **Documentation**
   - Self-documenting structure
   - Clear file names
   - Purpose-driven organization
