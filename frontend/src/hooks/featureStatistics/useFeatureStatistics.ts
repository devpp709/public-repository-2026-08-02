import {useCallback, useEffect, useState} from "react";
import {featureService, FeatureStatistics, FeatureWithCars, PopularFeature} from "../../services/featureService";

/**
 * Хук для получения статистики
 */
export const useFeatureStatistics = () => {
    const [statistics, setStatistics] = useState<FeatureStatistics[]>([]);
    const [popularFeatures, setPopularFeatures] = useState<PopularFeature[]>([]);
    const [featuresWithCars, setFeaturesWithCars] = useState<FeatureWithCars[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    const fetchStatistics = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const [stats, popular, withCars] = await Promise.all([
                featureService.getCategoryStatistics(),
                featureService.getPopularFeatures(10),
                featureService.getFeaturesWithCars(),
            ]);

            setStatistics(stats);
            setPopularFeatures(popular);
            setFeaturesWithCars(withCars);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Ошибка загрузки статистики');
            console.error('Error in useFeatureStatistics:', err);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchStatistics();
    }, [fetchStatistics]);

    return {
        statistics,
        popularFeatures,
        featuresWithCars,
        loading,
        error,
        refetch: fetchStatistics
    };
};
