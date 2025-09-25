# Architectural Patterns and Design Decisions

## Service Layer Architecture

### InstructionRequestService
**Purpose**: Centralized business logic for instruction request operations

**Key Responsibilities**:
- CRUD operations for requests
- Status change management
- Notification orchestration
- File attachment handling
- Lock management

**Pattern**: Single service for core entity operations

### NotificationService (Planned)
**Purpose**: Handle notification data preparation and dispatch

**Key Responsibilities**:
- Load request data with relationships
- Prepare template data packages
- Generate contextual subject lines
- Determine notification recipients
- Queue email notifications

**Pattern**: Separation of concerns - CRUD vs Communication

## Repository Pattern

### Implementation
- Base repository for common operations
- Specific repositories extend base
- Abstracts database access layer
- Enables testing with mock repositories

### Example
```php
// BaseRepository provides: findById, findAll, create, update, delete
// InstructionRequestRepository extends BaseRepository
// Adds: findByStatus, findByCampus, etc.
```

## Value Objects

### NotificationPackage (Planned)
**Purpose**: Immutable container for notification data

**Contents**:
- Template data array
- Dashboard URLs
- Subject lines per recipient type
- Metadata

**Benefits**:
- Single data load per notification
- Type-safe data passing
- Prevents accidental modification

## Edit Locking System

### Implementation
- Uses TestMonitor's eloquent-lockable package
- Custom extensions: `locked_by`, `locked_at` columns
- Stale lock detection (configurable timeout)

### Features
- Automatic lock on edit
- Lock refresh via JavaScript
- Inactivity warnings
- Auto-release on navigation/timeout
- Admin force unlock

### Lock States
- **Active**: Recently refreshed
- **Stale**: Older than threshold (15 min default)
- **Released**: Explicitly cleared

## File Management

### Strategy
- Spatie Media Library for file handling
- Token-based security for public uploads
- Temporary storage → permanent on request creation

### Flow
1. Public form requests upload token
2. Files uploaded to temp storage with token
3. Form submission includes token
4. Backend validates token and moves files
5. Files associated with request via Media Library

### Storage Organization
- `uploads/temp/`: Temporary files (token-protected)
- `uploads/YYYY/MM/`: Date-based permanent storage
- `uploads/{request_id}/`: Legacy structure (pre-2025)

## Google Calendar Integration

### Architecture
- CalendarService handles API communication
- GoogleCalendarEvent model stores local records
- Livewire components for UI
- Alpine.js for client-side validation

### Data Flow
1. Librarian accepts request
2. Form validates required fields (date/time)
3. Calendar event created via service
4. Local record stored in database
5. Request status updated to "scheduled"

### Authentication
- Service account with domain-wide delegation
- User impersonation for attendee management
- Campus-specific calendar IDs

## Notification System

### Current Architecture
- Base notification class with common logic
- Specific notification classes extend base
- Queued email delivery
- Template-based content

### Planned Refactoring
- Extract data loading to NotificationService
- Use NotificationPackage for data passing
- Simplify notification classes to focus on formatting
- Eliminate redundant data loads

## Request Lifecycle

### Status Workflow
```
received → assigned → accepted → scheduled → completed
           ↓
        rejected (can reassign)
```

### Status Change Notifications
- `received`: Notify campus librarians
- `assigned`: Notify assigned librarian
- `accepted`: Notify campus librarians
- `rejected`: Notify campus librarians
- `scheduled`: Update status only (no notification)

## Frontend Architecture

### Livewire Components
- Interactive UI without page reloads
- Server-side state management
- Real-time validation
- Event-driven communication

### Alpine.js Integration
- Client-side reactivity
- Form state management
- UI interactivity (modals, dropdowns)
- Lock refresh timing

### Tailwind CSS
- Utility-first styling
- Consistent design system
- Responsive by default
- Dark mode support

## Security Patterns

### Authentication
- SAML2 single sign-on
- Session-based for web routes
- Manual user creation (librarians)
- No public registration

### Authorization
- Simple admin flag (`is_admin`)
- No complex role system (currently)
- Feature-based checks (is_scheduler)

### File Upload Security
- Token-based validation
- File type whitelist
- Size limits enforced
- Server-side validation

## Performance Considerations

### Database
- Eager loading to prevent N+1
- Indexes on frequently queried columns
- Soft deletes for data retention
- Transaction wrapping for data integrity

### Caching
- Config/route caching in production
- View compilation
- Query result caching (selective)

### Queue System
- Email notifications queued
- Supervisor manages workers
- Retry logic for failures
- Logging for troubleshooting

## Testing Strategy

### Pest Framework
- Feature tests for user flows
- Unit tests for services/repositories
- Database transactions for isolation
- Factory-based test data

### Manual Testing
- Multi-environment validation
- Cross-browser compatibility
- Mobile responsiveness
- File upload verification

## Deployment Architecture

### Multi-Environment
- `.env.local`: Docker development
- `.env.testing`: PCC test server
- `.env.production`: PCC production server

### Deployment Script
- Environment switching via `./deploy.sh [env]`
- Automated cache clearing
- Permission setting
- Storage link creation

### Queue Workers
- Supervisor process management
- Automatic restart on code changes
- Error logging and monitoring
- Graceful shutdown handling