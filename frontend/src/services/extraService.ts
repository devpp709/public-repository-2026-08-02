// src/services/extraService.ts

const API_URL = process.env.NEXT_PUBLIC_API_URL || '';

export interface ExtraService {
    id: number;
    name: string;
    description?: string;
    icon?: string;
    category?: string;
    categoryLabel?: string;
    defaultPrice: number;
    isActive: boolean;
    createdAt?: string;
    updatedAt?: string;
    usageCount?: number;
    priceForCar?: number;
    isRequiredForCar?: boolean;
    // Статистика
    totalBookings?: number;
    totalRevenue?: number;
    lastUsedAt?: string;
}

export interface ExtraServiceRequest {
    name: string;
    description?: string;
    icon?: string;
    category?: string;
    defaultPrice?: number;
    isActive?: boolean;
}

export interface ExtraServiceStatistics {
    category: string;
    label: string;
    count: number;
    activeCount: number;
    totalBookings: number;
    totalRevenue: number;
    averagePrice: number;
}

export interface CategoryOption {
    value: string;
    label: string;
}

export interface PopularService {
    id: number;
    name: string;
    category: string;
    categoryLabel: string;
    usageCount: number;
    totalRevenue: number;
    defaultPrice: number;
    isActive: boolean;
}

export interface ExtraServiceResponse {
    success: boolean;
    data: ExtraService[] | ExtraService | ExtraServiceStatistics[] | CategoryOption[] | PopularService[];
    total?: number;
    message?: string;
}

