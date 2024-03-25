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

namespace App\Controller\Users;

use App\Controller\AbstractController;
use App\Exception\QueryException;
use App\Exception\Users\AlreadyUserExistsException;
use App\Exception\Users\UserNotFoundException;
use App\Service\Users\CreateUserService;
use App\Service\Users\DeleteUserService;
use App\Service\Users\ListUserService;
use Psr\Http\Message\ResponseInterface;

class UsersController extends AbstractController
{
    public function list(
        ListUserService $listUserService,
    ): array
    {
        return $listUserService->getResponse();
    }

    /**
     * @throws AlreadyUserExistsException
     * @throws QueryException
     */
    public function create(
        CreateUserService $createUserService,
    ): ResponseInterface
    {
        $body = json_decode($this->request->getBody()->getContents(), true);

        $user = $createUserService->create([
            'name' => $body['name'],
            'email' => $body['email'],
            'document' => $body['document'],
            'password' => $body['password'],
        ]);

        return $this->jsonResponse($user, 201);
    }

    /**
     * @throws UserNotFoundException
     * @throws QueryException
     */
    public function delete(
        int               $userId,
        DeleteUserService $deleteUserService,
    ): ResponseInterface
    {
        $deleteUserService->delete($userId);
        return $this->jsonResponse(null, 204);
    }
}
