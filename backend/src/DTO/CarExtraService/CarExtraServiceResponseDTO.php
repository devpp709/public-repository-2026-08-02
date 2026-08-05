<?php

namespace App\DTO\CarExtraService;

use App\Entity\CarExtraService;
use App\DTO\ExtraService\ExtraServiceResponseDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class CarExtraServiceResponseDTO
{
    #[Groups(['car_extra_service:read'])]
    public int $id;

    #[Groups(['car_extra_service:read'])]
    public int $carId;

    #[Groups(['car_extra_service:read'])]
    public ExtraServiceResponseDTO $extraService;

    #[Groups(['car_extra_service:read'])]
    public ?float $price;

    #[Groups(['car_extra_service:read'])]
    public ?float $effectivePrice;

    #[Groups(['car_extra_service:read'])]
    public bool $isRequired;

    #[Groups(['car_extra_service:read'])]
    public bool $hasCustomPrice;

    #[Groups(['car_extra_service:read'])]
    public string $createdAt;

    #[Groups(['car_extra_service:read'])]
    public string $updatedAt;

    public static function fromEntity(CarExtraService $carExtraService): self
    {
        $dto = new self();
        $dto->id = $carExtraService->getId();
        $dto->carId = $carExtraService->getCar() ? $carExtraService->getCar()->getId() : 0;
        $dto->extraService = ExtraServiceResponseDTO::fromEntity($carExtraService->getExtraService());
        $dto->price = $carExtraService->getPrice() ? (float) $carExtraService->getPrice() : null;
        $dto->effectivePrice = $carExtraService->getEffectivePrice();
        $dto->isRequired = $carExtraService->isRequired();
        $dto->hasCustomPrice = $carExtraService->hasCustomPrice();
        $dto->createdAt = $carExtraService->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $carExtraService->getUpdatedAt()->format('Y-m-d H:i:s');

        return $dto;
    }

    public static function fromEntities(array $carExtraServices): array
    {
        return array_map(
            fn(CarExtraService $carExtraService) => self::fromEntity($carExtraService),
            $carExtraServices
        );
    }
}
