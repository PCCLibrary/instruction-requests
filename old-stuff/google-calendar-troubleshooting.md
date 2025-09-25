# Google Calendar Integration Troubleshooting Guide

This guide helps you diagnose and resolve common issues with Google Calendar integration in the Library Instruction System.

## Key Requirements

For the Google Calendar integration to work correctly, especially with attendees, these requirements must be met:

1. **Service Account with Domain-Wide Delegation**
   - The Google API service account must have domain-wide delegation enabled in Google Workspace
   - This allows the service account to impersonate users within your domain

2. **Impersonation User Configuration**
   - The `GOOGLE_CALENDAR_IMPERSONATE_EMAIL` environment variable must be set
   - This user must have access to the calendars being used

3. **Proper OAuth Scopes**
   - The service account must be granted the correct OAuth scopes:
     - `https://www.googleapis.com/auth/calendar` (full access to calendars)
     - `https://www.googleapis.com/auth/calendar.events` (manage events)

4. **Valid Calendar IDs**
   - Each campus must have a valid Google Calendar ID in the format:
     - `c_XXXXX@group.calendar.google.com`

## Common Issues and Solutions

### "Service accounts cannot invite attendees without Domain-Wide Delegation of Authority"

This error occurs when:
- Domain-wide delegation is not enabled for the service account
- The impersonation email is not configured correctly

**Solution:**
1. Verify domain-wide delegation is enabled in Google Workspace Admin Console
2. Check that `GOOGLE_CALENDAR_IMPERSONATE_EMAIL` is set in your `.env` file
3. Run the diagnostic test command to verify configuration:
   ```
   php artisan diagnose:google-calendar-attendees
   ```

### "Invalid credentials" or "Authentication failed"

This indicates an issue with the service account credentials.

**Solution:**
1. Verify the path to the credentials file in `GOOGLE_APPLICATION_CREDENTIALS`
2. Check that the service account has not been disabled
3. Generate new service account credentials if necessary

### "Access denied" or "Permission denied"

This means the service account or impersonation user lacks permission to access the calendar.

**Solution:**
1. Verify the impersonation user has access to the calendar
2. Check that the calendar ID is correctly formatted
3. Add the impersonation user to the calendar with "Make changes AND manage sharing" permissions

### "Calendar not found"

This occurs when the calendar ID is incorrect or the calendar has been deleted.

**Solution:**
1. Verify the calendar ID in the Campus settings
2. Check that the calendar still exists in Google Calendar
3. Create a new calendar if necessary and update the ID

## Diagnostic Testing

When experiencing issues, use the diagnostic command to test the Google Calendar API directly:

```
php artisan diagnose:google-calendar-attendees
```

This command will:
- Show detailed configuration information
- Test the connection to Google Calendar API
- Attempt to create a test event with attendees
- Provide specific error messages and troubleshooting steps

## Testing with a Complete Request

To test the full calendar integration flow with an instruction request:

```
php artisan test:google-calendar-attendees
```

This command:
- Creates a test instruction request with associated data
- Uses the CalendarService to create an event with attendees
- Tests the complete flow as it would occur in the application
- Provides detailed output with diagnostic information

## Checking Logs

When troubleshooting, check these log files for detailed error information:

1. **Laravel Logs**
   ```
   tail -f storage/logs/laravel.log
   ```

2. **Daily Logs (where test commands log data)**
   ```
   tail -f storage/logs/laravel-YYYY-MM-DD.log
   ```

## Common Configuration Issues

### 1. Missing Impersonation Email

If you see this error:
```
Cannot add attendees without proper impersonation configuration
```

Fix by adding to your `.env` file:
```
GOOGLE_CALENDAR_IMPERSONATE_EMAIL=librarian@pcc.edu
```

### 2. Invalid Credentials Path

If you see authentication errors, check the credentials path:
```
GOOGLE_APPLICATION_CREDENTIALS=/absolute/path/to/credentials.json
```

### 3. Calendar ID Format

Calendar IDs should be in this format:
```
c_91e4e2a58503a3f41186894f62e70d8f904be3e72af56f6059fb4f0220b7dbc4@group.calendar.google.com
```

You can find the correct ID by:
1. Going to the calendar in Google Calendar
2. Clicking the three dots next to the calendar name
3. Selecting "Settings and sharing"
4. Scrolling down to "Integrate calendar"
5. Copying the "Calendar ID" value

## Google API Dashboard

You can also check the Google API Dashboard for API errors:

1. Go to the [Google Cloud Console](https://console.cloud.google.com/)
2. Navigate to "APIs & Services" > "Dashboard"
3. Select the project associated with your service account
4. Check for any API errors or quota issues

## Contacting Support

If you've tried all the troubleshooting steps and still experiencing issues:

1. Save all error messages and log data
2. Note which test commands you've run and their output
3. Contact the system administrator with this information
