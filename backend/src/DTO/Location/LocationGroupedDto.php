<?php

namespace App\DTO\Location;

use App\Entity\Location;

class LocationGroupedDto
{
    public string $city;
    public string $cityCode;
    public array $locations = [];

    public static function groupByCity(array $locations): array
    {
        $grouped = [];

        foreach ($locations as $location) {
            $cityName = $location->getCity()?->getName() ?? 'Без города';
            $cityCode = $location->getCity()?->getCode() ?? 'no-city';

            if (!isset($grouped[$cityCode])) {
                $grouped[$cityCode] = [
                    'city' => $cityName,
                    'cityCode' => $cityCode,
                    'locations' => []
                ];
            }

            $grouped[$cityCode]['locations'][] = [
                'id' => $location->getId(),
                'name' => $location->getName(),
                'address' => $location->getAddress(),
                'street' => $location->getStreet(),
                'building' => $location->getBuilding(),
                'latitude' => $location->getLatitude(),
                'longitude' => $location->getLongitude(),
                'workingHours' => $location->getWorkingHours(),
                'extraInfo' => $location->getExtraInfo(),
                'fullAddress' => $location->getFullAddress(),
            ];
        }

        // Сортируем по названию города
        ksort($grouped);

        return array_values($grouped);
    }
}
