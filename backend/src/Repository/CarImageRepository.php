<?php

namespace App\Repository;

use App\Entity\CarImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarImage>
 */
class CarImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarImage::class);
    }

    /**
     * Находит все изображения для автомобиля
     */
    public function findByCarId(int $carId): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.car = :carId')
            ->setParameter('carId', $carId)
            ->orderBy('i.isMain', 'DESC')
            ->addOrderBy('i.sortOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит главное изображение автомобиля
     */
    public function findMainImageByCarId(int $carId): ?CarImage
    {
        return $this->createQueryBuilder('i')
            ->where('i.car = :carId')
            ->andWhere('i.isMain = :isMain')
            ->setParameter('carId', $carId)
            ->setParameter('isMain', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Получает максимальный порядок сортировки для автомобиля
     */
    public function getMaxSortOrder(int $carId): int
    {
        $result = $this->createQueryBuilder('i')
            ->select('MAX(i.sortOrder)')
            ->where('i.car = :carId')
            ->setParameter('carId', $carId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (int) $result : 0;
    }

    /**
     * Удаляет все изображения автомобиля
     */
    public function deleteByCarId(int $carId): void
    {
        $this->createQueryBuilder('i')
            ->delete()
            ->where('i.car = :carId')
            ->setParameter('carId', $carId)
            ->getQuery()
            ->execute();
    }

    /**
     * Сбрасывает флаг isMain для всех изображений автомобиля
     */
    public function resetMainFlag(int $carId): void
    {
        $this->createQueryBuilder('i')
            ->update()
            ->set('i.isMain', ':isMain')
            ->where('i.car = :carId')
            ->setParameter('isMain', false)
            ->setParameter('carId', $carId)
            ->getQuery()
            ->execute();
    }

    /**
     * Находит изображения с пагинацией
     */
    public function findByCarIdWithPagination(int $carId, int $offset, int $limit): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.car = :carId')
            ->setParameter('carId', $carId)
            ->orderBy('i.isMain', 'DESC')
            ->addOrderBy('i.sortOrder', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Подсчитывает количество изображений у автомобиля
     */
    public function countByCarId(int $carId): int
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.car = :carId')
            ->setParameter('carId', $carId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
