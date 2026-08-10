<?php

namespace App\Service;

use App\DTO\Car\CarRequestDTO;
use App\DTO\Car\CarResponseDTO;
use App\DTO\Car\CarStatisticsDTO;
use App\DTO\CarClass\CarClassRequestDTO;
use App\DTO\CarClass\CarClassResponseDTO;
use App\DTO\CarClass\CarClassStatisticsDTO;
use App\Entity\Car;
use App\Entity\CarClass;
use App\Entity\Location;
use App\Repository\CarRepository;
use App\Repository\CarClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        public readonly CarRepository           $carRepository,
        private readonly CarClassRepository     $carClassRepository
    ) {
    }

    public function getAllCars(bool $withDetails = false): array
    {
        $cars = $this->carRepository->findWithImages();
        return CarResponseDTO::fromEntities($cars, $withDetails);
    }

    public function getCarById(int $id, bool $withDetails = false): CarResponseDTO
    {
        $car = $this->findCarOrFail($id);
        return CarResponseDTO::fromEntity($car, $withDetails);
    }

    public function createCar(CarRequestDTO $dto): CarResponseDTO
    {
        if ($this->carRepository->existsByVin($dto->vin)) {
            throw new \InvalidArgumentException('Автомобиль с таким VIN-кодом уже существует');
        }

        if ($this->carRepository->existsByLicensePlate($dto->licensePlate)) {
            throw new \InvalidArgumentException('Автомобиль с таким госномером уже существует');
        }

        $car = new Car();
        $this->updateCarFromDto($car, $dto);

        $this->entityManager->persist($car);
        $this->entityManager->flush();

        return CarResponseDTO::fromEntity($car);
    }

    public function updateCar(int $id, CarRequestDTO $dto): CarResponseDTO
    {
        $car = $this->findCarOrFail($id);

        if ($dto->vin && $this->carRepository->existsByVin($dto->vin, $id)) {
            throw new \InvalidArgumentException('Автомобиль с таким VIN-кодом уже существует');
        }

        if ($dto->licensePlate && $this->carRepository->existsByLicensePlate($dto->licensePlate, $id)) {
            throw new \InvalidArgumentException('Автомобиль с таким госномером уже существует');
        }

        $this->updateCarFromDto($car, $dto);
        $this->entityManager->flush();

        return CarResponseDTO::fromEntity($car);
    }

    public function deleteCar(int $id): void
    {
        $car = $this->findCarOrFail($id);

        foreach ($car->getBookings() as $booking) {
            if (in_array($booking->getStatus(), ['pending', 'confirmed', 'in_progress'], true)) {
                throw new \RuntimeException(
                    sprintf(
                        'Невозможно удалить автомобиль "%s", так как есть активные бронирования (#%s)',
                        $car->getFullName(),
                        $booking->getBookingNumber()
                    )
                );
            }
        }

        $car->setStatus('deleted');
        $car->setIsAvailable(false);

        $this->entityManager->flush();
    }

    public function searchCars(array $criteria): array
    {
        $cars = $this->carRepository->search($criteria);
        return CarResponseDTO::fromEntities($cars);
    }

    public function getCarsByClass(int $classId): array
    {
        $cars = $this->carRepository->findByClass($classId);
        return CarResponseDTO::fromEntities($cars);
    }

    public function getCarsByLocation(int $locationId): array
    {
        $cars = $this->carRepository->findByLocation($locationId);
        return CarResponseDTO::fromEntities($cars);
    }

    public function getAvailableCars(array $filters = []): array
    {
        $cars = $this->carRepository->findAvailableCars($filters);
        return CarResponseDTO::fromEntities($cars);
    }

    public function getCarsByPriceRange(float $min, float $max): array
    {
        $cars = $this->carRepository->findByPriceRange($min, $max);
        return CarResponseDTO::fromEntities($cars);
    }

    public function getAvailableForPeriod(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        array $filters = []
    ): array {
        $cars = $this->carRepository->findAvailableForPeriod($startDate, $endDate, $filters);
        return CarResponseDTO::fromEntities($cars);
    }

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

    public function getCarStatistics(): CarStatisticsDTO
    {
        $statistics = $this->carRepository->getStatistics();
        return CarStatisticsDTO::fromArray($statistics);
    }

    public function getBrandStatistics(): array
    {
        return $this->carRepository->getBrandStatistics();
    }

    public function updateCarStatus(int $id, string $status): CarResponseDTO
    {
        $car = $this->findCarOrFail($id);
        $car->setStatus($status);
        $car->setIsAvailable($status === 'available');

        $this->entityManager->flush();

        return CarResponseDTO::fromEntity($car);
    }

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

    // ============================================================
    //  Методы для работы с классами автомобилей (CarClass)
    // ============================================================

    public function getAllClasses(bool $withCarsCount = false): array
    {
        $classes = $this->carClassRepository->findAllOrderedByName();
        return CarClassResponseDTO::fromEntities($classes, $withCarsCount);
    }

    public function getClassById(int $id, bool $withCarsCount = false): CarClassResponseDTO
    {
        $class = $this->findClassOrFail($id);
        return CarClassResponseDTO::fromEntity($class, $withCarsCount);
    }

    public function createClass(CarClassRequestDTO $dto): CarClassResponseDTO
    {
        $class = new CarClass();
        $class->setName($dto->name);
        $class->setDescription($dto->description);
        $class->setIcon($dto->icon);
        $class->setDailyRate($dto->dailyRate !== null ? (string) $dto->dailyRate : null);
        $class->setHourlyRate($dto->hourlyRate !== null ? (string) $dto->hourlyRate : null);

        $this->entityManager->persist($class);
        $this->entityManager->flush();

        return CarClassResponseDTO::fromEntity($class);
    }

    public function updateClass(int $id, CarClassRequestDTO $dto): CarClassResponseDTO
    {
        $class = $this->findClassOrFail($id);

        if ($dto->name !== null) {
            $class->setName($dto->name);
        }
        if ($dto->description !== null) {
            $class->setDescription($dto->description);
        }
        if ($dto->icon !== null) {
            $class->setIcon($dto->icon);
        }
        if ($dto->dailyRate !== null) {
            $class->setDailyRate((string) $dto->dailyRate);
        }
        if ($dto->hourlyRate !== null) {
            $class->setHourlyRate((string) $dto->hourlyRate);
        }

        $this->entityManager->flush();

        return CarClassResponseDTO::fromEntity($class);
    }

    public function deleteClass(int $id): void
    {
        $class = $this->findClassOrFail($id);

        if ($class->getCars()->count() > 0) {
            throw new \RuntimeException('Невозможно удалить класс, так как к нему привязаны автомобили');
        }

        $this->entityManager->remove($class);
        $this->entityManager->flush();
    }

    public function searchClasses(string $searchTerm): array
    {
        $classes = $this->carClassRepository->searchByName($searchTerm);
        return CarClassResponseDTO::fromEntities($classes);
    }

    public function getClassesWithAvailableCars(): array
    {
        $classes = $this->carClassRepository->findWithAvailableCars();
        return CarClassResponseDTO::fromEntities($classes, true);
    }

    public function getClassStatistics(): array
    {
        $statistics = $this->carClassRepository->getClassStatistics();
        return CarClassStatisticsDTO::fromArrayCollection($statistics);
    }

    public function isClassNameExists(string $name, ?int $excludeId = null): bool
    {
        $class = $this->carClassRepository->findOneByName($name);
        if (!$class) {
            return false;
        }

        if ($excludeId !== null && $class->getId() === $excludeId) {
            return false;
        }

        return true;
    }

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

    private function findCarOrFail(int $id): Car
    {
        $car = $this->carRepository->find($id);
        if (!$car) {
            throw new NotFoundHttpException(sprintf('Автомобиль с ID %d не найден', $id));
        }

        return $car;
    }

    private function findClassOrFail(int $id): CarClass
    {
        $class = $this->carClassRepository->find($id);
        if (!$class) {
            throw new NotFoundHttpException(sprintf('Класс автомобиля с ID %d не найден', $id));
        }

        return $class;
    }
}
