<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AppBaseController extends Controller
{
    /**
     * Create a success response with data and message.
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    protected function sendResponse(mixed $data, string $message = 'Operation successful'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ]);
    }

    /**
     * Create an error response with message and optional code.
     *
     * @param string $message
     * @param int $code
     * @param array|null $errors
     * @return JsonResponse
     */
    protected function sendError(string $message, int $code = 400, array $errors = null): JsonResponse
    {
        // Log the error (optional for debugging AJAX requests).
        Log::error('Error Response: ' . $message, [
            'code' => $code,
            'errors' => $errors
        ]);

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,  // Optional debugging information
        ], $code);
    }

    /**
     * Create a success message response with no additional data.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function sendSuccess(string $message): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
}
