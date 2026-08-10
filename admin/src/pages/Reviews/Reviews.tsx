// src/pages/Reviews/Reviews.tsx

import React, { useState } from 'react';
import { useReviews } from '../../hooks/useReviews';
import ReviewCard from '../../components/Reviews/ReviewCard';
import ReviewFilters from '../../components/Reviews/ReviewFilters';
import ReviewStats from '../../components/Reviews/ReviewStats';
import LoadingSpinner from '../../components/common/LoadingSpinner';
import ErrorMessage from '../../components/common/ErrorMessage';

export default function Reviews() {
    const [filters, setFilters] = useState({
        sort: 'newest' as const,
        rating: 0,
        verified: false,
    });

    const {
        reviews,
        loading,
        error,
        meta,
        fetchMore,
        refresh,
        markHelpful,
        deleteReview,
        setFilters: setHookFilters, // добавляем
    } = useReviews({
        initialFilters: {
            page: 1,
            perPage: 10,
            sort: filters.sort,
        },
    });

    const handleFilterChange = (newFilters: typeof filters) => {
        setFilters(newFilters);
        // Обновляем фильтры в хуке - useEffect сам сделает запрос
        setHookFilters({
            ...newFilters,
            page: 1,
            perPage: 10,
        });
    };

    const handleMarkHelpful = async (reviewId: number) => {
        try {
            await markHelpful(reviewId);
        } catch (error) {
            console.error('Error marking helpful:', error);
        }
    };

    const handleDeleteReview = async (reviewId: number) => {
        if (window.confirm('Вы уверены, что хотите удалить этот отзыв?')) {
            try {
                await deleteReview(reviewId);
            } catch (error) {
                console.error('Error deleting review:', error);
            }
        }
    };

    if (loading && reviews.length === 0) {
        return <LoadingSpinner fullScreen />;
    }

    if (error) {
        return <ErrorMessage message={error} onRetry={refresh} />;
    }

    return (
        <div className="container mx-auto px-4 py-8 max-w-6xl">
            <div className="flex justify-between items-center mb-6">
                <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                    Отзывы
                </h1>
                <span className="text-sm text-gray-500 dark:text-gray-400">
                    Всего: {meta.total}
                </span>
            </div>

            <ReviewStats reviews={reviews} />

            <ReviewFilters
                filters={filters}
                onFilterChange={handleFilterChange}
                onRefresh={refresh}
                loading={loading}
            />

            {reviews.length === 0 ? (
                <div className="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p className="text-gray-500 dark:text-gray-400">
                        Нет отзывов, соответствующих вашим критериям
                    </p>
                </div>
            ) : (
                <div className="space-y-4">
                    {reviews.map((review) => (
                        <ReviewCard
                            key={review.id}
                            review={review}
                            onMarkHelpful={handleMarkHelpful}
                            onDelete={handleDeleteReview}
                            isOwner={false}
                        />
                    ))}

                    {meta.page < meta.totalPages && (
                        <div className="text-center mt-6">
                            <button
                                onClick={fetchMore}
                                disabled={loading}
                                className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                {loading ? 'Загрузка...' : 'Загрузить еще'}
                            </button>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}