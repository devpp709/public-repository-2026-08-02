<?php

namespace App\DTO\Feature;

use Symfony\Component\Validator\Constraints as Assert;

class FeatureRequestDTO
{
    #[Assert\NotBlank(message: 'Название характеристики обязательно')]
    #[Assert\Length(max: 100, maxMessage: 'Название не может быть длиннее 100 символов')]
    public ?string $name = null;

    #[Assert\Length(max: 50)]
    public ?string $icon = null;

    #[Assert\Choice(
        choices: ['safety', 'comfort', 'technology', 'exterior', 'interior', 'performance'],
        message: 'Выберите корректный код категории'
    )]
    public ?string $categoryCode = null;
}
