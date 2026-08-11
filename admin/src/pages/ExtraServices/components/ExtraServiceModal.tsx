// src/pages/ExtraServices/components/ExtraServiceModal.tsx

import { useState, useEffect } from 'react';
import { useExtraServices } from '../../../hooks/useExtraServices';
import { useLanguage } from '../../../i18n/LanguageProvider';

interface ExtraServiceModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: () => void;
    editingItem?: any;
}

const ICONS_CONFIG = [
    { value: 'child_seat', icon: '👶', key: 'icon_child_seat' },
    { value: 'gps', icon: '🗺️', key: 'icon_gps' },
    { value: 'driver', icon: '🧑‍✈️', key: 'icon_driver' },
    { value: 'winter_tires', icon: '❄️', key: 'icon_winter_tires' },
    { value: 'roof_rack', icon: '🧳', key: 'icon_roof_rack' },
    { value: 'fog_lights', icon: '🌫️', key: 'icon_fog_lights' },
];

const CATEGORIES_CONFIG = [
    { value: 'Insurance', icon: '🛡️', key: 'category_Insurance' },
    { value: 'Equipment', icon: '🔧', key: 'category_Equipment' },
    { value: 'Comfort', icon: '🛋️', key: 'category_Comfort' },
    { value: 'Safety', icon: '🛡️', key: 'category_Safety' },
    { value: 'Additional', icon: '📦', key: 'category_Additional' },
];

export default function ExtraServiceModal({ isOpen, onClose, onSuccess, editingItem }: ExtraServiceModalProps) {
    const { t } = useLanguage();
    const { createExtraService, updateExtraService } = useExtraServices();
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const [formData, setFormData] = useState({
        name: '',
        description: '',
        icon: '',
        category: '',
        defaultPrice: '',
        isActive: true,
    });

    const isEditing = !!editingItem;

    // Создаем массивы с переводами внутри компонента
    const AVAILABLE_ICONS = ICONS_CONFIG.map(item => ({
        value: item.value,
        label: `${item.icon} ${t(item.key)}`
    }));

    const AVAILABLE_CATEGORIES = CATEGORIES_CONFIG.map(item => ({
        value: item.value,
        label: `${item.icon} ${t(item.key)}`
    }));

    // Заполняем форму при редактировании
    useEffect(() => {
        if (editingItem) {
            setFormData({
                name: editingItem.name ?? '',
                description: editingItem.description ?? '',
                icon: editingItem.icon ?? '',
                category: editingItem.category ?? '',
                defaultPrice: editingItem.defaultPrice?.toString() ?? '',
                isActive: editingItem.isActive ?? true,
            });
        } else {
            setFormData({
                name: '',
                description: '',
                icon: '',
                category: '',
                defaultPrice: '',
                isActive: true,
            });
        }
        setError(null);
    }, [editingItem, isOpen]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleCheckboxChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setFormData(prev => ({
            ...prev,
            isActive: e.target.checked
        }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);

        // Валидация
        if (!formData.name.trim()) {
            setError(t('name_required'));
            return;
        }

        if (!formData.category) {
            setError(t('category_required'));
            return;
        }

        if (!formData.defaultPrice || parseFloat(formData.defaultPrice) < 0) {
            setError(t('price_required'));
            return;
        }

        try {
            setLoading(true);

            const payload = {
                name: formData.name.trim(),
                description: formData.description.trim() || null,
                icon: formData.icon || null,
                category: formData.category,
                defaultPrice: parseFloat(formData.defaultPrice),
                isActive: formData.isActive,
            };

            if (isEditing && editingItem) {
                await updateExtraService(editingItem.id, payload);
            } else {
                await createExtraService(payload);
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
                        {isEditing ? t('edit_service') : t('new_service')}
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
                            placeholder={t('enter_service_name')}
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
                            rows={2}
                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder={t('enter_service_description')}
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
                            name="category"
                            value={formData.category}
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

                    {/* Цена */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {t('default_price')} * ₽
                        </label>
                        <input
                            type="number"
                            name="defaultPrice"
                            value={formData.defaultPrice}
                            onChange={handleChange}
                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="3000"
                            min="0"
                            step="100"
                            required
                        />
                    </div>

                    {/* Активность */}
                    <div className="flex items-center gap-3">
                        <input
                            type="checkbox"
                            id="isActive"
                            checked={formData.isActive}
                            onChange={handleCheckboxChange}
                            className="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        />
                        <label htmlFor="isActive" className="text-sm text-gray-700 dark:text-gray-300">
                            {t('active')}
                        </label>
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