import { useCallback } from 'react';
import { useAuth } from '../context/AuthContext';

const API_URL = process.env.NEXT_PUBLIC_API_URL || '';

interface RequestOptions extends RequestInit {
    headers?: Record<string, string>;
}

export function useApi() {
    const { accessToken, refreshAccessToken, logout } = useAuth();

    const request = useCallback(
        async (url: string, options: RequestOptions = {}): Promise<Response> => {
        let token = accessToken;

        const fetchRequest = async (currentToken: string | null): Promise<Response> => {
            const headers: Record<string, string> = {
                ...(options.headers || {})
            };

            if (!(options.body instanceof FormData)) {
                headers['Content-Type'] = 'application/json';
            }

            if (currentToken) {
                headers.Authorization = `Bearer ${currentToken}`;
            }

            return fetch(`${url}`, {
                ...options,
                headers
            });
        };

        let response = await fetchRequest(token);

        if (response.status === 401) {
            const newToken = await refreshAccessToken();

            if (newToken) {
                response = await fetchRequest(newToken);
            } else {
                logout();
            }
        }

        return response;
    },
    [accessToken, refreshAccessToken, logout]
);

    return { request };
}