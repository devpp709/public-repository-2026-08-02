import i18n from './config';
import { translationService } from '../services/translationService';

export const loadTranslations = async (locale) => {
    try {
        // Загружаем переводы с бэкенда
        const translations = await translationService.getTranslations(locale);

        // Проверка: если translations - это объект с namespace
        if (translations && typeof translations === 'object') {
            // Если переводы пришли как { common: {...}, validation: {...} }
            if (translations.common || translations.validation || translations.car) {
                Object.keys(translations).forEach(namespace => {
                    i18n.addResources(locale, namespace, translations[namespace]);
                });
            } else {
                // Если переводы пришли как простой объект { welcome: "...", login: "..." }
                // Добавляем в namespace 'common'
                i18n.addResources(locale, 'common', translations);
            }
        }

        return true;
    } catch (error) {
        console.error('Failed to load translations:', error);
        // Если файл не найден (404) - используем fallback на русский
        if (error.message.includes('404')) {
            console.warn(`Translations for ${locale} not found, using Russian fallback`);
            // Загружаем русские переводы
            const fallbackTranslations = await translationService.getTranslations('ru');
            if (fallbackTranslations && typeof fallbackTranslations === 'object') {
                i18n.addResources(locale, 'common', fallbackTranslations);
            }
        }
        throw error;
    }
};
// Предзагрузка переводов для всех локалей
export const preloadTranslations = async (locales) => {
    const promises = locales.map(async (locale) => {
        await loadTranslations(locale);
    });
    await Promise.all(promises);
};