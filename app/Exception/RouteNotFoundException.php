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
namespace App\Exception;

use App\Exception\DomainException;

final class RouteNotFoundException extends DomainException
{
    protected $message = 'Route not found';
    protected $code = 404;
    protected string $customCode = 'ROUTE_NOT_FOUND';
}
