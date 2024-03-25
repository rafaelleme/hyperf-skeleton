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

namespace App\Controller;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

abstract class AbstractController
{
    protected RequestInterface $request;

    protected ResponseInterface $response;

    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
    )
    {
        $this->request = $request;
        $this->response = $response;
    }

    protected function jsonResponse(mixed $data, int $statusCode = 200): \Psr\Http\Message\ResponseInterface
    {
        $res = $this->response->withStatus($statusCode);

        if ($data !== null) {
            $res->json($data);
        }

        return $res;
    }
}
