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
namespace App\Middleware;

use App\Exception\RouteNotFoundException;
use Hyperf\HttpServer\CoreMiddleware as CoreMiddlewareAlias;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class CoreMiddleware extends CoreMiddlewareAlias implements MiddlewareInterface
{
    /**
     * @throws RouteNotFoundException
     */
    public function handleNotFound(ServerRequestInterface $request): array
    {
        throw new RouteNotFoundException();
    }
}
