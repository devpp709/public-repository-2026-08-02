// src/hooks/useExtraService.ts

import { useCallback, useEffect, useState } from "react";
import {extraService, ExtraService} from "../../services/extraService";

export const useExtraService = (id: number, withStats: boolean = false) => {
    const [service, setService] = useState<ExtraService | null>(null);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    const fetchService = useCallback(async () => {
        if (!id) {
            setLoading(false);
            return;
        }

        setLoading(true);
        setError(null);

        try {
            const result = await extraService.getServiceById(id, withStats);
            setService(result);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Ошибка загрузки услуги');
            console.error(`Error in useExtraService ${id}:`, err);
        } finally {
            setLoading(false);
        }
    }, [id, withStats]);

    useEffect(() => {
        fetchService();
    }, [fetchService]);

    return { service, loading, error, refetch: fetchService };
};