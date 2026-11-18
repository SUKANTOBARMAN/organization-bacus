<?php

return [
    // প্রথম সুপার অ্যাডমিন (php artisan db:seed চালালে তৈরি হবে)
    'admin_name' => env('ADMIN_NAME', 'Super Admin'),
    'admin_email' => env('ADMIN_EMAIL', 'admin@example.com'),
    'admin_password' => env('ADMIN_PASSWORD', 'ChangeMe123!'),
];
