<?php

namespace App\Service;

use App\DTO\ExtraService\ExtraServiceRequestDTO;
use App\DTO\ExtraService\ExtraServiceResponseDTO;
use App\DTO\ExtraService\ExtraServiceStatisticsDTO;
use App\Entity\Car;
use App\Entity\ExtraService;
use App\Repository\ExtraServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExtraServiceService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        public readonly ExtraServiceRepository $extraServiceRepository
    ) {
    }

    /**
     * Получить все услуги
     */
    public function getAllServices(bool $withStats = false, bool $onlyActive = false): array
    {
        if ($onlyActive) {
            $services = $this->extraServiceRepository->findActive();
        } else {
            $services = $this->extraServiceRepository->findAllOrdered();
        }

        return ExtraServiceResponseDTO::fromEntities($services, $withStats);
    }

    /**
     * Получить услугу по ID
     */
    public function getServiceById(int $id, bool $withStats = false): ExtraServiceResponseDTO
    {
        $service = $this->findServiceOrFail($id);
        return ExtraServiceResponseDTO::fromEntity($service, $withStats);
    }

    /**
     * Создать услугу
     */
    public function createService(ExtraServiceRequestDTO $dto): ExtraServiceResponseDTO
    {
        // Проверяем уникальность названия
        if ($this->extraServiceRepository->existsByName($dto->name)) {
            throw new \InvalidArgumentException('Услуга с таким названием уже существует');
        }

        $service = new ExtraService();
        $this->updateServiceFromDto($service, $dto);

        $this->entityManager->persist($service);
        $this->entityManager->flush();

        return ExtraServiceResponseDTO::fromEntity($service);
    }

    /**
     * Обновить услугу
     */
    public function updateService(int $id, ExtraServiceRequestDTO $dto): ExtraServiceResponseDTO
    {
        $service = $this->findServiceOrFail($id);

        // Проверяем уникальность названия (исключая текущую услугу)
        if ($dto->name && $this->extraServiceRepository->existsByName($dto->name, $id)) {
            throw new \InvalidArgumentException('Услуга с таким названием уже существует');
        }

        $this->updateServiceFromDto($service, $dto);
        $this->entityManager->flush();

        return ExtraServiceResponseDTO::fromEntity($service);
    }

    /**
     * Удалить услугу
     */
    public function deleteService(int $id): void
    {
        $service = $this->findServiceOrFail($id);

        // Проверяем, есть ли у услуги связанные данные
        if ($service->getCarExtraServices()->count() > 0) {
            throw new \RuntimeException(
                sprintf(
                    'Невозможно удалить услугу "%s", так как она используется в %d автомобилях',
                    $service->getName(),
                    $service->getCarExtraServices()->count()
                )
            );
        }

        if ($service->getBookingExtras()->count() > 0) {
            throw new \RuntimeException(
                sprintf(
                    'Невозможно удалить услугу "%s", так как она использовалась в %d бронированиях',
                    $service->getName(),
                    $service->getBookingExtras()->count()
                )
            );
        }

        $this->entityManager->remove($service);
        $this->entityManager->flush();
    }

    /**
     * Поиск услуг
     */
    public function searchServices(string $searchTerm): array
    {
        $services = $this->extraServiceRepository->search($searchTerm);
        return ExtraServiceResponseDTO::fromEntities($services);
    }

    /**
     * Получить услуги по категории
     */
    public function getServicesByCategory(string $category): array
    {
        $services = $this->extraServiceRepository->findByCategory($category);
        return ExtraServiceResponseDTO::fromEntities($services);
    }

    /**
     * Получить услуги по ID автомобиля
     */
    public function getServicesByCarId(int $carId, bool $onlyActive = true): array
    {
        $services = $this->extraServiceRepository->findByCarId($carId, $onlyActive);

        // Получаем автомобиль для расчета цен
        $car = $this->entityManager->getRepository(Car::class)->find($carId);

        if (!$car) {
            throw new NotFoundHttpException(sprintf('Автомобиль с ID %d не найден', $carId));
        }

        return array_map(
            fn(ExtraService $service) => ExtraServiceResponseDTO::fromEntityWithCarPrice($service, $car),
            $services
        );
    }

    /**
     * Получить обязательные услуги для автомобиля
     */
    public function getRequiredServicesForCar(int $carId): array
    {
        $services = $this->extraServiceRepository->findRequiredForCar($carId);

        $car = $this->entityManager->getRepository(Car::class)->find($carId);

        return array_map(
            fn(ExtraService $service) => ExtraServiceResponseDTO::fromEntityWithCarPrice($service, $car),
            $services
        );
    }

    /**
     * Получить статистику по категориям
     */
    public function getCategoryStatistics(): array
    {
        $statistics = $this->extraServiceRepository->getCategoryStatistics();
        return ExtraServiceStatisticsDTO::fromArrayCollection($statistics);
    }

    /**
     * Получить популярные услуги
     */
    public function getPopularServices(int $limit = 10): array
    {
        $services = $this->extraServiceRepository->findPopular($limit);
        return array_map(
            fn($data) => ExtraServiceResponseDTO::fromArrayWithUsage($data),
            $services
        );
    }

    /**
     * Получить все категории
     */
    public function getAllCategories(): array
    {
        $categories = $this->extraServiceRepository->findAllCategories();
        $categoryLabels = [];

        foreach ($categories as $categoryData) {
            $category = $categoryData['category'];
            $categoryLabels[] = [
                'value' => $category,
                'label' => $this->getCategoryLabel($category)
            ];
        }

        return $categoryLabels;
    }

    /**
     * Получить услуги с ценами для автомобиля
     */
    public function getServicesWithPricesForCar(int $carId): array
    {
        $services = $this->extraServiceRepository->findWithPricesForCar($carId);

        $car = $this->entityManager->getRepository(Car::class)->find($carId);

        if (!$car) {
            throw new NotFoundHttpException(sprintf('Автомобиль с ID %d не найден', $carId));
        }

        $result = [];
        foreach ($services as $service) {
            $dto = ExtraServiceResponseDTO::fromEntity($service);
            $dto->priceForCar = $service->getPriceForCar($car);
            $dto->isRequiredForCar = $service->isRequiredForCar($car);
            $result[] = $dto;
        }

        return $result;
    }

    /**
     * Обновить услугу из DTO
     */
    private function updateServiceFromDto(ExtraService $service, ExtraServiceRequestDTO $dto): void
    {
        if ($dto->name !== null) {
            $service->setName($dto->name);
        }
        if ($dto->description !== null) {
            $service->setDescription($dto->description);
        }
        if ($dto->icon !== null) {
            $service->setIcon($dto->icon);
        }
        if ($dto->category !== null) {
            $service->setCategory($dto->category);
        }
        if ($dto->defaultPrice !== null) {
            $service->setDefaultPrice((string) $dto->defaultPrice);
        }
        if ($dto->isActive !== null) {
            $service->setIsActive($dto->isActive);
        }
    }

    /**
     * Найти услугу или выбросить исключение
     */
    private function findServiceOrFail(int $id): ExtraService
    {
        $service = $this->extraServiceRepository->find($id);
        if (!$service) {
            throw new NotFoundHttpException(sprintf('Услуга с ID %d не найдена', $id));
        }

        return $service;
    }

    /**
     * Получить метку категории
     */
    private function getCategoryLabel(?string $category): string
    {
        return match($category) {
            'Insurance' => 'Страхование',
            'Equipment' => 'Оборудование',
            'Comfort' => 'Комфорт',
            'Safety' => 'Безопасность',
            'Additional' => 'Дополнительно',
            default => $category ?? 'Другое'
        };
    }
}
