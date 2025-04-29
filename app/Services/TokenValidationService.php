<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Service for validating stateless CSRF tokens.
 *
 * This service is used to validate tokens that don't depend on the user's session,
 * allowing for secure cross-application form submissions from Svelte to Laravel.
 */
class TokenValidationService
{
    /**
     * Validate a stateless form submission token.
     *
     * @param string|null $token
     * @return bool
     */
    public function validateToken(?string $token): bool
    {
        // If no token provided, validation fails
        if (!$token || !is_string($token)) {
            Log::warning('Token validation failed: No token provided or token is not a string');
            return false;
        }

        // Split the token into its components
        $parts = explode('|', $token);
        if (count($parts) !== 3) {
            Log::warning('Token validation failed: Invalid token format', [
                'token_parts_count' => count($parts)
            ]);
            return false;
        }

        [$timestamp, $random, $signature] = $parts;

        // Check if token has expired
        if ((int)$timestamp < now()->timestamp) {
            Log::warning('Token validation failed: Token expired', [
                'timestamp' => date('Y-m-d H:i:s', (int)$timestamp),
                'current_time' => now()->toDateTimeString()
            ]);
            return false;
        }

        // Recreate the data string that was signed
        $data = $timestamp . '|' . $random;

        // Calculate the expected signature
        $expectedSignature = hash_hmac('sha256', $data, config('app.key'));

        // Compare signatures using a time-constant comparison
        $isValid = hash_equals($expectedSignature, $signature);

        if (!$isValid) {
            Log::warning('Token validation failed: Invalid token signature');
        } else {
            Log::info('Token validation successful', [
                'valid_until' => date('Y-m-d H:i:s', (int)$timestamp)
            ]);
        }

        return $isValid;
    }
}
