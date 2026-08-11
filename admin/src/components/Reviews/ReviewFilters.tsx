// src/components/Reviews/ReviewFilters.tsx

import React from 'react';
import { useLanguage } from '../../i18n/LanguageProvider';

interface ReviewFiltersProps {
    filters: {
        sort: 'newest' | 'oldest' | 'rating_high' | 'rating_low' | 'helpful';
        rating: number;
        verified: boolean;
    };
    onFilterChange: (filters: any) => void;
    onRefresh: () => void;
    loading: boolean;
}

export default function ReviewFilters({
                                          filters,
                                          onFilterChange,
                                          onRefresh,
                                          loading
                                      }: ReviewFiltersProps) {
    const { t } = useLanguage();

    const handleSortChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        onFilterChange({
            ...filters,
            sort: e.target.value as typeof filters.sort,
        });
    };

    const handleRatingChange = (rating: number) => {
        onFilterChange({
            ...filters,
            rating: filters.rating === rating ? 0 : rating,
        });
    };

    const handleVerifiedToggle = () => {
        onFilterChange({
            ...filters,
            verified: !filters.verified,
        });
    };

    const sortOptions = [
        { value: 'newest', label: t('sort_newest') },
        { value: 'oldest', label: t('sort_oldest') },
        { value: 'rating_high', label: t('sort_rating_high') },
        { value: 'rating_low', label: t('sort_rating_low') },
        { value: 'helpful', label: t('sort_helpful') },
    ];

    return (
        <div className="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 mb-6">
            <div className="flex flex-col md:flex-row md:items-center gap-4">
                {/* Сортировка */}
                <div className="flex-1">
                    <select
                        value={filters.sort}
                        onChange={handleSortChange}
                        className="w-full md:w-auto px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        {sortOptions.map(option => (
                            <option key={option.value} value={option.value}>
                                {option.label}
                            </option>
                        ))}
                    </select>
                </div>

                {/* Фильтр по рейтингу */}
                <div className="flex items-center space-x-1">
                    <span className="text-sm text-gray-600 dark:text-gray-400 mr-2">{t('rating')}:</span>
                    {[1, 2, 3, 4, 5].map(rating => (
                        <button
                            key={rating}
                            onClick={() => handleRatingChange(rating)}
                            className={`w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium transition-colors ${
                                filters.rating === rating
                                    ? 'bg-yellow-500 text-white'
                                    : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500'
                            }`}
                        >
                            {rating}
                        </button>
                    ))}
                    {filters.rating > 0 && (
                        <button
                            onClick={() => handleRatingChange(0)}
                            className="ml-1 text-sm text-red-500 hover:text-red-700"
                        >
                            ✕
                        </button>
                    )}
                </div>

                {/* Только подтвержденные */}
                <div className="flex items-center space-x-2">
                    <input
                        type="checkbox"
                        id="verified"
                        checked={filters.verified}
                        onChange={handleVerifiedToggle}
                        className="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <label htmlFor="verified" className="text-sm text-gray-600 dark:text-gray-400">
                        {t('only_verified')}
                    </label>
                </div>

                {/* Кнопка обновления */}
                <button
                    onClick={onRefresh}
                    disabled={loading}
                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center space-x-2"
                >
                    <svg
                        className={`w-4 h-4 ${loading ? 'animate-spin' : ''}`}
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>{loading ? t('loading') : t('refresh')}</span>
                </button>
            </div>
        </div>
    );
}