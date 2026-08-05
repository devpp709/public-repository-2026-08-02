<?php

namespace App\Controller\Api\V1;

use App\DTO\ExtraService\ExtraServiceRequestDTO;
use App\Service\ExtraServiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/extra-services')]
class ExtraServiceController extends AbstractController
{
    public function __construct(
        private readonly ExtraServiceService $extraServiceService
    ) {
    }

    /**
     * Получить все услуги
     */
    #[Route('', name: 'api_extra_services_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $withStats = $request->query->getBoolean('with_stats', false);
        $onlyActive = $request->query->getBoolean('only_active', false);
        $category = $request->query->get('category');
        $search = $request->query->get('search');
        $carId = $request->query->getInt('car_id', 0);

        if ($search) {
            $services = $this->extraServiceService->searchServices($search);
        } elseif ($category) {
            $services = $this->extraServiceService->getServicesByCategory($category);
        } elseif ($carId > 0) {
            $services = $this->extraServiceService->getServicesWithPricesForCar($carId);
        } else {
            $services = $this->extraServiceService->getAllServices($withStats, $onlyActive);
        }

        return $this->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Получить обязательные услуги для автомобиля
     */
    #[Route('/required/{carId}', name: 'api_extra_services_required', methods: ['GET'])]
    public function getRequired(int $carId): JsonResponse
    {
        $services = $this->extraServiceService->getRequiredServicesForCar($carId);

        return $this->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Получить популярные услуги
     */
    #[Route('/popular', name: 'api_extra_services_popular', methods: ['GET'])]
    public function popular(Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', 10);
        $services = $this->extraServiceService->getPopularServices($limit);

        return $this->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Получить статистику по категориям
     */
    #[Route('/statistics', name: 'api_extra_services_statistics', methods: ['GET'])]
    public function statistics(): JsonResponse
    {
        $statistics = $this->extraServiceService->getCategoryStatistics();

        return $this->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Получить все категории
     */
    #[Route('/categories', name: 'api_extra_services_categories', methods: ['GET'])]
    public function getCategories(): JsonResponse
    {
        $categories = $this->extraServiceService->getAllCategories();

        return $this->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Получить услугу по ID
     */
    #[Route('/{id}', name: 'api_extra_services_show', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        $withStats = $request->query->getBoolean('with_stats', true);
        $service = $this->extraServiceService->getServiceById($id, $withStats);

        return $this->json([
            'success' => true,
            'data' => $service
        ]);
    }

    /**
     * Создать услугу
     */
    #[Route('', name: 'api_extra_services_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] ExtraServiceRequestDTO $dto): JsonResponse
    {
        try {
            $service = $this->extraServiceService->createService($dto);

            return $this->json([
                'success' => true,
                'message' => 'Услуга успешно создана',
                'data' => $service
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        }
    }

    /**
     * Обновить услугу
     */
    #[Route('/{id}', name: 'api_extra_services_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, #[MapRequestPayload] ExtraServiceRequestDTO $dto): JsonResponse
    {
        try {
            $service = $this->extraServiceService->updateService($id, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Услуга успешно обновлена',
                'data' => $service
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
     * Удалить услугу
     */
    #[Route('/{id}', name: 'api_extra_services_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->extraServiceService->deleteService($id);

            return $this->json([
                'success' => true,
                'message' => 'Услуга успешно удалена'
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
     * Проверить существование услуги
     */
    #[Route('/check-name', name: 'api_extra_services_check_name', methods: ['GET'])]
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

        $exists = $this->extraServiceService->extraServiceRepository->existsByName($name, $excludeId ?: null);

        return $this->json([
            'success' => true,
            'exists' => $exists
        ]);
    }
}
