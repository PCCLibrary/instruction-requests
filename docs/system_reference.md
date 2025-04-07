# Library Instruction System Reference Guide

## System Overview

The Library Instruction System is a Laravel 11 web application designed to streamline scheduling and management of library instruction sessions between faculty and librarians at Portland Community College.

## Core Components

### Request Lifecycle

1. Faculty submits instruction request through public form
2. System creates/links instructor record
3. Request enters `received` status
4. Librarians can view, assign, accept, and manage requests
5. Calendar events can be scheduled for sessions

### Request Statuses

- `received`: Initial state when faculty submits request
- `assigned`: Librarian has been assigned to the request
- `accepted`: Librarian has confirmed they'll handle the session
- `scheduled`: Session has been added to Google Calendar
- `completed`: Session has been completed
- `rejected`: Request returned to received state and reassignable
- `copied`: Duplicate of existing request (for repeat sessions)

### Key Models

- `InstructionRequests`: Central model
- `InstructionRequestDetails`: Additional fields for requests
- `Instructor`: Faculty member requesting instruction
- `Campus`: Campus information with Google Calendar links
- `User`: Librarians who manage the requests
- `GoogleCalendarEvent`: Tracking calendar integration

## Completed Features

1. **Core Request Management**
   - Request creation and editing
   - Status management workflow (receive → assign → accept → complete)
   - Request copying for repeat sessions
   - Dashboard views for librarians

2. **File Handling**
   - Spatie Media Library integration
   - Dropzone.js for drag-and-drop uploads
   - Token-based security for unauthenticated uploads
   - Modern file structure (year/month directories)
   - Automatic temporary file cleanup

3. **UI Improvements**
   - Tailwind CSS styling
   - Alpine.js for interactive components
   - Livewire 3 integration
   - Responsive design
   - Blade components for UI consistency

4. **Notification System**
   - Status-based notifications
   - Service-based notification handling
   - Consistent notification triggers

## Features In Progress

1. **Google Calendar Integration**
   - Backend infrastructure is largely complete:
     - Database migration for `google_calendar_events` table
     - `GoogleCalendarEvent` model with relationships
     - `CalendarService` with methods for creating and deleting events
     - Calendar ID extraction from campus URLs
     - `InstructionRequestController` updated to handle event deletion
     - Routes added for calendar event functionality
   - Frontend components still needed:
     - Complete the Livewire component for event creation
     - Finalize Blade template for the component
     - Fully integrate UI elements in the instruction request edit view

2. **File Management Improvements**
   - Consolidated "materials" collection
   - Enhanced file association process
   - More robust error handling
   - Better logging for troubleshooting
   - Custom path generation with separate temp storage

## Features Planned

1. **Standalone Public Form**
   - Vite + Svelte implementation
   - WordPress site embedding
   - Same web root but not integrated with Laravel app
   - Client-side validation
   - Drag & drop file uploads
   - Status updates for successful/failed submissions
   - Future: View request summary with ID parameter

2. **File Browser**
   - Centralized file management
   - Associate existing files with requests
   - Streamlined UI for librarians

## Technical Details

### Environment
- Laravel 11
- PHP 8.2+
- Timezone: America/Los_Angeles
- Livewire 3
- Tailwind CSS
- Alpine.js
- Spatie Media Library

### File Upload Architecture

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

### Deployment Considerations

1. **File Permissions**
   - Storage directory must be writable
   - Symbolic link from `public/storage` to `storage/app/public`
   - Required directories created automatically

2. **Deployment Script**
   - Environment-specific configuration
   - Cache clearing
   - Permission setting
   - SELinux context configuration

### Common Issues & Troubleshooting

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

## Development Notes

- No plans for comprehensive role or permissions enhancements
- Focus on workflow improvements, not feature expansion
- Maintain proof-of-concept/prototype scope
- Always use "materials" collection for file uploads
- Temporary files cleaned up automatically after 24 hours
