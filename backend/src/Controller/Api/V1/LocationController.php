<?php

namespace App\Controller\Api\V1;

use App\DTO\Location\LocationRequestDTO;
use App\Service\LocationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/locations')]
class LocationController extends AbstractController
{
    public function __construct(
        private readonly LocationService $locationService
    ) {
    }

    /**
     * Получить все локации
     */
    #[Route('', name: 'api_locations_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $withStats = $request->query->getBoolean('with_stats', false);
        $city = $request->query->get('city');
        $search = $request->query->get('search');

        if ($search) {
            $locations = $this->locationService->searchLocations($search);
        } elseif ($city) {
            $locations = $this->locationService->getLocationsByCity($city);
        } else {
            $locations = $this->locationService->getAllLocations($withStats);
        }

        return $this->json([
            'success' => true,
            'data' => $locations
        ]);
    }
//
//
//    /**
//     * Получить локацию по ID
//     */
//    #[Route('/{id}', name: 'api_locations_show', methods: ['GET'])]
//    public function show(int $id, Request $request): JsonResponse
//    {
//        $withStats = $request->query->getBoolean('with_stats', true);
//        $location = $this->locationService->getLocationById($id, $withStats);
//
//        return $this->json([
//            'success' => true,
//            'data' => $location
//        ]);
//    }


    /**
     * Получить локации с доступными автомобилями
     */
    #[Route('/available', name: 'api_locations_available', methods: ['GET'])]
    public function getAvailable(): JsonResponse
    {
        $locations = $this->locationService->getLocationsWithAvailableCars();

        return $this->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    /**
     * Найти локации рядом
     */
    #[Route('/nearby', name: 'api_locations_nearby', methods: ['GET'])]
    public function nearby(Request $request): JsonResponse
    {
        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');
        $radius = $request->query->get('radius', 10);

        if (!$latitude || !$longitude) {
            return $this->json([
                'success' => false,
                'message' => 'Параметры latitude и longitude обязательны'
            ], Response::HTTP_BAD_REQUEST);
        }

        $locations = $this->locationService->findNearby(
            (float) $latitude,
            (float) $longitude,
            (float) $radius
        );

        return $this->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    /**
     * Получить статистику по локациям
     */
    #[Route('/statistics', name: 'api_locations_statistics', methods: ['GET'])]
    public function statistics(): JsonResponse
    {
        $statistics = $this->locationService->getStatistics();

        return $this->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Получить популярные локации
     */
    #[Route('/popular', name: 'api_locations_popular', methods: ['GET'])]
    public function popular(Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', 5);
        $locations = $this->locationService->getPopularLocations($limit);

        return $this->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    /**
     * Создать локацию
     */
    #[Route('', name: 'api_locations_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] LocationRequestDTO $dto): JsonResponse
    {
        try {
            $location = $this->locationService->createLocation($dto);

            return $this->json([
                'success' => true,
                'message' => 'Локация успешно создана',
                'data' => $location
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        }
    }

    /**
     * Обновить локацию
     */
    #[Route('/{id}', name: 'api_locations_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, #[MapRequestPayload] LocationRequestDTO $dto): JsonResponse
    {
        try {
            $location = $this->locationService->updateLocation($id, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Локация успешно обновлена',
                'data' => $location
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
     * Удалить локацию
     */
    #[Route('/{id}', name: 'api_locations_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->locationService->deleteLocation($id);

            return $this->json([
                'success' => true,
                'message' => 'Локация успешно удалена'
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
     * Проверить существование локации
     */
    #[Route('/check-name', name: 'api_locations_check_name', methods: ['GET'])]
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

        $exists = $this->locationService->locationRepository->existsByName($name, $excludeId ?: null);

        return $this->json([
            'success' => true,
            'exists' => $exists
        ]);
    }

    /**
     * Получить локации для фронтенда (группированные по городам)
     */
    #[Route('/frontend', name: 'api_locations_frontend', methods: ['GET'])]
    public function getForFrontend(Request $request): JsonResponse
    {
        $cityId = $request->query->getInt('city_id', 0);
        $cityCode = $request->query->get('city_code');

        $locations = $this->locationService->getLocationsForFrontendGrouped(
            $cityId > 0 ? $cityId : null
        );

        return $this->json([
            'success' => true,
            'data' => $locations
        ]);
    }
}
