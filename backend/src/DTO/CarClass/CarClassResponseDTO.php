<?php

namespace App\DTO\CarClass;

use App\Entity\CarClass;
use JMS\Serializer\Annotation\Groups;

class CarClassResponseDTO
{
    #[Groups(['car_class:read'])]
    public int $id;

    #[Groups(['car_class:read'])]
    public string $name;

    #[Groups(['car_class:read'])]
    public ?string $description;

    #[Groups(['car_class:read'])]
    public ?string $icon;

    #[Groups(['car_class:read'])]
    public ?float $dailyRate;

    #[Groups(['car_class:read'])]
    public ?float $hourlyRate;

    #[Groups(['car_class:read'])]
    public string $createdAt;

    #[Groups(['car_class:read'])]
    public string $updatedAt;

    #[Groups(['car_class:read'])]
    public ?int $carsCount = 0;

    public static function fromEntity(CarClass $carClass, bool $withCarsCount = false): self
    {
        $dto = new self();
        $dto->id = $carClass->getId();
        $dto->name = $carClass->getName();
        $dto->description = $carClass->getDescription();
        $dto->icon = $carClass->getIcon();
        $dto->dailyRate = $carClass->getDailyRate() ? (float) $carClass->getDailyRate() : null;
        $dto->hourlyRate = $carClass->getHourlyRate() ? (float) $carClass->getHourlyRate() : null;
        $dto->createdAt = $carClass->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $carClass->getUpdatedAt()->format('Y-m-d H:i:s');

        if ($withCarsCount) {
            $dto->carsCount = $carClass->getCars()->count();
        }

        return $dto;
    }

    public static function fromEntities(array $carClasses, bool $withCarsCount = false): array
    {
        return array_map(
            fn(CarClass $carClass) => self::fromEntity($carClass, $withCarsCount),
            $carClasses
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'dailyRate' => $this->dailyRate,
            'hourlyRate' => $this->hourlyRate,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'carsCount' => $this->carsCount,
        ];
    }
}
