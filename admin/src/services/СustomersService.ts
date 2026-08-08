import { api } from './api';

// ==================== Типы и интерфейсы ====================

export interface DailyStat {
    date: string;
    count: number;
}

export interface CustomersStatisticsData {
    totalCustomers: number;
    newCustomers: number;
    growthPercentage: number;
    trend: 'up' | 'down' | 'stable';
    dailyStats: DailyStat[];
}

export interface CustomersStatisticsMeta {
    period_days: number;
    period_start: string;
    period_end: string;
    previous_new_customers: number;
}

export interface CustomersStatisticsResponse {
    data: CustomersStatisticsData;
    meta: CustomersStatisticsMeta;
}

export interface CustomersCountResponse {
    total_customers: number;
    timestamp: string;
}

export interface CustomersGrowthData {
    new_customers: number;
    previous_new_customers: number;
    growth_percentage: number;
    trend: 'up' | 'down' | 'stable';
    total_customers: number;
}

export interface CustomersGrowthResponse {
    data: CustomersGrowthData;
    period: {
        days: number;
        start: string;
        end: string;
    };
    daily_stats: DailyStat[];
}

export interface GetStatisticsParams {
    days?: number; // 1-90, default: 7
}

export interface GetGrowthParams {
    days?: number; // 1-90, default: 7
}

// ==================== Сервис ====================

class CustomersService {
    private readonly baseEndpoint = '/api/admin/customers';

    /**
     * Получение полной статистики по клиентам
     * @param params - параметры запроса
     * @param params.days - количество дней для анализа (1-90, по умолчанию 7)
     * @param token - токен авторизации (опционально)
     * @returns Promise с данными статистики
     */
    async getStatistics(
        params: GetStatisticsParams = {},
        token?: string
    ): Promise<CustomersStatisticsResponse> {
        const { days = 7 } = params;
        const endpoint = `${this.baseEndpoint}/statistics?days=${days}`;

        return api.get<CustomersStatisticsResponse>(endpoint, token);
    }

    /**
     * Получение общего количества клиентов
     * @param token - токен авторизации (опционально)
     * @returns Promise с количеством клиентов
     */
    async getCount(token?: string): Promise<CustomersCountResponse> {
        const endpoint = `${this.baseEndpoint}/count`;
        return api.get<CustomersCountResponse>(endpoint, token);
    }

    /**
     * Получение данных о динамике роста клиентов
     * @param params - параметры запроса
     * @param params.days - количество дней для анализа (1-90, по умолчанию 7)
     * @param token - токен авторизации (опционально)
     * @returns Promise с данными о росте
     */
    async getGrowth(
        params: GetGrowthParams = {},
        token?: string
    ): Promise<CustomersGrowthResponse> {
        const { days = 7 } = params;
        const endpoint = `${this.baseEndpoint}/growth?days=${days}`;

        return api.get<CustomersGrowthResponse>(endpoint, token);
    }

    /**
     * Получение статистики с автоматическим определением периода
     * @param period - 'week' | 'month' | 'quarter'
     * @param token - токен авторизации (опционально)
     * @returns Promise с данными статистики
     */
    async getStatisticsByPeriod(
        period: 'week' | 'month' | 'quarter',
        token?: string
    ): Promise<CustomersStatisticsResponse> {
        const daysMap = {
            week: 7,
            month: 30,
            quarter: 90,
        };

        const days = daysMap[period];
        return this.getStatistics({ days }, token);
    }

    /**
     * Получение данных для графиков (ежедневная динамика)
     * @param days - количество дней (1-90)
     * @param token - токен авторизации (опционально)
     * @returns Promise с ежедневной статистикой
     */
    async getDailyStats(days: number = 7, token?: string): Promise<DailyStat[]> {
        const response = await this.getStatistics({ days }, token);
        return response.data.daily_stats;
    }

    // ==================== Вспомогательные методы ====================

    /**
     * Форматирование процента роста для отображения
     * @param percentage - процент роста
     * @param trend - тренд ('up' | 'down' | 'stable')
     * @returns отформатированная строка с процентом и стрелкой
     */
    formatGrowthDisplay(percentage: number, trend: 'up' | 'down' | 'stable'): string {
        const sign = percentage > 0 ? '+' : '';
        const arrow = trend === 'up' ? '↑' : trend === 'down' ? '↓' : '→';
        return `${arrow} ${sign}${percentage.toFixed(2)}%`;
    }

    /**
     * Получение цвета для отображения тренда
     * @param trend - тренд ('up' | 'down' | 'stable')
     * @returns цвет в формате CSS
     */
    getTrendColor(trend: 'up' | 'down' | 'stable'): string {
        const colors = {
            up: '#22c55e',    // green-500
            down: '#ef4444',  // red-500
            stable: '#6b7280' // gray-500
        };
        return colors[trend];
    }

    /**
     * Получение текстового описания тренда
     * @param trend - тренд ('up' | 'down' | 'stable')
     * @returns текстовое описание
     */
    getTrendText(trend: 'up' | 'down' | 'stable'): string {
        const texts = {
            up: 'Рост',
            down: 'Падение',
            stable: 'Стабильно'
        };
        return texts[trend];
    }

    /**
     * Получение иконки для тренда
     * @param trend - тренд ('up' | 'down' | 'stable')
     * @returns строка с эмодзи или символом
     */
    getTrendIcon(trend: 'up' | 'down' | 'stable'): string {
        const icons = {
            up: '📈',
            down: '📉',
            stable: '➖'
        };
        return icons[trend];
    }

    /**
     * Проверка валидности параметров
     * @param days - количество дней
     * @returns true если валидно
     */
    validateDays(days: number): boolean {
        return days >= 1 && days <= 90;
    }

    /**
     * Получение максимального значения для построения графика
     * @param dailyStats - массив ежедневной статистики
     * @returns максимальное значение
     */
    getMaxDailyCount(dailyStats: DailyStat[]): number {
        return Math.max(...dailyStats.map(d => d.count), 1);
    }

    /**
     * Подготовка данных для построения графика
     * @param dailyStats - массив ежедневной статистики
     * @param maxHeight - максимальная высота в пикселях
     * @returns массив с рассчитанными высотами
     */
    prepareChartData(dailyStats: DailyStat[], maxHeight: number = 100): Array<DailyStat & { height: number }> {
        const max = this.getMaxDailyCount(dailyStats);

        return dailyStats.map(stat => ({
            ...stat,
            height: Math.round((stat.count / max) * maxHeight)
        }));
    }

    /**
     * Получение читаемого периода для отображения
     * @param startDate - дата начала периода
     * @param endDate - дата конца периода
     * @param locale - локаль (по умолчанию 'ru-RU')
     * @returns отформатированная строка периода
     */
    formatPeriod(startDate: string, endDate: string, locale: string = 'ru-RU'): string {
        const start = new Date(startDate);
        const end = new Date(endDate);

        const options: Intl.DateTimeFormatOptions = {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };

        return `${start.toLocaleDateString(locale, options)} - ${end.toLocaleDateString(locale, options)}`;
    }
}

export const customersService = new CustomersService();
export default customersService;