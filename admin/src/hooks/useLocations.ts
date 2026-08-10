import { useCallback, useEffect, useState } from 'react';
import locationsService, {
    Feature,
} from '../services/LocationsService';

export function useLocations() {
    const [locations, setLocations] = useState<Feature[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const loadLocations = useCallback(async () => {
        try {
            setLoading(true);
            setError(null);

            const data = await locationsService.getAllLocations();

            setLocations(data);
        } catch (e) {
            setError(
                e instanceof Error
                    ? e.message
                    : 'Не удалось загрузить комплектации'
            );
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        loadLocations();
    }, [loadLocations]);

    return {
        locations,
        loading,
        error,
        reload: loadLocations,
    };
}