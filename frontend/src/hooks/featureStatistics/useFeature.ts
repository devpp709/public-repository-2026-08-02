import {useCallback, useEffect, useState} from "react";
import {Feature, featureService} from "../../services/featureService";

/**
 * Хук для получения одной характеристики
 */
export const useFeature = (id: number, withStats: boolean = true) => {
    const [feature, setFeature] = useState<Feature | null>(null);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    const fetchFeature = useCallback(async () => {
        if (!id) {
            setLoading(false);
            return;
        }

        setLoading(true);
        setError(null);

        try {
            const result = await featureService.getFeatureById(id, withStats);
            setFeature(result);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Ошибка загрузки характеристики');
            console.error(`Error in useFeature ${id}:`, err);
        } finally {
            setLoading(false);
        }
    }, [id, withStats]);

    useEffect(() => {
        fetchFeature();
    }, [fetchFeature]);

    return { feature, loading, error, refetch: fetchFeature };
};
