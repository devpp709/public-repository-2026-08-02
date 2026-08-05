import {useCallback, useState} from "react";
import {Feature, FeatureRequest, featureService} from "../../services/featureService";

/**
 * Хук для управления характеристиками (CRUD операции)
 */
export const useFeatureManager = () => {
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const createFeature = useCallback(async (data: FeatureRequest): Promise<Feature | null> => {
        setLoading(true);
        setError(null);

        try {
            return await featureService.createFeature(data);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Ошибка создания характеристики');
            console.error('Error in createFeature:', err);
            throw err;
        } finally {
            setLoading(false);
        }
    }, []);

    const updateFeature = useCallback(async (id: number, data: Partial<FeatureRequest>): Promise<Feature | null> => {
        setLoading(true);
        setError(null);

        try {
            return await featureService.updateFeature(id, data);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Ошибка обновления характеристики');
            console.error(`Error in updateFeature ${id}:`, err);
            throw err;
        } finally {
            setLoading(false);
        }
    }, []);

    const deleteFeature = useCallback(async (id: number): Promise<boolean> => {
        setLoading(true);
        setError(null);

        try {
            return await featureService.deleteFeature(id);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Ошибка удаления характеристики');
            console.error(`Error in deleteFeature ${id}:`, err);
            throw err;
        } finally {
            setLoading(false);
        }
    }, []);

    return {
        loading,
        error,
        createFeature,
        updateFeature,
        deleteFeature,
    };
};
