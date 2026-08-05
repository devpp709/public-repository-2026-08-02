<?php

namespace App\Repository;

use App\Entity\CarClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarClass>
 */
class CarClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarClass::class);
    }

    /**
     * Находит все классы с сортировкой по имени
     */
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит классы с активными автомобилями
     */
    public function findWithAvailableCars(): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.cars', 'car')
            ->where('car.isAvailable = :available')
            ->andWhere('car.status = :status')
            ->setParameter('available', true)
            ->setParameter('status', 'available')
            ->groupBy('c.id')
            ->having('COUNT(car.id) > 0')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит класс по имени (для поиска)
     */
    public function findOneByName(string $name): ?CarClass
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.name) = LOWER(:name)')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Поиск классов по названию
     */
    public function searchByName(string $searchTerm): array
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.name) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает статистику по классам
     */
    public function getClassStatistics(): array
    {
        return $this->createQueryBuilder('c')
            ->select(
                'c.id',
                'c.name',
                'COUNT(car.id) as total_cars',
                'SUM(CASE WHEN car.isAvailable = true AND car.status = :available THEN 1 ELSE 0 END) as available_cars',
                'MIN(car.dailyPrice) as min_price',
                'MAX(car.dailyPrice) as max_price',
                'AVG(car.dailyPrice) as avg_price'
            )
            ->leftJoin('c.cars', 'car')
            ->setParameter('available', 'available')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }
}
