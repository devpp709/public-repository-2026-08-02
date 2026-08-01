<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * Находит бронирования по пользователю
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит бронирования по статусу
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.status = :status')
            ->setParameter('status', $status)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит бронирования по периоду
     */
    public function findByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.pickupDate BETWEEN :start AND :end')
            ->orWhere('b.dropoffDate BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('b.pickupDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит активные бронирования (подтвержденные и в процессе)
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.status IN (:statuses)')
            ->setParameter('statuses', ['confirmed', 'in_progress'])
            ->orderBy('b.pickupDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит завершенные бронирования
     */
    public function findCompleted(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.status = :status')
            ->setParameter('status', 'completed')
            ->orderBy('b.dropoffDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит бронирования по номеру
     */
    public function findByBookingNumber(string $bookingNumber): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->where('b.bookingNumber = :bookingNumber')
            ->setParameter('bookingNumber', $bookingNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Находит бронирования, требующие подтверждения
     */
    public function findPending(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('b.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит бронирования по автомобилю
     */
    public function findByCar(int $carId): array
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.bookingItems', 'bi')
            ->innerJoin('bi.car', 'c')
            ->where('c.id = :carId')
            ->setParameter('carId', $carId)
            ->orderBy('b.pickupDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверяет доступность автомобиля на указанный период
     */
    public function isCarAvailable(int $carId, \DateTimeInterface $pickupDate, \DateTimeInterface $dropoffDate): bool
    {
        $result = $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->innerJoin('b.bookingItems', 'bi')
            ->where('bi.car = :carId')
            ->andWhere('b.status IN (:statuses)')
            ->andWhere('b.pickupDate <= :dropoffDate')
            ->andWhere('b.dropoffDate >= :pickupDate')
            ->setParameter('carId', $carId)
            ->setParameter('statuses', ['confirmed', 'in_progress', 'pending'])
            ->setParameter('pickupDate', $pickupDate)
            ->setParameter('dropoffDate', $dropoffDate)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result === 0;
    }

    /**
     * Получает статистику по бронированиям
     */
    public function getStatistics(): array
    {
        return $this->createQueryBuilder('b')
            ->select(
                'COUNT(b.id) as total',
                'SUM(CASE WHEN b.status = :pending THEN 1 ELSE 0 END) as pending',
                'SUM(CASE WHEN b.status = :confirmed THEN 1 ELSE 0 END) as confirmed',
                'SUM(CASE WHEN b.status = :in_progress THEN 1 ELSE 0 END) as in_progress',
                'SUM(CASE WHEN b.status = :completed THEN 1 ELSE 0 END) as completed',
                'SUM(CASE WHEN b.status = :cancelled THEN 1 ELSE 0 END) as cancelled',
                'SUM(b.totalAmount) as total_revenue'
            )
            ->setParameter('pending', 'pending')
            ->setParameter('confirmed', 'confirmed')
            ->setParameter('in_progress', 'in_progress')
            ->setParameter('completed', 'completed')
            ->setParameter('cancelled', 'cancelled')
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Получает статистику по месяцам
     */
    public function getMonthlyStatistics(int $year): array
    {
        return $this->createQueryBuilder('b')
            ->select(
                'MONTH(b.createdAt) as month',
                'COUNT(b.id) as total',
                'SUM(b.totalAmount) as revenue'
            )
            ->where('YEAR(b.createdAt) = :year')
            ->andWhere('b.status = :status')
            ->setParameter('year', $year)
            ->setParameter('status', 'completed')
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Поиск бронирований
     */
    public function search(array $criteria): array
    {
        $qb = $this->createQueryBuilder('b');

        if (isset($criteria['bookingNumber'])) {
            $qb->andWhere('b.bookingNumber LIKE :bookingNumber')
                ->setParameter('bookingNumber', '%' . $criteria['bookingNumber'] . '%');
        }

        if (isset($criteria['userName'])) {
            $qb->innerJoin('b.user', 'u')
                ->andWhere('u.name LIKE :userName')
                ->setParameter('userName', '%' . $criteria['userName'] . '%');
        }

        if (isset($criteria['userEmail'])) {
            $qb->innerJoin('b.user', 'u')
                ->andWhere('u.email LIKE :userEmail')
                ->setParameter('userEmail', '%' . $criteria['userEmail'] . '%');
        }

        if (isset($criteria['status'])) {
            $qb->andWhere('b.status = :status')
                ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['startDate'])) {
            $qb->andWhere('b.pickupDate >= :startDate')
                ->setParameter('startDate', $criteria['startDate']);
        }

        if (isset($criteria['endDate'])) {
            $qb->andWhere('b.dropoffDate <= :endDate')
                ->setParameter('endDate', $criteria['endDate']);
        }

        if (isset($criteria['carId'])) {
            $qb->innerJoin('b.bookingItems', 'bi')
                ->andWhere('bi.car = :carId')
                ->setParameter('carId', $criteria['carId']);
        }

        $qb->orderBy('b.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Получает количество бронирований по дням
     */
    public function getDailyStats(int $days = 30): array
    {
        $startDate = new \DateTimeImmutable("-$days days");

        return $this->createQueryBuilder('b')
            ->select('DATE(b.createdAt) as date', 'COUNT(b.id) as count')
            ->where('b.createdAt >= :startDate')
            ->setParameter('startDate', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает топ пользователей по количеству бронирований
     */
    public function getTopUsers(int $limit = 10): array
    {
        return $this->createQueryBuilder('b')
            ->select('u.id', 'u.name', 'u.email', 'COUNT(b.id) as booking_count')
            ->innerJoin('b.user', 'u')
            ->groupBy('u.id')
            ->orderBy('booking_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
