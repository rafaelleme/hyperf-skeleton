<?php

namespace App\Trait;

trait SoftDeleteTrait
{
    private ?string $deletedAt;

    public function getDeletedAt(): ?string
    {
        return $this->deletedAt ?? null;
    }

    public function setDeletedAt(?string $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
