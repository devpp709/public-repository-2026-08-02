import PageMeta from "../../components/common/PageMeta";
import { useBookings } from "../../hooks/useBookings";

export default function BookingsAll() {
    const {
        bookings,
        loading,
        error,
    } = useBookings();

    return (
        <>
            <PageMeta
                title="Все заказы"
                description="Список всех заказов"
            />

            <div>
                <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                    Все заказы
                </h1>

                {loading && (
                    <div className="mt-6">
                        Загрузка...
                    </div>
                )}

                {error && (
                    <div className="mt-6 text-red-500">
                        {error}
                    </div>
                )}

                {!loading && !error && (
                    <div className="mt-6">
                        {bookings.map((booking) => (
                            <div
                                key={booking.id}
                                className="mb-3 rounded-lg border border-gray-200 p-4 dark:border-gray-800"
                            >
                                <div className="font-medium text-gray-800 dark:text-white">
                                    #{booking.bookingNumber}
                                </div>

                                <div className="mt-1 text-sm text-gray-500">
                                    {booking.car.name}
                                </div>

                                <div className="mt-1 text-sm text-gray-500">
                                    {booking.car.licensePlate}
                                </div>

                                <div className="mt-1 text-sm text-gray-500">
                                    {booking.pickupDate} — {booking.dropoffDate}
                                </div>

                                <div className="mt-1 text-sm text-gray-500">
                                    Дней: {booking.totalDays}
                                </div>

                                <div className="mt-1 text-sm text-gray-500">
                                    Статус: {booking.status}
                                </div>

                                <div className="mt-1 font-medium">
                                    {booking.totalPrice}
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}