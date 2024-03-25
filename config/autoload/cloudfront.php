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
return [
    'default' => [
        'cloudfront_domain_signed' => env('CLOUDFRONT_DOMAIN_SIGNED', "dev"),
        'cf_access_key_id' => env('CF_ACCESS_KEY_ID', null),
        'cf_private_key' => env('CF_PRIVATE_KEY', null),
        'expire' => env('EXPIRE', time() + 86400),
        'cloudfront_domain' => env('CLOUDFRONT_DOMAIN', "dev"),
    ],
];
