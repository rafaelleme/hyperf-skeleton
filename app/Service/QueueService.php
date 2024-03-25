<?php

namespace App\Service;

use App\Job\NotificationJob;
use App\Job\TransactionJob;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class QueueService
{
    /**
     * @var DriverInterface
     */
    protected DriverInterface $driver;

    public function __construct(DriverFactory $driverFactory)
    {
        $this->driver = $driverFactory->get('default');
    }

    public function pushToTransaction(array $params, int $delay = 0): bool
    {
        return $this->driver->push(new TransactionJob($params), $delay);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function pushToNotification(array $params, int $delay = 0): bool
    {
        return $this->driver->push(new NotificationJob($params), $delay);
    }
}