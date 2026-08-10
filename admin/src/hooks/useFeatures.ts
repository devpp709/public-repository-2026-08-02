// src/hooks/useFeatures.ts

import {useCallback, useEffect, useState} from 'react';
import {featuresService} from '../services/FeaturesService';

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

export function useFeatures() {
    const [features, setFeatures] = useState<Feature[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const fetchFeatures = useCallback(async () => {
        try {
            setLoading(true);
            setError(null);
            const response = await featuresService.getFeatures();
            setFeatures(response.data || []);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to fetch features');
        } finally {
            setLoading(false);
        }
    }, []);

    const createFeature = useCallback(async (data: any) => {
        return await featuresService.createFeature(data);
    }, []);

    const updateFeature = useCallback(async (id: number, data: any) => {
        return await featuresService.updateFeature(id, data);
    }, []);

    const deleteFeature = useCallback(async (id: number) => {
        await featuresService.deleteFeature(id);
    }, []);

    useEffect(() => {
        fetchFeatures();
    }, [fetchFeatures]);

    return {
        features,
        loading,
        error,
        refresh: fetchFeatures,
        createFeature,
        updateFeature,
        deleteFeature,
    };
}