<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configure allowed origins and headers for API/browser clients.
    | Keep this restrictive in production.
    |
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['*'],

    // Comma-separated list recommended, e.g. "https://yourdomain.com,https://app.yourdomain.com"
    'allowed_origins' => array_filter(array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', '')))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Set true only if you need cookies/authorization headers from browsers.
    'supports_credentials' => (bool) env('CORS_SUPPORTS_CREDENTIALS', false),

];
