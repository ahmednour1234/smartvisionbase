<?php

return [

    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [
        'smtp' => [
            'transport'    => 'smtp',
            'url'          => env('MAIL_URL'),
            'host'         => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port'         => env('MAIL_PORT', 587),
            'encryption'   => env('MAIL_ENCRYPTION', 'tls'),
            'username'     => env('MAIL_USERNAME'),
            'password'     => env('MAIL_PASSWORD'),
            'timeout'      => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],

        // Mailer العام (Gmail في حالتك)
        'public' => [
            'transport'    => env('PUBLIC_MAIL_MAILER', 'smtp'),
            'host'         => env('PUBLIC_MAIL_HOST'),
            'port'         => env('PUBLIC_MAIL_PORT', 465),
            'encryption'   => env('PUBLIC_MAIL_ENCRYPTION', 'ssl'),
            'username'     => env('PUBLIC_MAIL_USERNAME'),
            'password'     => env('PUBLIC_MAIL_PASSWORD'),
            'timeout'      => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],

        // استخدمه فقط لو عايز تتأكد إن الرسالة ما “تضيعش” (يسقط لـ log)
        'failover' => [
            'transport' => 'failover',
            'mailers'   => ['public', 'smtp', 'log'],
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path'      => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel'   => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name'    => env('MAIL_FROM_NAME', 'Example'),
    ],

    'markdown' => [
        'theme' => 'default',
        'paths' => [resource_path('views/vendor/mail')],
    ],
];
