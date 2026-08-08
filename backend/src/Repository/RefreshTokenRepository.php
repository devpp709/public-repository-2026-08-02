<?php
// src/Repository/RefreshTokenRepository.php

namespace App\Repository;

use App\Entity\RefreshToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RefreshTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    public function findValidToken(string $token): ?RefreshToken
    {
        return $this->createQueryBuilder('rt')
            ->where('rt.token = :token')
            ->andWhere('rt.expiresAt > :now')
            ->setParameter('token', $token)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deleteUserTokens(User $user): void
    {
        $this->createQueryBuilder('rt')
            ->delete()
            ->where('rt.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function deleteToken(string $token): void
    {
        $this->createQueryBuilder('rt')
            ->delete()
            ->where('rt.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->execute();
    }

    public function createRefreshToken(User $user): RefreshToken
    {
        // Генерируем безопасный токен
        $tokenString = bin2hex(random_bytes(64));

        $refreshToken = new RefreshToken();
        $refreshToken->setToken($tokenString);
        $refreshToken->setUser($user);
        $refreshToken->setExpiresAt(new \DateTimeImmutable('+7 days'));
        // createdAt устанавливается в конструкторе, но если нет - установим явно
        $refreshToken->setCreatedAt(new \DateTimeImmutable());

        $this->getEntityManager()->persist($refreshToken);
        $this->getEntityManager()->flush();

        return $refreshToken;
    }

    public function existsByToken(string $token): bool
    {
        return (bool) $this->createQueryBuilder('rt')
            ->select('COUNT(rt.id)')
            ->where('rt.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
