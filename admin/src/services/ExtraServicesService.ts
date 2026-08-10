import { api } from './api';

export interface ExtraService {
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

interface ExtraServicesResponse {
    data: ExtraService[];
}

class ExtraServicesService {
    private readonly baseEndpoint = '/api/admin/extra-services';

    async getAllExtraServices(token?: string): Promise<ExtraService[]> {
        const response = await api.get<ExtraServicesResponse>(
            this.baseEndpoint,
            token
        );

        return response.data;
    }

    async getExtraServiceById(
        id: number,
        token?: string
    ): Promise<ExtraService> {
        const response = await api.get<{ data: ExtraService }>(
            `${this.baseEndpoint}/${id}`,
            token
        );

        return response.data;
    }

    async searchExtraServices(
        search: string,
        token?: string
    ): Promise<ExtraService[]> {
        const response = await api.get<ExtraServicesResponse>(
            `${this.baseEndpoint}/search?q=${encodeURIComponent(search)}`,
            token
        );

        return response.data;
    }
}

export const extraServiceService = new ExtraServicesService();

export default extraServiceService;