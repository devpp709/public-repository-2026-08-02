import { api } from './api';

export interface DailyBookingStat {
    date: string;
    count: number;
}

export interface BookingsStatisticsData {
    totalBookings: number;
    newBookings: number;
    growthPercentage: number;
    trend: 'up' | 'down' | 'stable';
    dailyStats: DailyBookingStat[];
}

export interface BookingsStatisticsMeta {
    period_days: number;
    period_start: string;
    period_end: string;
    previous_new_bookings: number;
}

export interface BookingsStatisticsResponse {
    data: BookingsStatisticsData;
    meta: BookingsStatisticsMeta;
}

export interface GetBookingsStatisticsParams {
    days?: number;
}

class BookingsService {
    private readonly baseEndpoint = '/api/admin/bookings';

    async getStatistics(
        params: GetBookingsStatisticsParams = {},
        token?: string
    ): Promise<BookingsStatisticsResponse> {
        const { days = 7 } = params;

        return api.get<BookingsStatisticsResponse>(
            `${this.baseEndpoint}/statistics?days=${days}`,
            token
        );
    }

    async getStatisticsByPeriod(
        period: 'week' | 'month',
        token?: string
    ): Promise<BookingsStatisticsResponse> {
        return this.getStatistics(
            {
                days: period === 'week' ? 7 : 30
            },
            token
        );
    }

    getMaxDailyCount(dailyStats: DailyBookingStat[]): number {
        return Math.max(
            ...dailyStats.map(stat => stat.count),
            1
        );
    }

    formatGrowthDisplay(
        percentage: number,
        trend: 'up' | 'down' | 'stable'
    ): string {
        const sign = percentage > 0 ? '+' : '';
        const arrow =
            trend === 'up'
                ? '↑'
                : trend === 'down'
                    ? '↓'
                    : '→';

        return `${arrow} ${sign}${percentage.toFixed(2)}%`;
    }

    getTrendColor(
        trend: 'up' | 'down' | 'stable'
    ): string {
        const colors = {
            up: '#22c55e',
            down: '#ef4444',
            stable: '#6b7280',
        };

        return colors[trend];
    }
}

export const bookingsService = new BookingsService();

export default bookingsService;