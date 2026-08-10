<?php

// src/DTO/Reviews/ReviewUserDTO.php
namespace App\DTO\Reviews;

class ReviewUserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $email,
        public readonly ?string $avatar
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? '',
            email: $data['email'] ?? null,
            avatar: $data['avatar'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
        ];
    }
}
