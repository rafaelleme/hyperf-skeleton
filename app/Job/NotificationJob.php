<?php

namespace App\Job;

use App\Service\Users\Notifications\ProcessNotificationService;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Context\ApplicationContext;
use Hyperf\AsyncQueue\Job;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class NotificationJob extends Job
{
    public array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws GuzzleException
     */
    public function handle(): void
    {
        $container = ApplicationContext::getContainer();
        $service = $container->get(ProcessNotificationService::class);
        $service->process($this->params);
    }
}