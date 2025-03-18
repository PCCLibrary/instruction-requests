# Library Instruction System Documentation

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
- File attachments for various document types
- Basic dashboard functionality
- Instruction request creation
- Editing existing requests
- Copying existing requests

#### Technical Components
- Custom Blade components
- Alpine.js for frontend interactions
- Spatie Media Library for file management
- Standard Laravel authentication

## 2. Core Components

### 2.1 Request Lifecycle
1. Faculty submits instruction request
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
Supports file collections with the following specifications:

### Supported File Types
- Plain text (.txt)
- Rich Text Format (.rtf)
- Portable Document Format (.pdf)
- Microsoft Word Documents (.doc, .docx)
- Microsoft PowerPoint Presentations (.ppt, .pptx)

### Upload Restrictions
- Maximum file size: 8 MB per file
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
- Node.js 18+ for frontend compilation

## 8. Multi-Environment Configuration

The application is designed to run in multiple environments:

### Environment Files
- `.env.local`: Local Docker development environment
- `.env.testing`: PCC test server environment 
- `.env.production`: PCC production server environment

### Deployment Script
Use the deployment script to switch between environments:

```bash
# Deploy for local development
./deploy.sh local

# Deploy for test server
./deploy.sh testing

# Deploy for production
./deploy.sh production
```

The deployment script will:
1. Copy the appropriate .env file
2. Clear application caches
3. Create necessary storage directories
4. Set up the storage symbolic link
5. Set proper file permissions

### File Upload System

The file upload system is configured to work across all environments:

1. **Required Directories**
   ```bash
   # These directories must exist for file uploads to work
   storage/app/public/uploads/temp
   ```

2. **File Types**
   - PDF (.pdf)
   - Word documents (.doc, .docx)
   - PowerPoint presentations (.ppt, .pptx)
   - Text files (.txt, .rtf)

3. **Size Limits**
   - Maximum file size: 20MB per file

4. **Environment Testing**
   - Visit `/env-test` in non-production environments to verify configuration
