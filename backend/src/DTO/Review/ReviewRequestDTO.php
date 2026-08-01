<?php

namespace App\DTO\Review;

use Symfony\Component\Validator\Constraints as Assert;

class ReviewRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $bookingId = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $carId = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $userId = null;

    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 5)]
    public ?int $rating = null;

    #[Assert\Length(max: 255)]
    public ?string $title = null;

    #[Assert\Length(max: 5000)]
    public ?string $comment = null;

    #[Assert\Length(max: 2000)]
    public ?string $pros = null;

    #[Assert\Length(max: 2000)]
    public ?string $cons = null;

    public ?bool $isVerified = false;
}
