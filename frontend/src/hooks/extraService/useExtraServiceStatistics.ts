// src/hooks/useExtraServiceStatistics.ts

import { useCallback, useEffect, useState } from "react";
import {extraService, ExtraServiceStatistics, PopularService} from "../../services/extraService";

export const useExtraServiceStatistics = () => {
    const [statistics, setStatistics] = useState<ExtraServiceStatistics[]>([]);
    const [popularServices, setPopularServices] = useState<PopularService[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    const fetchStatistics = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const [stats, popular] = await Promise.all([
                extraService.getCategoryStatistics(),
                extraService.getPopularServices(10),
            ]);

            setStatistics(stats);
            setPopularServices(popular);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Ошибка загрузки статистики');
            console.error('Error in useExtraServiceStatistics:', err);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchStatistics();
    }, [fetchStatistics]);

    return { statistics, popularServices, loading, error, refetch: fetchStatistics };
};