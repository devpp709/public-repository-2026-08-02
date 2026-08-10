<?php

namespace App\Controller\Api\Admin;

use App\DTO\Review\ReviewRequestDTO;
use App\Service\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/reviews', name: 'api_admin_reviews_')]
class ReviewsController extends AbstractController
{
    public function __construct(
        private readonly ReviewService $reviewService
    ) {
    }

    /**
     * Получить все отзывы
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        // Получаем параметры из запроса
        $carId = $request->query->getInt('carId', 0);
        $userId = $request->query->getInt('userId', 0);
        $rating = $request->query->getInt('rating', 0);
        $verified = $request->query->get('verified');
        $page = $request->query->getInt('page', 1);
        $perPage = $request->query->getInt('perPage', 10);
        $sort = $request->query->get('sort', 'newest');

        // Получаем отзывы с фильтрацией и сортировкой
        $result = $this->reviewService->getReviews(
            carId: $carId ?: null,
            userId: $userId ?: null,
            rating: $rating ?: null,
            verified: $verified !== null ? (bool) $verified : null,
            sort: $sort,
            page: $page,
            perPage: $perPage
        );

        // Очищаем данные перед отправкой
        $cleanData = $this->cleanReviewData($result['data']);

        return $this->json(
            [
                'success' => true,
                'data' => $cleanData,
                'meta' => [
                    'total' => $result['total'],
                    'page' => $result['page'],
                    'perPage' => $result['perPage'],
                    'totalPages' => $result['totalPages'],
                ]
            ],
            Response::HTTP_OK,
            [],
            ['json_encode_options' => JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE]
        );
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

    private function cleanReviewData($reviews): array
    {
        $result = [];

        foreach ($reviews as $review) {
            // Если у объекта есть метод toArray - используем его
            if (method_exists($review, 'toArray')) {
                $data = $review->toArray();
            } else {
                // Иначе собираем из public свойств
                $data = [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'title' => $this->cleanString($review->title),
                    'comment' => $this->cleanString($review->comment),
                    'pros' => $this->cleanString($review->pros),
                    'cons' => $this->cleanString($review->cons),
                    'isVerified' => $review->isVerified,
                    'helpfulCount' => $review->helpfulCount,
                    'createdAt' => $review->createdAt,
                    'updatedAt' => $review->updatedAt,
                    'user' => $review->user->toArray(),
                    'car' => $review->car->toArray(),
                    'booking' => $review->booking->toArray(),
                ];
            }

            $result[] = $data;
        }

        return $result;
    }

    private function cleanString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Удаляем некорректные UTF-8 символы
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
    }
}
