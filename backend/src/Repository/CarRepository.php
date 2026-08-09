<?php

namespace App\Repository;

use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    /**
     * Находит все доступные автомобили
     */
    public function findAvailable(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.isAvailable = :available')
            ->andWhere('c.status = :status')
            ->setParameter('available', true)
            ->setParameter('status', 'available')
            ->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит автомобили по классу
     */
    public function findByClass(int $classId): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.carClass', 'cc')
            ->where('cc.id = :classId')
            ->setParameter('classId', $classId)
            ->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит автомобили по локации
     */
    public function findByLocation(int $locationId): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.location', 'l')
            ->where('l.id = :locationId')
            ->setParameter('locationId', $locationId)
            ->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит автомобили по бренду
     */
    public function findByBrand(string $brand): array
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.brand) = LOWER(:brand)')
            ->setParameter('brand', $brand)
            ->orderBy('c.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит автомобили по цене (диапазон)
     */
    public function findByPriceRange(float $min, float $max): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.dailyPrice BETWEEN :min AND :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->orderBy('c.dailyPrice', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит автомобили по типу топлива
     */
    public function findByFuelType(string $fuelType): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.fuelType = :fuelType')
            ->setParameter('fuelType', $fuelType)
            ->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит автомобили по коробке передач
     */
    public function findByTransmission(string $transmission): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.transmission = :transmission')
            ->setParameter('transmission', $transmission)
            ->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Поиск автомобилей по различным критериям
     */
    public function search(array $criteria): array
    {
        $qb = $this->createQueryBuilder('c');

        if (isset($criteria['brand'])) {
            $qb->andWhere('LOWER(c.brand) LIKE LOWER(:brand)')
                ->setParameter('brand', '%' . $criteria['brand'] . '%');
        }

        if (isset($criteria['model'])) {
            $qb->andWhere('LOWER(c.model) LIKE LOWER(:model)')
                ->setParameter('model', '%' . $criteria['model'] . '%');
        }

        if (isset($criteria['classId'])) {
            $qb->innerJoin('c.carClass', 'cc')
                ->andWhere('cc.id = :classId')
                ->setParameter('classId', $criteria['classId']);
        }

        if (isset($criteria['locationId'])) {
            $qb->innerJoin('c.location', 'l')
                ->andWhere('l.id = :locationId')
                ->setParameter('locationId', $criteria['locationId']);
        }

        if (isset($criteria['minPrice'])) {
            $qb->andWhere('c.dailyPrice >= :minPrice')
                ->setParameter('minPrice', $criteria['minPrice']);
        }

        if (isset($criteria['maxPrice'])) {
            $qb->andWhere('c.dailyPrice <= :maxPrice')
                ->setParameter('maxPrice', $criteria['maxPrice']);
        }

        if (isset($criteria['fuelType'])) {
            $qb->andWhere('c.fuelType = :fuelType')
                ->setParameter('fuelType', $criteria['fuelType']);
        }

        if (isset($criteria['transmission'])) {
            $qb->andWhere('c.transmission = :transmission')
                ->setParameter('transmission', $criteria['transmission']);
        }

        if (isset($criteria['seats'])) {
            $qb->andWhere('c.seats >= :seats')
                ->setParameter('seats', $criteria['seats']);
        }

        if (isset($criteria['available'])) {
            $qb->andWhere('c.isAvailable = :available')
                ->setParameter('available', $criteria['available']);
        }

        if (isset($criteria['status'])) {
            $qb->andWhere('c.status = :status')
                ->setParameter('status', $criteria['status']);
        }

        $qb->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Находит автомобили с их фото
     */
    public function findWithImages(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.images', 'i')
            ->addSelect('i')
            ->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит автомобили с их характеристиками
     */
    public function findWithFeatures(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.carFeatures', 'cf')
            ->leftJoin('cf.feature', 'f')
            ->addSelect('cf', 'f')
            ->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит автомобили с отзывами
     */
    public function findWithReviews(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.reviews', 'r')
            ->addSelect('r')
            ->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает статистику по автомобилям
     */
    public function getStatistics(): array
    {
        return $this->createQueryBuilder('c')
            ->select(
                'COUNT(c.id) as total',
                'SUM(CASE WHEN c.isAvailable = true AND c.status = :available THEN 1 ELSE 0 END) as available',
                'SUM(CASE WHEN c.status = :rented THEN 1 ELSE 0 END) as rented',
                'SUM(CASE WHEN c.status = :maintenance THEN 1 ELSE 0 END) as maintenance',
                'SUM(CASE WHEN c.status = :reserved THEN 1 ELSE 0 END) as reserved',
                'AVG(c.dailyPrice) as avg_daily_price',
                'MIN(c.dailyPrice) as min_daily_price',
                'MAX(c.dailyPrice) as max_daily_price'
            )
            ->setParameter('available', 'available')
            ->setParameter('rented', 'rented')
            ->setParameter('maintenance', 'maintenance')
            ->setParameter('reserved', 'reserved')
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Получает статистику по брендам
     */
    public function getBrandStatistics(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.brand', 'COUNT(c.id) as total')
            ->groupBy('c.brand')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит популярные автомобили (по количеству аренд)
     */
    public function findPopular(int $limit = 10): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'COUNT(b.id) as rental_count')
            ->leftJoin('c.bookings', 'b')
            ->groupBy('c.id')
            ->orderBy('rental_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит автомобили, доступные в указанный период
     */
    public function findAvailableForPeriod(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        array $filters = []
    ): array {
        $subquery = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(b.car)')
            ->from('App\Entity\Booking', 'b')
            ->where('b.status IN (:statuses)')
            ->andWhere(
                '(b.pickupDate < :endDate AND b.dropoffDate > :startDate)'
            )
            ->setParameter('statuses', [
                'confirmed',
                'in_progress',
                'pending',
            ])
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        $qb = $this->createQueryBuilder('c')
            ->where('c.isAvailable = :available')
            ->andWhere('c.status = :status')
            ->andWhere('c.id NOT IN (' . $subquery->getDQL() . ')')
            ->setParameter('available', true)
            ->setParameter('status', 'available');

        foreach ($subquery->getParameters() as $parameter) {
            $qb->setParameter(
                $parameter->getName(),
                $parameter->getValue()
            );
        }

        return $this->applyFilters($qb, $filters)
            ->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит автомобили с высоким рейтингом
     */
    public function findTopRated(int $limit = 10): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'AVG(r.rating) as avg_rating')
            ->leftJoin('c.reviews', 'r')
            ->groupBy('c.id')
            ->having('AVG(r.rating) >= 4.0')
            ->orderBy('avg_rating', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверяет существование автомобиля по VIN
     */
    public function existsByVin(string $vin, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.vin = :vin')
            ->setParameter('vin', $vin);

        if ($excludeId !== null) {
            $qb->andWhere('c.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Проверяет существование автомобиля по госномеру
     */
    public function existsByLicensePlate(string $licensePlate, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.licensePlate = :licensePlate')
            ->setParameter('licensePlate', $licensePlate);

        if ($excludeId !== null) {
            $qb->andWhere('c.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Применяет дополнительные фильтры к запросу
     */

    private function applyFilters($qb, array $filters): object
    {
        // Фильтр по локации pickup
        if (!empty($filters['pickup_location'])) {
            $qb->andWhere('c.location = :pickupLocation')
                ->setParameter('pickupLocation', (int) $filters['pickup_location']);
        }

        // Фильтр по локации dropoff
        if (!empty($filters['dropoff_location'])) {
            $qb->innerJoin('c.bookings', 'filterBooking')
                ->andWhere('filterBooking.dropoffLocation = :dropoffLocation')
                ->setParameter('dropoffLocation', (int) $filters['dropoff_location']);
        }

        // Фильтр по классу автомобиля
        if (!empty($filters['class_id'])) {
            $qb->andWhere('c.carClass = :classId')
                ->setParameter('classId', (int) $filters['class_id']);
        }

        // Фильтр по минимальной цене
        if (!empty($filters['min_price']) && is_numeric($filters['min_price'])) {
            $qb->andWhere('c.dailyPrice >= :minPrice')
                ->setParameter('minPrice', (float) $filters['min_price']);
        }

        // Фильтр по максимальной цене
        if (!empty($filters['max_price']) && is_numeric($filters['max_price'])) {
            $qb->andWhere('c.dailyPrice <= :maxPrice')
                ->setParameter('maxPrice', (float) $filters['max_price']);
        }

        // Фильтр по типу топлива
        if (!empty($filters['fuel_type'])) {
            $qb->andWhere('c.fuelType = :fuelType')
                ->setParameter('fuelType', $filters['fuel_type']);
        }

        // Фильтр по трансмиссии
        if (!empty($filters['transmission'])) {
            $qb->andWhere('c.transmission = :transmission')
                ->setParameter('transmission', $filters['transmission']);
        }

        // Фильтр по количеству мест
        if (!empty($filters['seats']) && is_numeric($filters['seats'])) {
            $qb->andWhere('c.seats >= :seats')
                ->setParameter('seats', (int) $filters['seats']);
        }

        return $qb;
    }

}
