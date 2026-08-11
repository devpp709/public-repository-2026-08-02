import { useState } from 'react';
import PageMeta from "../../components/common/PageMeta";
import { useExtraServices } from '../../hooks/useExtraServices';
import LoadingSpinner from "../../components/common/LoadingSpinner";
import ErrorMessage from "../../components/common/ErrorMessage";
import ExtraServiceModal from './components/ExtraServiceModal';
import { useLanguage } from "../../i18n/LanguageProvider";

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
const categoryConfig: Record<string, { icon: string; color: string }> = {
    Insurance: {
        icon: '🛡️',
        color: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
    },
    Equipment: {
        icon: '🔧',
        color: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
    },
    Comfort: {
        icon: '🛋️',
        color: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
    },
    Safety: {
        icon: '🛡️',
        color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
    },
    Additional: {
        icon: '📦',
        color: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'
    }
};

export default function ExtraServices() {
    const { t } = useLanguage();
    const {
        extraServices,
        loading,
        error,
        refresh,
        deleteExtraService,
    } = useExtraServices();

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingService, setEditingService] = useState<any>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    // Форматирование суммы
    const formatPrice = (price: number | null) => {
        if (price === null) return '—';
        return new Intl.NumberFormat('ru-RU').format(price);
    };

    const handleEdit = (item: any) => {
        setEditingService(item);
        setIsModalOpen(true);
    };

    const handleCreate = () => {
        setEditingService(null);
        setIsModalOpen(true);
    };

    const handleDelete = async (id: number, name: string) => {
        if (!confirm(t('delete_service_confirm', { name }))) {
            return;
        }

        try {
            setIsDeleting(true);
            await deleteExtraService(id);
            await refresh();
        } catch (err) {
            console.error('Error deleting service:', err);
            alert(t('delete_service_error'));
        } finally {
            setIsDeleting(false);
        }
    };

    const handleModalClose = () => {
        setIsModalOpen(false);
        setEditingService(null);
    };

    const handleModalSuccess = async () => {
        await refresh();
        handleModalClose();
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
    const categoryOrder = ['Insurance', 'Equipment', 'Comfort', 'Safety', 'Additional'];
    const sortedCategories = Object.keys(groupedServices).sort(
        (a, b) => categoryOrder.indexOf(a) - categoryOrder.indexOf(b)
    );

    return (
        <>
            <PageMeta
                title={t('extra_services')}
                description={t('extra_services_description')}
            />

            <div className="container mx-auto px-4 py-8">
                {/* Заголовок */}
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                            {t('extra_services')}
                        </h1>
                        <span className="text-sm text-gray-500 dark:text-gray-400">
                            {t('total')}: {extraServices.length}
                        </span>
                    </div>

                    <button
                        onClick={handleCreate}
                        className="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                        </svg>
                        {t('add_service')}
                    </button>
                </div>

                {extraServices.length === 0 ? (
                    <div className="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <p className="text-gray-500 dark:text-gray-400">
                            {t('no_services')}
                        </p>
                        <button
                            onClick={handleCreate}
                            className="mt-4 text-blue-600 dark:text-blue-400 hover:underline"
                        >
                            {t('add_first_service')}
                        </button>
                    </div>
                ) : (
                    <div className="space-y-6">
                        {sortedCategories.map((categoryCode) => {
                            const services = groupedServices[categoryCode];
                            const config = categoryConfig[categoryCode] || {
                                icon: '📦',
                                color: 'bg-gray-100 text-gray-800 dark:bg-gray-700/30 dark:text-gray-400'
                            };
                            const categoryLabel = t(`service_category_${categoryCode}`) || categoryCode;

                            // Активные услуги
                            const activeServices = services.filter(s => s.isActive);
                            const inactiveServices = services.filter(s => !s.isActive);

                            return (
                                <div key={categoryCode}>
                                    {/* Заголовок категории */}
                                    <div className="flex items-center gap-2 mb-3">
                                        <span className="text-xl">{config.icon}</span>
                                        <h2 className="text-lg font-medium text-gray-800 dark:text-white">
                                            {categoryLabel}
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
                                                className="group relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow"
                                            >
                                                {/* Кнопки действий */}
                                                <div className="absolute top-2 right-2 flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button
                                                        onClick={() => handleEdit(service)}
                                                        className="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                                        title={t('edit')}
                                                    >
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button
                                                        onClick={() => handleDelete(service.id, service.name)}
                                                        disabled={isDeleting}
                                                        className="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors disabled:opacity-50"
                                                        title={t('delete')}
                                                    >
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div className="flex items-start gap-3">
                                                    <div className="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-xl">
                                                        {iconMap[service.icon] || '🔹'}
                                                    </div>

                                                    <div className="flex-1 min-w-0">
                                                        <h3 className="font-medium text-gray-800 dark:text-white truncate pr-8">
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
                                                className="group relative bg-gray-50 dark:bg-gray-800/30 rounded-lg border border-gray-200 dark:border-gray-700 p-4 opacity-60"
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
                                                    <span className="text-xs text-gray-400 dark:text-gray-500">{t('inactive')}</span>
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
            <ExtraServiceModal
                isOpen={isModalOpen}
                onClose={handleModalClose}
                onSuccess={handleModalSuccess}
                editingItem={editingService}
            />
        </>
    );
}