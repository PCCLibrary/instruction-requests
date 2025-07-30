<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
|--------------------------------------------------------------------------
| SAML2 Authentication Configuration
|--------------------------------------------------------------------------
|
| This configuration is used by the SocialiteProviders SAML2 driver to
| connect with Microsoft Entra ID authentication service. All sensitive
| values are stored in environment variables.
|
*/
    'saml2' => [
        // Identity Provider (IdP) settings - Entra ID configuration
        'entityid' => env('SAML2_IDP_ENTITY_ID'),
        'acs' => env('SAML2_IDP_SSO_URL'),
        'certificate' => env('SAML2_IDP_X509CERT'),

        // Service Provider (SP) settings
        'sp_entityid' => env('SAML2_SP_ENTITY_ID'),
        'sp_acs' => 'saml2/acs',

        // Default binding method
        'sp_default_binding_method' => \LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST,

        // Security settings for Entra ID
        'sp_security_messages_signed' => env('SAML2_SECURITY_MESSAGES_SIGNED', false),
        'sp_security_assertions_signed' => env('SAML2_SECURITY_ASSERTIONS_SIGNED', true),
        'want_message_signed' => env('SAML2_WANT_MESSAGE_SIGNED', true),
        'want_assertion_signed' => env('SAML2_WANT_ASSERTION_SIGNED', true),
        'want_assertion_encrypted' => env('SAML2_WANT_ASSERTION_ENCRYPTED', false),

        // NameID format for Entra ID
        'nameid_format' => \LightSaml\SamlConstants::NAME_ID_FORMAT_PERSISTENT,

        // Entra ID specific settings
        'strict' => env('SAML2_STRICT', true),
        'wanna_match_url_profile' => env('SAML2_WANNA_MATCH_URL_PROFILE', true),
        'relay_state_required' => env('SAML2_RELAY_STATE_REQUIRED', true),

        // Clock skew tolerance for server time differences
        'validation' => [
            'clock_skew' => env('SAML2_CLOCK_SKEW', 120), // 2 minutes tolerance
        ],

        // Enhanced attribute mapping for Microsoft Entra ID
        'attribute_map' => [
            // Multiple fallback options for email extraction
            'email' => [
                'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress',
                'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name',
                'mail',
                'email',
                'emailAddress',
                'userPrincipalName'
            ],
            // Multiple fallback options for name extraction
            'name' => [
                'http://schemas.microsoft.com/ws/2008/06/identity/claims/displayname',
                'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name',
                'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname',
                'displayName',
                'cn',
                'name',
                'givenName',
                'firstName'
            ]
        ],
    ]
];
