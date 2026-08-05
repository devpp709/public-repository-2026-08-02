<?php

namespace App\Service;

use App\DTO\CarExtraService\CarExtraServiceRequestDTO;
use App\DTO\CarExtraService\CarExtraServiceResponseDTO;
use App\DTO\CarExtraService\CarExtraServiceStatisticsDTO;
use App\Entity\Car;
use App\Entity\CarExtraService;
use App\Entity\ExtraService;
use App\Repository\CarExtraServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarExtraServiceService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CarExtraServiceRepository $carExtraServiceRepository
    ) {
    }

    /**
     * Получить все услуги для автомобиля
     */
    public function getServicesByCarId(int $carId): array
    {
        $carServices = $this->carExtraServiceRepository->findByCarId($carId);
        return CarExtraServiceResponseDTO::fromEntities($carServices);
    }

    /**
     * Получить обязательные услуги для автомобиля
     */
    public function getRequiredServicesByCarId(int $carId): array
    {
        $carServices = $this->carExtraServiceRepository->findRequiredByCarId($carId);
        return CarExtraServiceResponseDTO::fromEntities($carServices);
    }

    /**
     * Получить услугу для автомобиля
     */
    public function getCarService(int $carId, int $serviceId): CarExtraServiceResponseDTO
    {
        $carService = $this->findCarServiceOrFail($carId, $serviceId);
        return CarExtraServiceResponseDTO::fromEntity($carService);
    }

    /**
     * Добавить услугу автомобилю
     */
    public function addService(int $carId, CarExtraServiceRequestDTO $dto): CarExtraServiceResponseDTO
    {
        $car = $this->findCarOrFail($carId);
        $service = $this->findServiceOrFail($dto->extraServiceId);

        // Проверяем, не существует ли уже такая услуга
        if ($this->carExtraServiceRepository->existsForCar($carId, $dto->extraServiceId)) {
            throw new \InvalidArgumentException(
                sprintf('Услуга "%s" уже добавлена этому автомобилю', $service->getName())
            );
        }

        $carService = new CarExtraService();
        $carService->setCar($car);
        $carService->setExtraService($service);

        if ($dto->price !== null) {
            $carService->setPrice((string) $dto->price);
        }

        if ($dto->isRequired !== null) {
            $carService->setIsRequired($dto->isRequired);
        }

        $this->entityManager->persist($carService);
        $this->entityManager->flush();

        return CarExtraServiceResponseDTO::fromEntity($carService);
    }

    /**
     * Добавить несколько услуг автомобилю (массовое добавление)
     */
    public function addServicesBulk(int $carId, array $serviceDtos): array
    {
        $car = $this->findCarOrFail($carId);
        $results = [];

        foreach ($serviceDtos as $dto) {
            try {
                $result = $this->addService($carId, $dto);
                $results[] = [
                    'success' => true,
                    'data' => $result,
                    'service_id' => $dto->extraServiceId
                ];
            } catch (\InvalidArgumentException $e) {
                $results[] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'service_id' => $dto->extraServiceId
                ];
            }
        }

        return $results;
    }

    /**
     * Обновить услугу у автомобиля
     */
    public function updateService(int $carId, int $serviceId, CarExtraServiceRequestDTO $dto): CarExtraServiceResponseDTO
    {
        $carService = $this->findCarServiceOrFail($carId, $serviceId);

        if ($dto->price !== null) {
            $carService->setPrice((string) $dto->price);
        }

        if ($dto->isRequired !== null) {
            $carService->setIsRequired($dto->isRequired);
        }

        $this->entityManager->flush();

        return CarExtraServiceResponseDTO::fromEntity($carService);
    }

    /**
     * Удалить услугу у автомобиля
     */
    public function removeService(int $carId, int $serviceId): void
    {
        $carService = $this->findCarServiceOrFail($carId, $serviceId);
        $this->entityManager->remove($carService);
        $this->entityManager->flush();
    }

    /**
     * Удалить все услуги автомобиля
     */
    public function removeAllServices(int $carId): void
    {
        $this->carExtraServiceRepository->deleteByCarId($carId);
    }

    /**
     * Заменить все услуги автомобиля на новый список
     */
    public function replaceServices(int $carId, array $serviceDtos): array
    {
        // Удаляем все существующие услуги
        $this->removeAllServices($carId);

        // Добавляем новые
        return $this->addServicesBulk($carId, $serviceDtos);
    }

    /**
     * Получить услуги по категории для автомобиля
     */
    public function getServicesByCategory(int $carId, string $category): array
    {
        $carServices = $this->carExtraServiceRepository->findByCarIdAndCategory($carId, $category);
        return CarExtraServiceResponseDTO::fromEntities($carServices);
    }

    /**
     * Получить статистику по услугам для автомобиля
     */
    public function getStatisticsForCar(int $carId): array
    {
        $statistics = $this->carExtraServiceRepository->getStatisticsForCar($carId);
        return CarExtraServiceStatisticsDTO::fromArrayCollection($statistics);
    }

    /**
     * Получить общую сумму обязательных услуг
     */
    public function getRequiredServicesTotal(int $carId): float
    {
        return $this->carExtraServiceRepository->getRequiredServicesTotal($carId);
    }

    /**
     * Получить услуги с индивидуальной ценой
     */
    public function getServicesWithCustomPrice(int $carId): array
    {
        $carServices = $this->carExtraServiceRepository->findWithCustomPrice($carId);
        return CarExtraServiceResponseDTO::fromEntities($carServices);
    }

    /**
     * Проверить наличие услуги у автомобиля
     */
    public function hasService(int $carId, int $serviceId): bool
    {
        return $this->carExtraServiceRepository->existsForCar($carId, $serviceId);
    }

    /**
     * Копировать услуги с одного автомобиля на другой
     */
    public function copyServices(int $sourceCarId, int $targetCarId): void
    {
        $sourceCar = $this->findCarOrFail($sourceCarId);
        $targetCar = $this->findCarOrFail($targetCarId);

        $this->carExtraServiceRepository->copyServicesFromCar($sourceCarId, $targetCarId);
    }

    /**
     * Найти связь или выбросить исключение
     */
    private function findCarServiceOrFail(int $carId, int $serviceId): CarExtraService
    {
        $carService = $this->carExtraServiceRepository->findByCarAndService($carId, $serviceId);
        if (!$carService) {
            throw new NotFoundHttpException(
                sprintf('Услуга с ID %d не найдена для автомобиля %d', $serviceId, $carId)
            );
        }

        return $carService;
    }

    /**
     * Найти автомобиль или выбросить исключение
     */
    private function findCarOrFail(int $id): Car
    {
        $car = $this->entityManager->getRepository(Car::class)->find($id);
        if (!$car) {
            throw new NotFoundHttpException(sprintf('Автомобиль с ID %d не найден', $id));
        }

        return $car;
    }

    /**
     * Найти услугу или выбросить исключение
     */
    private function findServiceOrFail(int $id): ExtraService
    {
        $service = $this->entityManager->getRepository(ExtraService::class)->find($id);
        if (!$service) {
            throw new NotFoundHttpException(sprintf('Услуга с ID %d не найдена', $id));
        }

        return $service;
    }
}
