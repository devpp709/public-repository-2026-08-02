// src/services/AuthService.ts
const API_URL = import.meta.env.VITE_API_URL || '';

interface AuthResponse {
    accessToken: string;
    refreshToken: string;
    user?: any;
}

const AuthService = {
    setTokens(accessToken: string, refreshToken: string) {
        localStorage.setItem('accessToken', accessToken);
        localStorage.setItem('refreshToken', refreshToken);
    },

    getAccessToken(): string | null {
        return localStorage.getItem('accessToken');
    },

    getRefreshToken(): string | null {
        return localStorage.getItem('refreshToken');
    },

    clearTokens() {
        localStorage.removeItem('accessToken');
        localStorage.removeItem('refreshToken');
    },

    async login(phone: string, password: string): Promise<AuthResponse> {
        const response = await fetch(`${API_URL}/api/v1/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ phone, password }),
        });
        if (!response.ok) {
            const error = await response.json().catch(() => ({ error: 'Login failed' }));
            throw new Error(error.error || 'Login failed');
        }
        const data = await response.json();
        AuthService.setTokens(data.accessToken, data.refreshToken);
        return data;
    },

    async register(email: string, password: string, name?: string, phone?: string): Promise<AuthResponse> {
        const response = await fetch(`${API_URL}/api/v1/auth/register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password, name, phone }),
        });
        if (!response.ok) {
            const error = await response.json().catch(() => ({ error: 'Registration failed' }));
            throw new Error(error.error || 'Registration failed');
        }
        const data = await response.json();
        AuthService.setTokens(data.accessToken, data.refreshToken);
        return data;
    },

    async refreshToken(): Promise<string> {
        const refreshToken = AuthService.getRefreshToken();
        if (!refreshToken) throw new Error('No refresh token');
        const response = await fetch(`${API_URL}/api/v1/auth/refresh`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ refreshToken }),
        });
        if (!response.ok) {
            AuthService.clearTokens();
            throw new Error('Token refresh failed');
        }
        const data = await response.json();
        if (data.accessToken) {
            localStorage.setItem('accessToken', data.accessToken);
        }
        if (data.refreshToken) {
            localStorage.setItem('refreshToken', data.refreshToken);
        }
        return data.accessToken;
    },

    async logout(): Promise<void> {
        const refreshToken = AuthService.getRefreshToken();
        try {
            await fetch(`${API_URL}/api/v1/auth/logout`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ refreshToken }),
            });
        } finally {
            AuthService.clearTokens();
        }
    },

    isAuthenticated(): boolean {
        return !!AuthService.getAccessToken();
    }
};

export default AuthService;