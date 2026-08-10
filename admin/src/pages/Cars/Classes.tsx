import { useState } from 'react';
import PageMeta from "../../components/common/PageMeta";
import { useCarClasses } from "../../hooks/useCarClasses";
import LoadingSpinner from "../../components/common/LoadingSpinner";
import ErrorMessage from "../../components/common/ErrorMessage";
import CarClassModal from './components/CarClassModal';

// Конфигурация иконок классов
const iconMap: Record<string, string> = {
    economy: '🚗',
    standard: '🚙',
    business: '💼',
    premium: '⭐',
    suv: '🚙',
};

export default function Classes() {
    const { classes, loading, error, refresh, deleteClass } = useCarClasses();
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingClass, setEditingClass] = useState<any>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    const handleEdit = (item: any) => {
        setEditingClass(item);
        setIsModalOpen(true);
    };

    const handleCreate = () => {
        setEditingClass(null);
        setIsModalOpen(true);
    };

    const handleDelete = async (id: number, name: string) => {
        if (!confirm(`Вы уверены, что хотите удалить класс "${name}"?`)) {
            return;
        }

        try {
            setIsDeleting(true);
            await deleteClass(id);
            await refresh();
        } catch (err) {
            console.error('Error deleting class:', err);
            alert('Ошибка при удалении класса');
        } finally {
            setIsDeleting(false);
        }
    };

    const handleModalClose = () => {
        setIsModalOpen(false);
        setEditingClass(null);
    };

    const handleModalSuccess = async () => {
        await refresh();
        handleModalClose();
    };

    if (loading && classes.length === 0) {
        return <LoadingSpinner fullScreen />;
    }

    if (error) {
        return <ErrorMessage message={error} onRetry={refresh} />;
    }

    // Сортировка по ID
    const sortedClasses = [...classes].sort((a, b) => a.id - b.id);

    return (
        <>
            <PageMeta
                title="Классы автомобилей"
                description="Классы автомобилей"
            />

            <div className="container mx-auto px-4 py-8">
                {/* Заголовок */}
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                            Классы автомобилей
                        </h1>
                        <span className="text-sm text-gray-500 dark:text-gray-400">
                            Всего: {classes.length}
                        </span>
                    </div>

                    <button
                        onClick={handleCreate}
                        className="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                        </svg>
                        Добавить класс
                    </button>
                </div>

                {/* Сетка классов */}
                {classes.length === 0 ? (
                    <div className="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <p className="text-gray-500 dark:text-gray-400">
                            Классы автомобилей не найдены
                        </p>
                        <button
                            onClick={handleCreate}
                            className="mt-4 text-blue-600 dark:text-blue-400 hover:underline"
                        >
                            Добавить первый класс
                        </button>
                    </div>
                ) : (
                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {sortedClasses.map((item) => (
                            <div
                                key={item.id}
                                className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow group relative"
                            >
                                {/* Кнопки действий */}
                                <div className="absolute top-3 right-3 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button
                                        onClick={() => handleEdit(item)}
                                        className="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        title="Редактировать"
                                    >
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button
                                        onClick={() => handleDelete(item.id, item.name)}
                                        disabled={isDeleting}
                                        className="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors disabled:opacity-50"
                                        title="Удалить"
                                    >
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>

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

                                {/* Количество автомобилей */}
                                <div className="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                                            <span>🚗</span>
                                            <span>Автомобилей: {item.carsCount || 0}</span>
                                        </div>

                                        {item.id && (
                                            <span className="text-xs text-gray-400 dark:text-gray-500">
                                                ID: {item.id}
                                            </span>
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* Модальное окно */}
            <CarClassModal
                isOpen={isModalOpen}
                onClose={handleModalClose}
                onSuccess={handleModalSuccess}
                editingItem={editingClass}
            />
        </>
    );
}