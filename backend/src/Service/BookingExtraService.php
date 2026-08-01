<?php

namespace App\Service;

use App\DTO\Booking\BookingExtraRequestDTO;
use App\DTO\Booking\BookingExtraResponseDTO;
use App\Entity\Booking;
use App\Entity\BookingExtra;
use App\Entity\ExtraService;
use App\Repository\BookingExtraRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingExtraService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BookingExtraRepository $bookingExtraRepository
    ) {
    }

    /**
     * Получить все услуги для бронирования
     */
    public function getExtrasByBookingId(int $bookingId): array
    {
        $extras = $this->bookingExtraRepository->findByBookingId($bookingId);
        return BookingExtraResponseDTO::fromEntities($extras);
    }

    /**
     * Получить услугу по ID
     */
    public function getExtraById(int $id): BookingExtraResponseDTO
    {
        $extra = $this->findExtraOrFail($id);
        return BookingExtraResponseDTO::fromEntity($extra);
    }

    /**
     * Добавить услугу в бронирование
     */
    public function addExtra(int $bookingId, BookingExtraRequestDTO $dto): BookingExtraResponseDTO
    {
        $booking = $this->findBookingOrFail($bookingId);
        $extraService = $this->findExtraServiceOrFail($dto->extraServiceId);

        // Проверяем, не добавлена ли уже такая услуга
        $existing = $this->bookingExtraRepository->findByBookingAndService($bookingId, $dto->extraServiceId);
        if ($existing) {
            throw new \InvalidArgumentException(
                sprintf('Услуга "%s" уже добавлена в это бронирование', $extraService->getName())
            );
        }

        $bookingExtra = new BookingExtra();
        $bookingExtra->setBooking($booking);
        $bookingExtra->setExtraService($extraService);
        $bookingExtra->setQuantity($dto->quantity ?? 1);
        $bookingExtra->setPricePerUnit((string) $dto->pricePerUnit);
        $bookingExtra->calculateTotalPrice();

        $this->entityManager->persist($bookingExtra);
        $this->entityManager->flush();

        // Обновляем общую сумму бронирования
        $this->updateBookingTotal($bookingId);

        return BookingExtraResponseDTO::fromEntity($bookingExtra);
    }

    /**
     * Обновить услугу в бронировании
     */
    public function updateExtra(int $id, BookingExtraRequestDTO $dto): BookingExtraResponseDTO
    {
        $bookingExtra = $this->findExtraOrFail($id);

        if ($dto->quantity !== null) {
            $bookingExtra->setQuantity($dto->quantity);
        }
        if ($dto->pricePerUnit !== null) {
            $bookingExtra->setPricePerUnit((string) $dto->pricePerUnit);
        }

        $bookingExtra->calculateTotalPrice();
        $this->entityManager->flush();

        // Обновляем общую сумму бронирования
        $this->updateBookingTotal($bookingExtra->getBooking()->getId());

        return BookingExtraResponseDTO::fromEntity($bookingExtra);
    }

    /**
     * Удалить услугу из бронирования
     */
    public function removeExtra(int $id): void
    {
        $bookingExtra = $this->findExtraOrFail($id);
        $bookingId = $bookingExtra->getBooking()->getId();

        $this->entityManager->remove($bookingExtra);
        $this->entityManager->flush();

        // Обновляем общую сумму бронирования
        $this->updateBookingTotal($bookingId);
    }

    /**
     * Удалить все услуги из бронирования
     */
    public function removeAllExtras(int $bookingId): void
    {
        $this->bookingExtraRepository->deleteByBookingId($bookingId);

        // Обновляем общую сумму бронирования
        $this->updateBookingTotal($bookingId);
    }

    /**
     * Получить общую сумму дополнительных услуг
     */
    public function getTotalForBooking(int $bookingId): float
    {
        return $this->bookingExtraRepository->getTotalForBooking($bookingId);
    }

    /**
     * Получить популярные услуги
     */
    public function getPopularServices(int $limit = 10): array
    {
        return $this->bookingExtraRepository->getPopularServices($limit);
    }

    /**
     * Обновить общую сумму бронирования
     */
    private function updateBookingTotal(int $bookingId): void
    {
        $booking = $this->findBookingOrFail($bookingId);

        // Пересчитываем общую сумму
        $booking->calculateTotals();
        $this->entityManager->flush();
    }

    /**
     * Найти услугу в бронировании или выбросить исключение
     */
    private function findExtraOrFail(int $id): BookingExtra
    {
        $extra = $this->bookingExtraRepository->find($id);
        if (!$extra) {
            throw new NotFoundHttpException(sprintf('Услуга в бронировании с ID %d не найдена', $id));
        }
        return $extra;
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

    /**
     * Найти дополнительную услугу или выбросить исключение
     */
    private function findExtraServiceOrFail(int $id): ExtraService
    {
        $service = $this->entityManager->getRepository(ExtraService::class)->find($id);
        if (!$service) {
            throw new NotFoundHttpException(sprintf('Дополнительная услуга с ID %d не найдена', $id));
        }
        return $service;
    }
}