export const extraService = {
    /**
     * Получить все услуги
     * @param withStats - включить статистику использования
     * @param onlyActive - только активные услуги
     */
    getAllServices: async (withStats: boolean = false, onlyActive: boolean = false): Promise<ExtraService[]> => {
        try {
            const params = new URLSearchParams();
            if (withStats) params.append('with_stats', 'true');
            if (onlyActive) params.append('only_active', 'true');

            const response = await fetch(`${API_URL}/v1/extra-services?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: ExtraServiceResponse = await response.json();
            return (data.data as ExtraService[]) || [];
        } catch (error) {
            console.error('Error fetching extra services:', error);
            return [];
        }
    },

    /**
     * Получить услугу по ID
     * @param id - ID услуги
     * @param withStats - включить статистику использования
     */
    getServiceById: async (id: number, withStats: boolean = false): Promise<ExtraService | null> => {
        try {
            const params = new URLSearchParams();
            if (withStats) params.append('with_stats', 'true');

            const response = await fetch(`${API_URL}/v1/extra-services/${id}?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: ExtraServiceResponse = await response.json();
            return (data.data as ExtraService) || null;
        } catch (error) {
            console.error(`Error fetching extra service ${id}:`, error);
            return null;
        }
    },

    /**
     * Создать новую услугу
     */
    createService: async (serviceData: ExtraServiceRequest): Promise<ExtraService | null> => {
        try {
            const response = await fetch(`${API_URL}/v1/extra-services`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(serviceData),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            const data: ExtraServiceResponse = await response.json();
            return (data.data as ExtraService) || null;
        } catch (error) {
            console.error('Error creating extra service:', error);
            throw error;
        }
    },

    /**
     * Обновить услугу
     */
    updateService: async (id: number, serviceData: Partial<ExtraServiceRequest>): Promise<ExtraService | null> => {
        try {
            const response = await fetch(`${API_URL}/v1/extra-services/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(serviceData),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            const data: ExtraServiceResponse = await response.json();
            return (data.data as ExtraService) || null;
        } catch (error) {
            console.error(`Error updating extra service ${id}:`, error);
            throw error;
        }
    },

    /**
     * Удалить услугу
     */
    deleteService: async (id: number): Promise<boolean> => {
        try {
            const response = await fetch(`${API_URL}/v1/extra-services/${id}`, {
                method: 'DELETE',
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data.success === true;
        } catch (error) {
            console.error(`Error deleting extra service ${id}:`, error);
            throw error;
        }
    },

    /**
     * Поиск услуг
     */
    searchServices: async (searchTerm: string): Promise<ExtraService[]> => {
        try {
            const params = new URLSearchParams();
            params.append('search', searchTerm);

            const response = await fetch(`${API_URL}/v1/extra-services?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: ExtraServiceResponse = await response.json();
            return (data.data as ExtraService[]) || [];
        } catch (error) {
            console.error('Error searching extra services:', error);
            return [];
        }
    },

    /**
     * Получить услуги по категории
     */
    getServicesByCategory: async (category: string): Promise<ExtraService[]> => {
        try {
            const params = new URLSearchParams();
            params.append('category', category);

            const response = await fetch(`${API_URL}/v1/extra-services?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: ExtraServiceResponse = await response.json();
            return (data.data as ExtraService[]) || [];
        } catch (error) {
            console.error('Error fetching services by category:', error);
            return [];
        }
    },

    /**
     * Получить услуги с ценами для автомобиля
     */
    getServicesWithPricesForCar: async (carId: number): Promise<ExtraService[]> => {
        try {
            const params = new URLSearchParams();
            params.append('car_id', String(carId));

            const response = await fetch(`${API_URL}/v1/extra-services?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: ExtraServiceResponse = await response.json();
            return (data.data as ExtraService[]) || [];
        } catch (error) {
            console.error('Error fetching services with prices for car:', error);
            return [];
        }
    },

    /**
     * Получить услуги по ID автомобиля (для конкретного автомобиля)
     */
    getServicesByCarId: async (carId: number, onlyActive: boolean = true): Promise<ExtraService[]> => {
        try {
            const params = new URLSearchParams();
            params.append('only_active', String(onlyActive));

            const response = await fetch(`${API_URL}/v1/extra-services/car/${carId}?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: ExtraServiceResponse = await response.json();
            return (data.data as ExtraService[]) || [];
        } catch (error) {
            console.error('Error fetching services by car ID:', error);
            return [];
        }
    },

    /**
     * Получить обязательные услуги для автомобиля
     */
    getRequiredServicesForCar: async (carId: number): Promise<ExtraService[]> => {
        try {
            const response = await fetch(`${API_URL}/v1/extra-services/car/${carId}/required`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: ExtraServiceResponse = await response.json();
            return (data.data as ExtraService[]) || [];
        } catch (error) {
            console.error('Error fetching required services for car:', error);
            return [];
        }
    },

    /**
     * Получить статистику по категориям
     */
    getCategoryStatistics: async (): Promise<ExtraServiceStatistics[]> => {
        try {
            const response = await fetch(`${API_URL}/v1/extra-services/statistics/categories`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: ExtraServiceResponse = await response.json();
            return (data.data as ExtraServiceStatistics[]) || [];
        } catch (error) {
            console.error('Error fetching category statistics:', error);
            return [];
        }
    },

    /**
     * Получить популярные услуги
     */
    getPopularServices: async (limit: number = 10): Promise<PopularService[]> => {
        try {
            const params = new URLSearchParams();
            params.append('limit', String(limit));

            const response = await fetch(`${API_URL}/v1/extra-services/popular?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: ExtraServiceResponse = await response.json();
            return (data.data as PopularService[]) || [];
        } catch (error) {
            console.error('Error fetching popular services:', error);
            return [];
        }
    },

    /**
     * Получить все категории
     */
    getAllCategories: async (): Promise<CategoryOption[]> => {
        try {
            const response = await fetch(`${API_URL}/v1/extra-services/categories`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: ExtraServiceResponse = await response.json();
            return (data.data as CategoryOption[]) || [];
        } catch (error) {
            console.error('Error fetching categories:', error);
            return [];
        }
    },

    /**
     * Получить услуги с ценами для автомобиля (детальный вариант с ценами)
     */
    getServicesWithPrices: async (carId: number): Promise<ExtraService[]> => {
        try {
            const response = await fetch(`${API_URL}/v1/extra-services/with-prices/${carId}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: ExtraServiceResponse = await response.json();
            return (data.data as ExtraService[]) || [];
        } catch (error) {
            console.error('Error fetching services with prices:', error);
            return [];
        }
    },
};
