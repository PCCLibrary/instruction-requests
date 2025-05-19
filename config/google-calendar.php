<?php

return [

    'default_auth_profile' => env('GOOGLE_CALENDAR_AUTH_PROFILE', 'service_account'),

    'auth_profiles' => [

        /*
         * Authenticate using a service account.
         */
        'service_account' => [
            /*
             * Path to the json file containing the credentials.
             */
            'credentials_json' => base_path(env('GOOGLE_CALENDAR_SERVICE_ACCOUNT_JSON_LOCATION')),
        ],

        /*
         * Authenticate with actual google user account.
         */
        'oauth' => [
            /*
             * Path to the json file containing the oauth2 credentials.
             */
            'credentials_json' => base_path('app/google-calendar/pcc-library-website-baf73fcc9190.json'),

            /*
             * Path to the json file containing the oauth2 token.
             */
            'token_json' => storage_path('app/google-calendar/oauth-token.json'),
        ],
    ],

    /*
     * The id of the Google Calendar that will be used by default.
     */
    'calendar_id' => env('GOOGLE_CALENDAR_ID', 'c_91e4e2a58503a3f41186894f62e70d8f904be3e72af56f6059fb4f0220b7dbc4@group.calendar.google.com'),

    /*
    * The email address of the user account to impersonate.
    */
    'user_to_impersonate' => env('GOOGLE_CALENDAR_IMPERSONATE_EMAIL'),
];
