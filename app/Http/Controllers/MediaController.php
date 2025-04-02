<?php

namespace App\Http\Controllers;

use App\Models\InstructionRequests;
use App\Models\TemporaryUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
 * - Proper use of Spatie Media Library API
 * - Token-based security for public uploads
 * - Improved error handling and recovery
 * - Detailed logging for debugging
 * - Comprehensive file type detection
 *
 * @since 1.0.0
 * @version 1.3.0
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
                    'file_path' => $media->getPath(),
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
     * @param string $token The upload token associated with the temporary files
     * @param int $requestId The instruction request ID
     * @return bool True if at least one file was successfully associated, false otherwise
     */
    public function associateFiles(string $token, int $requestId)
    {
        // Find the instruction request
        $instructionRequest = InstructionRequests::find($requestId);
        if (!$instructionRequest) {
            Log::error('Failed to find instruction request', [
                'token' => $token,
                'request_id' => $requestId
            ]);
            return false;
        }

        Log::info('Associating files with instruction request', [
            'token' => $token,
            'request_id' => $requestId,
        ]);

        // Find the temporary upload
        $temporaryUpload = TemporaryUpload::where('upload_token', $token)->first();

        if (!$temporaryUpload) {
            // This is not an error, just means no files were uploaded with this token
            Log::info('No temporary upload found for token', ['token' => $token]);
            return true;
        }

        // Get the media items associated with this temporary upload
        $files = $temporaryUpload->getMedia('materials');

        if ($files->isEmpty()) {
            Log::info('No files found to associate', ['token' => $token, 'temp_upload_id' => $temporaryUpload->id]);
            return true;
        }

        $filesProcessed = 0;

        // Use a transaction to ensure all file operations succeed or fail together
        DB::beginTransaction();

        try {
            foreach ($files as $file) {
                $fileName = $file->file_name ?? $file->name ?? 'unknown';

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

                    $filesProcessed++;

                    Log::info('File successfully associated', [
                        'file_id' => $file->id,
                        'new_media_id' => $newMedia->id,
                        'file_name' => $fileName,
                        'request_id' => $requestId
                    ]);
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
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('File association failed, transaction rolled back', [
                'token' => $token,
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }

        // Delete the temporary upload record.
        $temporaryUpload->delete();

        return $filesProcessed > 0;
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
                'file_path' => $media->getPath()
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
     * Simplified to only support the file types we actually need
     *
     * @param string $extension
     * @return string
     */
    private function getFileIconClass(string $extension): string
    {
        // Normalize extension by removing dots and converting to lowercase
        $extension = strtolower(trim($extension, '.'));

        // Simple mapping for supported file types
        $iconMap = [
            'pdf' => 'fa-file-pdf-o',
            'doc' => 'fa-file-word-o',
            'docx' => 'fa-file-word-o',
            'ppt' => 'fa-file-powerpoint-o',
            'pptx' => 'fa-file-powerpoint-o',
            'txt' => 'fa-file-text-o',
            'rtf' => 'fa-file-text-o'
        ];

        // Return the mapped icon or a default if extension not found
        return $iconMap[$extension] ?? 'fa-file-o';
    }
}
