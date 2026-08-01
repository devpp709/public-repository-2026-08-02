<?php

namespace App\Controller\Api\V1;

use App\DTO\Feature\FeatureRequestDTO;
use App\Service\FeatureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/features')]
class FeatureController extends AbstractController
{
    public function __construct(
        private readonly FeatureService $featureService
    ) {
    }

    /**
     * Получить все характеристики
     */
    #[Route('', name: 'api_features_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $withStats = $request->query->getBoolean('with_stats', false);
        $category = $request->query->get('category');
        $search = $request->query->get('search');
        $carId = $request->query->getInt('car_id', 0);

        if ($search) {
            $features = $this->featureService->searchFeatures($search);
        } elseif ($category) {
            $features = $this->featureService->getFeaturesByCategory($category);
        } elseif ($carId > 0) {
            $features = $this->featureService->getFeaturesByCarId($carId);
        } else {
            $features = $this->featureService->getAllFeatures($withStats);
        }

        return $this->json([
            'success' => true,
            'data' => $features
        ]);
    }

    /**
     * Получить характеристики с автомобилями
     */
    #[Route('/with-cars', name: 'api_features_with_cars', methods: ['GET'])]
    public function getWithCars(): JsonResponse
    {
        $features = $this->featureService->getFeaturesWithCars();

        return $this->json([
            'success' => true,
            'data' => $features
        ]);
    }

    /**
     * Получить популярные характеристики
     */
    #[Route('/popular', name: 'api_features_popular', methods: ['GET'])]
    public function popular(Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', 10);
        $features = $this->featureService->getPopularFeatures($limit);

        return $this->json([
            'success' => true,
            'data' => $features
        ]);
    }

    /**
     * Получить статистику по категориям
     */
    #[Route('/statistics', name: 'api_features_statistics', methods: ['GET'])]
    public function statistics(): JsonResponse
    {
        $statistics = $this->featureService->getCategoryStatistics();

        return $this->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Получить все категории
     */
    #[Route('/categories', name: 'api_features_categories', methods: ['GET'])]
    public function getCategories(): JsonResponse
    {
        $categories = $this->featureService->getAllCategories();

        return $this->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Получить характеристику по ID
     */
    #[Route('/{id}', name: 'api_features_show', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        $withStats = $request->query->getBoolean('with_stats', true);
        $feature = $this->featureService->getFeatureById($id, $withStats);

        return $this->json([
            'success' => true,
            'data' => $feature
        ]);
    }

    /**
     * Создать характеристику
     */
    #[Route('', name: 'api_features_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] FeatureRequestDTO $dto): JsonResponse
    {
        try {
            $feature = $this->featureService->createFeature($dto);

            return $this->json([
                'success' => true,
                'message' => 'Характеристика успешно создана',
                'data' => $feature
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        }
    }

    /**
     * Обновить характеристику
     */
    #[Route('/{id}', name: 'api_features_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, #[MapRequestPayload] FeatureRequestDTO $dto): JsonResponse
    {
        try {
            $feature = $this->featureService->updateFeature($id, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Характеристика успешно обновлена',
                'data' => $feature
            ]);
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
     * Удалить характеристику
     */
    #[Route('/{id}', name: 'api_features_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->featureService->deleteFeature($id);

            return $this->json([
                'success' => true,
                'message' => 'Характеристика успешно удалена'
            ]);
        } catch (\RuntimeException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Проверить существование характеристики
     */
    #[Route('/check-name', name: 'api_features_check_name', methods: ['GET'])]
    public function checkName(Request $request): JsonResponse
    {
        $name = $request->query->get('name');
        $excludeId = $request->query->getInt('exclude_id', 0);

        if (!$name) {
            return $this->json([
                'success' => false,
                'message' => 'Параметр "name" обязателен'
            ], Response::HTTP_BAD_REQUEST);
        }

        $exists = $this->featureService->featureRepository->existsByName($name, $excludeId ?: null);

        return $this->json([
            'success' => true,
            'exists' => $exists
        ]);
    }
}
