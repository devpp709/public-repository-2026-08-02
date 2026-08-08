<?php

namespace App\DTO\Api\Admin;

use Symfony\Component\Serializer\Annotation\Groups;

class CustomersStatisticsDto
{
    #[Groups(['admin:statistics'])]
    private int $totalCustomers;

    #[Groups(['admin:statistics'])]
    private int $newCustomers;

    #[Groups(['admin:statistics'])]
    private float $growthPercentage;

    #[Groups(['admin:statistics'])]
    private string $trend; // 'up', 'down', 'stable'

    #[Groups(['admin:statistics'])]
    private array $dailyStats;

    public function __construct(
        int $totalCustomers,
        int $newCustomers,
        float $growthPercentage,
        string $trend,
        array $dailyStats
    ) {
        $this->totalCustomers = $totalCustomers;
        $this->newCustomers = $newCustomers;
        $this->growthPercentage = $growthPercentage;
        $this->trend = $trend;
        $this->dailyStats = $dailyStats;
    }

    public function getTotalCustomers(): int
    {
        return $this->totalCustomers;
    }

    public function getNewCustomers(): int
    {
        return $this->newCustomers;
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
