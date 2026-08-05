// src/hooks/useTranslation.ts
import { useTranslation as useI18nTranslation } from 'react-i18next';
import { useLanguage } from '../i18n/LanguageProvider';

interface TranslateOptions {
    context?: string;
    [key: string]: any;
}

export const useTranslation = (namespace: string = 'common') => {
    const { t, i18n } = useI18nTranslation(namespace);
    const { currentLanguage, loading } = useLanguage();

    // Функция для перевода с контекстом
    const translate = (key: string, options: TranslateOptions = {}) => {
        return t(key, {
            ...options,
            ...(options.context && { context: options.context }),
        });
    };

    return {
        t: translate,
        i18n,
        language: currentLanguage,
        loading,
        // Для мобильного приложения - получение перевода для конкретного языка
        getTranslationFor: async (locale: string, key: string) => {
            // TODO: Добавить метод getTranslation в translationService
            // return await translationService.getTranslation(locale, key);
            return key; // Временное решение
        }
    };
};