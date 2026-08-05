<?php

namespace App\DTO\Booking;

class BookingStatisticsDTO
{
    public int $total;
    public int $pending;
    public int $confirmed;
    public int $inProgress;
    public int $completed;
    public int $cancelled;
    public float $totalRevenue;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->total = (int) $data['total'];
        $dto->pending = (int) $data['pending'];
        $dto->confirmed = (int) $data['confirmed'];
        $dto->inProgress = (int) $data['in_progress'];
        $dto->completed = (int) $data['completed'];
        $dto->cancelled = (int) $data['cancelled'];
        $dto->totalRevenue = (float) $data['total_revenue'];

        return $dto;
    }
}
