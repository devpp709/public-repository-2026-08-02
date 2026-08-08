// src/context/AuthContext.tsx
import React, { createContext, useContext, useState, useEffect, useCallback, useRef, ReactNode } from 'react';

interface User {
    id: number;
    email: string;
    phone?: string;
    firstName?: string;
    lastName?: string;
    username?: string;
    roles?: string[];
}

interface AuthContextType {
    user: User | null;
    loading: boolean;
    accessToken: string | null;
    refreshToken: string | null;
    login: (phone: string, password: string) => Promise<{ success: boolean; error?: string }>;
    register: (email: string, password: string, name?: string, phone?: string) => Promise<{ success: boolean; error?: string }>;
    logout: () => Promise<void>;
    refreshAccessToken: () => Promise<string | null>;
    isAuthenticated: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

const API_URL = import.meta.env.VITE_API_URL || '';

export function AuthProvider({ children }: { children: ReactNode }) {
    const [user, setUser] = useState<User | null>(null);
    const [accessToken, setAccessToken] = useState<string | null>(() => localStorage.getItem('accessToken'));
    const [refreshToken, setRefreshToken] = useState<string | null>(() => localStorage.getItem('refreshToken'));
    const [loading, setLoading] = useState(true);
    const hasLoadedRef = useRef(false);
    const refreshPromise = useRef<Promise<string | null> | null>(null);

    useEffect(() => {
        const loadUser = async () => {
            if (hasLoadedRef.current) return;
            hasLoadedRef.current = true;

            const token = localStorage.getItem('accessToken');
            const storedRefresh = localStorage.getItem('refreshToken');

            console.log('🔍 AuthProvider init — token exists:', !!token, 'refresh exists:', !!storedRefresh);

            if (!token) {
                console.log('ℹ️ No access token, skipping /me');
                setLoading(false);
                return;
            }

            // Пробуем получить пользователя
            const userData = await fetchUser(token);
            if (userData) {
                console.log('✅ /me succeeded with existing token');
                setUser(userData);
                setAccessToken(token);
                setRefreshToken(storedRefresh);
                setLoading(false);
                return;
            }

            console.log('⚠️ /me failed, trying refresh...');
            const newToken = await refreshAccessToken();
            if (newToken) {
                console.log('🔄 Refresh succeeded, retrying /me...');
                const retryUser = await fetchUser(newToken);
                if (retryUser) {
                    console.log('✅ /me succeeded after refresh');
                    setUser(retryUser);
                    setAccessToken(localStorage.getItem('accessToken'));
                    setRefreshToken(localStorage.getItem('refreshToken'));
                } else {
                    console.error('❌ /me failed after refresh');
                    clearTokens();
                }
            } else {
                console.error('❌ Refresh failed');
                clearTokens();
            }
            setLoading(false);
        };

        loadUser();
    }, []);

    const fetchUser = async (token: string): Promise<User | null> => {
        try {
            const response = await fetch(`${API_URL}/api/v1/auth/me`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: 'application/json',
                },
            });

            const text = await response.text();

            console.log('/me:', response.status, text);

            if (!response.ok) {
                return null;
            }

            return JSON.parse(text);
        } catch (error) {
            console.error('/me error:', error);
            return null;
        }
    };

    const saveTokens = (access: string, refresh: string) => {
        console.log('💾 Saving tokens');
        setAccessToken(access);
        setRefreshToken(refresh);
        localStorage.setItem('accessToken', access);
        localStorage.setItem('refreshToken', refresh);
    };

    const clearTokens = () => {
        console.warn('🗑️ Clearing tokens');
        setAccessToken(null);
        setRefreshToken(null);
        setUser(null);
        localStorage.removeItem('accessToken');
        localStorage.removeItem('refreshToken');
    };

    const refreshAccessToken = useCallback(async (): Promise<string | null> => {
        if (refreshPromise.current) {
            return refreshPromise.current;
        }

        refreshPromise.current = (async () => {
            const storedRefresh = localStorage.getItem('refreshToken');
            if (!storedRefresh) {
                console.warn('No refresh token in storage');
                return null;
            }

            try {
                const response = await fetch(`${API_URL}/api/v1/auth/refresh`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ refreshToken: storedRefresh }),
                });

                if (!response.ok) {
                    console.error('Refresh request failed:', response.status);
                    const errorText = await response.text();
                    console.error('Refresh error body:', errorText);
                    return null;
                }

                const data = await response.json();

                if (!data.accessToken) {
                    console.error('No accessToken in refresh response:', data);
                    return null;
                }

                console.log('🔑 Tokens refreshed successfully');
                localStorage.setItem('accessToken', data.accessToken);
                setAccessToken(data.accessToken);

                if (data.refreshToken) {
                    localStorage.setItem('refreshToken', data.refreshToken);
                    setRefreshToken(data.refreshToken);
                }

                return data.accessToken;
            } catch (error) {
                console.error('Refresh network error:', error);
                return null;
            } finally {
                refreshPromise.current = null;
            }
        })();

        return refreshPromise.current;
    }, []);

    const login = async (phone: string, password: string) => {
        try {
            const cleanPhone = phone.replace(/[^0-9+]/g, '');

            const response = await fetch(`${API_URL}/api/v1/auth/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ phone: cleanPhone, password })
            });

            const data = await response.json();

            if (response.ok) {
                console.log('✅ Login success');
                saveTokens(data.accessToken, data.refreshToken);
                setUser(data.user);
                return { success: true };
            } else {
                console.error('Login failed:', data.error);
                return { success: false, error: data.error || 'Login failed' };
            }
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, error: 'Network error' };
        }
    };

    const register = async (email: string, password: string, name?: string, phone?: string) => {
        try {
            const response = await fetch(`${API_URL}/api/v1/auth/register`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password, name, phone })
            });

            const data = await response.json();

            if (response.ok) {
                saveTokens(data.accessToken, data.refreshToken);
                setUser(data.user);
                return { success: true };
            } else {
                return { success: false, error: data.error || 'Registration failed' };
            }
        } catch (error) {
            console.error('Register error:', error);
            return { success: false, error: 'Network error' };
        }
    };

    const logout = async () => {
        const currentRefresh = refreshToken || localStorage.getItem('refreshToken');
        if (currentRefresh) {
            try {
                await fetch(`${API_URL}/api/v1/auth/logout`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ refreshToken: currentRefresh })
                });
            } catch (error) {
                console.error('Logout error:', error);
            }
        }
        clearTokens();
    };

    return (
        <AuthContext.Provider value={{
            user,
            loading,
            login,
            register,
            logout,
            accessToken,
            refreshToken,
            refreshAccessToken,
            isAuthenticated: !!user && !!accessToken,
        }}>
            {children}
        </AuthContext.Provider>
    );
}

export const useAuth = (): AuthContextType => {
    const context = useContext(AuthContext);
    if (context === undefined) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};