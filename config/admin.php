<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Account Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for demo / initial local administration setup.
    |
    */

    'demo' => [
        'name' => env('ADMIN_NAME', 'Demo Admin'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],
];
