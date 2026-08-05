<?php

namespace App\Controller\Api\V1;

use App\DTO\CarFeature\CarFeatureRequestDTO;
use App\DTO\CarFeature\CarFeatureBulkRequestDTO;
use App\Service\CarFeatureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/cars/{carId}/features')]
class CarFeatureController extends AbstractController
{
    public function __construct(
        private readonly CarFeatureService $carFeatureService
    ) {
    }

    /**
     * Получить все характеристики автомобиля
     */
    #[Route('', name: 'api_car_features_list', methods: ['GET'])]
    public function list(int $carId, Request $request): JsonResponse
    {
        $category = $request->query->get('category');

        if ($category) {
            $features = $this->carFeatureService->getFeaturesByCategory($carId, $category);
        } else {
            $features = $this->carFeatureService->getFeaturesByCarId($carId);
        }

        return $this->json([
            'success' => true,
            'data' => $features
        ]);
    }

    /**
     * Получить статистику по характеристикам автомобиля
     */
    #[Route('/statistics', name: 'api_car_features_statistics', methods: ['GET'])]
    public function statistics(int $carId): JsonResponse
    {
        $statistics = $this->carFeatureService->getStatisticsForCar($carId);

        return $this->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Проверить наличие характеристики у автомобиля
     */
    #[Route('/check/{featureId}', name: 'api_car_features_check', methods: ['GET'])]
    public function check(int $carId, int $featureId): JsonResponse
    {
        $hasFeature = $this->carFeatureService->hasFeature($carId, $featureId);

        return $this->json([
            'success' => true,
            'has_feature' => $hasFeature
        ]);
    }

    /**
     * Добавить характеристику автомобилю
     */
    #[Route('', name: 'api_car_features_add', methods: ['POST'])]
    public function add(int $carId, #[MapRequestPayload] CarFeatureRequestDTO $dto): JsonResponse
    {
        try {
            $carFeature = $this->carFeatureService->addFeature($carId, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Характеристика успешно добавлена',
                'data' => $carFeature
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Добавить несколько характеристик автомобилю (массовое добавление)
     */
    #[Route('/bulk', name: 'api_car_features_add_bulk', methods: ['POST'])]
    public function addBulk(int $carId, #[MapRequestPayload] CarFeatureBulkRequestDTO $dto): JsonResponse
    {
        try {
            $results = $this->carFeatureService->addFeaturesBulk($carId, $dto->getFeatures());

            $successCount = count(array_filter($results, fn($r) => $r['success']));
            $failCount = count($results) - $successCount;

            return $this->json([
                'success' => true,
                'message' => sprintf('Добавлено %d характеристик, ошибок: %d', $successCount, $failCount),
                'results' => $results
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Заменить все характеристики автомобиля
     */
    #[Route('/replace', name: 'api_car_features_replace', methods: ['PUT'])]
    public function replace(int $carId, #[MapRequestPayload] CarFeatureBulkRequestDTO $dto): JsonResponse
    {
        try {
            $results = $this->carFeatureService->replaceFeatures($carId, $dto->getFeatures());

            return $this->json([
                'success' => true,
                'message' => 'Характеристики автомобиля обновлены',
                'results' => $results
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Получить конкретную характеристику автомобиля
     */
    #[Route('/{featureId}', name: 'api_car_features_show', methods: ['GET'])]
    public function show(int $carId, int $featureId): JsonResponse
    {
        try {
            $carFeature = $this->carFeatureService->getCarFeature($carId, $featureId);

            return $this->json([
                'success' => true,
                'data' => $carFeature
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Обновить значение характеристики автомобиля
     */
    #[Route('/{featureId}', name: 'api_car_features_update', methods: ['PUT', 'PATCH'])]
    public function update(
        int $carId,
        int $featureId,
        #[MapRequestPayload] CarFeatureRequestDTO $dto
    ): JsonResponse {
        try {
            $carFeature = $this->carFeatureService->updateFeature($carId, $featureId, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Значение характеристики обновлено',
                'data' => $carFeature
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Удалить характеристику у автомобиля
     */
    #[Route('/{featureId}', name: 'api_car_features_delete', methods: ['DELETE'])]
    public function delete(int $carId, int $featureId): JsonResponse
    {
        try {
            $this->carFeatureService->removeFeature($carId, $featureId);

            return $this->json([
                'success' => true,
                'message' => 'Характеристика удалена'
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Удалить все характеристики автомобиля
     */
    #[Route('', name: 'api_car_features_delete_all', methods: ['DELETE'])]
    public function deleteAll(int $carId): JsonResponse
    {
        $this->carFeatureService->removeAllFeatures($carId);

        return $this->json([
            'success' => true,
            'message' => 'Все характеристики автомобиля удалены'
        ]);
    }
}
