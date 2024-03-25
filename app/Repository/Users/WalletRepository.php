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
namespace App\Repository\Users;

use App\Exception\QueryException;
use App\Model\Users\User;
use App\Model\Users\Wallet;
use App\Repository\DomainRepository;
use Hyperf\Cache\Annotation\Cacheable;

final class WalletRepository extends DomainRepository
{
    /**
     * @throws QueryException
     */
//    #[Cacheable(prefix: "wallet", ttl: 60, listener: "findByUser")]
    public function findByUser(User $user): array
    {
        $userId = $user->getId();

        $query = "
            SELECT w.*
            FROM users.wallet w
            WHERE w.deleted_at IS NULL
                AND w.user_id = {$userId}
        ";

        return $this->db->query(
            $query
        )[0] ?? [];
    }

    /**
     * @throws QueryException
     */
    public function insert(Wallet $wallet): void
    {
        $this->db->insert(
            'users.wallet',
            [
                'user_id' => $wallet->getUserId(),
                'balance' => $wallet->getBalance(),
            ],
        );
    }

    public function update(Wallet $wallet, array $fields): void
    {
        $walletId = $wallet->getId();

        $values = '';
        $separator = '';
        foreach ($fields as $field => $value) {
            $values .= $separator . $field . ' = ' . $value;
            $separator = ',';
        }

        $query = "
            UPDATE users.wallet w
            SET $values
            WHERE w.id = $walletId
        ";

        $this->db->run(
            $query
        );
    }
}
