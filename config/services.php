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
| connect with PCC's authentication service. All sensitive values are
| stored in environment variables.
|
*/
    'saml2' => [
        // Identity Provider (IdP) settings - direct configuration
        'entityid' => env('SAML2_IDP_ENTITY_ID', 'authenticate.pcc.edu'),
        'acs' => env('SAML2_IDP_SSO_URL', 'https://authenticate.pcc.edu/samlsso'),
        'certificate' => env('SAML2_IDP_X509CERT'),

        // Service Provider (SP) settings
        'sp_entityid' => env('SAML2_SP_ENTITY_ID', 'https://wwwtest.pcc.edu/library/instruction-requests/public/saml2/metadata'),
        'sp_acs' => 'saml2/acs',

        // Default binding method
        'sp_default_binding_method' => \LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST,

        // Security settings
        'sp_security_messages_signed' => env('SAML2_SECURITY_MESSAGES_SIGNED', false),
        'sp_security_assertions_signed' => env('SAML2_SECURITY_ASSERTIONS_SIGNED', false),

        // NameID format - change to support various formats, not just email
        'nameid_format' => \LightSaml\SamlConstants::NAME_ID_FORMAT_PERSISTENT,

        // Custom attribute mapping
        'attribute_map' => [
            'email' => ['mail', 'email', 'emailAddress'],
            'name' => ['displayName', 'cn', 'name', 'uid']
        ],
    ]
];
