import { useEffect, useState } from "react";
import { carClassesService, CarClass } from "../services/CarClassesService.ts";

export function useCarClasses() {
    const [classes, setClasses] = useState<CarClass[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = async () => {
        try {
            setLoading(true);
            setError(null);

            const data = await carClassesService.getAllClasses();
            setClasses(data);
        } catch (e) {
            setError("Не удалось загрузить классы автомобилей");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        load();
    }, []);

    return {
        classes,
        loading,
        error,
        reload: load,
    };
}