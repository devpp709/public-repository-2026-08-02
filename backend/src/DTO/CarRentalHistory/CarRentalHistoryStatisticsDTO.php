<?php

namespace App\DTO\CarRentalHistory;

class CarRentalHistoryStatisticsDTO
{
    public int $totalRentals;
    public int $totalDistance;
    public int $totalDays;
    public int $totalHours;
    public float $avgDistance;
    public ?string $firstRental;
    public ?string $lastRental;
    public int $damagesCount;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->totalRentals = (int) $data['total_rentals'];
        $dto->totalDistance = (int) $data['total_distance'];
        $dto->totalDays = (int) $data['total_days'];
        $dto->totalHours = (int) $data['total_hours'];
        $dto->avgDistance = (float) $data['avg_distance'];
        $dto->firstRental = $data['first_rental'] instanceof \DateTime
            ? $data['first_rental']->format('Y-m-d H:i:s')
            : null;
        $dto->lastRental = $data['last_rental'] instanceof \DateTime
            ? $data['last_rental']->format('Y-m-d H:i:s')
            : null;
        $dto->damagesCount = (int) $data['damages_count'];

        return $dto;
    }
}
