<?php

namespace App\DTO\CarImage;

use App\Entity\CarImage;
use JMS\Serializer\Annotation\Groups;

class CarImageResponseDTO
{
    #[Groups(['car_image:read'])]
    public int $id;

    #[Groups(['car_image:read'])]
    public int $carId;

    #[Groups(['car_image:read'])]
    public string $imageUrl;

    #[Groups(['car_image:read'])]
    public bool $isMain;

    #[Groups(['car_image:read'])]
    public int $sortOrder;

    #[Groups(['car_image:read'])]
    public string $createdAt;

    #[Groups(['car_image:read'])]
    public string $updatedAt;

    public static function fromEntity(CarImage $carImage): self
    {
        $dto = new self();
        $dto->id = $carImage->getId();
        $dto->carId = $carImage->getCar() ? $carImage->getCar()->getId() : 0;
        $dto->imageUrl = $carImage->getImageUrl();
        $dto->isMain = $carImage->isMain();
        $dto->sortOrder = $carImage->getSortOrder();
        $dto->createdAt = $carImage->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $carImage->getUpdatedAt()->format('Y-m-d H:i:s');

        return $dto;
    }

    public static function fromEntities(array $carImages): array
    {
        return array_map(
            fn(CarImage $carImage) => self::fromEntity($carImage),
            $carImages
        );
    }
}
