<?php
// src/DTO/UserProfileDTO.php

namespace App\DTO;

use App\Entity\User;

class UserProfileDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $username,
        public readonly float $balance,
        public readonly float $availableBalance,
        public readonly array $roles,
        public readonly string $createdAt,
        public readonly ?string $updatedAt = null,
    ) {}

    public static function fromUser(User $user): self
    {
        return new self(
            id: $user->getId(),
            email: $user->getEmail(),
            username: $user->getUsername(),
            balance: $user->getBalance(),
            availableBalance: $user->getAvailableBalance(),
            roles: $user->getRoles(),
            createdAt: $user->getCreatedAt()->format(\DateTimeInterface::ISO8601),
            updatedAt: $user->getUpdatedAt()?->format(\DateTimeInterface::ISO8601),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'balance' => $this->balance,
            'availableBalance' => $this->availableBalance,
            'roles' => $this->roles,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
