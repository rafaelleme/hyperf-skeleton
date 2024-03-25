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
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LogLevel;
use Hyperf\Support;

return [
    'app_name' => Support\env('APP_NAME', 'hyperf-skeleton'),
    'app_env' => Support\env('APP_ENV', 'dev'),
    'scan_cacheable' => Support\env('SCAN_CACHEABLE', false),
    StdoutLoggerInterface::class => [
        'log_level' => [
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::DEBUG,
            LogLevel::EMERGENCY,
            LogLevel::ERROR,
            LogLevel::INFO,
            LogLevel::NOTICE,
            LogLevel::WARNING,
        ],
    ],
    'integrations' => [
        'authorizer_url' => Support\env('AUTHORIZER_URL'),
        'notification_url' => Support\env('NOTIFICATION_URL'),
    ]
];
