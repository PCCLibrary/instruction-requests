# Library Instruction Request System

A web application designed to streamline the scheduling and management of library instruction sessions between faculty and librarians at Portland Community College.

## Overview

The Library Instruction Request System provides a simple public form for faculty to submit instruction requests and a comprehensive dashboard for librarians to manage, assign, and track these requests through their complete lifecycle.

**Current Status**: Production (v1.0)
**Framework**: Laravel 11 with Livewire 3

## Key Features

### For Faculty
- Simple web form for submitting instruction requests
- Course information and preferred time submission
- File upload support (syllabi, materials)
- Email notifications for request status updates

### For Librarians
- Personalized dashboard for viewing requests
- Request assignment and acceptance workflow
- Google Calendar integration for scheduling
- File management (upload/download materials)
- Comment system for collaboration
- Edit locking to prevent concurrent editing conflicts

### For Administrators
- Assignment availability toggle for testing/coverage
- Lock management (monitor and unlock stale locks)
- Queue monitoring and management
- Cache management tools
- System maintenance utilities

## Technology Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS
- **Authentication**: Microsoft Entra SAML 2.0
- **Database**: MySQL
- **File Storage**: Spatie Media Library
- **Calendar**: Google Calendar API
- **Data Tables**: PowerGrid 6.1
- **Edit Locking**: TestMonitor Eloquent Lockable

## Documentation

- **[User Guide](user-guide.md)** - For faculty and librarians using the system
- **[Admin Guide](admin-guide.md)** - For system administrators
- **[Technical Architecture](technical-architecture.md)** - For developers and AI assistants
- **[Deployment Guide](deployment-guide.md)** - Setup and deployment instructions

## Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL database
- Web server (Apache/Nginx)

### Installation

```bash
# Clone the repository
git clone [repository-url]

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Create storage link
php artisan storage:link

# Build assets
npm run build
```

For detailed deployment instructions, see the [Deployment Guide](deployment-guide.md).

## Core Request Workflow

1. **Received** - Faculty submits request via public form
2. **Assigned** - Scheduling librarian assigns to teaching librarian
3. **Accepted** - Teaching librarian confirms the assignment
4. **Scheduled** - Session added to Google Calendar
5. **Completed** - Session has been conducted

Requests can also be **Rejected** and reassigned if needed.

## Project Structure

```
app/
├── Http/Controllers/     # Request handling
├── Livewire/            # Interactive components
├── Models/              # Database models
├── Notifications/       # Email notifications
├── Repositories/        # Data access layer
├── Services/            # Business logic
└── ValueObjects/        # Immutable data containers

resources/
├── views/               # Blade templates
└── js/                  # JavaScript/Alpine.js

docs/                    # Documentation
```

## Development

### Local Development

```bash
# Start development server
php artisan serve

# Watch for asset changes
npm run dev

# Run tests
php artisan test
```

### Key Commands

```bash
# Clear all caches
./clear-caches.sh

# Deploy to test server
./deploy.sh testing

# Deploy to production
./deploy.sh production

# Manage queue workers
php artisan queue:work
php artisan queue:restart
```

## Environment Configuration

The system supports three environments:
- `.env.local` - Docker development
- `.env.testing` - PCC test server
- `.env.production` - PCC production server

## System Requirements

- **PHP**: 8.2 or higher
- **MySQL**: 5.7 or higher
- **Node.js**: 18.x or higher
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Storage**: 10GB minimum for file uploads
- **Memory**: 512MB PHP memory limit recommended

## Security

- VPN-restricted access for production environment
- SAML 2.0 authentication via Microsoft Entra ID
- Token-based security for public file uploads
- Manual librarian account management

## Support & Troubleshooting

For common issues and solutions, see:
- [User Guide - Troubleshooting](user-guide.md#troubleshooting)
- [Admin Guide - Troubleshooting](admin-guide.md#troubleshooting)

## Contributing

### Development Workflow

For detailed development guidelines, see the [Technical Architecture](technical-architecture.md) documentation.

### Code Standards

- **Indentation**: 4 spaces (no tabs)
- **PHP**: Strict types, PSR-12 coding standards
- **Naming**: PascalCase for classes, camelCase for methods, snake_case for database columns
- **Documentation**: PHPDoc for all public methods

## Project History

- **2024**: Initial development (Laravel 8, AdminLTE)
- **Early 2025**: Modernization to Laravel 11 + Livewire 3
- **May 2025**: Edit lock system and Google Calendar integration
- **June 2025**: Notification system optimization
- **July-Aug 2025**: Microsoft Entra SAML migration
- **Sept 2025**: Production deployment and documentation update

## License

Internal Portland Community College project - not licensed for external use.

## Contact

For questions or support, contact the PCC Library Technology team.

---

**Last Updated**: September 25, 2025
**Version**: 1.0
**Status**: Production Ready
