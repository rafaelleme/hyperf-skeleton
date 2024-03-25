<?php declare(strict_types=1);

namespace App\Model\Users;

use App\Model\DomainModel;

class Wallet extends DomainModel
{
    protected const SERIALIZE_FIELDS = [
        'id',
        'user_id',
        'balance',
    ];

    private int $userId;

    private float $balance;

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }
}
