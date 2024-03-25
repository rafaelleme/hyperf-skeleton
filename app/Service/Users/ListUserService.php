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
use App\Model\Users\User;
use App\Repository\Users\UserRepository;
use App\Service\AbstractPaginateService;

class ListUserService extends AbstractPaginateService
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws QueryException
     */
    protected function getItems(): array
    {
        return array_map(function (array $user) {
            $user = User::make($user);
            return $user->serialize();
        }, $this->userRepository->list());
    }

    /**
     * @throws QueryException
     */
    protected function getTotalItems(): ?int
    {
        return $this->userRepository->list(true);
    }
}
