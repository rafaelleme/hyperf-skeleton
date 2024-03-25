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
use App\Exception\Users\Transactions\TransactionNotPermittedException;
use App\Service\Users\Transactions\CreateTransactionService;
use Psr\Http\Message\ResponseInterface;

class TransactionsController extends AbstractController
{
    /**
     * @throws TransactionNotPermittedException
     * @throws QueryException
     */
    public function create(
        CreateTransactionService $transactionService,
    ): ResponseInterface
    {
        $body = json_decode($this->request->getBody()->getContents(), true);

        $transactionService->process([
            'payer' => $body['payer'],
            'payee' => $body['payee'],
            'value' => $body['value'],
        ]);

        return $this->jsonResponse(null, 201);
    }
}
