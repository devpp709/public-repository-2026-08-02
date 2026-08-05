<?php

namespace App\DTO\CarExtraService;

class CarExtraServiceStatisticsDTO
{
    public ?string $category;
    public int $total;
    public int $required;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->category = $data['category'];
        $dto->total = (int) $data['total'];
        $dto->required = (int) $data['required'];

        return $dto;
    }

    public static function fromArrayCollection(array $data): array
    {
        return array_map([self::class, 'fromArray'], $data);
    }
}
