// src/types/auth.types.ts
export interface User {
    id: string;
    email: string;
    name?: string;
    role?: string;
}

export interface AuthResponse {
    token: string;
    refreshToken?: string;
    user: User;
}

export interface AuthContextType {
    user: User | null;
    loading: boolean;
    accessToken: string | null;
    login: (email: string, password: string) => Promise<AuthResponse>;
    logout: () => void;
    register: (userData: RegisterData) => Promise<AuthResponse>;
    refreshAccessToken: () => Promise<string | null>;
}

export interface RegisterData {
    email: string;
    password: string;
    name?: string;
}