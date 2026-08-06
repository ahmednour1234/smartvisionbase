<?php

return [
    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.hostinger.com'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', null), // اختياري
        ],
        'log'   => ['transport' => 'log'],
        'array' => ['transport' => 'array'],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'info@forextraderssummit.com'),
        'name' => env('MAIL_FROM_NAME', 'Forex Traders Summit'),
    ],
];
