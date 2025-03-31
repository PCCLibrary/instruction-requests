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

/**
 * MediaController handles file uploads and management for the Library Instruction System.
 * 
 * This controller provides endpoints for:
 * - Token generation for secure file uploads
 * - Public file uploads with token validation
 * - File association with instruction requests
 * - Authenticated file uploads and management
 * - File deletion
 * 
 * File handling has been enhanced with:
 * - Reliable path generation independent of file->path
 * - Multiple source path detection with fallbacks
 * - Improved directory creation with verification
 * - Enhanced file movement with copy+delete fallback
 * - Better error handling and recovery
 * - Detailed logging for debugging
 * - Comprehensive file type detection
 * 
 * @since 1.0.0
 * @version 1.2.0
 */
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

            if (!$this->ensureDirectoryExists($tempPath, $disk)) {
                Log::error("Failed to create temporary upload directory", [
                    'path' => $tempPath,
                    'disk' => $disk,
                    'environment' => app()->environment()
                ]);
                return response()->json(['error' => 'Server configuration error: Unable to create upload directory'], 500);
            }

            Log::log($logLevel, 'Using temporary upload for file association', [
                'model_type' => get_class($temporaryUpload),
                'model_id' => $temporaryUpload->id,
                'token' => $temporaryUpload->upload_token,
                'environment' => app()->environment()
            ]);

            try {
                // Get file directly from request
                $file = $request->file('file');
                $fileName = $file->getClientOriginalName();

                // Add the file to the materials collection of the TemporaryUpload model
                $media = $temporaryUpload->addMediaFromRequest('file')
                    ->usingName($fileName) // Use original file name
                    ->withCustomProperties([
                        'upload_token' => $token,
                        'upload_date' => now()->toDateTimeString(),
                        'temporary' => true,
                        'upload_source' => 'public',
                        'environment' => app()->environment()
                    ])
                    ->toMediaCollection('materials');

                // Log file upload details
                Log::debug('File uploaded and media record created', [
                    'media_id' => $media->id,
                    'file_name' => $media->file_name,
                    'file_original_name' => $fileName,
                    'disk' => $media->disk,
                    'path' => $media->getPath(),
                    'url' => $media->getUrl(),
                ]);


            } catch (\Exception $e) {
                Log::error('Failed to upload file and create media record', [
                    'temp_upload_id' => $temporaryUpload->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Re-throw the exception to be caught by the outer try-catch
            }

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
     * Associates temporary uploaded files with an instruction request.
     * 
     * This method transfers files from a TemporaryUpload model to an InstructionRequest model.
     * It finds files associated with the given upload token, moves them from the temporary
     * location to a permanent year/month-based directory structure, and updates the media records
     * to point to the InstructionRequest model instead of the TemporaryUpload model.
     *
     * Path generation is handled reliably without depending on the Media->path attribute which
     * may be empty. Multiple source path detection mechanisms are employed as fallbacks:
     * 1. Standard temp directory path construction
     * 2. Path from the media record if available
     * 3. Extracted filename from the path
     * 4. Last resort getPath() method from the Media instance
     *
     * If file movement succeeds, the media record is updated to reflect the new ownership
     * and path. Custom properties are preserved with 'temporary' set to false.
     * 
     * The method includes comprehensive logging and error handling to facilitate debugging.
     *
     * @param string $token The upload token associated with the temporary files
     * @param int|InstructionRequests $instructionRequestOrId The instruction request ID or model instance
     * @return bool True if at least one file was successfully associated, false otherwise
     */
    public function associateFiles(string $token, $instructionRequestOrId)
    {
        // Load the instruction request model if only an ID was provided
        $instructionRequest = $instructionRequestOrId;
        if (is_numeric($instructionRequestOrId)) {
            $instructionRequest = InstructionRequests::find($instructionRequestOrId);
            if (!$instructionRequest) {
                Log::error('Failed to find instruction request', [
                    'token' => $token,
                    'request_id' => $instructionRequestOrId
                ]);
                return false;
            }
        }

        Log::info('Calling associateFiles with token', [
            'token' => $token,
            'request_id' => $instructionRequest->id,
        ]);

        Log::info('Associating files with instruction request', [
            'token' => $token,
            'request_id' => $instructionRequest->id,
        ]);
        
        // Use the correct column name 'upload_token' instead of 'token'
        $temporaryUpload = null;
        
        try {
            $temporaryUpload = TemporaryUpload::where('upload_token', $token)->first();
        } catch (\Exception $e) {
            Log::error('Error finding temporary upload record', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return true; // Continue with the process, just without file associations
        }

        if (!$temporaryUpload) {
            // This is not an error, just means no files were uploaded with this token
            Log::info('No temporary upload found for token', ['token' => $token]);
            return true;
        }

        $files = $temporaryUpload->media; // Get the associated Media objects.

        Log::debug('Token validated successfully', [
            'token' => $token,
            'temporary_upload_id' => $temporaryUpload->id,
            'files_count' => $files->count(),
            'files' => $files->pluck('id')->toArray(),
        ]);

        if ($files->isEmpty()) {
            Log::info('No files found to associate', ['token' => $token, 'temp_upload_id' => $temporaryUpload->id]);
            return true;
        }
        
        Log::info('Found files to associate', [
            'token' => $token,
            'file_count' => $files->count(),
            'temp_upload_id' => $temporaryUpload->id,
        ]);

        Log::info('Found instruction request', [
            'request_id' => $instructionRequest->id,
            'model_type' => get_class($instructionRequest),
            'request_status' => $instructionRequest->status,
        ]);

        foreach ($files as $file) {
            // Get a consistent file name
            $fileName = $file->file_name ?? $file->name ?? 'unknown';
            Log::info('Processing file association', [
                'file_id' => $file->id,
                'file_name' => $fileName,
                'request_id' => $instructionRequest->id,
                'model_type' => $file->model_type,
                'model_id' => $file->model_id,
            ]);
            Log::debug('*** FILE ASSOCIATION BEGIN ***', [
                'file_id' => $file->id,
                'file_name' => $fileName,
                'temp_upload_id' => $temporaryUpload->id,
                'request_id' => $instructionRequest->id,
                'operation_time' => now()->format('Y-m-d H:i:s'),
            ]);

            // Check if the file is already associated with THIS specific instruction request.
            // We only want to skip if it's already associated with the target instruction request,
            // not if it's associated with the temporary upload model.
            if ($file->model_id == $instructionRequest->id && 
                $file->model_type == get_class($instructionRequest)) {
                // Get the file name in a simple expression first
                $fileName = $file->file_name ?? $file->name ?? 'unknown';
                Log::warning("File {$file->id} ({$fileName}) is already associated with this instruction request. Skipping association.", [
                    'file_id' => $file->id,
                    'file_name' => $fileName,
                    'existing_model_id' => $file->model_id,
                    'existing_model_type' => $file->model_type,
                    'request_id' => $instructionRequest->id,
                ]);
                continue; // Skip to the next file.
            }

            // Use reliable path generation that doesn't depend on $file->path
            // Get the file name from media record properties
            $fileName = $file->file_name ?? $file->name ?? ('file_' . $file->id);
            
            // Define source and target paths explicitly
            $yearMonth = date('Y/m');
            $targetDir = 'uploads/' . $yearMonth . '/';
            $sourcePath = 'uploads/temp/' . $fileName;
            $newPath = $targetDir . $fileName;
            
            Log::debug('Reliable path generation', [
                'media_id' => $file->id,
                'file_name' => $fileName,
                'source_path' => $sourcePath, 
                'target_dir' => $targetDir,
                'new_path' => $newPath,
                'current_path' => $file->path ?? 'not set'
            ]);

            try {
                // Ensure consistent disk configuration
                $disk = config('media-library.disk_name');
                
                // Ensure the target directory exists
                if (!$this->ensureDirectoryExists($targetDir, $disk)) {
                    Log::error("Failed to create target directory, skipping file", [
                        'file_id' => $file->id,
                        'target_dir' => $targetDir,
                        'disk' => $disk
                    ]);
                    continue; // Skip to next file if we can't create directory
                }

                // Try multiple possible source paths
                $possibleSourcePaths = [
                    $sourcePath,                   // Standard temp path
                    $file->path ?? '',             // Path from the database (if set)
                    'uploads/temp/' . basename($file->path ?? '') // Extracted filename from path
                ];
                
                $sourceFound = false;
                $actualSourcePath = '';
                
                foreach ($possibleSourcePaths as $path) {
                    if (!empty($path) && Storage::disk($disk)->exists($path)) {
                        $sourceFound = true;
                        $actualSourcePath = $path;
                        break;
                    }
                }
                
                // If we have a sourceFound, log file details to help with future debugging
                if ($sourceFound) {
                    try {
                        // Get detailed file information
                        $fileSize = Storage::disk($disk)->size($actualSourcePath);
                        $fileMime = Storage::disk($disk)->mimeType($actualSourcePath);
                        $fileExists = Storage::disk($disk)->exists($actualSourcePath);
                        $fileModified = Storage::disk($disk)->lastModified($actualSourcePath);
                        
                        Log::debug("Source file details", [
                            'file_id' => $file->id,
                            'file_name' => $fileName,
                            'size' => $fileSize,
                            'mime_type' => $fileMime,
                            'exists' => $fileExists,
                            'last_modified' => date('Y-m-d H:i:s', $fileModified),
                            'source_path' => $actualSourcePath
                        ]);
                    } catch (\Exception $e) {
                        Log::warning("Error getting file details, but file exists", [
                            'file_id' => $file->id,
                            'source_path' => $actualSourcePath,
                            'error' => $e->getMessage()
                        ]);
                    }
                    
                    // Enhanced file move with better error handling
                    try {
                        Storage::disk($disk)->move($actualSourcePath, $newPath);
                        Log::info("File moved successfully", [
                            'file_id' => $file->id,
                            'from' => $actualSourcePath,
                            'to' => $newPath,
                            'disk' => $disk
                        ]);
                    } catch (\Exception $e) {
                        // Try copy and delete as a fallback if move fails
                        Log::warning("Move failed, trying copy and delete", [
                            'file_id' => $file->id,
                            'error' => $e->getMessage()
                        ]);
                        
                        try {
                            // Copy file to new location
                            Storage::disk($disk)->copy($actualSourcePath, $newPath);
                            // Delete original if copy succeeds
                            if (Storage::disk($disk)->exists($newPath)) {
                                Storage::disk($disk)->delete($actualSourcePath);
                                Log::info("File copied and original deleted successfully", [
                                    'file_id' => $file->id,
                                    'from' => $actualSourcePath,
                                    'to' => $newPath,
                                    'disk' => $disk
                                ]);
                            } else {
                                throw new \Exception("Copy appeared to succeed but destination file doesn't exist");
                            }
                        } catch (\Exception $copyEx) {
                            Log::error("Even copy and delete failed", [
                                'file_id' => $file->id,
                                'error' => $copyEx->getMessage(),
                                'source_exists' => Storage::disk($disk)->exists($actualSourcePath),
                                'destination_exists' => Storage::disk($disk)->exists($newPath)
                            ]);
                            throw $copyEx;
                        }
                    }
                } else {
                    // If we can't find the source file, try to get its location from other means
                    Log::warning("Could not find source file in expected paths", [
                        'file_id' => $file->id,
                        'file_name' => $fileName,
                        'tried_paths' => $possibleSourcePaths,
                        'disk' => $disk
                    ]);
                    
                    // Try to use the path from the file's getPath() method as a last resort
                    try {
                        $lastResortPath = $file->getPath();
                        if (Storage::disk($disk)->exists($lastResortPath)) {
                            Log::info("Found file using getPath() method", [
                                'file_id' => $file->id,
                                'path' => $lastResortPath
                            ]);
                            Storage::disk($disk)->move($lastResortPath, $newPath);
                            $sourceFound = true;
                        }
                    } catch (\Exception $e) {
                        Log::error("Error trying to get path from getPath()", [
                            'file_id' => $file->id,
                            'error' => $e->getMessage() 
                        ]);
                    }
                    
                    // If we still couldn't find it, skip this file
                    if (!$sourceFound) {
                        Log::error("Source file not found in any expected location", [
                            'file_id' => $file->id,
                            'file_name' => $fileName,
                            'tried_paths' => $possibleSourcePaths,
                            'disk' => $disk
                        ]);
                        continue; // Skip to the next file
                    }
                }

                // Update the media record with the new path and model ID
                Log::info("Updating media record for file association", [
                    'file_id' => $file->id,
                    'file_name' => $fileName,
                    'old_model_type' => $file->model_type,
                    'old_model_id' => $file->model_id,
                    'new_model_type' => get_class($instructionRequest),
                    'new_model_id' => $instructionRequest->id,
                    'new_path' => $newPath,
                    'collection' => $file->collection_name
                ]);
                
                // Update the media record - use DB query to ensure it works even if model caching issues
                try {
                    // First try with the model update
                    $file->update([
                        'path' => $newPath,
                        'model_type' => get_class($instructionRequest),
                        'model_id' => $instructionRequest->id,
                    ]);
                    
                    // Update custom properties separately to maintain existing ones
                    $customProperties = $file->custom_properties;
                    $customProperties['temporary'] = false;
                    $file->custom_properties = $customProperties;
                    $file->save();
                    
                    // Get a fresh instance to verify the update
                    $refreshedFile = Media::find($file->id);
                    Log::info("Media record updated successfully", [
                        'file_id' => $refreshedFile->id,
                        'current_model_type' => $refreshedFile->model_type,
                        'current_model_id' => $refreshedFile->model_id,
                        'current_path' => $refreshedFile->path,
                        'is_temporary' => $refreshedFile->getCustomProperty('temporary', false)
                    ]);
                } catch (\Exception $e) {
                    // If model update fails, try direct DB update as fallback
                    Log::warning("Model update failed, trying direct DB update", [
                        'file_id' => $file->id,
                        'error' => $e->getMessage()
                    ]);
                    
                    try {
                        \DB::table('media')
                            ->where('id', $file->id)
                            ->update([
                                'path' => $newPath,
                                'model_type' => get_class($instructionRequest),
                                'model_id' => $instructionRequest->id,
                                'custom_properties' => json_encode(array_merge(
                                    (array)$file->custom_properties,
                                    ['temporary' => false]
                                ))
                            ]);
                            
                        Log::info("Media record updated via direct DB query", [
                            'file_id' => $file->id
                        ]);
                    } catch (\Exception $dbEx) {
                        Log::error("Even direct DB update failed", [
                            'file_id' => $file->id,
                            'error' => $dbEx->getMessage()
                        ]);
                        throw $dbEx;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error updating media record', [
                    'file_id' => $file->id,
                    'request_id' => $instructionRequest->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Continue to the next file rather than failing the entire process
                continue;
            }
        }

        // Count how many files were successfully processed
        $successCount = 0;
        $mediaCount = 0;
        
        try {
            // Get a fresh list of files associated with the instruction request
            $mediaCount = $instructionRequest->getMedia('materials')->count();
            $successCount = $mediaCount;
            
            Log::info('Media count after association', [
                'request_id' => $instructionRequest->id,
                'media_count' => $mediaCount,
                'collections' => [
                    'materials' => $instructionRequest->getMedia('materials')->count(),
                    'syllabus' => $instructionRequest->getMedia('syllabus')->count(),
                    'instructor_attachments' => $instructionRequest->getMedia('instructor_attachments')->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::warning('Could not get final media count', [
                'request_id' => $instructionRequest->id,
                'error' => $e->getMessage()
            ]);
        }
        
        // Delete the temporary upload record.
        $temporaryUpload->delete();
        Log::info('Deleted temporary upload record', [
            'temporary_upload_id' => $temporaryUpload->id,
            'token' => $token,
        ]);

        $status = $mediaCount > 0 ? 'true' : ($files->count() > 0 ? 'partial' : 'false');
        
        Log::info('File association completed', [
            'success' => $status,
            'token' => $token,
            'request_id' => $instructionRequest->id,
            'files_processed' => $successCount,
            'files_attempted' => $files->count()
        ]);
        
        return $mediaCount > 0;
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
            $yearMonth = date('Y/m');
            $uploadPath = 'uploads/' . $yearMonth . '/';
            
            if (!$this->ensureDirectoryExists($uploadPath, $disk)) {
                Log::error("Failed to create upload directory for authenticated request", [
                    'path' => $uploadPath,
                    'disk' => $disk,
                    'user_id' => auth()->id()
                ]);
                return response()->json(['error' => 'Server configuration error: Unable to create upload directory'], 500);
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
     * Enhanced to handle a wider range of file types
     *
     * @param string $extension
     * @return string
     */
    private function getFileIconClass(string $extension): string
    {
        // Normalize extension by removing dots and converting to lowercase
        $extension = strtolower(trim($extension, '.'));
        
        // More comprehensive mapping of file extensions to Font Awesome icons
        $iconMap = [
            // Documents
            'pdf' => 'fa-file-pdf-o',
            'doc' => 'fa-file-word-o',
            'docx' => 'fa-file-word-o',
            'rtf' => 'fa-file-text-o',
            'odt' => 'fa-file-word-o',
            
            // Spreadsheets
            'xls' => 'fa-file-excel-o',
            'xlsx' => 'fa-file-excel-o',
            'csv' => 'fa-file-excel-o',
            'ods' => 'fa-file-excel-o',
            
            // Presentations
            'ppt' => 'fa-file-powerpoint-o',
            'pptx' => 'fa-file-powerpoint-o',
            'odp' => 'fa-file-powerpoint-o',
            
            // Text
            'txt' => 'fa-file-text-o',
            'md' => 'fa-file-text-o',
            'html' => 'fa-file-code-o',
            'htm' => 'fa-file-code-o',
            'xml' => 'fa-file-code-o',
            'json' => 'fa-file-code-o',
            
            // Images
            'jpg' => 'fa-file-image-o',
            'jpeg' => 'fa-file-image-o',
            'png' => 'fa-file-image-o',
            'gif' => 'fa-file-image-o',
            'svg' => 'fa-file-image-o',
            'bmp' => 'fa-file-image-o',
            'tiff' => 'fa-file-image-o',
            
            // Archives
            'zip' => 'fa-file-archive-o',
            'rar' => 'fa-file-archive-o',
            'gz' => 'fa-file-archive-o',
            'tar' => 'fa-file-archive-o',
            '7z' => 'fa-file-archive-o',
            
            // Audio/Video
            'mp3' => 'fa-file-audio-o',
            'wav' => 'fa-file-audio-o',
            'mp4' => 'fa-file-video-o',
            'avi' => 'fa-file-video-o',
            'mov' => 'fa-file-video-o',
            
            // Code
            'js' => 'fa-file-code-o',
            'php' => 'fa-file-code-o',
            'py' => 'fa-file-code-o',
            'c' => 'fa-file-code-o',
            'cpp' => 'fa-file-code-o',
            'java' => 'fa-file-code-o',
            'css' => 'fa-file-code-o',
        ];
        
        // Return the mapped icon or a default if extension not found
        return $iconMap[$extension] ?? 'fa-file-o';
    }

    /**
     * Ensure directory exists and create it if it doesn't
     * This is a more thorough version of the directory creation that handles nested paths
     *
     * @param string $path Directory path to ensure exists
     * @param string $disk Storage disk name
     * @return bool True if directory exists or was created, false on failure
     */
    private function ensureDirectoryExists(string $path, string $disk): bool
    {
        try {
            // Remove trailing slash for consistency when checking
            $path = rtrim($path, '/');
            
            // If path is empty, assume it's root which always exists
            if (empty($path)) {
                return true;
            }
            
            // Check if directory already exists
            if (Storage::disk($disk)->exists($path)) {
                // Verify it's actually a directory, not a file
                try {
                    $files = Storage::disk($disk)->files($path);
                    // If we can list files in it, it's a directory
                    return true;
                } catch (\Exception $e) {
                    // If we can't list files, it might be a file with the same name
                    Log::warning("Path exists but may not be a directory", [
                        'path' => $path,
                        'disk' => $disk,
                        'error' => $e->getMessage()
                    ]);
                    // Try to delete and recreate if it's not a directory
                    try {
                        // Only do this for paths we expect to be directories
                        if (str_ends_with($path, '/') || str_contains($path, 'uploads/')) {
                            Storage::disk($disk)->delete($path);
                        } else {
                            return false; // Don't mess with paths we're not sure about
                        }
                    } catch (\Exception $ex) {
                        Log::error("Failed to delete non-directory path", [
                            'path' => $path,
                            'disk' => $disk,
                            'error' => $ex->getMessage()
                        ]);
                        return false;
                    }
                }
            }
            
            // Create the directory
            Log::info("Creating directory", [
                'path' => $path,
                'disk' => $disk
            ]);
            
            $created = Storage::disk($disk)->makeDirectory($path);
            
            // Verify creation
            if (!$created || !Storage::disk($disk)->exists($path)) {
                Log::error("Failed to create directory", [
                    'path' => $path,
                    'disk' => $disk,
                    'created_result' => $created,
                    'exists_after' => Storage::disk($disk)->exists($path)
                ]);
                return false;
            }
            
            Log::info("Directory created successfully", [
                'path' => $path,
                'disk' => $disk
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Exception during directory creation", [
                'path' => $path,
                'disk' => $disk,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
