<?php

declare(strict_types=1);

use \Hyperf\Support;

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    'default' => [
        'host' => Support\env('REDIS_HOST', 'redis'),
        'auth' => Support\env('REDIS_AUTH', null),
        'port' => (int) Support\env('REDIS_PORT', 6379),
        'db' => (int) Support\env('REDIS_DB', 0),
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 3000,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => (float) Support\env('REDIS_MAX_IDLE_TIME', 60),
        ],
    ],
];
