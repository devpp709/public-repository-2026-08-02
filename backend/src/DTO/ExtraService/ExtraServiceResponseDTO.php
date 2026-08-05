<?php

namespace App\DTO\ExtraService;

use App\Entity\Car;
use App\Entity\ExtraService;
use JMS\Serializer\Annotation\Groups;

class ExtraServiceResponseDTO
{
    #[Groups(['extra_service:read'])]
    public int $id;

    #[Groups(['extra_service:read'])]
    public string $name;

    #[Groups(['extra_service:read'])]
    public ?string $description;

    #[Groups(['extra_service:read'])]
    public ?string $icon;

    #[Groups(['extra_service:read'])]
    public ?string $category;

    #[Groups(['extra_service:read'])]
    public ?string $categoryLabel;

    #[Groups(['extra_service:read'])]
    public ?float $defaultPrice;

    #[Groups(['extra_service:read'])]
    public bool $isActive;

    #[Groups(['extra_service:read'])]
    public string $createdAt;

    #[Groups(['extra_service:read'])]
    public string $updatedAt;

    #[Groups(['extra_service:read'])]
    public ?int $carsCount = 0;

    #[Groups(['extra_service:read'])]
    public ?int $usageCount = 0;

    #[Groups(['extra_service:read'])]
    public ?float $priceForCar = null;

    #[Groups(['extra_service:read'])]
    public ?bool $isRequiredForCar = null;

    public static function fromEntity(ExtraService $extraService, bool $withStats = false): self
    {
        $dto = new self();
        $dto->id = $extraService->getId();
        $dto->name = $extraService->getName();
        $dto->description = $extraService->getDescription();
        $dto->icon = $extraService->getIcon();
        $dto->category = $extraService->getCategory();
        $dto->categoryLabel = $extraService->getCategoryLabel();
        $dto->defaultPrice = $extraService->getDefaultPrice() ? (float) $extraService->getDefaultPrice() : null;
        $dto->isActive = $extraService->isActive();
        $dto->createdAt = $extraService->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $extraService->getUpdatedAt()->format('Y-m-d H:i:s');

        if ($withStats) {
            $dto->carsCount = $extraService->getCarExtraServices()->count();
        }

        return $dto;
    }

    public static function fromEntityWithCarPrice(ExtraService $extraService, ?Car $car = null): self
    {
        $dto = self::fromEntity($extraService);

        if ($car) {
            $dto->priceForCar = $extraService->getPriceForCar($car);
            $dto->isRequiredForCar = $extraService->isRequiredForCar($car);
        }

        return $dto;
    }

    public static function fromEntities(array $extraServices, bool $withStats = false): array
    {
        return array_map(
            fn(ExtraService $extraService) => self::fromEntity($extraService, $withStats),
            $extraServices
        );
    }

    public static function fromArrayWithUsage(array $data): self
    {
        $extraService = $data[0]; // ExtraService entity
        $dto = self::fromEntity($extraService, true);
        $dto->usageCount = (int) $data['usage_count'];

        return $dto;
    }

    public static function fromEntityWithCarPrices(ExtraService $extraService, ?Car $car = null): self
    {
        $dto = self::fromEntity($extraService);

        if ($car) {
            $dto->priceForCar = $extraService->getPriceForCar($car);
            $dto->isRequiredForCar = $extraService->isRequiredForCar($car);
        }

        return $dto;
    }
}
