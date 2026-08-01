// frontend/lib/api.js
import { useCallback } from 'react';
import { useAuth } from '../context/AuthContext';

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3001';

export function useApi() {
    const { accessToken, refreshAccessToken, logout, user } = useAuth();

    const request = useCallback(async (url, options = {}) => {
        let token = accessToken;
        if (!token) {
            token = await refreshAccessToken();
        }
        if (!token) {
            await logout();
            throw new Error('Not authenticated');
        }

        const doFetch = async (tok) => {
            const headers = {
                ...(options.headers || {}),
            };
            // НЕ ставим Content-Type, если передан FormData
            if (!(options.body instanceof FormData)) {
                headers['Content-Type'] = 'application/json';
            }
            if (tok) {
                console.log('🔑 Sending token:', tok.substring(0, 20) + '...');
                headers.Authorization = `Bearer ${tok}`;
            } else {
                console.warn('⚠️ No token available');
            }
            console.log('📤 Request headers:', headers);
            return fetch(url, { ...options, headers });
        };

        let res = await doFetch(token);
        if (res.status === 401) {
            const newToken = await refreshAccessToken();
            if (!newToken) {
                await logout();
                return res;
            }
            res = await doFetch(newToken);
        }
        return res;
    }, [accessToken, refreshAccessToken, logout, user]);

    return { request };
}