<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class CsrfTokenController extends Controller
{
    /**
     * Return a stateless, signature-based token for form submission validation.
     *
     * @return JsonResponse
     */
    public function getToken(): JsonResponse
    {
        try {
            // Generate a token valid for 24 hours
            $timestamp = now()->addHours(24)->timestamp;

            // Add a random component for additional security
            $random = bin2hex(random_bytes(8));

            // Data to sign
            $data = $timestamp . '|' . $random;

            // Create a signature using the app key
            $signature = hash_hmac('sha256', $data, config('app.key'));

            // Combine into final token
            $token = $timestamp . '|' . $random . '|' . $signature;

            return Response::json(['csrf_token' => $token], 200);
        } catch (\Exception $e) {
            Log::error('Error generating form token', [
                'error' => $e->getMessage()
            ]);

            return Response::json([
                'message' => 'Failed to generate form token'
            ], 500);
        }
    }
}
