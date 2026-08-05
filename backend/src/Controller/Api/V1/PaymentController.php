<?php

namespace App\Controller\Api\V1;

use App\DTO\Payment\PaymentRequestDTO;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1')]
class PaymentController extends AbstractController
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {
    }

    /**
     * Получить все платежи
     */
    #[Route('/payments', name: 'api_payments_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $payments = $this->paymentService->getAllPayments();

        return $this->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Получить платежи по бронированию
     */
    #[Route('/bookings/{bookingId}/payments', name: 'api_booking_payments_list', methods: ['GET'])]
    public function getByBooking(int $bookingId): JsonResponse
    {
        $payments = $this->paymentService->getPaymentsByBooking($bookingId);

        return $this->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Проверить наличие оплаты для бронирования
     */
    #[Route('/bookings/{bookingId}/payments/check', name: 'api_booking_payments_check', methods: ['GET'])]
    public function checkPayment(int $bookingId): JsonResponse
    {
        $hasPayment = $this->paymentService->hasSuccessfulPayment($bookingId);
        $totalPaid = $this->paymentService->getTotalPaidForBooking($bookingId);

        return $this->json([
            'success' => true,
            'data' => [
                'has_payment' => $hasPayment,
                'total_paid' => $totalPaid
            ]
        ]);
    }

    /**
     * Создать платеж для бронирования
     */
    #[Route('/bookings/{bookingId}/payments', name: 'api_booking_payments_create', methods: ['POST'])]
    public function create(
        int $bookingId,
        #[MapRequestPayload] PaymentRequestDTO $dto
    ): JsonResponse {
        try {
            $payment = $this->paymentService->createPayment($bookingId, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Платеж создан',
                'data' => $payment
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
     * Получить платеж по ID
     */
    #[Route('/payments/{id}', name: 'api_payments_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->getPaymentById($id);

            return $this->json([
                'success' => true,
                'data' => $payment
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Обновить статус платежа
     */
    #[Route('/payments/{id}/status', name: 'api_payments_update_status', methods: ['PATCH'])]
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
            $payment = $this->paymentService->updatePaymentStatus($id, $status);

            return $this->json([
                'success' => true,
                'message' => 'Статус платежа обновлен',
                'data' => $payment
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Отменить платеж
     */
    #[Route('/payments/{id}/cancel', name: 'api_payments_cancel', methods: ['PATCH'])]
    public function cancel(int $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->cancelPayment($id);

            return $this->json([
                'success' => true,
                'message' => 'Платеж отменен',
                'data' => $payment
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
     * Возврат платежа
     */
    #[Route('/payments/{id}/refund', name: 'api_payments_refund', methods: ['PATCH'])]
    public function refund(int $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->refundPayment($id);

            return $this->json([
                'success' => true,
                'message' => 'Возврат платежа выполнен',
                'data' => $payment
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
     * Получить статистику по платежам
     */
    #[Route('/payments/statistics', name: 'api_payments_statistics', methods: ['GET'])]
    public function getStatistics(): JsonResponse
    {
        $statistics = $this->paymentService->getStatistics();

        return $this->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Получить статистику по месяцам
     */
    #[Route('/payments/statistics/monthly/{year}', name: 'api_payments_statistics_monthly', methods: ['GET'])]
    public function getMonthlyStatistics(int $year): JsonResponse
    {
        $statistics = $this->paymentService->getMonthlyStatistics($year);

        return $this->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Получить платежи за период
     */
    #[Route('/payments/by-period', name: 'api_payments_by_period', methods: ['GET'])]
    public function getByPeriod(Request $request): JsonResponse
    {
        $start = $request->query->get('start_date');
        $end = $request->query->get('end_date');

        if (!$start || !$end) {
            return $this->json([
                'success' => false,
                'message' => 'Параметры start_date и end_date обязательны'
            ], Response::HTTP_BAD_REQUEST);
        }

        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);

        $payments = $this->paymentService->getPaymentsByDateRange($startDate, $endDate);
        $total = $this->paymentService->getTotalByDateRange($startDate, $endDate);

        return $this->json([
            'success' => true,
            'data' => [
                'payments' => $payments,
                'total' => $total
            ]
        ]);
    }
}
