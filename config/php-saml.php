<?php

return [

    'strict' => true,
    'debug' => env('APP_DEBUG', false),

    // Service Provider Configuration
    'sp' => [
        'entityId' => env('APP_URL', 'https://wwwtest.pcc.edu/library/instruction-requests/'),
        'assertionConsumerService' => [
            'url' => env('APP_URL') . '/saml2/acs',
        ],
        'singleLogoutService' => [
            'url' => env('APP_URL') . '/saml2/sls',
        ],
        'x509cert' => env('SAML2_SP_CERT_x509', ''),
        'privateKey' => env('SAML2_SP_CERT_PRIVATEKEY', ''),
    ],

    // Identity Provider Settings (IdP)
    'idp' => [
        'entityId' => env('SAML2_IDP_ENTITY_ID', ''),
        'singleSignOnService' => [
            'url' => env('SAML2_IDP_SSO_URL', ''),
        ],
        'singleLogoutService' => [
            'url' => env('SAML2_IDP_SLO_URL', ''),
        ],
        'x509cert' => env('SAML2_IDP_X509CERT', ''),
    ],

    'security' => [
        'authnRequestsSigned' => false,
        'logoutRequestSigned' => false,
        'logoutResponseSigned' => false,
        'wantMessagesSigned' => false,
        'wantAssertionsSigned' => false,
        'wantNameIdEncrypted' => false,
    ],

    // Contact and Organization Info (optional)
    'contactPerson' => [
        'technical' => [
            'givenName' => 'Support',
            'emailAddress' => env('SAML2_CONTACT_TECHNICAL_EMAIL', 'no-reply@pcc.edu'),
        ],
    ],

    'organization' => [
        'en-US' => [
            'name' => env('SAML2_ORGANIZATION_NAME', 'PCC'),
            'displayname' => env('SAML2_ORGANIZATION_DISPLAYNAME', 'PCC Library'),
            'url' => env('SAML2_ORGANIZATION_URL', 'https://wwwtest.pcc.edu'),
        ],
    ],
];
