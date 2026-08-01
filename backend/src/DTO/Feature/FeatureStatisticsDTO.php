<?php

namespace App\DTO\Feature;

class FeatureStatisticsDTO
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

    public function getCategoryLabel(): string
    {
        return match($this->category) {
            'Safety' => 'Безопасность',
            'Comfort' => 'Комфорт',
            'Technology' => 'Технологии',
            'Exterior' => 'Экстерьер',
            'Interior' => 'Интерьер',
            'Performance' => 'Производительность',
            default => $this->category ?? 'Другое'
        };
    }
}
