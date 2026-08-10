// src/services/featuresService.ts

import { api } from './api';

export interface Feature {
    id: number;
    name: string;
    icon?: string;
    category?: string;
    categoryLabel?: string;
    categoryCode?: string;
    createdAt: string;
    updatedAt: string;
}

export interface FeaturesResponse {
    data: Feature[];
}

export interface CreateFeatureRequest {
    name: string;
    icon?: string;
    categoryCode: string;
}

export interface UpdateFeatureRequest extends Partial<CreateFeatureRequest> {}

class FeaturesService {
    private readonly baseEndpoint = '/api/admin/features';

    async getFeatures(token?: string): Promise<FeaturesResponse> {
        return api.get<FeaturesResponse>(this.baseEndpoint, token);
    }

    async getFeatureById(id: number, token?: string): Promise<Feature> {
        return api.get<Feature>(`${this.baseEndpoint}/${id}`, token);
    }

    async createFeature(data: CreateFeatureRequest, token?: string): Promise<Feature> {
        return api.post<Feature>(this.baseEndpoint, data, token);
    }

    async updateFeature(id: number, data: UpdateFeatureRequest, token?: string): Promise<Feature> {
        return api.put<Feature>(`${this.baseEndpoint}/${id}`, data, token);
    }

    async deleteFeature(id: number, token?: string): Promise<void> {
        return api.delete<void>(`${this.baseEndpoint}/${id}`, token);
    }
}

export const featuresService = new FeaturesService();
export default featuresService;