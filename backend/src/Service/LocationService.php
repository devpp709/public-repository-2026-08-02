<?php

namespace App\Service;

use App\DTO\Location\LocationRequestDTO;
use App\DTO\Location\LocationResponseDTO;
use App\DTO\Location\LocationStatisticsDTO;
use App\Entity\Location;
use App\src\Repository\LocationRepository;
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
     * Получить локацию по ID
     */
    public function getLocationById(int $id, bool $withStats = false): LocationResponseDTO
    {
        $location = $this->findLocationOrFail($id);
        return LocationResponseDTO::fromEntity($location, $withStats);
    }

    /**
     * Создать новую локацию
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
        if ($dto->name && $this->locationRepository->existsByName($dto->name, $id)) {
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

        // Проверяем, есть ли у локации связанные данные
        if ($location->getCars()->count() > 0) {
            throw new \RuntimeException('Невозможно удалить локацию, так как к ней привязаны автомобили');
        }

        if ($location->getPickupBookings()->count() > 0 || $location->getDropoffBookings()->count() > 0) {
            throw new \RuntimeException('Невозможно удалить локацию, так как с ней связаны бронирования');
        }

        $this->entityManager->remove($location);
        $this->entityManager->flush();
    }

    /**
     * Поиск локаций
     */
    public function searchLocations(string $searchTerm): array
    {
        $locations = $this->locationRepository->search($searchTerm);
        return LocationResponseDTO::fromEntities($locations);
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
     * Получить локации с доступными автомобилями
     */
    public function getLocationsWithAvailableCars(): array
    {
        $locations = $this->locationRepository->findWithAvailableCars();
        return LocationResponseDTO::fromEntities($locations, true);
    }

    /**
     * Найти локации рядом
     */
    public function findNearby(float $latitude, float $longitude, float $radiusKm = 10): array
    {
        $locations = $this->locationRepository->findNearby($latitude, $longitude, $radiusKm);
        return LocationResponseDTO::fromEntities($locations);
    }

    /**
     * Получить статистику по локациям
     */
    public function getStatistics(): array
    {
        $statistics = $this->locationRepository->getStatistics();
        return LocationStatisticsDTO::fromArrayCollection($statistics);
    }

    /**
     * Получить популярные локации
     */
    public function getPopularLocations(int $limit = 5): array
    {
        $locations = $this->locationRepository->findPopular($limit);
        // Преобразуем результат (который содержит данные с booking_count)
        return array_map(
            function($item) {
                $location = $item[0]; // Location entity
                $dto = LocationResponseDTO::fromEntity($location, true);
                $dto->bookingCount = (int) $item['booking_count'];
                return $dto;
            },
            $locations
        );
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
