import { useState, useEffect, useCallback } from 'react';
import {Feature} from "../../services/carService";
import {featureService} from "../../services/featureService";

/**
 * Хук для получения списка характеристик
 */
export const useFeatures = (options?: {
    withStats?: boolean;
    category?: string;
    search?: string;
    carId?: number;
}) => {
    const [features, setFeatures] = useState<Feature[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    const fetchFeatures = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            let result: Feature[] = [];

            if (options?.search) {
                result = await featureService.searchFeatures(options.search);
            } else if (options?.category) {
                result = await featureService.getFeaturesByCategory(options.category);
            } else if (options?.carId && options.carId > 0) {
                result = await featureService.getFeaturesByCarId(options.carId);
            } else {
                result = await featureService.getAllFeatures(options?.withStats || false);
            }

            setFeatures(result);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Ошибка загрузки характеристик');
            console.error('Error in useFeatures:', err);
        } finally {
            setLoading(false);
        }
    }, [options?.withStats, options?.category, options?.search, options?.carId]);

    useEffect(() => {
        fetchFeatures();
    }, [fetchFeatures]);

    return { features, loading, error, refetch: fetchFeatures };
};
