<?php

namespace App\Http\Controllers;

use App\Models\InstructionRequests;
use App\Models\TemporaryUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    /**
     * Generate a secure upload token valid for 120 minutes and create a TemporaryUpload record
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateUploadToken()
    {
        try {
            $token = Str::random(40);

            Log::info('Generating upload token', [
                'token' => $token,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'content_type' => request()->header('Content-Type'),
                'method' => request()->method()
            ]);

            // Create a new TemporaryUpload record with a 2-hour expiration
            $temporaryUpload = TemporaryUpload::create([
                'upload_token' => $token,
                'expires_at' => now()->addHours(2),
            ]);

            Log::info('Token generated successfully', [
                'token' => $token,
                'temp_upload_id' => $temporaryUpload->id,
                'expires_at' => $temporaryUpload->expires_at
            ]);

            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            Log::error('Error generating upload token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error generating token'], 500);
        }
    }

    /**
     * Validate upload token by finding a valid TemporaryUpload record
     *
     * @param string $token
     * @return TemporaryUpload|bool
     */
    private function validateToken(string $token)
    {
        try {
            $temporaryUpload = TemporaryUpload::where('upload_token', $token)->first();

            if (!$temporaryUpload) {
                Log::warning('Token not found', ['token' => $token]);
                return false;
            }

            if ($temporaryUpload->hasExpired()) {
                Log::warning('Token expired', [
                    'token' => $token,
                    'created_at' => $temporaryUpload->created_at,
                    'expires_at' => $temporaryUpload->expires_at,
                    'age' => now()->diffInMinutes($temporaryUpload->created_at) . ' minutes'
                ]);
                return false;
            }

            // Get media IDs associated with this temporary upload
            $mediaIds = $temporaryUpload->getMedia('materials')->pluck('id')->toArray();

            Log::debug('Token validated successfully', [
                'token' => $token,
                'temporary_upload_id' => $temporaryUpload->id,
                'files_count' => count($mediaIds),
                'files' => $mediaIds
            ]);

            return $temporaryUpload;
        } catch (\Exception $e) {
            Log::error('Error validating token', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Handle file upload for public form using TemporaryUpload model
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicUpload(Request $request)
    {
        try {
            // Set log level based on environment
            $logLevel = app()->environment('production') ? 'info' : 'debug';

            // Log more detailed info about the request
            Log::log('debug', 'File upload request received [DETAILED]', [
                'headers' => $request->headers->all(),
                'has_file' => $request->hasFile('file'),
                'file_name' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : 'no file',
                'file_size' => $request->hasFile('file') ? $request->file('file')->getSize() : 0,
                'file_mime' => $request->hasFile('file') ? $request->file('file')->getMimeType() : '',
                'storage_path' => storage_path(),
                'public_path' => public_path('storage'),
                'environment' => app()->environment(),
                'media_disk' => config('media-library.disk_name'),
                'request_ip' => $request->ip(),
                'request_method' => $request->method(),
                'request_time' => now()->toDateTimeString(),
            ]);

            // Validate token
            $token = $request->header('X-Upload-Token');
            if (!$token) {
                Log::error('No upload token provided');
                return response()->json(['error' => 'No upload token provided'], 401);
            }

            // Validate token and get the temporary upload record
            $temporaryUpload = $this->validateToken($token);

            if (!$temporaryUpload) {
                Log::error('Invalid or expired upload token', ['token' => $token]);
                return response()->json(['error' => 'Invalid or expired upload token'], 401);
            }

            // Validate file
            if (!$request->hasFile('file')) {
                Log::error('No file provided in request');
                return response()->json(['error' => 'No file provided'], 400);
            }

            Log::log($logLevel, 'Validating file', [
                'name' => $request->file('file')->getClientOriginalName(),
                'size' => $request->file('file')->getSize(),
                'mime' => $request->file('file')->getMimeType()
            ]);

            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,txt,rtf|max:20480', // 20MB
            ]);

            // Ensure upload directories exist
            $disk = config('media-library.disk_name');
            $tempPath = 'uploads/temp';

            if (!Storage::disk($disk)->exists($tempPath)) {
                Storage::disk($disk)->makeDirectory($tempPath);
                Log::log($logLevel, 'Created temporary upload directory', [
                    'path' => $tempPath,
                    'disk' => $disk
                ]);
            }

            Log::log($logLevel, 'Using temporary upload for file association', [
                'model_type' => get_class($temporaryUpload),
                'model_id' => $temporaryUpload->id,
                'token' => $temporaryUpload->upload_token,
                'environment' => app()->environment()
            ]);

            try {
                // Get file directly from request and save to new path in storage
                $file = $request->file('file');
                $fileName = $file->getClientOriginalName();
                $uploadPath = 'uploads/temp';

                // Save file directly to disk without going through MediaLibrary
                $directPath = $file->storeAs($uploadPath, $fileName, config('media-library.disk_name'));
                Log::debug('Directly stored file in temp directory', [
                    'request_file_name' => $fileName,
                    'request_file_size' => $file->getSize(),
                    'direct_path' => $directPath,
                    'direct_exists' => Storage::disk(config('media-library.disk_name'))->exists($directPath),
                    'upload_path' => $uploadPath
                ]);

                // Add the file to the materials collection of the TemporaryUpload model
                $media = $temporaryUpload->addMediaFromRequest('file')
                    ->usingName($request->file('file')->getClientOriginalName())
                    ->withCustomProperties([
                        'upload_token' => $token,
                        'upload_date' => now()->toDateTimeString(),
                        'temporary' => true,
                        'upload_source' => 'public',
                        'environment' => app()->environment()
                    ])
                    ->toMediaCollection('materials');

                // Log VERY detailed information about the media record and file
                Log::debug('*** INITIAL MEDIA RECORD CREATED ***', [
                    'media_id' => $media->id,
                    'media_exists' => $media->exists,
                    'media_saved' => !is_null($media->id),
                    'media_model_id' => $media->model_id,
                    'media_model_type' => $media->model_type,
                    'media_collection' => $media->collection_name,
                    'file_name' => $media->file_name,
                    'file_name_original' => $request->file('file')->getClientOriginalName(),
                    'disk' => $media->disk,
                    'disk_name' => config('media-library.disk_name'),
                    'path' => $media->getPath(),
                    'getPath_result' => $media->getPath(),
                    'media_file_path' => $media->file_name,
                    'expected_full_path' => $media->getPath() . '/' . $media->file_name,
                    'storage_app_path' => storage_path('app/public'),
                    'disk_path' => storage_path('app/public/' . $media->getPath()),
                    'absolute_path' => storage_path('app/public/' . $media->getPath() . '/' . $media->file_name),
                    'file_exists_at_path' => Storage::disk($media->disk)->exists($media->getPath() . '/' . $media->file_name),
                    'file_exists_disk_path' => file_exists(storage_path('app/public/' . $media->getPath() . '/' . $media->file_name)),
                    'file_exists_direct' => file_exists($media->getPath() . '/' . $media->file_name),
                    'file_exists_direct_nocwd' => Storage::disk($media->disk)->exists($media->file_name),
                    'file_exists_temp_direct' => Storage::disk($media->disk)->exists('uploads/temp/' . $media->file_name),
                    'getUrl_result' => $media->getUrl(),
                    'full_url' => $media->getUrl(),
                    'custom_properties' => $media->custom_properties,
                    'size' => $media->size,
                    'mime_type' => $media->mime_type,
                    'human_readable_size' => $media->human_readable_size,
                    'uploaded_at' => $media->created_at->toDateTimeString()
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to create media record', [
                    'temp_upload_id' => $temporaryUpload->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Re-throw the exception to be caught by the outer try-catch
            }

            Log::debug('*** FILE UPLOAD COMPLETED SUCCESSFULLY ***', [
                'media_id' => $media->id,
                'collection' => $media->collection_name,
                'disk' => $media->disk,
                'file_name' => $media->file_name,
                'size' => $media->size,
                'mime_type' => $media->mime_type,
                'path' => $media->getPath(),
                'full_path' => storage_path('app/public/' . $media->getPath() . '/' . $media->file_name),
                'file_exists' => Storage::disk($media->disk)->exists($media->getPath() . '/' . $media->file_name),
                'file_contents_exists' => Storage::disk($media->disk)->exists($media->getPath() . '/' . $media->file_name),
                'file_size_on_disk' => Storage::disk($media->disk)->exists($media->getPath() . '/' . $media->file_name) ? 
                    Storage::disk($media->disk)->size($media->getPath() . '/' . $media->file_name) : 0,
                'url' => $media->getUrl(),
                'absolute_url' => url($media->getUrl()),
                'storage_path' => storage_path(),
                'public_path' => public_path('storage'),
                'environment' => app()->environment(),
                'upload_complete_time' => now()->toDateTimeString()
            ]);

            // Refresh the expiration time on the temporary upload
            $temporaryUpload->expires_at = now()->addHours(2);
            $temporaryUpload->save();

            // Prepare file type info
            $extension = $request->file('file')->getClientOriginalExtension();
            $iconClass = $this->getFileIconClass($extension);

            return response()->json([
                'success' => true,
                'file' => [
                    'id' => $media->id,
                    'name' => $media->file_name,
                    'size' => $media->size,
                    'extension' => $extension,
                    'icon' => $iconClass,
                ]
            ]);

        } catch (FileDoesNotExist $e) {
            Log::error('File does not exist: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'environment' => app()->environment()
            ]);
            return response()->json(['error' => 'The file does not exist.'], 400);
        } catch (FileIsTooBig $e) {
            Log::error('File is too big: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'environment' => app()->environment()
            ]);
            return response()->json(['error' => 'The file is too large. Maximum size is 20MB.'], 400);
        } catch (\Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'environment' => app()->environment()
            ]);
            return response()->json(['error' => 'An error occurred while uploading the file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a file from temporary storage
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicDelete(Request $request, int $id)
    {
        try {
            // Validate token
            $token = $request->header('X-Upload-Token');
            $temporaryUpload = $this->validateToken($token);

            if (!$temporaryUpload) {
                return response()->json(['error' => 'Invalid or expired upload token'], 401);
            }

            // Find the media record
            $media = Media::find($id);

            if (!$media) {
                return response()->json(['error' => 'File not found'], 404);
            }

            // Verify the media belongs to this token
            $fileToken = $media->getCustomProperty('upload_token');
            if ($fileToken !== $token) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Delete the media
            $media->delete();

            Log::info('Media file deleted', [
                'media_id' => $id,
                'token' => $token,
                'temporary_upload_id' => $temporaryUpload->id
            ]);

            // Refresh the expiration time on the temporary upload
            $temporaryUpload->expires_at = now()->addHours(2);
            $temporaryUpload->save();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error deleting file: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An error occurred while deleting the file.'], 500);
        }
    }

    /**
     * Associate files with a new instruction request
     *
     * @param string $token
     * @param int $requestId
     * @return bool
     */
    public function associateFiles(string $token, int $requestId): bool
    {
        Log::info('Associating files with instruction request', [
            'token' => $token,
            'request_id' => $requestId
        ]);

        $temporaryUpload = $this->validateToken($token);

        if (!$temporaryUpload) {
            Log::error('Invalid token when associating files', ['token' => $token]);
            return false;
        }

        // Get all media associated with this temporary upload
        $mediaItems = $temporaryUpload->getMedia('materials');

        if ($mediaItems->isEmpty()) {
            Log::warning('No files to associate', ['token' => $token]);
            return true; // Nothing to do, but not an error
        }

        Log::info('Found files to associate', [
            'token' => $token,
            'file_count' => $mediaItems->count(),
            'temp_upload_id' => $temporaryUpload->id
        ]);

        try {
            // Get the instruction request
            $request = InstructionRequests::findOrFail($requestId);
            Log::info('Found instruction request', [
                'request_id' => $requestId,
                'model_type' => get_class($request),
                'request_status' => $request->status
            ]);

            // Associate all files with the request
            $associatedCount = 0;
            $errorCount = 0;

            foreach ($mediaItems as $media) {
                Log::info('Processing file association', [
                    'file_id' => $media->id,
                    'file_name' => $media->file_name,
                    'request_id' => $requestId
                ]);

                try {
                    // Log state before starting operation
                    Log::debug('*** FILE ASSOCIATION BEGIN ***', [
                        'file_id' => $media->id,
                        'file_name' => $media->file_name,
                        'temp_upload_id' => $temporaryUpload->id,
                        'request_id' => $requestId,
                        'operation_time' => now()->toDateTimeString()
                    ]);
                    
                    // Get the current physical file path before changing model properties
                    $oldFilePath = $media->getPath();
                    $oldFullPath = $oldFilePath . '/' . $media->file_name;
                    $oldDisk = $media->disk;
                    
                    // Check if the source file exists in expected location
                    // Try multiple possible path formats
                    $oldDirectPath = storage_path('app/public/' . $oldFilePath);
                    $oldFullPath = $oldFilePath . '/' . $media->file_name;
                    $oldStandardPath = $oldFilePath . '/' . $media->file_name;
                    $alternativePath = 'uploads/temp/' . $media->file_name;
                    
                    $fileExistsDirectPath = file_exists($oldDirectPath);
                    $fileExistsFullPath = Storage::disk($oldDisk)->exists($oldFullPath);
                    $fileExistsStandardPath = Storage::disk($oldDisk)->exists($oldStandardPath);
                    $fileExistsAlternativePath = Storage::disk($oldDisk)->exists($alternativePath);
                    
                    // Determine which path to use based on existence
                    $effectiveSourcePath = null;
                    $sourceFileExists = false;
                    
                    if ($fileExistsDirectPath) {
                        $effectiveSourcePath = $oldDirectPath;
                        $sourceFileExists = true;
                        $effectiveSourceType = 'direct';
                    } else if ($fileExistsFullPath) {
                        $effectiveSourcePath = $oldFullPath;
                        $sourceFileExists = true;
                        $effectiveSourceType = 'full';
                    } else if ($fileExistsStandardPath) {
                        $effectiveSourcePath = $oldStandardPath;
                        $sourceFileExists = true;
                        $effectiveSourceType = 'standard';
                    } else if ($fileExistsAlternativePath) {
                        $effectiveSourcePath = $alternativePath;
                        $sourceFileExists = true;
                        $effectiveSourceType = 'alternative';
                    }
                    
                    // Additional check - scan root directory for this file if still not found
                    if (!$sourceFileExists) {
                        // Try to find the file by name in root dirs
                        $potentialLocations = [
                            'uploads/temp',
                            'uploads',
                            '.'
                        ];
                        
                        foreach ($potentialLocations as $location) {
                            if ($disk->exists($location)) {
                                $filesInLocation = $disk->files($location);
                                Log::debug('Searching in location ' . $location, [
                                    'files_found' => $filesInLocation
                                ]);
                                
                                foreach ($filesInLocation as $file) {
                                    if (basename($file) === $media->file_name) {
                                        $effectiveSourcePath = $file;
                                        $sourceFileExists = true;
                                        $effectiveSourceType = 'found';
                                        $fileSize = $disk->size($file);
                                        
                                        Log::debug('Found file in alternative location', [
                                            'location' => $location,
                                            'file' => $file,
                                            'size' => $fileSize
                                        ]);
                                        break 2;
                                    }
                                }
                            }
                        }
                    }
                    
                    $fileSize = $sourceFileExists ? 
                        ($effectiveSourceType === 'direct' ? filesize($effectiveSourcePath) : Storage::disk($oldDisk)->size($effectiveSourcePath)) 
                        : 0;
                    
                    Log::debug('*** PRE-ASSOCIATION FILE STATUS ***', [
                        'file_id' => $media->id,
                        'old_path' => $oldFilePath,
                        'old_full_path' => $oldFullPath,
                        'old_disk' => $oldDisk,
                        'file_exists' => $sourceFileExists,
                        'effective_source_path' => $effectiveSourcePath,
                        'effective_source_type' => $effectiveSourceType ?? 'none',
                        'file_size' => $fileSize,
                        'file_exists_direct_path' => $fileExistsDirectPath,
                        'file_exists_full_path' => $fileExistsFullPath,
                        'file_exists_standard_path' => $fileExistsStandardPath,
                        'file_exists_alternative_path' => $fileExistsAlternativePath,
                        'old_direct_path' => $oldDirectPath,
                        'alternative_path' => $alternativePath,
                        'media_id' => $media->id,
                        'media_model_id' => $media->model_id,
                        'media_model_type' => $media->model_type,
                        'media_collection' => $media->collection_name,
                        'custom_properties' => $media->custom_properties,
                        'absolute_storage_path' => storage_path('app/public/' . $oldFilePath . '/' . $media->file_name)
                    ]);

                    // Update model properties
                    $media->model_id = $requestId;
                    $media->model_type = InstructionRequests::class;
                    $media->setCustomProperty('temporary', false);
                    $media->setCustomProperty('associated', true);
                    $media->setCustomProperty('associated_date', now()->toDateTimeString());
                    $media->save();

                    // Get the new path that the file should be located at
                    $newFilePath = $media->getPath();
                    $newFullPath = $newFilePath . '/' . $media->file_name;
                    
                    // Log model update info
                    Log::debug('*** MEDIA RECORD UPDATED ***', [
                        'file_id' => $media->id,
                        'old_path' => $oldFilePath,
                        'new_path' => $newFilePath,
                        'old_full_path' => $oldFullPath,
                        'new_full_path' => $newFullPath,
                        'old_model_id' => $temporaryUpload->id,
                        'new_model_id' => $requestId,
                        'old_model_type' => get_class($temporaryUpload),
                        'new_model_type' => InstructionRequests::class,
                        'custom_properties' => $media->custom_properties
                    ]);
                    
                    // Check if the file needs to be physically moved
                    if ($oldFilePath !== $newFilePath) {
                        // Log detailed info about source and target paths
                        Log::debug('*** PHYSICAL FILE MOVE NEEDED ***', [
                            'old_path' => $oldFilePath,
                            'new_path' => $newFilePath,
                            'old_full_path' => $oldFullPath,
                            'new_full_path' => $newFullPath,
                            'disk' => $oldDisk,
                            'file_exists_at_old_path' => Storage::disk($oldDisk)->exists($oldFullPath),
                            'file_exists_at_new_path' => Storage::disk($oldDisk)->exists($newFullPath),
                            'new_dir_exists' => Storage::disk($oldDisk)->exists($newFilePath),
                            'file_id' => $media->id,
                            'file_name' => $media->file_name
                        ]);
                        
                        // Get disk instance
                        $disk = Storage::disk($oldDisk);
                        
                        // Ensure the target directory exists
                        $newDirectory = $newFilePath;
                        if (!$disk->exists($newDirectory)) {
                            $disk->makeDirectory($newDirectory, 0755, true);
                            Log::debug('Created target directory for file move', [
                                'directory' => $newDirectory,
                                'dir_created' => $disk->exists($newDirectory),
                                'disk' => $oldDisk
                            ]);
                        }
                        
                        // Move the file - first try to find it
                        if ($sourceFileExists) {
                            Log::debug('Reading file content from source...', [
                                'source_path' => $effectiveSourcePath,
                                'source_type' => $effectiveSourceType,
                                'size' => $fileSize
                            ]);
                            
                            // Get file content based on source type
                            if ($effectiveSourceType === 'direct') {
                                $fileContent = file_get_contents($effectiveSourcePath);
                            } else {
                                $fileContent = $disk->get($effectiveSourcePath);
                            }
                            
                            Log::debug('Writing file content to destination...', [
                                'destination' => $newFullPath,
                                'content_size' => strlen($fileContent),
                                'new_directory' => $newDirectory,
                                'dir_exists' => $disk->exists($newDirectory)
                            ]);
                            
                            $disk->put($newFullPath, $fileContent);
                            
                            // Delete the file from the old location only if it was successfully copied
                            if ($disk->exists($newFullPath)) {
                                Log::debug('File copied successfully, removing original...', [
                                    'new_path' => $newFullPath,
                                    'new_size' => $disk->size($newFullPath),
                                    'old_path' => $effectiveSourcePath
                                ]);
                                
                                // Delete based on source type
                                if ($effectiveSourceType === 'direct') {
                                    @unlink($effectiveSourcePath);
                                    $deleteSuccess = !file_exists($effectiveSourcePath);
                                } else {
                                    $disk->delete($effectiveSourcePath);
                                    $deleteSuccess = !$disk->exists($effectiveSourcePath);
                                }
                                
                                Log::debug('*** FILE PHYSICALLY MOVED ***', [
                                    'from_type' => $effectiveSourceType,
                                    'from' => $effectiveSourcePath,
                                    'to' => $newFullPath,
                                    'file_id' => $media->id,
                                    'original_deleted' => $deleteSuccess,
                                    'destination_exists' => $disk->exists($newFullPath),
                                    'destination_size' => $disk->exists($newFullPath) ? $disk->size($newFullPath) : 0
                                ]);
                            } else {
                                Log::error('*** FAILED TO COPY FILE TO NEW LOCATION ***', [
                                    'from_type' => $effectiveSourceType,
                                    'from' => $effectiveSourcePath,
                                    'to' => $newFullPath,
                                    'file_id' => $media->id,
                                    'source_exists' => $sourceFileExists,
                                    'dest_exists' => $disk->exists($newFullPath),
                                    'dest_dir_exists' => $disk->exists($newDirectory)
                                ]);
                            }
                        } else {
                            Log::warning('*** SOURCE FILE DOES NOT EXIST FOR MOVE OPERATION ***', [
                                'path' => $oldFullPath,
                                'file_id' => $media->id,
                                'file_name' => $media->file_name,
                                'absolute_path' => storage_path('app/public/' . $oldFilePath . '/' . $media->file_name),
                                'media_disk' => $media->disk,
                                'disk_name' => config('media-library.disk_name'),
                            ]);
                            
                            // Additional diagnostic info - listing nearby files
                            $filesInDir = [];
                            try {
                                if ($disk->exists($oldFilePath)) {
                                    $filesInDir = $disk->files($oldFilePath);
                                } else {
                                    $filesInDir = ['DIRECTORY DOES NOT EXIST'];
                                }
                            } catch (\Exception $e) {
                                $filesInDir = ['ERROR: ' . $e->getMessage()];
                            }
                            
                            Log::debug('Files in source directory:', [
                                'directory' => $oldFilePath,
                                'files' => $filesInDir
                            ]);
                        }
                    } else {
                        Log::debug('*** FILE PATH UNCHANGED, NO MOVE REQUIRED ***', [
                            'path' => $oldFilePath,
                            'file_id' => $media->id,
                            'file_exists' => Storage::disk($oldDisk)->exists($oldFullPath)
                        ]);
                    }

                    Log::debug('*** FILE ASSOCIATION COMPLETED ***', [
                        'file_id' => $media->id,
                        'request_id' => $requestId,
                        'filename' => $media->file_name,
                        'old_path' => $oldFilePath,
                        'new_path' => $newFilePath,
                        'new_model_id' => $media->model_id,
                        'new_model_type' => $media->model_type,
                        'saved_successfully' => $media->wasChanged(),
                        'file_exists_at_final_location' => Storage::disk($oldDisk)->exists($newFullPath),
                        'final_file_size' => Storage::disk($oldDisk)->exists($newFullPath) ? 
                            Storage::disk($oldDisk)->size($newFullPath) : 0,
                        'final_url' => $media->getUrl(),
                        'completion_time' => now()->toDateTimeString()
                    ]);

                    $associatedCount++;
                } catch (\Exception $e) {
                    Log::error('Error updating media record', [
                        'file_id' => $media->id,
                        'request_id' => $requestId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $errorCount++;
                }
            }

            // Delete the temporary upload (soft delete)
            $temporaryUpload->delete();
            Log::info('Deleted temporary upload record', [
                'temp_upload_id' => $temporaryUpload->id,
                'token' => $token
            ]);

            Log::info('File association completed', [
                'token' => $token,
                'request_id' => $requestId,
                'total_files' => $mediaItems->count(),
                'associated_count' => $associatedCount,
                'error_count' => $errorCount
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error associating files: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'token' => $token,
                'request_id' => $requestId
            ]);
            return false;
        }
    }

    /**
     * Handle file upload for authenticated users
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            $logLevel = app()->environment('production') ? 'info' : 'debug';

            Log::log($logLevel, 'Admin file upload request received', [
                'user_id' => auth()->id(),
                'has_file' => $request->hasFile('file'),
                'file_name' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : 'no file',
                'environment' => app()->environment()
            ]);

            // Validate file
            if (!$request->hasFile('file')) {
                Log::error('No file provided in authenticated upload request');
                return response()->json(['error' => 'No file provided'], 400);
            }

            // Validate request parameters
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,txt,rtf|max:20480', // 20MB
                'instruction_request_id' => 'required|integer|exists:instruction_requests,id',
                'collection' => 'required|string|in:materials,assessments,syllabus,instructor_attachments',
            ]);

            // Get the instruction request
            $instructionRequest = InstructionRequests::findOrFail($request->input('instruction_request_id'));
            $collection = $request->input('collection');

            Log::log($logLevel, 'Adding file to instruction request', [
                'request_id' => $instructionRequest->id,
                'collection' => $collection,
                'file_name' => $request->file('file')->getClientOriginalName()
            ]);

            // Ensure upload directory exists
            $disk = config('media-library.disk_name');
            $uploadPath = 'uploads/' . $instructionRequest->id;

            if (!Storage::disk($disk)->exists($uploadPath)) {
                Storage::disk($disk)->makeDirectory($uploadPath);
                Log::log($logLevel, 'Created upload directory', [
                    'path' => $uploadPath,
                    'disk' => $disk
                ]);
            }

            // Add the file to the specified collection
            $media = $instructionRequest->addMediaFromRequest('file')
                ->usingName($request->file('file')->getClientOriginalName())
                ->withCustomProperties([
                    'uploaded_by' => auth()->user()->name ?? 'Unknown',
                    'uploaded_by_id' => auth()->id(),
                    'upload_date' => now()->toDateTimeString(),
                    'environment' => app()->environment()
                ])
                ->toMediaCollection($collection);

            Log::log($logLevel, 'File uploaded successfully by authenticated user', [
                'media_id' => $media->id,
                'collection' => $media->collection_name,
                'disk' => $media->disk,
                'file_name' => $media->file_name,
                'size' => $media->size,
                'path' => $media->getPath()
            ]);

            // Prepare file type info
            $extension = $request->file('file')->getClientOriginalExtension();
            $iconClass = $this->getFileIconClass($extension);

            return response()->json([
                'success' => true,
                'file' => [
                    'id' => $media->id,
                    'name' => $media->file_name,
                    'size' => $media->size,
                    'extension' => $extension,
                    'icon' => $iconClass,
                    'url' => $media->getUrl()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in authenticated file upload: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'environment' => app()->environment()
            ]);
            return response()->json(['error' => 'An error occurred while uploading the file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a file (for authenticated users)
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        try {
            // Find the media record
            $media = Media::findOrFail($id);

            // Check authorization (user must be admin or own the request)
            $instructionRequest = InstructionRequests::find($media->model_id);

            if (!$instructionRequest) {
                return response()->json(['error' => 'Associated instruction request not found'], 404);
            }

            // Log the deletion
            Log::info('Deleting file by authenticated user', [
                'media_id' => $media->id,
                'file_name' => $media->file_name,
                'user_id' => auth()->id(),
                'request_id' => $instructionRequest->id
            ]);

            // Delete the media
            $media->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error deleting file: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An error occurred while deleting the file.'], 500);
        }
    }

    /**
     * Get Font Awesome icon class based on file extension
     *
     * @param string $extension
     * @return string
     */
    private function getFileIconClass(string $extension): string
    {
        $iconMap = [
            'pdf' => 'fa-file-pdf-o',
            'doc' => 'fa-file-word-o',
            'docx' => 'fa-file-word-o',
            'ppt' => 'fa-file-powerpoint-o',
            'pptx' => 'fa-file-powerpoint-o',
            'txt' => 'fa-file-text-o',
            'rtf' => 'fa-file-text-o'
        ];

        return $iconMap[strtolower($extension)] ?? 'fa-file-o';
    }
}
