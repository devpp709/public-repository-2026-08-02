<?php

namespace App\Service;

use App\DTO\Car\CarRequestDTO;
use App\DTO\Car\CarResponseDTO;
use App\DTO\Car\CarStatisticsDTO;
use App\Entity\Car;
use App\Entity\CarClass;
use App\Entity\Location;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        public readonly CarRepository $carRepository
    ) {
    }

    /**
     * Получить все автомобили
     */
    public function getAllCars(bool $withDetails = false): array
    {
        $cars = $this->carRepository->findWithImages();
        return CarResponseDTO::fromEntities($cars, $withDetails);
    }

    /**
     * Получить автомобиль по ID
     */
    public function getCarById(int $id, bool $withDetails = false): CarResponseDTO
    {
        $car = $this->findCarOrFail($id);
        return CarResponseDTO::fromEntity($car, $withDetails);
    }

    /**
     * Создать автомобиль
     */
    public function createCar(CarRequestDTO $dto): CarResponseDTO
    {
        // Проверяем уникальность VIN
        if ($this->carRepository->existsByVin($dto->vin)) {
            throw new \InvalidArgumentException('Автомобиль с таким VIN-кодом уже существует');
        }

        // Проверяем уникальность госномера
        if ($this->carRepository->existsByLicensePlate($dto->licensePlate)) {
            throw new \InvalidArgumentException('Автомобиль с таким госномером уже существует');
        }

        $car = new Car();
        $this->updateCarFromDto($car, $dto);

        $this->entityManager->persist($car);
        $this->entityManager->flush();

        return CarResponseDTO::fromEntity($car);
    }

    /**
     * Обновить автомобиль
     */
    public function updateCar(int $id, CarRequestDTO $dto): CarResponseDTO
    {
        $car = $this->findCarOrFail($id);

        // Проверяем уникальность VIN (исключая текущий автомобиль)
        if ($dto->vin && $this->carRepository->existsByVin($dto->vin, $id)) {
            throw new \InvalidArgumentException('Автомобиль с таким VIN-кодом уже существует');
        }

        // Проверяем уникальность госномера (исключая текущий автомобиль)
        if ($dto->licensePlate && $this->carRepository->existsByLicensePlate($dto->licensePlate, $id)) {
            throw new \InvalidArgumentException('Автомобиль с таким госномером уже существует');
        }

        $this->updateCarFromDto($car, $dto);
        $this->entityManager->flush();

        return CarResponseDTO::fromEntity($car);
    }

    /**
     * Удалить автомобиль (мягкое удаление)
     */
    public function deleteCar(int $id): void
    {
        $car = $this->findCarOrFail($id);

        // Проверяем, есть ли активные бронирования
        foreach ($car->getBookings() as $booking) {
            if (in_array($booking->getStatus(), [
                'pending',
                'confirmed',
                'in_progress',
            ], true)) {
                throw new \RuntimeException(
                    sprintf(
                        'Невозможно удалить автомобиль "%s", так как есть активные бронирования (#%s)',
                        $car->getFullName(),
                        $booking->getBookingNumber()
                    )
                );
            }
        }

        // Мягкое удаление
        $car->setStatus('deleted');
        $car->setIsAvailable(false);

        $this->entityManager->flush();
    }

    /**
     * Поиск автомобилей
     */
    public function searchCars(array $criteria): array
    {
        $cars = $this->carRepository->search($criteria);
        return CarResponseDTO::fromEntities($cars);
    }

    /**
     * Получить автомобили по классу
     */
    public function getCarsByClass(int $classId): array
    {
        $cars = $this->carRepository->findByClass($classId);
        return CarResponseDTO::fromEntities($cars);
    }

    /**
     * Получить автомобили по локации
     */
    public function getCarsByLocation(int $locationId): array
    {
        $cars = $this->carRepository->findByLocation($locationId);
        return CarResponseDTO::fromEntities($cars);
    }

    /**
     * Получить все доступные автомобили с фильтрами
     */
    public function getAvailableCars(array $filters = []): array
    {
        $cars = $this->carRepository->findAvailableCars($filters);
        return CarResponseDTO::fromEntities($cars);
    }

    /**
     * Получить автомобили по цене
     */
    public function getCarsByPriceRange(float $min, float $max): array
    {
        $cars = $this->carRepository->findByPriceRange($min, $max);
        return CarResponseDTO::fromEntities($cars);
    }

    /**
     * Получить автомобили, доступные в указанный период
     */
    public function getAvailableForPeriod(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        array $filters = []
    ): array {
        $cars = $this->carRepository->findAvailableForPeriod($startDate, $endDate, $filters);

        return CarResponseDTO::fromEntities($cars);
    }

    /**
     * Получить популярные автомобили
     */
    public function getPopularCars(int $limit = 10): array
    {
        $cars = $this->carRepository->findPopular($limit);
        return array_map(
            function($data) {
                $car = $data[0];
                $dto = CarResponseDTO::fromEntity($car, true);
                $dto->totalBookings = (int) $data['rental_count'];
                return $dto;
            },
            $cars
        );
    }

    /**
     * Получить автомобили с высоким рейтингом
     */
    public function getTopRatedCars(int $limit = 10): array
    {
        $cars = $this->carRepository->findTopRated($limit);
        return array_map(
            function($data) {
                $car = $data[0];
                $dto = CarResponseDTO::fromEntity($car, true);
                $dto->averageRating = round((float) $data['avg_rating'], 1);
                return $dto;
            },
            $cars
        );
    }

    /**
     * Получить статистику по автомобилям
     */
    public function getStatistics(): CarStatisticsDTO
    {
        $statistics = $this->carRepository->getStatistics();
        return CarStatisticsDTO::fromArray($statistics);
    }

    /**
     * Получить статистику по брендам
     */
    public function getBrandStatistics(): array
    {
        return $this->carRepository->getBrandStatistics();
    }

    /**
     * Обновить статус автомобиля
     */
    public function updateStatus(int $id, string $status): CarResponseDTO
    {
        $car = $this->findCarOrFail($id);
        $car->setStatus($status);

        // Если статус не 'available', то автомобиль недоступен для аренды
        $car->setIsAvailable($status === 'available');

        $this->entityManager->flush();

        return CarResponseDTO::fromEntity($car);
    }

    /**
     * Обновить пробег автомобиля
     */
    public function updateMileage(int $id, int $mileage): CarResponseDTO
    {
        $car = $this->findCarOrFail($id);

        if ($mileage < $car->getMileage()) {
            throw new \InvalidArgumentException('Новый пробег не может быть меньше текущего');
        }

        $car->setMileage($mileage);
        $this->entityManager->flush();

        return CarResponseDTO::fromEntity($car);
    }

    /**
     * Обновить автомобиль из DTO
     */
    private function updateCarFromDto(Car $car, CarRequestDTO $dto): void
    {
        if ($dto->brand !== null) {
            $car->setBrand($dto->brand);
        }
        if ($dto->model !== null) {
            $car->setModel($dto->model);
        }
        if ($dto->year !== null) {
            $car->setYear($dto->year);
        }
        if ($dto->color !== null) {
            $car->setColor($dto->color);
        }
        if ($dto->licensePlate !== null) {
            $car->setLicensePlate($dto->licensePlate);
        }
        if ($dto->vin !== null) {
            $car->setVin($dto->vin);
        }
        if ($dto->mileage !== null) {
            $car->setMileage($dto->mileage);
        }
        if ($dto->fuelType !== null) {
            $car->setFuelType($dto->fuelType);
        }
        if ($dto->transmission !== null) {
            $car->setTransmission($dto->transmission);
        }
        if ($dto->seats !== null) {
            $car->setSeats($dto->seats);
        }
        if ($dto->doors !== null) {
            $car->setDoors($dto->doors);
        }
        if ($dto->bags !== null) {
            $car->setBags($dto->bags);
        }
        if ($dto->dailyPrice !== null) {
            $car->setDailyPrice((string) $dto->dailyPrice);
        }
        if ($dto->hourlyPrice !== null) {
            $car->setHourlyPrice((string) $dto->hourlyPrice);
        }
        if ($dto->securityDeposit !== null) {
            $car->setSecurityDeposit((string) $dto->securityDeposit);
        }
        if ($dto->isAvailable !== null) {
            $car->setIsAvailable($dto->isAvailable);
        }
        if ($dto->status !== null) {
            $car->setStatus($dto->status);
        }
        if ($dto->description !== null) {
            $car->setDescription($dto->description);
        }

        // Связи
        if ($dto->classId !== null) {
            $carClass = $this->entityManager->getRepository(CarClass::class)->find($dto->classId);
            if (!$carClass) {
                throw new NotFoundHttpException(sprintf('Класс автомобиля с ID %d не найден', $dto->classId));
            }
            $car->setCarClass($carClass);
        }

        if ($dto->locationId !== null) {
            $location = $this->entityManager->getRepository(Location::class)->find($dto->locationId);
            if (!$location) {
                throw new NotFoundHttpException(sprintf('Локация с ID %d не найдена', $dto->locationId));
            }
            $car->setLocation($location);
        }
    }

    /**
     * Найти автомобиль или выбросить исключение
     */
    private function findCarOrFail(int $id): Car
    {
        $car = $this->carRepository->find($id);
        if (!$car) {
            throw new NotFoundHttpException(sprintf('Автомобиль с ID %d не найден', $id));
        }

        return $car;
    }
}
