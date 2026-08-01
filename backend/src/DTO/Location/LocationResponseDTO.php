<?php

namespace App\DTO\Location;

use App\Entity\Car;
use App\Entity\Location;
use JMS\Serializer\Annotation\Groups;

class LocationResponseDTO
{
    #[Groups(['location:read'])]
    public int $id;

    #[Groups(['location:read'])]
    public string $name;

    #[Groups(['location:read'])]
    public ?string $address;

    #[Groups(['location:read'])]
    public ?string $city;

    #[Groups(['location:read'])]
    public ?string $state;

    #[Groups(['location:read'])]
    public ?string $country;

    #[Groups(['location:read'])]
    public ?string $zipCode;

    #[Groups(['location:read'])]
    public ?string $phone;

    #[Groups(['location:read'])]
    public ?string $email;

    #[Groups(['location:read'])]
    public ?float $latitude;

    #[Groups(['location:read'])]
    public ?float $longitude;

    #[Groups(['location:read'])]
    public string $createdAt;

    #[Groups(['location:read'])]
    public string $updatedAt;

    #[Groups(['location:read'])]
    public ?string $fullAddress;

    #[Groups(['location:read'])]
    public ?int $carsCount = 0;

    #[Groups(['location:read'])]
    public ?int $availableCarsCount = 0;

    public static function fromEntity(Location $location, bool $withStats = false): self
    {
        $dto = new self();
        $dto->id = $location->getId();
        $dto->name = $location->getName();
        $dto->address = $location->getAddress();
        $dto->city = $location->getCity();
        $dto->state = $location->getState();
        $dto->country = $location->getCountry();
        $dto->zipCode = $location->getZipCode();
        $dto->phone = $location->getPhone();
        $dto->email = $location->getEmail();
        $dto->latitude = $location->getLatitude() ? (float) $location->getLatitude() : null;
        $dto->longitude = $location->getLongitude() ? (float) $location->getLongitude() : null;
        $dto->createdAt = $location->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $location->getUpdatedAt()->format('Y-m-d H:i:s');
        $dto->fullAddress = $location->getFullAddress();

        if ($withStats) {
            $dto->carsCount = $location->getCars()->count();
            $dto->availableCarsCount = $location->getCars()
                ->filter(fn(Car $car) => $car->isAvailable() && $car->getStatus() === 'available')
                ->count();
        }

        return $dto;
    }

    public static function fromEntities(array $locations, bool $withStats = false): array
    {
        return array_map(
            fn(Location $location) => self::fromEntity($location, $withStats),
            $locations
        );
    }
}
