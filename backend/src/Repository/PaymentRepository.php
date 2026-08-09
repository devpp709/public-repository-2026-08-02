<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * Находит платежи по бронированию
     */
    public function findByBookingId(int $bookingId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.booking = :bookingId')
            ->setParameter('bookingId', $bookingId)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит платежи по статусу
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', $status)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит успешные платежи
     */
    public function findSuccessful(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', 'paid')
            ->orderBy('p.paymentDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит платежи за период
     */
    public function findByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.paymentDate BETWEEN :start AND :end')
            ->andWhere('p.status = :status')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('status', 'paid')
            ->orderBy('p.paymentDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает общую сумму платежей за период
     */
    public function getTotalByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): float
    {
        $result = $this->createQueryBuilder('p')
            ->select('SUM(p.amount) as total')
            ->where('p.paymentDate BETWEEN :start AND :end')
            ->andWhere('p.status = :status')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('status', 'paid')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    /**
     * Получает статистику по платежам
     */
    public function getStatistics(): array
    {
        return $this->createQueryBuilder('p')
            ->select(
                'COUNT(p.id) as total',
                'SUM(CASE WHEN p.status = :pending THEN 1 ELSE 0 END) as pending',
                'SUM(CASE WHEN p.status = :paid THEN 1 ELSE 0 END) as paid',
                'SUM(CASE WHEN p.status = :failed THEN 1 ELSE 0 END) as failed',
                'SUM(CASE WHEN p.status = :refunded THEN 1 ELSE 0 END) as refunded',
                'SUM(CASE WHEN p.status = :paid THEN p.amount ELSE 0 END) as total_revenue'
            )
            ->setParameter('pending', 'pending')
            ->setParameter('paid', 'paid')
            ->setParameter('failed', 'failed')
            ->setParameter('refunded', 'refunded')
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Получает статистику по методам оплаты
     */
    public function getPaymentMethodStatistics(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.paymentMethod', 'COUNT(p.id) as count', 'SUM(p.amount) as total')
            ->where('p.status = :status')
            ->setParameter('status', 'paid')
            ->groupBy('p.paymentMethod')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получает статистику по месяцам
     */
    public function getMonthlyStatistics(int $year): array
    {
        return $this->createQueryBuilder('p')
            ->select(
                'MONTH(p.paymentDate) as month',
                'COUNT(p.id) as total',
                'SUM(p.amount) as revenue'
            )
            ->where('YEAR(p.paymentDate) = :year')
            ->andWhere('p.status = :status')
            ->setParameter('year', $year)
            ->setParameter('status', 'paid')
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Находит платеж по транзакции
     */
    public function findByTransactionId(string $transactionId): ?Payment
    {
        return $this->createQueryBuilder('p')
            ->where('p.transactionId = :transactionId')
            ->setParameter('transactionId', $transactionId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Проверяет, есть ли успешный платеж для бронирования
     */
    public function hasSuccessfulPayment(int $bookingId): bool
    {
        $result = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.booking = :bookingId')
            ->andWhere('p.status = :status')
            ->setParameter('bookingId', $bookingId)
            ->setParameter('status', 'paid')
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result > 0;
    }

    /**
     * Получает общую сумму оплат по бронированию
     */
    public function getTotalPaidForBooking(int $bookingId): float
    {
        $result = $this->createQueryBuilder('p')
            ->select('SUM(p.amount) as total')
            ->where('p.booking = :bookingId')
            ->andWhere('p.status = :status')
            ->setParameter('bookingId', $bookingId)
            ->setParameter('status', 'paid')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.booking', 'b')
            ->addSelect('b')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
