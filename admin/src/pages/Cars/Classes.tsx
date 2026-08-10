import PageMeta from "../../components/common/PageMeta";
import { useCarClasses } from "../../hooks/useCarClasses";
import LoadingSpinner from "../../components/common/LoadingSpinner";
import ErrorMessage from "../../components/common/ErrorMessage";

// Конфигурация иконок классов
const iconMap: Record<string, string> = {
    economy: '🚗',
    standard: '🚙',
    business: '💼',
    premium: '⭐',
    suv: '🚙',
};

export default function Classes() {
    const { classes, loading, error, refresh } = useCarClasses();

    // Форматирование суммы
    const formatPrice = (price: number | null) => {
        if (price === null) return '—';
        return new Intl.NumberFormat('ru-RU').format(price);
    };

    if (loading && classes.length === 0) {
        return <LoadingSpinner fullScreen />;
    }

    if (error) {
        return <ErrorMessage message={error} onRetry={refresh} />;
    }

    // Сортировка по ID (или по имени)
    const sortedClasses = [...classes].sort((a, b) => a.id - b.id);

    return (
        <>
            <PageMeta
                title="Классы автомобилей"
                description="Классы автомобилей"
            />

            <div className="container mx-auto px-4 py-8">
                {/* Заголовок */}
                <div className="flex items-center justify-between mb-6">
                    <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                        Классы автомобилей
                    </h1>
                    <span className="text-sm text-gray-500 dark:text-gray-400">
                        Всего: {classes.length}
                    </span>
                </div>

                {/* Сетка классов */}
                {classes.length === 0 ? (
                    <div className="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <p className="text-gray-500 dark:text-gray-400">
                            Классы автомобилей не найдены
                        </p>
                    </div>
                ) : (
                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {sortedClasses.map((item) => (
                            <div
                                key={item.id}
                                className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow group"
                            >
                                {/* Иконка и название */}
                                <div className="flex items-start gap-3">
                                    <div className="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-2xl group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                                        {iconMap[item.icon] || '🚘'}
                                    </div>

                                    <div className="flex-1 min-w-0">
                                        <h2 className="text-lg font-semibold text-gray-800 dark:text-white truncate">
                                            {item.name}
                                        </h2>
                                        {item.description && (
                                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                                {item.description}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                {/* Цены */}
                                <div className="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-4 text-sm">
                                            <div>
                                                <span className="text-gray-500 dark:text-gray-400">День:</span>
                                                <span className="ml-1 font-medium text-gray-800 dark:text-white">
                                                    {formatPrice(item.dailyRate)} ₽
                                                </span>
                                            </div>
                                            {item.hourlyRate && (
                                                <div>
                                                    <span className="text-gray-500 dark:text-gray-400">Час:</span>
                                                    <span className="ml-1 font-medium text-gray-800 dark:text-white">
                                                        {formatPrice(item.hourlyRate)} ₽
                                                    </span>
                                                </div>
                                            )}
                                        </div>

                                        {/* Количество автомобилей */}
                                        <div className="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                                            <span>🚗</span>
                                            <span>{item.carsCount}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}