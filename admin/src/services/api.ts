
const API_BASE_URL = import.meta.env.VITE_API_URL || '';

interface ApiError {
    message: string;
    status?: number;
}

const getAccessToken = (): string | null => {
    return localStorage.getItem('accessToken');
};

const getHeaders = (): HeadersInit => {
    const token = getAccessToken();

    return {
        'Content-Type': 'application/json',
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
    };
};

export const api = {
    get: async <T = any>(
        endpoint: string,
        token?: string
    ): Promise<T> => {
        const accessToken = token || getAccessToken();

        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            headers: {
                'Content-Type': 'application/json',
                ...(accessToken
                    ? { Authorization: `Bearer ${accessToken}` }
                    : {}),
            },
        });

        if (!response.ok) {
            const error: ApiError = {
                message: `API Error: ${response.statusText}`,
                status: response.status,
            };

            throw error;
        }

        return response.json();
    },

    post: async <T = any>(
        endpoint: string,
        data?: any
    ): Promise<T> => {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            method: 'POST',
            headers: getHeaders(),
            body: data ? JSON.stringify(data) : undefined,
        });

        if (!response.ok) {
            const error: ApiError = {
                message: `API Error: ${response.statusText}`,
                status: response.status,
            };

            throw error;
        }

        return response.json();
    },

    put: async <T = any>(
        endpoint: string,
        data?: any
    ): Promise<T> => {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            method: 'PUT',
            headers: getHeaders(),
            body: data ? JSON.stringify(data) : undefined,
        });

        if (!response.ok) {
            const error: ApiError = {
                message: `API Error: ${response.statusText}`,
                status: response.status,
            };

            throw error;
        }

        return response.json();
    },

    patch: async <T = any>(
        endpoint: string,
        data?: any
    ): Promise<T> => {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            method: 'PATCH',
            headers: getHeaders(),
            body: data ? JSON.stringify(data) : undefined,
        });

        if (!response.ok) {
            const error: ApiError = {
                message: `API Error: ${response.statusText}`,
                status: response.status,
            };

            throw error;
        }

        return response.json();
    },

    delete: async <T = any>(
        endpoint: string
    ): Promise<T> => {
        const token = getAccessToken();

        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            method: 'DELETE',
            headers: {
                ...(token
                    ? { Authorization: `Bearer ${token}` }
                    : {}),
            },
        });

        if (!response.ok) {
            const error: ApiError = {
                message: `API Error: ${response.statusText}`,
                status: response.status,
            };

            throw error;
        }

        return response.json();
    },
};