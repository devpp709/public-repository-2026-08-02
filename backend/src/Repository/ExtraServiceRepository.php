<?php

namespace App\Repository;

use App\Entity\ExtraService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExtraService>
 */
class ExtraServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExtraService::class);
    }

    /**
     * Находит все активные услуги
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('e.category', 'ASC')
            ->addOrderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит все услуги с сортировкой по категории и имени
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.category', 'ASC')
            ->addOrderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит услуги по категории
     */
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.category = :category')
            ->setParameter('category', $category)
            ->andWhere('e.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит услуги по ID автомобиля
     */
    public function findByCarId(int $carId, bool $onlyActive = true): array
    {
        $qb = $this->createQueryBuilder('e')
            ->innerJoin('e.carExtraServices', 'ces')
            ->innerJoin('ces.car', 'c')
            ->where('c.id = :carId')
            ->setParameter('carId', $carId)
            ->orderBy('e.category', 'ASC')
            ->addOrderBy('e.name', 'ASC');

        if ($onlyActive) {
            $qb->andWhere('e.isActive = :active')
                ->setParameter('active', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Находит обязательные услуги для автомобиля
     */
    public function findRequiredForCar(int $carId): array
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.carExtraServices', 'ces')
            ->innerJoin('ces.car', 'c')
            ->where('c.id = :carId')
            ->andWhere('ces.isRequired = :required')
            ->andWhere('e.isActive = :active')
            ->setParameter('carId', $carId)
            ->setParameter('required', true)
            ->setParameter('active', true)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Поиск услуг по названию или описанию
     */
    public function search(string $searchTerm): array
    {
        return $this->createQueryBuilder('e')
            ->where('LOWER(e.name) LIKE LOWER(:search)')
            ->orWhere('LOWER(e.description) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->andWhere('e.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает статистику по категориям
     */
    public function getCategoryStatistics(): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.category', 'COUNT(e.id) as total')
            ->where('e.isActive = :active')
            ->setParameter('active', true)
            ->groupBy('e.category')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает популярные услуги
     */
    public function findPopular(int $limit = 10): array
    {
        return $this->createQueryBuilder('e')
            ->select('e', 'COUNT(be.id) as usage_count')
            ->leftJoin('e.bookingExtras', 'be')
            ->where('e.isActive = :active')
            ->setParameter('active', true)
            ->groupBy('e.id')
            ->orderBy('usage_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверяет существование услуги с таким названием
     */
    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('LOWER(e.name) = LOWER(:name)')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb->andWhere('e.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Находит услуги с ценами для автомобиля
     */
    public function findWithPricesForCar(int $carId): array
    {
        return $this->createQueryBuilder('e')
            ->select('e', 'ces.price as custom_price', 'ces.isRequired as is_required')
            ->leftJoin('e.carExtraServices', 'ces', 'WITH', 'ces.car = :carId')
            ->where('e.isActive = :active')
            ->setParameter('carId', $carId)
            ->setParameter('active', true)
            ->orderBy('e.category', 'ASC')
            ->addOrderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает все уникальные категории
     */
    public function findAllCategories(): array
    {
        return $this->createQueryBuilder('e')
            ->select('DISTINCT e.category')
            ->where('e.category IS NOT NULL')
            ->andWhere('e.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('e.category', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
