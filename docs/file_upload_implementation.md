# File Upload System Implementation

## Overview of Changes Made (March 2025)

This document summarizes the changes made to fix and enhance the file upload system for the Library Instruction Requests application, ensuring it works reliably across multiple environments.

## Key Issues Addressed

- Fixed the "Unable to create a directory at /var/www/html/library/instruction-requests/public/storage" error
- Implemented environment-specific configuration
- Enhanced error handling and logging
- Added missing controller methods for authenticated uploads
- Created deployment tooling for environment management
- Fixed file association issues in MediaController's `associateFiles` method
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

The current implementation of creating temporary `InstructionRequests` models for file uploads is problematic because:
- It requires database fields that shouldn't be necessary for temporary storage
- It creates database constraints issues (such as with the required `instructor_id` field)
- It generates unnecessary database entries that need cleanup

**Implementation Solution**: Create a custom `TemporaryUpload` model since the free version of Spatie Media Library doesn't include the `TemporaryUpload` model that's available in the Pro version.

1. **Benefits**:
   - Purpose-built for temporary file storage
   - No database constraint issues
   - Clean separation of concerns
   - Configurable expiration/cleanup

2. **Implementation Status**:
   - Created a custom `TemporaryUpload` model in `app/Models/TemporaryUpload.php`
   - Added migration for the `temporary_uploads` table
   - Created `CleanTemporaryUploads` command for scheduled cleanup
   - Updated `MediaController.php` to use `TemporaryUpload` instead of temp `InstructionRequests`
   - Modified the token management to use database records instead of cache
   - Updated the file association logic to handle the different model type

3. **Custom TemporaryUpload Model Structure**:
   - `id`: Primary key
   - `upload_token`: Unique token for identifying the upload session (string, 100 chars)
   - `expires_at`: When the temporary upload expires (timestamp)
   - `created_at`/`updated_at`: Standard timestamps
   - `deleted_at`: Soft delete column

This approach maintains the current user experience while implementing a cleaner, more sustainable architecture for file uploads. It avoids the need for the Pro version of Spatie Media Library by implementing a custom solution with the same core functionality.

## Common Issues and Debugging

When encountering file upload issues, check these common problems first:

1. **Storage Symlink Issues**: The storage symlink might be broken or missing. This is the most common issue.
   - Verify the symlink exists: `ls -la public/storage` should point to `../storage/app/public`
   - If missing or damaged, recreate with: `php artisan storage:link`

2. **Directory Permissions**: The storage directory needs correct permissions.
   - Ensure web server has write access to `storage/app/public` and subdirectories
   - Check permissions with: `ls -la storage/app`

3. **Upload Paths**: Check if necessary directories exist within the storage area.
   - The CustomPathGenerator creates paths like `uploads/temp/` for temporary files
   - Manually create these directories if they don't exist: `mkdir -p storage/app/public/uploads/temp`

4. **Token Management**: Uploads now rely on tokens stored in the database via TemporaryUpload model.
   - Check if the TemporaryUpload records exist in the database
   - Verify column name is 'upload_token' (not 'token') in queries
   - Ensure tokens haven't expired (check 'expires_at' field)

5. **Headers Issues**: Dropzone needs to properly send the X-Upload-Token header.
   - Check browser console logs to ensure headers are being sent correctly

6. **Media Library Configuration**: Verify the media-library configuration is correct.
   - Should use 'public' disk: `'disk_name' => env('MEDIA_DISK', 'public')`
   - Max file size setting might be too low: check `'max_file_size'` value

## Technical Details

### 1. Configuration Changes

#### Filesystems Configuration (`config/filesystems.php`)
- Changed 'public' disk to use `storage_path('app/public')` instead of `public_path('storage')`
- Updated URL configuration to use environment variables: `env('APP_URL').'/storage'`
- Ensured storage paths are consistent with Laravel conventions

#### Media Library Configuration
- Added environment variables for media configuration:
  ```
  MEDIA_DISK=public
  MEDIA_PREFIX=""
  IMAGE_DRIVER=gd
  ```

### 2. Custom Path Generator Enhancement

Extended `App\Services\MediaLibrary\CustomPathGenerator` to:
- Ensure directories exist before file saves with robust directory validation
- Return boolean status for directory creation operations
- Handle path verification and provide detailed error reporting
- Verify paths are actually directories and not files with the same name
- Include environment awareness in logging
- Handle temporary uploads more gracefully
- Direct all new files to date-based path structure (year/month)
- Maintain path structure for existing legacy files (before March 2025)
- Provide consistent API and behavior with MediaController
- Use the same robust directory creation logic as MediaController
- Harmonize error handling and logging patterns with MediaController

