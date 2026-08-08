<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPasswordHash($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Сохранение пользователя
     */
    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Находит пользователя по email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('LOWER(u.email) = LOWER(:email)')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Находит пользователя по телефону
     */
    public function findByPhone(string $phone): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.phone = :phone')
            ->setParameter('phone', $phone)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Находит пользователей по роли
     */
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.role = :role')
            ->setParameter('role', $role)
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит пользователей по статусу
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.status = :status')
            ->setParameter('status', $status)
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит активных пользователей
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.status = :status')
            ->setParameter('status', 'active')
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Поиск пользователей по имени или email
     */
    public function search(string $searchTerm): array
    {
        return $this->createQueryBuilder('u')
            ->where('LOWER(u.name) LIKE LOWER(:search)')
            ->orWhere('LOWER(u.email) LIKE LOWER(:search)')
            ->orWhere('LOWER(u.phone) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит пользователей, зарегистрированных в указанный период
     */
    public function findRegisteredBetween(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверяет существование пользователя с таким email
     */
    public function existsByEmail(string $email, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('LOWER(u.email) = LOWER(:email)')
            ->setParameter('email', $email);

        if ($excludeId !== null) {
            $qb->andWhere('u.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Проверяет существование пользователя с таким телефоном
     */
    public function existsByPhone(string $phone, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.phone = :phone')
            ->setParameter('phone', $phone);

        if ($excludeId !== null) {
            $qb->andWhere('u.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Получает статистику по пользователям
     */
    public function getStatistics(): array
    {
        return $this->createQueryBuilder('u')
            ->select(
                'COUNT(u.id) as total',
                'SUM(CASE WHEN u.status = :active THEN 1 ELSE 0 END) as active',
                'SUM(CASE WHEN u.status = :blocked THEN 1 ELSE 0 END) as blocked',
                'SUM(CASE WHEN u.status = :pending THEN 1 ELSE 0 END) as pending',
                'SUM(CASE WHEN u.role = :admin THEN 1 ELSE 0 END) as admins',
                'SUM(CASE WHEN u.role = :manager THEN 1 ELSE 0 END) as managers',
                'SUM(CASE WHEN u.role = :customer THEN 1 ELSE 0 END) as customers'
            )
            ->setParameter('active', 'active')
            ->setParameter('blocked', 'blocked')
            ->setParameter('pending', 'pending')
            ->setParameter('admin', 'admin')
            ->setParameter('manager', 'manager')
            ->setParameter('customer', 'customer')
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Получает статистику по регистрациям по дням (за последние 30 дней)
     */
    public function getRegistrationStats(int $days = 30): array
    {
        $startDate = new \DateTimeImmutable("-$days days");

        return $this->createQueryBuilder('u')
            ->select('DATE(u.createdAt) as date', 'COUNT(u.id) as count')
            ->where('u.createdAt >= :startDate')
            ->setParameter('startDate', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит пользователей с активными бронированиями
     */
    public function findWithActiveBookings(): array
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.bookings', 'b')
            ->where('b.status IN (:statuses)')
            ->setParameter('statuses', ['confirmed', 'in_progress', 'pending'])
            ->groupBy('u.id')
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит пользователей без бронирований
     */
    public function findWithoutBookings(): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.bookings', 'b')
            ->groupBy('u.id')
            ->having('COUNT(b.id) = 0')
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит пользователей с отзывами
     */
    public function findWithReviews(): array
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.reviews', 'r')
            ->groupBy('u.id')
            ->having('COUNT(r.id) > 0')
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает последних зарегистрированных пользователей
     */
    public function findLatest(int $limit = 10): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
