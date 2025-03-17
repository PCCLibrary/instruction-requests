# Library Instruction System Technical Documentation

## 1. System Overview

### 1.1 Purpose
The Library Instruction System is a Laravel 11 web application designed to streamline the process of scheduling and managing library instruction sessions between faculty and librarians within the Portland Community College network.

### 1.2 Technical Evolution
* **Originally:** Laravel 8, Infyom Generator, AdminLTE, Yajra DataTables
* **Current:** Laravel 11, Livewire 3, Tailwind CSS, Livewire PowerGrid 6.1

### 1.3 Current Implementation Status

#### Working Features
- Request viewing and management
- Status updates across multiple states
- File attachments with drag-and-drop interface
- Basic dashboard functionality
- Instruction request creation
- Editing existing requests
- Copying existing requests
- Token-based secure file uploads

#### Technical Components
- Custom Blade components
- Alpine.js for frontend interactions
- Spatie Media Library for file management
- Dropzone.js for drag-and-drop file uploads
- Standard Laravel authentication

## 2. Core Components

### 2.1 Request Lifecycle
1. Faculty submits instruction request (with optional file attachments)
2. System creates/links instructor record
3. Request enters `received` status
4. Librarians can:
   - View requests
   - Assign requests
   - Accept requests
   - Mark requests complete
   - Reject or copy requests

### 2.2 Request Statuses
- `received`: Initial state
- `assigned`: Librarian assigned
- `accepted`: Librarian confirmed
- `completed`: Session finished
- `copied`: Request duplicated

### 2.3 Key Models
- `InstructionRequests`: Central model
  - Relates to Instructor
  - Relates to Classes
  - Relates to Campus
  - Associated with `InstructionRequestDetails`

- `Instructor`: Faculty representation
- `User`: Librarian accounts (manually created)

## 3. File Handling

The system supports both traditional file uploads and a modern drag-and-drop interface using Dropzone.js. Files are stored using Spatie Media Library.

### 3.1 Implementation

- **Frontend**: Dropzone.js provides an intuitive drag-and-drop interface
- **Backend**: Token-based security flow for temporary file storage
- **Storage**: Files use year/month directory structure

### 3.2 File Flow

1. User drags files to upload area
2. File uploads immediately with token authentication
3. On form submission, files are associated with the request
4. Temporary files are cleaned up automatically after 24 hours

### 3.3 Supported File Types
- Plain text (.txt)
- Rich Text Format (.rtf)
- Portable Document Format (.pdf)
- Microsoft Word Documents (.doc, .docx)
- Microsoft PowerPoint Presentations (.ppt, .pptx)

### 3.4 Upload Restrictions
- Maximum file size: 20MB per file
- Maximum 4 files per upload session
- File collections:
  - Course syllabi
  - Instructor attachments
  - Teaching materials
  - Assessment documents

## 4. Notification System
Provides notifications for:
- Request received
- Request assigned
- Request accepted
- Request rejected

## 5. Authentication
- Standard Laravel authentication
- Manual user creation
- No role-based access control

## 6. Development Environment
- Framework: Laravel 11
- PHP Version: 8.2+
- Timezone: America/Los_Angeles
- Date Format: YYYY-MM-DD H:i:s
- Access: Internal VPN network

## 7. System Requirements
- PHP 8.2+
- Composer dependencies
- Node.js for frontend compilation
- Proper storage directory permissions

## 8. Security Considerations
- VPN-restricted access
- Manual user management
- Token-based file upload security
- File upload restrictions
- Soft delete implementations

## 9. Installation & Setup

### 9.1 Basic Setup
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

### 9.2 File Upload Setup
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

### 9.3 Scheduled Tasks
```bash
# Add to server crontab
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

This ensures temporary files are cleaned up and other scheduled tasks run properly.

## 10. Maintenance

### 10.1 File Cleanup
```bash
# Manual cleanup of temporary files
php artisan media:cleanup-temp
```

### 10.2 Cache Management
```bash
# Clear application cache
php artisan cache:clear

# Clear route cache
php artisan route:clear

# Clear config cache
php artisan config:clear
```

## 11. Troubleshooting

### 11.1 Common Issues
- File upload failures: Check permissions and logs
- Token generation errors: Verify CSRF exceptions
- File association problems: Check database records

### 11.2 Logs
```bash
# View application logs
tail -f storage/logs/laravel.log
```

## 12. Future Development
- Role-based access control
- Calendar integration
- Email notifications
- Admin dashboard for drag-and-drop uploads
- Analytics dashboard