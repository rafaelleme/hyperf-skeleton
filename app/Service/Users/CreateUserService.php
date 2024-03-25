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
namespace App\Service\Users;

use App\Exception\QueryException;
use App\Exception\Users\AlreadyUserExistsException;
use App\Model\Users\User;
use App\Model\Users\Wallet;
use App\Repository\Users\UserRepository;
use App\Repository\Users\WalletRepository;

class CreateUserService
{
    private UserRepository $userRepository;

    private WalletRepository $walletRepository;

    public function __construct(
        UserRepository $userRepository,
        WalletRepository $walletRepository
    ) {
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
    }

    /**
     * @throws QueryException|AlreadyUserExistsException
     */
    public function create(array $params): array
    {
        $user = $this->getUserByEmailOrDocument($params['email'], $params['document']);

        $this->validate($user);

        $user = $this->prepareUser($params);

        $this->insert($user);

        $user = $this->getUserByEmailOrDocument(
            $user->getEmail(),
            $user->getDocument()
        );

        $this->createWallet($user);

        return $this->response($user);
    }

    private function response(array $user): array
    {
        return User::make($user)->serialize();
    }

    /**
     * @throws QueryException
     */
    private function getUserByEmailOrDocument(string $email, string $document): array
    {
        return $this->userRepository->findByEmailOrDocument(
            $email,
            $document
        );
    }

    /**
     * @throws AlreadyUserExistsException
     */
    private function validate(array $user): void
    {
        if (!empty($user)) {
            throw new AlreadyUserExistsException('Already exists user with document');
        }
    }

    private function prepareUser(array $params): User
    {
        /** @var User $user */
        $user = User::make($params);
        $user->defineType();
        return $user;
    }

    /**
     * @throws QueryException
     */
    private function insert(User $user): void
    {
        $this->userRepository->insert($user);
    }

    /**
     * @throws QueryException
     */
    private function createWallet(array $data): void
    {
        /** @var Wallet $wallet */
        $wallet = Wallet::make([
            'user_id' => $data['id'],
            'balance' => 0,
        ]);

        $this->walletRepository->insert($wallet);
    }
}
