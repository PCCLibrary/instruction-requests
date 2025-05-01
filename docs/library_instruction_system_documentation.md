# Library Instruction System - Comprehensive Documentation

## 1. System Overview

The Library Instruction System is a web application designed to streamline the process of scheduling and managing library instruction sessions between faculty and librarians at Portland Community College.

The system allows faculty to submit instruction requests through a public form, while providing librarians with tools to manage, assign, and track these requests through a complete workflow.

## 2. Application Workflow

### 2.1 Request Lifecycle

1. **Request Submission**: Faculty submits an instruction request through the public form
   - Provides course information, preferred dates/times, and instruction needs
   - Can attach files like syllabi or assignment descriptions
   - System automatically creates or links to existing instructor record

2. **Initial Request Processing**
   - Request enters `received` status in the system
   - Scheduling librarians at the relevant campus are notified
   - Instructor receives confirmation of submission

3. **Request Assignment**
   - Scheduling librarians review incoming requests
   - Assign requests to appropriate teaching librarians based on subject expertise and availability
   - Assigned librarian receives notification of assignment
   - Request status changes to `assigned`

4. **Request Acceptance**
   - Assigned librarian reviews request details
   - Accepts the assignment (or can reject if necessary)
   - Instructor receives notification when librarian accepts
   - Request status changes to `accepted`

5. **Calendar Scheduling**
   - Librarian schedules the session in Google Calendar
   - System links the calendar event to the request
   - Request status changes to `scheduled`

6. **Session Completion**
   - After instruction session occurs, librarian marks request as complete
   - Request status changes to `completed`

### 2.2 Request Statuses

- `received`: Initial state when faculty submits request
- `assigned`: Librarian has been assigned to the request
- `accepted`: Librarian has confirmed they'll handle the session
- `scheduled`: Session has been added to Google Calendar
- `completed`: Session has been completed
- `rejected`: Request has been rejected and can be reassigned

The system also has functionality to duplicate requests for repeat sessions, though this feature is currently disabled.

### 2.3 File Uploads

Faculty and librarians can upload and share files through the system:

- **Faculty can upload**:
  - Course syllabi
  - Assignment descriptions
  - Other instructional materials

- **Librarians can upload**:
  - Teaching materials
  - Assessment documents
  - Handouts and research guides

Files can be downloaded or viewed from the request edit page.

### 2.4 Notification System

The system automatically sends notifications to keep all parties informed:

1. **New Request Created**
   - Instructor receives confirmation
   - Scheduling librarians at the campus are notified

2. **Request Assigned to Librarian**
   - Assigned librarian receives notification

3. **Request Accepted by Librarian**
   - Instructor receives confirmation

4. **Request Rejected**
   - Campus scheduling librarians receive notification

## 3. Upcoming Enhancements

### 3.1 Google Calendar Integration

- Seamless creation of calendar events from instruction requests
- Automatic updates when request details change
- Dashboard calendar view for librarians
- Bulk scheduling for multiple sessions

### 3.2 Standalone Public Form

- Modern drag & drop interface for file uploads
- Embedded in WordPress site for easy faculty access
- Real-time feedback during form submission
- Summary view of submitted requests

### 3.3 File Management Improvements

- Centralized file browser
- Easily associate existing files with requests
- Improved organization for teaching materials

### 3.4 Additional Enhancements

- Admin dashboard for drag-and-drop uploads
- Analytics dashboard for instruction statistics

## 4. Technical Details

### 4.1 Core Components

#### 4.1.1 Key Models
- **InstructionRequests**: Central model for tracking instruction requests
- **InstructionRequestDetails**: Additional details related to requests
- **Instructor**: Faculty member information
- **Classes**: Course information
- **Campus**: Campus location data with Google Calendar links
- **User**: Librarian accounts
- **GoogleCalendarEvent**: Tracking Google Calendar integration
- **TemporaryUpload**: Handles file uploads in public form

#### 4.1.2 Environment Information
- Framework: Laravel 11
- PHP Version: 8.2+
- Timezone: America/Los_Angeles
- Date Format: YYYY-MM-DD H:i:s
- File Storage: Local filesystem with symbolic links
- Access: Internal VPN network

### 4.2 Technical Implementation

#### 4.2.1 Key Technologies
- **Laravel 11**: Primary framework
- **Livewire 3**: For interactive components
- **Tailwind CSS**: For styling
- **Alpine.js**: Frontend interactions
  - Section-based form field toggling with centralized configuration
  - Form change detection and validation
  - Proper binding syntax (x-bind:class, x-bind:readonly)
  - Centralized state management via Alpine.store
  - Conditional UI feedback with proper Alpine.js directives
- **Spatie Media Library**: File management
- **Dropzone.js**: Drag-and-drop file uploads
- **Google Calendar API**: Calendar integration

