<?php

namespace App\Repository;

use App\Entity\Feature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feature>
 */
class FeatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feature::class);
    }

    /**
     * Находит все характеристики с сортировкой по категории и имени
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.category', 'ASC')
            ->addOrderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит характеристики по категории
     */
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.category = :category')
            ->setParameter('category', $category)
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит характеристики по ID автомобиля
     */
    public function findByCarId(int $carId): array
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.carFeatures', 'cf')
            ->innerJoin('cf.car', 'c')
            ->where('c.id = :carId')
            ->setParameter('carId', $carId)
            ->orderBy('f.category', 'ASC')
            ->addOrderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Поиск характеристик по названию
     */
    public function search(string $searchTerm): array
    {
        return $this->createQueryBuilder('f')
            ->where('LOWER(f.name) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает статистику по категориям
     */
    public function getCategoryStatistics(): array
    {
        return $this->createQueryBuilder('f')
            ->select('f.category', 'COUNT(f.id) as total')
            ->groupBy('f.category')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает популярные характеристики
     */
    public function findPopular(int $limit = 10): array
    {
        return $this->createQueryBuilder('f')
            ->select('f', 'COUNT(cf.id) as usage_count')
            ->leftJoin('f.carFeatures', 'cf')
            ->groupBy('f.id')
            ->orderBy('usage_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверяет существование характеристики с таким названием
     */
    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->where('LOWER(f.name) = LOWER(:name)')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb->andWhere('f.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Находит характеристики по категориям
     */
    public function findByCategories(array $categories): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.category IN (:categories)')
            ->setParameter('categories', $categories)
            ->orderBy('f.category', 'ASC')
            ->addOrderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает все уникальные категории
     */
    public function findAllCategories(): array
    {
        return $this->createQueryBuilder('f')
            ->select('DISTINCT f.category')
            ->where('f.category IS NOT NULL')
            ->orderBy('f.category', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает характеристики с автомобилями
     */
    public function findWithCars(): array
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.carFeatures', 'cf')
            ->innerJoin('cf.car', 'c')
            ->select('f', 'COUNT(c.id) as cars_count')
            ->groupBy('f.id')
            ->having('COUNT(c.id) > 0')
            ->orderBy('cars_count', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
