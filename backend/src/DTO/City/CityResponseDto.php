<?php

namespace App\DTO\City;

class CityResponseDto
{
    public int $id;
    public string $name;
    public string $code;
    public ?string $country;
    public bool $isActive;
    public string $createdAt;
    public ?string $updatedAt;

    public static function fromEntity($city): self
    {
        $dto = new self();
        $dto->id = $city->getId();
        $dto->name = $city->getName();
        $dto->code = $city->getCode();
        $dto->country = $city->getCountry();
        $dto->isActive = $city->isActive();
        $dto->createdAt = $city->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $city->getUpdatedAt()?->format('Y-m-d H:i:s');

        return $dto;
    }

    public static function fromEntities(array $cities): array
    {
        return array_map([self::class, 'fromEntity'], $cities);
    }
}
