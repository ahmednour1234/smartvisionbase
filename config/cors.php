<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'auth/*'],

    'allowed_methods' => ['*'],

    // For SPA cookie auth you must set FRONTEND_ORIGIN and enable credentials.
    'allowed_origins' => [env('FRONTEND_ORIGIN', 'http://localhost:3000')],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    // Expose Authorization for token-mode; cookie-mode does not need it.
    'exposed_headers' => ['Authorization'],

    'max_age' => 0,

    // IMPORTANT for Sanctum SPA (cookies)
    'supports_credentials' => filter_var(env('CORS_SUPPORTS_CREDENTIALS', true), FILTER_VALIDATE_BOOL),
];
