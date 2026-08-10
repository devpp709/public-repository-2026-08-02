// src/components/Reviews/ReviewStats.tsx

import React from 'react';
import { Review } from '../../services/reviewsService';

interface ReviewStatsProps {
    reviews: Review[];
}

export default function ReviewStats({ reviews }: ReviewStatsProps) {
    if (reviews.length === 0) {
        return null;
    }

    const totalReviews = reviews.length;
    const averageRating = reviews.reduce((sum, r) => sum + r.rating, 0) / totalReviews;
    const verifiedCount = reviews.filter(r => r.isVerified).length;
    const totalHelpful = reviews.reduce((sum, r) => sum + r.helpfulCount, 0);

    // Распределение рейтингов
    const ratingDistribution = Array(5).fill(0);
    reviews.forEach(review => {
        ratingDistribution[review.rating - 1]++;
    });

    const maxCount = Math.max(...ratingDistribution);

    const getRatingLabel = (rating: number) => {
        const labels = ['Ужасно', 'Плохо', 'Средне', 'Хорошо', 'Отлично'];
        return labels[rating - 1] || '';
    };

    return (
        <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                {/* Средний рейтинг */}
                <div className="text-center">
                    <div className="text-4xl font-bold text-gray-800 dark:text-white">
                        {averageRating.toFixed(1)}
                    </div>
                    <div className="text-yellow-500 text-2xl">
                        {'★'.repeat(Math.round(averageRating))}
                    </div>
                    <div className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {totalReviews} {totalReviews === 1 ? 'отзыв' : 'отзывов'}
                    </div>
                </div>

                {/* Распределение рейтингов */}
                <div className="md:col-span-2">
                    {ratingDistribution.map((count, index) => {
                        const rating = index + 1;
                        const percentage = maxCount > 0 ? (count / maxCount) * 100 : 0;

                        return (
                            <div key={rating} className="flex items-center space-x-2 mb-1">
                                <div className="w-12 text-sm text-gray-600 dark:text-gray-400 text-right">
                                    {rating} ★
                                </div>
                                <div className="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div
                                        className="h-full bg-yellow-500 rounded-full transition-all duration-300"
                                        style={{ width: `${percentage}%` }}
                                    />
                                </div>
                                <div className="w-12 text-sm text-gray-600 dark:text-gray-400">
                                    {count}
                                </div>
                            </div>
                        );
                    })}
                </div>

                {/* Статистика */}
                <div className="space-y-2">
                    <div className="flex items-center justify-between text-sm">
                        <span className="text-gray-600 dark:text-gray-400">Подтверждено:</span>
                        <span className="font-medium text-gray-800 dark:text-white">
                            {verifiedCount} ({Math.round((verifiedCount / totalReviews) * 100)}%)
                        </span>
                    </div>
                    <div className="flex items-center justify-between text-sm">
                        <span className="text-gray-600 dark:text-gray-400">Всего полезных:</span>
                        <span className="font-medium text-gray-800 dark:text-white">
                            {totalHelpful}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    );
}