// src/pages/Features/components/FeatureModal.tsx

import { useState, useEffect } from 'react';
import { useFeatures } from '../../../hooks/useFeatures';
import { useLanguage } from '../../../i18n/LanguageProvider';

interface FeatureModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: () => void;
    editingItem?: any;
}

// Доступные иконки
const AVAILABLE_ICONS = [
    { value: 'ac', label: '❄️ Кондиционер' },
    { value: 'heated_seats', label: '🔥 Подогрев сидений' },
    { value: 'leather', label: '🪑 Кожаный салон' },
    { value: 'back_camera', label: '📷 Камера заднего вида' },
    { value: 'parking_sensors', label: '📡 Парктроники' },
    { value: 'abs', label: '🛑 ABS' },
    { value: 'esp', label: '🔄 ESP' },
    { value: 'usb', label: '🔌 USB порт' },
    { value: 'bluetooth', label: '📶 Bluetooth' },
    { value: 'navigation', label: '🗺️ Навигация' },
    { value: 'sunroof', label: '🌤️ Люк' },
    { value: 'cruise_control', label: '🎯 Круиз-контроль' },
];

// Доступные категории
const AVAILABLE_CATEGORIES = [
    { value: 'comfort', label: '🛋️ Комфорт' },
    { value: 'safety', label: '🛡️ Безопасность' },
    { value: 'media', label: '🎵 Мультимедиа' },
];

export default function FeatureModal({ isOpen, onClose, onSuccess, editingItem }: FeatureModalProps) {
    const { t } = useLanguage();
    const { createFeature, updateFeature } = useFeatures();
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const [formData, setFormData] = useState({
        name: '',
        icon: '',
        categoryCode: '',
    });

    const isEditing = !!editingItem;

    // Заполняем форму при редактировании
    useEffect(() => {
        if (editingItem) {
            setFormData({
                name: editingItem.name ?? '',
                icon: editingItem.icon ?? '',
                categoryCode: editingItem.categoryCode ?? '',
            });
        } else {
            setFormData({
                name: '',
                icon: '',
                categoryCode: '',
            });
        }
        setError(null);
    }, [editingItem, isOpen]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
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

        if (!formData.categoryCode) {
            setError(t('category_required'));
            return;
        }

        try {
            setLoading(true);

            const payload = {
                name: formData.name.trim(),
                icon: formData.icon || null,
                categoryCode: formData.categoryCode,
            };

            if (isEditing && editingItem) {
                await updateFeature(editingItem.id, payload);
            } else {
                await createFeature(payload);
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
                        {isEditing ? t('edit_feature') : t('new_feature')}
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
                            placeholder={t('enter_feature_name')}
                            required
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
                            <option value="">{t('select_icon')}</option>
                            {AVAILABLE_ICONS.map((icon) => (
                                <option key={icon.value} value={icon.value}>
                                    {icon.label}
                                </option>
                            ))}
                        </select>
                    </div>

                    {/* Категория */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {t('category')} *
                        </label>
                        <select
                            name="categoryCode"
                            value={formData.categoryCode}
                            onChange={handleChange}
                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                        >
                            <option value="">{t('select_category')}</option>
                            {AVAILABLE_CATEGORIES.map((category) => (
                                <option key={category.value} value={category.value}>
                                    {category.label}
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