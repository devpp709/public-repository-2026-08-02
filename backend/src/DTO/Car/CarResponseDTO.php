<?php

namespace App\DTO\Car;

use App\Entity\Car;
use App\DTO\CarClass\CarClassResponseDTO;
use App\DTO\Location\LocationResponseDTO;
use App\DTO\Feature\FeatureResponseDTO;
use App\DTO\ExtraService\ExtraServiceResponseDTO;
use JMS\Serializer\Annotation\Groups;

class CarResponseDTO
{
    #[Groups(['car:read'])]
    public int $id;

    #[Groups(['car:read'])]
    public ?CarClassResponseDTO $carClass = null;

    #[Groups(['car:read'])]
    public ?LocationResponseDTO $location = null;

    #[Groups(['car:read'])]
    public string $brand;

    #[Groups(['car:read'])]
    public string $model;

    #[Groups(['car:read'])]
    public string $fullName;

    #[Groups(['car:read'])]
    public int $year;

    #[Groups(['car:read'])]
    public ?string $color;

    #[Groups(['car:read'])]
    public string $licensePlate;

    #[Groups(['car:read'])]
    public string $vin;

    #[Groups(['car:read'])]
    public int $mileage;

    #[Groups(['car:read'])]
    public ?string $fuelType;

    #[Groups(['car:read'])]
    public ?string $fuelTypeLabel;

    #[Groups(['car:read'])]
    public ?string $transmission;

    #[Groups(['car:read'])]
    public ?string $transmissionLabel;

    #[Groups(['car:read'])]
    public int $seats;

    #[Groups(['car:read'])]
    public int $doors;

    #[Groups(['car:read'])]
    public int $bags;

    #[Groups(['car:read'])]
    public float $dailyPrice;

    #[Groups(['car:read'])]
    public ?float $hourlyPrice;

    #[Groups(['car:read'])]
    public float $securityDeposit;

    #[Groups(['car:read'])]
    public bool $isAvailable;

    #[Groups(['car:read'])]
    public string $status;

    #[Groups(['car:read'])]
    public string $statusLabel;

    #[Groups(['car:read'])]
    public ?string $description;

    #[Groups(['car:read'])]
    public string $createdAt;

    #[Groups(['car:read'])]
    public string $updatedAt;

    #[Groups(['car:read'])]
    public ?array $images = [];

    #[Groups(['car:read'])]
    public ?string $mainImage = null;

    #[Groups(['car:read'])]
    public ?array $features = [];

    #[Groups(['car:read'])]
    public ?array $extraServices = [];

    #[Groups(['car:read'])]
    public ?float $averageRating = 0;

    #[Groups(['car:read'])]
    public ?int $totalBookings = 0;

    #[Groups(['car:read'])]
    public ?int $totalRentalDays = 0;

    public static function fromEntity(Car $car, bool $withDetails = false): self
    {
        $dto = new self();
        $dto->id = $car->getId();
        $dto->brand = $car->getBrand();
        $dto->model = $car->getModel();
        $dto->fullName = $car->getFullName();
        $dto->year = $car->getYear();
        $dto->color = $car->getColor();
        $dto->licensePlate = $car->getLicensePlate();
        $dto->vin = $car->getVin();
        $dto->mileage = $car->getMileage();
        $dto->fuelType = $car->getFuelType();
        $dto->fuelTypeLabel = $car->getFuelTypeLabel();
        $dto->transmission = $car->getTransmission();
        $dto->transmissionLabel = $car->getTransmissionLabel();
        $dto->seats = $car->getSeats();
        $dto->doors = $car->getDoors();
        $dto->bags = $car->getBags();
        $dto->dailyPrice = (float) $car->getDailyPrice();
        $dto->hourlyPrice = $car->getHourlyPrice() ? (float) $car->getHourlyPrice() : null;
        $dto->securityDeposit = (float) $car->getSecurityDeposit();
        $dto->isAvailable = $car->isAvailable();
        $dto->status = $car->getStatus();
        $dto->statusLabel = $car->getStatusLabel();
        $dto->description = $car->getDescription();
        $dto->createdAt = $car->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $car->getUpdatedAt()->format('Y-m-d H:i:s');

        if ($car->getCarClass()) {
            $dto->carClass = CarClassResponseDTO::fromEntity($car->getCarClass());
        }

        if ($car->getLocation()) {
            $dto->location = LocationResponseDTO::fromEntity($car->getLocation());
        }

        // Изображения
        $dto->images = $car->getImages()->map(function($image) {
            return [
                'id' => $image->getId(),
                'url' => $image->getImageUrl(),
                'isMain' => $image->isMain(),
                'sortOrder' => $image->getSortOrder()
            ];
        })->toArray();

        $mainImage = $car->getMainImage();
        $dto->mainImage = $mainImage ? $mainImage->getImageUrl() : null;

        if ($withDetails) {
            // Характеристики
            $dto->features = $car->getFeatures()->map(function($feature) {
                return FeatureResponseDTO::fromEntity($feature);
            })->toArray();

            // Дополнительные услуги
            $dto->extraServices = $car->getCarExtraServices()->map(function($carExtraService) {
                $extraService = $carExtraService->getExtraService();
                $dto = ExtraServiceResponseDTO::fromEntity($extraService);

                $dto->priceForCar = $carExtraService->getPrice() !== null
                    ? (float) $carExtraService->getPrice()
                    : null;
                $dto->isRequiredForCar = $carExtraService->isRequired();

                return $dto;
            })->toArray();
        }

        return $dto;
    }

    public static function fromEntities(array $cars, bool $withDetails = false): array
    {
        return array_map(
            fn(Car $car) => self::fromEntity($car, $withDetails),
            $cars
        );
    }

    // Добавьте этот метод
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'carClass' => $this->carClass?->toArray(),
            'location' => $this->location?->toArray(),
            'brand' => $this->brand,
            'model' => $this->model,
            'fullName' => $this->fullName,
            'year' => $this->year,
            'color' => $this->color,
            'licensePlate' => $this->licensePlate,
            'vin' => $this->vin,
            'mileage' => $this->mileage,
            'fuelType' => $this->fuelType,
            'fuelTypeLabel' => $this->fuelTypeLabel,
            'transmission' => $this->transmission,
            'transmissionLabel' => $this->transmissionLabel,
            'seats' => $this->seats,
            'doors' => $this->doors,
            'bags' => $this->bags,
            'dailyPrice' => $this->dailyPrice,
            'hourlyPrice' => $this->hourlyPrice,
            'securityDeposit' => $this->securityDeposit,
            'isAvailable' => $this->isAvailable,
            'status' => $this->status,
            'statusLabel' => $this->statusLabel,
            'description' => $this->description,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'images' => $this->images,
            'mainImage' => $this->mainImage,
            'features' => $this->features,
            'extraServices' => $this->extraServices,
            'averageRating' => $this->averageRating,
            'totalBookings' => $this->totalBookings,
            'totalRentalDays' => $this->totalRentalDays,
        ];
    }
}
