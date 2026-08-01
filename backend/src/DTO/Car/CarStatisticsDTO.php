<?php

namespace App\DTO\Car;

class CarStatisticsDTO
{
    public int $total;
    public int $available;
    public int $rented;
    public int $maintenance;
    public int $reserved;
    public ?float $avgDailyPrice;
    public ?float $minDailyPrice;
    public ?float $maxDailyPrice;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->total = (int) $data['total'];
        $dto->available = (int) $data['available'];
        $dto->rented = (int) $data['rented'];
        $dto->maintenance = (int) $data['maintenance'];
        $dto->reserved = (int) $data['reserved'];
        $dto->avgDailyPrice = $data['avg_daily_price'] ? (float) $data['avg_daily_price'] : null;
        $dto->minDailyPrice = $data['min_daily_price'] ? (float) $data['min_daily_price'] : null;
        $dto->maxDailyPrice = $data['max_daily_price'] ? (float) $data['max_daily_price'] : null;

        return $dto;
    }
}
