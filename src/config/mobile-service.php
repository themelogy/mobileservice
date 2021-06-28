<?php

return [
    'username'       => env('MOBILE_SERVICE_USERNAME', 'username'),
    'password'       => env('MOBILE_SERVICE_PASSWORD', 'password'),
    'vendor_code'    => env('MOBILE_SERVICE_VENDOR_CODE', 'vendor_code'),
    'authentication' => env('MOBILE_SERVICE_AUTHENTICATION', 'api'),
    'provider'       => env('MOBILE_SERVICE_GATEWAY', 'MobilDev'),
    'expire'         => 180,
    'date_format'    => 'dmYHi',
    'encoding'       => 0,
    'originator'     => '',
    'blacklist'      => 0,
    'code-length'    => 8,
    'cache-prefix'   => 'sms-code',
    'code-lifetime'  => 3
];
