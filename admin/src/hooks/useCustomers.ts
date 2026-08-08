import { useState, useEffect, useCallback } from 'react';
import {
    CustomersGrowthResponse,
    customersService,
    CustomersStatisticsResponse,
    DailyStat
} from '../services/СustomersService.ts';

// ==================== Типы ====================

export interface UseCustomersStatsParams {
    days?: number;
    autoFetch?: boolean;
}

export interface UseCustomersStatsReturn {
    data: CustomersStatisticsResponse | null;
    loading: boolean;
    error: string | null;
    refetch: () => Promise<void>;
    refresh: () => Promise<void>;
    periodDays: number;
    setPeriodDays: (days: number) => void;
}

export interface UseCustomersCountReturn {
    count: number | null;
    loading: boolean;
    error: string | null;
    refetch: () => Promise<void>;
}

export interface UseCustomersGrowthReturn {
    data: CustomersGrowthResponse | null;
    loading: boolean;
    error: string | null;
    refetch: () => Promise<void>;
    growthDisplay: string;
    trendColor: string;
    trendText: string;
    trendIcon: string;
}

// ==================== Хуки ====================

/**
 * Хук для получения полной статистики по клиентам
 */
export const useCustomersStatistics = (
    params: UseCustomersStatsParams = {}
): UseCustomersStatsReturn => {
    const { days = 7, autoFetch = true } = params;

    const [data, setData] = useState<CustomersStatisticsResponse | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const token = localStorage.getItem('auth_token') || undefined;

    const fetchData = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const result = await customersService.getStatistics(
                { days },
                token
            );

            setData(result);
        } catch (err) {
            const errorMessage = err instanceof Error
                ? err.message
                : 'Не удалось загрузить статистику клиентов';

            setError(errorMessage);
            console.error('Error fetching customers statistics:', err);
        } finally {
            setLoading(false);
        }
    }, [days, token]);

    useEffect(() => {
        if (autoFetch) {
            fetchData();
        }
    }, [fetchData, autoFetch]);

    return {
        data,
        loading,
        error,
        refetch: fetchData,
        refresh: fetchData,
        periodDays: days,
        setPeriodDays: () => {},
    };
};

/**
 * Хук для получения количества клиентов
 */
export const useCustomersCount = (autoFetch: boolean = true): UseCustomersCountReturn => {
    const [count, setCount] = useState<number | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const token = localStorage.getItem('auth_token') || undefined;

    const fetchCount = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const result = await customersService.getCount(token);
            setCount(result.total_customers);
        } catch (err) {
            const errorMessage = err instanceof Error
                ? err.message
                : 'Не удалось получить количество клиентов';
            setError(errorMessage);
            console.error('Error fetching customers count:', err);
        } finally {
            setLoading(false);
        }
    }, [token]);

    useEffect(() => {
        if (autoFetch) {
            fetchCount();
        }
    }, [fetchCount, autoFetch]);

    return {
        count,
        loading,
        error,
        refetch: fetchCount,
    };
};

/**
 * Хук для получения данных о росте клиентов
 */
export const useCustomersGrowth = (
    days: number = 7,
    autoFetch: boolean = true
): UseCustomersGrowthReturn => {
    const [data, setData] = useState<CustomersGrowthResponse | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const token = localStorage.getItem('auth_token') || undefined;

    const fetchGrowth = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const result = await customersService.getGrowth({ days }, token);
            setData(result);
        } catch (err) {
            const errorMessage = err instanceof Error
                ? err.message
                : 'Не удалось получить данные о росте клиентов';
            setError(errorMessage);
            console.error('Error fetching customers growth:', err);
        } finally {
            setLoading(false);
        }
    }, [days, token]);

    useEffect(() => {
        if (autoFetch) {
            fetchGrowth();
        }
    }, [fetchGrowth, autoFetch]);

    // Вычисляем вспомогательные значения
    const growthDisplay = data
        ? customersService.formatGrowthDisplay(data.data.growth_percentage, data.data.trend)
        : '';

    const trendColor = data
        ? customersService.getTrendColor(data.data.trend)
        : '#6b7280';

    const trendText = data
        ? customersService.getTrendText(data.data.trend)
        : '';

    const trendIcon = data
        ? customersService.getTrendIcon(data.data.trend)
        : '';

    return {
        data,
        loading,
        error,
        refetch: fetchGrowth,
        growthDisplay,
        trendColor,
        trendText,
        trendIcon,
    };
};

