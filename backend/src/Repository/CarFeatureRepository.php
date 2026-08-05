<?php

namespace App\Repository;

use App\Entity\CarFeature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarFeature>
 */
class CarFeatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarFeature::class);
    }

    /**
     * Находит все характеристики для автомобиля
     */
    public function findByCarId(int $carId): array
    {
        return $this->createQueryBuilder('cf')
            ->innerJoin('cf.feature', 'f')
            ->addSelect('f')
            ->where('cf.car = :carId')
            ->setParameter('carId', $carId)
            ->orderBy('f.category', 'ASC')
            ->addOrderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит характеристику для автомобиля по ID характеристики
     */
    public function findByCarAndFeature(int $carId, int $featureId): ?CarFeature
    {
        return $this->createQueryBuilder('cf')
            ->where('cf.car = :carId')
            ->andWhere('cf.feature = :featureId')
            ->setParameter('carId', $carId)
            ->setParameter('featureId', $featureId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Находит автомобили с указанной характеристикой
     */
    public function findCarsByFeature(int $featureId): array
    {
        return $this->createQueryBuilder('cf')
            ->innerJoin('cf.car', 'c')
            ->addSelect('c')
            ->where('cf.feature = :featureId')
            ->setParameter('featureId', $featureId)
            ->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит характеристики по категории для автомобиля
     */
    public function findByCarIdAndCategory(int $carId, string $category): array
    {
        return $this->createQueryBuilder('cf')
            ->innerJoin('cf.feature', 'f')
            ->addSelect('f')
            ->where('cf.car = :carId')
            ->andWhere('f.category = :category')
            ->setParameter('carId', $carId)
            ->setParameter('category', $category)
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Удаляет все характеристики для автомобиля
     */
    public function deleteByCarId(int $carId): void
    {
        $this->createQueryBuilder('cf')
            ->delete()
            ->where('cf.car = :carId')
            ->setParameter('carId', $carId)
            ->getQuery()
            ->execute();
    }

    /**
     * Удаляет конкретную характеристику у автомобиля
     */
    public function deleteByCarAndFeature(int $carId, int $featureId): void
    {
        $this->createQueryBuilder('cf')
            ->delete()
            ->where('cf.car = :carId')
            ->andWhere('cf.feature = :featureId')
            ->setParameter('carId', $carId)
            ->setParameter('featureId', $featureId)
            ->getQuery()
            ->execute();
    }

    /**
     * Получает статистику по характеристикам для автомобиля
     */
    public function getStatisticsForCar(int $carId): array
    {
        return $this->createQueryBuilder('cf')
            ->select('f.category', 'COUNT(cf.id) as total')
            ->innerJoin('cf.feature', 'f')
            ->where('cf.car = :carId')
            ->setParameter('carId', $carId)
            ->groupBy('f.category')
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверяет наличие характеристики у автомобиля
     */
    public function existsForCar(int $carId, int $featureId): bool
    {
        $result = $this->createQueryBuilder('cf')
            ->select('COUNT(cf.id)')
            ->where('cf.car = :carId')
            ->andWhere('cf.feature = :featureId')
            ->setParameter('carId', $carId)
            ->setParameter('featureId', $featureId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result > 0;
    }

    /**
     * Получает все уникальные значения характеристики для автомобилей
     */
    public function findDistinctValuesByFeature(int $featureId): array
    {
        return $this->createQueryBuilder('cf')
            ->select('DISTINCT cf.value')
            ->where('cf.feature = :featureId')
            ->andWhere('cf.value IS NOT NULL')
            ->setParameter('featureId', $featureId)
            ->orderBy('cf.value', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает количество автомобилей с характеристикой
     */
    public function countByFeature(int $featureId): int
    {
        return $this->createQueryBuilder('cf')
            ->select('COUNT(cf.id)')
            ->where('cf.feature = :featureId')
            ->setParameter('featureId', $featureId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
