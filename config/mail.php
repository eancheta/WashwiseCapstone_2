<?php

return [

'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp-relay.brevo.com'),
            'port' => env('MAIL_PORT', 587),
            'username' => env('MAIL_USERNAME', '92b03a001@smtp-brevo.com'),
            'password' => env('MAIL_PASSWORD', 'xjbS3I2mfcLVrXTP'),
            'timeout' => null,
            'auth_mode' => null,
            'stream' => [
                'ssl' => [
                    'allow_self_signed' => false,
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                    'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                ],
            ],
        ],

'brevo' => [
    'transport' => 'sendinblue', // transport must be 'sendinblue'
    'dsn' => env('MAILER_DSN', ''),
],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL', 'stack'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],

    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

];
