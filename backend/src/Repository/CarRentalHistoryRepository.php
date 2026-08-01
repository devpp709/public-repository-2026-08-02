<?php

namespace App\Repository;

use App\Entity\CarRentalHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarRentalHistory>
 */
class CarRentalHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarRentalHistory::class);
    }

    /**
     * Находит историю для автомобиля
     */
    public function findByCarId(int $carId): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.car = :carId')
            ->setParameter('carId', $carId)
            ->orderBy('h.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит историю по бронированию
     */
    public function findByBookingId(int $bookingId): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.booking = :bookingId')
            ->setParameter('bookingId', $bookingId)
            ->orderBy('h.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит историю за период
     */
    public function findByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.startDate BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('h.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает статистику по автомобилю
     */
    public function getStatisticsForCar(int $carId): array
    {
        return $this->createQueryBuilder('h')
            ->select(
                'COUNT(h.id) as total_rentals',
                'SUM(h.totalDistance) as total_distance',
                'SUM(h.totalDays) as total_days',
                'SUM(h.totalHours) as total_hours',
                'AVG(h.totalDistance) as avg_distance',
                'MIN(h.startDate) as first_rental',
                'MAX(h.endDate) as last_rental',
                'SUM(CASE WHEN h.conditionAfter = \'Damaged\' THEN 1 ELSE 0 END) as damages_count'
            )
            ->where('h.car = :carId')
            ->setParameter('carId', $carId)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Получает статистику по всем автомобилям
     */
    public function getGlobalStatistics(): array
    {
        return $this->createQueryBuilder('h')
            ->select(
                'COUNT(h.id) as total_rentals',
                'SUM(h.totalDistance) as total_distance',
                'SUM(h.totalDays) as total_days',
                'SUM(h.totalHours) as total_hours',
                'AVG(h.totalDistance) as avg_distance',
                'COUNT(DISTINCT h.car) as unique_cars',
                'SUM(CASE WHEN h.conditionAfter = \'Damaged\' THEN 1 ELSE 0 END) as damages_count'
            )
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Получает топ автомобилей по пробегу
     */
    public function getTopByDistance(int $limit = 10): array
    {
        return $this->createQueryBuilder('h')
            ->select('c.id', 'c.brand', 'c.model', 'SUM(h.totalDistance) as total_distance')
            ->innerJoin('h.car', 'c')
            ->groupBy('c.id')
            ->orderBy('total_distance', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает топ автомобилей по количеству аренд
     */
    public function getTopByRentals(int $limit = 10): array
    {
        return $this->createQueryBuilder('h')
            ->select('c.id', 'c.brand', 'c.model', 'COUNT(h.id) as rentals_count')
            ->innerJoin('h.car', 'c')
            ->groupBy('c.id')
            ->orderBy('rentals_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает количество повреждений по месяцам
     */
    public function getDamagesByMonth(int $year): array
    {
        return $this->createQueryBuilder('h')
            ->select('MONTH(h.endDate) as month', 'COUNT(h.id) as damages_count')
            ->where('YEAR(h.endDate) = :year')
            ->andWhere('h.conditionAfter = :condition')
            ->setParameter('year', $year)
            ->setParameter('condition', 'Damaged')
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит активные аренды (не завершенные)
     */
    public function findActiveRentals(): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.endDate > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('h.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверяет, был ли автомобиль в аренде в указанный период
     */
    public function isCarRentedInPeriod(int $carId, \DateTimeInterface $start, \DateTimeInterface $end): bool
    {
        $result = $this->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->where('h.car = :carId')
            ->andWhere('h.startDate <= :end')
            ->andWhere('h.endDate >= :start')
            ->setParameter('carId', $carId)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result > 0;
    }

    /**
     * Получает среднюю продолжительность аренды
     */
    public function getAverageRentalDuration(): float
    {
        $result = $this->createQueryBuilder('h')
            ->select('AVG(h.totalDays) as avg_days')
            ->where('h.totalDays > 0')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    public function findAvailableForPeriod(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $subquery = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(bi.car)')
            ->from('App\Entity\BookingItem', 'bi')
            ->innerJoin('bi.booking', 'b')
            ->where('b.status IN (:statuses)')
            ->andWhere('b.pickupDate <= :endDate')
            ->andWhere('b.dropoffDate >= :startDate')
            ->setParameter('statuses', ['confirmed', 'in_progress', 'pending'])
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        return $this->createQueryBuilder('c')
            ->where('c.isAvailable = :available')
            ->andWhere('c.status = :status')
            ->andWhere($this->getEntityManager()->createQueryBuilder()->expr()->notIn('c.id', $subquery->getDQL()))
            ->setParameter('available', true)
            ->setParameter('status', 'available')
            ->orderBy('c.dailyPrice', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
