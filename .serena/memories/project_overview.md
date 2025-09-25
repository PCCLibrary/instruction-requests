# Library Instruction System - Project Overview

## Purpose
A Laravel 11 web application for streamlining the scheduling and management of library instruction sessions between faculty and librarians at Portland Community College.

## Tech Stack

### Backend
- **Framework**: Laravel 11
- **PHP Version**: 8.2+
- **Key Packages**:
  - Livewire 3 (interactive components)
  - Spatie Laravel Media Library (file management)
  - Spatie Laravel Google Calendar (calendar integration)
  - PowerGrid 6.1 (advanced data tables)
  - Laravel Socialite + SAML2 (authentication)
  - TestMonitor Eloquent Lockable (edit locking)
  - Lakm Laravel Comments (commenting system)

### Frontend
- **CSS Framework**: Tailwind CSS
- **JavaScript**: Alpine.js
- **UI Components**: Blade components with Blade UI Kit
- **Additional Libraries**:
  - Choices.js (enhanced selects)
  - Flatpickr (date/time picker)
  - SortableJS (drag & drop)

### Development Tools
- Vite (build tool)
- Pest (testing framework)
- Composer (PHP dependencies)
- NPM (JavaScript dependencies)

## Project Structure

### Core Directories
- `app/Models` - Eloquent models
- `app/Http/Controllers` - Request handlers
- `app/Services` - Business logic layer
- `app/Repositories` - Data access layer
- `app/Livewire` - Livewire components
- `app/Notifications` - Email notifications
- `app/ValueObjects` - Immutable data objects
- `resources/views` - Blade templates
- `resources/js` - JavaScript files
- `resources/css` - CSS files
- `routes` - Application routes
- `config` - Configuration files
- `database/migrations` - Database schema

### Key Models
- **InstructionRequests**: Central model for instruction sessions
- **Instructor**: Faculty representation
- **User**: Librarian accounts
- **Classes**: Course information
- **Campus**: Campus locations
- **GoogleCalendarEvent**: Calendar event records

## Request Lifecycle

### Status Flow
1. `received` - Initial state when faculty submits request
2. `assigned` - Librarian has been assigned
3. `accepted` - Librarian confirmed assignment
4. `scheduled` - Event created in Google Calendar
5. `completed` - Session finished
6. `rejected` - Request declined (can be reassigned)

### Core Features
- Request submission via public form
- File attachments (syllabi, materials)
- Email notifications
- Google Calendar integration
- Edit locking system
- Comment system with reactions
- Dashboard with filtering

## File Upload System

### Supported Types
- PDF (.pdf)
- Word Documents (.doc, .docx)
- PowerPoint (.ppt, .pptx)
- Text files (.txt, .rtf)

### Specifications
- Maximum file size: 20MB per file
- Maximum files per request: 4
- Storage: Spatie Media Library with date-based organization
- Token-based security for public uploads

## Environment Configuration

### Available Environments
- `.env` - Active environment
- `.env.local` - Local Docker development
- `.env.testing` - PCC test server
- `.env.production` - PCC production server

### Deployment
Use `./deploy.sh [local|testing|production]` to switch environments

## Google Calendar Integration

### Configuration Requirements
- Service account credentials
- Domain-wide delegation
- Impersonation email
- Campus calendar IDs

### Test Commands
- `php artisan test:google-calendar-attendees` - Full integration test
- `php artisan diagnose:google-calendar-attendees` - API diagnostics

## System Timezone
- Application: America/Los_Angeles
- Date Format: YYYY-MM-DD H:i:s