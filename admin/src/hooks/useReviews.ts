// src/hooks/useReviews.ts

import { useState, useEffect, useCallback, useRef } from 'react';
import reviewsService, { Review, ReviewFilters } from '../services/reviewsService';

interface UseReviewsOptions {
    initialFilters?: ReviewFilters;
    autoFetch?: boolean;
}

interface UseReviewsReturn {
    reviews: Review[];
    loading: boolean;
    error: string | null;
    meta: {
        total: number;
        page: number;
        perPage: number;
        totalPages: number;
    };
    filters: ReviewFilters;
    setFilters: (filters: ReviewFilters) => void;
    fetchReviews: (filters?: ReviewFilters) => Promise<void>;
    fetchMore: () => Promise<void>;
    refresh: () => Promise<void>;
    addReview: (review: Omit<Review, 'id' | 'createdAt' | 'updatedAt'>) => Promise<Review>;
    updateReview: (id: number, data: Partial<Review>) => Promise<Review>;
    deleteReview: (id: number) => Promise<void>;
    markHelpful: (id: number) => Promise<void>;
}

export function useReviews(options: UseReviewsOptions = {}): UseReviewsReturn {
    const { initialFilters = {}, autoFetch = true } = options;

    const [reviews, setReviews] = useState<Review[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);
    const [filters, setFilters] = useState<ReviewFilters>({
        page: 1,
        perPage: 10,
        sort: 'newest',
        ...initialFilters,
    });
    const [meta, setMeta] = useState({
        total: 0,
        page: 1,
        perPage: 10,
        totalPages: 0,
    });

    // Запрос с текущими фильтрами
    const fetchReviews = useCallback(async (currentFilters?: ReviewFilters) => {
        try {
            setLoading(true);
            setError(null);

            const params = currentFilters || filters;
            const response = await reviewsService.getReviews(params);

            setReviews(response.data || []);
            if (response.meta) {
                setMeta(response.meta);
            }
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to fetch reviews');
            console.error('Error fetching reviews:', err);
        } finally {
            setLoading(false);
        }
    }, [filters]);

    // Запрос при изменении фильтров
    useEffect(() => {
        if (autoFetch) {
            fetchReviews();
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [filters, autoFetch]);

    const fetchMore = useCallback(async () => {
        if (loading || meta.page >= meta.totalPages) return;

        const nextPage = meta.page + 1;
        const newFilters = { ...filtersRef.current, page: nextPage };

        try {
            setLoading(true);
            const response = await reviewsService.getReviews(newFilters);
            setReviews(prev => [...prev, ...response.data]);
            if (response.meta) {
                setMeta(response.meta);
            }
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to fetch more reviews');
        } finally {
            setLoading(false);
        }
    }, [loading, meta.page, meta.totalPages]);

    const refresh = useCallback(async () => {
        await fetchReviews({ ...filtersRef.current, page: 1 });
    }, [fetchReviews]);

    const addReview = useCallback(async (reviewData: Omit<Review, 'id' | 'createdAt' | 'updatedAt'>) => {
        try {
            const newReview = await reviewsService.createReview(reviewData);
            setReviews(prev => [newReview, ...prev]);
            return newReview;
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to create review');
            throw err;
        }
    }, []);

    const updateReview = useCallback(async (id: number, data: Partial<Review>) => {
        try {
            const updatedReview = await reviewsService.updateReview(id, data);
            setReviews(prev => prev.map(review =>
                review.id === id ? updatedReview : review
            ));
            return updatedReview;
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to update review');
            throw err;
        }
    }, []);

    const deleteReview = useCallback(async (id: number) => {
        try {
            await reviewsService.deleteReview(id);
            setReviews(prev => prev.filter(review => review.id !== id));
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to delete review');
            throw err;
        }
    }, []);

    const markHelpful = useCallback(async (id: number) => {
        try {
            const response = await reviewsService.markHelpful(id);
            setReviews(prev => prev.map(review =>
                review.id === id
                    ? { ...review, helpfulCount: response.helpfulCount }
                    : review
            ));
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to mark review as helpful');
            throw err;
        }
    }, []);

    useEffect(() => {
        if (autoFetch) {
            fetchReviews();
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [autoFetch]);

    return {
        reviews,
        loading,
        error,
        meta,
        filters,
        setFilters,
        fetchReviews,
        fetchMore,
        refresh,
        addReview,
        updateReview,
        deleteReview,
        markHelpful,
    };
}