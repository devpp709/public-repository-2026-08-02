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

interface LocationsResponse {
    data: Feature[];
}

class LocationsService {
    private readonly baseEndpoint = '/api/admin/locations';

    async getAllLocations(token?: string): Promise<Feature[]> {
        const response = await api.get<LocationsResponse>(
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

    async searchLocations(
        search: string,
        token?: string
    ): Promise<Feature[]> {
        const response = await api.get<LocationsResponse>(
            `${this.baseEndpoint}/search?q=${encodeURIComponent(search)}`,
            token
        );

        return response.data;
    }
}

export const locationsService = new LocationsService();

export default locationsService;