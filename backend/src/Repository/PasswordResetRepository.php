<?php

namespace App\Repository;

use App\Entity\PasswordReset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PasswordResetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordReset::class);
    }

    public function findByToken(string $token): ?PasswordReset
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deleteExpired(): int
    {
        return $this->createQueryBuilder('pr')
            ->delete()
            ->where('pr.expiresAt <= :now')
            ->orWhere('pr.isUsed = :used')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('used', true)
            ->getQuery()
            ->execute();
    }
}
