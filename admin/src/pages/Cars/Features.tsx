import PageMeta from "../../components/common/PageMeta";
import { useFeatures } from '../../hooks/useFeatures';
import LoadingSpinner from "../../components/common/LoadingSpinner";
import ErrorMessage from "../../components/common/ErrorMessage";

// Конфигурация иконок
const iconMap: Record<string, string> = {
    ac: '❄️',
    heated_seats: '🔥',
    leather: '🪑',
    back_camera: '📷',
    parking_sensors: '📡',
    abs: '🛑',
    esp: '🔄',
    usb: '🔌',
    bluetooth: '📶',
    navigation: '🗺️',
    sunroof: '🌤️',
    cruise_control: '🎯',
};

// Конфигурация категорий
const categoryConfig: Record<string, { label: string; icon: string; color: string }> = {
    comfort: {
        label: 'Комфорт',
        icon: '🛋️',
        color: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
    },
    safety: {
        label: 'Безопасность',
        icon: '🛡️',
        color: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
    },
    media: {
        label: 'Мультимедиа',
        icon: '🎵',
        color: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'
    }
};

export default function Features() {
    const {
        features,
        loading,
        error,
        refresh,
    } = useFeatures();

    if (loading && features.length === 0) {
        return <LoadingSpinner fullScreen />;
    }

    if (error) {
        return <ErrorMessage message={error} onRetry={refresh} />;
    }

    // Группировка по категориям
    const groupedFeatures = features.reduce((acc, feature) => {
        const category = feature.categoryCode || 'other';
        if (!acc[category]) {
            acc[category] = [];
        }
        acc[category].push(feature);
        return acc;
    }, {} as Record<string, typeof features>);

    // Сортировка категорий
    const categoryOrder = ['comfort', 'safety', 'media'];
    const sortedCategories = Object.keys(groupedFeatures).sort(
        (a, b) => categoryOrder.indexOf(a) - categoryOrder.indexOf(b)
    );

    return (
        <>
            <PageMeta
                title="Комплектации автомобилей"
                description="Особенности и характеристики автомобилей"
            />

            <div className="container mx-auto px-4 py-8">
                {/* Заголовок */}
                <div className="flex items-center justify-between mb-6">
                    <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                        Комплектации автомобилей
                    </h1>
                    <span className="text-sm text-gray-500 dark:text-gray-400">
                        Всего: {features.length}
                    </span>
                </div>

                {features.length === 0 ? (
                    <div className="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <p className="text-gray-500 dark:text-gray-400">
                            Особенности не найдены
                        </p>
                    </div>
                ) : (
                    <div className="space-y-8">
                        {sortedCategories.map((categoryCode) => {
                            const categoryFeatures = groupedFeatures[categoryCode];
                            const config = categoryConfig[categoryCode] || {
                                label: categoryCode,
                                icon: '📦',
                                color: 'bg-gray-100 text-gray-800 dark:bg-gray-700/30 dark:text-gray-400'
                            };

                            return (
                                <div key={categoryCode}>
                                    {/* Заголовок категории */}
                                    <div className="flex items-center gap-2 mb-3">
                                        <span className="text-xl">{config.icon}</span>
                                        <h2 className="text-lg font-medium text-gray-800 dark:text-white">
                                            {config.label}
                                        </h2>
                                        <span className="text-sm text-gray-400 dark:text-gray-500">
                                            ({categoryFeatures.length})
                                        </span>
                                    </div>

                                    {/* Сетка фич */}
                                    <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                        {categoryFeatures.map((feature) => (
                                            <div
                                                key={feature.id}
                                                className="flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                            >
                                                <span className="text-base">
                                                    {iconMap[feature.icon] || '🔹'}
                                                </span>
                                                <span className="text-sm text-gray-700 dark:text-gray-300 truncate">
                                                    {feature.name}
                                                </span>
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