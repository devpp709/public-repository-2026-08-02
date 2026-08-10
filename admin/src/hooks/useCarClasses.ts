import {useCallback, useEffect, useState} from "react";
import { carClassesService, CarClass } from "../services/CarClassesService.ts";

export function useCarClasses() {
    const [classes, setClasses] = useState<CarClass[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const fetchClasses = useCallback(async () => {
        try {
            setLoading(true);
            setError(null);
            const response = await carClassesService.getCarClasses();
            setClasses(response.data || []);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to fetch classes');
        } finally {
            setLoading(false);
        }
    }, []);

    const createClass = useCallback(async (data: any) => {
        const response = await carClassesService.createCarClass(data);
        return response;
    }, []);

    const updateClass = useCallback(async (id: number, data: any) => {
        const response = await carClassesService.updateCarClass(id, data);
        return response;
    }, []);

    const deleteClass = useCallback(async (id: number) => {
        await carClassesService.deleteCarClass(id);
    }, []);

    useEffect(() => {
        fetchClasses();
    }, [fetchClasses]);

    return {
        classes,
        loading,
        error,
        refresh: fetchClasses,
        createClass,
        updateClass,
        deleteClass,
    };
}