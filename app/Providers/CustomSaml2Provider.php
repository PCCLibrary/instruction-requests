<?php

namespace App\Providers;

use SocialiteProviders\Saml2\Provider as Saml2Provider;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Two\User;

/**
 * Custom SAML2 Provider extending SocialiteProviders\Saml2\Provider
 *
 * This class addresses compatibility issues between LightSAML's RelayState extraction
 * and Microsoft Entra ID's SAML response format. It overrides the hasInvalidState()
 * method to use raw RelayState from the request input instead of relying on
 * LightSAML's messageContext parsing.
 */
class CustomSaml2Provider extends Saml2Provider
{
    /**
     * Determine if the current request has a CSRF token mismatch.
     *
     * This method overrides the parent implementation to work around
     * LightSAML's RelayState parsing issue with Microsoft Entra ID.
     * Instead of using $this->messageContext->getMessage()->getRelayState(),
     * we use the raw RelayState directly from the request input.
     *
     * @return bool
     */
    protected function hasInvalidState(): bool
    {
        $sessionState = $this->request->session()->pull('state');
        $requestRelayState = $this->request->input('RelayState');

        Log::info('CustomSaml2Provider State Validation:', [
            'session_state' => $sessionState,
            'request_relay_state' => $requestRelayState,
            'is_match' => ($sessionState === $requestRelayState),
            'session_state_length' => strlen((string) $sessionState),
            'request_relay_state_length' => strlen((string) $requestRelayState),
        ]);

        // Validate that both state values exist and match
        // This bypasses the LightSAML messageContext parsing issue
        return !(strlen((string) $sessionState) > 0 && $sessionState === $requestRelayState);
    }

