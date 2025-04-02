# File Upload System Implementation

## Overview of Changes Made (April 2025)

This document outlines the changes made to enhance the file upload system for the Library Instruction Requests application, ensuring it works reliably across multiple environments. The latest update focuses on adopting Spatie Media Library's native methods rather than our custom file management approach.

## Key Issues Addressed

- Fixed the "Unable to create a directory at /var/www/html/library/instruction-requests/public/storage" error
- Implemented environment-specific configuration
- Enhanced error handling and logging
- Added missing controller methods for authenticated uploads
- Created deployment tooling for environment management
- Refactored file association to use Spatie's native `move()` method
- Implemented database transactions for file associations
- Updated MediaController to use consistent configuration keys and column names

## File Upload Architecture

Files are organized in the storage system according to the following rules:

1. **Temporary Uploads** (Public Form):
   - Path: `uploads/temp/`
   - Files uploaded through the public form are initially stored here
   - They are later moved and associated with a specific instruction request
   - These files have a `temporary` custom property set to `true`

2. **Date-Based Organization** (Dashboard Uploads):
   - Path: `uploads/YYYY/MM/`
   - Files uploaded directly from the dashboard go here
   - Files are organized by upload date rather than by instruction request
   - These files do not have the `temporary` property set
   - This applies to files uploaded after March 1, 2025

3. **Legacy Structure** (Existing Files):
   - Path: `uploads/{request_id}/`
   - Files uploaded before March 2025 remain in this structure
   - This maintains compatibility with existing files

### Temporary Upload Strategy

The TemporaryUpload model provides a clean, dedicated approach for handling temporary file uploads:

1. **Benefits**:
   - Purpose-built for temporary file storage
   - No database constraint issues
   - Clean separation of concerns
   - Configurable expiration/cleanup
   - Integrates seamlessly with Spatie Media Library

2. **Implementation**:
   - Uses custom `TemporaryUpload` model in `app/Models/TemporaryUpload.php`
   - Implements `HasMedia` interface and `InteractsWithMedia` trait
   - Media collection enforces file type restrictions
   - Expiration handled through `expires_at` field
   - Token generation and validation methods encapsulated in MediaController

3. **TemporaryUpload Model Structure**:
   - `id`: Primary key
   - `upload_token`: Unique token for identifying the upload session (string, 100 chars)
   - `expires_at`: When the temporary upload expires (timestamp)
   - `created_at`/`updated_at`: Standard timestamps
   - `deleted_at`: Soft delete column

## Enhanced File Association Process

The new file association process uses Spatie Media Library's native methods for improved reliability and maintainability:

### Before (Old Approach):
- Used custom file movement logic with Storage facade
- Required explicit path generation and directory creation
- Multiple points of failure in file movement process
- Manual metadata updates with direct database queries
- No transaction support, leading to potential inconsistencies

### Now (Spatie-Native Approach):
- Uses Spatie's built-in `move()` method
- Automatic path generation through CustomPathGenerator
- Custom properties preservation during move operation
- Transaction-based processing for all-or-nothing operations
- Better error handling and reporting

### Key Changes in the `associateFiles()` Method:

```php
// Use Spatie's move method to transfer the file to the instruction request
try {
    // Copy custom properties before moving
    $customProperties = $file->custom_properties;
    $customProperties['temporary'] = false;

    // Move the file to the instruction request and the materials collection
    $newMedia = $file->move($instructionRequest, 'materials');

    // Update custom properties on the moved file
    foreach ($customProperties as $key => $value) {
        $newMedia->setCustomProperty($key, $value);
    }
    $newMedia->save();
} catch (\Exception $e) {
    Log::error('Error moving file', [
        'file_id' => $file->id,
        'file_name' => $fileName,
        'request_id' => $requestId,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    throw $e; // Re-throw to trigger transaction rollback
}
```

## Common Issues and Debugging

When encountering file upload issues, check these common problems first:

1. **Storage Symlink Issues**: The storage symlink might be broken or missing. This is the most common issue.
   - Verify the symlink exists: `ls -la public/storage` should point to `../storage/app/public`
   - If missing or damaged, recreate with: `php artisan storage:link`

2. **Directory Permissions**: The storage directory needs correct permissions.
   - Ensure web server has write access to `storage/app/public` and subdirectories
   - Check permissions with: `ls -la storage/app`

3. **Upload Paths**: Check if necessary directories exist within the storage area.
   - Directories are now created automatically by Spatie Media Library
   - For manual checks: `mkdir -p storage/app/public/uploads/temp`

4. **Token Management**: Uploads now rely on tokens stored in the database via TemporaryUpload model.
   - Check if the TemporaryUpload records exist in the database
   - Verify column name is 'upload_token' (not 'token') in queries
   - Ensure tokens haven't expired (check 'expires_at' field)

5. **Headers Issues**: Dropzone needs to properly send the X-Upload-Token header.
   - Check browser console logs to ensure headers are being sent correctly

