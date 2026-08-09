import { useCallback, useEffect, useState } from 'react';
import paymentsService, {
    Payment,
} from '../services/PaymentsService';

export interface UsePaymentsReturn {
    payments: Payment[];
    loading: boolean;
    error: string | null;
    refetch: () => Promise<void>;
}

export const usePayments = (
    autoFetch: boolean = true
): UsePaymentsReturn => {
    const [payments, setPayments] = useState<Payment[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const fetchPayments = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const token =
                localStorage.getItem('auth_token') || undefined;

            const response =
                await paymentsService.getAllPayments(token);

            setPayments(response.data);
        } catch (err) {
            const message =
                err instanceof Error
                    ? err.message
                    : 'Не удалось загрузить платежи';

            setError(message);
            console.error('Error fetching payments:', err);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        if (autoFetch) {
            fetchPayments();
        }
    }, [fetchPayments, autoFetch]);

    return {
        payments,
        loading,
        error,
        refetch: fetchPayments,
    };
};