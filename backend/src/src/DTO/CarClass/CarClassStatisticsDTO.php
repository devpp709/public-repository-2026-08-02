<?php

namespace App\src\DTO\CarClass;

class CarClassStatisticsDTO
{
    public int $id;
    public string $name;
    public int $totalCars;
    public int $availableCars;
    public ?float $minPrice;
    public ?float $maxPrice;
    public ?float $avgPrice;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'];
        $dto->name = $data['name'];
        $dto->totalCars = (int) $data['total_cars'];
        $dto->availableCars = (int) $data['available_cars'];
        $dto->minPrice = $data['min_price'] ? (float) $data['min_price'] : null;
        $dto->maxPrice = $data['max_price'] ? (float) $data['max_price'] : null;
        $dto->avgPrice = $data['avg_price'] ? (float) $data['avg_price'] : null;

        return $dto;
    }

    public static function fromArrayCollection(array $data): array
    {
        return array_map([self::class, 'fromArray'], $data);
    }
}
