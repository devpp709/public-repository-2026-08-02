<?php

namespace App\DTO\Location;

use App\Entity\Location;

class LocationGroupedByCityDTO
{
    public string $city;
    public array $locations = [];

    public static function fromEntity(Location $location): self
    {
        $dto = new self();
        $dto->city = $location->getCity() ?? 'Без города';

        // Добавляем информацию о локации
        $dto->locations[] = [
            'id' => $location->getId(),
            'name' => $location->getName(),
            'address' => $location->getAddress(),
            'street' => $location->getStreet(),
            'building' => $location->getBuilding(),
            'fullAddress' => $location->getFullAddress(),
            'latitude' => $location->getLatitude(),
            'longitude' => $location->getLongitude(),
            'workingHours' => $location->getWorkingHours(),
            'extraInfo' => $location->getExtraInfo(),
            'phone' => $location->getPhone(),
            'email' => $location->getEmail(),
        ];

        return $dto;
    }

    public static function fromEntities(array $locations): array
    {
        $grouped = [];

        foreach ($locations as $location) {
            $cityName = $location->getCity() ?: 'Без города';

            if (!isset($grouped[$cityName])) {
                $grouped[$cityName] = [
                    'city' => $cityName,
                    'locations' => []
                ];
            }

            $grouped[$cityName]['locations'][] = [
                'id' => $location->getId(),
                'name' => $location->getName(),
                'address' => $location->getAddress()
            ];
        }

        return array_values($grouped);
    }
}
