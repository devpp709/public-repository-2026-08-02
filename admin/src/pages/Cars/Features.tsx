import { useState } from 'react';
import PageMeta from "../../components/common/PageMeta";
import { useFeatures } from '../../hooks/useFeatures';
import LoadingSpinner from "../../components/common/LoadingSpinner";
import ErrorMessage from "../../components/common/ErrorMessage";
import FeatureModal from './components/FeatureModal';

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
        deleteFeature,
    } = useFeatures();

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingFeature, setEditingFeature] = useState<any>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    const handleEdit = (item: any) => {
        setEditingFeature(item);
        setIsModalOpen(true);
    };

    const handleCreate = () => {
        setEditingFeature(null);
        setIsModalOpen(true);
    };

    const handleDelete = async (id: number, name: string) => {
        if (!confirm(`Вы уверены, что хотите удалить особенность "${name}"?`)) {
            return;
        }

        try {
            setIsDeleting(true);
            await deleteFeature(id);
            await refresh();
        } catch (err) {
            console.error('Error deleting feature:', err);
            alert('Ошибка при удалении особенности');
        } finally {
            setIsDeleting(false);
        }
    };

    const handleModalClose = () => {
        setIsModalOpen(false);
        setEditingFeature(null);
    };

    const handleModalSuccess = async () => {
        await refresh();
        handleModalClose();
    };

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
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                            Комплектации автомобилей
                        </h1>
                        <span className="text-sm text-gray-500 dark:text-gray-400">
                            Всего: {features.length}
                        </span>
                    </div>

                    <button
                        onClick={handleCreate}
                        className="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                        </svg>
                        Добавить особенность
                    </button>
                </div>

                {features.length === 0 ? (
                    <div className="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <p className="text-gray-500 dark:text-gray-400">
                            Особенности не найдены
                        </p>
                        <button
                            onClick={handleCreate}
                            className="mt-4 text-blue-600 dark:text-blue-400 hover:underline"
                        >
                            Добавить первую особенность
                        </button>
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
                                    <div className="flex items-center justify-between mb-3">
                                        <div className="flex items-center gap-2">
                                            <span className="text-xl">{config.icon}</span>
                                            <h2 className="text-lg font-medium text-gray-800 dark:text-white">
                                                {config.label}
                                            </h2>
                                            <span className="text-sm text-gray-400 dark:text-gray-500">
                                                ({categoryFeatures.length})
                                            </span>
                                        </div>
                                    </div>

                                    {/* Сетка фич */}
                                    <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                        {categoryFeatures.map((feature) => (
                                            <div
                                                key={feature.id}
                                                className="group relative flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                            >
                                                <span className="text-base">
                                                    {iconMap[feature.icon] || '🔹'}
                                                </span>
                                                <span className="text-sm text-gray-700 dark:text-gray-300 truncate flex-1">
                                                    {feature.name}
                                                </span>

                                                {/* Кнопки действий при ховере */}
                                                <div className="absolute right-1 top-1/2 -translate-y-1/2 flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button
                                                        onClick={() => handleEdit(feature)}
                                                        className="p-1 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded hover:bg-white dark:hover:bg-gray-700 transition-colors"
                                                        title="Редактировать"
                                                    >
                                                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button
                                                        onClick={() => handleDelete(feature.id, feature.name)}
                                                        disabled={isDeleting}
                                                        className="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded hover:bg-white dark:hover:bg-gray-700 transition-colors disabled:opacity-50"
                                                        title="Удалить"
                                                    >
                                                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
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

            {/* Модальное окно */}
            <FeatureModal
                isOpen={isModalOpen}
                onClose={handleModalClose}
                onSuccess={handleModalSuccess}
                editingItem={editingFeature}
            />
        </>
    );
}