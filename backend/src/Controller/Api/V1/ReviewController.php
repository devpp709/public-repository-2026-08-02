<?php

namespace App\Controller\Api\V1;

use App\DTO\Review\ReviewRequestDTO;
use App\Service\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1')]
class ReviewController extends AbstractController
{
    public function __construct(
        private readonly ReviewService $reviewService
    ) {
    }

    /**
     * Получить все отзывы
     */
    #[Route('/reviews', name: 'api_reviews_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $type = $request->query->get('type');
        $limit = $request->query->getInt('limit', 10);

        if ($type === 'top') {
            $reviews = $this->reviewService->getTopRatedReviews($limit);
        } elseif ($type === 'helpful') {
            $reviews = $this->reviewService->getMostHelpfulReviews($limit);
        } elseif ($type === 'latest') {
            $reviews = $this->reviewService->getLatestReviews($limit);
        } else {
            $reviews = $this->reviewService->getAllReviews();
        }

        return $this->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Получить отзывы по автомобилю
     */
    #[Route('/cars/{carId}/reviews', name: 'api_car_reviews_list', methods: ['GET'])]
    public function getCarReviews(int $carId): JsonResponse
    {
        $reviews = $this->reviewService->getReviewsByCarId($carId);

        return $this->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Получить статистику по отзывам для автомобиля
     */
    #[Route('/cars/{carId}/reviews/statistics', name: 'api_car_reviews_statistics', methods: ['GET'])]
    public function getCarReviewsStatistics(int $carId): JsonResponse
    {
        $statistics = $this->reviewService->getStatisticsForCar($carId);

        return $this->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Получить отзывы по пользователю
     */
    #[Route('/users/{userId}/reviews', name: 'api_user_reviews_list', methods: ['GET'])]
    public function getUserReviews(int $userId): JsonResponse
    {
        $reviews = $this->reviewService->getReviewsByUserId($userId);

        return $this->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Получить глобальную статистику по отзывам
     */
    #[Route('/reviews/statistics', name: 'api_reviews_global_statistics', methods: ['GET'])]
    public function getGlobalStatistics(): JsonResponse
    {
        $statistics = $this->reviewService->getGlobalStatistics();
        $distribution = $this->reviewService->getRatingDistribution();

        return $this->json([
            'success' => true,
            'data' => [
                'general' => $statistics,
                'distribution' => $distribution
            ]
        ]);
    }

    /**
     * Получить отзыв по ID
     */
    #[Route('/reviews/{id}', name: 'api_reviews_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $review = $this->reviewService->getReviewById($id);

            return $this->json([
                'success' => true,
                'data' => $review
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Создать отзыв
     */
    #[Route('/reviews', name: 'api_reviews_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] ReviewRequestDTO $dto): JsonResponse
    {
        try {
            $review = $this->reviewService->createReview($dto);

            return $this->json([
                'success' => true,
                'message' => 'Отзыв успешно создан',
                'data' => $review
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Обновить отзыв
     */
    #[Route('/reviews/{id}', name: 'api_reviews_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, #[MapRequestPayload] ReviewRequestDTO $dto): JsonResponse
    {
        try {
            $review = $this->reviewService->updateReview($id, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Отзыв обновлен',
                'data' => $review
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Удалить отзыв
     */
    #[Route('/reviews/{id}', name: 'api_reviews_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->reviewService->deleteReview($id);

            return $this->json([
                'success' => true,
                'message' => 'Отзыв удален'
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Отметить отзыв как полезный
     */
    #[Route('/reviews/{id}/helpful', name: 'api_reviews_helpful', methods: ['POST'])]
    public function markHelpful(int $id): JsonResponse
    {
        try {
            $review = $this->reviewService->markHelpful($id);

            return $this->json([
                'success' => true,
                'message' => 'Отзыв отмечен как полезный',
                'data' => $review
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Убрать отметку "полезный"
     */
    #[Route('/reviews/{id}/unhelpful', name: 'api_reviews_unhelpful', methods: ['POST'])]
    public function unmarkHelpful(int $id): JsonResponse
    {
        try {
            $review = $this->reviewService->unmarkHelpful($id);

            return $this->json([
                'success' => true,
                'message' => 'Отметка "полезный" убрана',
                'data' => $review
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Проверить, оставлял ли пользователь отзыв
     */
    #[Route('/bookings/{bookingId}/reviews/check/{userId}', name: 'api_reviews_check', methods: ['GET'])]
    public function checkReview(int $bookingId, int $userId): JsonResponse
    {
        $hasReview = $this->reviewService->hasReview($bookingId, $userId);

        return $this->json([
            'success' => true,
            'has_review' => $hasReview
        ]);
    }
}
