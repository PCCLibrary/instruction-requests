<?php

namespace App\Http\Controllers;

use App\Models\InstructionRequests;
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
     * Generate a secure upload token valid for 120 minutes
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

            Cache::put('upload_token_' . $token, [
                'created_at' => now(),
                'files' => []
            ], now()->addMinutes(120)); // 2 hour expiration

            Log::info('Token generated successfully', [
                'token' => $token
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
     * Validate upload token
     *
     * @param string $token
     * @return array|bool
     */
    private function validateToken(string $token)
    {
        $tokenData = Cache::get('upload_token_' . $token);
        
        if (!$tokenData) {
            Log::warning('Token not found in cache', ['token' => $token]);
            return false;
        }
        
        if (now()->diffInMinutes($tokenData['created_at']) > 120) {
            Log::warning('Token expired', [
                'token' => $token, 
                'created_at' => $tokenData['created_at'],
                'age' => now()->diffInMinutes($tokenData['created_at']) . ' minutes'
            ]);
            Cache::forget('upload_token_' . $token);
            return false;
        }
        
        Log::debug('Token validated successfully', [
            'token' => $token,
            'files_count' => count($tokenData['files']),
            'files' => $tokenData['files']
        ]);
        
        return $tokenData;
    }

    /**
     * Handle file upload for public form
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicUpload(Request $request)
    {
        try {
            // Set log level based on environment
            $logLevel = app()->environment('production') ? 'info' : 'debug';
            
            // Validate token
            $token = $request->header('X-Upload-Token');

            Log::log($logLevel, 'File upload request received', [
                'headers' => $request->headers->all(),
                'has_file' => $request->hasFile('file'),
                'file_name' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : 'no file',
                'environment' => app()->environment()
            ]);

            // Validate token
            $token = $request->header('X-Upload-Token');
            if (!$token) {
                Log::error('No upload token provided');
                return response()->json(['error' => 'No upload token provided'], 401);
            }

            $tokenData = $this->validateToken($token);

            if (!$tokenData) {
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

            // Create a temporary media record
            $tempModel = new InstructionRequests();
            
            // We need to temporarily save the model to get a real ID in the database
            // This is required for Media Library to work properly
            // Fill all required fields to satisfy the database constraints
            $tempModel->fill([
                'instruction_type' => 'temp',
                'status' => 'temp',
                'campus_id' => 1, // Default campus
                'department' => 'temp',
                'course_number' => 'temp',
                'course_crn' => 'temp',
                'number_of_students' => 0,
                'instructor_id' => 1 // Use a default instructor ID
            ]);
            
            // Save to get a database ID
            $tempModel->save();

            Log::log($logLevel, 'Creating temp model for file association', [
                'model_type' => get_class($tempModel),
                'model_id' => $tempModel->id,
                'temp_model_saved' => $tempModel->exists,
                'environment' => app()->environment()
            ]);

            try {
                // Add the file to the materials collection
                $media = $tempModel->addMediaFromRequest('file')
                    ->usingName($request->file('file')->getClientOriginalName())
                    ->withCustomProperties([
                        'upload_token' => $token,
                        'upload_date' => now()->toDateTimeString(),
                        'temporary' => true,
                        'upload_source' => 'public',
                        'environment' => app()->environment()
                    ])
                    ->toMediaCollection('materials');
                
                // Log detailed information about the media record
                Log::log($logLevel, 'Media record created', [
                    'media_id' => $media->id,
                    'media_exists' => $media->exists,
                    'media_saved' => !is_null($media->id),
                    'media_model_id' => $media->model_id,
                    'media_model_type' => $media->model_type,
                    'file_name' => $media->file_name,
                    'disk' => $media->disk
                ]);
                
                // After successful media creation, mark the temporary model for deletion after association
                // We don't delete it immediately because we need it for the media association
                $tempModel->status = 'to_delete';
                $tempModel->save();
                
                Log::log($logLevel, 'Temporary model marked for deletion', [
                    'temp_model_id' => $tempModel->id,
                    'status' => $tempModel->status
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to create media record', [
                    'temp_model_id' => $tempModel->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Re-throw the exception to be caught by the outer try-catch
            }

            Log::log($logLevel, 'File uploaded successfully', [
                'media_id' => $media->id,
                'collection' => $media->collection_name,
                'disk' => $media->disk,
                'file_name' => $media->file_name,
                'size' => $media->size,
                'mime_type' => $media->mime_type,
                'path' => $media->getPath(),
                'environment' => app()->environment()
            ]);

            // Update token with file ID
            $files = $tokenData['files'];
            $files[] = $media->id;

            Log::log($logLevel, 'Preparing to update token with file ID', [
                'token' => $token,
                'media_id' => $media->id,
                'media_id_type' => gettype($media->id),
                'old_files' => $tokenData['files'],
                'new_files' => $files
            ]);

            Cache::put('upload_token_' . $token, [
                'created_at' => $tokenData['created_at'],
                'files' => $files
            ], now()->addMinutes(120));

            // Verify the token was updated properly
            $updatedToken = Cache::get('upload_token_' . $token);
            Log::log($logLevel, 'Token updated with new file', [
                'token' => $token,
                'files' => $updatedToken['files'] ?? 'token not found',
                'cache_success' => !empty($updatedToken)
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
            $tokenData = $this->validateToken($token);
            
            if (!$tokenData) {
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
            
            // Update token data
            $files = array_diff($tokenData['files'], [$id]);
            
            Cache::put('upload_token_' . $token, [
                'created_at' => $tokenData['created_at'],
                'files' => $files
            ], now()->addMinutes(120));
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting file: ' . $e->getMessage());
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

        $tokenData = $this->validateToken($token);

        if (!$tokenData) {
            Log::error('Invalid token when associating files', ['token' => $token]);
            return false;
        }

        if (empty($tokenData['files'])) {
            Log::warning('No files to associate', ['token' => $token]);
            return true; // Nothing to do, but not an error
        }

        Log::info('Found files to associate', [
            'token' => $token,
            'file_count' => count($tokenData['files']),
            'files' => $tokenData['files']
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

            foreach ($tokenData['files'] as $fileId) {
                Log::info('Processing file association', [
                    'file_id' => $fileId,
                    'request_id' => $requestId
                ]);

                if (is_null($fileId)) {
                    Log::warning('Skipping null file ID', [
                        'token' => $token, 
                        'request_id' => $requestId
                    ]);
                    $errorCount++;
                    continue;
                }

                $media = Media::find($fileId);

                if ($media) {
                    Log::info('Found media to associate', [
                        'file_id' => $fileId,
                        'file_name' => $media->file_name,
                        'old_model_id' => $media->model_id,
                        'old_model_type' => $media->model_type,
                        'custom_properties' => $media->custom_properties
                    ]);

                    try {
                        $media->model_id = $requestId;
                        $media->model_type = InstructionRequests::class;
                        $media->setCustomProperty('temporary', false);
                        $media->setCustomProperty('associated', true);
                        $media->setCustomProperty('associated_date', now()->toDateTimeString());
                        $media->save();

                        Log::info('Associated file with request', [
                            'file_id' => $fileId,
                            'request_id' => $requestId,
                            'filename' => $media->file_name,
                            'new_path' => $media->getPath(),
                            'new_model_id' => $media->model_id,
                            'new_model_type' => $media->model_type,
                            'saved_successfully' => $media->wasChanged()
                        ]);

                        $associatedCount++;
                    } catch (\Exception $e) {
                        Log::error('Error updating media record', [
                            'file_id' => $fileId,
                            'request_id' => $requestId,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        $errorCount++;
                    }
                } else {
                    Log::warning('Media not found for association', [
                        'file_id' => $fileId,
                        'request_id' => $requestId
                    ]);
                    $errorCount++;
                }
            }

            // Clear the token
            Cache::forget('upload_token_' . $token);

            // Clean up any temporary instruction requests
            try {
                $tempRequests = InstructionRequests::where('status', 'temp')
                    ->orWhere('status', 'to_delete')
                    ->get();
                
                Log::info('Found temporary requests to clean up', [
                    'count' => $tempRequests->count(),
                    'ids' => $tempRequests->pluck('id')->toArray()
                ]);

                foreach ($tempRequests as $tempRequest) {
                    Log::info('Cleaning up temporary instruction request', [
                        'temp_id' => $tempRequest->id,
                        'status' => $tempRequest->status,
                        'created_at' => $tempRequest->created_at->toDateTimeString()
                    ]);

                    // We don't need to delete the media because it's already been reassociated
                    $tempRequest->delete();
                }
            } catch (\Exception $e) {
                Log::warning('Error cleaning up temporary requests', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Continue processing - this is just cleanup
            }

            Log::info('File association completed', [
                'token' => $token,
                'request_id' => $requestId,
                'total_files' => count($tokenData['files']),
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
