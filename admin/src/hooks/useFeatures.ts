import { useCallback, useEffect, useState } from 'react';
import featuresService, {
    Feature,
} from '../services/FeaturesService';

export function useFeatures() {
    const [features, setFeatures] = useState<Feature[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const loadFeatures = useCallback(async () => {
        try {
            setLoading(true);
            setError(null);

            const data = await featuresService.getAllFeatures();

            setFeatures(data);
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
        loadFeatures();
    }, [loadFeatures]);

    return {
        features,
        loading,
        error,
        reload: loadFeatures,
    };
}