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
namespace App\Model;

use App\Helper\StringHelper;
use App\Trait\SoftDeleteTrait;
use App\Trait\TimestampTrait;

abstract class DomainModel
{
    use SoftDeleteTrait, TimestampTrait;

    protected const SERIALIZE_FIELDS = [];

    protected const PROTECTED_FIELDS = [];

    private ?int $id;

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function serialize(): array
    {
        $data = [];

        $fields = array_merge(static::SERIALIZE_FIELDS, ['created_at', 'updated_at', 'deleted_at']);

        foreach ($fields as $field) {
            $methodName = 'get' . StringHelper::toCamelCase($field);
            if (method_exists($this, $methodName)) {
                $data[$field] = $this->{$methodName}();
            }
        }

        return array_filter($data);
    }

    public static function make(array $attributes = []): self
    {
        $model = new static();

        $fields = array_merge(static::SERIALIZE_FIELDS, ['created_at', 'updated_at', 'deleted_at']);

        foreach ($fields as $field) {
            if (!isset($attributes[$field])) {
                continue;
            }

            $model->{'set' . StringHelper::toCamelCase($field)}($attributes[$field]);
        }

        foreach (static::PROTECTED_FIELDS as $field) {
            if (!isset($attributes[$field])) {
                continue;
            }

            $model->{'set' . StringHelper::toCamelCase($field)}($attributes[$field]);
        }

        return $model;
    }
}
