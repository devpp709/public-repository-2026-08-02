// src/hooks/useLocations.ts
const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3001';

import { useState, useEffect, useCallback } from 'react';
import { useApi } from '../lib/api';

interface Location {
    id: number;
    name: string;
    address: string;
    street: string;
    building: string;
    fullAddress: string;
    latitude?: string;
    longitude?: string;
    workingHours?: any;
    extraInfo?: string;
    phone?: string;
    email?: string;
    isActive: boolean;
    sortOrder: number;
}

interface CityGroup {
    city: string;
    cityId: number;
    locations: Location[];
}

interface LocationsResponse {
    success: boolean;
    data: CityGroup[];
}

const CACHE_KEY = 'locations_grouped';
const CACHE_TIMESTAMP_KEY = 'locations_grouped_timestamp';
const CACHE_DURATION = 1000 * 60 * 60; // 1 час

export function useLocations() {
    const [locations, setLocations] = useState<CityGroup[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const { request } = useApi();

    const loadLocations = useCallback(async (forceRefresh = false) => {
        try {
            setIsLoading(true);
            setError(null);

            // Проверяем кеш
            const cachedData = localStorage.getItem(CACHE_KEY);
            const cachedTimestamp = localStorage.getItem(CACHE_TIMESTAMP_KEY);
            const now = Date.now();
            const cacheAge = cachedTimestamp ? now - parseInt(cachedTimestamp) : Infinity;

            // Используем кеш если он свежий и не требуется принудительное обновление
            if (!forceRefresh && cachedData && cacheAge < CACHE_DURATION) {
                console.log('Locations loaded from cache');
                const parsedLocations = JSON.parse(cachedData);
                setLocations(parsedLocations);
                setIsLoading(false);
                return;
            }

            // Загружаем с сервера
            console.log('Locations loaded from server');
            const response = await request(`${API_URL}/v1/locations/frontend`);

            if (!response.ok) {
                throw new Error(`Failed to fetch locations: ${response.status}`);
            }

            const data: LocationsResponse = await response.json();

            if (!data.success || !data.data) {
                throw new Error('Invalid response format');
            }

            // Сохраняем данные как есть (уже сгруппированы по городам)
            setLocations(data.data);

            // Сохраняем в кеш
            localStorage.setItem(CACHE_KEY, JSON.stringify(data.data));
            localStorage.setItem(CACHE_TIMESTAMP_KEY, String(now));

        } catch (err) {
            console.error('Error in useLocations:', err);
            setError(err instanceof Error ? err.message : 'Unknown error');

            // Используем кеш как fallback
            const cachedData = localStorage.getItem(CACHE_KEY);
            if (cachedData) {
                try {
                    const parsedLocations = JSON.parse(cachedData);
                    setLocations(parsedLocations);
                } catch (parseErr) {
                    console.error('Error parsing cached locations:', parseErr);
                }
            }
        } finally {
            setIsLoading(false);
        }
    }, [request]);

    // Получить локации для конкретного города
    const getLocationsByCity = useCallback((cityName: string): Location[] => {
        const cityGroup = locations.find(group => group.city === cityName);
        return cityGroup?.locations || [];
    }, [locations]);

    // Получить локации по ID города
    const getLocationsByCityId = useCallback((cityId: number): Location[] => {
        const cityGroup = locations.find(group => group.cityId === cityId);
        return cityGroup?.locations || [];
    }, [locations]);

    // Получить все города
    const getCities = useCallback(() => {
        return locations.map(group => ({
            id: group.cityId,
            name: group.city
        }));
    }, [locations]);

    // Получить локацию по ID
    const getLocationById = useCallback((locationId: number): Location | null => {
        for (const group of locations) {
            const location = group.locations.find(loc => loc.id === locationId);
            if (location) return location;
        }
        return null;
    }, [locations]);

    const refresh = useCallback(() => {
        loadLocations(true);
    }, [loadLocations]);

    const clearCache = useCallback(() => {
        localStorage.removeItem(CACHE_KEY);
        localStorage.removeItem(CACHE_TIMESTAMP_KEY);
        setLocations([]);
    }, []);

    useEffect(() => {
        loadLocations();
    }, [loadLocations]);

    return {
        locations,
        isLoading,
        error,
        refresh,
        clearCache,
        getLocationsByCity,
        getLocationsByCityId,
        getCities,
        getLocationById,
        isCacheValid: () => {
            const timestamp = localStorage.getItem(CACHE_TIMESTAMP_KEY);
            if (!timestamp) return false;
            return Date.now() - parseInt(timestamp) < CACHE_DURATION;
        }
    };
}