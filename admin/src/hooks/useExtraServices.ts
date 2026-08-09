import { useCallback, useEffect, useState } from 'react';
import extraServicesService, {
    ExtraService,
} from '../services/ExtraServicesService';

export function useExtraServices() {
    const [extraServices, setExtraServices] = useState<ExtraService[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const loadExtraServices = useCallback(async () => {
        try {
            setLoading(true);
            setError(null);

            const data = await extraServicesService.getAllExtraServices();

            setExtraServices(data);
        } catch (e) {
            setError(
                e instanceof Error
                    ? e.message
                    : 'Не удалось загрузить доп. услуги'
            );
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        loadExtraServices();
    }, [loadExtraServices]);

    return {
        extraServices,
        loading,
        error,
        reload: loadExtraServices,
    };
}