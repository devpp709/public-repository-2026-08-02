<?php

namespace App\DTO\CarClass;

use Symfony\Component\Validator\Constraints as Assert;

class CarClassRequestDTO
{
    #[Assert\NotBlank(message: 'Название класса обязательно')]
    #[Assert\Length(max: 100, maxMessage: 'Название не может быть длиннее 100 символов')]
    public ?string $name = null;

    #[Assert\Length(max: 1000)]
    public ?string $description = null;

    #[Assert\Length(max: 50)]
    public ?string $icon = null;

    #[Assert\PositiveOrZero(message: 'Дневная ставка должна быть положительным числом')]
    public ?float $dailyRate = null;

    #[Assert\PositiveOrZero(message: 'Часовая ставка должна быть положительным числом')]
    public ?float $hourlyRate = null;
}
