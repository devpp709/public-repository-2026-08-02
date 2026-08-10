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
     * Все локации с сортировкой по названию.
     */
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Все локации с сортировкой по стране, городу и названию.
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.country', 'ASC')
            ->addOrderBy('l.city', 'ASC')
            ->addOrderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Локации по городу.
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
     * Локации с доступными автомобилями.
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
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Поиск по названию, адресу, городу, региону и стране.
     */
    public function search(string $searchTerm): array
    {
        return $this->createQueryBuilder('l')
            ->where('LOWER(l.name) LIKE LOWER(:search)')
            ->orWhere('LOWER(l.address) LIKE LOWER(:search)')
            ->orWhere('LOWER(l.city) LIKE LOWER(:search)')
            ->orWhere('LOWER(l.state) LIKE LOWER(:search)')
            ->orWhere('LOWER(l.country) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Локации в приблизительном радиусе.
     */
    public function findNearby(
        float $latitude,
        float $longitude,
        float $radiusKm = 10
    ): array {
        $radiusLat = $radiusKm / 111;

        $radiusLng = $radiusKm / (
                111 * max(cos(deg2rad($latitude)), 0.01)
            );

        return $this->createQueryBuilder('l')
            ->where('l.latitude IS NOT NULL')
            ->andWhere('l.longitude IS NOT NULL')
            ->andWhere('ABS(CAST(l.latitude AS float) - :lat) <= :radiusLat')
            ->andWhere('ABS(CAST(l.longitude AS float) - :lng) <= :radiusLng')
            ->setParameter('lat', $latitude)
            ->setParameter('lng', $longitude)
            ->setParameter('radiusLat', $radiusLat)
            ->setParameter('radiusLng', $radiusLng)
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Статистика по локациям.
     */
    public function getStatistics(): array
    {
        return $this->createQueryBuilder('l')
            ->select(
                'l.id',
                'l.name',
                'l.city',
                'COUNT(DISTINCT car.id) as total_cars',
                'COUNT(DISTINCT CASE WHEN car.isAvailable = true AND car.status = :available THEN car.id ELSE NULL END) as available_cars',
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
     * Популярные локации по количеству бронирований.
     */
    public function findPopular(int $limit = 5): array
    {
        return $this->createQueryBuilder('l')
            ->select('l', 'COUNT(DISTINCT pb.id) as booking_count')
            ->leftJoin('l.pickupBookings', 'pb')
            ->andWhere('pb.status IN (:statuses)')
            ->setParameter(
                'statuses',
                ['confirmed', 'in_progress', 'completed']
            )
            ->groupBy('l.id')
            ->orderBy('booking_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверяет наличие локации с таким названием.
     */
    public function existsByName(
        string $name,
        ?int $excludeId = null
    ): bool {
        $qb = $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('LOWER(l.name) = LOWER(:name)')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb
                ->andWhere('l.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    /**
     * Локации с координатами.
     */
    public function findWithCoordinates(): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.latitude IS NOT NULL')
            ->andWhere('l.longitude IS NOT NULL')
            ->orderBy('l.country', 'ASC')
            ->addOrderBy('l.city', 'ASC')
            ->addOrderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Локации конкретной страны.
     */
    public function findByCountry(string $country): array
    {
        return $this->createQueryBuilder('l')
            ->where('LOWER(l.country) = LOWER(:country)')
            ->setParameter('country', $country)
            ->orderBy('l.city', 'ASC')
            ->addOrderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Локации конкретного региона.
     */
    public function findByState(string $state): array
    {
        return $this->createQueryBuilder('l')
            ->where('LOWER(l.state) = LOWER(:state)')
            ->setParameter('state', $state)
            ->orderBy('l.city', 'ASC')
            ->addOrderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
