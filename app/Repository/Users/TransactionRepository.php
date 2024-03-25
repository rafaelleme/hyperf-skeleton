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
use App\Model\Users\Transaction;
use App\Repository\DomainRepository;
use Hyperf\Cache\Annotation\Cacheable;

final class TransactionRepository extends DomainRepository
{
    /**
     * @throws QueryException
     */
    public function insert(Transaction $transaction): void
    {
        $this->db->insert(
            'users.transactions',
            [
                'payer_id' => $transaction->getPayerId(),
                'payee_id' => $transaction->getPayeeId(),
                'value' => $transaction->getValue(),
                'status' => $transaction->getStatus(),
                'notified' => $transaction->isNotified() ? 'true' : 'false',
            ],
        );
    }

    public function findByFields(array $params): array
    {
        $payerId = $params['payer_id'];
        $payeeId = $params['payee_id'];
        $value = $params['value'];
        $status = $params['status'];

        $query = "
            SELECT t.*
            FROM users.transactions t
            WHERE t.deleted_at IS NULL 
                AND t.payer_id = {$payerId}
                AND t.payee_id = {$payeeId}
                AND t.value = {$value}
                AND t.status = '{$status}'
        ";

        $result = $this->db->query(
            $query
        );

        return $result[0] ?? [];
    }

    public function update(Transaction $transaction, array $fields): void
    {
        $transactionId = $transaction->getId();

        $values = '';
        $separator = '';
        foreach ($fields as $field => $value) {
            $values .= $separator . $field . " = '" . $value . "'";
            $separator = ',';
        }

        $query = "
            UPDATE users.transactions t
            SET $values
            WHERE t.id = {$transactionId}
        ";

        $this->db->run(
            $query
        );
    }
}
