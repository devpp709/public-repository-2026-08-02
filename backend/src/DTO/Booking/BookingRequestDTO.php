<?php

namespace App\DTO\Booking;

use Symfony\Component\Validator\Constraints as Assert;

class BookingRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $userId = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $pickupLocationId = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $dropoffLocationId = null;

    #[Assert\NotBlank]
    #[Assert\Date]
    public ?string $pickupDate = null;

    #[Assert\NotBlank]
    #[Assert\Time]
    public ?string $pickupTime = null;

    #[Assert\NotBlank]
    #[Assert\Date]
    public ?string $dropoffDate = null;

    #[Assert\NotBlank]
    #[Assert\Time]
    public ?string $dropoffTime = null;

    #[Assert\NotBlank]
    #[Assert\Type('array')]
    #[Assert\Valid]
    public array $items = [];

    #[Assert\Type('array')]
    #[Assert\Valid]
    public array $extras = [];

    public ?string $notes = null;
}
