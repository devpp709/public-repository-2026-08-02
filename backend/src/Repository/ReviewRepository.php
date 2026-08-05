<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * Находит отзывы по автомобилю
     */
    public function findByCarId(int $carId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.car = :carId')
            ->setParameter('carId', $carId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит отзывы по пользователю
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит отзывы по бронированию
     */
    public function findByBookingId(int $bookingId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.booking = :bookingId')
            ->setParameter('bookingId', $bookingId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит отзывы с высоким рейтингом
     */
    public function findTopRated(int $limit = 10): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.rating >= 4')
            ->andWhere('r.isVerified = :verified')
            ->setParameter('verified', true)
            ->orderBy('r.rating', 'DESC')
            ->addOrderBy('r.helpfulCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит отзывы с низким рейтингом
     */
    public function findLowRated(int $limit = 10): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.rating <= 2')
            ->orderBy('r.rating', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает статистику по отзывам для автомобиля
     */
    public function getStatisticsForCar(int $carId): array
    {
        return $this->createQueryBuilder('r')
            ->select(
                'COUNT(r.id) as total',
                'AVG(r.rating) as avg_rating',
                'MIN(r.rating) as min_rating',
                'MAX(r.rating) as max_rating',
                'SUM(CASE WHEN r.rating >= 4 THEN 1 ELSE 0 END) as positive',
                'SUM(CASE WHEN r.rating = 3 THEN 1 ELSE 0 END) as neutral',
                'SUM(CASE WHEN r.rating <= 2 THEN 1 ELSE 0 END) as negative',
                'SUM(CASE WHEN r.isVerified = true THEN 1 ELSE 0 END) as verified'
            )
            ->where('r.car = :carId')
            ->setParameter('carId', $carId)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Получает глобальную статистику по отзывам
     */
    public function getGlobalStatistics(): array
    {
        return $this->createQueryBuilder('r')
            ->select(
                'COUNT(r.id) as total',
                'AVG(r.rating) as avg_rating',
                'COUNT(DISTINCT r.car) as unique_cars',
                'COUNT(DISTINCT r.user) as unique_users',
                'SUM(CASE WHEN r.isVerified = true THEN 1 ELSE 0 END) as verified',
                'SUM(r.helpfulCount) as total_helpful'
            )
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Получает распределение рейтингов
     */
    public function getRatingDistribution(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.rating', 'COUNT(r.id) as count')
            ->groupBy('r.rating')
            ->orderBy('r.rating', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает последние отзывы
     */
    public function findLatest(int $limit = 10): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает самые полезные отзывы
     */
    public function findMostHelpful(int $limit = 10): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.helpfulCount', 'DESC')
            ->addOrderBy('r.rating', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверяет, оставлял ли пользователь отзыв на бронирование
     */
    public function existsByBookingAndUser(int $bookingId, int $userId): bool
    {
        $result = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.booking = :bookingId')
            ->andWhere('r.user = :userId')
            ->setParameter('bookingId', $bookingId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result > 0;
    }

    /**
     * Получает отзывы по диапазону дат
     */
    public function findByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает отзывы с комментариями
     */
    public function findWithComment(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.comment IS NOT NULL')
            ->andWhere('r.comment != \'\'')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
