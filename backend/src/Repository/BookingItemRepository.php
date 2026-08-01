<?php

namespace App\Repository;

use App\Entity\BookingItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookingItem>
 */
class BookingItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingItem::class);
    }

    /**
     * Находит все автомобили в бронировании
     */
    public function findByBookingId(int $bookingId): array
    {
        return $this->createQueryBuilder('bi')
            ->innerJoin('bi.car', 'c')
            ->addSelect('c')
            ->where('bi.booking = :bookingId')
            ->setParameter('bookingId', $bookingId)
            ->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит бронирования для автомобиля
     */
    public function findByCarId(int $carId): array
    {
        return $this->createQueryBuilder('bi')
            ->innerJoin('bi.booking', 'b')
            ->addSelect('b')
            ->where('bi.car = :carId')
            ->setParameter('carId', $carId)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает общую сумму всех автомобилей в бронировании
     */
    public function getTotalForBooking(int $bookingId): float
    {
        $result = $this->createQueryBuilder('bi')
            ->select('SUM(bi.totalPrice) as total')
            ->where('bi.booking = :bookingId')
            ->setParameter('bookingId', $bookingId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    /**
     * Проверяет, есть ли автомобиль в бронировании
     */
    public function existsInBooking(int $bookingId, int $carId): bool
    {
        $result = $this->createQueryBuilder('bi')
            ->select('COUNT(bi.id)')
            ->where('bi.booking = :bookingId')
            ->andWhere('bi.car = :carId')
            ->setParameter('bookingId', $bookingId)
            ->setParameter('carId', $carId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result > 0;
    }

    /**
     * Удаляет все автомобили из бронирования
     */
    public function deleteByBookingId(int $bookingId): void
    {
        $this->createQueryBuilder('bi')
            ->delete()
            ->where('bi.booking = :bookingId')
            ->setParameter('bookingId', $bookingId)
            ->getQuery()
            ->execute();
    }

    /**
     * Получает статистику по популярности автомобилей в бронированиях
     */
    public function getPopularCars(int $limit = 10): array
    {
        return $this->createQueryBuilder('bi')
            ->select('c.id', 'c.brand', 'c.model', 'COUNT(bi.id) as total_bookings')
            ->innerJoin('bi.car', 'c')
            ->groupBy('c.id')
            ->orderBy('total_bookings', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает сумму бронирований по дням
     */
    public function getRevenueByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): float
    {
        $result = $this->createQueryBuilder('bi')
            ->select('SUM(bi.totalPrice) as total')
            ->innerJoin('bi.booking', 'b')
            ->where('b.createdAt BETWEEN :start AND :end')
            ->andWhere('b.status = :status')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('status', 'completed')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }
}
