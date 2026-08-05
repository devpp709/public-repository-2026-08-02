<?php

namespace App\DTO\Location;

use Symfony\Component\Validator\Constraints as Assert;

class LocationRequestDTO
{
    #[Assert\NotBlank(message: 'Название локации обязательно')]
    #[Assert\Length(max: 200, maxMessage: 'Название не может быть длиннее 200 символов')]
    public ?string $name = null;

    #[Assert\Length(max: 1000)]
    public ?string $address = null;

    #[Assert\Length(max: 100)]
    public ?string $city = null;

    #[Assert\Length(max: 50)]
    public ?string $state = null;

    #[Assert\Length(max: 50)]
    public ?string $country = null;

    #[Assert\Length(max: 20)]
    public ?string $zipCode = null;

    #[Assert\Regex(pattern: '/^\+?[0-9\s\-()]+$/', message: 'Неверный формат телефона')]
    public ?string $phone = null;

    #[Assert\Email(message: 'Неверный формат email')]
    public ?string $email = null;

    #[Assert\Range(notInRangeMessage: 'Широта должна быть между -90 и 90 градусами', min: -90, max: 90)]
    public ?float $latitude = null;

    #[Assert\Range(notInRangeMessage: 'Долгота должна быть между -180 и 180 градусами', min: -180, max: 180)]
    public ?float $longitude = null;
}
