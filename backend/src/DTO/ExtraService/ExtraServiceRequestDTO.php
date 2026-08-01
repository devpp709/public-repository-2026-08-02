<?php

namespace App\DTO\ExtraService;

use Symfony\Component\Validator\Constraints as Assert;

class ExtraServiceRequestDTO
{
    #[Assert\NotBlank(message: 'Название услуги обязательно')]
    #[Assert\Length(max: 200, maxMessage: 'Название не может быть длиннее 200 символов')]
    public ?string $name = null;

    #[Assert\Length(max: 1000)]
    public ?string $description = null;

    #[Assert\Length(max: 50)]
    public ?string $icon = null;

    #[Assert\Choice(
        choices: ['Insurance', 'Equipment', 'Comfort', 'Safety', 'Additional'],
        message: 'Выберите корректную категорию'
    )]
    public ?string $category = null;

    #[Assert\PositiveOrZero(message: 'Цена должна быть положительным числом')]
    public ?float $defaultPrice = null;

    public ?bool $isActive = true;
}
