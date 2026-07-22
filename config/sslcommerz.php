<?php

return [
    'apiCredentials' => [
        'store_id' => env('SSLCOMMERZ_STORE_ID', 'testbox'),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD', 'qwerty'),
    ],
    'sandbox' => env('SSLCOMMERZ_SANDBOX', true),
];
