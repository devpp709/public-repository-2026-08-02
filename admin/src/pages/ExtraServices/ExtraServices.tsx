import PageMeta from "../../components/common/PageMeta";
import { useExtraServices } from '../../hooks/useExtraServices';
import LoadingSpinner from "../../components/common/LoadingSpinner";
import ErrorMessage from "../../components/common/ErrorMessage";

// Конфигурация иконок
const iconMap: Record<string, string> = {
    child_seat: '👶',
    gps: '🗺️',
    driver: '🧑‍✈️',
    winter_tires: '❄️',
    roof_rack: '🧳',
    fog_lights: '🌫️',
};

// Конфигурация категорий
const categoryConfig: Record<string, { label: string; icon: string; color: string }> = {
    safety: {
        label: 'Безопасность',
        icon: '🛡️',
        color: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
    },
    media: {
        label: 'Мультимедиа',
        icon: '🎵',
        color: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'
    },
    service: {
        label: 'Услуги',
        icon: '🔧',
        color: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
    }
};

export default function ExtraServices() {
    const {
        extraServices,
        loading,
        error,
        refresh,
    } = useExtraServices();

    // Форматирование суммы
    const formatPrice = (price: number | null) => {
        if (price === null) return '—';
        return new Intl.NumberFormat('ru-RU').format(price);
    };

    if (loading && extraServices.length === 0) {
        return <LoadingSpinner fullScreen />;
    }

    if (error) {
        return <ErrorMessage message={error} onRetry={refresh} />;
    }

    // Группировка по категориям
    const groupedServices = extraServices.reduce((acc, service) => {
        const category = service.category || 'other';
        if (!acc[category]) {
            acc[category] = [];
        }
        acc[category].push(service);
        return acc;
    }, {} as Record<string, typeof extraServices>);

    // Сортировка категорий
    const categoryOrder = ['safety', 'media', 'service'];
    const sortedCategories = Object.keys(groupedServices).sort(
        (a, b) => categoryOrder.indexOf(a) - categoryOrder.indexOf(b)
    );

    return (
        <>
            <PageMeta
                title="Дополнительные услуги"
                description="Дополнительные услуги для автомобилей"
            />

            <div className="container mx-auto px-4 py-8">
                {/* Заголовок */}
                <div className="flex items-center justify-between mb-6">
                    <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                        Дополнительные услуги
                    </h1>
                    <span className="text-sm text-gray-500 dark:text-gray-400">
                        Всего: {extraServices.length}
                    </span>
                </div>

                {extraServices.length === 0 ? (
                    <div className="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <p className="text-gray-500 dark:text-gray-400">
                            Дополнительные услуги не найдены
                        </p>
                    </div>
                ) : (
                    <div className="space-y-6">
                        {sortedCategories.map((categoryCode) => {
                            const services = groupedServices[categoryCode];
                            const config = categoryConfig[categoryCode] || {
                                label: categoryCode,
                                icon: '📦',
                                color: 'bg-gray-100 text-gray-800 dark:bg-gray-700/30 dark:text-gray-400'
                            };

                            // Активные услуги
                            const activeServices = services.filter(s => s.isActive);
                            const inactiveServices = services.filter(s => !s.isActive);

                            return (
                                <div key={categoryCode}>
                                    {/* Заголовок категории */}
                                    <div className="flex items-center gap-2 mb-3">
                                        <span className="text-xl">{config.icon}</span>
                                        <h2 className="text-lg font-medium text-gray-800 dark:text-white">
                                            {config.label}
                                        </h2>
                                        <span className="text-sm text-gray-400 dark:text-gray-500">
                                            ({services.length})
                                        </span>
                                    </div>

                                    {/* Сетка услуг */}
                                    <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                        {activeServices.map((service) => (
                                            <div
                                                key={service.id}
                                                className="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow"
                                            >
                                                <div className="flex items-start gap-3">
                                                    <div className="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-xl">
                                                        {iconMap[service.icon] || '🔹'}
                                                    </div>

                                                    <div className="flex-1 min-w-0">
                                                        <h3 className="font-medium text-gray-800 dark:text-white truncate">
                                                            {service.name}
                                                        </h3>
                                                        {service.description && (
                                                            <p className="mt-0.5 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                                                {service.description}
                                                            </p>
                                                        )}
                                                    </div>
                                                </div>

                                                {/* Цена и статистика */}
                                                <div className="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                                    <div className="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                        {formatPrice(service.defaultPrice)} ₽
                                                    </div>
                                                    <div className="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                                        <span>🚗 {service.carsCount || 0}</span>
                                                        <span>📊 {service.usageCount || 0}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}

                                        {/* Неактивные услуги */}
                                        {inactiveServices.map((service) => (
                                            <div
                                                key={service.id}
                                                className="bg-gray-50 dark:bg-gray-800/30 rounded-lg border border-gray-200 dark:border-gray-700 p-4 opacity-60"
                                            >
                                                <div className="flex items-start gap-3">
                                                    <div className="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xl">
                                                        {iconMap[service.icon] || '🔹'}
                                                    </div>

                                                    <div className="flex-1 min-w-0">
                                                        <h3 className="font-medium text-gray-500 dark:text-gray-500 truncate line-through">
                                                            {service.name}
                                                        </h3>
                                                        {service.description && (
                                                            <p className="mt-0.5 text-sm text-gray-400 dark:text-gray-500 line-clamp-2">
                                                                {service.description}
                                                            </p>
                                                        )}
                                                    </div>
                                                </div>

                                                <div className="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                                    <div className="text-sm font-medium text-gray-400 dark:text-gray-500">
                                                        {formatPrice(service.defaultPrice)} ₽
                                                    </div>
                                                    <span className="text-xs text-gray-400 dark:text-gray-500">Неактивна</span>
                                                </div>
                                            </div>
                                        ))}
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