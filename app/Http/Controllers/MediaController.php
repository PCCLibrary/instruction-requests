<?php

namespace App\Http\Controllers;

use App\Models\InstructionRequests;
use App\Models\TemporaryUpload;
use Illuminate\Http\Request;
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
            $tempPath = 'uploads/temp/';

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
                $uploadPath = 'uploads/temp/';

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
                    'media_file_path' => $media->file_name,
                    'file_exists_temp_direct' => Storage::disk($media->disk)->exists($uploadPath . $media->file_name),
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to create media record', [
                    'temp_upload_id' => $temporaryUpload->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Re-throw the exception to be caught by the outer try-catch
            }

            // Get the full paths for logging
            $mediaPath = $media->getPath();
            $fullFilePath = $mediaPath . $media->file_name;
            
            Log::debug('*** FILE UPLOAD COMPLETED SUCCESSFULLY ***', [
                'media_id' => $media->id,
                'collection' => $media->collection_name,
                'disk' => $media->disk,
                'file_name' => $media->file_name,
                'size' => $media->size,
                'mime_type' => $media->mime_type,
                'directory_path' => $mediaPath,
                'full_file_path' => $fullFilePath,
                'file_exists' => Storage::disk($media->disk)->exists($fullFilePath),
                'file_size_on_disk' => Storage::disk($media->disk)->exists($fullFilePath) ?
                    Storage::disk($media->disk)->size($fullFilePath) : 0,
                'url' => $media->getUrl(),
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
            $disk = config('media-library.disk_name');

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
                    
                    // Define source and target paths clearly
                    $sourcePath = 'uploads/temp/' . $media->file_name;
                    $sourcePath = $this->cleanPath($sourcePath);
                    
                    // Update model properties
                    $media->model_id = $requestId;
                    $media->model_type = InstructionRequests::class;
                    $media->setCustomProperty('temporary', false);
                    $media->setCustomProperty('associated', true);
                    $media->setCustomProperty('associated_date', now()->toDateTimeString());
                    $media->save();

                    // Get the new path using the model's getPath method after updating model properties
                    // This ensures the correct path is used based on the CustomPathGenerator
                    $targetDir = $media->getPath();
                    
                    // Clean the path to handle potential absolute paths
                    $targetDir = $this->cleanPath($targetDir);
                    
                    // Path already has trailing slash from CustomPathGenerator
                    $targetPath = $targetDir . $media->file_name;
                    
                    // Log model update info
                    Log::debug('*** MEDIA RECORD UPDATED ***', [
                        'file_id' => $media->id,
                        'old_path' => 'uploads/temp/',
                        'new_path' => $targetDir,
                        'original_media_path' => $media->getPath(),
                        'cleaned_media_path' => $this->cleanPath($media->getPath()),
                        'source_path' => $sourcePath,
                        'target_path' => $targetPath,
                        'old_model_id' => $temporaryUpload->id,
                        'new_model_id' => $requestId,
                        'old_model_type' => get_class($temporaryUpload),
                        'new_model_type' => InstructionRequests::class,
                        'custom_properties' => $media->custom_properties
                    ]);
                    
                    // Check if the source file exists
                    if (!Storage::disk($disk)->exists($sourcePath)) {
                        Log::warning('Source file not found for move operation', [
                            'file_id' => $media->id,
                            'file_name' => $media->file_name,
                            'source_path' => $sourcePath
                        ]);
                        
                        // If the source file doesn't exist where expected, try to find it
                        $altSourcePaths = [
                            'uploads/temp/' . $media->file_name,  // With trailing slash
                            'uploads/temp' . $media->file_name,   // Without trailing slash 
                            'uploadstemp/' . $media->file_name,   // No slash variation
                            'uploadstemp' . $media->file_name,    // Even more wrong path but worth checking
                            $media->file_name                     // Just the filename
                        ];
                        
                        $sourceExists = false;
                        foreach ($altSourcePaths as $altPath) {
                            if (Storage::disk($disk)->exists($altPath)) {
                                $sourcePath = $altPath;
                                $sourceExists = true;
                                Log::debug('Found source file in alternative location', [
                                    'file_path' => $altPath
                                ]);
                                break;
                            }
                        }
                        
                        if (!$sourceExists) {
                            Log::error('Source file not found after searching alternatives', [
                                'file_id' => $media->id,
                                'file_name' => $media->file_name
                            ]);
                            continue; // Skip this file
                        }
                    }
                    
                    // Ensure the target directory exists
                    if (!Storage::disk($disk)->exists($targetDir)) {
                        Storage::disk($disk)->makeDirectory($targetDir);
                        Log::debug('Created target directory', [
                            'directory' => $targetDir,
                            'created' => Storage::disk($disk)->exists($targetDir)
                        ]);
                    }
                    
                    // Move the file using Laravel's Storage facade
                    Log::debug('Moving file', [
                        'from' => $sourcePath,
                        'to' => $targetPath,
                        'disk' => $disk
                    ]);
                    
                    // Use copy then delete pattern to avoid path issues
                    if (Storage::disk($disk)->copy($sourcePath, $targetPath)) {
                        // Delete the source file after successful copy
                        Storage::disk($disk)->delete($sourcePath);
                        
                        Log::debug('*** FILE PHYSICALLY MOVED ***', [
                            'from' => $sourcePath,
                            'to' => $targetPath,
                            'file_id' => $media->id,
                            'original_deleted' => !Storage::disk($disk)->exists($sourcePath),
                            'destination_exists' => Storage::disk($disk)->exists($targetPath),
                            'destination_size' => Storage::disk($disk)->exists($targetPath) ? 
                                Storage::disk($disk)->size($targetPath) : 0
                        ]);
                    } else {
                        Log::error('Failed to move file', [
                            'from' => $sourcePath,
                            'to' => $targetPath
                        ]);
                    }

                    // Get URL directly from media library
                    $finalUrl = $media->getUrl();
                    
                    // Check for duplicate URL patterns or paths
                    $appUrl = config('app.url');
                    $storageBasePath = '/storage/';
                    
                    // Fix duplicate storage paths
                    if (substr_count($finalUrl, $storageBasePath) > 1) {
                        // Keep only the first occurrence and everything after
                        $pos = strpos($finalUrl, $storageBasePath);
                        $finalUrl = substr($finalUrl, 0, $pos) . substr($finalUrl, $pos);
                        Log::debug('Fixed duplicate storage paths', [
                            'original_url' => $media->getUrl(),
                            'fixed_url' => $finalUrl
                        ]);
                    }
                    
                    // Fix any duplicated app URLs
                    if (substr_count($finalUrl, $appUrl) > 1) {
                        $finalUrl = $appUrl . parse_url($finalUrl, PHP_URL_PATH);
                        Log::debug('Fixed URL with duplicate app base', [
                            'original_url' => $media->getUrl(),
                            'fixed_url' => $finalUrl
                        ]);
                    }
                    
                    // Fix absolute paths in URL paths
                    $absolutePathPattern = '/\/var\/www\/html\/.*\/storage\//';
                    if (preg_match($absolutePathPattern, $finalUrl)) {
                        $fixedUrl = preg_replace($absolutePathPattern, $storageBasePath, $finalUrl);
                        Log::debug('Fixed absolute paths in URL', [
                            'original_url' => $finalUrl,
                            'fixed_url' => $fixedUrl
                        ]);
                        $finalUrl = $fixedUrl;
                    }
                    
                    // Update additional logging info
                    Log::debug('*** FILE ASSOCIATION COMPLETED ***', [
                        'file_id' => $media->id,
                        'request_id' => $requestId,
                        'filename' => $media->file_name,
                        'path' => $targetDir,
                        'final_path' => $targetDir . $media->file_name,
                        'media_path' => $media->getPath(),
                        'storage_path' => storage_path('app/public'),
                        'app_url' => config('app.url'),
                        'base_path' => base_path(),
                        'public_path' => public_path(),
                        'new_model_id' => $media->model_id,
                        'new_model_type' => $media->model_type,
                        'saved_successfully' => true,
                        'file_exists_at_final_location' => Storage::disk($disk)->exists($targetPath),
                        'final_file_size' => Storage::disk($disk)->exists($targetPath) ? 
                            Storage::disk($disk)->size($targetPath) : 0,
                        'original_url' => $media->getUrl(),
                        'fixed_url' => $finalUrl,
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

    /**
     * Clean path to ensure it's relative to the storage disk, not absolute
     * 
     * @param string $path The path to clean
     * @return string The cleaned path
     */
    private function cleanPath(string $path): string
    {
        // Storage path to detect and remove
        $storagePath = storage_path('app/public/'); 
        
        // Replace storage path with empty string if found
        if (strpos($path, $storagePath) === 0) {
            return substr($path, strlen($storagePath));
        }
        
        // Alternative: just check for any duplicate directory structures
        $secondaryCheck = 'var/www/html/';
        if (substr_count($path, $secondaryCheck) > 1) {
            // Find the position of the second occurrence
            $firstPos = strpos($path, $secondaryCheck);
            $secondPos = strpos($path, $secondaryCheck, $firstPos + 1);
            
            // Keep only from the second occurrence onward
            return substr($path, $secondPos);
        }
        
        return $path;
    }
}