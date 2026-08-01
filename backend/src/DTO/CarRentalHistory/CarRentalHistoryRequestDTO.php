<?php

namespace App\DTO\CarRentalHistory;

use Symfony\Component\Validator\Constraints as Assert;

class CarRentalHistoryRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $carId = null;

    public ?int $bookingId = null;

    #[Assert\PositiveOrZero]
    public ?int $startMileage = null;

    #[Assert\PositiveOrZero]
    public ?int $endMileage = null;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    public ?string $startDate = null;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    public ?string $endDate = null;

    #[Assert\Choice(choices: ['Excellent', 'Good', 'Fair', 'Poor', 'Damaged'])]
    public ?string $conditionBefore = null;

    #[Assert\Choice(choices: ['Excellent', 'Good', 'Fair', 'Poor', 'Damaged'])]
    public ?string $conditionAfter = null;

    public ?string $notes = null;
}
