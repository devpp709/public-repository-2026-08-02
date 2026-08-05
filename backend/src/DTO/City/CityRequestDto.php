<?php

namespace App\DTO\City;

use Symfony\Component\Validator\Constraints as Assert;

class CityRequestDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 10)]
    public string $code;

    #[Assert\Length(max: 50)]
    public ?string $country = null;

    public bool $isActive = true;
}
