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
namespace App\Exception\Users\Transactions;

use App\Exception\DomainException;

final class TransactionNotPermittedException extends DomainException
{
    protected $message = 'Transaction not permitted';
    protected $code = 400;
    protected string $customCode = 'INVALID_TRANSACTION';
}
