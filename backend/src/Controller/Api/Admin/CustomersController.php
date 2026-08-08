<?php

namespace App\Controller\Api\Admin;

use App\DTO\Api\Admin\CustomersStatisticsDto;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/admin/customers')]
class CustomersController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SerializerInterface $serializer
    ) {
    }

    #[Route('/statistics', name: 'api_admin_customers_statistics', methods: ['GET'])]
    public function getStatistics(Request $request): JsonResponse
    {
        // Получаем количество дней из запроса (по умолчанию 7)
        $days = $request->query->getInt('days', 7);

        // Ограничиваем максимальное значение
        if ($days > 90) {
            $days = 90;
        }
        if ($days < 1) {
            $days = 1;
        }

        try {
            $stats = $this->userRepository->getCustomersStatistics($days);

            // Создаем DTO
            $dto = new CustomersStatisticsDto(
                totalCustomers: $stats['total_customers'],
                newCustomers: $stats['new_customers'],
                growthPercentage: $stats['growth_percentage'],
                trend: $stats['trend'],
                dailyStats: $stats['daily_stats']
            );

            // Добавляем дополнительную информацию в ответ
            $response = [
                'data' => $dto,
                'meta' => [
                    'period_days' => $stats['period_days'],
                    'period_start' => $stats['period_start'],
                    'period_end' => $stats['period_end'],
                    'previous_new_customers' => $stats['previous_new_customers'],
                ]
            ];

            return $this->json($response, Response::HTTP_OK, [], [
                'groups' => ['admin:statistics']
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Failed to get customers statistics',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/count', name: 'api_admin_customers_count', methods: ['GET'])]
    public function getCount(): JsonResponse
    {
        try {
            $count = $this->userRepository->countCustomers();

            return $this->json([
                'total_customers' => $count,
                'timestamp' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Failed to get customers count',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/growth', name: 'api_admin_customers_growth', methods: ['GET'])]
    public function getGrowth(Request $request): JsonResponse
    {
        $days = $request->query->getInt('days', 7);

        if ($days > 90) {
            $days = 90;
        }
        if ($days < 1) {
            $days = 1;
        }

        try {
            $stats = $this->userRepository->getCustomersStatistics($days);

            return $this->json([
                'data' => [
                    'new_customers' => $stats['new_customers'],
                    'previous_new_customers' => $stats['previous_new_customers'],
                    'growth_percentage' => $stats['growth_percentage'],
                    'trend' => $stats['trend'],
                    'total_customers' => $stats['total_customers'],
                ],
                'period' => [
                    'days' => $stats['period_days'],
                    'start' => $stats['period_start'],
                    'end' => $stats['period_end'],
                ],
                'daily_stats' => $stats['daily_stats']
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Failed to get customers growth',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
