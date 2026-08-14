<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ZKTeco ADMS Device Network & Response Configuration
    |--------------------------------------------------------------------------
    */

    'server_ip' => env('ADMS_SERVER_IP', '0.0.0.0'),
    'server_port' => env('ADMS_SERVER_PORT', 8000),

    'heartbeat_timeout' => env('ADMS_HEARTBEAT_TIMEOUT', 300), // seconds (5 minutes)

    'response' => [
        'error_delay' => env('ADMS_RESPONSE_ERROR_DELAY', 60),
        'delay' => env('ADMS_RESPONSE_DELAY', 30),
        'realtime' => env('ADMS_RESPONSE_REALTIME', 1),
        'trans_times' => env('ADMS_RESPONSE_TRANS_TIMES', '00:00;14:00'),
        'trans_interval' => env('ADMS_RESPONSE_TRANS_INTERVAL', 1),
        'trans_flag' => env('ADMS_RESPONSE_TRANS_FLAG', '1111000000'),
        'time_zone' => env('ADMS_RESPONSE_TIME_ZONE', 'UTC'),
    ],
];
