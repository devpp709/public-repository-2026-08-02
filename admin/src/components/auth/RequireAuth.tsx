// src/components/auth/RequireAuth.tsx
import { Navigate, Outlet, useLocation } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

export function RequireAuth() {
    const { loading, isAuthenticated } = useAuth();
    const location = useLocation();

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
            </div>
        );
    }

    if (!isAuthenticated) {
        // Сохраняем путь, с которого пришли, для редиректа после логина
        return <Navigate to="/signin" state={{ from: location }} replace />;
    }

    return <Outlet />;
}