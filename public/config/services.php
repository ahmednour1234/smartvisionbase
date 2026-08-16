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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
'whatsapp' => [
    'version'     => env('WA_API_VERSION', 'v22.0'),
    'phone_id'    => env('WA_PHONE_NUMBER_ID'),
    'token'       => env('WA_TOKEN'),
],
'meta' => [
    'pixel_id'   => env('META_PIXEL_ID'),
    'capi_token' => env('META_CAPI_TOKEN'),
],

    'wati' => [
            'base_url'        => env('WATI_API_ENDPOINT', 'https://live-server.wati.io'),
    'token'       => env('WATI_API_TOKEN'),
    'channel_number'  => env('WATI_CHANNEL_NUMBER'),
    'template_name'   => env('WATI_TEMPLATE'),

        'default_broadcast' => env('WATI_DEFAULT_BROADCAST', 'Default Campaign'),
        
        // Defaults for template placeholders
        'event_name'     => env('WATI_EVENT_NAME', 'Dubai Event'),
        'event_date'     => env('WATI_EVENT_DATE', 'Nov 5–6, 2025'),
        'event_time'     => env('WATI_EVENT_TIME', '10:00–18:00'),
        'event_location' => env('WATI_EVENT_LOCATION', 'Ritz-Carlton, DIFC'),
    ],
];
