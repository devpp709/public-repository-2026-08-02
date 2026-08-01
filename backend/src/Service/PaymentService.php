<?php

namespace App\Service;

use App\DTO\Payment\PaymentRequestDTO;
use App\DTO\Payment\PaymentResponseDTO;
use App\DTO\Payment\PaymentStatisticsDTO;
use App\Entity\Booking;
use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PaymentRepository $paymentRepository
    ) {
    }

    /**
     * Получить все платежи
     */
    public function getAllPayments(): array
    {
        $payments = $this->paymentRepository->findBy([], ['createdAt' => 'DESC']);
        return PaymentResponseDTO::fromEntities($payments);
    }

    /**
     * Получить платеж по ID
     */
    public function getPaymentById(int $id): PaymentResponseDTO
    {
        $payment = $this->findPaymentOrFail($id);
        return PaymentResponseDTO::fromEntity($payment);
    }

    /**
     * Получить платежи по бронированию
     */
    public function getPaymentsByBooking(int $bookingId): array
    {
        $payments = $this->paymentRepository->findByBookingId($bookingId);
        return PaymentResponseDTO::fromEntities($payments);
    }

    /**
     * Создать платеж
     */
    public function createPayment(int $bookingId, PaymentRequestDTO $dto): PaymentResponseDTO
    {
        $booking = $this->findBookingOrFail($bookingId);

        // Проверяем, есть ли уже успешный платеж по этому бронированию
        if ($this->paymentRepository->hasSuccessfulPayment($bookingId)) {
            throw new \InvalidArgumentException('Для этого бронирования уже есть успешный платеж');
        }

        $payment = new Payment();
        $payment->setBooking($booking);
        $payment->setAmount((string) $dto->amount);
        $payment->setPaymentMethod($dto->paymentMethod);
        $payment->setStatus($dto->status ?? 'pending');
        $payment->setTransactionId($dto->transactionId);

        if ($dto->paymentDate) {
            $payment->setPaymentDate(new \DateTime($dto->paymentDate));
        }

        // Если статус 'paid', устанавливаем дату оплаты
        if ($payment->getStatus() === 'paid') {
            $payment->setPaymentDate(new \DateTime());
        }

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        // Если платеж успешный, обновляем статус бронирования
        if ($payment->isPaid()) {
            $booking->setStatus('confirmed');
            $this->entityManager->flush();
        }

        return PaymentResponseDTO::fromEntity($payment);
    }

    /**
     * Обновить статус платежа
     */
    public function updatePaymentStatus(int $id, string $status): PaymentResponseDTO
    {
        $payment = $this->findPaymentOrFail($id);
        $oldStatus = $payment->getStatus();

        $payment->setStatus($status);

        // Если статус становится 'paid', устанавливаем дату оплаты
        if ($status === 'paid' && $oldStatus !== 'paid') {
            $payment->setPaymentDate(new \DateTime());

            // Обновляем статус бронирования
            $booking = $payment->getBooking();
            if ($booking && $booking->getStatus() === 'pending') {
                $booking->setStatus('confirmed');
            }
        }

        // Если статус становится 'refunded', создаем запись о возврате
        if ($status === 'refunded' && $oldStatus === 'paid') {
            // Здесь можно добавить логику возврата средств
        }

        $this->entityManager->flush();

        return PaymentResponseDTO::fromEntity($payment);
    }

    /**
     * Отменить платеж
     */
    public function cancelPayment(int $id): PaymentResponseDTO
    {
        $payment = $this->findPaymentOrFail($id);

        if (!$payment->canBeCancelled()) {
            throw new \RuntimeException(
                sprintf('Невозможно отменить платеж со статусом "%s"', $payment->getStatusLabel())
            );
        }

        $payment->setStatus('cancelled');
        $this->entityManager->flush();

        return PaymentResponseDTO::fromEntity($payment);
    }

    /**
     * Возврат платежа
     */
    public function refundPayment(int $id): PaymentResponseDTO
    {
        $payment = $this->findPaymentOrFail($id);

        if (!$payment->canBeRefunded()) {
            throw new \RuntimeException(
                sprintf('Невозможно выполнить возврат для платежа со статусом "%s"', $payment->getStatusLabel())
            );
        }

        $payment->setStatus('refunded');
        $this->entityManager->flush();

        return PaymentResponseDTO::fromEntity($payment);
    }

    /**
     * Получить успешные платежи за период
     */
    public function getPaymentsByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $payments = $this->paymentRepository->findByDateRange($start, $end);
        return PaymentResponseDTO::fromEntities($payments);
    }

    /**
     * Получить общую сумму платежей за период
     */
    public function getTotalByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): float
    {
        return $this->paymentRepository->getTotalByDateRange($start, $end);
    }

    /**
     * Получить статистику по платежам
     */
    public function getStatistics(): array
    {
        $statistics = $this->paymentRepository->getStatistics();
        $methodStats = $this->paymentRepository->getPaymentMethodStatistics();

        return [
            'general' => PaymentStatisticsDTO::fromArray($statistics),
            'by_method' => $methodStats
        ];
    }

    /**
     * Получить статистику по месяцам
     */
    public function getMonthlyStatistics(int $year): array
    {
        return $this->paymentRepository->getMonthlyStatistics($year);
    }

    /**
     * Проверить наличие успешного платежа для бронирования
     */
    public function hasSuccessfulPayment(int $bookingId): bool
    {
        return $this->paymentRepository->hasSuccessfulPayment($bookingId);
    }

    /**
     * Получить общую сумму оплат по бронированию
     */
    public function getTotalPaidForBooking(int $bookingId): float
    {
        return $this->paymentRepository->getTotalPaidForBooking($bookingId);
    }

    /**
     * Найти платеж или выбросить исключение
     */
    private function findPaymentOrFail(int $id): Payment
    {
        $payment = $this->paymentRepository->find($id);
        if (!$payment) {
            throw new NotFoundHttpException(sprintf('Платеж с ID %d не найден', $id));
        }
        return $payment;
    }

    /**
     * Найти бронирование или выбросить исключение
     */
    private function findBookingOrFail(int $id): Booking
    {
        $booking = $this->entityManager->getRepository(Booking::class)->find($id);
        if (!$booking) {
            throw new NotFoundHttpException(sprintf('Бронирование с ID %d не найдено', $id));
        }
        return $booking;
    }
}
