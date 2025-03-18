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
        $token = Str::random(40);
        
        Log::info('Generating upload token', [
            'token' => $token,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        Cache::put('upload_token_' . $token, [
            'created_at' => now(),
            'files' => []
        ], now()->addMinutes(120)); // 2 hour expiration
        
        return response()->json(['token' => $token]);
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
            $tempModel->id = 0; // Placeholder ID

            Log::log($logLevel, 'Creating temp model for file association', [
                'model_type' => get_class($tempModel),
                'model_id' => $tempModel->id,
                'environment' => app()->environment()
            ]);

            // Add the file to the materials collection
            $media = $tempModel->addMediaFromRequest('file')
                ->usingName($request->file('file')->getClientOriginalName())
                ->withCustomProperties([
                    'upload_token' => $token,
                    'upload_date' => now()->toDateTimeString(),
                    'temporary' => true,
                    'environment' => app()->environment()
                ])
                ->toMediaCollection('materials');

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

            Cache::put('upload_token_' . $token, [
                'created_at' => $tokenData['created_at'],
                'files' => $files
            ], now()->addMinutes(120));

            Log::log($logLevel, 'Token updated with new file', [
                'token' => $token,
                'files' => $files
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
        
        try {
            // Get the instruction request
            $request = InstructionRequests::findOrFail($requestId);
            Log::info('Found instruction request', [
                'request_id' => $requestId,
                'model_type' => get_class($request)
            ]);
            
            // Associate all files with the request
            $associatedCount = 0;
            $errorCount = 0;
            
            foreach ($tokenData['files'] as $fileId) {
                Log::info('Processing file association', ['file_id' => $fileId]);
                
                $media = Media::find($fileId);
                
                if ($media) {
                    Log::info('Found media to associate', [
                        'file_id' => $fileId,
                        'file_name' => $media->file_name,
                        'old_model_id' => $media->model_id,
                        'old_model_type' => $media->model_type
                    ]);
                    
                    $media->model_id = $requestId;
                    $media->model_type = InstructionRequests::class;
                    $media->setCustomProperty('temporary', false);
                    $media->save();
                    
                    Log::info('Associated file with request', [
                        'file_id' => $fileId,
                        'request_id' => $requestId,
                        'filename' => $media->file_name,
                        'new_path' => $media->getPath()
                    ]);
                    
                    $associatedCount++;
                } else {
                    Log::warning('Media not found for association', ['file_id' => $fileId]);
                    $errorCount++;
                }
            }
            
            // Clear the token
            Cache::forget('upload_token_' . $token);
            
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
