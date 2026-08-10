// src/hooks/useExtraServices.ts

import {useCallback, useEffect, useState} from 'react';
import extraServicesService from '../services/ExtraServicesService';

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
    priceForCar?: number | null;
    isRequiredForCar?: boolean | null;
}

export function useExtraServices() {
    const [extraServices, setExtraServices] = useState<ExtraService[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const fetchExtraServices = useCallback(async () => {
        try {
            setLoading(true);
            setError(null);
            const response = await extraServicesService.getExtraServices();
            setExtraServices(response.data || []);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to fetch extra services');
        } finally {
            setLoading(false);
        }
    }, []);

    const createExtraService = useCallback(async (data: any) => {
        return await extraServicesService.createExtraService(data);
    }, []);

    const updateExtraService = useCallback(async (id: number, data: any) => {
        return await extraServicesService.updateExtraService(id, data);
    }, []);

    const deleteExtraService = useCallback(async (id: number) => {
        await extraServicesService.deleteExtraService(id);
    }, []);

    useEffect(() => {
        fetchExtraServices();
    }, [fetchExtraServices]);

    return {
        extraServices,
        loading,
        error,
        refresh: fetchExtraServices,
        createExtraService,
        updateExtraService,
        deleteExtraService,
    };
}