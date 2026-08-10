import PageMeta from "../../components/common/PageMeta";
import { useBookings } from "../../hooks/useBookings";
import LoadingSpinner from "../../components/common/LoadingSpinner";
import ErrorMessage from "../../components/common/ErrorMessage";

// Статус бейджи
const statusConfig: Record<string, { label: string; className: string }> = {
    pending: {
        label: 'Ожидает',
        className: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
    },
    confirmed: {
        label: 'Подтвержден',
        className: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
    },
    in_progress: {
        label: 'В процессе',
        className: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'
    },
    completed: {
        label: 'Завершен',
        className: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
    },
    cancelled: {
        label: 'Отменен',
        className: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
    }
};

export default function BookingsAll() {
    const {
        bookings,
        loading,
        error,
        refresh,
    } = useBookings();

    if (loading && bookings.length === 0) {
        return <LoadingSpinner fullScreen />;
    }

    if (error) {
        return <ErrorMessage message={error} onRetry={refresh} />;
    }

    return (
        <>
            <PageMeta
                title="Все заказы"
                description="Список всех заказов"
            />

            <div className="container mx-auto px-4 py-8">
                {/* Заголовок */}
                <div className="flex items-center justify-between mb-6">
                    <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                        Все заказы
                    </h1>
                    <span className="text-sm text-gray-500 dark:text-gray-400">
                        Всего: {bookings.length}
                    </span>
                </div>

                {/* Список заказов */}
                {bookings.length === 0 ? (
                    <div className="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <p className="text-gray-500 dark:text-gray-400">
                            Заказов пока нет
                        </p>
                    </div>
                ) : (
                    <div className="grid gap-4">
                        {bookings.map((booking) => {
                            const status = statusConfig[booking.status] || statusConfig.pending;

                            return (
                                <div
                                    key={booking.id}
                                    className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow"
                                >
                                    <div className="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                        {/* Левая часть */}
                                        <div className="flex-1">
                                            {/* Номер заказа и статус */}
                                            <div className="flex items-center gap-3 flex-wrap">
                                                <span className="text-lg font-semibold text-gray-800 dark:text-white">
                                                    #{booking.bookingNumber}
                                                </span>
                                                <span className={`text-xs px-2.5 py-1 rounded-full font-medium ${status.className}`}>
                                                    {status.label}
                                                </span>
                                            </div>

                                            {/* Автомобиль */}
                                            <div className="mt-2 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                                <span className="font-medium">🚗</span>
                                                <span>{booking.car.name}</span>
                                                <span className="text-gray-400 dark:text-gray-500">•</span>
                                                <span className="text-gray-500 dark:text-gray-400">{booking.car.licensePlate}</span>
                                                <span className="text-gray-400 dark:text-gray-500">•</span>
                                                <span className="text-gray-500 dark:text-gray-400">{booking.car.year}</span>
                                            </div>

                                            {/* Клиент */}
                                            <div className="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                                <span className="font-medium">👤</span> {booking.user.name}
                                            </div>
                                        </div>

                                        {/* Правая часть - даты и цена */}
                                        <div className="flex flex-col items-start md:items-end gap-1 text-sm">
                                            <div className="text-gray-600 dark:text-gray-300">
                                                <span className="font-medium">📅</span> {booking.pickupDate} → {booking.dropoffDate}
                                            </div>
                                            <div className="text-gray-600 dark:text-gray-300">
                                                <span className="font-medium">⏱</span> {booking.totalDays} {booking.totalDays === 1 ? 'день' : booking.totalDays < 5 ? 'дня' : 'дней'}
                                            </div>
                                            <div className="text-lg font-bold text-blue-600 dark:text-blue-400">
                                                {booking.totalPrice} ₽
                                            </div>
                                        </div>
                                    </div>

                                    {/* Дополнительные услуги */}
                                    {booking.extras && booking.extras.length > 0 && (
                                        <div className="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                            <div className="flex flex-wrap gap-2">
                                                {booking.extras.map((extra) => (
                                                    <span
                                                        key={extra.id}
                                                        className="text-xs px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full"
                                                    >
                                                        {extra.service.icon && `${extra.service.icon} `}
                                                        {extra.service.name} ×{extra.quantity}
                                                    </span>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    {/* Локации */}
                                    <div className="mt-3 flex flex-col sm:flex-row gap-1 text-xs text-gray-500 dark:text-gray-400">
                                        <span>
                                            📍 Забор: {booking.pickupLocation.name}
                                        </span>
                                        <span className="hidden sm:inline text-gray-300 dark:text-gray-600">→</span>
                                        <span>
                                            📍 Возврат: {booking.dropoffLocation.name}
                                        </span>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>
        </>
    );
}