### 3. MediaController Improvements

#### Public Upload Method
- Added directory existence verification
- Created real database records for temporary uploads
- Improved error handling with detailed logs
- Added environment-specific logging levels
- Enhanced debugging information

#### Admin Upload Methods
- Implemented missing `upload()` method for authenticated users
- Implemented missing `delete()` method for authenticated users
- Added security checking and error handling

#### File Association Method
- Updated `associateFiles` method signature to match how it's called
- Fixed column name from 'token' to 'upload_token' in TemporaryUpload queries
- Standardized configuration key usage from 'media.disk' to 'media-library.disk_name'
- Added proper error handling and return values
- Improved path handling for file movements
- Added check to prevent re-association of already-associated files

### 4. Dashboard File Browser Requirements

For browsing and attaching unassociated files in the dashboard:

1. **File Upload Storage**:
   - Dashboard uploaded files go to date-based folders (`uploads/YYYY/MM/`)
   - Files remain unassociated until manually linked to an instruction request

2. **File Status Tracking**:
   - Files can be in one of these states:
     - `temporary`: Uploaded through public form, in temp directory
     - `unassociated`: Uploaded through dashboard, not yet linked to a request
     - `associated`: Linked to a specific instruction request

3. **File Browser Interface**:
   - Needs to show both unassociated and associated files
   - Should allow filtering by date, file type, and association status
   - Must provide a way to select and associate files with instruction requests

4. **Association Mechanism**:
   - When a file is associated with a request, its metadata is updated
   - The file itself remains in its original location to maintain stable URLs

### 5. Implementation Design

#### Model Treatment:
- **Temporary Uploads**: Use a temporary InstructionRequests model that gets cleaned up
- **Dashboard Uploads**: Associate with the actual user account

#### Custom Properties for Files:
- `temporary`: Boolean indicating if file is temporary
- `upload_source`: String indicating if uploaded from "public" or "dashboard"
- `uploaded_by`: ID of user who uploaded file (for dashboard uploads)
- `upload_date`: Timestamp of upload
- `associated_date`: Timestamp when associated with an instruction request

## Environment Requirements

For proper file upload functionality:
- Storage directory must be writable by web server
- Symbolic link must exist from `public/storage` to `storage/app/public`
- Required directories must exist: `storage/app/public/uploads/temp`

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
   - "Creating temp model for file association" - Temp model creation
   - "File uploaded successfully" - File saved in media library
   - Any error messages related to directory creation or permissions

5. **Quick Fixes**:
   - Recreate the storage link: `php artisan storage:link`
   - Clear Laravel cache: `php artisan cache:clear`
   - Verify `uploads/temp` directory exists in storage
   - Check error logs for specific failures

### 3. MediaController Improvements

#### Public Upload Method
- Added directory existence verification
- Improved error handling with detailed logs
- Added environment-specific logging levels
- Enhanced debugging information

#### Admin Upload Methods
- Implemented missing `upload()` method for authenticated users
- Implemented missing `delete()` method for authenticated users
- Added security checking and error handling

### 4. Environment-Specific Configuration

Created environment-specific configuration files:
- `.env.local` - Local development environment
- `.env.testing` - Test server environment
- `.env.production` - Production environment template

### 5. System-Wide Harmonization 

To improve reliability and maintainability, the file handling has been harmonized across all components:

1. **Unified Directory Creation**: 
   - Both `CustomPathGenerator` and `MediaController` now use the same robust directory creation logic
   - Both implementations verify if a path is actually a directory rather than a file
   - Both classes handle edge cases consistently, such as empty paths or special characters
   - Both return boolean results with proper error reporting

2. **Consistent Path Generation**:
   - All new files use date-based folders (YYYY/MM) by default
   - Temporary uploads consistently use `uploads/temp/`
   - Only legacy files (before March 2025) maintain their original structure
   - Standardized handling of missing created_at timestamps

3. **Unified Error Handling**:
   - Consistent error logging format and detail level across all components
   - Environment-aware logging levels (production vs. development/testing)
   - Full exception details in non-production environments
   - Both components preserve path operation even when directory creation fails

### 6. MediaController associateFiles Improvements

The `associateFiles` method in MediaController has been significantly improved:

1. **Method Signature**: Updated to match how it's actually called:
   ```php
   // Old signature (didn't match caller)
   public function associateFiles(string $token, $request, $relatedModel, string $collectionName)
   
   // New signature (matches caller)
   public function associateFiles(string $token, $instructionRequestOrId)
   ```

