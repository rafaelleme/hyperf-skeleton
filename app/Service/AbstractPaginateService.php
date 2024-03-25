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
namespace App\Service;

abstract class AbstractPaginateService extends DomainService
{
    protected const MAX_LIMIT = 50;

    protected ?int $lastId = null;

    protected ?int $limit = 10;

    protected ?string $order = null;

    public function setLastId(?int $lastId): static
    {
        $this->lastId = $lastId;
        return $this;
    }

    public function getLastId(): ?int
    {
        return $this->lastId;
    }

    public function setLimit(?int $limit): static
    {
        if (empty($limit)) {
            return $this;
        }

        $this->limit = min($limit, self::MAX_LIMIT);

        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function setOrder(?string $order): static
    {
        $this->order = strtoupper($order);
        return $this;
    }

    final public function getResponse(): array
    {
        $items = array_chunk($this->getItems(), $this->limit);
        $lastId = null;

        if (isset($items[0])) {
            $last = end($items[0]);
            $lastId = $last['id'];
        }

        return [
            'totalItems' => $this->getTotalItems(),
            'items' => $items[0] ?? [],
            'lastId' => $lastId,
            'hasMoreItems' => isset($items[1]),
        ];
    }

    abstract protected function getItems(): array;

    abstract protected function getTotalItems(): ?int;
}
