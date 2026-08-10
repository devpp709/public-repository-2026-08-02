// src/services/extraServicesService.ts

import { api } from './api';

export interface ExtraService {
    id: number;
    name: string;
    description?: string;
    icon?: string;
    category?: string;
    categoryLabel?: string;
    defaultPrice: number | null;
    isActive: boolean;
    createdAt: string;
    updatedAt: string;
    carsCount?: number;
    usageCount?: number;
}

export interface ExtraServicesResponse {
    data: ExtraService[];
}

export interface CreateExtraServiceRequest {
    name: string;
    description?: string | null;
    icon?: string;
    category: string;
    defaultPrice: number;
    isActive: boolean;
}

export interface UpdateExtraServiceRequest extends Partial<CreateExtraServiceRequest> {}

class ExtraServicesService {
    private readonly baseEndpoint = '/api/admin/extra-services';

    async getExtraServices(token?: string): Promise<ExtraServicesResponse> {
        return api.get<ExtraServicesResponse>(this.baseEndpoint, token);
    }

    async getExtraServiceById(id: number, token?: string): Promise<ExtraService> {
        return api.get<ExtraService>(`${this.baseEndpoint}/${id}`, token);
    }

    async createExtraService(data: CreateExtraServiceRequest, token?: string): Promise<ExtraService> {
        return api.post<ExtraService>(this.baseEndpoint, data, token);
    }

    async updateExtraService(id: number, data: UpdateExtraServiceRequest, token?: string): Promise<ExtraService> {
        return api.put<ExtraService>(`${this.baseEndpoint}/${id}`, data, token);
    }

    async deleteExtraService(id: number, token?: string): Promise<void> {
        return api.delete<void>(`${this.baseEndpoint}/${id}`, token);
    }

    async toggleActive(id: number, isActive: boolean, token?: string): Promise<ExtraService> {
        return api.patch<ExtraService>(
            `${this.baseEndpoint}/${id}/toggle-active`,
            { isActive },
            token
        );
    }

    async getCategories(token?: string): Promise<{ data: { value: string; label: string }[] }> {
        return api.get<{ data: { value: string; label: string }[] }>(
            `${this.baseEndpoint}/categories`,
            token
        );
    }
}

export const extraServicesService = new ExtraServicesService();
export default extraServicesService;