<?php

namespace App\DTO\CarFeature;

use Symfony\Component\Validator\Constraints as Assert;

class CarFeatureRequestDTO
{
    #[Assert\NotBlank(message: 'ID характеристики обязателен')]
    #[Assert\Positive]
    public ?int $featureId = null;

    #[Assert\Length(max: 100)]
    public ?string $value = null;
}
