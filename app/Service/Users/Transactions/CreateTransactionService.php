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
use App\Exception\Users\Transactions\TransactionNotPermittedException;
use App\Exception\Users\UserNotFoundException;
use App\Exception\Users\Wallet\WalletNotFoundException;
use App\Model\Users\Transaction;
use App\Model\Users\User;
use App\Model\Users\Wallet;
use App\Repository\Users\TransactionRepository;
use App\Repository\Users\UserRepository;
use App\Repository\Users\WalletRepository;
use App\Service\Integration\AuthorizerService;
use App\Service\QueueService;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Coroutine\Parallel;

class CreateTransactionService
{
    private UserRepository $userRepository;

    private WalletRepository $walletRepository;

    private TransactionRepository $transactionRepository;

    private QueueService $queueService;

    private AuthorizerService $authorizerService;

    public function __construct(
        UserRepository        $userRepository,
        WalletRepository      $walletRepository,
        TransactionRepository $transactionRepository,

        QueueService          $queueService,
        AuthorizerService     $authorizerService,
    )
    {
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
        $this->transactionRepository = $transactionRepository;

        $this->queueService = $queueService;
        $this->authorizerService = $authorizerService;
    }

    /**
     * @param array $params
     * @throws GuzzleException
     * @throws TransactionNotPermittedException
     */
    public function process(array $params): void
    {
        $parallel = new Parallel();
        $parallel->add(function () use ($params) {
            $user = $this->getUser($params['payer']);
            $user->setWallet(
                $this->getWallet($user)
            );
            return $user;
        }, 'payer');

        $parallel->add(function () use ($params) {
            return $this->getUser($params['payee']);
        }, 'payee');
        $res = $parallel->wait();

        $this->validate($res['payer'], $res['payee'], $params['value']);

        $this->save($res['payer'], $res['payee'], $params['value']);
    }

    private function save(User $payer, User $payee, float $value): void
    {
        try {
            $transaction = $this->createTransaction($payer, $payee, $value);
            $this->updateWalletBalance($payer, $value);
            $this->queueService->pushToTransaction($transaction->serialize());
        } catch (\Exception $e) {
            // todo: Fallback
            throw $e;
        }
    }

    /**
     * @throws QueryException
     */
    private function createTransaction(User $payer, User $payee, float $value): Transaction
    {
        /** @var Transaction $transaction */
        $transaction = Transaction::make([
            'payer_id' => $payer->getId(),
            'payee_id' => $payee->getId(),
            'value' => $value,
            'status' => Transaction::STATUS_PENDING,
            'notified' => false,
        ]);

        $this->transactionRepository->insert($transaction);

        return $transaction;
    }

    private function updateWalletBalance(User $payer, float $value): void
    {
        $wallet = $payer->getWallet();
        $newBalance = $wallet->getBalance() - $value;
        $this->walletRepository->update($wallet, ['balance' => $newBalance]);
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

    /**
     * @throws TransactionNotPermittedException
     * @throws GuzzleException
     */
    private function validate(User $payer, User $payee, float $value): void
    {
        if ($payer->getId() === $payee->getId()) {
            throw new TransactionNotPermittedException('Invalid payer and payee');
        }

        if ($payer->isSeller()) {
            throw new TransactionNotPermittedException('Seller cannot sent value');
        }

        if ($payer->getWallet()->getBalance() < $value) {
            throw new TransactionNotPermittedException('Insufficient funds');
        }

        if (!$this->authorizeTransaction(true)) {
            throw new TransactionNotPermittedException('Transaction not authorized');
        }
    }

    /**
     * @throws GuzzleException
     */
    private function authorizeTransaction(bool $falseAuthorize = false): bool
    {
        if ($falseAuthorize) {
            return true;
        }

        try {
            $this->authorizerService->get('');
        } catch (\Exception $e) {
            // Todo: Implement log
            return false;
        }

        return true;
    }
}
