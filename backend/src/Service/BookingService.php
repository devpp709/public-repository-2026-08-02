<?php

namespace App\Service;

use App\DTO\Booking\BookingRequestDTO;
use App\DTO\Booking\BookingResponseDTO;
use App\DTO\Booking\BookingStatisticsDTO;
use App\Entity\Booking;
use App\Entity\BookingItem;
use App\Entity\BookingExtra;
use App\Entity\Car;
use App\Entity\ExtraService;
use App\Entity\Location;
use App\Entity\User;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BookingRepository $bookingRepository
    ) {
    }

    /**
     * Получить все бронирования
     */
    public function getAllBookings(bool $withDetails = false): array
    {
        $bookings = $this->bookingRepository->findBy([], ['createdAt' => 'DESC']);
        return BookingResponseDTO::fromEntities($bookings, $withDetails);
    }

    /**
     * Получить бронирование по ID
     */
    public function getBookingById(int $id, bool $withDetails = true): BookingResponseDTO
    {
        $booking = $this->findBookingOrFail($id);
        return BookingResponseDTO::fromEntity($booking, $withDetails);
    }

    /**
     * Получить бронирование по номеру
     */
    public function getBookingByNumber(string $number, bool $withDetails = true): BookingResponseDTO
    {
        $booking = $this->bookingRepository->findByBookingNumber($number);
        if (!$booking) {
            throw new NotFoundHttpException(sprintf('Бронирование с номером %s не найдено', $number));
        }
        return BookingResponseDTO::fromEntity($booking, $withDetails);
    }

    /**
     * Создать бронирование
     */
    public function createBooking(BookingRequestDTO $dto): BookingResponseDTO
    {
        $user = $this->findUserOrFail($dto->userId);
        $pickupLocation = $dto->pickupLocationId ? $this->findLocationOrFail($dto->pickupLocationId) : null;
        $dropoffLocation = $dto->dropoffLocationId ? $this->findLocationOrFail($dto->dropoffLocationId) : null;

        $booking = new Booking();
        $booking->setUser($user);
        $booking->setPickupLocation($pickupLocation);
        $booking->setDropoffLocation($dropoffLocation);
        $booking->setPickupDate(new \DateTime($dto->pickupDate));
        $booking->setPickupTime(new \DateTime($dto->pickupTime));
        $booking->setDropoffDate(new \DateTime($dto->dropoffDate));
        $booking->setDropoffTime(new \DateTime($dto->dropoffTime));
        $booking->setNotes($dto->notes);

        // Добавляем автомобили в бронирование
        $totalSecurityDeposit = 0;
        foreach ($dto->items as $itemDto) {
            $car = $this->findCarOrFail($itemDto->carId);

            // Проверяем доступность автомобиля
            if (!$this->bookingRepository->isCarAvailable(
                $itemDto->carId,
                $booking->getPickupDate(),
                $booking->getDropoffDate()
            )) {
                throw new \InvalidArgumentException(
                    sprintf('Автомобиль "%s" недоступен на выбранные даты', $car->getFullName())
                );
            }

            $bookingItem = new BookingItem();
            $bookingItem->setCar($car);
            $bookingItem->setDailyRate((string) $itemDto->dailyRate);
            $bookingItem->setHourlyRate($itemDto->hourlyRate ? (string) $itemDto->hourlyRate : null);
            $bookingItem->setTotalPrice((string) $itemDto->totalPrice);

            $booking->addBookingItem($bookingItem);

            $totalSecurityDeposit += (float) $car->getSecurityDeposit();
        }

        // Добавляем дополнительные услуги
        foreach ($dto->extras as $extraDto) {
            $extraService = $this->findExtraServiceOrFail($extraDto->extraServiceId);

            $bookingExtra = new BookingExtra();
            $bookingExtra->setExtraService($extraService);
            $bookingExtra->setQuantity($extraDto->quantity);
            $bookingExtra->setPricePerUnit((string) $extraDto->pricePerUnit);
            $bookingExtra->setTotalPrice((string) $extraDto->totalPrice);

            $booking->addBookingExtra($bookingExtra);
        }

        // Устанавливаем страховой депозит
        $booking->setSecurityDeposit((string) $totalSecurityDeposit);

        // Пересчитываем итоговые суммы
        $booking->calculateTotals();

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return BookingResponseDTO::fromEntity($booking, true);
    }

    /**
     * Обновить статус бронирования
     */
    public function updateStatus(int $id, string $status): BookingResponseDTO
    {
        $booking = $this->findBookingOrFail($id);
        $booking->setStatus($status);

        // Если бронирование отменено, освобождаем автомобили
        if ($status === 'cancelled') {
            // Здесь можно добавить логику освобождения автомобилей
        }

        $this->entityManager->flush();

        return BookingResponseDTO::fromEntity($booking, true);
    }

    /**
     * Отменить бронирование
     */
    public function cancelBooking(int $id): BookingResponseDTO
    {
        $booking = $this->findBookingOrFail($id);

        if (!$booking->canBeCancelled()) {
            throw new \RuntimeException(
                sprintf('Невозможно отменить бронирование со статусом "%s"', $booking->getStatusLabel())
            );
        }

        $booking->setStatus('cancelled');
        $this->entityManager->flush();

        return BookingResponseDTO::fromEntity($booking, true);
    }

    /**
     * Получить бронирования пользователя
     */
    public function getUserBookings(int $userId, bool $withDetails = false): array
    {
        $bookings = $this->bookingRepository->findByUser($userId);
        return BookingResponseDTO::fromEntities($bookings, $withDetails);
    }

    /**
     * Получить бронирования по статусу
     */
    public function getBookingsByStatus(string $status, bool $withDetails = false): array
    {
        $bookings = $this->bookingRepository->findByStatus($status);
        return BookingResponseDTO::fromEntities($bookings, $withDetails);
    }

    /**
     * Получить активные бронирования
     */
    public function getActiveBookings(bool $withDetails = false): array
    {
        $bookings = $this->bookingRepository->findActive();
        return BookingResponseDTO::fromEntities($bookings, $withDetails);
    }

    /**
     * Получить завершенные бронирования
     */
    public function getCompletedBookings(bool $withDetails = false): array
    {
        $bookings = $this->bookingRepository->findCompleted();
        return BookingResponseDTO::fromEntities($bookings, $withDetails);
    }

    /**
     * Поиск бронирований
     */
    public function searchBookings(array $criteria, bool $withDetails = false): array
    {
        $bookings = $this->bookingRepository->search($criteria);
        return BookingResponseDTO::fromEntities($bookings, $withDetails);
    }

    /**
     * Получить статистику по бронированиям
     */
    public function getStatistics(): array
    {
        $statistics = $this->bookingRepository->getStatistics();
        $dailyStats = $this->bookingRepository->getDailyStats(30);
        $topUsers = $this->bookingRepository->getTopUsers(5);

        return [
            'general' => BookingStatisticsDTO::fromArray($statistics),
            'daily' => $dailyStats,
            'top_users' => $topUsers
        ];
    }

    /**
     * Получить статистику по месяцам
     */
    public function getMonthlyStatistics(int $year): array
    {
        return $this->bookingRepository->getMonthlyStatistics($year);
    }

    /**
     * Проверить доступность автомобиля на даты
     */
    public function checkCarAvailability(int $carId, string $pickupDate, string $dropoffDate): bool
    {
        $pickup = new \DateTime($pickupDate);
        $dropoff = new \DateTime($dropoffDate);

        return $this->bookingRepository->isCarAvailable($carId, $pickup, $dropoff);
    }

    /**
     * Получить доступные автомобили на даты
     */
    public function getAvailableCars(string $pickupDate, string $dropoffDate): array
    {
        $pickup = new \DateTime($pickupDate);
        $dropoff = new \DateTime($dropoffDate);

        $cars = $this->entityManager->getRepository(Car::class)->findAvailable();
        $availableCars = [];

        foreach ($cars as $car) {
            if ($this->bookingRepository->isCarAvailable($car->getId(), $pickup, $dropoff)) {
                $availableCars[] = $car;
            }
        }

        return $availableCars;
    }

    /**
     * Найти бронирование или выбросить исключение
     */
    private function findBookingOrFail(int $id): Booking
    {
        $booking = $this->bookingRepository->find($id);
        if (!$booking) {
            throw new NotFoundHttpException(sprintf('Бронирование с ID %d не найдено', $id));
        }
        return $booking;
    }

    /**
     * Найти пользователя или выбросить исключение
     */
    private function findUserOrFail(int $id): User
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('Пользователь с ID %d не найден', $id));
        }
        return $user;
    }

    /**
     * Найти локацию или выбросить исключение
     */
    private function findLocationOrFail(int $id): Location
    {
        $location = $this->entityManager->getRepository(Location::class)->find($id);
        if (!$location) {
            throw new NotFoundHttpException(sprintf('Локация с ID %d не найдена', $id));
        }
        return $location;
    }

    /**
     * Найти автомобиль или выбросить исключение
     */
    private function findCarOrFail(int $id): Car
    {
        $car = $this->entityManager->getRepository(Car::class)->find($id);
        if (!$car) {
            throw new NotFoundHttpException(sprintf('Автомобиль с ID %d не найден', $id));
        }
        return $car;
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
