<?php

namespace App\Controller\Api\V1;

use App\DTO\CarRentalHistory\CarRentalHistoryRequestDTO;
use App\Service\CarRentalHistoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1')]
class CarRentalHistoryController extends AbstractController
{
    public function __construct(
        private readonly CarRentalHistoryService $carRentalHistoryService
    ) {
    }

    /**
     * Получить историю аренды автомобиля
     */
    #[Route('/cars/{carId}/history', name: 'api_car_history_list', methods: ['GET'])]
    public function getCarHistory(int $carId): JsonResponse
    {
        $history = $this->carRentalHistoryService->getHistoryByCarId($carId);

        return $this->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Получить статистику по автомобилю
     */
    #[Route('/cars/{carId}/history/statistics', name: 'api_car_history_statistics', methods: ['GET'])]
    public function getCarStatistics(int $carId): JsonResponse
    {
        $statistics = $this->carRentalHistoryService->getStatisticsForCar($carId);

        return $this->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Получить глобальную статистику
     */
    #[Route('/history/statistics', name: 'api_history_global_statistics', methods: ['GET'])]
    public function getGlobalStatistics(): JsonResponse
    {
        $statistics = $this->carRentalHistoryService->getGlobalStatistics();

        return $this->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Получить топ автомобилей по пробегу
     */
    #[Route('/history/top-by-distance', name: 'api_history_top_distance', methods: ['GET'])]
    public function getTopByDistance(Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', 10);
        $top = $this->carRentalHistoryService->getTopByDistance($limit);

        return $this->json([
            'success' => true,
            'data' => $top
        ]);
    }

    /**
     * Получить топ автомобилей по количеству аренд
     */
    #[Route('/history/top-by-rentals', name: 'api_history_top_rentals', methods: ['GET'])]
    public function getTopByRentals(Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', 10);
        $top = $this->carRentalHistoryService->getTopByRentals($limit);

        return $this->json([
            'success' => true,
            'data' => $top
        ]);
    }

    /**
     * Получить статистику повреждений по месяцам
     */
    #[Route('/history/damages/{year}', name: 'api_history_damages', methods: ['GET'])]
    public function getDamagesByMonth(int $year): JsonResponse
    {
        $damages = $this->carRentalHistoryService->getDamagesByMonth($year);

        return $this->json([
            'success' => true,
            'data' => $damages
        ]);
    }

    /**
     * Проверить, арендован ли автомобиль в период
     */
    #[Route('/cars/{carId}/history/check-availability', name: 'api_car_history_check', methods: ['GET'])]
    public function checkAvailability(int $carId, Request $request): JsonResponse
    {
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');

        if (!$startDate || !$endDate) {
            return $this->json([
                'success' => false,
                'message' => 'Параметры start_date и end_date обязательны'
            ], Response::HTTP_BAD_REQUEST);
        }

        $isRented = $this->carRentalHistoryService->isCarRentedInPeriod($carId, $startDate, $endDate);

        return $this->json([
            'success' => true,
            'data' => [
                'available' => !$isRented
            ]
        ]);
    }

    /**
     * Создать запись истории аренды
     */
    #[Route('/history', name: 'api_history_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CarRentalHistoryRequestDTO $dto): JsonResponse
    {
        try {
            $history = $this->carRentalHistoryService->createHistory($dto);

            return $this->json([
                'success' => true,
                'message' => 'Запись истории создана',
                'data' => $history
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
     * Завершить аренду
     */
    #[Route('/history/{id}/complete', name: 'api_history_complete', methods: ['PATCH'])]
    public function complete(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $history = $this->carRentalHistoryService->completeRental($id, $data);

            return $this->json([
                'success' => true,
                'message' => 'Аренда завершена',
                'data' => $history
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Получить запись истории по ID
     */
    #[Route('/history/{id}', name: 'api_history_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $history = $this->carRentalHistoryService->getHistoryById($id);

            return $this->json([
                'success' => true,
                'data' => $history
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Получить историю по бронированию
     */
    #[Route('/bookings/{bookingId}/history', name: 'api_booking_history_list', methods: ['GET'])]
    public function getBookingHistory(int $bookingId): JsonResponse
    {
        $history = $this->carRentalHistoryService->getHistoryByBookingId($bookingId);

        return $this->json([
            'success' => true,
            'data' => $history
        ]);
    }
}
