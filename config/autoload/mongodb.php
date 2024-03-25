<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use App\DB\Driver\MongoDbConnectionPool;

return [
    'default' => [
        'driver' => MongoDbConnectionPool::class,
        'host' => env('MONGODB_HOST'),
        'database' => env('MONGODB_DATABASE'),
        'port' => env('MONGODB_PORT', 27017),
        'username' => env('MONGODB_USERNAME'),
        'password' => env('MONGODB_PASSWORD'),
        'ssl:ca:ccerts' => env('MONGODB_SSL_CA_CERTS'),
        'pool' => [
            'min_connections' => 300,
            'max_connections' => 2000,
            'connect_timeout' => 10.0,
            'wait_timeout' => 5.0,
            'heartbeat' => -1,
            'max_idle_time' => 60,
        ],
        'commands' => [
            'gen:model' => [
                'path' => 'app/Model',
                'force_casts' => true,
                'inheritance' => 'Model',
            ],
        ],
    ],
];
