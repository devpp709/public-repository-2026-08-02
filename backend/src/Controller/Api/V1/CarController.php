<?php

namespace App\Controller\Api\V1;

use App\DTO\Car\CarRequestDTO;
use App\Service\CarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/cars')]
class CarController extends AbstractController
{
    public function __construct(
        private readonly CarService $carService
    ) {
    }

    /**
     * Получить все автомобили
     */
    #[Route('', name: 'api_cars_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $withDetails = $request->query->getBoolean('with_details', false);
        $available = $request->query->getBoolean('available', false);
        $classId = $request->query->getInt('class_id', 0);
        $locationId = $request->query->getInt('location_id', 0);
        $brand = $request->query->get('brand');
        $minPrice = $request->query->get('min_price');
        $maxPrice = $request->query->get('max_price');
        $fuelType = $request->query->get('fuel_type');
        $transmission = $request->query->get('transmission');
        $seats = $request->query->getInt('seats', 0);
        $search = $request->query->get('search');

        // Если есть поисковый запрос
        if ($search) {
            $cars = $this->carService->searchCars(['search' => $search]);
            return $this->json(['success' => true, 'data' => $cars]);
        }

        // Фильтрация
        $criteria = [];

        if ($available) {
            $criteria['available'] = true;
        }

        if ($classId > 0) {
            $criteria['classId'] = $classId;
        }

        if ($locationId > 0) {
            $criteria['locationId'] = $locationId;
        }

        if ($brand) {
            $criteria['brand'] = $brand;
        }

        if ($minPrice !== null) {
            $criteria['minPrice'] = (float) $minPrice;
        }

        if ($maxPrice !== null) {
            $criteria['maxPrice'] = (float) $maxPrice;
        }

        if ($fuelType) {
            $criteria['fuelType'] = $fuelType;
        }

        if ($transmission) {
            $criteria['transmission'] = $transmission;
        }

        if ($seats > 0) {
            $criteria['seats'] = $seats;
        }

        if (!empty($criteria)) {
            $cars = $this->carService->searchCars($criteria);
        } else {
            $cars = $this->carService->getAllCars($withDetails);
        }

        return $this->json([
            'success' => true,
            'data' => $cars
        ]);
    }

    /**
     * Получить доступные автомобили
     */
    #[Route('/available', name: 'api_cars_available', methods: ['GET'])]
    public function getAvailable(Request $request): JsonResponse
    {
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');

        if ($startDate && $endDate) {
            $start = new \DateTimeImmutable($startDate);
            $end = new \DateTimeImmutable($endDate);
            $cars = $this->carService->getAvailableForPeriod($start, $end);
        } else {
            $cars = $this->carService->getAvailableCars();
        }

        return $this->json([
            'success' => true,
            'data' => $cars
        ]);
    }

    /**
     * Получить популярные автомобили
     */
    #[Route('/popular', name: 'api_cars_popular', methods: ['GET'])]
    public function getPopular(Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', 10);
        $cars = $this->carService->getPopularCars($limit);

        return $this->json([
            'success' => true,
            'data' => $cars
        ]);
    }

    /**
     * Получить автомобили с высоким рейтингом
     */
    #[Route('/top-rated', name: 'api_cars_top_rated', methods: ['GET'])]
    public function getTopRated(Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', 10);
        $cars = $this->carService->getTopRatedCars($limit);

        return $this->json([
            'success' => true,
            'data' => $cars
        ]);
    }

    /**
     * Получить статистику по автомобилям
     */
    #[Route('/statistics', name: 'api_cars_statistics', methods: ['GET'])]
    public function getStatistics(): JsonResponse
    {
        $statistics = $this->carService->getStatistics();
        $brandStatistics = $this->carService->getBrandStatistics();

        return $this->json([
            'success' => true,
            'data' => [
                'general' => $statistics,
                'by_brand' => $brandStatistics
            ]
        ]);
    }

    /**
     * Получить автомобиль по ID
     */
    #[Route('/{id}', name: 'api_cars_show', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        $withDetails = $request->query->getBoolean('with_details', true);
        $car = $this->carService->getCarById($id, $withDetails);

        return $this->json([
            'success' => true,
            'data' => $car
        ]);
    }

    /**
     * Создать автомобиль
     */
    #[Route('', name: 'api_cars_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CarRequestDTO $dto): JsonResponse
    {
        try {
            $car = $this->carService->createCar($dto);

            return $this->json([
                'success' => true,
                'message' => 'Автомобиль успешно создан',
                'data' => $car
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        }
    }

    /**
     * Обновить автомобиль
     */
    #[Route('/{id}', name: 'api_cars_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, #[MapRequestPayload] CarRequestDTO $dto): JsonResponse
    {
        try {
            $car = $this->carService->updateCar($id, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Автомобиль успешно обновлен',
                'data' => $car
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
     * Обновить статус автомобиля
     */
    #[Route('/{id}/status', name: 'api_cars_update_status', methods: ['PATCH'])]
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $status = $request->request->get('status');

        if (!$status) {
            return $this->json([
                'success' => false,
                'message' => 'Параметр status обязателен'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $car = $this->carService->updateStatus($id, $status);

            return $this->json([
                'success' => true,
                'message' => 'Статус автомобиля успешно обновлен',
                'data' => $car
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Обновить пробег автомобиля
     */
    #[Route('/{id}/mileage', name: 'api_cars_update_mileage', methods: ['PATCH'])]
    public function updateMileage(int $id, Request $request): JsonResponse
    {
        $mileage = $request->request->get('mileage');

        if ($mileage === null) {
            return $this->json([
                'success' => false,
                'message' => 'Параметр mileage обязателен'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $car = $this->carService->updateMileage($id, (int) $mileage);

            return $this->json([
                'success' => true,
                'message' => 'Пробег автомобиля успешно обновлен',
                'data' => $car
            ]);
        } catch (\InvalidArgumentException $e) {
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
     * Удалить автомобиль
     */
    #[Route('/{id}', name: 'api_cars_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->carService->deleteCar($id);

            return $this->json([
                'success' => true,
                'message' => 'Автомобиль успешно удален'
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
     * Проверить существование автомобиля по VIN
     */
    #[Route('/check-vin', name: 'api_cars_check_vin', methods: ['GET'])]
    public function checkVin(Request $request): JsonResponse
    {
        $vin = $request->query->get('vin');
        $excludeId = $request->query->getInt('exclude_id', 0);

        if (!$vin) {
            return $this->json([
                'success' => false,
                'message' => 'Параметр vin обязателен'
            ], Response::HTTP_BAD_REQUEST);
        }

        $exists = $this->carService->carRepository->existsByVin($vin, $excludeId ?: null);

        return $this->json([
            'success' => true,
            'exists' => $exists
        ]);
    }

    /**
     * Проверить существование автомобиля по госномеру
     */
    #[Route('/check-license-plate', name: 'api_cars_check_license_plate', methods: ['GET'])]
    public function checkLicensePlate(Request $request): JsonResponse
    {
        $licensePlate = $request->query->get('license_plate');
        $excludeId = $request->query->getInt('exclude_id', 0);

        if (!$licensePlate) {
            return $this->json([
                'success' => false,
                'message' => 'Параметр license_plate обязателен'
            ], Response::HTTP_BAD_REQUEST);
        }

        $exists = $this->carService->carRepository->existsByLicensePlate($licensePlate, $excludeId ?: null);

        return $this->json([
            'success' => true,
            'exists' => $exists
        ]);
    }
}
