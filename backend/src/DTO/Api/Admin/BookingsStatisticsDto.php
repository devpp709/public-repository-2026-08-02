<?php

namespace App\DTO\Api\Admin;

use Symfony\Component\Serializer\Annotation\Groups;

class BookingsStatisticsDto
{
    #[Groups(['admin:statistics'])]
    private int $totalOrders;

    #[Groups(['admin:statistics'])]
    private int $newOrders;

    #[Groups(['admin:statistics'])]
    private float $growthPercentage;

    #[Groups(['admin:statistics'])]
    private string $trend;

    #[Groups(['admin:statistics'])]
    private array $dailyStats;

    public function __construct(
        int $totalOrders,
        int $newOrders,
        float $growthPercentage,
        string $trend,
        array $dailyStats
    ) {
        $this->totalOrders = $totalOrders;
        $this->newOrders = $newOrders;
        $this->growthPercentage = $growthPercentage;
        $this->trend = $trend;
        $this->dailyStats = $dailyStats;
    }

    public function getTotalOrders(): int
    {
        return $this->totalOrders;
    }

    public function getNewOrders(): int
    {
        return $this->newOrders;
    }

    public function getGrowthPercentage(): float
    {
        return $this->growthPercentage;
    }

    public function getTrend(): string
    {
        return $this->trend;
    }

    public function getDailyStats(): array
    {
        return $this->dailyStats;
    }
}
