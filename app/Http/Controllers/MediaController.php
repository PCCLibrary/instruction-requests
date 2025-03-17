<?php

namespace App\Http\Controllers;

use App\Models\InstructionRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    /**
     * Maximum number of files allowed per upload session
     */
    const MAX_FILES_PER_SESSION = 4;

    /**
     * Token expiration time in minutes
     */
    const TOKEN_EXPIRATION = 120;

    /**
     * Generate a temporary upload token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateToken()
    {
        $token = Str::random(40);
        
        Cache::put('upload_token_' . $token, [
            'created_at' => now(),
            'files' => [],
            'count' => 0
        ], now()->addMinutes(self::TOKEN_EXPIRATION));
        
        return response()->json([
            'token' => $token,
            'expires_in' => self::TOKEN_EXPIRATION * 60, // seconds
            'max_files' => self::MAX_FILES_PER_SESSION
        ]);
    }

    /**
     * Handle file upload from public form (unauthenticated)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicUpload(Request $request)
    {
        // Validate token
        $token = $request->header('X-Upload-Token');
        if (!$token) {
            return response()->json(['error' => 'Upload token is required'], 401);
        }

        $tokenData = $this->validateToken($token);
        if (!$tokenData) {
            return response()->json(['error' => 'Invalid or expired upload token'], 401);
        }

        // Check if maximum files reached
        if ($tokenData['count'] >= self::MAX_FILES_PER_SESSION) {
            return response()->json(['error' => 'Maximum number of files reached'], 400);
        }

        // Validate file
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,txt,rtf|max:20480'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first('file')], 400);
        }

        try {
            // Create a temporary model to attach the file to
            $tempModel = new InstructionRequests();
            $tempModel->id = 0; // This will be used by the path generator to identify temporary files
            
            // Add the file to the media library
            $media = $tempModel->addMediaFromRequest('file')
                ->withCustomProperties([
                    'upload_source' => 'public',
                    'purpose' => 'request materials', 
                    'temporary' => true
                ])
                ->toMediaCollection('materials');

            // Update token data
            $tokenData['files'][] = $media->id;
            $tokenData['count']++;
            Cache::put('upload_token_' . $token, $tokenData, now()->addMinutes(self::TOKEN_EXPIRATION));

            // Return response with file details
            return response()->json([
                'id' => $media->id,
                'name' => $media->file_name,
                'size' => $media->size,
                'type' => $media->mime_type,
                'icon' => $this->getFileIcon($media->file_name),
                'url' => $media->getUrl()
            ]);

        } catch (FileDoesNotExist $e) {
            Log::error('File upload failed: File does not exist', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'The file does not exist'], 400);
        } catch (FileIsTooBig $e) {
            Log::error('File upload failed: File is too big', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'The file is too big (max 20MB)'], 400);
        } catch (\Exception $e) {
            Log::error('File upload failed: Unknown error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to upload file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle file upload from admin dashboard (authenticated)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        // Validate user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate file
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,txt,rtf|max:20480',
            'request_id' => 'required|exists:instruction_requests,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first('file') ?? $validator->errors()->first('request_id')
            ], 400);
        }

        try {
            // Find the instruction request
            $instructionRequest = InstructionRequests::findOrFail($request->request_id);
            
            // Add the file to the media library
            $media = $instructionRequest->addMediaFromRequest('file')
                ->withCustomProperties([
                    'upload_source' => 'admin',
                    'uploaded_by' => Auth::user()->display_name,
                    'purpose' => 'instruction materials'
                ])
                ->toMediaCollection('materials');

            // Return response with file details
            return response()->json([
                'id' => $media->id,
                'name' => $media->file_name,
                'size' => $media->size,
                'type' => $media->mime_type,
                'icon' => $this->getFileIcon($media->file_name, true), // true for admin (Heroicons)
                'url' => $media->getUrl()
            ]);

        } catch (FileDoesNotExist $e) {
            Log::error('Admin file upload failed: File does not exist', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'The file does not exist'], 400);
        } catch (FileIsTooBig $e) {
            Log::error('Admin file upload failed: File is too big', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'The file is too big (max 20MB)'], 400);
        } catch (\Exception $e) {
            Log::error('Admin file upload failed: Unknown error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to upload file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a file
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $id)
    {
        // For public form, require token
        if (!Auth::check()) {
            $token = $request->header('X-Upload-Token');
            if (!$token) {
                return response()->json(['error' => 'Upload token is required'], 401);
            }

            $tokenData = $this->validateToken($token);
            if (!$tokenData) {
                return response()->json(['error' => 'Invalid or expired upload token'], 401);
            }

            // Check if file is in token
            if (!in_array($id, $tokenData['files'])) {
                return response()->json(['error' => 'File not found in your upload session'], 404);
            }
        }

        try {
            // Find and delete the media
            $media = Media::findOrFail($id);
            
            // For admin, check permissions - either it's a temp file or they can access the model
            if (Auth::check() && !$media->getCustomProperty('temporary', false)) {
                $model = $media->model;
                if (!$model || !Auth::user()->can('update', $model)) {
                    return response()->json(['error' => 'You do not have permission to delete this file'], 403);
                }
            }

            $fileName = $media->file_name;
            $media->delete();

            // Update token data if using token
            if (!Auth::check() && isset($tokenData)) {
                $tokenData['files'] = array_values(array_diff($tokenData['files'], [$id]));
                $tokenData['count']--;
                Cache::put('upload_token_' . $token, $tokenData, now()->addMinutes(self::TOKEN_EXPIRATION));
            }

            return response()->json([
                'message' => "File {$fileName} deleted successfully"
            ]);

        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to delete file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Associate temporary files with a request
     *
     * @param string $token
     * @param int $requestId
     * @return bool
     */
    public function associateFiles($token, $requestId)
    {
        $tokenData = $this->validateToken($token);
        if (!$tokenData || empty($tokenData['files'])) {
            return false;
        }

        try {
            $request = InstructionRequests::findOrFail($requestId);
            
            foreach ($tokenData['files'] as $fileId) {
                $media = Media::find($fileId);
                
                if ($media && $media->getCustomProperty('temporary', false)) {
                    // Update the media model
                    $media->model_id = $requestId;
                    $media->model_type = InstructionRequests::class;
                    $media->setCustomProperty('temporary', false);
                    $media->save();
                    
                    Log::info("File associated with request", [
                        'file_id' => $fileId,
                        'request_id' => $requestId
                    ]);
                }
            }
            
            // Clear the token
            Cache::forget('upload_token_' . $token);
            
            return true;
        } catch (\Exception $e) {
            Log::error('File association failed', [
                'token' => $token,
                'request_id' => $requestId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get appropriate icon for a file based on its extension
     *
     * @param string $filename
     * @param bool $isAdmin
     * @return string
     */
    private function getFileIcon($filename, $isAdmin = false)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if ($isAdmin) {
            // Heroicons for admin
            $iconMap = [
                'pdf' => 'document-text',
                'doc' => 'document',
                'docx' => 'document',
                'ppt' => 'presentation-chart',
                'pptx' => 'presentation-chart',
                'txt' => 'document-text',
                'rtf' => 'document-text'
            ];
            
            return $iconMap[$extension] ?? 'document';
        } else {
            // FontAwesome for public
            $iconMap = [
                'pdf' => 'fa-file-pdf-o',
                'doc' => 'fa-file-word-o',
                'docx' => 'fa-file-word-o',
                'ppt' => 'fa-file-powerpoint-o',
                'pptx' => 'fa-file-powerpoint-o',
                'txt' => 'fa-file-text-o',
                'rtf' => 'fa-file-text-o'
            ];
            
            return $iconMap[$extension] ?? 'fa-file-o';
        }
    }

    /**
     * Validate upload token
     *
     * @param string $token
     * @return array|null
     */
    private function validateToken($token)
    {
        $tokenData = Cache::get('upload_token_' . $token);
        
        if (!$tokenData) {
            return null;
        }
        
        if (now()->diffInMinutes($tokenData['created_at']) > self::TOKEN_EXPIRATION) {
            Cache::forget('upload_token_' . $token);
            return null;
        }
        
        return $tokenData;
    }
}
