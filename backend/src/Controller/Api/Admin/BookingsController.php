<?php

namespace App\Controller\Api\Admin;

use App\DTO\Api\Admin\BookingsStatisticsDto;
use App\DTO\Booking\LatestBookingDTO;
use App\Repository\BookingRepository;
use App\Service\BookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/bookings')]
class BookingsController extends AbstractController
{
    public function __construct(
        private readonly BookingRepository $bookingRepository,
        private readonly BookingService $bookingService,
    ) {
    }

    #[Route('/statistics', methods: ['GET'])]
    public function statistics(Request $request): JsonResponse
    {
        $days = (int) $request->query->get('days', 7);

        if (!in_array($days, [7, 30], true)) {
            return $this->json([
                'error' => 'Days must be 7 or 30',
            ], 400);
        }

        $statistics = $this->bookingRepository->getBookingStatistics($days);

        $dto = new BookingsStatisticsDto(
            $statistics['total_orders'],
            $statistics['new_orders'],
            (float) $statistics['growth_percentage'],
            $statistics['trend'],
            $statistics['daily_stats'],
        );

        return $this->json([
            'data' => [
                'totalOrders' => $dto->getTotalOrders(),
                'newOrders' => $dto->getNewOrders(),
                'growthPercentage' => $dto->getGrowthPercentage(),
                'trend' => $dto->getTrend(),
                'dailyStats' => $dto->getDailyStats(),
            ],
            'meta' => [
                'period_days' => $statistics['period_days'],
                'period_start' => $statistics['period_start'],
                'period_end' => $statistics['period_end'],
                'previous_new_orders' => $statistics['previous_new_orders'],
            ],
        ]);
    }

    #[Route('/statistics/monthly', methods: ['GET'])]
    public function monthlyStatistics(
        Request $request,
        BookingRepository $bookingRepository
    ): JsonResponse {
        $year = $request->query->getInt(
            'year',
            (int) date('Y')
        );

        return $this->json([
            'data' => $bookingRepository->getMonthlyBookingStats($year),
            'year' => $year,
        ]);
    }

    #[Route('/statistics/chart', methods: ['GET'])]
    public function statisticsChart(Request $request): JsonResponse
    {
        $period = $request->query->get('period', 'month');

        if (!in_array($period, ['month', 'quarter', 'year', 'custom'], true)) {
            return $this->json([
                'error' => 'Invalid period',
            ], 400);
        }

        $start = $request->query->get('start');
        $end = $request->query->get('end');

        return $this->json([
            'data' => $this->bookingRepository->getBookingStatisticsByPeriod(
                $period,
                $start,
                $end
            ),
            'period' => $period,
        ]);
    }

    #[Route('/statistics/regions', name: 'statistics_regions', methods: ['GET'])]
    public function regionStatistics(): JsonResponse
    {
        $statistics = $this->bookingRepository->getRegionStatistics();

        return $this->json([
            'data' => $statistics,
        ]);
    }

    #[Route('/latest', methods: ['GET'])]
    public function latest(Request $request): JsonResponse
    {
        $limit = max(1, min(
            50,
            (int) $request->query->get('limit', 10)
        ));

        $bookings = $this->bookingRepository->findLatest($limit);

        return $this->json([
            'success' => true,
            'data' => LatestBookingDTO::fromEntities($bookings),
        ]);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->json([
            'data' => $this->bookingService->getAllBookingsWithDetails(),
        ]);
    }

}
