<?php

namespace App\DTO\Booking;

use Symfony\Component\Validator\Constraints as Assert;

class BookingExtraDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $extraServiceId = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $quantity = 1;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?float $pricePerUnit = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?float $totalPrice = null;
}
