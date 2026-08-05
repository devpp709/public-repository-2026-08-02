import {useCallback, useEffect, useState} from "react";
import {CategoryOption, featureService} from "../../services/featureService";

/**
 * Хук для получения категорий
 */
export const useFeatureCategories = () => {
    const [categories, setCategories] = useState<CategoryOption[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    const fetchCategories = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const result = await featureService.getAllCategories();
            setCategories(result);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Ошибка загрузки категорий');
            console.error('Error in useFeatureCategories:', err);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchCategories();
    }, [fetchCategories]);

    return { categories, loading, error, refetch: fetchCategories };
};
