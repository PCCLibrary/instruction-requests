# Dropzone File Upload Implementation

## Overview

This document explains the implementation of drag-and-drop file uploads in the Library Instruction Request system using Dropzone.js. The solution supports both the public form and admin dashboard with a token-based approach for handling file uploads.

## Features

- Drag-and-drop file upload interface
- Progress indicators for uploads
- File type icons based on extension
- Token-based security for unauthenticated uploads
- Automatic cleanup of temporary files
- Maximum file size: 20MB per file
- Maximum files per upload: 4 files
- Supported file types: PDF, Word, PowerPoint, and text files (.pdf, .doc, .docx, .ppt, .pptx, .txt, .rtf)

## Components

### Frontend Components

1. **Dropzone Component** (`resources/views/public_form/partials/dropzone.blade.php`)
   - Bootstrap-styled drag-and-drop interface
   - File preview template with progress indicator
   - Error message display
   - Token storage

2. **CSS & JavaScript**
   - Dropzone.js v5.9.3 (CDN)
   - Font Awesome 4.7.0 for file type icons (CDN)
   - Custom styles in component

3. **Implementation in Forms**
   - Public form: Replaces the standard file inputs
   - Admin dashboard: To be implemented

### Backend Components

1. **MediaController** (`app/Http/Controllers/MediaController.php`)
   - `generateUploadToken()`: Creates a 120-minute valid upload token
   - `publicUpload()`: Handles file uploads for unauthenticated users
   - `publicDelete()`: Deletes temporary files
   - `associateFiles()`: Associates temporary files with an instruction request

2. **Routes** (`routes/web.php`)
   - `/api/token/generate`: Generates a secure upload token
   - `/api/media/upload`: Handles file upload with token validation
   - `/api/media/delete/{id}`: Deletes a file with token validation

3. **Cleanup Command** (`app/Console/Commands/CleanupTemporaryFiles.php`)
   - Daily scheduled task to remove temporary files older than 24 hours

## How It Works

### Token-Based Security Flow

1. When a user accesses the form, the system generates a secure token via Ajax
2. The token is stored in a hidden input field and used for all file operations
3. Files are uploaded to a temporary storage area with token association
4. Upon form submission, the token is included and files are associated with the new request
5. The token expires after 120 minutes, and temporary files are cleaned up after 24 hours

### File Upload Flow

1. User drags files onto the dropzone or clicks to browse
2. Files are validated client-side (size and type)
3. Files are uploaded to the server with the token
4. The server validates the token and file
5. The file is stored temporarily with metadata (token, upload date, etc.)
6. The file appears in the interface with appropriate icon
7. The user can remove files before submission

### Form Submission Flow

1. User submits the form with the token
2. The server creates a new instruction request
3. The system calls `associateFiles` with the token and instruction request ID
4. The method verifies the token, retrieves the temporary files, and moves them to permanent storage
5. Each file is updated with the correct model type and ID
6. The temporary upload record is deleted to prevent reuse
7. The method returns boolean value indicating success or failure

## Technical Details

### Storage Structure

- Files are stored using Spatie Media Library
- Temporary files are stored in `uploads/temp/` directory
- Regular files are stored by date: `uploads/YYYY/MM/`
- All files are in the "materials" collection

### Token and TemporaryUpload Structure

The system now uses a database-based approach with the TemporaryUpload model:

```php
// TemporaryUpload model
[
    'id' => Primary key,
    'upload_token' => String (unique token used for authentication),
    'expires_at' => DateTime (when the upload session expires),
    'created_at' => Carbon instance,
    'updated_at' => Carbon instance
]

// Associated media files are linked through Spatie's MediaLibrary
```

### File Type Mapping

```php
$iconMap = [
    'pdf' => 'fa-file-pdf-o',
    'doc' => 'fa-file-word-o',
    'docx' => 'fa-file-word-o',
    'ppt' => 'fa-file-powerpoint-o',
    'pptx' => 'fa-file-powerpoint-o',
    'txt' => 'fa-file-text-o',
    'rtf' => 'fa-file-text-o'
];
```

## Installation & Setup

### Prerequisites

- Laravel 11 with Spatie Media Library installed
- Storage permissions configured correctly

### File Permissions

To ensure proper file upload functionality:

1. Ensure the web server has write permissions to the storage directory:
   ```bash
   sudo chown -R www-data:www-data storage
   sudo chmod -R 775 storage
   ```

2. Make sure the storage link is properly published:
   ```bash
   php artisan storage:link
   ```

3. Check that the `uploads` directory exists in `public/storage` and has proper permissions:
   ```bash
   mkdir -p public/storage/uploads
   sudo chown -R www-data:www-data public/storage
   sudo chmod -R 775 public/storage
   ```

4. For local development, you may need to adjust permissions for your user:
   ```bash
   sudo usermod -a -G www-data $USER
   ```

### Configuration

1. Make sure the CSRF token exceptions are in place in `app/Http/Middleware/VerifyCsrfToken.php`:
   ```php
   protected $except = [
       'api/token/generate',
       'api/media/upload',
       'api/media/delete/*'
   ];
   ```

2. Ensure the media-library.php configuration has the correct disk settings:
   ```php
   'disk_name' => env('MEDIA_DISK', 'public'),
   ```

## Maintenance

### Cleanup

The system includes an automatic cleanup command (`media:cleanup-temp`) that runs daily to remove temporary files older than 24 hours. This can also be run manually:

```bash
php artisan media:cleanup-temp
```

### Monitoring

Monitor the following for potential issues:

- Disk space usage for temporary files
- Cache storage for expired tokens
- Failed uploads in the logs
- Media library records with `model_id = 0` that aren't being cleaned up

## Troubleshooting

### Common Issues

1. **File Upload Fails with 500 Error**:
   - Check Laravel logs for specific errors
   - Verify file permissions in storage directories
   - Ensure the web server has write access to the upload directory

2. **Token Generation Fails**:
   - Check CSRF middleware exceptions
   - Verify routes are correctly defined
   - Look for syntax errors in MediaController

3. **Files Not Associated with Request**:
   - Check that the token is being properly passed in the form
   - Verify the associateFiles method is being called with the correct parameters
   - Ensure the TemporaryUpload record exists with the correct upload_token value
   - Check that config('media-library.disk_name') is used consistently
   - Look for errors in the logs during file association

### Debugging

For detailed debugging:

1. Review logs for the detailed error information and file association tracking:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. Check the database for uploaded files and temporary uploads:
   ```sql
   SELECT * FROM media WHERE model_id = 0;
   SELECT * FROM temporary_uploads WHERE deleted_at IS NULL;
   ```

3. Inspect the storage directory for temporary files:
   ```bash
   ls -la storage/app/public/uploads/temp/
   ```

4. Check the associateFiles method return value:
   ```php
   // The method now returns a boolean value
   $result = app(\App\Http\Controllers\MediaController::class)->associateFiles($token, $requestId);
   Log::info('Association result', ['success' => $result]);
   ```

## Future Enhancements

1. Admin dashboard integration
2. Chunked uploads for larger files
3. File type validation on the server side
4. Image previews for supported file types
5. Better error handling and retry mechanisms
