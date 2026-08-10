// src/services/reviewsService.ts

import { api } from './api';

// ============ DTO / Types ============

export interface ReviewUser {
    id: number;
    name: string;
    email?: string;
    avatar?: string;
}

export interface ReviewCar {
    id: number;
    brand: string;
    model: string;
    year: number;
    image?: string;
}

export interface ReviewBooking {
    id: number;
    bookingNumber: string;
    pickupDate: string;
    dropoffDate: string;
}

export interface Review {
    id: number;
    rating: number;
    title?: string;
    comment?: string;
    pros?: string;
    cons?: string;
    isVerified: boolean;
    helpfulCount: number;
    createdAt: string;
    updatedAt: string;
    user: ReviewUser;
    car: ReviewCar;
    booking: ReviewBooking;
}

export interface ReviewsListResponse {
    data: Review[];
    meta: {
        total: number;
        page: number;
        perPage: number;
        totalPages: number;
    };
}

export interface CreateReviewRequest {
    bookingId: number;
    rating: number;
    title?: string;
    comment?: string;
    pros?: string;
    cons?: string;
}

export interface UpdateReviewRequest {
    rating?: number;
    title?: string;
    comment?: string;
    pros?: string;
    cons?: string;
}

export interface GetReviewsParams {
    carId?: number;
    userId?: number;
    rating?: number;
    verified?: boolean;
    page?: number;
    perPage?: number;
    sort?: 'newest' | 'oldest' | 'rating_high' | 'rating_low' | 'helpful';
}

export interface CarAverageRatingResponse {
    average: number;
    total: number;
}

export interface MarkHelpfulResponse {
    helpfulCount: number;
}

export interface ReviewsStatisticsResponse {
    total: number;
    verified: number;
    unverified: number;
    averageRating: number;
    ratingDistribution: {
        1: number;
        2: number;
        3: number;
        4: number;
        5: number;
    };
}

// ============ Service ============

class ReviewsService {
    private readonly baseEndpoint = '/api/admin/reviews';

    /**
     * Получить все отзывы с фильтрацией и пагинацией
     */
    async getReviews(
        params: GetReviewsParams = {},
        token?: string
    ): Promise<ReviewsListResponse> {
        const queryParams = new URLSearchParams();

        if (params.carId !== undefined) {
            queryParams.append('carId', String(params.carId));
        }
        if (params.userId !== undefined) {
            queryParams.append('userId', String(params.userId));
        }
        if (params.rating !== undefined) {
            queryParams.append('rating', String(params.rating));
        }
        if (params.verified !== undefined) {
            queryParams.append('verified', String(params.verified));
        }
        if (params.page !== undefined) {
            queryParams.append('page', String(params.page));
        }
        if (params.perPage !== undefined) {
            queryParams.append('perPage', String(params.perPage));
        }
        if (params.sort !== undefined) {
            queryParams.append('sort', params.sort);
        }

        const url = `${this.baseEndpoint}${
            queryParams.toString() ? `?${queryParams.toString()}` : ''
        }`;

        return api.get<ReviewsListResponse>(url, token);
    }

    /**
     * Получить отзыв по ID
     */
    async getReviewById(id: number, token?: string): Promise<Review> {
        return api.get<Review>(`${this.baseEndpoint}/${id}`, token);
    }

    /**
     * Получить отзывы для конкретного автомобиля
     */
    async getReviewsByCarId(
        carId: number,
        page: number = 1,
        perPage: number = 10,
        token?: string
    ): Promise<ReviewsListResponse> {
        return this.getReviews(
            {
                carId,
                page,
                perPage,
                sort: 'newest',
            },
            token
        );
    }

    /**
     * Получить отзывы пользователя
     */
    async getReviewsByUserId(
        userId: number,
        page: number = 1,
        perPage: number = 10,
        token?: string
    ): Promise<ReviewsListResponse> {
        return this.getReviews(
            {
                userId,
                page,
                perPage,
                sort: 'newest',
            },
            token
        );
    }

    /**
     * Получить последние отзывы
     */
    async getLatestReviews(
        limit: number = 5,
        token?: string
    ): Promise<ReviewsListResponse> {
        return this.getReviews(
            {
                page: 1,
                perPage: limit,
                sort: 'newest',
            },
            token
        );
    }

    /**
     * Получить отзывы с высоким рейтингом
     */
    async getTopRatedReviews(
        limit: number = 10,
        minRating: number = 4,
        token?: string
    ): Promise<ReviewsListResponse> {
        return this.getReviews(
            {
                page: 1,
                perPage: limit,
                sort: 'rating_high',
                rating: minRating,
                verified: true,
            },
            token
        );
    }

    /**
     * Создать новый отзыв
     */
    async createReview(
        review: CreateReviewRequest,
        token?: string
    ): Promise<Review> {
        return api.post<Review>(this.baseEndpoint, review, token);
    }

    /**
     * Обновить отзыв
     */
    async updateReview(
        id: number,
        review: UpdateReviewRequest,
        token?: string
    ): Promise<Review> {
        return api.put<Review>(`${this.baseEndpoint}/${id}`, review, token);
    }

