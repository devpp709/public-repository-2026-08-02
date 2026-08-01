<?php

namespace App\Service;

use App\DTO\CarRentalHistory\CarRentalHistoryRequestDTO;
use App\DTO\CarRentalHistory\CarRentalHistoryResponseDTO;
use App\DTO\CarRentalHistory\CarRentalHistoryStatisticsDTO;
use App\Entity\Booking;
use App\Entity\Car;
use App\Entity\CarRentalHistory;
use App\Repository\CarRentalHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarRentalHistoryService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CarRentalHistoryRepository $carRentalHistoryRepository
    ) {
    }

    /**
     * Получить историю для автомобиля
     */
    public function getHistoryByCarId(int $carId): array
    {
        $history = $this->carRentalHistoryRepository->findByCarId($carId);
        return CarRentalHistoryResponseDTO::fromEntities($history);
    }

    /**
     * Получить историю по бронированию
     */
    public function getHistoryByBookingId(int $bookingId): array
    {
        $history = $this->carRentalHistoryRepository->findByBookingId($bookingId);
        return CarRentalHistoryResponseDTO::fromEntities($history);
    }

    /**
     * Получить запись истории по ID
     */
    public function getHistoryById(int $id): CarRentalHistoryResponseDTO
    {
        $history = $this->findHistoryOrFail($id);
        return CarRentalHistoryResponseDTO::fromEntity($history);
    }

    /**
     * Создать запись истории аренды
     */
    public function createHistory(CarRentalHistoryRequestDTO $dto): CarRentalHistoryResponseDTO
    {
        $car = $this->findCarOrFail($dto->carId);
        $booking = $dto->bookingId ? $this->findBookingOrFail($dto->bookingId) : null;

        // Проверяем, не пересекается ли аренда с существующими
        if ($this->carRentalHistoryRepository->isCarRentedInPeriod(
            $dto->carId,
            new \DateTime($dto->startDate),
            new \DateTime($dto->endDate)
        )) {
            throw new \InvalidArgumentException('Автомобиль уже арендован в указанный период');
        }

        $history = new CarRentalHistory();
        $history->setCar($car);
        $history->setBooking($booking);
        $history->setStartMileage($dto->startMileage);
        $history->setEndMileage($dto->endMileage);
        $history->setStartDate(new \DateTime($dto->startDate));
        $history->setEndDate(new \DateTime($dto->endDate));
        $history->setConditionBefore($dto->conditionBefore);
        $history->setConditionAfter($dto->conditionAfter);
        $history->setNotes($dto->notes);

        // Рассчитываем пробег и длительность
        $history->calculateTotals();

        // Обновляем пробег автомобиля
        if ($dto->endMileage !== null) {
            $car->setMileage($dto->endMileage);
        }

        $this->entityManager->persist($history);
        $this->entityManager->flush();

        return CarRentalHistoryResponseDTO::fromEntity($history);
    }

    /**
     * Завершить аренду (обновить данные по окончании)
     */
    public function completeRental(int $id, array $data): CarRentalHistoryResponseDTO
    {
        $history = $this->findHistoryOrFail($id);

        if ($data['endMileage'] !== null) {
            $history->setEndMileage($data['endMileage']);
        }
        if ($data['conditionAfter'] !== null) {
            $history->setConditionAfter($data['conditionAfter']);
        }
        if ($data['notes'] !== null) {
            $history->setNotes($data['notes']);
        }

        $history->setEndDate(new \DateTime());
        $history->calculateTotals();

        // Обновляем пробег автомобиля
        if ($data['endMileage'] !== null) {
            $car = $history->getCar();
            $car->setMileage($data['endMileage']);
        }

        $this->entityManager->flush();

        return CarRentalHistoryResponseDTO::fromEntity($history);
    }

    /**
     * Получить статистику для автомобиля
     */
    public function getStatisticsForCar(int $carId): CarRentalHistoryStatisticsDTO
    {
        $statistics = $this->carRentalHistoryRepository->getStatisticsForCar($carId);
        return CarRentalHistoryStatisticsDTO::fromArray($statistics);
    }

    /**
     * Получить глобальную статистику
     */
    public function getGlobalStatistics(): array
    {
        $statistics = $this->carRentalHistoryRepository->getGlobalStatistics();

        return [
            'total_rentals' => (int) $statistics['total_rentals'],
            'total_distance' => (int) $statistics['total_distance'],
            'total_days' => (int) $statistics['total_days'],
            'total_hours' => (int) $statistics['total_hours'],
            'avg_distance' => round((float) $statistics['avg_distance'], 2),
            'unique_cars' => (int) $statistics['unique_cars'],
            'damages_count' => (int) $statistics['damages_count']
        ];
    }

    /**
     * Получить топ автомобилей по пробегу
     */
    public function getTopByDistance(int $limit = 10): array
    {
        return $this->carRentalHistoryRepository->getTopByDistance($limit);
    }

    /**
     * Получить топ автомобилей по количеству аренд
     */
    public function getTopByRentals(int $limit = 10): array
    {
        return $this->carRentalHistoryRepository->getTopByRentals($limit);
    }

    /**
     * Получить статистику повреждений по месяцам
     */
    public function getDamagesByMonth(int $year): array
    {
        return $this->carRentalHistoryRepository->getDamagesByMonth($year);
    }

    /**
     * Проверить, арендован ли автомобиль в период
     */
    public function isCarRentedInPeriod(int $carId, string $startDate, string $endDate): bool
    {
        return $this->carRentalHistoryRepository->isCarRentedInPeriod(
            $carId,
            new \DateTime($startDate),
            new \DateTime($endDate)
        );
    }

    /**
     * Найти историю или выбросить исключение
     */
    private function findHistoryOrFail(int $id): CarRentalHistory
    {
        $history = $this->carRentalHistoryRepository->find($id);
        if (!$history) {
            throw new NotFoundHttpException(sprintf('Запись истории с ID %d не найдена', $id));
        }
        return $history;
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
