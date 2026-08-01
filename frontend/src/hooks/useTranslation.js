// src/hooks/useTranslation.js
import {useTranslation as useI18nTranslation} from 'react-i18next';
import {useLanguage} from '../i18n/LanguageProvider';

export const useTranslation = (namespace = 'common') => {
    const { t, i18n } = useI18nTranslation(namespace);
    const { currentLanguage, loading } = useLanguage();

    // Функция для перевода с контекстом
    const translate = (key, options = {}) => {
        return t(key, {
            ...options,
            // Передаем дополнительный контекст для бэкенда
            ...(options.context && { context: options.context }),
        });
    };

    return {
        t: translate,
        i18n,
        language: currentLanguage,
        loading,
        // Для мобильного приложения - получение перевода для конкретного языка
        getTranslationFor: async (locale, key) => {
            return await translationService.getTranslation(locale, key);
        }
    };
};