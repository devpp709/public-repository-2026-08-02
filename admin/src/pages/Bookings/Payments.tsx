import PageMeta from "../../components/common/PageMeta";
import { usePayments } from "../../hooks/usePayments";

export default function Payments() {
    const {
        payments,
        loading,
        error,
        refetch,
    } = usePayments();

    return (
        <>
            <PageMeta
                title="Платежи"
                description="Платежи по бронированиям"
            />

            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                        Платежи
                    </h1>
                </div>

                {loading && (
                    <div className="text-gray-500">
                        Загрузка...
                    </div>
                )}

                {error && (
                    <div className="text-red-500">
                        {error}
                    </div>
                )}

                {!loading && !error && (
                    <div className="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div className="max-w-full overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                <tr className="border-b border-gray-100 dark:border-gray-800">
                                    <th className="px-5 py-4 text-left text-sm font-medium text-gray-500">
                                        ID
                                    </th>
                                    <th className="px-5 py-4 text-left text-sm font-medium text-gray-500">
                                        Бронирование
                                    </th>
                                    <th className="px-5 py-4 text-left text-sm font-medium text-gray-500">
                                        Сумма
                                    </th>
                                    <th className="px-5 py-4 text-left text-sm font-medium text-gray-500">
                                        Способ оплаты
                                    </th>
                                    <th className="px-5 py-4 text-left text-sm font-medium text-gray-500">
                                        Статус
                                    </th>
                                    <th className="px-5 py-4 text-left text-sm font-medium text-gray-500">
                                        Дата
                                    </th>
                                </tr>
                                </thead>

                                <tbody>
                                {payments.map((payment) => (
                                    <tr
                                        key={payment.id}
                                        className="border-b border-gray-100 dark:border-gray-800"
                                    >
                                        <td className="px-5 py-4 text-sm text-gray-800 dark:text-white/90">
                                            #{payment.id}
                                        </td>

                                        <td className="px-5 py-4 text-sm text-gray-500">
                                            #{payment.bookingId}
                                        </td>

                                        <td className="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                                            {payment.amount.toFixed(2)}
                                        </td>

                                        <td className="px-5 py-4 text-sm text-gray-500">
                                            {payment.paymentMethodLabel ??
                                                payment.paymentMethod ??
                                                '—'}
                                        </td>

                                        <td className="px-5 py-4 text-sm">
                                                <span
                                                    className={
                                                        payment.isPaid
                                                            ? "text-green-600"
                                                            : payment.isRefunded
                                                                ? "text-orange-600"
                                                                : payment.isPending
                                                                    ? "text-yellow-600"
                                                                    : "text-red-600"
                                                    }
                                                >
                                                    {payment.statusLabel}
                                                </span>
                                        </td>

                                        <td className="px-5 py-4 text-sm text-gray-500">
                                            {payment.paymentDate ?? '—'}
                                        </td>
                                    </tr>
                                ))}
                                </tbody>
                            </table>
                        </div>

                        {payments.length === 0 && (
                            <div className="p-8 text-center text-gray-500">
                                Платежей нет
                            </div>
                        )}
                    </div>
                )}
            </div>
        </>
    );
}