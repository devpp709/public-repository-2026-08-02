import { api } from './api';

export interface CarClass {
    id: number;
    name: string;
    description: string | null;
    icon: string | null;
    dailyRate: number | null;
    hourlyRate: number | null;
    createdAt: string;
    updatedAt: string;
    carsCount: number;
}

interface CarClassesResponse {
    data: CarClass[];
}

class CarClassesService {
    private readonly baseEndpoint = '/api/admin/car-classes';

    async getAllClasses(token?: string): Promise<CarClass[]> {
        const response = await api.get<CarClassesResponse>(
            this.baseEndpoint,
            token
        );

        return response.data;
    }

    async getClassById(
        id: number,
        token?: string
    ): Promise<CarClass> {
        const response = await api.get<CarClass | { data: CarClass }>(
            `${this.baseEndpoint}/${id}`,
            token
        );

        return 'data' in response ? response.data : response;
    }

    async getClassesWithAvailableCars(
        token?: string
    ): Promise<CarClass[]> {
        const response = await api.get<CarClassesResponse>(
            `${this.baseEndpoint}/available`,
            token
        );

        return response.data;
    }

    async searchClasses(
        search: string,
        token?: string
    ): Promise<CarClass[]> {
        const response = await api.get<CarClassesResponse>(
            `${this.baseEndpoint}/search?search=${encodeURIComponent(search)}`,
            token
        );

        return response.data;
    }
}

export const carClassesService = new CarClassesService();

export default carClassesService;
