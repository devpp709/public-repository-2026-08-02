<?php
// src/DTO/Reviews/ReviewsResponseDTO.php

namespace App\DTO\Reviews;

class ReviewsResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $rating,
        public readonly ?string $title,
        public readonly ?string $comment,
        public readonly ?string $pros,
        public readonly ?string $cons,
        public readonly bool $isVerified,
        public readonly int $helpfulCount,
        public readonly string $createdAt,
        public readonly string $updatedAt,
        public readonly ReviewUserDTO $user,
        public readonly ReviewCarDTO $car,
        public readonly ReviewBookingDTO $booking
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            rating: $data['rating'],
            title: $data['title'] ?? null,
            comment: $data['comment'] ?? null,
            pros: $data['pros'] ?? null,
            cons: $data['cons'] ?? null,
            isVerified: $data['is_verified'] ?? false,
            helpfulCount: $data['helpful_count'] ?? 0,
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at'],
            user: ReviewUserDTO::fromArray($data['user'] ?? []),
            car: ReviewCarDTO::fromArray($data['car'] ?? []),
            booking: ReviewBookingDTO::fromArray($data['booking'] ?? [])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'pros' => $this->pros,
            'cons' => $this->cons,
            'isVerified' => $this->isVerified,
            'helpfulCount' => $this->helpfulCount,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'user' => $this->user->toArray(),
            'car' => $this->car->toArray(),
            'booking' => $this->booking->toArray(),
        ];
    }
}





