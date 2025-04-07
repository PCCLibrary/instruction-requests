# Library Instruction System Reference Document

## System Overview

The Library Instruction System is a Laravel 11 web application designed to streamline the process of scheduling and managing library instruction sessions between faculty and librarians at Portland Community College.

## Technical Stack
- Framework: Laravel 11
- Frontend: Livewire 3, Alpine.js, Tailwind CSS, Livewire PowerGrid 6.1
- File Management: Spatie Media Library
- UI Components: Custom Blade components
- File Upload: Dropzone.js
- Authentication: Standard Laravel authentication

## Core Features

### Request Workflow
1. Faculty submits instruction request via public form
2. System creates/links instructor record
3. Request enters `received` status
4. Request can be assigned to a librarian (status → `assigned`)
5. Librarian can accept or reject (status → `accepted` or back to `received`)
6. Request can be marked as scheduled with Google Calendar (status → `scheduled`)
7. Request can be marked completed (status → `completed`)

### Request Statuses
- `received`: Initial state when faculty submits request
- `assigned`: Request assigned to a librarian
- `accepted`: Librarian confirmed they'll teach the session
- `scheduled`: Calendar event has been created (new)
- `rejected`: Librarian rejected the assignment (new/planned)
- `completed`: Session has been completed
- `copied`: Duplicate of existing request (for repeat sessions)

### File Management
- Token-based secure file uploads with Dropzone.js
- Temporary file storage & automatic cleanup (24-hour expiration)
- Progressive file path structure:
  - `uploads/temp/`: Temporary uploads
  - `uploads/YYYY/MM/`: New date-based organization (since March 2025)
  - `uploads/{request_id}/`: Legacy structure (pre-March 2025)
- Support for PDF, Word, PowerPoint, and text documents

## Components & Models

### Key Models
- `InstructionRequests`: Main request with basic info
- `InstructionRequestDetails`: Extended request information
- `Instructor`: Faculty member information
- `Classes`: Course information
- `Campus`: Location information
- `User`: Librarian accounts
- `GoogleCalendarEvent`: Calendar event mapping (new)
- `TemporaryUpload`: Token-based file upload management

### Implemented Services
- `InstructionRequestService`: Manages request lifecycle
- `InstructionRequestDetailsService`: Handles extended request info
- `CalendarService`: Google Calendar integration (new)
- `DepartmentService`: Department data management
- `MediaController`: File upload handling

## Feature Status

### Completed Features
- ✅ Request creation and management
- ✅ Status update workflow
- ✅ File attachments with token-based security
- ✅ Dashboard views for librarians
- ✅ Instructor management
- ✅ Custom Path Generator for file organization
- ✅ File cleanup and management

### In Progress Features
- 🔄 Google Calendar integration
  - Data model and service layer implemented
  - UI components partially implemented
  - Event creation/deletion routes added
- 🔄 Refactoring notification system
  - Moving from controllers to service layer
  - Adding support for new status transitions

### Planned Features
- 📝 Full Google Calendar synchronization
- 📝 Enhanced notification system for all status changes
- 📝 Role-based access control
- 📝 Analytics dashboard
- 📝 Admin dashboard for drag-and-drop file management
- 📝 File browser for selecting and reusing existing uploads
- 📝 File versioning for document revisions

## Deployment & Environment

### Environment Requirements
- PHP 8.2+
- Composer dependencies
- Node.js for frontend compilation
- Storage directory permissions
- America/Los_Angeles timezone
- YYYY-MM-DD H:i:s date format

### Deployment Scripts
- `deploy.sh`: Environment-specific deployment automation
- Environment files: .env.local, .env.testing, .env.production
- Automatic SELinux context configuration for RHEL/CentOS/Fedora

## Common Issues & Troubleshooting

### File Upload Issues
- Storage symlink broken or missing (fix: `php artisan storage:link`)
- Directory permissions incorrect (fix: check web server write access)
- Token management issues (check TemporaryUpload records)
- URL generation issues with temporary property

### Configuration Issues
- Media Library disk configuration (check `media-library.php`)
- Filesystem configuration (check `filesystems.php`) 
- Cache conflicts (run `php artisan cache:clear`)

## Maintenance Tasks
- File cleanup: `php artisan media:cleanup-temp`
- Cache clearing: `php artisan cache:clear`
- Storage link verification: `php artisan storage:link`