    /**
     * Удалить отзыв
     */
    async deleteReview(id: number, token?: string): Promise<void> {
        return api.delete<void>(`${this.baseEndpoint}/${id}`, token);
    }

    /**
     * Отметить отзыв как полезный
     */
    async markHelpful(id: number, token?: string): Promise<MarkHelpfulResponse> {
        return api.post<MarkHelpfulResponse>(
            `${this.baseEndpoint}/${id}/helpful`,
            {},
            token
        );
    }

    /**
     * Отменить отметку "полезный"
     */
    async unmarkHelpful(id: number, token?: string): Promise<MarkHelpfulResponse> {
        return api.delete<MarkHelpfulResponse>(
            `${this.baseEndpoint}/${id}/helpful`,
            token
        );
    }

    /**
     * Получить средний рейтинг автомобиля
     */
    async getCarAverageRating(
        carId: number,
        token?: string
    ): Promise<CarAverageRatingResponse> {
        return api.get<CarAverageRatingResponse>(
            `${this.baseEndpoint}/car/${carId}/rating`,
            token
        );
    }

    /**
     * Получить статистику по отзывам
     */
    async getStatistics(token?: string): Promise<ReviewsStatisticsResponse> {
        return api.get<ReviewsStatisticsResponse>(
            `${this.baseEndpoint}/statistics`,
            token
        );
    }

    /**
     * Получить непроверенные отзывы (для модерации)
     */
    async getUnverifiedReviews(
        page: number = 1,
        perPage: number = 10,
        token?: string
    ): Promise<ReviewsListResponse> {
        return this.getReviews(
            {
                page,
                perPage,
                verified: false,
                sort: 'newest',
            },
            token
        );
    }

    /**
     * Подтвердить отзыв
     */
    async verifyReview(id: number, token?: string): Promise<Review> {
        return api.patch<Review>(
            `${this.baseEndpoint}/${id}/verify`,
            {},
            token
        );
    }

    /**
     * Отклонить отзыв
     */
    async rejectReview(id: number, token?: string): Promise<void> {
        return api.delete<void>(`${this.baseEndpoint}/${id}/reject`, token);
    }

    /**
     * Поиск отзывов по тексту
     */
    async searchReviews(
        searchTerm: string,
        page: number = 1,
        perPage: number = 10,
        token?: string
    ): Promise<ReviewsListResponse> {
        const params: GetReviewsParams = {
            page,
            perPage,
        };

        // Добавляем поисковый параметр в URL
        const queryParams = new URLSearchParams();
        queryParams.append('search', searchTerm);
        queryParams.append('page', String(page));
        queryParams.append('perPage', String(perPage));

        const url = `${this.baseEndpoint}/search?${queryParams.toString()}`;
        return api.get<ReviewsListResponse>(url, token);
    }

    // ============ Helper методы ============

    /**
     * Получить средний рейтинг из списка отзывов
     */
    getAverageRating(reviews: Review[]): number {
        if (reviews.length === 0) return 0;
        const sum = reviews.reduce((acc, review) => acc + review.rating, 0);
        return parseFloat((sum / reviews.length).toFixed(1));
    }

    /**
     * Получить распределение рейтингов из списка отзывов
     */
    getRatingDistribution(reviews: Review[]): Record<1 | 2 | 3 | 4 | 5, number> {
        const distribution: Record<1 | 2 | 3 | 4 | 5, number> = {
            1: 0,
            2: 0,
            3: 0,
            4: 0,
            5: 0,
        };

        reviews.forEach((review) => {
            const rating = review.rating as 1 | 2 | 3 | 4 | 5;
            if (rating >= 1 && rating <= 5) {
                distribution[rating]++;
            }
        });

        return distribution;
    }

    /**
     * Получить процент подтвержденных отзывов
     */
    getVerifiedPercentage(reviews: Review[]): number {
        if (reviews.length === 0) return 0;
        const verifiedCount = reviews.filter((r) => r.isVerified).length;
        return parseFloat(((verifiedCount / reviews.length) * 100).toFixed(1));
    }

    /**
     * Получить общее количество полезных отметок
     */
    getTotalHelpfulCount(reviews: Review[]): number {
        return reviews.reduce((acc, review) => acc + review.helpfulCount, 0);
    }

    /**
     * Получить текстовый лейбл для рейтинга
     */
    getRatingLabel(rating: number): string {
        const labels: Record<number, string> = {
            1: 'Ужасно',
            2: 'Плохо',
            3: 'Средне',
            4: 'Хорошо',
            5: 'Отлично',
        };
        return labels[rating] || 'Не оценено';
    }

    /**
     * Получить HTML звезд для рейтинга
     */
    getRatingStars(rating: number): string {
        return '★'.repeat(rating) + '☆'.repeat(5 - rating);
    }

    /**
     * Проверить, может ли пользователь оставить отзыв на бронирование
     */
    async canReviewBooking(
        bookingId: number,
        token?: string
    ): Promise<{ canReview: boolean; message?: string }> {
        return api.get<{ canReview: boolean; message?: string }>(
            `${this.baseEndpoint}/can-review/${bookingId}`,
            token
        );
    }
}

// ============ Export ============

export const reviewsService = new ReviewsService();
export default reviewsService;