<?php

namespace App\Controller\Api\V1;

use App\DTO\Booking\BookingRequestDTO;
use App\Service\BookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/bookings')]
class BookingController extends AbstractController
{
    public function __construct(
        private readonly BookingService $bookingService
    ) {
    }

    /**
     * Получить все бронирования
     */
    #[Route('', name: 'api_bookings_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $withDetails = $request->query->getBoolean('with_details', false);
        $status = $request->query->get('status');
        $userId = $request->query->getInt('user_id', 0);

        if ($userId > 0) {
            $bookings = $this->bookingService->getUserBookings($userId, $withDetails);
        } elseif ($status) {
            $bookings = $this->bookingService->getBookingsByStatus($status, $withDetails);
        } else {
            $bookings = $this->bookingService->getAllBookings($withDetails);
        }

        return $this->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    /**
     * Получить активные бронирования
     */
    #[Route('/active', name: 'api_bookings_active', methods: ['GET'])]
    public function getActive(Request $request): JsonResponse
    {
        $withDetails = $request->query->getBoolean('with_details', true);
        $bookings = $this->bookingService->getActiveBookings($withDetails);

        return $this->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    /**
     * Получить завершенные бронирования
     */
    #[Route('/completed', name: 'api_bookings_completed', methods: ['GET'])]
    public function getCompleted(Request $request): JsonResponse
    {
        $withDetails = $request->query->getBoolean('with_details', true);
        $bookings = $this->bookingService->getCompletedBookings($withDetails);

        return $this->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    /**
     * Получить статистику по бронированиям
     */
    #[Route('/statistics', name: 'api_bookings_statistics', methods: ['GET'])]
    public function getStatistics(): JsonResponse
    {
        $statistics = $this->bookingService->getStatistics();

        return $this->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Получить статистику по месяцам
     */
    #[Route('/statistics/monthly/{year}', name: 'api_bookings_statistics_monthly', methods: ['GET'])]
    public function getMonthlyStatistics(int $year): JsonResponse
    {
        $statistics = $this->bookingService->getMonthlyStatistics($year);

        return $this->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Проверить доступность автомобиля
     */
    #[Route('/check-availability', name: 'api_bookings_check_availability', methods: ['GET'])]
    public function checkAvailability(Request $request): JsonResponse
    {
        $carId = $request->query->getInt('car_id');
        $pickupDate = $request->query->get('pickup_date');
        $dropoffDate = $request->query->get('dropoff_date');

        if (!$carId || !$pickupDate || !$dropoffDate) {
            return $this->json([
                'success' => false,
                'message' => 'Параметры car_id, pickup_date и dropoff_date обязательны'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $isAvailable = $this->bookingService->checkCarAvailability($carId, $pickupDate, $dropoffDate);

            return $this->json([
                'success' => true,
                'data' => [
                    'available' => $isAvailable
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Получить доступные автомобили на даты
     */
    #[Route('/available-cars', name: 'api_bookings_available_cars', methods: ['GET'])]
    public function getAvailableCars(Request $request): JsonResponse
    {
        $pickupDate = $request->query->get('pickup_date');
        $dropoffDate = $request->query->get('dropoff_date');

        if (!$pickupDate || !$dropoffDate) {
            return $this->json([
                'success' => false,
                'message' => 'Параметры pickup_date и dropoff_date обязательны'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $cars = $this->bookingService->getAvailableCars($pickupDate, $dropoffDate);

            return $this->json([
                'success' => true,
                'data' => $cars
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Получить бронирование по ID
     */
    #[Route('/{id}', name: 'api_bookings_show', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        $withDetails = $request->query->getBoolean('with_details', true);

        try {
            $booking = $this->bookingService->getBookingById($id, $withDetails);

            return $this->json([
                'success' => true,
                'data' => $booking
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Получить бронирование по номеру
     */
    #[Route('/number/{number}', name: 'api_bookings_by_number', methods: ['GET'])]
    public function getByNumber(string $number, Request $request): JsonResponse
    {
        $withDetails = $request->query->getBoolean('with_details', true);

        try {
            $booking = $this->bookingService->getBookingByNumber($number, $withDetails);

            return $this->json([
                'success' => true,
                'data' => $booking
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Создать бронирование
     */
    #[Route('', name: 'api_bookings_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] BookingRequestDTO $dto): JsonResponse
    {
        try {
            $booking = $this->bookingService->createBooking($dto);

            return $this->json([
                'success' => true,
                'message' => 'Бронирование успешно создано',
                'data' => $booking
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
     * Обновить статус бронирования
     */
    #[Route('/{id}/status', name: 'api_bookings_update_status', methods: ['PATCH'])]
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
            $booking = $this->bookingService->updateStatus($id, $status);

            return $this->json([
                'success' => true,
                'message' => 'Статус бронирования обновлен',
                'data' => $booking
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Отменить бронирование
     */
    #[Route('/{id}/cancel', name: 'api_bookings_cancel', methods: ['PATCH'])]
    public function cancel(int $id): JsonResponse
    {
        try {
            $booking = $this->bookingService->cancelBooking($id);

            return $this->json([
                'success' => true,
                'message' => 'Бронирование успешно отменено',
                'data' => $booking
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
     * Поиск бронирований
     */
    #[Route('/search', name: 'api_bookings_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $withDetails = $request->query->getBoolean('with_details', true);

        $criteria = [];
        if ($request->query->has('booking_number')) {
            $criteria['bookingNumber'] = $request->query->get('booking_number');
        }
        if ($request->query->has('user_name')) {
            $criteria['userName'] = $request->query->get('user_name');
        }
        if ($request->query->has('user_email')) {
            $criteria['userEmail'] = $request->query->get('user_email');
        }
        if ($request->query->has('status')) {
            $criteria['status'] = $request->query->get('status');
        }
        if ($request->query->has('start_date')) {
            $criteria['startDate'] = $request->query->get('start_date');
        }
        if ($request->query->has('end_date')) {
            $criteria['endDate'] = $request->query->get('end_date');
        }
        if ($request->query->has('car_id')) {
            $criteria['carId'] = $request->query->getInt('car_id');
        }

        $bookings = $this->bookingService->searchBookings($criteria, $withDetails);

        return $this->json([
            'success' => true,
            'data' => $bookings
        ]);
    }
}
