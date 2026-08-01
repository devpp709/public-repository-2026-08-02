<?php

namespace App\Service;

use App\DTO\Feature\FeatureRequestDTO;
use App\DTO\Feature\FeatureResponseDTO;
use App\DTO\Feature\FeatureStatisticsDTO;
use App\Entity\Feature;
use App\Repository\FeatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FeatureService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        public readonly FeatureRepository $featureRepository
    ) {
    }

    /**
     * Получить все характеристики
     */
    public function getAllFeatures(bool $withStats = false): array
    {
        $features = $this->featureRepository->findAllOrdered();
        return FeatureResponseDTO::fromEntities($features, $withStats);
    }

    /**
     * Получить характеристику по ID
     */
    public function getFeatureById(int $id, bool $withStats = false): FeatureResponseDTO
    {
        $feature = $this->findFeatureOrFail($id);
        return FeatureResponseDTO::fromEntity($feature, $withStats);
    }

    /**
     * Создать характеристику
     */
    public function createFeature(FeatureRequestDTO $dto): FeatureResponseDTO
    {
        // Проверяем уникальность названия
        if ($this->featureRepository->existsByName($dto->name)) {
            throw new \InvalidArgumentException('Характеристика с таким названием уже существует');
        }

        $feature = new Feature();
        $this->updateFeatureFromDto($feature, $dto);

        $this->entityManager->persist($feature);
        $this->entityManager->flush();

        return FeatureResponseDTO::fromEntity($feature);
    }

    /**
     * Обновить характеристику
     */
    public function updateFeature(int $id, FeatureRequestDTO $dto): FeatureResponseDTO
    {
        $feature = $this->findFeatureOrFail($id);

        // Проверяем уникальность названия (исключая текущую характеристику)
        if ($dto->name && $this->featureRepository->existsByName($dto->name, $id)) {
            throw new \InvalidArgumentException('Характеристика с таким названием уже существует');
        }

        $this->updateFeatureFromDto($feature, $dto);
        $this->entityManager->flush();

        return FeatureResponseDTO::fromEntity($feature);
    }

    /**
     * Удалить характеристику
     */
    public function deleteFeature(int $id): void
    {
        $feature = $this->findFeatureOrFail($id);

        // Проверяем, есть ли у характеристики связанные данные
        if ($feature->getCarFeatures()->count() > 0) {
            throw new \RuntimeException(
                sprintf(
                    'Невозможно удалить характеристику "%s", так как она используется в %d автомобилях',
                    $feature->getName(),
                    $feature->getCarFeatures()->count()
                )
            );
        }

        $this->entityManager->remove($feature);
        $this->entityManager->flush();
    }

    /**
     * Поиск характеристик
     */
    public function searchFeatures(string $searchTerm): array
    {
        $features = $this->featureRepository->search($searchTerm);
        return FeatureResponseDTO::fromEntities($features);
    }

    /**
     * Получить характеристики по категории
     */
    public function getFeaturesByCategory(string $category): array
    {
        $features = $this->featureRepository->findByCategory($category);
        return FeatureResponseDTO::fromEntities($features);
    }

    /**
     * Получить характеристики по ID автомобиля
     */
    public function getFeaturesByCarId(int $carId): array
    {
        $features = $this->featureRepository->findByCarId($carId);
        return FeatureResponseDTO::fromEntities($features);
    }

    /**
     * Получить характеристики по категориям
     */
    public function getFeaturesByCategories(array $categories): array
    {
        $features = $this->featureRepository->findByCategories($categories);
        return FeatureResponseDTO::fromEntities($features);
    }

    /**
     * Получить статистику по категориям
     */
    public function getCategoryStatistics(): array
    {
        $statistics = $this->featureRepository->getCategoryStatistics();
        return FeatureStatisticsDTO::fromArrayCollection($statistics);
    }

    /**
     * Получить популярные характеристики
     */
    public function getPopularFeatures(int $limit = 10): array
    {
        $features = $this->featureRepository->findPopular($limit);
        return array_map(
            fn($data) => FeatureResponseDTO::fromArrayWithUsage($data),
            $features
        );
    }

    /**
     * Получить все категории
     */
    public function getAllCategories(): array
    {
        $categories = $this->featureRepository->findAllCategories();
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
     * Получить характеристики с автомобилями
     */
    public function getFeaturesWithCars(): array
    {
        $features = $this->featureRepository->findWithCars();
        return array_map(
            fn($data) => FeatureResponseDTO::fromArrayWithCarsCount($data),
            $features
        );
    }

    /**
     * Обновить характеристику из DTO
     */
    private function updateFeatureFromDto(Feature $feature, FeatureRequestDTO $dto): void
    {
        if ($dto->name !== null) {
            $feature->setName($dto->name);
        }
        if ($dto->icon !== null) {
            $feature->setIcon($dto->icon);
        }
        if ($dto->category !== null) {
            $feature->setCategory($dto->category);
        }
    }

    /**
     * Найти характеристику или выбросить исключение
     */
    private function findFeatureOrFail(int $id): Feature
    {
        $feature = $this->featureRepository->find($id);
        if (!$feature) {
            throw new NotFoundHttpException(sprintf('Характеристика с ID %d не найдена', $id));
        }

        return $feature;
    }

    /**
     * Получить метку категории
     */
    private function getCategoryLabel(?string $category): string
    {
        return match($category) {
            'Safety' => 'Безопасность',
            'Comfort' => 'Комфорт',
            'Technology' => 'Технологии',
            'Exterior' => 'Экстерьер',
            'Interior' => 'Интерьер',
            'Performance' => 'Производительность',
            default => $category ?? 'Другое'
        };
    }
}