6. **Media Library Configuration**: Verify the media-library configuration is correct.
   - Should use 'public' disk: `'disk_name' => env('MEDIA_DISK', 'public')`
   - Max file size setting: `max_file_size' => 1024 * 1024 * 20 // 20MB`

## Detailed Implementation Improvements

### 1. Media Library Integration

Increased use of Spatie Media Library's native features:
- `addMediaFromRequest()` for file uploads
- `move()` for file transfers between models
- `withCustomProperties()` for metadata
- `toMediaCollection()` for organizing files
- `getUrl()` and `getPath()` for file access

### 2. File Upload Flow

1. **Token Generation**:
   - Generate random token with `Str::random(40)`
   - Create TemporaryUpload record with expiration date
   - Return token to client-side application

2. **File Upload**:
   - Client includes token in X-Upload-Token header
   - Server validates token and gets TemporaryUpload model
   - File is uploaded directly to TemporaryUpload model
   - Custom properties are added to track metadata

3. **File Association**:
   - All files are moved in a single database transaction
   - Each file uses Spatie's `move()` method to transfer to InstructionRequest
   - Custom properties are preserved and updated
   - TemporaryUpload record is deleted after successful transfer

### 3. Dropzone Integration

The front-end integration with Dropzone.js remains unchanged, but now works more reliably with the backend changes:
- Token acquisition on page load
- X-Upload-Token inclusion in headers
- Separate endpoints for upload and delete operations
- Consistent error handling and status reporting

## Database Transactions

File associations now use database transactions for improved reliability:

```php
DB::beginTransaction();

try {
    // Process all files
    foreach ($files as $file) {
        // Move operations using Spatie Media Library methods
    }

    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    Log::error('File association failed, transaction rolled back');
    return false;
}
```

This ensures that either all files are properly associated or none are, preventing partial failures that could leave the system in an inconsistent state.

## Environment Requirements

For proper file upload functionality:
- Storage directory must be writable by web server
- Symbolic link must exist from `public/storage` to `storage/app/public`
- Required directories are now created automatically by the system

## Troubleshooting Dropzone File Uploads

When files are uploaded via Dropzone but not saved to the server, check:

1. **Console Logging**: Look for these messages in browser console:
   - "File added to Dropzone: [filename]" - Initial file selection
   - "Sending file: [filename]" - File being sent to server
   - "Headers: [object]" - Headers being sent (should include X-Upload-Token)
   - "File uploaded successfully: [filename]" - Successful server response

2. **Server Response**: Check the response payload structure:
   ```
   {
     success: true,
     file: {
       id: [number],
       name: [string],
       size: [number],
       extension: [string],
       icon: [string]
     }
   }
   ```

   If `id` is `null`, the file was recognized but not properly saved to the database.

3. **Common Frontend Issues**:
   - Missing CSRF token
   - Incorrect base URL for API endpoints
   - Mismatched route names
   - Missing X-Upload-Token header

4. **Server-side Logging**: Check Laravel logs for:
   - "Generating upload token" - Token generation success
   - "File upload request received" - Request reached controller
   - "Validating file" - File validation process
   - "Using temporary upload for file association" - Temp model creation
   - "File uploaded successfully" - File saved in media library
   - Any error messages related to directory creation or permissions

5. **Quick Fixes**:
   - Recreate the storage link: `php artisan storage:link`
   - Clear Laravel cache: `php artisan cache:clear`
   - Verify `uploads/temp` directory exists in storage
   - Check error logs for specific failures

## Design Decisions

### Spatie Media Library Native Methods
- Using Spatie's built-in methods ensures better maintenance and compatibility
- Removes dependence on custom file movement code
- Takes advantage of Spatie's robust error handling
- Ensures consistent behavior across different environments
- Simplifies future upgrades of the Media Library package

### Transaction-Based File Operations
- Guarantees data consistency even with multiple file operations
- Prevents orphaned files or incomplete associations
- Provides clear success/failure status for the entire operation
- Improves error recovery and debugging

### Token-Based Security
- Secures public uploads without requiring authentication
- Time-limited tokens reduce security risks
- Database storage provides reliable token validation
- Supports multiple simultaneous upload sessions

## Next Steps & Future Considerations

- Implement file type validation on the frontend
- Add progress indicators during uploads
- Consider implementing image previews for supported file types
- Explore chunked uploads for larger files
- Consider implementing file deduplication
- Add file browser for selecting and reusing existing uploads
- Implement file versioning for document revisions

## Compatibility Notes

The implemented changes maintain full compatibility with existing features:
- SAML authentication is untouched
- Request creation flows remain the same
- Existing file uploads are preserved with legacy path structure
- All database interactions remain unchanged

## Testing

After deployment, verify functionality by:
1. Accessing the `/env-test` route to confirm configuration
2. Uploading a file from the public form
3. Uploading a file from the admin interface
4. Checking file accessibility in both public form and dashboard
5. Testing file deletion
6. Verifying file associations work correctly
