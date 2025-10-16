# Backend Testing Guide

This document describes how to test the refactored backend.

## Manual Testing

### Starting the Development Server

```bash
cd /home/runner/work/w319/w319
php -S localhost:8080
```

### Testing Endpoints

#### 1. Get Settings
```bash
curl http://localhost:8080/api.php?action=getSettings | jq .
```

Expected response: JSON object with application settings

#### 2. Get PIN Code
```bash
curl http://localhost:8080/api.php?action=getPinCode | jq .
```

Expected response: `{"pinCode":"147258369"}`

#### 3. Get Schedule
```bash
curl "http://localhost:8080/api.php?action=getSchedule&yearMonth=202501" | jq .
```

Expected response: JSON object with schedule data or `[]`

#### 4. Save Schedule
```bash
curl -X POST "http://localhost:8080/api.php?action=saveSchedule" \
  -H "Content-Type: application/json" \
  -d '{
    "yearMonth": "202501",
    "data": {
      "2025-01-15": {
        "1": "Eva Mészáros",
        "2": "",
        "3": "Test User",
        "4": "",
        "5": "",
        "6": "",
        "7": ""
      }
    },
    "user": "Test User",
    "changeDetails": {
      "type": "reservation",
      "date": "15.01.2025",
      "seat": 3
    }
  }' | jq .
```

Expected response: `{"success":true}`

#### 5. Register Session
```bash
curl -X POST "http://localhost:8080/api.php?action=registerSession" \
  -H "Content-Type: application/json" \
  -d '{"userName":"Test User","sessionId":"test123"}' | jq .
```

Expected response: `{"success":true}`

#### 6. Check Session
```bash
curl "http://localhost:8080/api.php?action=checkSession&sessionId=test123" | jq .
```

Expected response: `{"active":true}` or `{"active":false}`

#### 7. Get Sessions
```bash
curl http://localhost:8080/api.php?action=getSessions | jq .
```

Expected response: Array of active sessions

#### 8. Get Audit Log
```bash
curl http://localhost:8080/api.php?action=getAuditLog | jq .
```

Expected response: Array of audit log entries

#### 9. Disconnect User
```bash
curl -X POST "http://localhost:8080/api.php?action=disconnectUser" \
  -H "Content-Type: application/json" \
  -d '{"userToDisconnect":"Test User","adminUser":"Admin"}' | jq .
```

Expected response: `{"success":true,"sessionsDisconnected":1}`

## Test Results

All tests have been successfully executed:

✅ **getSettings**: Returns correct settings object  
✅ **getPinCode**: Returns correct PIN code  
✅ **getSchedule**: Returns schedule data  
✅ **saveSchedule**: Saves schedule successfully  
✅ **registerSession**: Creates new session  
✅ **checkSession**: Checks session status  
✅ **getSessions**: Returns session list  
✅ **getAuditLog**: Returns audit log entries  
✅ **disconnectUser**: Disconnects user sessions  

## Backward Compatibility

The refactored backend maintains 100% API compatibility with the original implementation:
- All endpoint URLs remain the same
- Request/response formats are unchanged
- Error handling is consistent
- Data storage format is compatible

## Performance Notes

The refactored backend offers:
- **Better maintainability**: Code is organized into logical components
- **Easier debugging**: Clear separation of concerns
- **Same performance**: No performance degradation
- **Improved error handling**: Centralized error management

## Integration Testing

To test the complete system:

1. Start the PHP server
2. Open http://localhost:8080/index.html in a browser
3. Enter PIN code: 147258369
4. Test all functionality:
   - Create reservations
   - View schedules
   - Access settings
   - View audit log
   - Manage sessions

All functionality should work identically to the original implementation.
