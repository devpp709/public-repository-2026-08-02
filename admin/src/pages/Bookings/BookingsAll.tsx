import PageMeta from "../../components/common/PageMeta";

export default function BookingsAll() {
    return (
        <>
            <PageMeta
                title="Все заказы"
                description="Управление бронированиями"
            />

            <div>
                <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                    Все заказы
                </h1>
            </div>
        </>
    );
}