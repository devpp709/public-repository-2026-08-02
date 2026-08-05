import { useState, useEffect } from 'react';
import { useApi } from '../lib/api';

interface CarClass {
    id: string;
    name: string;
    value: string;
}

const CACHE_KEY = 'car_classes';

export function useCarClasses() {
    const [carClasses, setCarClasses] = useState<CarClass[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const { request } = useApi();

    const fetchCarClasses = async (search?: string) => {
        try {
            setIsLoading(true);
            setError(null);

            // кеш используем только без поиска
            if (!search) {
                const cached = localStorage.getItem(CACHE_KEY);

                if (cached) {
                    console.log('Car classes from cache');

                    setCarClasses(JSON.parse(cached));
                    return;
                }
            }

            const url = search
                ? `/api/v1/car-classes?search=${encodeURIComponent(search)}`
                : '/api/v1/car-classes';


            const response = await request(url);

            if (!response.ok) {
                throw new Error(
                    `Failed to fetch car classes: ${response.status}`
                );
            }


            const data = await response.json();

            const classes = data.data || [];

            setCarClasses(classes);


            // кешируем только полный список
            if (!search) {
                localStorage.setItem(
                    CACHE_KEY,
                    JSON.stringify(classes)
                );
            }

        } catch (err) {
            console.error('Error fetching car classes:', err);

            setError(
                err instanceof Error
                    ? err.message
                    : 'Unknown error'
            );

            // никаких fallback данных
            setCarClasses([]);

        } finally {
            setIsLoading(false);
        }
    };


    useEffect(() => {
        fetchCarClasses();
    }, []);


    return {
        carClasses,
        isLoading,
        error,
        fetchCarClasses,
        refetch: fetchCarClasses
    };
}