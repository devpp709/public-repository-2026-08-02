<?php

namespace App\Repository;

use App\Entity\Car;
use App\Entity\CarExtraService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarExtraService>
 */
class CarExtraServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarExtraService::class);
    }

    /**
     * Находит все дополнительные услуги для автомобиля
     */
    public function findByCarId(int $carId): array
    {
        return $this->createQueryBuilder('ces')
            ->innerJoin('ces.extraService', 'es')
            ->addSelect('es')
            ->where('ces.car = :carId')
            ->setParameter('carId', $carId)
            ->orderBy('es.category', 'ASC')
            ->addOrderBy('es.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит обязательные услуги для автомобиля
     */
    public function findRequiredByCarId(int $carId): array
    {
        return $this->createQueryBuilder('ces')
            ->innerJoin('ces.extraService', 'es')
            ->addSelect('es')
            ->where('ces.car = :carId')
            ->andWhere('ces.isRequired = :required')
            ->setParameter('carId', $carId)
            ->setParameter('required', true)
            ->orderBy('es.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит услугу для автомобиля по ID услуги
     */
    public function findByCarAndService(int $carId, int $serviceId): ?CarExtraService
    {
        return $this->createQueryBuilder('ces')
            ->where('ces.car = :carId')
            ->andWhere('ces.extraService = :serviceId')
            ->setParameter('carId', $carId)
            ->setParameter('serviceId', $serviceId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Находит автомобили с указанной услугой
     */
    public function findCarsByService(int $serviceId): array
    {
        return $this->createQueryBuilder('ces')
            ->innerJoin('ces.car', 'c')
            ->addSelect('c')
            ->where('ces.extraService = :serviceId')
            ->setParameter('serviceId', $serviceId)
            ->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит услуги по категории для автомобиля
     */
    public function findByCarIdAndCategory(int $carId, string $category): array
    {
        return $this->createQueryBuilder('ces')
            ->innerJoin('ces.extraService', 'es')
            ->addSelect('es')
            ->where('ces.car = :carId')
            ->andWhere('es.category = :category')
            ->setParameter('carId', $carId)
            ->setParameter('category', $category)
            ->orderBy('es.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Удаляет все услуги для автомобиля
     */
    public function deleteByCarId(int $carId): void
    {
        $this->createQueryBuilder('ces')
            ->delete()
            ->where('ces.car = :carId')
            ->setParameter('carId', $carId)
            ->getQuery()
            ->execute();
    }

    /**
     * Удаляет конкретную услугу у автомобиля
     */
    public function deleteByCarAndService(int $carId, int $serviceId): void
    {
        $this->createQueryBuilder('ces')
            ->delete()
            ->where('ces.car = :carId')
            ->andWhere('ces.extraService = :serviceId')
            ->setParameter('carId', $carId)
            ->setParameter('serviceId', $serviceId)
            ->getQuery()
            ->execute();
    }

    /**
     * Проверяет наличие услуги у автомобиля
     */
    public function existsForCar(int $carId, int $serviceId): bool
    {
        $result = $this->createQueryBuilder('ces')
            ->select('COUNT(ces.id)')
            ->where('ces.car = :carId')
            ->andWhere('ces.extraService = :serviceId')
            ->setParameter('carId', $carId)
            ->setParameter('serviceId', $serviceId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result > 0;
    }

    /**
     * Получает статистику по услугам для автомобиля
     */
    public function getStatisticsForCar(int $carId): array
    {
        return $this->createQueryBuilder('ces')
            ->select('es.category', 'COUNT(ces.id) as total', 'SUM(CASE WHEN ces.isRequired = true THEN 1 ELSE 0 END) as required')
            ->innerJoin('ces.extraService', 'es')
            ->where('ces.car = :carId')
            ->setParameter('carId', $carId)
            ->groupBy('es.category')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает общую сумму обязательных услуг для автомобиля
     */
    public function getRequiredServicesTotal(int $carId): float
    {
        $result = $this->createQueryBuilder('ces')
            ->select('SUM(COALESCE(ces.price, es.defaultPrice)) as total')
            ->innerJoin('ces.extraService', 'es')
            ->where('ces.car = :carId')
            ->andWhere('ces.isRequired = :required')
            ->setParameter('carId', $carId)
            ->setParameter('required', true)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    /**
     * Находит услуги с индивидуальной ценой
     */
    public function findWithCustomPrice(int $carId): array
    {
        return $this->createQueryBuilder('ces')
            ->innerJoin('ces.extraService', 'es')
            ->addSelect('es')
            ->where('ces.car = :carId')
            ->andWhere('ces.price IS NOT NULL')
            ->orderBy('es.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Копирует услуги с одного автомобиля на другой
     */
    public function copyServicesFromCar(int $sourceCarId, int $targetCarId): void
    {
        $services = $this->findByCarId($sourceCarId);

        foreach ($services as $service) {
            $newService = new CarExtraService();
            $newService->setCar($this->getEntityManager()->getReference(Car::class, $targetCarId));
            $newService->setExtraService($service->getExtraService());
            $newService->setPrice($service->getPrice());
            $newService->setIsRequired($service->isRequired());

            $this->getEntityManager()->persist($newService);
        }

        $this->getEntityManager()->flush();
    }
}
