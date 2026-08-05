<?php

namespace App\DTO\CarFeature;

use App\Entity\CarFeature;
use App\DTO\Feature\FeatureResponseDTO;
use JMS\Serializer\Annotation\Groups;

class CarFeatureResponseDTO
{
    #[Groups(['car_feature:read'])]
    public int $id;

    #[Groups(['car_feature:read'])]
    public int $carId;

    #[Groups(['car_feature:read'])]
    public FeatureResponseDTO $feature;

    #[Groups(['car_feature:read'])]
    public ?string $value;

    #[Groups(['car_feature:read'])]
    public string $createdAt;

    public static function fromEntity(CarFeature $carFeature): self
    {
        $dto = new self();
        $dto->id = $carFeature->getId();
        $dto->carId = $carFeature->getCar() ? $carFeature->getCar()->getId() : 0;
        $dto->feature = FeatureResponseDTO::fromEntity($carFeature->getFeature());
        $dto->value = $carFeature->getValue();
        $dto->createdAt = $carFeature->getCreatedAt()->format('Y-m-d H:i:s');

        return $dto;
    }

    public static function fromEntities(array $carFeatures): array
    {
        return array_map(
            fn(CarFeature $carFeature) => self::fromEntity($carFeature),
            $carFeatures
        );
    }
}
