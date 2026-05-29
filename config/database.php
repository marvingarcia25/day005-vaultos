<?php

return [
    'default' => env('DB_CONNECTION', 'sqlsrv'),

    'connections' => [
        'sqlsrv' => [
            'driver'                   => 'sqlsrv',
            'url'                      => env('DATABASE_URL'),
            'host'                     => env('DB_HOST', 'db'),
            'port'                     => env('DB_PORT', '1433'),
            'database'                 => env('DB_DATABASE', 'vaultos'),
            'username'                 => env('DB_USERNAME', 'sa'),
            'password'                 => env('DB_PASSWORD', ''),
            'charset'                  => 'utf8',
            'prefix'                   => '',
            'prefix_indexes'           => true,
            'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', false),
        ],
    ],

    'migrations' => [
        'table'                => 'migrations',
        'update_date_on_publish' => true,
    ],

    'redis' => [
        'client'  => env('REDIS_CLIENT', 'phpredis'),
        'default' => [
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port'     => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
    ],
];
