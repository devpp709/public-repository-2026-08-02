// src/services/featureService.ts

const API_URL = process.env.NEXT_PUBLIC_API_URL || '';

export interface Feature {
    id: number;
    name: string;
    icon?: string;
    category?: string;
    categoryCode?: string;
    categoryLabel?: string;
    createdAt?: string;
    updatedAt?: string;
    usageCount?: number;
    carsCount?: number;
}

export interface FeatureRequest {
    name: string;
    icon?: string;
    category?: string;
    categoryCode?: string;
}

export interface FeatureStatistics {
    category: string;
    categoryCode: string;
    label: string;
    total: number;
}

export interface CategoryOption {
    value: string;
    label: string;
    code?: string;
}

export interface PopularFeature extends Feature {
    usage_count: number;
}

export interface FeatureWithCars extends Feature {
    cars_count: number;
    cars?: Array<{
        id: number;
        brand: string;
        model: string;
    }>;
}

export interface FeatureResponse {
    success: boolean;
    data: Feature[] | Feature | FeatureStatistics[] | CategoryOption[] | PopularFeature[] | FeatureWithCars[];
    total?: number;
    message?: string;
}

export interface CheckNameResponse {
    success: boolean;
    exists: boolean;
}

export const featureService = {
    /**
     * Получить все характеристики
     * @param withStats - включить статистику использования
     */
    getAllFeatures: async (withStats: boolean = false): Promise<Feature[]> => {
        try {
            const params = new URLSearchParams();
            if (withStats) params.append('with_stats', 'true');

            const response = await fetch(`${API_URL}/v1/features?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: FeatureResponse = await response.json();
            return (data.data as Feature[]) || [];
        } catch (error) {
            console.error('Error fetching features:', error);
            return [];
        }
    },

    /**
     * Получить характеристику по ID
     * @param id - ID характеристики
     * @param withStats - включить статистику использования
     */
    getFeatureById: async (id: number, withStats: boolean = true): Promise<Feature | null> => {
        try {
            const params = new URLSearchParams();
            if (withStats) params.append('with_stats', 'true');

            const response = await fetch(`${API_URL}/v1/features/${id}?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: FeatureResponse = await response.json();
            return (data.data as Feature) || null;
        } catch (error) {
            console.error(`Error fetching feature ${id}:`, error);
            return null;
        }
    },

    /**
     * Создать новую характеристику
     */
    createFeature: async (featureData: FeatureRequest): Promise<Feature | null> => {
        try {
            const response = await fetch(`${API_URL}/v1/features`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(featureData),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            const data: FeatureResponse = await response.json();
            return (data.data as Feature) || null;
        } catch (error) {
            console.error('Error creating feature:', error);
            throw error;
        }
    },

    /**
     * Обновить характеристику
     */
    updateFeature: async (id: number, featureData: Partial<FeatureRequest>): Promise<Feature | null> => {
        try {
            const response = await fetch(`${API_URL}/v1/features/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(featureData),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            const data: FeatureResponse = await response.json();
            return (data.data as Feature) || null;
        } catch (error) {
            console.error(`Error updating feature ${id}:`, error);
            throw error;
        }
    },

    /**
     * Удалить характеристику
     */
    deleteFeature: async (id: number): Promise<boolean> => {
        try {
            const response = await fetch(`${API_URL}/v1/features/${id}`, {
                method: 'DELETE',
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data.success === true;
        } catch (error) {
            console.error(`Error deleting feature ${id}:`, error);
            throw error;
        }
    },

    /**
     * Поиск характеристик
     */
    searchFeatures: async (searchTerm: string): Promise<Feature[]> => {
        try {
            const params = new URLSearchParams();
            params.append('search', searchTerm);

            const response = await fetch(`${API_URL}/v1/features?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: FeatureResponse = await response.json();
            return (data.data as Feature[]) || [];
        } catch (error) {
            console.error('Error searching features:', error);
            return [];
        }
    },

    /**
     * Получить характеристики по категории
     */
    getFeaturesByCategory: async (category: string): Promise<Feature[]> => {
        try {
            const params = new URLSearchParams();
            params.append('category', category);

            const response = await fetch(`${API_URL}/v1/features?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: FeatureResponse = await response.json();
            return (data.data as Feature[]) || [];
        } catch (error) {
            console.error('Error fetching features by category:', error);
            return [];
        }
    },

    /**
     * Получить характеристики по ID автомобиля
     */
    getFeaturesByCarId: async (carId: number): Promise<Feature[]> => {
        try {
            const params = new URLSearchParams();
            params.append('car_id', String(carId));

            const response = await fetch(`${API_URL}/v1/features?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: FeatureResponse = await response.json();
            return (data.data as Feature[]) || [];
        } catch (error) {
            console.error('Error fetching features by car ID:', error);
            return [];
        }
    },

    /**
     * Получить характеристики с автомобилями
     */
    getFeaturesWithCars: async (): Promise<FeatureWithCars[]> => {
        try {
            const response = await fetch(`${API_URL}/v1/features/with-cars`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: FeatureResponse = await response.json();
            return (data.data as FeatureWithCars[]) || [];
        } catch (error) {
            console.error('Error fetching features with cars:', error);
            return [];
        }
    },

    /**
     * Получить популярные характеристики
     */
    getPopularFeatures: async (limit: number = 10): Promise<PopularFeature[]> => {
        try {
            const params = new URLSearchParams();
            params.append('limit', String(limit));

            const response = await fetch(`${API_URL}/v1/features/popular?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: FeatureResponse = await response.json();
            return (data.data as PopularFeature[]) || [];
        } catch (error) {
            console.error('Error fetching popular features:', error);
            return [];
        }
    },

    /**
     * Получить статистику по категориям
     */
    getCategoryStatistics: async (): Promise<FeatureStatistics[]> => {
        try {
            const response = await fetch(`${API_URL}/v1/features/statistics`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: FeatureResponse = await response.json();
            return (data.data as FeatureStatistics[]) || [];
        } catch (error) {
            console.error('Error fetching category statistics:', error);
            return [];
        }
    },

    /**
     * Получить все категории
     */
    getAllCategories: async (): Promise<CategoryOption[]> => {
        try {
            const response = await fetch(`${API_URL}/v1/features/categories`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: FeatureResponse = await response.json();
            return (data.data as CategoryOption[]) || [];
        } catch (error) {
            console.error('Error fetching categories:', error);
            return [];
        }
    },

    /**
     * Проверить существование характеристики по имени
     */
    checkNameExists: async (name: string, excludeId?: number): Promise<boolean> => {
        try {
            const params = new URLSearchParams();
            params.append('name', name);
            if (excludeId) {
                params.append('exclude_id', String(excludeId));
            }

            const response = await fetch(`${API_URL}/v1/features/check-name?${params.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data: CheckNameResponse = await response.json();
            return data.exists || false;
        } catch (error) {
            console.error('Error checking feature name:', error);
            return false;
        }
    },
};