<?php
// src/DTO/AuthResponseDTO.php

namespace App\DTO;

class AuthResponseDTO
{
    public function __construct(
        public readonly array $user,
        public readonly string $accessToken,
        public readonly string $refreshToken,
    ) {}

    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'accessToken' => $this->accessToken,
            'refreshToken' => $this->refreshToken,
        ];
    }

    public static function fromUserAndTokens(
        \App\Entity\User $user,
        string $accessToken,
        string $refreshToken
    ): self {
        return new self(
            user: [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'username' => $user->getUsername(),
                'balance' => $user->getBalance(),
                'createdAt' => $user->getCreatedAt()->format(\DateTimeInterface::ISO8601),
            ],
            accessToken: $accessToken,
            refreshToken: $refreshToken,
        );
    }
}
