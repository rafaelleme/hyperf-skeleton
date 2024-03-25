<?php

namespace App\Job;

use App\Service\Users\Transactions\ProcessTransactionService;
use Exception;
use Hyperf\AsyncQueue\Job;
use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TransactionJob extends Job
{
    public array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function handle(): void
    {
        $container = ApplicationContext::getContainer();
        $service = $container->get(ProcessTransactionService::class);
        $service->process($this->params);
    }
}