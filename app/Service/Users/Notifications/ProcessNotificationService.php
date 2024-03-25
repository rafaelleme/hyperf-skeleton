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
namespace App\Service\Users\Notifications;

use App\Model\Users\Transaction;
use App\Repository\Users\TransactionRepository;
use App\Service\Integration\NotificationService;
use GuzzleHttp\Exception\GuzzleException;

class ProcessNotificationService
{
    private TransactionRepository $transactionRepository;

    private NotificationService $notificationService;

    public function __construct(
        TransactionRepository $transactionRepository,
        NotificationService $notificationService
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->notificationService = $notificationService;
    }

    /**
     * @throws GuzzleException
     */
    public function process(array $params): void
    {
        try {
            /** @var Transaction $transaction */
            $transaction = Transaction::make($params['transaction']);

            $this->send(true);
            $this->updateTransactionNotification(
                $transaction
            );
        } catch (\Exception $e) {
            // Todo: implement fallback
        }


    }

    private function updateTransactionNotification(Transaction $transaction): void
    {
        $this->transactionRepository->update($transaction, ['notified' => 'true']);
    }

    /**
     * @throws GuzzleException
     */
    private function send(bool $falseNotification = false): bool
    {
        if ($falseNotification) {
            return true;
        }

        try {
            $this->notificationService->get('');
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
