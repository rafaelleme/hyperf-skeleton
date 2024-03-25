<?php declare(strict_types=1);

namespace App\Model\Users;

use App\Helper\StringHelper;
use App\Model\DomainModel;
use Carbon\Carbon;

class User extends DomainModel
{
    protected const SERIALIZE_FIELDS = [
        'id',
        'name',
        'email',
        'document',
        'type',
        'roles',
    ];

    protected const PROTECTED_FIELDS = [
        'password',
    ];

    private string $name;

    private string $document;

    private string $email;

    private string $password;

    private string $type;

    private array $roles;

    private ?Wallet $wallet;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDocument(): string
    {
        return $this->document;
    }

    public function setDocument(string $document): void
    {
        $this->document = $document;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getRoles(): array
    {
        return $this->roles ?? [];
    }

    public function setRoles(mixed $roles): void
    {
        if (!is_array($roles)) {
            $roles = json_decode($roles);
        }

        $this->roles = $roles;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): void
    {
        $this->wallet = $wallet;
    }

    public function defineType(): void
    {
        if (strlen($this->getDocument()) > 11) {
            $this->setType(UserType::SELLER);
            return;
        }

        $this->setType(UserType::CONSUMER);
    }

    public function delete(): void
    {
        $this->setDeletedAt(Carbon::now()->toString());
    }

    public function isConsumer(): bool
    {
        return $this->type === UserType::CONSUMER;
    }

    public function isSeller(): bool
    {
        return $this->type === UserType::SELLER;
    }
}
