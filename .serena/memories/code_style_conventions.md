# Code Style and Conventions

## Laravel Conventions

### Naming Standards

#### Classes
- Models: Singular PascalCase (`InstructionRequests`, `Instructor`)
- Controllers: PascalCase with suffix (`InstructionRequestController`)
- Services: PascalCase with suffix (`InstructionRequestService`)
- Repositories: PascalCase with suffix (`InstructionRequestRepository`)
- Livewire Components: PascalCase (`CreateGoogleCalendarEventForm`)
- Value Objects: PascalCase (`NotificationPackage`)

#### Methods
- Controllers: camelCase action methods (`index`, `store`, `update`)
- Services: camelCase descriptive names (`createRequest`, `sendNotifications`)
- Repositories: camelCase with prefixes (`findById`, `getAllActive`)

#### Variables
- camelCase for all variables (`$instructionRequest`, `$campusLibrarians`)
- snake_case for database columns (`created_at`, `instruction_type`)
- SCREAMING_SNAKE_CASE for constants (`MAX_FILE_SIZE`)

#### Files & Directories
- Views: kebab-case (`instruction-requests/edit.blade.php`)
- Routes: kebab-case (`instruction-requests`, `google-calendar`)
- Config files: kebab-case (`google-calendar.php`)

### PHP Standards

#### Code Style
- Indentation: 4 spaces (no tabs)
- Line endings: LF (Unix style)
- PHP version: 8.2+
- Type hints: Use strict types where possible
- Return types: Always declare for methods
- Nullable types: Use `?Type` or `Type|null`

#### Documentation
- Use PHPDoc blocks for all public methods
- Document complex logic with inline comments
- Include `@param`, `@return`, `@throws` tags
- Example:
```php
/**
 * Create a new instruction request.
 *
 * @param array $data Request data
 * @return InstructionRequests
 * @throws \Exception
 */
public function createRequest(array $data): InstructionRequests
```

### Laravel Best Practices

#### Architecture Patterns
- **Repository Pattern**: Data access abstraction
- **Service Pattern**: Business logic encapsulation
- **Value Objects**: Immutable data containers
- **Observers**: Model event handling
- **Jobs**: Queued background tasks

#### Eloquent Models
- Use relationship methods (`hasMany`, `belongsTo`)
- Define fillable or guarded properties
- Use casts for type conversion
- Implement scopes for reusable queries
- Use accessors/mutators sparingly

#### Controllers
- Keep thin - delegate to services
- Use form requests for validation
- Return views or JSON responses
- Use route model binding when appropriate

#### Services
- Single responsibility principle
- Inject dependencies via constructor
- Use database transactions for data integrity
- Handle exceptions appropriately
- Log important operations

### Livewire Conventions

#### Components
- Property naming: public for template access
- Method naming: descriptive action names
- Use `$rules` for validation
- Emit events for component communication
- Use `wire:model` for two-way binding

#### Views
- Use Blade directives (`@if`, `@foreach`)
- Alpine.js for client-side interactivity
- Tailwind CSS for styling
- Keep logic minimal in views

### Frontend Standards

#### Blade Templates
- Use components for reusable UI
- Keep templates focused and simple
- Use slots for flexible content
- Leverage Blade UI Kit components

#### Tailwind CSS
- Use utility classes
- Follow mobile-first approach
- Use custom classes sparingly
- Maintain consistent spacing scale

#### Alpine.js
- Use `x-data` for component state
- Use `x-bind` for reactive attributes
- Use `@click` for event handling
- Keep Alpine logic simple

### Database Conventions

#### Migrations
- Use descriptive names (`create_instruction_requests_table`)
- Follow up/down pattern
- Use foreign key constraints
- Add indexes for performance

#### Schema
- snake_case for table and column names
- Singular model names, plural table names
- Use soft deletes where appropriate
- Timestamp columns: `created_at`, `updated_at`

### Testing

#### Pest Framework
- Use descriptive test names
- Follow AAA pattern (Arrange, Act, Assert)
- Use factories for test data
- Test edge cases and failures

### Git Conventions

#### Commit Messages
- Use conventional commits format
- Start with type: `feat:`, `fix:`, `refactor:`, etc.
- Keep subject line under 72 characters
- Include detailed description when needed

#### Branching
- `main` - production-ready code
- `develop` - integration branch
- `feature/*` - new features
- `fix/*` - bug fixes
- `hotfix/*` - urgent production fixes

### Security

#### Authentication
- Use Laravel's built-in authentication
- SAML2 for single sign-on
- Session-based for web routes
- Token-based for API routes (future)

#### File Uploads
- Validate file types and sizes
- Use token-based security for public uploads
- Store files outside web root
- Scan for malware (consideration)

#### Data Protection
- Sanitize user inputs
- Use parameterized queries (Eloquent)
- Implement CSRF protection
- Use HTTPS in production

### Performance

#### Database
- Eager load relationships to avoid N+1
- Use database indexes
- Cache frequently accessed data
- Use query optimization

#### Caching
- Config cache in production
- Route cache in production
- View compilation
- Application cache when appropriate

### Project-Specific Patterns

#### Edit Locking
- Use Eloquent Lockable package
- Implement stale lock detection (15 min)
- Auto-release on save/navigate away
- Admin override capability

#### Notifications
- Queue email notifications
- Use Mailable classes
- Template-based content
- Log notification events

#### File Management
- Spatie Media Library
- Token-based temporary uploads
- Date-based storage organization
- Support multiple collections