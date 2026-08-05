<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    /**
     * Находит все локации с сортировкой по названию
     */
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит локации по городу
     */
    public function findByCity(string $city): array
    {
        return $this->createQueryBuilder('l')
            ->where('LOWER(l.city) = LOWER(:city)')
            ->setParameter('city', $city)
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит локации с доступными автомобилями
     */
    public function findWithAvailableCars(): array
    {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.cars', 'car')
            ->where('car.isAvailable = :available')
            ->andWhere('car.status = :status')
            ->setParameter('available', true)
            ->setParameter('status', 'available')
            ->groupBy('l.id')
            ->having('COUNT(car.id) > 0')
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Поиск локаций по названию или адресу
     */
    public function search(string $searchTerm): array
    {
        return $this->createQueryBuilder('l')
            ->where('LOWER(l.name) LIKE LOWER(:search)')
            ->orWhere('LOWER(l.address) LIKE LOWER(:search)')
            ->orWhere('LOWER(l.city) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит локации в радиусе (в километрах)
     */
    public function findNearby(float $latitude, float $longitude, float $radiusKm = 10): array
    {
        // Преобразуем радиус в градусы (приблизительно 1 градус = 111 км)
        $radiusDeg = $radiusKm / 111;

        return $this->createQueryBuilder('l')
            ->where('l.latitude IS NOT NULL')
            ->andWhere('l.longitude IS NOT NULL')
            ->andWhere('ABS(l.latitude - :lat) <= :radius')
            ->andWhere('ABS(l.longitude - :lng) <= :radius')
            ->setParameter('lat', $latitude)
            ->setParameter('lng', $longitude)
            ->setParameter('radius', $radiusDeg)
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает статистику по локациям
     */
    public function getStatistics(): array
    {
        return $this->createQueryBuilder('l')
            ->select(
                'l.id',
                'l.name',
                'l.city',
                'COUNT(car.id) as total_cars',
                'SUM(CASE WHEN car.isAvailable = true AND car.status = :available THEN 1 ELSE 0 END) as available_cars',
                'COUNT(DISTINCT pb.id) as pickup_bookings',
                'COUNT(DISTINCT db.id) as dropoff_bookings'
            )
            ->leftJoin('l.cars', 'car')
            ->leftJoin('l.pickupBookings', 'pb')
            ->leftJoin('l.dropoffBookings', 'db')
            ->setParameter('available', 'available')
            ->groupBy('l.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит популярные локации (по количеству бронирований)
     */
    public function findPopular(int $limit = 5): array
    {
        return $this->createQueryBuilder('l')
            ->select('l', 'COUNT(pb.id) as booking_count')
            ->leftJoin('l.pickupBookings', 'pb')
            ->where('pb.status IN (:statuses)')
            ->setParameter('statuses', ['confirmed', 'in_progress', 'completed'])
            ->groupBy('l.id')
            ->orderBy('booking_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверяет наличие локации с таким названием
     */
    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('LOWER(l.name) = LOWER(:name)')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb->andWhere('l.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Находит все локации с данными города
     */
    public function findAllWithCity(): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.city', 'c')
            ->addSelect('c')
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('l.sortOrder', 'ASC')
            ->addOrderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит все активные локации с данными города
     */
    public function findActiveWithCity(): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.city', 'ASC')
            ->addOrderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит локации по ID города
     */
    public function findByCityId(int $cityId, bool $onlyActive = true): array
    {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.city', 'c')
            ->addSelect('c')
            ->where('c.id = :cityId')
            ->setParameter('cityId', $cityId)
            ->orderBy('l.sortOrder', 'ASC')
            ->addOrderBy('l.name', 'ASC');

        if ($onlyActive) {
            $qb->andWhere('l.isActive = :active')
                ->setParameter('active', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Находит локации по коду города
     */
    public function findByCityCode(string $cityCode, bool $onlyActive = true): array
    {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.city', 'c')
            ->addSelect('c')
            ->where('c.code = :cityCode')
            ->setParameter('cityCode', $cityCode)
            ->orderBy('l.sortOrder', 'ASC')
            ->addOrderBy('l.name', 'ASC');

        if ($onlyActive) {
            $qb->andWhere('l.isActive = :active')
                ->setParameter('active', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Находит локации с координатами
     */
    public function findWithCoordinates(): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.city', 'c')
            ->addSelect('c')
            ->where('l.latitude IS NOT NULL')
            ->andWhere('l.longitude IS NOT NULL')
            ->andWhere('l.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('l.sortOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('l.city', 'ASC')
            ->addOrderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
