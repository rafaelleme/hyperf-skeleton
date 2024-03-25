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
use App\Repository\DomainRepository;
use Hyperf\Cache\Annotation\Cacheable;

final class UserRepository extends DomainRepository
{
    /**
     * @throws QueryException
     */
//    #[Cacheable(prefix: "users", ttl: 60, listener: "list")]
    public function list(bool $count = false): array|int
    {
        $field = $count ? 'COUNT(u.id)' : 'u.*';
        $order = $count ? '' : 'ORDER BY u.id ASC';

        $query = "
            SELECT {$field}
            FROM users.users u
            WHERE u.deleted_at IS NULL
            {$order}
        ";

        $result = $this->db->query(
            $query
        );

        if ($count) {
            return $result[0]['count'];
        }

        return $result;
    }

    public function findById(int $id): array
    {
        $query = "
            SELECT u.*
            FROM users.users u
            WHERE u.deleted_at IS NULL 
                AND u.id = {$id}
        ";

        $result = $this->db->query(
            $query
        );

        return $result[0] ?? [];
    }

    /**
     * @throws QueryException
     */
    public function findByEmailOrDocument(string $email, string $document): array
    {
        $query = "
            SELECT u.*
            FROM users.users u
            WHERE u.deleted_at IS NULL 
                AND (u.email = '{$email}' OR u.document = '{$document}')
        ";

        $result = $this->db->query(
            $query
        );
        
        return $result[0] ?? [];
    }

    /**
     * @throws QueryException
     */
    public function insert(User $user): void
    {
        $this->db->insert(
            'users.users',
            [
                'name' => $user->getName(),
                'document' => $user->getDocument(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'type' => $user->getType(),
            ],
        );
    }

    public function update(User $user, array $fields): void
    {
        $userId = $user->getId();

        $values = '';
        $separator = '';
        foreach ($fields as $field => $value) {
            $values .= $separator . $field . ' = ' . $value;
            $separator = ',';
        }

        $query = "
            UPDATE users.users u
            SET $values
            WHERE u.id = $userId
        ";

        $this->db->run(
            $query
        );
    }
}
