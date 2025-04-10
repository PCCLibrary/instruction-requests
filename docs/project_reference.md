# Library Instruction System Project Reference

## Project Overview

The Library Instruction System is a Laravel 11 web application designed to streamline the process of scheduling and managing library instruction sessions between faculty and librarians at Portland Community College.

## Core Components

### Key Models
- **InstructionRequests**: Central model for tracking instruction requests
- **InstructionRequestDetails**: Additional details related to requests
- **Instructor**: Faculty member information
- **Classes**: Course information
- **Campus**: Campus location data
- **User**: Librarian accounts
- **GoogleCalendarEvent**: Google Calendar integration
- **TemporaryUpload**: Handles file uploads in public form

### Request Lifecycle
1. Faculty submits instruction request (with optional file attachments)
2. System creates/links instructor record
3. Request enters `received` status
4. Request is assigned to librarian (`assigned` status)
5. Librarian accepts the request (`accepted` status)
6. Librarian schedules the session on Google Calendar (`scheduled` status) - NEW
7. Session is completed (`completed` status)

### File Handling Architecture
- **Temporary Uploads**: Path: `uploads/temp/`
- **Date-Based Organization**: Path: `uploads/YYYY/MM/` (files after March 2025)
- **Legacy Structure**: Path: `uploads/{request_id}/` (files before March 2025)

## Features Status

### Completed Features
- Core request creation and management
- Status management workflow (received → assigned → accepted → completed)
- File attachments with token-based security
- Librarian dashboard with request filtering and management
- Notification system for status changes
- Basic user management and authentication

### Features in Progress
1. **Google Calendar Integration**
   - Added new `scheduled` status
   - Created GoogleCalendarEvent model
   - Implemented CalendarService for event management
   - New database migration for google_calendar_events table
   - Implemented section-based edit toggle system for form fields
   - Enhanced form validation for calendar event creation
   - Fixed Alpine.js binding syntax for proper browser compatibility

2. **File Upload System Enhancements**
   - Implemented token-based security for public uploads
   - Added CustomPathGenerator for better file organization
   - Refactored file association with native Spatie Media Library methods
   - Added database transactions for reliable file operations
   - Fixed URL generation issues for associated files

### Planned Features
1. **Svelte Public Form**
   - Standalone Vite + Svelte application
   - Will be embedded (not integrated) in WordPress site
   - Features: validation, drag & drop uploads, status updates
   - Future capability: view request summary with request ID parameter

## Technical Implementation

### Key Technologies
- **Laravel 11**: Primary framework
- **Livewire 3**: For interactive components
- **Tailwind CSS**: For styling
- **Alpine.js**: Frontend interactions
  - Section-based form field toggling
  - Form change detection and validation
  - Proper binding syntax (x-bind:class, x-bind:readonly)
  - Centralized state management via Alpine.store
- **Spatie Media Library**: File management
- **Dropzone.js**: Drag-and-drop file uploads
- **Google Calendar API**: Calendar integration

### Architecture Patterns
- Repository pattern for data access
- Service pattern for business logic
- Notification system for status changes
- Token-based security for public file uploads

## Environment Information
- Timezone: America/Los_Angeles
- Date Format: YYYY-MM-DD H:i:s
- File Storage: Local filesystem with symbolic links
- Framework: Laravel 11
- PHP Version: 8.2+
