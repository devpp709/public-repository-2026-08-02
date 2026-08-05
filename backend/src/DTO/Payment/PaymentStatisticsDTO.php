<?php

namespace App\DTO\Payment;

class PaymentStatisticsDTO
{
    public int $total;
    public int $pending;
    public int $paid;
    public int $failed;
    public int $refunded;
    public float $totalRevenue;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->total = (int) $data['total'];
        $dto->pending = (int) $data['pending'];
        $dto->paid = (int) $data['paid'];
        $dto->failed = (int) $data['failed'];
        $dto->refunded = (int) $data['refunded'];
        $dto->totalRevenue = (float) $data['total_revenue'];

        return $dto;
    }
}
