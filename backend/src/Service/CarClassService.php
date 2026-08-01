<?php

namespace App\Service;

use App\DTO\CarClass\CarClassRequestDTO;
use App\DTO\CarClass\CarClassResponseDTO;
use App\Entity\CarClass;
use App\Repository\CarClassRepository;
use App\src\DTO\CarClass\CarClassStatisticsDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarClassService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CarClassRepository $carClassRepository
    ) {
    }

    /**
     * Получить все классы
     */
    public function getAllClasses(bool $withCarsCount = false): array
    {
        $classes = $this->carClassRepository->findAllOrderedByName();
        return CarClassResponseDTO::fromEntities($classes, $withCarsCount);
    }

    /**
     * Получить класс по ID
     */
    public function getClassById(int $id, bool $withCarsCount = false): CarClassResponseDTO
    {
        $class = $this->findClassOrFail($id);
        return CarClassResponseDTO::fromEntity($class, $withCarsCount);
    }

    /**
     * Создать новый класс
     */
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

    /**
     * Обновить класс
     */
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

    /**
     * Удалить класс
     */
    public function deleteClass(int $id): void
    {
        $class = $this->findClassOrFail($id);

        // Проверяем, есть ли у класса автомобили
        if ($class->getCars()->count() > 0) {
            throw new \RuntimeException('Невозможно удалить класс, так как к нему привязаны автомобили');
        }

        $this->entityManager->remove($class);
        $this->entityManager->flush();
    }

    /**
     * Поиск классов по названию
     */
    public function searchClasses(string $searchTerm): array
    {
        $classes = $this->carClassRepository->searchByName($searchTerm);
        return CarClassResponseDTO::fromEntities($classes);
    }

    /**
     * Получить классы с доступными автомобилями
     */
    public function getClassesWithAvailableCars(): array
    {
        $classes = $this->carClassRepository->findWithAvailableCars();
        return CarClassResponseDTO::fromEntities($classes, true);
    }

    /**
     * Получить статистику по классам
     */
    public function getClassStatistics(): array
    {
        $statistics = $this->carClassRepository->getClassStatistics();
        return CarClassStatisticsDTO::fromArrayCollection($statistics);
    }

    /**
     * Проверить, существует ли класс с таким именем
     */
    public function isNameExists(string $name, ?int $excludeId = null): bool
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

    /**
     * Найти класс или выбросить исключение
     */
    private function findClassOrFail(int $id): CarClass
    {
        $class = $this->carClassRepository->find($id);
        if (!$class) {
            throw new NotFoundHttpException(sprintf('Класс автомобиля с ID %d не найден', $id));
        }

        return $class;
    }
}
