<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    // কমা দিয়ে একাধিক origin: FRONTEND_URL=http://localhost:5173,https://example.org
    'allowed_origins' => array_filter(array_map('trim', explode(',', env('FRONTEND_URL', 'http://localhost:5173')))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
