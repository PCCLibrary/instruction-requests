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
            // Validate token
            $token = $request->header('X-Upload-Token');

            Log::info('File upload request received', [
                'headers' => $request->headers->all(),
                'has_file' => $request->hasFile('file'),
                'file_name' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : 'no file'
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
            
            Log::info('Validating file', [
                'name' => $request->file('file')->getClientOriginalName(),
                'size' => $request->file('file')->getSize(),
                'mime' => $request->file('file')->getMimeType()
            ]);
            
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,txt,rtf|max:20480', // 20MB
            ]);
            
            // Create a temporary media record
            $tempModel = new InstructionRequests();
            $tempModel->id = 0; // Placeholder ID
            
            Log::info('Creating temp model for file association', [
                'model_type' => get_class($tempModel),
                'model_id' => $tempModel->id
            ]);
            
            // Add the file to the materials collection
            $media = $tempModel->addMediaFromRequest('file')
                ->usingName($request->file('file')->getClientOriginalName())
                ->withCustomProperties([
                    'upload_token' => $token,
                    'upload_date' => now()->toDateTimeString(),
                    'temporary' => true,
                ])
                ->toMediaCollection('materials');
            
            Log::info('File uploaded successfully', [
                'media_id' => $media->id,
                'collection' => $media->collection_name,
                'disk' => $media->disk,
                'file_name' => $media->file_name,
                'size' => $media->size,
                'mime_type' => $media->mime_type,
                'path' => $media->getPath(),
                'custom_properties' => $media->custom_properties
            ]);
            
            // Update token with file ID
            $files = $tokenData['files'];
            $files[] = $media->id;
            
            Cache::put('upload_token_' . $token, [
                'created_at' => $tokenData['created_at'],
                'files' => $files
            ], now()->addMinutes(120));
            
            Log::info('Token updated with new file', [
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
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'The file does not exist.'], 400);
        } catch (FileIsTooBig $e) {
            Log::error('File is too big: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'The file is too large. Maximum size is 20MB.'], 400);
        } catch (\Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
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
