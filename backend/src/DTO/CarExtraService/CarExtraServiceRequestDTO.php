<?php

namespace App\DTO\CarExtraService;

use Symfony\Component\Validator\Constraints as Assert;

class CarExtraServiceRequestDTO
{
    #[Assert\NotBlank(message: 'ID услуги обязателен')]
    #[Assert\Positive]
    public ?int $extraServiceId = null;

    #[Assert\PositiveOrZero(message: 'Цена должна быть положительным числом')]
    public ?float $price = null;

    public ?bool $isRequired = false;
}
