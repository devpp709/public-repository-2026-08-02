<?php

namespace App\Controller\Api\V1;

use App\DTO\Booking\BookingExtraRequestDTO;
use App\Service\BookingExtraService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/bookings/{bookingId}/extras')]
class BookingExtraController extends AbstractController
{
    public function __construct(
        private readonly BookingExtraService $bookingExtraService
    ) {
    }

    /**
     * Получить все услуги в бронировании
     */
    #[Route('', name: 'api_booking_extras_list', methods: ['GET'])]
    public function list(int $bookingId): JsonResponse
    {
        $extras = $this->bookingExtraService->getExtrasByBookingId($bookingId);

        return $this->json([
            'success' => true,
            'data' => $extras
        ]);
    }

    /**
     * Получить общую сумму дополнительных услуг
     */
    #[Route('/total', name: 'api_booking_extras_total', methods: ['GET'])]
    public function getTotal(int $bookingId): JsonResponse
    {
        $total = $this->bookingExtraService->getTotalForBooking($bookingId);

        return $this->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'formatted' => number_format($total, 2, '.', ' ')
            ]
        ]);
    }

    /**
     * Добавить услугу в бронирование
     */
    #[Route('', name: 'api_booking_extras_add', methods: ['POST'])]
    public function add(
        int $bookingId,
        #[MapRequestPayload] BookingExtraRequestDTO $dto
    ): JsonResponse {
        try {
            $extra = $this->bookingExtraService->addExtra($bookingId, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Услуга добавлена в бронирование',
                'data' => $extra
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
     * Получить услугу по ID
     */
    #[Route('/{id}', name: 'api_booking_extras_show', methods: ['GET'])]
    public function show(int $bookingId, int $id): JsonResponse
    {
        try {
            $extra = $this->bookingExtraService->getExtraById($id);

            // Проверяем, что услуга принадлежит этому бронированию
            if ($extra->bookingId !== $bookingId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Услуга не принадлежит этому бронированию'
                ], Response::HTTP_FORBIDDEN);
            }

            return $this->json([
                'success' => true,
                'data' => $extra
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Обновить услугу в бронировании
     */
    #[Route('/{id}', name: 'api_booking_extras_update', methods: ['PUT', 'PATCH'])]
    public function update(
        int $bookingId,
        int $id,
        #[MapRequestPayload] BookingExtraRequestDTO $dto
    ): JsonResponse {
        try {
            $extra = $this->bookingExtraService->updateExtra($id, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Услуга обновлена',
                'data' => $extra
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Удалить услугу из бронирования
     */
    #[Route('/{id}', name: 'api_booking_extras_delete', methods: ['DELETE'])]
    public function delete(int $bookingId, int $id): JsonResponse
    {
        try {
            $this->bookingExtraService->removeExtra($id);

            return $this->json([
                'success' => true,
                'message' => 'Услуга удалена из бронирования'
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Удалить все услуги из бронирования
     */
    #[Route('', name: 'api_booking_extras_delete_all', methods: ['DELETE'])]
    public function deleteAll(int $bookingId): JsonResponse
    {
        $this->bookingExtraService->removeAllExtras($bookingId);

        return $this->json([
            'success' => true,
            'message' => 'Все услуги удалены из бронирования'
        ]);
    }
}
