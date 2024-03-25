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

namespace App\Exception\Handler;

use App\Exception\Users\AlreadyUserExistsException;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected StdoutLoggerInterface $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $codeStatus = $throwable->getCode() ?? 400;
        $message = $throwable->getMessage() ?? 'Internal error';

        if (method_exists($throwable, 'getCustomCode')) {
            $code = $throwable->getCustomCode();
        } else {
            $code = 'INTERNAL_ERROR';
        }

        $data = json_encode([
            'code' => $code,
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE);

        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $this->logger->error($throwable->getTraceAsString());

        $this->stopPropagation();

        return $response->withHeader('Server', 'Hyperf')->withHeader('Content-type', 'application/json')->withStatus($codeStatus)->withBody(new SwooleStream($data));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
