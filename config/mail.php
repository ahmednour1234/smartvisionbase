<?php

return [

    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [
        'smtp' => [
            'transport'    => 'smtp',

            // مهم: لا تستخدم MAIL_URL إطلاقًا (لو موجود بيتجاهل host/port/encryption)
            'url'          => null,

            'host'         => env('MAIL_HOST', 'smtp.hostinger.com'),
            'port'         => env('MAIL_PORT', 465),
            'encryption'   => env('MAIL_ENCRYPTION', 'ssl'), // 465=ssl ، 587=tls
            'username'     => env('MAIL_USERNAME'),
            'password'     => env('MAIL_PASSWORD'),
            'timeout'      => env('MAIL_TIMEOUT', 30),
            'local_domain' => env('MAIL_EHLO_DOMAIN', null),

            // اختياري: فك التعليق فقط لو عندك مشكلة شهادة (للاختبار، ليس للإنتاج)
            // 'stream' => [
            //     'ssl' => [
            //         'allow_self_signed' => env('MAIL_SSL_ALLOW_SELF_SIGNED', false),
            //         'verify_peer'       => env('MAIL_SSL_VERIFY_PEER', true),
            //         'verify_peer_name'  => env('MAIL_SSL_VERIFY_PEER_NAME', true),
            //     ],
            // ],
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers'   => ['smtp', 'log'],
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
        // لازم From يساوي Username في أغلب السيرفرات
        'address' => env('MAIL_FROM_ADDRESS', 'info@forextraderssummit.com'),
        'name'    => env('MAIL_FROM_NAME', 'Top Trusted'),
    ],

    'markdown' => [
        'theme' => 'default',
        'paths' => [resource_path('views/vendor/mail')],
    ],
];
