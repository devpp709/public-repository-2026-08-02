<?php

namespace App\Service;

use App\DTO\Review\ReviewRequestDTO;
use App\DTO\Review\ReviewResponseDTO;
use App\DTO\Review\ReviewStatisticsDTO;
use App\Entity\Car;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\ReviewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReviewService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ReviewsRepository $reviewRepository
    ) {
    }

    /**
     * Получить все отзывы
     */
    public function getAllReviews(): array
    {
        $reviews = $this->reviewRepository->findBy([], ['createdAt' => 'DESC']);
        return ReviewResponseDTO::fromEntities($reviews);
    }

    /**
     * Получить отзыв по ID
     */
    public function getReviewById(int $id): ReviewResponseDTO
    {
        $review = $this->findReviewOrFail($id);
        return ReviewResponseDTO::fromEntity($review);
    }

    /**
     * Получить отзывы по автомобилю
     */
    public function getReviewsByCarId(int $carId): array
    {
        $reviews = $this->reviewRepository->findByCarId($carId);
        return ReviewResponseDTO::fromEntities($reviews);
    }

    /**
     * Получить отзывы по пользователю
     */
    public function getReviewsByUserId(int $userId): array
    {
        $reviews = $this->reviewRepository->findByUserId($userId);
        return ReviewResponseDTO::fromEntities($reviews);
    }

    /**
     * Создать отзыв
     */
    public function createReview(ReviewRequestDTO $dto): ReviewResponseDTO
    {
        // Проверяем, существует ли уже отзыв на это бронирование
        if ($this->reviewRepository->existsByBookingAndUser($dto->bookingId, $dto->userId)) {
            throw new \InvalidArgumentException('Вы уже оставляли отзыв на это бронирование');
        }

        $booking = $this->findBookingOrFail($dto->bookingId);
        $car = $this->findCarOrFail($dto->carId);
        $user = $this->findUserOrFail($dto->userId);

        $review = new Review();
        $review->setBooking($booking);
        $review->setCar($car);
        $review->setUser($user);
        $review->setRating($dto->rating);
        $review->setTitle($dto->title);
        $review->setComment($dto->comment);
        $review->setPros($dto->pros);
        $review->setCons($dto->cons);
        $review->setIsVerified($dto->isVerified ?? false);

        $this->entityManager->persist($review);
        $this->entityManager->flush();

        return ReviewResponseDTO::fromEntity($review);
    }

    /**
     * Обновить отзыв
     */
    public function updateReview(int $id, ReviewRequestDTO $dto): ReviewResponseDTO
    {
        $review = $this->findReviewOrFail($id);

        if ($dto->rating !== null) {
            $review->setRating($dto->rating);
        }
        if ($dto->title !== null) {
            $review->setTitle($dto->title);
        }
        if ($dto->comment !== null) {
            $review->setComment($dto->comment);
        }
        if ($dto->pros !== null) {
            $review->setPros($dto->pros);
        }
        if ($dto->cons !== null) {
            $review->setCons($dto->cons);
        }
        if ($dto->isVerified !== null) {
            $review->setIsVerified($dto->isVerified);
        }

        $this->entityManager->flush();

        return ReviewResponseDTO::fromEntity($review);
    }

    /**
     * Удалить отзыв
     */
    public function deleteReview(int $id): void
    {
        $review = $this->findReviewOrFail($id);
        $this->entityManager->remove($review);
        $this->entityManager->flush();
    }

    /**
     * Отметить отзыв как полезный
     */
    public function markHelpful(int $id): ReviewResponseDTO
    {
        $review = $this->findReviewOrFail($id);
        $review->incrementHelpful();
        $this->entityManager->flush();

        return ReviewResponseDTO::fromEntity($review);
    }

    /**
     * Отменить отметку "полезный"
     */
    public function unmarkHelpful(int $id): ReviewResponseDTO
    {
        $review = $this->findReviewOrFail($id);
        $review->decrementHelpful();
        $this->entityManager->flush();

        return ReviewResponseDTO::fromEntity($review);
    }

    /**
     * Получить статистику по автомобилю
     */
    public function getStatisticsForCar(int $carId): ReviewStatisticsDTO
    {
        $statistics = $this->reviewRepository->getStatisticsForCar($carId);
        return ReviewStatisticsDTO::fromArray($statistics);
    }

    /**
     * Получить глобальную статистику
     */
    public function getGlobalStatistics(): array
    {
        $statistics = $this->reviewRepository->getGlobalStatistics();

        return [
            'total' => (int) $statistics['total'],
            'avg_rating' => round((float) $statistics['avg_rating'], 2),
            'unique_cars' => (int) $statistics['unique_cars'],
            'unique_users' => (int) $statistics['unique_users'],
            'verified' => (int) $statistics['verified'],
            'total_helpful' => (int) $statistics['total_helpful']
        ];
    }

    /**
     * Получить распределение рейтингов
     */
    public function getRatingDistribution(): array
    {
        return $this->reviewRepository->getRatingDistribution();
    }

    /**
     * Получить лучшие отзывы
     */
    public function getTopRatedReviews(int $limit = 10): array
    {
        $reviews = $this->reviewRepository->findTopRated($limit);
        return ReviewResponseDTO::fromEntities($reviews);
    }

    /**
     * Получить самые полезные отзывы
     */
    public function getMostHelpfulReviews(int $limit = 10): array
    {
        $reviews = $this->reviewRepository->findMostHelpful($limit);
        return ReviewResponseDTO::fromEntities($reviews);
    }

    /**
     * Получить последние отзывы
     */
    public function getLatestReviews(int $limit = 10): array
    {
        $reviews = $this->reviewRepository->findLatest($limit);
        return ReviewResponseDTO::fromEntities($reviews);
    }

    /**
     * Проверить, оставлял ли пользователь отзыв
     */
    public function hasReview(int $bookingId, int $userId): bool
    {
        return $this->reviewRepository->existsByBookingAndUser($bookingId, $userId);
    }

    /**
     * Найти отзыв или выбросить исключение
     */
    private function findReviewOrFail(int $id): Review
    {
        $review = $this->reviewRepository->find($id);
        if (!$review) {
            throw new NotFoundHttpException(sprintf('Отзыв с ID %d не найден', $id));
        }
        return $review;
    }

    /**
     * Найти бронирование или выбросить исключение
     */
    private function findBookingOrFail(int $id): Booking
    {
        $booking = $this->entityManager->getRepository(Booking::class)->find($id);
        if (!$booking) {
            throw new NotFoundHttpException(sprintf('Бронирование с ID %d не найдено', $id));
        }
        return $booking;
    }

    /**
     * Найти автомобиль или выбросить исключение
     */
    private function findCarOrFail(int $id): Car
    {
        $car = $this->entityManager->getRepository(Car::class)->find($id);
        if (!$car) {
            throw new NotFoundHttpException(sprintf('Автомобиль с ID %d не найден', $id));
        }
        return $car;
    }

    /**
     * Найти пользователя или выбросить исключение
     */
    private function findUserOrFail(int $id): User
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('Пользователь с ID %d не найден', $id));
        }
        return $user;
    }
}