/**
 * Хук для получения ежедневной статистики (для графиков)
 */
export const useCustomersDailyStats = (
    days: number = 7,
    autoFetch: boolean = true
) => {
    const [stats, setStats] = useState<DailyStat[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [maxValue, setMaxValue] = useState(1);

    const token = localStorage.getItem('auth_token') || undefined;

    const fetchDailyStats = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const result = await customersService.getDailyStats(days, token);
            setStats(result);
            setMaxValue(customersService.getMaxDailyCount(result));
        } catch (err) {
            const errorMessage = err instanceof Error
                ? err.message
                : 'Не удалось получить ежедневную статистику';
            setError(errorMessage);
            console.error('Error fetching daily stats:', err);
        } finally {
            setLoading(false);
        }
    }, [days, token]);

    useEffect(() => {
        if (autoFetch) {
            fetchDailyStats();
        }
    }, [fetchDailyStats, autoFetch]);

    // Подготовка данных для графика
    const chartData = stats.map(stat => ({
        ...stat,
        percentage: maxValue > 0 ? (stat.count / maxValue) * 100 : 0,
    }));

    return {
        stats,
        chartData,
        loading,
        error,
        refetch: fetchDailyStats,
        maxValue,
        total: stats.reduce((sum, stat) => sum + stat.count, 0),
        average: stats.length > 0
            ? Math.round(stats.reduce((sum, stat) => sum + stat.count, 0) / stats.length)
            : 0,
    };
};

/**
 * Хук для автоматического обновления статистики с интервалом
 */
export const useAutoRefreshStatistics = (
    intervalMs: number = 60000, // 1 минута по умолчанию
    days: number = 7
) => {
    const [isEnabled, setIsEnabled] = useState(true);
    const stats = useCustomersStatistics({ days, autoFetch: true });

    useEffect(() => {
        if (!isEnabled) return;

        const intervalId = setInterval(() => {
            stats.refetch();
        }, intervalMs);

        return () => clearInterval(intervalId);
    }, [intervalMs, isEnabled, stats]);

    return {
        ...stats,
        isAutoRefreshEnabled: isEnabled,
        toggleAutoRefresh: () => setIsEnabled(prev => !prev),
    };
};

/**
 * Хук для получения статистики по периоду (неделя/месяц/квартал)
 */
export const useCustomersStatisticsByPeriod = (
    period: 'week' | 'month' | 'quarter' = 'week',
    autoFetch: boolean = true
) => {
    const daysMap = {
        week: 7,
        month: 30,
        quarter: 90,
    };

    const [currentPeriod, setCurrentPeriod] = useState(period);
    const [data, setData] = useState<CustomersStatisticsResponse | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const token = localStorage.getItem('auth_token') || undefined;

    const fetchData = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const result = await customersService.getStatisticsByPeriod(
                currentPeriod,
                token
            );
            setData(result);
        } catch (err) {
            const errorMessage = err instanceof Error
                ? err.message
                : 'Не удалось загрузить статистику клиентов';
            setError(errorMessage);
            console.error('Error fetching period statistics:', err);
        } finally {
            setLoading(false);
        }
    }, [currentPeriod, token]);

    useEffect(() => {
        if (autoFetch) {
            fetchData();
        }
    }, [fetchData, autoFetch]);

    const changePeriod = (newPeriod: 'week' | 'month' | 'quarter') => {
        setCurrentPeriod(newPeriod);
    };

    return {
        data,
        loading,
        error,
        refetch: fetchData,
        period: currentPeriod,
        changePeriod,
        days: daysMap[currentPeriod],
    };
};