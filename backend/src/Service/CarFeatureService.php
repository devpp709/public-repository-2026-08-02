<?php

namespace App\Service;

use App\DTO\CarFeature\CarFeatureRequestDTO;
use App\DTO\CarFeature\CarFeatureResponseDTO;
use App\DTO\CarFeature\CarFeatureStatisticsDTO;
use App\Entity\Car;
use App\Entity\CarFeature;
use App\Entity\Feature;
use App\Repository\CarFeatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarFeatureService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CarFeatureRepository $carFeatureRepository
    ) {
    }

    /**
     * Получить все характеристики для автомобиля
     */
    public function getFeaturesByCarId(int $carId): array
    {
        $carFeatures = $this->carFeatureRepository->findByCarId($carId);
        return CarFeatureResponseDTO::fromEntities($carFeatures);
    }

    /**
     * Получить характеристику для автомобиля
     */
    public function getCarFeature(int $carId, int $featureId): CarFeatureResponseDTO
    {
        $carFeature = $this->findCarFeatureOrFail($carId, $featureId);
        return CarFeatureResponseDTO::fromEntity($carFeature);
    }

    /**
     * Добавить характеристику автомобилю
     */
    public function addFeature(int $carId, CarFeatureRequestDTO $dto): CarFeatureResponseDTO
    {
        $car = $this->findCarOrFail($carId);
        $feature = $this->findFeatureOrFail($dto->featureId);

        // Проверяем, не существует ли уже такая характеристика
        if ($this->carFeatureRepository->existsForCar($carId, $dto->featureId)) {
            throw new \InvalidArgumentException(
                sprintf('Характеристика "%s" уже добавлена этому автомобилю', $feature->getName())
            );
        }

        $carFeature = new CarFeature();
        $carFeature->setCar($car);
        $carFeature->setFeature($feature);
        $carFeature->setValue($dto->value);

        $this->entityManager->persist($carFeature);
        $this->entityManager->flush();

        return CarFeatureResponseDTO::fromEntity($carFeature);
    }

    /**
     * Добавить несколько характеристик автомобилю (массовое добавление)
     */
    public function addFeaturesBulk(int $carId, array $featureDtos): array
    {
        $car = $this->findCarOrFail($carId);
        $results = [];

        foreach ($featureDtos as $dto) {
            try {
                $result = $this->addFeature($carId, $dto);
                $results[] = [
                    'success' => true,
                    'data' => $result,
                    'feature_id' => $dto->featureId
                ];
            } catch (\InvalidArgumentException $e) {
                $results[] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'feature_id' => $dto->featureId
                ];
            }
        }

        return $results;
    }

    /**
     * Обновить значение характеристики у автомобиля
     */
    public function updateFeature(int $carId, int $featureId, CarFeatureRequestDTO $dto): CarFeatureResponseDTO
    {
        $carFeature = $this->findCarFeatureOrFail($carId, $featureId);

        if ($dto->value !== null) {
            $carFeature->setValue($dto->value);
        }

        $this->entityManager->flush();

        return CarFeatureResponseDTO::fromEntity($carFeature);
    }

    /**
     * Удалить характеристику у автомобиля
     */
    public function removeFeature(int $carId, int $featureId): void
    {
        $carFeature = $this->findCarFeatureOrFail($carId, $featureId);
        $this->entityManager->remove($carFeature);
        $this->entityManager->flush();
    }

    /**
     * Удалить все характеристики автомобиля
     */
    public function removeAllFeatures(int $carId): void
    {
        $this->carFeatureRepository->deleteByCarId($carId);
    }

    /**
     * Заменить все характеристики автомобиля на новый список
     */
    public function replaceFeatures(int $carId, array $featureDtos): array
    {
        // Удаляем все существующие характеристики
        $this->removeAllFeatures($carId);

        // Добавляем новые
        return $this->addFeaturesBulk($carId, $featureDtos);
    }

    /**
     * Получить характеристики по категории для автомобиля
     */
    public function getFeaturesByCategory(int $carId, string $category): array
    {
        $carFeatures = $this->carFeatureRepository->findByCarIdAndCategory($carId, $category);
        return CarFeatureResponseDTO::fromEntities($carFeatures);
    }

    /**
     * Получить статистику по характеристикам для автомобиля
     */
    public function getStatisticsForCar(int $carId): array
    {
        $statistics = $this->carFeatureRepository->getStatisticsForCar($carId);
        return CarFeatureStatisticsDTO::fromArrayCollection($statistics);
    }

    /**
     * Проверить наличие характеристики у автомобиля
     */
    public function hasFeature(int $carId, int $featureId): bool
    {
        return $this->carFeatureRepository->existsForCar($carId, $featureId);
    }

    /**
     * Получить все уникальные значения характеристики
     */
    public function getDistinctValuesForFeature(int $featureId): array
    {
        return $this->carFeatureRepository->findDistinctValuesByFeature($featureId);
    }

    /**
     * Найти связь или выбросить исключение
     */
    private function findCarFeatureOrFail(int $carId, int $featureId): CarFeature
    {
        $carFeature = $this->carFeatureRepository->findByCarAndFeature($carId, $featureId);
        if (!$carFeature) {
            throw new NotFoundHttpException(
                sprintf('Характеристика с ID %d не найдена для автомобиля %d', $featureId, $carId)
            );
        }

        return $carFeature;
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
     * Найти характеристику или выбросить исключение
     */
    private function findFeatureOrFail(int $id): Feature
    {
        $feature = $this->entityManager->getRepository(Feature::class)->find($id);
        if (!$feature) {
            throw new NotFoundHttpException(sprintf('Характеристика с ID %d не найдена', $id));
        }

        return $feature;
    }
}
