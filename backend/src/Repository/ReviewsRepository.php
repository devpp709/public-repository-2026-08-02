<?php

namespace App\Repository;

use App\Entity\Review;
use App\Entity\Car;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * Сохранить отзыв
     */
    public function save(Review $review, bool $flush = true): void
    {
        $this->getEntityManager()->persist($review);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Удалить отзыв
     */
    public function remove(Review $review, bool $flush = true): void
    {
        $this->getEntityManager()->remove($review);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Найти отзывы с фильтрацией и пагинацией
     */
    public function findReviews(
        ?int $carId = null,
        ?int $userId = null,
        ?int $rating = null,
        ?bool $verified = null,
        ?string $sort = 'newest',
        int $page = 1,
        int $perPage = 10
    ): array {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.car', 'c')
            ->addSelect('u')
            ->addSelect('c');

        // Фильтр по автомобилю
        if ($carId) {
            $qb->andWhere('c.id = :carId')
                ->setParameter('carId', $carId);
        }

        // Фильтр по пользователю
        if ($userId) {
            $qb->andWhere('u.id = :userId')
                ->setParameter('userId', $userId);
        }

        // Фильтр по рейтингу
        if ($rating && $rating >= 1 && $rating <= 5) {
            $qb->andWhere('r.rating = :rating')
                ->setParameter('rating', $rating);
        }

        // Фильтр по подтвержденным отзывам
        if ($verified !== null) {
            $qb->andWhere('r.isVerified = :verified')
                ->setParameter('verified', $verified);
        }

        // Сортировка
        $sortMap = [
            'newest' => ['r.createdAt', 'DESC'],
            'oldest' => ['r.createdAt', 'ASC'],
            'rating_high' => ['r.rating', 'DESC'],
            'rating_low' => ['r.rating', 'ASC'],
            'helpful' => ['r.helpfulCount', 'DESC'],
        ];

        if (isset($sortMap[$sort])) {
            [$field, $direction] = $sortMap[$sort];
            $qb->orderBy($field, $direction);
        } else {
            $qb->orderBy('r.createdAt', 'DESC');
        }

        // Пагинация
        $qb->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $query = $qb->getQuery();

        // Получаем результаты и общее количество
        $items = $query->getResult();
        $total = $this->countReviews($carId, $userId, $rating, $verified);

        return [
            'data' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage),
        ];
    }

    /**
     * Подсчитать количество отзывов с фильтрами
     */
    public function countReviews(
        ?int $carId = null,
        ?int $userId = null,
        ?int $rating = null,
        ?bool $verified = null
    ): int {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)');

        if ($carId) {
            $qb->andWhere('r.car = :carId')
                ->setParameter('carId', $carId);
        }

        if ($userId) {
            $qb->andWhere('r.user = :userId')
                ->setParameter('userId', $userId);
        }

        if ($rating && $rating >= 1 && $rating <= 5) {
            $qb->andWhere('r.rating = :rating')
                ->setParameter('rating', $rating);
        }

        if ($verified !== null) {
            $qb->andWhere('r.isVerified = :verified')
                ->setParameter('verified', $verified);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Найти отзывы по автомобилю
     */
    public function findByCar(Car $car, int $page = 1, int $perPage = 10): array
    {
        return $this->findReviews(
            carId: $car->getId(),
            page: $page,
            perPage: $perPage
        );
    }

    /**
     * Найти отзывы по пользователю
     */
    public function findByUser(User $user, int $page = 1, int $perPage = 10): array
    {
        return $this->findReviews(
            userId: $user->getId(),
            page: $page,
            perPage: $perPage
        );
    }

    /**
     * Получить средний рейтинг автомобиля
     */
    public function getCarAverageRating(Car $car): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('AVG(r.rating) as average, COUNT(r.id) as total')
            ->where('r.car = :car')
            ->setParameter('car', $car);

        $result = $qb->getQuery()->getSingleResult();

        return [
            'average' => round((float) $result['average'], 1),
            'total' => (int) $result['total'],
        ];
    }

    /**
     * Получить распределение рейтингов для автомобиля
     */
    public function getRatingDistribution(Car $car): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r.rating, COUNT(r.id) as count')
            ->where('r.car = :car')
            ->groupBy('r.rating')
            ->setParameter('car', $car);

        $results = $qb->getQuery()->getResult();

        $distribution = array_fill(1, 5, 0);
        foreach ($results as $result) {
            $distribution[$result['rating']] = (int) $result['count'];
        }

        return $distribution;
    }

    /**
     * Найти последние отзывы
     */
    public function findLatest(int $limit = 10): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.car', 'c')
            ->addSelect('u')
            ->addSelect('c')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Найти отзывы с высоким рейтингом
     */
    public function findTopRated(int $limit = 10, int $minRating = 4): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.car', 'c')
            ->addSelect('u')
            ->addSelect('c')
            ->where('r.rating >= :minRating')
            ->andWhere('r.isVerified = true')
            ->setParameter('minRating', $minRating)
            ->orderBy('r.rating', 'DESC')
            ->addOrderBy('r.helpfulCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Найти отзывы, требующие проверки
     */
    public function findUnverified(int $page = 1, int $perPage = 10): array
    {
        return $this->findReviews(
            verified: false,
            page: $page,
            perPage: $perPage,
            sort: 'newest'
        );
    }

    /**
     * Проверить, существует ли отзыв от пользователя на бронирование
     */
    public function existsForBookingAndUser(int $bookingId, int $userId): bool
    {
        $result = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.booking = :bookingId')
            ->andWhere('r.user = :userId')
            ->setParameter('bookingId', $bookingId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result > 0;
    }

    /**
     * Найти отзыв по бронированию и пользователю
     */
    public function findByBookingAndUser(int $bookingId, int $userId): ?Review
    {
        return $this->createQueryBuilder('r')
            ->where('r.booking = :bookingId')
            ->andWhere('r.user = :userId')
            ->setParameter('bookingId', $bookingId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Удалить все отзывы пользователя
     */
    public function deleteAllByUser(User $user): int
    {
        return $this->createQueryBuilder('r')
            ->delete()
            ->where('r.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    /**
     * Удалить все отзывы об автомобиле
     */
    public function deleteAllByCar(Car $car): int
    {
        return $this->createQueryBuilder('r')
            ->delete()
            ->where('r.car = :car')
            ->setParameter('car', $car)
            ->getQuery()
            ->execute();
    }

    /**
     * Получить статистику по отзывам
     */
    public function getStatistics(): array
    {
        $qb = $this->createQueryBuilder('r');

        $total = $qb->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $verified = $qb->select('COUNT(r.id)')
            ->where('r.isVerified = true')
            ->getQuery()
            ->getSingleScalarResult();

        $avgRating = $qb->select('AVG(r.rating)')
            ->where('r.isVerified = true')
            ->getQuery()
            ->getSingleScalarResult();

        $ratings = $this->createQueryBuilder('r')
            ->select('r.rating, COUNT(r.id) as count')
            ->groupBy('r.rating')
            ->getQuery()
            ->getResult();

        $ratingDistribution = array_fill(1, 5, 0);
        foreach ($ratings as $rating) {
            $ratingDistribution[$rating['rating']] = (int) $rating['count'];
        }

        return [
            'total' => (int) $total,
            'verified' => (int) $verified,
            'unverified' => (int) $total - (int) $verified,
            'averageRating' => round((float) $avgRating, 1),
            'ratingDistribution' => $ratingDistribution,
        ];
    }

    /**
     * Обновить количество полезных отметок
     */
    public function incrementHelpfulCount(int $reviewId): int
    {
        $qb = $this->createQueryBuilder('r')
            ->update()
            ->set('r.helpfulCount', 'r.helpfulCount + 1')
            ->where('r.id = :id')
            ->setParameter('id', $reviewId);

        return $qb->getQuery()->execute();
    }

    /**
     * Уменьшить количество полезных отметок
     */
    public function decrementHelpfulCount(int $reviewId): int
    {
        $qb = $this->createQueryBuilder('r')
            ->update()
            ->set('r.helpfulCount', 'r.helpfulCount - 1')
            ->where('r.id = :id')
            ->andWhere('r.helpfulCount > 0')
            ->setParameter('id', $reviewId);

        return $qb->getQuery()->execute();
    }

    /**
     * Поиск отзывов по тексту
     */
    public function searchByText(string $searchTerm, int $page = 1, int $perPage = 10): array
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.car', 'c')
            ->addSelect('u')
            ->addSelect('c')
            ->where('r.title LIKE :search')
            ->orWhere('r.comment LIKE :search')
            ->orWhere('r.pros LIKE :search')
            ->orWhere('r.cons LIKE :search')
            ->orWhere('u.name LIKE :search')
            ->orWhere('c.brand LIKE :search')
            ->orWhere('c.model LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('r.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $query = $qb->getQuery();
        $items = $query->getResult();

        $totalQb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.car', 'c')
            ->where('r.title LIKE :search')
            ->orWhere('r.comment LIKE :search')
            ->orWhere('r.pros LIKE :search')
            ->orWhere('r.cons LIKE :search')
            ->orWhere('u.name LIKE :search')
            ->orWhere('c.brand LIKE :search')
            ->orWhere('c.model LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%');

        $total = (int) $totalQb->getQuery()->getSingleScalarResult();

        return [
            'data' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage),
        ];
    }

    /**
     * Получить все отзывы с пагинацией (для админки)
     */
    public function findAllPaginated(int $page = 1, int $perPage = 10): array
    {
        return $this->findReviews(page: $page, perPage: $perPage);
    }

    /**
     * Получить Qb для фильтрации (для использования в других сервисах)
     */
    public function getFilteredQuery(array $filters = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.car', 'c')
            ->addSelect('u')
            ->addSelect('c');

        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            switch ($key) {
                case 'carId':
                    $qb->andWhere('c.id = :carId')
                        ->setParameter('carId', $value);
                    break;
                case 'userId':
                    $qb->andWhere('u.id = :userId')
                        ->setParameter('userId', $value);
                    break;
                case 'rating':
                    if ($value >= 1 && $value <= 5) {
                        $qb->andWhere('r.rating = :rating')
                            ->setParameter('rating', $value);
                    }
                    break;
                case 'verified':
                    $qb->andWhere('r.isVerified = :verified')
                        ->setParameter('verified', (bool) $value);
                    break;
                case 'minRating':
                    $qb->andWhere('r.rating >= :minRating')
                        ->setParameter('minRating', $value);
                    break;
                case 'maxRating':
                    $qb->andWhere('r.rating <= :maxRating')
                        ->setParameter('maxRating', $value);
                    break;
                case 'dateFrom':
                    $qb->andWhere('r.createdAt >= :dateFrom')
                        ->setParameter('dateFrom', new \DateTime($value));
                    break;
                case 'dateTo':
                    $qb->andWhere('r.createdAt <= :dateTo')
                        ->setParameter('dateTo', new \DateTime($value));
                    break;
            }
        }

        return $qb;
    }
}