2. **ID to Model Conversion**: Added logic to handle either an ID or a model instance:
   ```php
   // Convert ID to model instance if needed
   if (is_numeric($instructionRequestOrId)) {
       $instructionRequest = InstructionRequests::find($instructionRequestOrId);
       // Error handling if not found
   }
   ```

3. **Database Column Fix**: Using the correct column name in TemporaryUpload model:
   ```php
   // Old (incorrect column name)
   $temporaryUpload = TemporaryUpload::where('token', $token)->first();
   
   // New (correct column name)
   $temporaryUpload = TemporaryUpload::where('upload_token', $token)->first();
   ```

4. **Configuration Consistency**: Using consistent configuration keys:
   ```php
   // Old (inconsistent key)
   Storage::disk(config('media.disk'))->makeDirectory(dirname($newPath));
   
   // New (consistent key)
   $disk = config('media-library.disk_name');
   Storage::disk($disk)->makeDirectory(dirname($newPath));
   ```

5. **Return Values**: Added proper return values for error handling:
   ```php
   return true; // Success
   return false; // Error case
   ```

6. **Re-association Prevention**: Added check to prevent duplicate associations:
   ```php
   if ($file->model_id) {
       Log::warning("File already associated, skipping...");
       continue;
   }
   ```

These improvements ensure more reliable file uploads and associations between temporary files and instruction requests.

### 6. Deployment Script

Added `deploy.sh` for environment switching:
```bash
./deploy.sh [local|testing|production]
```

Script capabilities:
- Copies appropriate environment configuration
- Clears Laravel caches
- Creates necessary storage directories
- Sets up storage symbolic links
- Sets appropriate file permissions
- Flushes and restarts queues
- Optimizes for production (when applicable)

### 7. Diagnostic Tools

Created an environment test route `/env-test` to verify configuration:
```php
Route::get('/env-test', function () {
    return [
        'environment' => app()->environment(),
        'app_url' => config('app.url'),
        'filesystem_driver' => config('filesystems.default'),
        'media_disk' => config('media-library.disk_name'),
        // More diagnostic information...
    ];
});
```

## Design Decisions

### Storage Location
- Using Laravel's standard storage location (`storage/app/public`) ensures compatibility with Laravel's file handling logic
- Symbolic links maintain proper public access
- Improved security by moving files out of web-accessible directories

### Path Generation Strategy
- Temporary uploads go to `uploads/temp/`
- All new files use the date-based structure `uploads/YYYY/MM/` by default
- Only legacy files (created before March 2025) maintain the original path structure `uploads/{request_id}/`
- Both `CustomPathGenerator` and `MediaController` now use this consistent path strategy

### Error Handling Approach
- Enhanced logging with environment-specific detail levels
- Explicit directory creation checks with verification of directory vs. file
- Consistent return values across all directory and file operations
- Detailed error reporting with specific failure reasons
- Fallback mechanisms for handling path-related issues
- Proper exception handling with detailed error messages
- Harmonized error handling patterns between `CustomPathGenerator` and `MediaController`
- Identical directory creation logic in both classes to ensure consistent behavior
- Standardized path validation across the entire system
- Return boolean values from all operations that could potentially fail

### Deployment Strategy
- Environment-specific configuration files
- Simple script for environment switching
- Idempotent operations (can be run multiple times without harm)

## Next Steps & Future Considerations

- Implement file type validation on the frontend
- Add progress indicators during uploads
- Consider implementing image previews for supported file types
- Explore chunked uploads for larger files
- Consider implementing file deduplication

## Compatibility Notes

The implemented changes maintain full compatibility with existing features:
- SAML authentication is untouched
- Request creation flows remain the same
- Existing file uploads are preserved
- All database interactions remain unchanged

## Testing Steps

After deployment, verify functionality by:
1. Accessing the `/env-test` route to confirm configuration
2. Uploading a file from the public form
3. Uploading a file from the admin interface
4. Checking file accessibility
5. Testing file deletion

## Environment Requirements

For proper file upload functionality:
- Storage directory must be writable by web server
- Symbolic link must exist from `public/storage` to `storage/app/public`
- Required directories must exist: `storage/app/public/uploads/temp`

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
   - "Creating temp model for file association" - Temp model creation
   - "File uploaded successfully" - File saved in media library
   - Any error messages related to directory creation or permissions

5. **Quick Fixes**:
   - Recreate the storage link: `php artisan storage:link`
   - Clear Laravel cache: `php artisan cache:clear`
   - Verify `uploads/temp` directory exists in storage
   - Check error logs for specific failures
