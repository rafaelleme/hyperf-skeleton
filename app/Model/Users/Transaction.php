<?php declare(strict_types=1);

namespace App\Model\Users;

use App\Model\DomainModel;

class Transaction extends DomainModel
{
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    protected const SERIALIZE_FIELDS = [
        'id',
        'payer_id',
        'payee_id',
        'value',
        'status',
        'notified',
    ];

    private int $payerId;

    private int $payeeId;

    private float $value;

    private string $status;

    private bool $notified;

    public function getPayerId(): int
    {
        return $this->payerId;
    }

    public function setPayerId(int $payerId): void
    {
        $this->payerId = $payerId;
    }

    public function getPayeeId(): int
    {
        return $this->payeeId;
    }

    public function setPayeeId(int $payeeId): void
    {
        $this->payeeId = $payeeId;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): void
    {
        $this->value = $value;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function isNotified(): bool
    {
        return $this->notified;
    }

    public function setNotified(bool $notified): void
    {
        $this->notified = $notified;
    }
}
