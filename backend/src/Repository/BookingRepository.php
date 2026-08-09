<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * Находит бронирования по пользователю
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит бронирования по статусу
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.status = :status')
            ->setParameter('status', $status)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит бронирования по периоду
     */
    public function findByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.pickupDate BETWEEN :start AND :end')
            ->orWhere('b.dropoffDate BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('b.pickupDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит активные бронирования (подтвержденные и в процессе)
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.status IN (:statuses)')
            ->setParameter('statuses', ['confirmed', 'in_progress'])
            ->orderBy('b.pickupDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит завершенные бронирования
     */
    public function findCompleted(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.status = :status')
            ->setParameter('status', 'completed')
            ->orderBy('b.dropoffDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит бронирования по номеру
     */
    public function findByBookingNumber(string $bookingNumber): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->where('b.bookingNumber = :bookingNumber')
            ->setParameter('bookingNumber', $bookingNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Находит бронирования, требующие подтверждения
     */
    public function findPending(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('b.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит бронирования по автомобилю
     */
    public function findByCar(int $carId): array
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.bookingItems', 'bi')
            ->innerJoin('bi.car', 'c')
            ->where('c.id = :carId')
            ->setParameter('carId', $carId)
            ->orderBy('b.pickupDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверяет доступность автомобиля на указанный период
     */
    public function isCarAvailable(int $carId, \DateTimeInterface $pickupDate, \DateTimeInterface $dropoffDate): bool
    {
        $result = $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->innerJoin('b.bookingItems', 'bi')
            ->where('bi.car = :carId')
            ->andWhere('b.status IN (:statuses)')
            ->andWhere('b.pickupDate <= :dropoffDate')
            ->andWhere('b.dropoffDate >= :pickupDate')
            ->setParameter('carId', $carId)
            ->setParameter('statuses', ['confirmed', 'in_progress', 'pending'])
            ->setParameter('pickupDate', $pickupDate)
            ->setParameter('dropoffDate', $dropoffDate)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result === 0;
    }

    /**
     * Получает статистику по бронированиям
     */
    public function getStatistics(): array
    {
        return $this->createQueryBuilder('b')
            ->select(
                'COUNT(b.id) as total',
                'SUM(CASE WHEN b.status = :pending THEN 1 ELSE 0 END) as pending',
                'SUM(CASE WHEN b.status = :confirmed THEN 1 ELSE 0 END) as confirmed',
                'SUM(CASE WHEN b.status = :in_progress THEN 1 ELSE 0 END) as in_progress',
                'SUM(CASE WHEN b.status = :completed THEN 1 ELSE 0 END) as completed',
                'SUM(CASE WHEN b.status = :cancelled THEN 1 ELSE 0 END) as cancelled',
                'SUM(b.totalAmount) as total_revenue'
            )
            ->setParameter('pending', 'pending')
            ->setParameter('confirmed', 'confirmed')
            ->setParameter('in_progress', 'in_progress')
            ->setParameter('completed', 'completed')
            ->setParameter('cancelled', 'cancelled')
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Получает статистику по месяцам
     */
    public function getMonthlyStatistics(int $year): array
    {
        return $this->createQueryBuilder('b')
            ->select(
                'MONTH(b.createdAt) as month',
                'COUNT(b.id) as total',
                'SUM(b.totalAmount) as revenue'
            )
            ->where('YEAR(b.createdAt) = :year')
            ->andWhere('b.status = :status')
            ->setParameter('year', $year)
            ->setParameter('status', 'completed')
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Поиск бронирований
     */
    public function search(array $criteria): array
    {
        $qb = $this->createQueryBuilder('b');

        if (isset($criteria['bookingNumber'])) {
            $qb->andWhere('b.bookingNumber LIKE :bookingNumber')
                ->setParameter('bookingNumber', '%' . $criteria['bookingNumber'] . '%');
        }

        if (isset($criteria['userName'])) {
            $qb->innerJoin('b.user', 'u')
                ->andWhere('u.name LIKE :userName')
                ->setParameter('userName', '%' . $criteria['userName'] . '%');
        }

        if (isset($criteria['userEmail'])) {
            $qb->innerJoin('b.user', 'u')
                ->andWhere('u.email LIKE :userEmail')
                ->setParameter('userEmail', '%' . $criteria['userEmail'] . '%');
        }

        if (isset($criteria['status'])) {
            $qb->andWhere('b.status = :status')
                ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['startDate'])) {
            $qb->andWhere('b.pickupDate >= :startDate')
                ->setParameter('startDate', $criteria['startDate']);
        }

        if (isset($criteria['endDate'])) {
            $qb->andWhere('b.dropoffDate <= :endDate')
                ->setParameter('endDate', $criteria['endDate']);
        }

        if (isset($criteria['carId'])) {
            $qb->innerJoin('b.bookingItems', 'bi')
                ->andWhere('bi.car = :carId')
                ->setParameter('carId', $criteria['carId']);
        }

        $qb->orderBy('b.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Получает количество бронирований по дням
     */
    public function getDailyStats(int $days = 30): array
    {
        $startDate = new \DateTimeImmutable("-$days days");

        return $this->createQueryBuilder('b')
            ->select('DATE(b.createdAt) as date', 'COUNT(b.id) as count')
            ->where('b.createdAt >= :startDate')
            ->setParameter('startDate', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает топ пользователей по количеству бронирований
     */
    public function getTopUsers(int $limit = 10): array
    {
        return $this->createQueryBuilder('b')
            ->select('u.id', 'u.name', 'u.email', 'COUNT(b.id) as booking_count')
            ->innerJoin('b.user', 'u')
            ->groupBy('u.id')
            ->orderBy('booking_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает статистику по бронированиям за период.
     */
    public function getBookingStatistics(int $days = 7): array
    {
        $endDate = new \DateTimeImmutable('today');
        $startDate = $endDate->modify("-{$days} days")->setTime(0, 0, 0);

        // Всего заказов, кроме отменённых
        $totalOrders = (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.status != :cancelled')
            ->setParameter('cancelled', 'cancelled')
            ->getQuery()
            ->getSingleScalarResult();

        // Новые заказы за текущий период
        $newOrders = (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.createdAt >= :start')
            ->andWhere('b.createdAt < :end')
            ->andWhere('b.status != :cancelled')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('cancelled', 'cancelled')
            ->getQuery()
            ->getSingleScalarResult();

        // Предыдущий аналогичный период
        $previousStart = $startDate->modify("-{$days} days");

        $previousOrders = (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.createdAt >= :start')
            ->andWhere('b.createdAt < :end')
            ->andWhere('b.status != :cancelled')
            ->setParameter('start', $previousStart)
            ->setParameter('end', $startDate)
            ->setParameter('cancelled', 'cancelled')
            ->getQuery()
            ->getSingleScalarResult();

        // Рост
        $growthPercentage = 0.0;

        if ($previousOrders > 0) {
            $growthPercentage = (
                    ($newOrders - $previousOrders) / $previousOrders
                ) * 100;
        } elseif ($newOrders > 0) {
            $growthPercentage = 100.0;
        }

        // Тренд
        if ($growthPercentage > 5) {
            $trend = 'up';
        } elseif ($growthPercentage < -5) {
            $trend = 'down';
        } else {
            $trend = 'stable';
        }

        // Статистика по дням
        $dailyStats = $this->getBookingDailyStats($days);

        return [
            'total_orders' => $totalOrders,
            'new_orders' => $newOrders,
            'previous_new_orders' => $previousOrders,
            'growth_percentage' => round($growthPercentage, 2),
            'trend' => $trend,
            'daily_stats' => $dailyStats,
            'period_days' => $days,
            'period_start' => $startDate->format('Y-m-d'),
            'period_end' => $endDate->format('Y-m-d'),
        ];
    }

    /**
     * Получает количество заказов по дням.
     */
    public function getBookingDailyStats(int $days = 30): array
    {
        $endDate = new \DateTimeImmutable('today');
        $startDate = $endDate
            ->modify("-{$days} days")
            ->setTime(0, 0, 0);

        $sql = <<<SQL
        SELECT
            DATE(created_at) AS date,
            COUNT(id) AS count
        FROM bookings
        WHERE created_at >= :start
          AND created_at < :end
          AND status != :cancelled
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at) ASC
    SQL;

        $connection = $this->getEntityManager()->getConnection();

        $result = $connection->executeQuery($sql, [
            'start' => $startDate->format('Y-m-d H:i:s'),
            'end' => $endDate->format('Y-m-d H:i:s'),
            'cancelled' => 'cancelled',
        ])->fetchAllAssociative();

        $stats = [];

        foreach ($result as $row) {
            $stats[$row['date']] = (int) $row['count'];
        }

        $dailyStats = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate
                ->modify("+{$i} days")
                ->format('Y-m-d');

            $dailyStats[] = [
                'date' => $date,
                'count' => $stats[$date] ?? 0,
            ];
        }

        return $dailyStats;
    }

    public function getMonthlyBookingStats(int $year): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
        SELECT
            EXTRACT(MONTH FROM created_at)::integer AS month,
            COUNT(id) AS count
        FROM bookings
        WHERE created_at >= :start
          AND created_at < :end
          AND status != :cancelled
        GROUP BY EXTRACT(MONTH FROM created_at)
        ORDER BY month
    SQL;

        $result = $connection->executeQuery($sql, [
            'start' => "$year-01-01 00:00:00",
            'end' => ($year + 1) . '-01-01 00:00:00',
            'cancelled' => 'cancelled',
        ])->fetchAllAssociative();

        $stats = array_fill(1, 12, 0);

        foreach ($result as $row) {
            $stats[(int) $row['month']] = (int) $row['count'];
        }

        return array_values($stats);
    }

    public function getBookingStatisticsByPeriod(
        string $period,
        ?string $customStart = null,
        ?string $customEnd = null
    ): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $today = new \DateTimeImmutable('today');
        $year = (int) $today->format('Y');

        switch ($period) {
            case 'month':
                $start = $today->modify('first day of this month')->setTime(0, 0);
                $end = $start->modify('+1 month');

                $daysInMonth = (int) $end->modify('-1 day')->format('d');

                // FACT
                $factResult = $connection->executeQuery(
                    <<<SQL
                SELECT
                    EXTRACT(DAY FROM created_at)::integer AS period,
                    COUNT(id) AS count
                FROM bookings
                WHERE created_at >= :start
                  AND created_at < :end
                  AND status != :cancelled
                GROUP BY EXTRACT(DAY FROM created_at)
                ORDER BY period
                SQL,
                    [
                        'start' => $start->format('Y-m-d H:i:s'),
                        'end' => $end->format('Y-m-d H:i:s'),
                        'cancelled' => 'cancelled',
                    ]
                )->fetchAllAssociative();

                // PLAN
                $planResult = $connection->executeQuery(
                    <<<SQL
                SELECT
                    EXTRACT(DAY FROM date)::integer AS period,
                    bookings AS count
                FROM booking_plan
                WHERE date >= :start
                  AND date < :end
                ORDER BY date
                SQL,
                    [
                        'start' => $start->format('Y-m-d'),
                        'end' => $end->format('Y-m-d'),
                    ]
                )->fetchAllAssociative();

                $fact = array_fill(1, $daysInMonth, 0);
                $plan = array_fill(1, $daysInMonth, 0);

                foreach ($factResult as $row) {
                    $fact[(int) $row['period']] = (int) $row['count'];
                }

                foreach ($planResult as $row) {
                    $plan[(int) $row['period']] = (int) $row['count'];
                }

                return [
                    [
                        'name' => 'plan',
                        'data' => array_values($plan),
                    ],
                    [
                        'name' => 'fact',
                        'data' => array_values($fact),
                    ],
                ];

            case 'quarter':
                $start = new \DateTimeImmutable("$year-01-01 00:00:00");
                $end = $start->modify('+1 year');

                // FACT
                $factResult = $connection->executeQuery(
                    <<<SQL
                SELECT
                    EXTRACT(QUARTER FROM created_at)::integer AS period,
                    COUNT(id) AS count
                FROM bookings
                WHERE created_at >= :start
                  AND created_at < :end
                  AND status != :cancelled
                GROUP BY EXTRACT(QUARTER FROM created_at)
                ORDER BY period
                SQL,
                    [
                        'start' => $start->format('Y-m-d H:i:s'),
                        'end' => $end->format('Y-m-d H:i:s'),
                        'cancelled' => 'cancelled',
                    ]
                )->fetchAllAssociative();

                // PLAN
                $planResult = $connection->executeQuery(
                    <<<SQL
                SELECT
                    EXTRACT(QUARTER FROM date)::integer AS period,
                    SUM(bookings) AS count
                FROM booking_plan
                WHERE date >= :start
                  AND date < :end
                GROUP BY EXTRACT(QUARTER FROM date)
                ORDER BY period
                SQL,
                    [
                        'start' => $start->format('Y-m-d'),
                        'end' => $end->format('Y-m-d'),
                    ]
                )->fetchAllAssociative();

                $fact = array_fill(1, 4, 0);
                $plan = array_fill(1, 4, 0);

                foreach ($factResult as $row) {
                    $fact[(int) $row['period']] = (int) $row['count'];
                }

                foreach ($planResult as $row) {
                    $plan[(int) $row['period']] = (int) $row['count'];
                }

                return [
                    [
                        'name' => 'plan',
                        'data' => array_values($plan),
                    ],
                    [
                        'name' => 'fact',
                        'data' => array_values($fact),
                    ],
                ];

            case 'year':
                $start = new \DateTimeImmutable("$year-01-01 00:00:00");
                $end = $start->modify('+1 year');

                // FACT
                $factResult = $connection->executeQuery(
                    <<<SQL
                SELECT
                    EXTRACT(MONTH FROM created_at)::integer AS period,
                    COUNT(id) AS count
                FROM bookings
                WHERE created_at >= :start
                  AND created_at < :end
                  AND status != :cancelled
                GROUP BY EXTRACT(MONTH FROM created_at)
                ORDER BY period
                SQL,
                    [
                        'start' => $start->format('Y-m-d H:i:s'),
                        'end' => $end->format('Y-m-d H:i:s'),
                        'cancelled' => 'cancelled',
                    ]
                )->fetchAllAssociative();

                // PLAN
                $planResult = $connection->executeQuery(
                    <<<SQL
                SELECT
                    EXTRACT(MONTH FROM date)::integer AS period,
                    SUM(bookings) AS count
                FROM booking_plan
                WHERE date >= :start
                  AND date < :end
                GROUP BY EXTRACT(MONTH FROM date)
                ORDER BY period
                SQL,
                    [
                        'start' => $start->format('Y-m-d'),
                        'end' => $end->format('Y-m-d'),
                    ]
                )->fetchAllAssociative();

                $fact = array_fill(1, 12, 0);
                $plan = array_fill(1, 12, 0);

                foreach ($factResult as $row) {
                    $fact[(int) $row['period']] = (int) $row['count'];
                }

                foreach ($planResult as $row) {
                    $plan[(int) $row['period']] = (int) $row['count'];
                }

                return [
                    [
                        'name' => 'plan',
                        'data' => array_values($plan),
                    ],
                    [
                        'name' => 'fact',
                        'data' => array_values($fact),
                    ],
                ];

            case 'custom':
                if (!$customStart || !$customEnd) {
                    throw new \InvalidArgumentException(
                        'For custom period start and end are required'
                    );
                }

                $start = new \DateTimeImmutable($customStart . ' 00:00:00');
                $end = new \DateTimeImmutable($customEnd . ' 00:00:00');

                // включаем конечный день
                $endExclusive = $end->modify('+1 day');

                $days = (int) $start->diff($endExclusive)->days;

                // FACT
                $factResult = $connection->executeQuery(
                    <<<SQL
        SELECT
            DATE(created_at) AS period,
            COUNT(id) AS count
        FROM bookings
        WHERE created_at >= :start
          AND created_at < :end
          AND status != :cancelled
        GROUP BY DATE(created_at)
        ORDER BY period
        SQL,
                    [
                        'start' => $start->format('Y-m-d H:i:s'),
                        'end' => $endExclusive->format('Y-m-d H:i:s'),
                        'cancelled' => 'cancelled',
                    ]
                )->fetchAllAssociative();

                // PLAN
                $planResult = $connection->executeQuery(
                    <<<SQL
        SELECT
            date AS period,
            bookings AS count
        FROM booking_plan
        WHERE date >= :start
          AND date < :end
        ORDER BY date
        SQL,
                    [
                        'start' => $start->format('Y-m-d'),
                        'end' => $endExclusive->format('Y-m-d'),
                    ]
                )->fetchAllAssociative();

                $fact = array_fill(0, $days, 0);
                $plan = array_fill(0, $days, 0);

                $startDate = $start;

                foreach ($factResult as $row) {
                    $date = new \DateTimeImmutable($row['period']);
                    $index = (int) $startDate->diff($date)->days;

                    if ($index >= 0 && $index < $days) {
                        $fact[$index] = (int) $row['count'];
                    }
                }

                foreach ($planResult as $row) {
                    $date = new \DateTimeImmutable($row['period']);
                    $index = (int) $startDate->diff($date)->days;

                    if ($index >= 0 && $index < $days) {
                        $plan[$index] = (int) $row['count'];
                    }
                }

                return [
                    [
                        'name' => 'plan',
                        'data' => $plan,
                    ],
                    [
                        'name' => 'fact',
                        'data' => $fact,
                    ],
                ];

            default:
                throw new \InvalidArgumentException(
                    'Invalid period. Allowed: month, quarter, year'
                );
        }
    }
}
