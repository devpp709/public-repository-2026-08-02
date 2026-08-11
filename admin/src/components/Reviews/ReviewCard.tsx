// src/components/Reviews/ReviewCard.tsx

import React from 'react';
import { Review } from '../../services/reviewsService';
import { useLanguage } from '../../i18n/LanguageProvider';

interface ReviewCardProps {
    review: Review;
    onMarkHelpful: (id: number) => void;
    onDelete?: (id: number) => void;
    isOwner?: boolean;
    showCarInfo?: boolean;
}

export default function ReviewCard({
                                       review,
                                       onMarkHelpful,
                                       onDelete,
                                       isOwner = false,
                                       showCarInfo = false
                                   }: ReviewCardProps) {
    const { t } = useLanguage();

    const renderStars = (rating: number) => {
        return '★'.repeat(rating) + '☆'.repeat(5 - rating);
    };

    const formatDate = (dateString: string) => {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('ru-RU', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        }).format(date);
    };

    return (
        <div className="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            {/* Шапка с информацией о пользователе */}
            <div className="flex items-start justify-between">
                <div className="flex items-center space-x-3">
                    {/* Аватар */}
                    <div className="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold text-lg">
                        {review.user.avatar ? (
                            <img
                                src={review.user.avatar}
                                alt={review.user.name}
                                className="w-10 h-10 rounded-full object-cover"
                            />
                        ) : (
                            review.user.name.charAt(0).toUpperCase()
                        )}
                    </div>

                    <div>
                        <h3 className="font-medium text-gray-800 dark:text-white">
                            {review.user.name}
                        </h3>
                        <div className="flex items-center space-x-2">
                            <span className="text-yellow-500 text-sm">
                                {renderStars(review.rating)}
                            </span>
                            <span className="text-xs text-gray-500 dark:text-gray-400">
                                {formatDate(review.createdAt)}
                            </span>
                        </div>
                    </div>
                </div>

                {/* Бейджи */}
                <div className="flex flex-col items-end space-y-1">
                    {review.isVerified && (
                        <span className="text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded-full">
                            ✓ {t('verified')}
                        </span>
                    )}
                    {showCarInfo && (
                        <span className="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full">
                            {review.car.brand} {review.car.model} ({review.car.year})
                        </span>
                    )}
                </div>
            </div>

            {/* Заголовок */}
            {review.title && (
                <h4 className="mt-3 text-lg font-semibold text-gray-800 dark:text-white">
                    {review.title}
                </h4>
            )}

            {/* Комментарий */}
            {review.comment && (
                <p className="mt-2 text-gray-600 dark:text-gray-300 leading-relaxed">
                    {review.comment}
                </p>
            )}

            {/* Плюсы и минусы */}
            {(review.pros || review.cons) && (
                <div className="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    {review.pros && (
                        <div className="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                            <span className="font-medium text-green-600 dark:text-green-400">✓ {t('pros')}:</span>
                            <p className="text-gray-600 dark:text-gray-300 mt-1">{review.pros}</p>
                        </div>
                    )}
                    {review.cons && (
                        <div className="bg-red-50 dark:bg-red-900/20 rounded-lg p-3">
                            <span className="font-medium text-red-600 dark:text-red-400">✗ {t('cons')}:</span>
                            <p className="text-gray-600 dark:text-gray-300 mt-1">{review.cons}</p>
                        </div>
                    )}
                </div>
            )}

            {/* Действия */}
            <div className="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <button
                    onClick={() => onMarkHelpful(review.id)}
                    className="flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                >
                    <span>👍</span>
                    <span>{t('helpful')} ({review.helpfulCount})</span>
                </button>

                {isOwner && onDelete && (
                    <button
                        onClick={() => onDelete(review.id)}
                        className="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                    >
                        🗑 {t('delete')}
                    </button>
                )}
            </div>
        </div>
    );
}