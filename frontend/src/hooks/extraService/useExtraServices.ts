// src/hooks/useExtraServices.ts

import { useCallback, useEffect, useState } from 'react';
import {extraService, ExtraService} from "../../services/extraService";

export interface UseExtraServicesOptions {
    withStats?: boolean;
    onlyActive?: boolean;
    category?: string;
    search?: string;
    carId?: number;
}

export const useExtraServices = (options?: UseExtraServicesOptions) => {
    const [services, setServices] = useState<ExtraService[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    const fetchServices = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            let result: ExtraService[] = [];

            if (options?.search) {
                result = await extraService.searchServices(options.search);
            } else if (options?.category) {
                result = await extraService.getServicesByCategory(options.category);
            } else if (options?.carId && options.carId > 0) {
                result = await extraService.getServicesWithPricesForCar(options.carId);
            } else {
                result = await extraService.getAllServices(
                    options?.withStats || false,
                    options?.onlyActive || false
                );
            }

            setServices(result);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Ошибка загрузки услуг');
            console.error('Error in useExtraServices:', err);
        } finally {
            setLoading(false);
        }
    }, [options?.withStats, options?.onlyActive, options?.category, options?.search, options?.carId]);

    useEffect(() => {
        fetchServices();
    }, [fetchServices]);

    return { services, loading, error, refetch: fetchServices };
};