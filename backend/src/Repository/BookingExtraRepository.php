<?php

namespace App\Repository;

use App\Entity\BookingExtra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookingExtra>
 */
class BookingExtraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingExtra::class);
    }

    /**
     * Находит все услуги для бронирования
     */
    public function findByBookingId(int $bookingId): array
    {
        return $this->createQueryBuilder('be')
            ->innerJoin('be.extraService', 'es')
            ->addSelect('es')
            ->where('be.booking = :bookingId')
            ->setParameter('bookingId', $bookingId)
            ->orderBy('es.category', 'ASC')
            ->addOrderBy('es.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает общую сумму дополнительных услуг для бронирования
     */
    public function getTotalForBooking(int $bookingId): float
    {
        $result = $this->createQueryBuilder('be')
            ->select('SUM(be.totalPrice) as total')
            ->where('be.booking = :bookingId')
            ->setParameter('bookingId', $bookingId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    /**
     * Находит услугу в бронировании
     */
    public function findByBookingAndService(int $bookingId, int $serviceId): ?BookingExtra
    {
        return $this->createQueryBuilder('be')
            ->where('be.booking = :bookingId')
            ->andWhere('be.extraService = :serviceId')
            ->setParameter('bookingId', $bookingId)
            ->setParameter('serviceId', $serviceId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Удаляет все услуги из бронирования
     */
    public function deleteByBookingId(int $bookingId): void
    {
        $this->createQueryBuilder('be')
            ->delete()
            ->where('be.booking = :bookingId')
            ->setParameter('bookingId', $bookingId)
            ->getQuery()
            ->execute();
    }

    /**
     * Получает статистику по популярности услуг
     */
    public function getPopularServices(int $limit = 10): array
    {
        return $this->createQueryBuilder('be')
            ->select('es.id', 'es.name', 'es.icon', 'es.category', 'SUM(be.quantity) as total_quantity')
            ->innerJoin('be.extraService', 'es')
            ->groupBy('es.id')
            ->orderBy('total_quantity', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
