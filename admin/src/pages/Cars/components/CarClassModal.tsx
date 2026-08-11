// admin/src/pages/Cars/components/CarClassModal.tsx

import { useState, useEffect } from 'react';
import { useCarClasses } from '../../../hooks/useCarClasses';
import { useLanguage } from '../../../i18n/LanguageProvider';

interface CarClassModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: () => void;
    editingItem?: any;
}

// Доступные иконки
const AVAILABLE_ICONS = [
    { value: 'economy', label: '🚗 Эконом' },
    { value: 'standard', label: '🚙 Стандарт' },
    { value: 'business', label: '💼 Бизнес' },
    { value: 'premium', label: '⭐ Премиум' },
    { value: 'suv', label: '🚙 Внедорожник' },
];

export default function CarClassModal({ isOpen, onClose, onSuccess, editingItem }: CarClassModalProps) {
    const { t } = useLanguage();
    const { createClass, updateClass } = useCarClasses();
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const [formData, setFormData] = useState({
        name: '',
        description: '',
        icon: 'economy',
    });

    const isEditing = !!editingItem;

    // Заполняем форму при редактировании
    useEffect(() => {
        if (editingItem) {
            setFormData({
                name: editingItem.name ?? '',
                description: editingItem.description ?? '',
                icon: editingItem.icon ?? 'economy',
            });
        } else {
            setFormData({
                name: '',
                description: '',
                icon: 'economy',
            });
        }
        setError(null);
    }, [editingItem, isOpen]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);

        // Валидация
        if (!formData.name.trim()) {
            setError(t('name_required'));
            return;
        }

        try {
            setLoading(true);

            const payload = {
                name: formData.name.trim(),
                description: formData.description.trim() || null,
                icon: formData.icon,
            };

            if (isEditing && editingItem) {
                await updateClass(editingItem.id, payload);
            } else {
                await createClass(payload);
            }

            onSuccess();
        } catch (err: any) {
            setError(err?.message || t('save_error'));
        } finally {
            setLoading(false);
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div className="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                {/* Заголовок */}
                <div className="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 className="text-xl font-semibold text-gray-800 dark:text-white">
                        {isEditing ? t('edit_class') : t('new_class')}
                    </h2>
                    <button
                        onClick={onClose}
                        className="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    >
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {/* Форма */}
                <form onSubmit={handleSubmit} className="p-6 space-y-4">
                    {error && (
                        <div className="p-3 text-sm text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400 rounded-lg">
                            {error}
                        </div>
                    )}

                    {/* Название */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {t('name')} *
                        </label>
                        <input
                            type="text"
                            name="name"
                            value={formData.name}
                            onChange={handleChange}
                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder={t('enter_class_name')}
                            required
                        />
                    </div>

                    {/* Описание */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {t('description')}
                        </label>
                        <textarea
                            name="description"
                            value={formData.description}
                            onChange={handleChange}
                            rows={3}
                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder={t('enter_class_description')}
                        />
                    </div>

                    {/* Иконка */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {t('icon')}
                        </label>
                        <select
                            name="icon"
                            value={formData.icon}
                            onChange={handleChange}
                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            {AVAILABLE_ICONS.map((icon) => (
                                <option key={icon.value} value={icon.value}>
                                    {icon.label}
                                </option>
                            ))}
                        </select>
                    </div>

                    {/* Кнопки */}
                    <div className="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button
                            type="button"
                            onClick={onClose}
                            className="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                            {t('cancel')}
                        </button>
                        <button
                            type="submit"
                            disabled={loading}
                            className="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {loading ? t('saving') : isEditing ? t('save') : t('create')}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}