// src/hooks/useExtraServiceManager.ts

import { useCallback, useState } from "react";
import {extraService, ExtraService, ExtraServiceRequest} from "../../services/extraService";



export const useExtraServiceManager = () => {
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const createService = useCallback(async (data: ExtraServiceRequest): Promise<ExtraService | null> => {
        setLoading(true);
        setError(null);

        try {
            return await extraService.createService(data);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Ошибка создания услуги');
            console.error('Error in createService:', err);
            throw err;
        } finally {
            setLoading(false);
        }
    }, []);

    const updateService = useCallback(async (id: number, data: Partial<ExtraServiceRequest>): Promise<ExtraService | null> => {
        setLoading(true);
        setError(null);

        try {
            return await extraService.updateService(id, data);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Ошибка обновления услуги');
            console.error(`Error in updateService ${id}:`, err);
            throw err;
        } finally {
            setLoading(false);
        }
    }, []);

    const deleteService = useCallback(async (id: number): Promise<boolean> => {
        setLoading(true);
        setError(null);

        try {
            return await extraService.deleteService(id);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Ошибка удаления услуги');
            console.error(`Error in deleteService ${id}:`, err);
            throw err;
        } finally {
            setLoading(false);
        }
    }, []);

    return {
        loading,
        error,
        createService,
        updateService,
        deleteService,
    };
};