import { useCallback, useEffect, useState } from "react";
import { regionsService, RegionData } from "../services/RegionsService";

export interface UseRegionsStatisticsParams {
    autoFetch?: boolean;
}

export interface UseRegionsStatisticsReturn {
    data: Record<string, RegionData>;
    loading: boolean;
    error: string | null;
    refetch: () => Promise<void>;
}

export const useRegionsStatistics = (
    params: UseRegionsStatisticsParams = {}
): UseRegionsStatisticsReturn => {
    const {
        autoFetch = true,
    } = params;

    const [data, setData] = useState<Record<string, RegionData>>({});
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const fetchData = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const token =
                localStorage.getItem("auth_token") || undefined;

            const response =
                await regionsService.getStatistics(token);

            const regions: Record<string, RegionData> = {};

            response.data.forEach((region) => {
                regions[region.code] = region;
            });

            setData(regions);
        } catch (err) {
            const errorMessage =
                err instanceof Error
                    ? err.message
                    : "Не удалось загрузить статистику регионов";

            setError(errorMessage);

            console.error(
                "Error fetching regions statistics:",
                err
            );
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        if (autoFetch) {
            fetchData();
        }
    }, [fetchData, autoFetch]);

    return {
        data,
        loading,
        error,
        refetch: fetchData,
    };
};