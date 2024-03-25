<?php

namespace App\Trait;

trait TimestampTrait
{
    private ?string $createdAt;

    private ?string $updatedAt;

    public function getCreatedAt(): ?string
    {
        return $this->createdAt ?? null;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt ?? null;
    }

    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
