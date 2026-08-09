import { useCallback, useEffect, useState } from 'react';
import {
    BookingsStatisticsResponse,
    bookingsService,
    DailyBookingStat, MonthlyBookingStatsResponse, RegionBookingStat, LatestBooking, BookingListItem,
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

export const useBookingsMonthlyStatistics = (
    year: number = new Date().getFullYear(),
    autoFetch: boolean = true
) => {
    const [data, setData] =
        useState<MonthlyBookingStatsResponse | null>(null);

    const [loading, setLoading] = useState(true);
    const [error, setError] =
        useState<string | null>(null);

    const fetchData = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const token =
                localStorage.getItem('auth_token') || undefined;

            const response =
                await bookingsService.getMonthlyStatistics(
                    year,
                    token
                );

            setData(response);
        } catch (err) {
            const errorMessage =
                err instanceof Error
                    ? err.message
                    : 'Не удалось загрузить статистику заказов';

            setError(errorMessage);

            console.error(
                'Error fetching monthly booking statistics:',
                err
            );
        } finally {
            setLoading(false);
        }
    }, [year]);

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
    };
};

export interface UseBookingsChartStatisticsReturn {
    data: {
        categories: string[];
        series: {
            name: string;
            data: number[];
        }[];
    } | null;
    loading: boolean;
    error: string | null;
    refetch: () => Promise<void>;
}

export interface UseBookingsChartStatisticsParams {
    period: "month" | "quarter" | "year" | "custom";
    start?: string | null;
    end?: string | null;
    autoFetch?: boolean;
}

export const useBookingsChartStatistics = (
    params: UseBookingsChartStatisticsParams,
): UseBookingsChartStatisticsReturn => {
    const {
        period,
        start,
        end,
        autoFetch = true,
    } = params;

    const [data, setData] =
        useState<UseBookingsChartStatisticsReturn['data']>(null);

    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const fetchData = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const token =
                localStorage.getItem('auth_token') || undefined;

            const response =
                await bookingsService.getStatisticsChartByPeriod(
                    {
                        period,
                        start,
                        end,
                    },
                    token
                );

            setData(response.data);
        } catch (err) {
            const errorMessage =
                err instanceof Error
                    ? err.message
                    : 'Не удалось загрузить статистику заказов';

            setError(errorMessage);
        } finally {
            setLoading(false);
        }
    }, [period, start, end]);

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
    };
};

export interface UseBookingsRegionStatisticsReturn {
    data: Record<string, RegionBookingStat>;
    loading: boolean;
    error: string | null;
    refetch: () => Promise<void>;
}

export const useBookingsRegionStatistics = (
    autoFetch: boolean = true
): UseBookingsRegionStatisticsReturn => {
    const [data, setData] = useState<Record<string, RegionBookingStat>>({});
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const fetchData = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const token =
                localStorage.getItem("auth_token") || undefined;

            const response =
                await bookingsService.getRegionStatistics(token);

            const regions: Record<string, RegionBookingStat> = {};

            response.data.forEach((region) => {
                regions[region.code] = region;
            });

            setData(regions);
        } catch (err) {
            const errorMessage =
                err instanceof Error
                    ? err.message
                    : "Не удалось загрузить статистику регионов";

            setError(errorMessage);

            console.error(
                "Error fetching region booking statistics:",
                err
            );
        } finally {
            setLoading(false);
        }
    }, []);

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
    };
};

export interface UseLatestBookingsReturn {
    data: LatestBooking[];
    loading: boolean;
    error: string | null;
    refetch: () => Promise<void>;
}

export const useLatestBookings = (
    limit: number = 5,
    autoFetch: boolean = true
): UseLatestBookingsReturn => {
    const [data, setData] = useState<LatestBooking[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const fetchData = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const token =
                localStorage.getItem('auth_token') || undefined;

            const response =
                await bookingsService.getLatest(limit, token);

            setData(response.data);
        } catch (err) {
            const errorMessage =
                err instanceof Error
                    ? err.message
                    : 'Не удалось загрузить последние заказы';

            setError(errorMessage);

            console.error(
                'Error fetching latest bookings:',
                err
            );
        } finally {
            setLoading(false);
        }
    }, [limit]);

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
    };
};


export interface UseBookingsReturn {
    bookings: BookingListItem[];
    loading: boolean;
    error: string | null;
    refetch: () => Promise<void>;
}


export const useBookings = (
    autoFetch: boolean = true
): UseBookingsReturn => {
    const [bookings, setBookings] = useState<BookingListItem[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const fetchBookings = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const token =
                localStorage.getItem('auth_token') || undefined;

            const response =
                await bookingsService.getAllBookings(token);

            setBookings(response.data);
        } catch (err) {
            const errorMessage =
                err instanceof Error
                    ? err.message
                    : 'Не удалось загрузить заказы';

            setError(errorMessage);

            console.error(
                'Error fetching bookings:',
                err
            );
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        if (autoFetch) {
            fetchBookings();
        }
    }, [fetchBookings, autoFetch]);

    return {
        bookings,
        loading,
        error,
        refetch: fetchBookings,
    };
};