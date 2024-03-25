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

namespace App\Service\Users\Transactions;

use App\Exception\QueryException;
use App\Exception\Users\Transactions\TransactionNotFoundException;
use App\Exception\Users\UserNotFoundException;
use App\Exception\Users\Wallet\WalletNotFoundException;
use App\Model\Users\Transaction;
use App\Model\Users\User;
use App\Model\Users\Wallet;
use App\Repository\Users\TransactionRepository;
use App\Repository\Users\UserRepository;
use App\Repository\Users\WalletRepository;
use App\Service\QueueService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ProcessTransactionService
{
    private UserRepository $userRepository;

    private WalletRepository $walletRepository;

    private TransactionRepository $transactionRepository;

    private QueueService $queueService;

    public function __construct(
        UserRepository        $userRepository,
        WalletRepository      $walletRepository,
        TransactionRepository $transactionRepository,
        QueueService          $queueService,
    )
    {
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
        $this->transactionRepository = $transactionRepository;
        $this->queueService = $queueService;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws WalletNotFoundException
     * @throws UserNotFoundException
     * @throws QueryException
     * @throws ContainerExceptionInterface
     * @throws TransactionNotFoundException
     */
    public function process(array $params): void
    {
        $transaction = $this->getTransaction($params);
        
        try {
            $this->updateTransactionStatus($transaction, Transaction::STATUS_PROCESSING);
            $this->sendValueToPayee($transaction);
            $this->updateTransactionStatus($transaction, Transaction::STATUS_SUCCESS);
        } catch (\Exception $e) {
            $this->updateTransactionStatus($transaction, Transaction::STATUS_FAILED);
            throw $e;
        }
    }

    /**
     * @throws TransactionNotFoundException
     */
    private function getTransaction(array $params): Transaction
    {
        $params = array_merge(['status' => 'pending'], $params);
        $transaction = $this->transactionRepository->findByFields($params);

        if (empty($transaction)) {
            throw new TransactionNotFoundException();
        }

        /** @var Transaction $entity */
        $entity = Transaction::make($transaction);
        return $entity;
    }

    private function updateTransactionStatus(Transaction $transaction, string $status): void
    {
        $this->transactionRepository->update($transaction, ['status' => $status]);
    }

    /**
     * @param Transaction $transaction
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws QueryException
     * @throws UserNotFoundException
     * @throws WalletNotFoundException
     */
    private function sendValueToPayee(Transaction $transaction): void
    {
        $payee = $this->getUser($transaction->getPayeeId());
        $payee->setWallet(
            $this->getWallet($payee)
        );
        $this->updateWalletBalance($payee, $transaction);
        $this->sendNotification($payee, $transaction);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function sendNotification(User $user, Transaction $transaction): void
    {
        $this->queueService->pushToNotification([
            'user' => $user->serialize(),
            'transaction' => $transaction->serialize()
        ]);
    }

    /**
     * @throws UserNotFoundException
     */
    private function getUser(int $id): User
    {
        $user = $this->userRepository->findById($id);

        if (empty($user)) {
            throw new UserNotFoundException();
        }

        /** @var User $entity */
        $entity = User::make($user);
        return $entity;
    }

    /**
     * @throws WalletNotFoundException
     * @throws QueryException
     */
    private function getWallet(User $user): Wallet
    {
        $wallet = $this->walletRepository->findByUser($user);

        if (empty($wallet)) {
            throw new WalletNotFoundException();
        }

        /** @var Wallet $entity */
        $entity = Wallet::make($wallet);
        return $entity;
    }

    private function updateWalletBalance(User $payee, Transaction $transaction): void
    {
        $wallet = $payee->getWallet();
        $newBalance = $wallet->getBalance() + $transaction->getValue();
        $this->walletRepository->update($wallet, ['balance' => $newBalance]);
    }
}
