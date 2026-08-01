<?php

namespace App\DTO\Feature;

use App\Entity\Feature;
use JMS\Serializer\Annotation\Groups;

class FeatureResponseDTO
{
    #[Groups(['feature:read'])]
    public int $id;

    #[Groups(['feature:read'])]
    public string $name;

    #[Groups(['feature:read'])]
    public ?string $icon;

    #[Groups(['feature:read'])]
    public ?string $category;

    #[Groups(['feature:read'])]
    public ?string $categoryLabel;

    #[Groups(['feature:read'])]
    public string $createdAt;

    #[Groups(['feature:read'])]
    public string $updatedAt;

    #[Groups(['feature:read'])]
    public ?int $carsCount = 0;

    #[Groups(['feature:read'])]
    public ?int $usageCount = 0;

    public static function fromEntity(Feature $feature, bool $withStats = false): self
    {
        $dto = new self();
        $dto->id = $feature->getId();
        $dto->name = $feature->getName();
        $dto->icon = $feature->getIcon();
        $dto->category = $feature->getCategory();
        $dto->categoryLabel = $feature->getCategoryLabel();
        $dto->createdAt = $feature->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $feature->getUpdatedAt()->format('Y-m-d H:i:s');

        if ($withStats) {
            $dto->carsCount = $feature->getCarFeatures()->count();
        }

        return $dto;
    }

    public static function fromEntities(array $features, bool $withStats = false): array
    {
        return array_map(
            fn(Feature $feature) => self::fromEntity($feature, $withStats),
            $features
        );
    }

    public static function fromArrayWithUsage(array $data): self
    {
        $feature = $data[0]; // Feature entity
        $dto = self::fromEntity($feature, true);
        $dto->usageCount = (int) $data['usage_count'];

        return $dto;
    }

    public static function fromArrayWithCarsCount(array $data): self
    {
        $feature = $data[0]; // Feature entity
        $dto = self::fromEntity($feature, true);
        $dto->carsCount = (int) $data['cars_count'];

        return $dto;
    }
}
