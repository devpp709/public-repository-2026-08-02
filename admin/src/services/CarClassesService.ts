// src/services/carClassesService.ts

import { api } from './api';

export interface CarClass {
    id: number;
    name: string;
    description?: string;
    icon?: string;
    createdAt: string;
    updatedAt: string;
    carsCount: number;
}

export interface CarClassResponse {
    data: CarClass[];
}

export interface CreateCarClassRequest {
    name: string;
    description?: string | null;
    icon?: string;
}

export interface UpdateCarClassRequest extends Partial<CreateCarClassRequest> {}

class CarClassesService {
    private readonly baseEndpoint = '/api/admin/car-classes';

    async getCarClasses(token?: string): Promise<CarClassResponse> {
        return api.get<CarClassResponse>(this.baseEndpoint, token);
    }

    async getCarClassById(id: number, token?: string): Promise<CarClass> {
        return api.get<CarClass>(`${this.baseEndpoint}/${id}`, token);
    }

    async createCarClass(data: CreateCarClassRequest, token?: string): Promise<CarClass> {
        return api.post<CarClass>(this.baseEndpoint, data, token);
    }

    async updateCarClass(id: number, data: UpdateCarClassRequest, token?: string): Promise<CarClass> {
        return api.put<CarClass>(`${this.baseEndpoint}/${id}`, data, token);
    }

    async deleteCarClass(id: number, token?: string): Promise<void> {
        return api.delete<void>(`${this.baseEndpoint}/${id}`, token);
    }
}

export const carClassesService = new CarClassesService();
export default carClassesService;