#### 4.2.2 Architecture Patterns
- Repository pattern for data access
- Service pattern for business logic
- Notification system for status changes
- Token-based security for public file uploads

### 4.3 File Handling Architecture

#### 4.3.1 File Upload Security
- Token-based security for unauthenticated uploads
- Tokens valid for 120 minutes
- Files initially uploaded to temporary storage
- Associated with requests upon form submission
- Files automatically removed after 24 hours if not associated

#### 4.3.2 File Structure

1. **Temporary Uploads (Public Form)**
   - Path: `uploads/temp/`
   - Files initially stored here
   - Associated with a request upon submission
   - Have a `temporary` custom property set to `true`

2. **Date-Based Organization (Dashboard)**
   - Path: `uploads/YYYY/MM/`
   - Organized by upload date
   - Only a single "materials" collection
   - No `temporary` property
   - For files uploaded after March 1, 2025

3. **Legacy Structure**
   - Path: `uploads/{request_id}/`
   - Files uploaded before March 2025
   - Maintained for compatibility

#### 4.3.2 Supported File Types & Restrictions

- **Supported File Types**
  - Plain text (.txt)
  - Rich Text Format (.rtf)
  - Portable Document Format (.pdf)
  - Microsoft Word Documents (.doc, .docx)
  - Microsoft PowerPoint Presentations (.ppt, .pptx)

- **Upload Restrictions**
  - Maximum file size: 20MB per file
  - Maximum 4 files per upload session
  - File collections:
    - Course syllabi
    - Instructor attachments
    - Teaching materials
    - Assessment documents

### 4.4 Notification System Technical Details

#### 4.4.1 Notification Architecture
- Uses Laravel's native notification system
- Service-based approach in InstructionRequestService
- Triggered automatically during status transitions
- Includes comprehensive logging for troubleshooting
- Exception handling for notification failures

#### 4.4.2 Notification Types and Recipients

1. **New Request Created** (status change from '' to 'received')
   - **Notification Class:** RequestReceivedNotification
   - **Recipients:** 
     - The instructor who submitted the request
     - All librarians assigned to the campus where the instruction will take place

2. **Request Assigned to Librarian** (status change to 'assigned')
   - **Notification Class:** RequestAssignedNotification
   - **Recipients:** 
     - The librarian who has been assigned to the request

3. **Request Accepted by Librarian** (status change to 'accepted')
   - **Notification Class:** RequestAcceptedNotification
   - **Recipients:** 
     - The instructor who submitted the request

4. **Request Rejected** (status change from 'assigned' to 'rejected')
   - **Notification Class:** RequestRejectedNotification
   - **Recipients:** 
     - All librarians assigned to the campus where the instruction would have taken place

5. **Request Scheduled** (status change to 'scheduled')
   - No notifications currently sent for this status change
   - Status change is logged for audit purposes

6. **Request Rejected and Returned to Received Status** (status change from 'assigned' to 'received')
   - No notifications currently sent for this status change
   - Status change is logged for audit purposes

#### 4.4.3 Implementation Details
- Notifications are triggered in the `handleStatusChange` method of InstructionRequestService
- Each notification includes the request ID, old status, and new status
- Notifications are sent asynchronously
- Comprehensive logging captures all notification attempts and failures
- The system logs context data including:
  - Request ID
  - Old and new status
  - Who made the change (user ID or "system")
  - Timestamp of change
  - Instructor ID
  - Librarian ID (if assigned)
  - Campus ID

### 4.5 Google Calendar Integration Technical Details

#### 4.5.1 Implementation
- **Google Calendar Events Model**: Tracks created events with request associations
- **Calendar Service**: Handles calendar operations with Google Calendar API
- **Livewire Components**: For event creation and deletion
- **Alpine.js Form Validation**: Ensures proper calendar event creation conditions

#### 4.5.2 Event Flow
1. Librarian accepts instruction request (status: accepted)
2. Form validation ensures datetime and duration are set and saved
3. Librarian clicks "Create Google Calendar Event" button
4. Event creation form populates with saved instruction data
5. Event is created in Google Calendar via API
6. Request status updates to "scheduled"

#### 4.5.3 Required Configuration
- Google API credentials for service account
- Spatie Laravel Google Calendar package
- Campus models with calendar IDs

### 4.6 System Requirements & Installation

#### 4.6.1 System Requirements
- PHP 8.2+
- Composer dependencies
- Node.js for frontend compilation
- Proper storage directory permissions

#### 4.6.2 Basic Setup
```bash
# Clone the repository
git clone [repository-url]

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Compile assets
npm run dev

# Run migrations
php artisan migrate

# Create storage link
php artisan storage:link
```

#### 4.6.3 File Upload Setup
Ensure proper file permissions for uploads:

