# Backend Architecture

This directory contains the refactored backend code for the Desk Reservation System.

## Structure

```
backend/
├── config/
│   └── constants.php         # Application configuration and constants
├── controllers/
│   ├── AuditController.php   # Audit log operations
│   ├── ScheduleController.php # Schedule/reservation operations
│   ├── SessionController.php  # User session management
│   └── SettingsController.php # Application settings
├── helpers/
│   ├── AuditHelper.php       # Audit log utility functions
│   ├── FileHelper.php        # File operations utilities
│   └── SessionHelper.php     # Session management utilities
├── models/
│   ├── ScheduleModel.php     # Schedule data access layer
│   └── SettingsModel.php     # Settings data access layer
├── bootstrap.php             # Application initialization
└── Router.php               # API request router
```

## Key Improvements

### 1. Separation of Concerns
- **Controllers**: Handle HTTP request/response logic
- **Models**: Manage data access and business logic
- **Helpers**: Provide reusable utility functions
- **Config**: Centralized configuration

### 2. Maintainability
- Each component has a single responsibility
- Easy to locate and modify specific functionality
- Reduced code duplication

### 3. Scalability
- Easy to add new endpoints via Router
- Simple to extend with new controllers/models
- Clear dependencies between components

### 4. Error Handling
- Centralized error handling in FileHelper
- Consistent error response format
- Better logging and debugging

## How It Works

1. **api.php** is the entry point that:
   - Sets response headers
   - Loads the bootstrap file
   - Initializes the router
   - Handles the incoming request

2. **Router** maps actions to controller methods:
   - `getSchedule` → ScheduleController::getSchedule()
   - `saveSettings` → SettingsController::saveSettings()
   - etc.

3. **Controllers** handle the request:
   - Validate input
   - Call appropriate Model methods
   - Format and return response

4. **Models** interact with data:
   - Read/write JSON files via FileHelper
   - Apply business logic (e.g., booking validation)
   - Return data or error messages

5. **Helpers** provide utilities:
   - File operations (FileHelper)
   - Session management (SessionHelper)
   - Audit logging (AuditHelper)

## Adding New Endpoints

To add a new API endpoint:

1. Create or update a Controller with the handler method
2. Register the route in Router::initializeRoutes()
3. Optionally create/update Model methods for data access
4. Use Helpers for common operations

Example:
```php
// In Router.php
self::register('myNewAction', 'MyController', 'myMethod');

// In controllers/MyController.php
class MyController {
    public static function myMethod() {
        // Handle request
        $data = MyModel::getData();
        echo json_encode($data);
    }
}
```

## Configuration

Edit `backend/config/constants.php` to change:
- Directory paths
- Default settings
- Application constants

## Testing

Start PHP development server:
```bash
php -S localhost:8080
```

Test endpoints:
```bash
# Get settings
curl http://localhost:8080/api.php?action=getSettings

# Get schedule
curl http://localhost:8080/api.php?action=getSchedule&yearMonth=202501
```

## Backward Compatibility

The refactored backend maintains 100% API compatibility with the original monolithic version. All existing frontend code works without modification.
