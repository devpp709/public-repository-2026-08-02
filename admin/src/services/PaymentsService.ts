import { api } from './api';

export interface Payment {
    id: number;
    bookingId: number;
    amount: number;
    paymentMethod: string | null;
    paymentMethodLabel: string | null;
    status: string;
    statusLabel: string;
    transactionId: string | null;
    paymentDate: string | null;
    createdAt: string;
    updatedAt: string;
    isPaid: boolean;
    isPending: boolean;
    isRefunded: boolean;
}

class PaymentsService {
    private readonly baseEndpoint = '/api/admin/payments';

    async getAllPayments(token?: string): Promise<Payment[]> {
        return api.get<Payment[]>(
            this.baseEndpoint,
            token
        );
    }

    async getPaymentById(
        id: number,
        token?: string
    ): Promise<Payment> {
        return api.get<Payment>(
            `${this.baseEndpoint}/${id}`,
            token
        );
    }

    async getPaymentsByBooking(
        bookingId: number,
        token?: string
    ): Promise<Payment[]> {
        return api.get<Payment[]>(
            `${this.baseEndpoint}/booking/${bookingId}`,
            token
        );
    }
}

export const paymentsService = new PaymentsService();

export default paymentsService;