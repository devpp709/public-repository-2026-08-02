<?php

namespace App\Service;

use App\DTO\Location\LocationGroupedByCityDTO;
use App\DTO\Location\LocationRequestDTO;
use App\DTO\Location\LocationResponseDTO;
use App\Entity\Location;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        public readonly LocationRepository $locationRepository
    ) {
    }

    /**
     * Получить все локации
     */
    public function getAllLocations(bool $withStats = false): array
    {
        $locations = $this->locationRepository->findAllOrderedByName();
        return LocationResponseDTO::fromEntities($locations, $withStats);
    }


    /**
     * Получить локации, сгруппированные по городам
     */
    public function getLocationsGroupedByCity(bool $onlyActive = true): array
    {
        $locations = $onlyActive
            ? $this->locationRepository->findActiveWithCity()
            : $this->locationRepository->findAllWithCity();

        return LocationGroupedByCityDTO::fromEntities($locations);
    }

    /**
     * Получить локации для конкретного города, сгруппированные
     */
    public function getLocationsByCityGrouped(int $cityId, bool $onlyActive = true): array
    {
        $locations = $this->locationRepository->findByCityId($cityId, $onlyActive);

        if (empty($locations)) {
            return [];
        }

        return LocationGroupedByCityDTO::fromEntities($locations);
    }

    /**
     * Получить локации для фронтенда (группированные по городам)
     */
    public function getLocationsForFrontendGrouped(?int $cityId = null): array
    {
        if ($cityId) {
            $locations = $this->locationRepository->findByCityId($cityId, true);
        } else {
            $locations = $this->locationRepository->findActiveWithCity();
        }

        return LocationGroupedByCityDTO::fromEntities($locations);
    }

    /**
     * Сгруппировать локации по городам
     */
    private function groupLocationsByCity(array $locations): array
    {
        $grouped = [];

        foreach ($locations as $location) {
            $cityName = $location->getCity() ?? 'Без города';

            if (!isset($grouped[$cityName])) {
                $grouped[$cityName] = [
                    'city' => $cityName,
                    'locations' => []
                ];
            }

            $grouped[$cityName]['locations'][] = [
                'id' => $location->getId(),
                'name' => $location->getName(),
                'address' => $location->getAddress(),
                'city' => $location->getCity(),
                'state' => $location->getState(),
                'country' => $location->getCountry(),
                'zipCode' => $location->getZipCode(),
                'phone' => $location->getPhone(),
                'email' => $location->getEmail(),
                'latitude' => $location->getLatitude(),
                'longitude' => $location->getLongitude(),
                'fullAddress' => $location->getFullAddress(),
            ];
        }

        // Сортируем по названию города
        ksort($grouped);

        return array_values($grouped);
    }

    /**
     * Получить локацию по ID
     */
    public function getLocationById(int $id, bool $withStats = false): LocationResponseDTO
    {
        $location = $this->findLocationOrFail($id);
        return LocationResponseDTO::fromEntity($location, $withStats);
    }

    /**
     * Получить локации по городу
     */
    public function getLocationsByCity(string $city): array
    {
        $locations = $this->locationRepository->findByCity($city);
        return LocationResponseDTO::fromEntities($locations);
    }

    /**
     * Поиск локаций
     */
    public function searchLocations(string $search): array
    {
        $locations = $this->locationRepository->search($search);
        return LocationResponseDTO::fromEntities($locations);
    }

    /**
     * Создать локацию
     */
    public function createLocation(LocationRequestDTO $dto): LocationResponseDTO
    {
        // Проверяем уникальность названия
        if ($this->locationRepository->existsByName($dto->name)) {
            throw new \InvalidArgumentException('Локация с таким названием уже существует');
        }

        $location = new Location();
        $this->updateLocationFromDto($location, $dto);

        $this->entityManager->persist($location);
        $this->entityManager->flush();

        return LocationResponseDTO::fromEntity($location);
    }

    /**
     * Обновить локацию
     */
    public function updateLocation(int $id, LocationRequestDTO $dto): LocationResponseDTO
    {
        $location = $this->findLocationOrFail($id);

        // Проверяем уникальность названия (исключая текущую локацию)
        if ($this->locationRepository->existsByName($dto->name, $id)) {
            throw new \InvalidArgumentException('Локация с таким названием уже существует');
        }

        $this->updateLocationFromDto($location, $dto);
        $this->entityManager->flush();

        return LocationResponseDTO::fromEntity($location);
    }

    /**
     * Удалить локацию
     */
    public function deleteLocation(int $id): void
    {
        $location = $this->findLocationOrFail($id);
        $this->entityManager->remove($location);
        $this->entityManager->flush();
    }

    /**
     * Обновить локацию из DTO
     */
    private function updateLocationFromDto(Location $location, LocationRequestDTO $dto): void
    {
        if ($dto->name !== null) {
            $location->setName($dto->name);
        }
        if ($dto->address !== null) {
            $location->setAddress($dto->address);
        }
        if ($dto->city !== null) {
            $location->setCity($dto->city);
        }
        if ($dto->state !== null) {
            $location->setState($dto->state);
        }
        if ($dto->country !== null) {
            $location->setCountry($dto->country);
        }
        if ($dto->zipCode !== null) {
            $location->setZipCode($dto->zipCode);
        }
        if ($dto->phone !== null) {
            $location->setPhone($dto->phone);
        }
        if ($dto->email !== null) {
            $location->setEmail($dto->email);
        }
        if ($dto->latitude !== null) {
            $location->setLatitude((string) $dto->latitude);
        }
        if ($dto->longitude !== null) {
            $location->setLongitude((string) $dto->longitude);
        }
    }

    /**
     * Найти локацию или выбросить исключение
     */
    private function findLocationOrFail(int $id): Location
    {
        $location = $this->locationRepository->find($id);
        if (!$location) {
            throw new NotFoundHttpException(sprintf('Локация с ID %d не найдена', $id));
        }

        return $location;
    }
}
