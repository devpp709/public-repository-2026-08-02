import { api } from './api';

export interface Feature {
    id: number;
    name: string;
    icon: string | null;
    category: string | null;
    categoryLabel: string | null;
    categoryCode: string | null;
    createdAt: string;
    updatedAt: string;
    carsCount: number;
    usageCount: number;
}

interface FeaturesResponse {
    data: Feature[];
}

class FeaturesService {
    private readonly baseEndpoint = '/api/admin/cars/features';

    async getAllFeatures(token?: string): Promise<Feature[]> {
        const response = await api.get<FeaturesResponse>(
            this.baseEndpoint,
            token
        );

        return response.data;
    }

    async getFeatureById(
        id: number,
        token?: string
    ): Promise<Feature> {
        const response = await api.get<{ data: Feature }>(
            `${this.baseEndpoint}/${id}`,
            token
        );

        return response.data;
    }

    async searchFeatures(
        search: string,
        token?: string
    ): Promise<Feature[]> {
        const response = await api.get<FeaturesResponse>(
            `${this.baseEndpoint}/search?q=${encodeURIComponent(search)}`,
            token
        );

        return response.data;
    }
}

export const featuresService = new FeaturesService();

export default featuresService;