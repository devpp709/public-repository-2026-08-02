<?php

namespace App\DTO\City;

class CityListResponseDto
{
    public array $items = [];
    public int $total = 0;

    public static function fromEntities(array $cities): self
    {
        $dto = new self();
        $dto->items = CityResponseDto::fromEntities($cities);
        $dto->total = count($cities);
        return $dto;
    }
}
