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
}
