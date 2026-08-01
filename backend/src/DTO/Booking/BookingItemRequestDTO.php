<?php

namespace App\DTO\Booking;

use Symfony\Component\Validator\Constraints as Assert;

class BookingItemRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $carId = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?float $dailyRate = null;

    #[Assert\PositiveOrZero]
    public ?float $hourlyRate = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?float $totalPrice = null;
}
