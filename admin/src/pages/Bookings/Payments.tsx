import PageMeta from "../../components/common/PageMeta";
import { usePayments } from "../../hooks/usePayments";
import LoadingSpinner from "../../components/common/LoadingSpinner";
import ErrorMessage from "../../components/common/ErrorMessage";
import { useLanguage } from "../../i18n/LanguageProvider";

// Конфигурация статусов
const statusConfig: Record<string, { className: string }> = {
    pending: {
        className: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
    },
    completed: {
        className: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
    },
    refunded: {
        className: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400'
    },
    failed: {
        className: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
    }
};

// Конфигурация методов оплаты
const methodConfig: Record<string, { icon: string }> = {
    credit_card: { icon: '💳' },
    bank_transfer: { icon: '🏦' },
    cash: { icon: '💰' },
    apple_pay: { icon: '📱' },
    google_pay: { icon: '📱' }
};

export default function Payments() {
    const { t } = useLanguage();
    const {
        payments,
        loading,
        error,
        refetch,
    } = usePayments();

    // Форматирование даты
    const formatDate = (dateString: string | null) => {
        if (!dateString) return '—';
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('ru-RU', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(date);
    };

    // Форматирование суммы
    const formatAmount = (amount: number) => {
        return new Intl.NumberFormat('ru-RU', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    };

    if (loading && payments.length === 0) {
        return <LoadingSpinner fullScreen />;
    }

    if (error) {
        return <ErrorMessage message={error} onRetry={refetch} />;
    }

    // Считаем статистику
    const totalAmount = payments.reduce((sum, p) => sum + p.amount, 0);
    const completedCount = payments.filter(p => p.status === 'completed').length;
    const pendingCount = payments.filter(p => p.status === 'pending').length;
    const refundedCount = payments.filter(p => p.status === 'refunded').length;

    return (
        <>
            <PageMeta
                title={t('payments')}
                description={t('payments_description')}
            />

            <div className="container mx-auto px-4 py-8">
                {/* Заголовок */}
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                        {t('payments')}
                    </h1>
                    <span className="text-sm text-gray-500 dark:text-gray-400">
                        {t('total')}: {payments.length}
                    </span>
                </div>

                {/* Статистика */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div className="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div className="text-sm text-gray-500 dark:text-gray-400">{t('total_payments')}</div>
                        <div className="text-2xl font-bold text-gray-800 dark:text-white">{payments.length}</div>
                    </div>
                    <div className="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div className="text-sm text-gray-500 dark:text-gray-400">{t('total_amount')}</div>
                        <div className="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {formatAmount(totalAmount)} ₽
                        </div>
                    </div>
                    <div className="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div className="text-sm text-gray-500 dark:text-gray-400">{t('paid')}</div>
                        <div className="text-2xl font-bold text-green-600 dark:text-green-400">{completedCount}</div>
                    </div>
                    <div className="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div className="text-sm text-gray-500 dark:text-gray-400">{t('pending')}</div>
                        <div className="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{pendingCount}</div>
                    </div>
                </div>

                {/* Таблица платежей */}
                {payments.length === 0 ? (
                    <div className="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <p className="text-gray-500 dark:text-gray-400">
                            {t('no_payments')}
                        </p>
                    </div>
                ) : (
                    <div className="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                        <div className="max-w-full overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                <tr className="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                                    <th className="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {t('booking')}
                                    </th>
                                    <th className="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {t('amount')}
                                    </th>
                                    <th className="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {t('payment_method')}
                                    </th>
                                    <th className="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {t('status')}
                                    </th>
                                    <th className="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {t('payment_date')}
                                    </th>
                                    <th className="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {t('transaction')}
                                    </th>
                                </tr>
                                </thead>

                                <tbody className="divide-y divide-gray-100 dark:divide-gray-800">
                                {payments.map((payment) => {
                                    const status = statusConfig[payment.status] || statusConfig.pending;
                                    const statusLabel = t(`payment_status_${payment.status}`) || payment.status;
                                    const method = methodConfig[payment.paymentMethod] || { icon: '❓' };
                                    const methodLabel = t(`payment_method_${payment.paymentMethod}`) || payment.paymentMethod || '—';

                                    return (
                                        <tr
                                            key={payment.id}
                                            className="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
                                        >
                                            {/* Бронирование */}
                                            <td className="px-5 py-4">
                                                <div>
                                                    <div className="text-sm font-medium text-gray-800 dark:text-white">
                                                        #{payment.bookingNumber}
                                                    </div>
                                                    <div className="text-xs text-gray-400 dark:text-gray-500">
                                                        ID: {payment.bookingId}
                                                    </div>
                                                </div>
                                            </td>

                                            {/* Сумма */}
                                            <td className="px-5 py-4">
                                                <div className="text-sm font-semibold text-gray-800 dark:text-white">
                                                    {formatAmount(payment.amount)} ₽
                                                </div>
                                            </td>

                                            {/* Способ оплаты */}
                                            <td className="px-5 py-4">
                                                <div className="flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300">
                                                    <span>{method.icon}</span>
                                                    <span>{methodLabel}</span>
                                                </div>
                                            </td>

                                            {/* Статус */}
                                            <td className="px-5 py-4">
                                                    <span className={`text-xs px-2.5 py-1 rounded-full font-medium ${status.className}`}>
                                                        {statusLabel}
                                                    </span>
                                            </td>

                                            {/* Дата платежа */}
                                            <td className="px-5 py-4">
                                                <div className="text-sm text-gray-600 dark:text-gray-300">
                                                    {formatDate(payment.paymentDate)}
                                                </div>
                                            </td>

                                            {/* Транзакция */}
                                            <td className="px-5 py-4">
                                                <div className="text-xs font-mono text-gray-500 dark:text-gray-400">
                                                    {payment.transactionId || '—'}
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })}
                                </tbody>

                                {/* Итого */}
                                <tfoot className="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <td className="px-5 py-3 text-sm font-medium text-gray-600 dark:text-gray-400" colSpan={1}>
                                        {t('total')}
                                    </td>
                                    <td className="px-5 py-3 text-sm font-bold text-gray-800 dark:text-white">
                                        {formatAmount(totalAmount)} ₽
                                    </td>
                                    <td className="px-5 py-3" colSpan={4} />
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}