    /**
     * Override the user method to add enhanced logging for debugging
     * and to ensure proper user object creation after state validation.
     *
     * @return \Laravel\Socialite\Two\User
     */
    public function user()
    {
        try {
            Log::info('CustomSaml2Provider: Starting user() method');

            // ===== RAW SAML RESPONSE LOGGING =====
            $this->logRawSamlResponseData();

            // ===== DECODED SAML XML LOGGING =====
            $this->logDecodedSamlXml();

            // Log available request data before calling parent
            Log::info('CustomSaml2Provider: Request data before parent call:', [
                'relay_state' => $this->request->input('RelayState'),
                'saml_response' => substr($this->request->input('SAMLResponse', 'MISSING'), 0, 100) . '...',
                'request_method' => $this->request->method(),
                'session_id' => session()->getId(),
            ]);

            // Call parent user() method which will use our overridden hasInvalidState()
            $user = parent::user();

            Log::info('CustomSaml2Provider User Object Created:', [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('CustomSaml2Provider User Creation Failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Override mapUserToObject to ensure email is correctly extracted
     * if the default attribute mapping fails with Entra ID.
     *
     * @param array $user
     * @return \Laravel\Socialite\Two\User
     */
    protected function mapUserToObject(array $user)
    {
        $socialiteUser = parent::mapUserToObject($user);

        // Enhanced email extraction for Microsoft Entra ID
        if (empty($socialiteUser->getEmail()) && isset($user['attributes'])) {
            $email = $this->extractEmailFromAttributes($user['attributes']);
            if ($email) {
                $socialiteUser->setEmail($email);
                Log::info('CustomSaml2Provider: Email manually mapped from attributes.', [
                    'email' => $email
                ]);
            }
        }

        // Enhanced name extraction for Microsoft Entra ID
        if (empty($socialiteUser->getName()) && isset($user['attributes'])) {
            $name = $this->extractNameFromAttributes($user['attributes']);
            if ($name) {
                $socialiteUser->setName($name);
                Log::info('CustomSaml2Provider: Name manually mapped from attributes.', [
                    'name' => $name
                ]);
            }
        }

        return $socialiteUser;
    }

    /**
     * Extract email from SAML attributes with multiple fallback options
     * for Microsoft Entra ID compatibility.
     *
     * @param array $attributes
     * @return string|null
     */
    private function extractEmailFromAttributes(array $attributes): ?string
    {
        // Common Entra ID email claim names
        $emailClaims = [
            'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress',
            'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name',
            'mail',
            'email',
            'emailAddress',
            'userPrincipalName'
        ];

        foreach ($emailClaims as $claim) {
            if (isset($attributes[$claim])) {
                $value = is_array($attributes[$claim]) ? $attributes[$claim][0] : $attributes[$claim];
                if (!empty($value) && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return $value;
                }
            }
        }

        return null;
    }

    /**
     * Extract name from SAML attributes with multiple fallback options
     * for Microsoft Entra ID compatibility.
     *
     * @param array $attributes
     * @return string|null
     */
    private function extractNameFromAttributes(array $attributes): ?string
    {
        // Common Entra ID name claim names
        $nameClaims = [
            'http://schemas.microsoft.com/ws/2008/06/identity/claims/displayname',
            'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name',
            'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname',
            'displayName',
            'cn',
            'name',
            'givenName',
            'firstName'
        ];

        foreach ($nameClaims as $claim) {
            if (isset($attributes[$claim])) {
                $value = is_array($attributes[$claim]) ? $attributes[$claim][0] : $attributes[$claim];
                if (!empty($value)) {
                    return $value;
                }
            }
        }

        return null;
    }

    /**
     * Log raw SAML response data for debugging comparison between environments
     */
    private function logRawSamlResponseData(): void
    {
        Log::info('=== RAW SAML RESPONSE DATA ===', [
            'environment' => config('app.env'),
            'timestamp' => now()->toISOString(),
            'relay_state' => $this->request->input('RelayState'),
            'saml_response_b64' => $this->request->input('SAMLResponse'),
            'all_post_data' => $this->request->all(),
            'request_method' => $this->request->method(),
            'content_type' => $this->request->header('Content-Type'),
            'user_agent' => $this->request->userAgent(),
            'request_url' => $this->request->fullUrl(),
        ]);
    }

    /**
     * Log decoded SAML XML structure for debugging comparison between environments
     */
    private function logDecodedSamlXml(): void
    {
        $samlResponseB64 = $this->request->input('SAMLResponse');

        if (!$samlResponseB64) {
            Log::warning('=== DECODED SAML XML === No SAMLResponse found in request');
            return;
        }

        try {
            $samlXML = base64_decode($samlResponseB64);

            if ($samlXML === false) {
                Log::error('=== DECODED SAML XML === Failed to base64 decode SAMLResponse');
                return;
            }

            // Clean sensitive data but preserve structure for comparison
            $cleanedXML = $this->sanitizeSamlXml($samlXML);

            Log::info('=== DECODED SAML XML STRUCTURE ===', [
                'environment' => config('app.env'),
                'timestamp' => now()->toISOString(),
                'xml_structure' => $cleanedXML,
                'xml_length' => strlen($samlXML),
                'contains_assertion' => strpos($samlXML, '<saml:Assertion') !== false,
                'contains_attributes' => strpos($samlXML, '<saml:AttributeStatement') !== false,
                'contains_nameid' => strpos($samlXML, '<saml:NameID') !== false,
            ]);

        } catch (\Exception $e) {
            Log::error('=== DECODED SAML XML === Exception during decode:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Sanitize SAML XML by removing sensitive data while preserving structure
     */
    private function sanitizeSamlXml(string $samlXML): string
    {
        // Remove sensitive content but keep structure
        $patterns = [
            // Replace NameID values but keep the element structure
            '/(<saml:NameID[^>]*>)[^<]*(<\/saml:NameID>)/' => '$1[NAMEID_REDACTED]$2',
            '/(<saml2:NameID[^>]*>)[^<]*(<\/saml2:NameID>)/' => '$1[NAMEID_REDACTED]$2',

            // Replace attribute values but keep attribute names and structure
            '/(<saml:AttributeValue[^>]*>)[^<]*(<\/saml:AttributeValue>)/' => '$1[ATTR_VALUE_REDACTED]$2',
            '/(<saml2:AttributeValue[^>]*>)[^<]*(<\/saml2:AttributeValue>)/' => '$1[ATTR_VALUE_REDACTED]$2',

            // Replace signature values but keep signature structure
            '/(<ds:SignatureValue[^>]*>)[^<]*(<\/ds:SignatureValue>)/' => '$1[SIGNATURE_REDACTED]$2',
            '/(<SignatureValue[^>]*>)[^<]*(<\/SignatureValue>)/' => '$1[SIGNATURE_REDACTED]$2',

            // Replace certificate values but keep certificate structure
            '/(<ds:X509Certificate[^>]*>)[^<]*(<\/ds:X509Certificate>)/' => '$1[CERT_REDACTED]$2',
            '/(<X509Certificate[^>]*>)[^<]*(<\/X509Certificate>)/' => '$1[CERT_REDACTED]$2',
        ];

        $cleanedXML = $samlXML;
        foreach ($patterns as $pattern => $replacement) {
            $cleanedXML = preg_replace($pattern, $replacement, $cleanedXML);
        }

        return $cleanedXML;
    }
}
