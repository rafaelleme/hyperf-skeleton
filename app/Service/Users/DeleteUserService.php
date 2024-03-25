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
use App\Exception\Users\UserNotFoundException;
use App\Model\Users\User;
use App\Repository\Users\UserRepository;
use Carbon\Carbon;

class DeleteUserService
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws QueryException
     * @throws UserNotFoundException
     */
    public function delete(int $userId): void
    {
        $user = $this->getUser($userId);

        if (empty($user)) {
            throw new UserNotFoundException();
        }
        
        $this->process(User::make($user));
    }

    private function getUser(int $userId): array
    {
        return $this->userRepository->findById($userId);
    }

    private function process(User $user): void
    {
        $user->delete();
        $this->userRepository->update($user, ['deleted_at' => 'NOW()']);
    }
}
