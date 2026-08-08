import { useCallback, useEffect, useState } from 'react';
import {
    BookingsStatisticsResponse,
    bookingsService,
    DailyBookingStat,
} from '../services/BookingsService';

export interface UseBookingsStatisticsParams {
    days?: number;
    autoFetch?: boolean;
}

export interface UseBookingsStatisticsReturn {
    data: BookingsStatisticsResponse | null;
    loading: boolean;
    error: string | null;
    refetch: () => Promise<void>;
    refresh: () => Promise<void>;
    periodDays: number;
    setPeriodDays: (days: number) => void;
}

export const useBookingsStatistics = (
    params: UseBookingsStatisticsParams = {}
): UseBookingsStatisticsReturn => {
    const {
        days = 7,
        autoFetch = true,
    } = params;

    const [data, setData] =
        useState<BookingsStatisticsResponse | null>(null);

    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [periodDays, setPeriodDays] = useState(days);

    useEffect(() => {
        setPeriodDays(days);
    }, [days]);

    const fetchData = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const token =
                localStorage.getItem('auth_token') || undefined;

            const result =
                await bookingsService.getStatistics(
                    { days: periodDays },
                    token
                );

            setData(result);
        } catch (err) {
            const errorMessage =
                err instanceof Error
                    ? err.message
                    : 'Не удалось загрузить статистику заказов';

            setError(errorMessage);

            console.error(
                'Error fetching bookings statistics:',
                err
            );
        } finally {
            setLoading(false);
        }
    }, [periodDays]);

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
        periodDays,
        setPeriodDays,
    };
};

export const useBookingsDailyStats = (
    days: number = 7,
    autoFetch: boolean = true
) => {
    const [stats, setStats] =
        useState<DailyBookingStat[]>([]);

    const [loading, setLoading] = useState(true);
    const [error, setError] =
        useState<string | null>(null);

    const fetchStats = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const token =
                localStorage.getItem('auth_token') || undefined;

            const response =
                await bookingsService.getStatistics(
                    { days },
                    token
                );

            setStats(response.data.dailyStats);
        } catch (err) {
            const errorMessage =
                err instanceof Error
                    ? err.message
                    : 'Не удалось загрузить статистику заказов';

            setError(errorMessage);

            console.error(
                'Error fetching bookings daily stats:',
                err
            );
        } finally {
            setLoading(false);
        }
    }, [days]);

    useEffect(() => {
        if (autoFetch) {
            fetchStats();
        }
    }, [fetchStats, autoFetch]);

    const maxValue =
        bookingsService.getMaxDailyCount(stats);

    return {
        stats,
        loading,
        error,
        refetch: fetchStats,
        maxValue,
        total: stats.reduce(
            (sum, stat) => sum + stat.count,
            0
        ),
        average:
            stats.length > 0
                ? Math.round(
                    stats.reduce(
                        (sum, stat) => sum + stat.count,
                        0
                    ) / stats.length
                )
                : 0,
    };
};