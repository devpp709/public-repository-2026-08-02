<?php

namespace App\DTO\Review;

class ReviewStatisticsDTO
{
    public int $total;
    public float $avgRating;
    public int $minRating;
    public int $maxRating;
    public int $positive;
    public int $neutral;
    public int $negative;
    public int $verified;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->total = (int) $data['total'];
        $dto->avgRating = round((float) $data['avg_rating'], 2);
        $dto->minRating = (int) $data['min_rating'];
        $dto->maxRating = (int) $data['max_rating'];
        $dto->positive = (int) $data['positive'];
        $dto->neutral = (int) $data['neutral'];
        $dto->negative = (int) $data['negative'];
        $dto->verified = (int) $data['verified'];

        return $dto;
    }

    public function getRatingDistribution(): array
    {
        return [
            'positive' => $this->positive,
            'neutral' => $this->neutral,
            'negative' => $this->negative
        ];
    }

    public function getPositivePercentage(): float
    {
        return $this->total > 0 ? round(($this->positive / $this->total) * 100, 2) : 0;
    }

    public function getVerifiedPercentage(): float
    {
        return $this->total > 0 ? round(($this->verified / $this->total) * 100, 2) : 0;
    }
}
