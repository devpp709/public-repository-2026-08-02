<?php

namespace App\DTO\CarFeature;

class CarFeatureStatisticsDTO
{
    public ?string $category;
    public int $total;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->category = $data['category'];
        $dto->total = (int) $data['total'];

        return $dto;
    }

    public static function fromArrayCollection(array $data): array
    {
        return array_map([self::class, 'fromArray'], $data);
    }
}