```bash
# Set storage permissions
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage

# Create uploads directory
mkdir -p public/storage/uploads
mkdir -p public/storage/uploads/temp
sudo chown -R www-data:www-data public/storage
sudo chmod -R 775 public/storage
```

#### 4.6.4 Scheduled Tasks
```bash
# Add to server crontab
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

This ensures temporary files are cleaned up and other scheduled tasks run properly.

### 4.7 Maintenance & Troubleshooting

#### 4.7.1 File Cleanup
```bash
# Manual cleanup of temporary files
php artisan media:cleanup-temp
```

#### 4.7.2 Cache Management
```bash
# Clear application cache
php artisan cache:clear

# Clear route cache
php artisan route:clear

# Clear config cache
php artisan config:clear
```

#### 4.7.3 Common Issues & Troubleshooting

1. **File Uploads**
   - Storage symlink issues
   - Directory permissions
   - Token management
   - MediaLibrary configuration
   - URL generation for temporary files

2. **Performance**
   - Check for cached views or routes
   - Verify configuration cache status
   - Monitor log for slow queries

3. **Alpine.js Issues**
   - Use x-bind:class instead of :class to avoid parsing errors

4. **Debugging**
   ```bash
   # View application logs
   tail -f storage/logs/laravel.log
   ```

### 4.8 Security Considerations

- VPN-restricted access
- Manual user management
- Token-based file upload security
- File upload restrictions
- Soft delete implementations

## 5. Features In Progress

### 5.1 Google Calendar Integration
- Backend infrastructure is largely complete
- Frontend components being implemented
- Will enable direct creation of calendar events from requests
- Alpine.js form validation prevents scheduling with unsaved changes
- Section-based edit toggle system for form fields
- Calendar events can be created and deleted (updating will be added later)
- Integration with campus-specific Google Calendars

### 5.2 File Management Improvements
- Consolidating file collections for better organization
- Refactored file association to use Spatie Media Library's native methods
- Added database transactions for reliable file operations
- Improved error handling and recovery for failed uploads
- Better logging for troubleshooting
- Fixed URL generation issues for associated files
- Enhanced temporary file cleanup process

## 6. Development Notes

- **No plans for role or permissions enhancements** - Role-based access control is explicitly out of scope
- Focus on workflow improvements, not feature expansion
- Maintain proof-of-concept/prototype scope
- Always use "materials" collection for file uploads
- Temporary files cleaned up automatically after 24 hours

## 7. Context for LLMs

This section is designed to preserve important context for language models assisting with this project.

### 7.1 Project Evolution Context

The Library Instruction System has evolved from a Laravel 8 application using AdminLTE and Yajra DataTables to the current Laravel 11 implementation using Livewire 3, Tailwind CSS, Alpine.js and more modern architecture patterns. This evolution represents a significant modernization effort while maintaining the core functionality.

### 7.2 Code Organization and Patterns

- The codebase follows service-repository pattern
- The `InstructionRequestService` contains core business logic for request management
- Notification logic is centralized in the `handleStatusChange` method
- File handling uses Spatie Media Library with custom path generators
- Alpine.js is used with specific binding syntax (x-bind:class instead of :class)
- Form sections and toggles use Alpine.store for centralized state management

### 7.3 Key Technical Considerations

- March 1, 2025 is an important date boundary for file storage paths
- The system uses token-based authentication for file uploads
- Status transitions trigger notifications to different stakeholders
- "Scheduling librarians" assign requests to teaching librarians
- Campus models contain Google Calendar IDs
- DB transactions are used for file operations to maintain data integrity
- VPN access restriction is a key security measure

### 7.5 Key Implementation Details for Future Development

#### 7.5.1 File Upload System
- The system uses a token-based approach with 120-minute validation window
- Tokens are stored in the `TemporaryUpload` model with expiration tracking
- File icons are mapped using Font Awesome classes
- Custom path generator handles both temporary and permanent storage paths
- API endpoints exist for token generation, upload, and deletion operations
- Common file upload issues typically relate to symlinks, permissions, or MediaLibrary configuration

#### 7.5.2 Google Calendar Integration
- Uses Spatie Laravel Google Calendar package with a service account
- Calendar IDs are extracted from campus URLs using regex patterns
- The system prevents event creation if the form has unsaved changes
- Schedule button is conditionally disabled using Alpine.js state tracking
- Livewire components handle the event creation modal and form submission
- Calendar events are soft-deleted when removed

#### 7.5.3 Svelte Public Form
- Being developed as a Svelte 5 application using Vite for compilation
- Will be compiled to a single embed.js file for WordPress integration
- Uses Svelte 5 runes for state management (let, $state, $derived, $effect)
- Communicates with the existing Laravel API endpoints
- Implementation includes custom form field components
- Uses proper fetch API for CSRF token handling and file uploads