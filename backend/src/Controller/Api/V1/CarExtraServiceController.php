<?php

namespace App\Controller\Api\V1;

use App\DTO\CarExtraService\CarExtraServiceRequestDTO;
use App\DTO\CarExtraService\CarExtraServiceBulkRequestDTO;
use App\Service\CarExtraServiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/cars/{carId}/extra-services')]
class CarExtraServiceController extends AbstractController
{
    public function __construct(
        private readonly CarExtraServiceService $carExtraServiceService
    ) {
    }

    /**
     * Получить все услуги автомобиля
     */
    #[Route('', name: 'api_car_extra_services_list', methods: ['GET'])]
    public function list(int $carId, Request $request): JsonResponse
    {
        $category = $request->query->get('category');
        $onlyRequired = $request->query->getBoolean('only_required', false);
        $withCustomPrice = $request->query->getBoolean('with_custom_price', false);

        if ($onlyRequired) {
            $services = $this->carExtraServiceService->getRequiredServicesByCarId($carId);
        } elseif ($withCustomPrice) {
            $services = $this->carExtraServiceService->getServicesWithCustomPrice($carId);
        } elseif ($category) {
            $services = $this->carExtraServiceService->getServicesByCategory($carId, $category);
        } else {
            $services = $this->carExtraServiceService->getServicesByCarId($carId);
        }

        return $this->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Получить обязательные услуги автомобиля
     */
    #[Route('/required', name: 'api_car_extra_services_required', methods: ['GET'])]
    public function required(int $carId): JsonResponse
    {
        $services = $this->carExtraServiceService->getRequiredServicesByCarId($carId);

        return $this->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Получить услуги с индивидуальной ценой
     */
    #[Route('/custom-price', name: 'api_car_extra_services_custom_price', methods: ['GET'])]
    public function customPrice(int $carId): JsonResponse
    {
        $services = $this->carExtraServiceService->getServicesWithCustomPrice($carId);

        return $this->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Получить общую сумму обязательных услуг
     */
    #[Route('/required-total', name: 'api_car_extra_services_required_total', methods: ['GET'])]
    public function requiredTotal(int $carId): JsonResponse
    {
        $total = $this->carExtraServiceService->getRequiredServicesTotal($carId);

        return $this->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'formatted' => number_format($total, 2, '.', ' ')
            ]
        ]);
    }

    /**
     * Получить статистику по услугам автомобиля
     */
    #[Route('/statistics', name: 'api_car_extra_services_statistics', methods: ['GET'])]
    public function statistics(int $carId): JsonResponse
    {
        $statistics = $this->carExtraServiceService->getStatisticsForCar($carId);

        return $this->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Проверить наличие услуги у автомобиля
     */
    #[Route('/check/{serviceId}', name: 'api_car_extra_services_check', methods: ['GET'])]
    public function check(int $carId, int $serviceId): JsonResponse
    {
        $hasService = $this->carExtraServiceService->hasService($carId, $serviceId);

        return $this->json([
            'success' => true,
            'has_service' => $hasService
        ]);
    }

    /**
     * Добавить услугу автомобилю
     */
    #[Route('', name: 'api_car_extra_services_add', methods: ['POST'])]
    public function add(int $carId, #[MapRequestPayload] CarExtraServiceRequestDTO $dto): JsonResponse
    {
        try {
            $carService = $this->carExtraServiceService->addService($carId, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Услуга успешно добавлена',
                'data' => $carService
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
     * Добавить несколько услуг автомобилю (массовое добавление)
     */
    #[Route('/bulk', name: 'api_car_extra_services_add_bulk', methods: ['POST'])]
    public function addBulk(int $carId, #[MapRequestPayload] CarExtraServiceBulkRequestDTO $dto): JsonResponse
    {
        try {
            $results = $this->carExtraServiceService->addServicesBulk($carId, $dto->getServices());

            $successCount = count(array_filter($results, fn($r) => $r['success']));
            $failCount = count($results) - $successCount;

            return $this->json([
                'success' => true,
                'message' => sprintf('Добавлено %d услуг, ошибок: %d', $successCount, $failCount),
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
     * Заменить все услуги автомобиля
     */
    #[Route('/replace', name: 'api_car_extra_services_replace', methods: ['PUT'])]
    public function replace(int $carId, #[MapRequestPayload] CarExtraServiceBulkRequestDTO $dto): JsonResponse
    {
        try {
            $results = $this->carExtraServiceService->replaceServices($carId, $dto->getServices());

            return $this->json([
                'success' => true,
                'message' => 'Услуги автомобиля обновлены',
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
     * Получить конкретную услугу автомобиля
     */
    #[Route('/{serviceId}', name: 'api_car_extra_services_show', methods: ['GET'])]
    public function show(int $carId, int $serviceId): JsonResponse
    {
        try {
            $carService = $this->carExtraServiceService->getCarService($carId, $serviceId);

            return $this->json([
                'success' => true,
                'data' => $carService
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Обновить услугу автомобиля
     */
    #[Route('/{serviceId}', name: 'api_car_extra_services_update', methods: ['PUT', 'PATCH'])]
    public function update(
        int $carId,
        int $serviceId,
        #[MapRequestPayload] CarExtraServiceRequestDTO $dto
    ): JsonResponse {
        try {
            $carService = $this->carExtraServiceService->updateService($carId, $serviceId, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Услуга обновлена',
                'data' => $carService
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Удалить услугу у автомобиля
     */
    #[Route('/{serviceId}', name: 'api_car_extra_services_delete', methods: ['DELETE'])]
    public function delete(int $carId, int $serviceId): JsonResponse
    {
        try {
            $this->carExtraServiceService->removeService($carId, $serviceId);

            return $this->json([
                'success' => true,
                'message' => 'Услуга удалена'
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Удалить все услуги автомобиля
     */
    #[Route('', name: 'api_car_extra_services_delete_all', methods: ['DELETE'])]
    public function deleteAll(int $carId): JsonResponse
    {
        $this->carExtraServiceService->removeAllServices($carId);

        return $this->json([
            'success' => true,
            'message' => 'Все услуги автомобиля удалены'
        ]);
    }

    /**
     * Копировать услуги с одного автомобиля на другой
     */
    #[Route('/copy/{sourceCarId}', name: 'api_car_extra_services_copy', methods: ['POST'])]
    public function copy(int $carId, int $sourceCarId): JsonResponse
    {
        try {
            $this->carExtraServiceService->copyServices($sourceCarId, $carId);

            return $this->json([
                'success' => true,
                'message' => 'Услуги успешно скопированы'
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
